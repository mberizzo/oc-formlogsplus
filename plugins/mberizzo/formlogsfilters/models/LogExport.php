<?php namespace Mberizzo\Formlogsfilters\Models;

use Illuminate\Support\Facades\DB;
use Mberizzo\FormLogsFilters\Models\Log;

class LogExport extends \Backend\Models\ExportModel
{

    public function exportData($columns, $sessionKey = null)
    {
        foreach ($columns as $col) {
            $select[] = $this->prepareColumnToSelect($col);
        }

        $logs = Log::select($select)->orderBy('id', 'desc')->get();

        return $logs->toArray();
    }

    /**
     * Detect if column is JSON and then
     * Prepare a deep query selection
     *
     * @param string $col
     * @return string $col
     */
    private function prepareColumnToSelect($col)
    {
        // JSON field has dot notations
        $fieldParts = explode('.', $col);

        // Detect if is JSON type
        if (count($fieldParts) > 1) {
            $field = array_shift($fieldParts);
            $fieldDeep = implode('.', $fieldParts);
            $col = DB::raw($field . '->>"$.' . $fieldDeep . '" as "' . $col . '"');
        }

        return $col;
    }
}
