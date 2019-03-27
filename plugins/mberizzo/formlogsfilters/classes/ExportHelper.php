<?php namespace Mberizzo\FormLogsFilters\Classes;

use Illuminate\Support\Facades\DB;
use Mberizzo\FormLogsFilters\Models\Log;
use Mberizzo\FormLogsFilters\Traits\FormSettings;

class ExportHelper
{

    use FormSettings;

    protected $formId;

    public function __construct($formId)
    {
        $this->formId = $formId;
    }

    public function getExportableColumns()
    {
        $this->fields()->each(function ($field) use (&$data) {
            if ($this->isNonExportable($field)) {
                return;
            }

            list($name, $label) = $this->makeColumn($field);

            $data[$name] = $label;
        });

        return $data;
    }

    private function makeColumn($field)
    {
        $name = "form_data.{$field->name}.value";

        if (in_array($field->name, ['id', 'created_at'])) {
            $name = $field->name;
        }

        return [$name, $field->label];
    }

    private function isNonExportable($field)
    {
        if (in_array($field->name, ['submit', 'g-recaptcha-response'])) {
            return true;
        }

        return false;
    }

    public function logQuery()
    {
        return Log::where('form_id', $this->formId);
    }

    /**
     * Raw selection inside JSON column
     * @param string $col. i.e: form_data.name.value
     */
    public function getQuerySelect4JsonData($col)
    {
        if (! $this->isJsonColumn($col)) {
            return $col;
        }

        $xp = explode('.', $col);

        // i.e: form_data->>"$.name.value" as "form_data.name.value"
        $qs = $xp[0] . '->>"$.' . "{$xp[1]}.{$xp[2]}" . '" as "' . $col . '"';

        return DB::raw($qs);
    }

    private function isJsonColumn($column)
    {
        $parts = explode('.', $column);

        return count($parts) == 3;
    }
}
