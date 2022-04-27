<?php namespace System\Database\Seeds;

use Seeder;
use System;
use System\Classes\UpdateManager;
use Exception;

/**
 * SeedSetBuildNumber
 */
class SeedSetBuildNumber extends Seeder
{
    /**
     * run
     */
    public function run($buildNumber = null)
    {
        $this->command->line('');

        try {
            if ($buildNumber) {
                UpdateManager::instance()->setBuild((int) $buildNumber);
            }
            else {
                $build = UpdateManager::instance()->setBuildNumberManually();
            }

            $this->command->comment('* You are using October CMS version: v' . System::VERSION . '.' . $build);
        }
        catch (Exception $ex) {
            $this->command->comment('*** Unable to set build: [' . $ex->getMessage() . ']');
        }
    }
}
