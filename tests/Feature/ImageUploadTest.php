<?php

namespace Tests\Feature;

use App\Models\ImageUpload;
use App\Models\User\Rank;
use App\Models\User\User;
use App\Services\ImageManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImageUploadTest extends TestCase {
    use RefreshDatabase;

    /******************************************************************************
        IMAGE UPLOADS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->service = (new ImageManager);
    }

    /**
     * Test index access.
     *
     * @dataProvider getIndexProvider
     *
     * @param array|null $imageStatus
     * @param int        $status
     */
    public function testGetIndex($imageStatus, $status) {
        if ($imageStatus) {
            if ($imageStatus[0]) {
                $ownImage = ImageUpload::factory()->user($this->user->id)->create();
                $this->service->testImages($ownImage);
            }
            if ($imageStatus[1]) {
                $otherImage = ImageUpload::factory()->user(User::factory()->create()->id)->create();
                $this->service->testImages($otherImage);
            }
        }

        $response = $this
            ->actingAs($this->user)
            ->get('/');

        $response
            ->assertStatus($status);

        if ($imageStatus) {
            if ($imageStatus[0]) {
                $response->assertViewHas('images', function ($images) use ($ownImage) {
                    return $images->contains($ownImage);
                });
                $this->service->testImages($ownImage, false);
            }
            if ($imageStatus[1]) {
                $response->assertViewHas('images', function ($images) use ($otherImage) {
                    return !$images->contains($otherImage);
                });
                $this->service->testImages($otherImage, false);
            }
        }
    }

    public static function getIndexProvider() {
        return [
            // $imageStatus = [$withOwn, $withOther]

            'without image'    => [null, 200],
            'with own image'   => [[1, 0], 200],
            'with other image' => [[0, 1], 200],
            'with both images' => [[1, 1], 200],
        ];
    }

    /**
     * Test image modal access.
     *
     * @dataProvider getImageProvider
     *
     * @param array|null $userData
     * @param bool       $isValid
     * @param int        $status
     */
    public function testGetImageModal($userData, $isValid, $status) {
        if ($isValid) {
            $image = ImageUpload::factory()->user($this->user->id)->create();
            $this->service->testImages($image);
        }

        $response = $this;

        if ($userData) {
            $rankId = Rank::where('sort', $userData[1] ? 1 : 0)->first()->id;
            if (!$userData[0]) {
                $user = User::factory()->create(['rank_id' => $rankId]);
            } else {
                $user = $this->user;
                $user->update(['rank_id' => $rankId]);
            }

            $response = $response->actingAs($user);
        }

        $response = $response
            ->get('images/view/'.(
                $isValid ? $image->slug : mt_rand(1, 1000).randomString(15)
            ));

        $response->assertStatus($status);

        if ($status == 200) {
            // Check to see if the web/share URLs are displayed
            $response->assertSee($image->imageUrl);
            $response->assertSee(url('images/converted/'.$image->slug));
        }

        if ($isValid) {
            $this->service->testImages($image, false);
        }
    }

    public static function getImageProvider() {
        return [
            // $userData = [$isUploader, $isMod]

            'valid image, uploader'     => [[1, 0], 1, 200],
            'valid image, other user'   => [[0, 0], 1, 404],
            'valid image, moderator'    => [[0, 1], 1, 404],
            'valid image, no user'      => [null, 1, 302],
            'invalid image, uploader'   => [[1, 0], 0, 404],
            'invalid image, other user' => [[0, 0], 0, 404],
            'invalid image, moderator'  => [[0, 1], 0, 404],
            'invalid image, no user'    => [null, 0, 302],
        ];
    }

    /**
     * Test converted image access.
     *
     * @dataProvider getConvertedImageProvider
     *
     * @param bool  $isValid
     * @param int   $status
     * @param mixed $user
     */
    public function testGetConvertedImage($user, $isValid, $status) {
        if ($isValid) {
            $image = ImageUpload::factory()->user($this->user->id)->create();
            $this->service->testImages($image);
        }

        if ($user) {
            $response = $this->actingAs($this->user);
        } else {
            $response = $this;
        }

        $response = $response
            ->get('images/converted/'.(
                $isValid ? $image->slug : mt_rand(1, 1000).randomString(15)
            ));

        $response->assertStatus($status);

        if ($isValid) {
            $this->assertFileExists($image->convertedPath.'/'.$image->convertedFileName);
            $this->service->testImages($image, false);
        }
    }

    public static function getConvertedImageProvider() {
        return [
            'valid image, user'       => [1, 1, 200],
            'valid image, no user'    => [0, 1, 200],
            'invalid image, uploader' => [1, 0, 404],
            'invalid image, no user'  => [0, 0, 404],
        ];
    }

    /**
     * Test converted image cache expiry.
     */
    public function testConvertedImageExpiry() {
        $image = ImageUpload::factory()->user($this->user->id)->create();
        $this->service->testImages($image);

        // Ping the converted image's address to generate the file
        $this
            ->get('images/converted/'.$image->slug);

        // First test that the image persists through a cache expiry check
        $this->artisan('app:process-cache-expiry');
        $this->assertFileExists($image->convertedPath.'/'.$image->convertedFileName);

        // Then move forward to after the cached image expires
        // and confirm that the image is now deleted
        $this->travel(25)->hours();
        $this->artisan('app:process-cache-expiry');
        $this->assertFileDoesNotExist($image->convertedPath.'/'.$image->convertedFileName);

        $this->service->testImages($image, false);
    }

    /**
     * Test image uploading.
     *
     * @dataProvider postUploadImageProvider
     *
     * @param array $fileData
     * @param bool  $expected
     */
    public function testPostUploadImage($fileData, $expected) {
        if ($fileData[0]) {
            $file = UploadedFile::fake()
                ->image('test_image.png', 2000, 2000)
                ->size($fileData[1] ? 10000 : 20000);
        } else {
            $file = UploadedFile::fake()
                ->create('invalid.pdf', $fileData[1] ? 10000 : 20000);
        }

        $response = $this
            ->actingAs($this->user)
            ->post('/images/upload', [
                'image' => $file,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();

            // Locate the image object to verify that it exists
            $image = ImageUpload::where('user_id', $this->user->id)->first();
            $this->assertModelExists($image);

            $this->assertFileExists($image->imagePath.'/'.$image->imageFileName);
            $this->assertFileExists($image->imagePath.'/'.$image->thumbnailFileName);

            $this->service->testImages($image, false);
        } else {
            $response->assertSessionHasErrors();
            $this->assertDatabaseEmpty('image_uploads');
        }
    }

    public static function postUploadImageProvider() {
        return [
            // $fileData = [$isImage, $isValid]

            'valid image'   => [[1, 1], 1],
            'invalid image' => [[1, 0], 0],
            'valid file'    => [[0, 1], 0],
            'invalid file'  => [[0, 0], 0],
        ];
    }

    /**
     * Test image deletion.
     *
     * @dataProvider postDeleteImageProvider
     *
     * @param array $userData
     * @param bool  $isValid
     * @param bool  $expected
     */
    public function testPostDeleteImage($userData, $isValid, $expected) {
        if ($isValid) {
            // Create an image and test files
            $image = ImageUpload::factory()->user($this->user->id)->create();
            $this->service->testImages($image);
        }

        $rankId = Rank::where('sort', $userData[1] ? 1 : 0)->first()->id;
        if (!$userData[0]) {
            $user = User::factory()->create(['rank_id' => $rankId]);
        } else {
            $user = $this->user;
            $user->update(['rank_id' => $rankId]);
        }

        $response = $this
            ->actingAs($user)
            ->post('/images/delete/'.(
                $isValid ? $image->slug : mt_rand(1, 1000).randomString(15)
            ));

        if ($expected) {
            $response->assertSessionHasNoErrors();

            if ($isValid) {
                $this->assertSoftDeleted($image);
                $this->assertFileDoesNotExist($image->imagePath.'/'.$image->imageFileName);
                $this->assertFileDoesNotExist($image->imagePath.'/'.$image->thumbnailFileName);
            }
        } elseif ($isValid) {
            $this->assertNotSoftDeleted($image);
            $this->assertFileExists($image->imagePath.'/'.$image->imageFileName);
            $this->assertFileExists($image->imagePath.'/'.$image->thumbnailFileName);

            $this->service->testImages($image, false);
        } else {
            $response->assertStatus(404);
        }
    }

    public static function postDeleteImageProvider() {
        return [
            // $userData = [$isUploader, $isMod]

            'valid image, uploader'     => [[1, 0], 1, 1],
            'valid image, other user'   => [[0, 0], 1, 0],
            'valid image, moderator'    => [[0, 1], 1, 0],
            'invalid image, uploader'   => [[1, 0], 0, 0],
            'invalid image, other user' => [[0, 0], 0, 0],
            'invalid image, moderator'  => [[0, 1], 0, 0],
        ];
    }
}
