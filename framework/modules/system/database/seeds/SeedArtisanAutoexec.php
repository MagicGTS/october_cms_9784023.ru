<?php namespace System\Database\Seeds;

use File;
use Seeder;
use Artisan;

/**
 * SeedArtisanAutoexec
 */
class SeedArtisanAutoexec extends Seeder
{
    /**
     * run
     */
    public function run()
    {
        $this->command->line('');

        $seedFile = storage_path('cms/autoexec.json');
        if (!File::exists($seedFile)) {
            return;
        }

        $contents = json_decode(File::get($seedFile), true);
        if (!$contents || !is_array($contents)) {
            return;
        }

        try {
            foreach ($contents as $artisanCmd) {
                Artisan::call($artisanCmd, [], $this->command->getOutput());
            }
        }
        finally {
            File::delete($seedFile);
        }
    }
}
