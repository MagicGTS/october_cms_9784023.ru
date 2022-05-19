<?php namespace Tailor\Models;

use Model;
use October\Rain\Database\Schema\Blueprint as DbBlueprint;

/**
 * ContentSchema record
 *
 * @package october\tailor
 * @author Alexey Bobkov, Samuel Georges
 */
class ContentSchema extends Model
{
    /**
     * @var string table associated with the model
     */
    protected $table = 'tailor_content_schema';

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = ['meta', 'fields'];

    /**
     * @var array proposedFields
     */
    protected $proposedFields = [];

    /**
     * @var array droppedFields
     */
    protected $droppedFields = [];

    /**
     * @var array existingColumns
     */
    protected $existingColumns = [];

    /**
     * @var array toCreate
     */
    protected $toCreate = [];

    /**
     * @var array droppedFields
     */
    protected $toDrop = [];

    /**
     * @var array changedFields
     */
    protected $toChange = [];

    /**
     * findRecord
     */
    public static function findRecord(string $tableName): ContentSchema
    {
        $record = self::where('table_name', $tableName)->first();

        if (!$record) {
            $record = new self;
            $record->table_name = $tableName;
            $record->save();
        }

        return $record;
    }

    /**
     * afterFetch
     */
    public function afterFetch()
    {
        $this->proposedFields = $this->fields['active'] ?? [];
        $this->droppedFields = $this->fields['dropped'] ?? [];
    }

    /**
     * setExistingColumns
     */
    public function setExistingColumns(array $columns)
    {
        $this->existingColumns = $columns;
    }

    /**
     * setDroppedColumn
     */
    public function setDroppedColumn($fieldName, $droppedName)
    {
        $this->droppedFields[$droppedName] = [
            'original' => $fieldName,
            'details' => $this->proposedFields[$fieldName] ?? null
        ];

        unset($this->proposedFields[$fieldName]);
    }

    /**
     * proposeChanges returns true if changes are needed
     */
    public function proposeChanges(DbBlueprint $table): bool
    {
        // Extract fields from blueprint
        $fields = [];
        foreach ($table->getColumns() as $column) {
            if (!isset($column['name'])) {
                continue;
            }

            $fields[$column['name']] = $column;
        }

        // Compare for existing schema
        foreach ($this->proposedFields as $fieldName => $details) {
            if (isset($fields[$fieldName])) {
                continue;
            }

            $this->toDrop[$fieldName] = $details;
        }

        // Propose for new schema
        foreach ($fields as $fieldName => $details) {
            if (!in_array($fieldName, $this->existingColumns)) {
                $this->toCreate[$fieldName] = $this->proposedFields[$fieldName] = $details;
                continue;
            }

            $wantType = $details['type'] ?? null;
            if (!$wantType) {
                $this->toDrop[$fieldName] = $details;
                continue;
            }

            $hasType = $this->proposedFields[$fieldName]['type'] ?? null;
            if (!$hasType || $wantType !== $hasType) {
                $this->toChange[$fieldName] = $this->proposedFields[$fieldName] = $details;
                continue;
            }
        }

        return $this->toCreate || $this->toDrop || $this->toChange;
    }

    /**
     * commitChanges
     */
    public function commitChanges()
    {
        $this->fields = [
            'active' => $this->proposedFields,
            'dropped' => $this->droppedFields
        ];

        $this->save();
    }

    /**
     * getMissingFields returns missing fields as an array
     * eg: [fieldName => fieldDetails]
     */
    public function getMissingFields(): array
    {
        return $this->toCreate;
    }

    /**
     * getDroppedFields returns fields that should be dropped
     * eg: [fieldName => fieldDetails]
     */
    public function getDroppedFields(): array
    {
        return $this->toDrop;
    }

    /**
     * getChangedFields returns fields that have a new type
     */
    public function getChangedFields(): array
    {
        return $this->toChange;
    }
}
