<?php namespace Mberizzo\FormLogsPlus\Models;

use Model;

/**
 * Log Model
 */
class Log extends Model
{
    /**
     * @var string The database table used by the model.
     */
    public $table = 'renatio_formbuilder_form_logs';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];
}
