<?php namespace Tailor\Classes;

use File;
use System;
use Tailor\Classes\Blueprint\EntryBlueprint;

/**
 * BlueprintIndexer super class responsible for indexing blueprints
 *
 * @method static BlueprintIndexer instance()
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class BlueprintIndexer
{
    use \System\Traits\NoteMaker;
    use \October\Rain\Support\Traits\Singleton;
    use \Tailor\Classes\BlueprintIndexer\MixinIndex;
    use \Tailor\Classes\BlueprintIndexer\GlobalIndex;
    use \Tailor\Classes\BlueprintIndexer\SectionIndex;
    use \Tailor\Classes\BlueprintIndexer\FieldsetIndex;
    use \Tailor\Classes\BlueprintIndexer\PermissionRegistry;
    use \Tailor\Classes\BlueprintIndexer\NavigationRegistry;

    /**
     * @var bool useCache
     */
    protected $useCache;

    /**
     * @var array cache collection
     */
    public static $memoryCache = [];

    /**
     * @var int migrateCount number of migrations that occured.
     */
    protected $migrateCount = 0;

    /**
     * migrate
     */
    public function migrate()
    {
        static::clearCache();

        $this->migrateCount = 0;

        $this->note('Migrating Content Tables');

        foreach (Blueprint::listInProject() as $blueprint) {
            // Validate blueprint
            $blueprint->validate();

            // Saving a blueprint will generate a uuid
            if (!$blueprint->uuid) {
                $blueprint->forceSave();
            }

            if ($blueprint instanceof EntryBlueprint) {
                $this->migrateContentInternal($blueprint);
            }
        }

        if ($this->migrateCount === 0) {
            $this->note('<info>Nothing to migrate.</info>');
        }
    }

    /**
     * migrateBlueprint
     */
    public function migrateBlueprint(Blueprint $blueprint)
    {
        static::clearCache();

        // Saving a blueprint will generate a uuid
        if (!$blueprint->uuid) {
            $blueprint->forceSave();
        }

        $this->migrateContentInternal($blueprint);
    }

    /**
     * migrateContentInternal
     */
    protected function migrateContentInternal(Blueprint $blueprint)
    {
        if ($fieldset = $this->findContentFieldset($blueprint->uuid)) {
            if (SchemaBuilder::migrateBlueprint($blueprint, $fieldset)) {
                $this->note('- <info>'.$blueprint->name.'</info>: '.$blueprint->handle .' ['.$blueprint->getContentTableName().']');
                $this->migrateCount++;
            }
        }
    }

    /**
     * getCache
     */
    protected function getCache($name): array
    {
        if ($this->useCache === null) {
            $this->useCache = !System::checkDebugMode();
        }

        if (!$this->useCache) {
            return [];
        }

        if (array_key_exists($name, static::$memoryCache)) {
            return static::$memoryCache[$name];
        }

        $fileName = $this->makeCacheFile($name);

        if (!File::exists($fileName)) {
            return [];
        }

        return static::$memoryCache[$name] = json_decode(File::get($fileName), true);
    }

    /**
     * toggleCache
     */
    public function toggleCache($value): void
    {
        $this->useCache = $value;
    }

    /**
     * putCache
     */
    protected function putCache($name, array $contents): void
    {
        if ($this->useCache) {
            File::put($this->makeCacheFile($name), json_encode($contents));
        }
    }

    /**
     * makeCacheFile
     */
    protected function makeCacheFile($name): string
    {
        $rootPath = cache_path('cms/cache/blueprints');

        if (!File::exists($rootPath)) {
            File::makeDirectory($rootPath, 0755, true);
        }

        return $rootPath.'/'.$name.'.json';
    }

    /**
     * flushCache clears the memory cache
     */
    public static function flushCache()
    {
        static::$memoryCache = [];
    }

    /**
     * clearCache clears the disk cache
     */
    public static function clearCache()
    {
        $rootPath = cache_path('cms/cache/blueprints');

        if (!File::exists($rootPath)) {
            File::makeDirectory($rootPath, 0755, true);
            return;
        }

        File::cleanDirectory($rootPath);
    }
}
