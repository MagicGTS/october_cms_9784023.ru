<?php namespace T9784023\Cources\Models;

use Model;

/**
 * HighAltitudeWorks Model
 */
class HighAltitudeWorks extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string table associated with the model
     */
    public $table = 't9784023_cources__h_alt_w';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['*'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [
        'name',
        'cost_min',
        'cost_max',
        'description',
        'hours_min',
        'hours_max',
        'learning_form',
        'fines_description',
        'fines_links',
        'whom_needs',
        'what_will_learn',
        'schedule_description',
        'howto_sign_up',
        'result_img_folder',
        'schould_to_know',
        'youtube_url',
        'img_background',
        'img_rounded',
        'files_folder'
    ];

    /**
     * @var array rules for validation
     */
    public $rules = [];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = [];

    /**
     * @var array appends attributes to the API representation of the model (ex. toArray())
     */
    protected $appends = [];

    /**
     * @var array hidden attributes removed from the API representation of the model (ex. toArray())
     */
    protected $hidden = [];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];
    public $implement = ['ReaZzon.Gutenberg.Behaviors.Gutenbergable'];
    /**
     * @var array hasOne and other relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [
        'schedule' => [\T9784023\Schedules\Models\Schedule::class, 'name' => 'schedule'],
        'program' => [\T9784023\LearningPrograms\Models\Program::class, 'name' => 'program'],];
    public $attachOne = [];
    public $attachMany = [];
}
