<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageViewTest extends TestCase {
    use RefreshDatabase;

    /******************************************************************************
        PUBLIC: PAGES
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * Test page access.
     *
     * @dataProvider pageProvider
     *
     * @param string $page
     * @param bool   $setup
     * @param bool   $user
     * @param int    $status
     */
    public function testGetPage($page, $setup, $user, $status) {
        if ($setup) {
            $this->artisan('add-site-pages');
        }

        $response = $this;
        if ($user) {
            $response = $response
                ->actingAs($this->user);
        }

        $response->get('/info/'.$page)->assertStatus($status);
    }

    public static function pageProvider() {
        return [
            'terms, not set up, visitor'          => ['terms', 0, 0, 404],
            'terms, not set up, user'             => ['terms', 0, 1, 404],
            'terms, set up, visitor'              => ['terms', 1, 0, 200],
            'terms, set up, user'                 => ['terms', 1, 1, 200],
            'privacy policy, not set up, visitor' => ['privacy', 0, 0, 404],
            'privacy policy, not set up, user'    => ['privacy', 0, 1, 404],
            'privacy policy, set up, visitor'     => ['privacy', 1, 0, 200],
            'privacy policy, set up, user'        => ['privacy', 1, 1, 200],
            'invalid page, visitor'               => ['invalid', 1, 0, 404],
            'invalid page, user'                  => ['invalid', 1, 1, 404],
        ];
    }
}
