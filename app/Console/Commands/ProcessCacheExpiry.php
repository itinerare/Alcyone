<?php

namespace App\Console\Commands;

use App\Models\ImageUpload;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessCacheExpiry extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-cache-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes cache expiry for impacted images.';

    /**
     * Execute the console command.
     */
    public function handle() {
        $expiredImages = ImageUpload::whereNotNull('cache_expiry')->where('cache_expiry', '<', Carbon::now());

        foreach ($expiredImages->get() as $image) {
            unlink($image->convertedPath.'/'.$image->convertedFileName);
        }

        $expiredImages->update([
            'cache_expiry' => null,
        ]);
    }
}
