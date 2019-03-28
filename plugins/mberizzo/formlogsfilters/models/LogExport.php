<?php namespace Mberizzo\Formlogsfilters\Models;

use Illuminate\Support\Arr;
use Mberizzo\FormLogsFilters\Classes\ExportHelper;

class LogExport extends \Backend\Models\ExportModel
{

    protected $helper;

    /**
     * @var array Fillable fields
     */
    protected $fillable = ['date_from', 'date_to'];

    public function __construct()
    {
        $this->helper = new ExportHelper(request()->form_id);

        parent::__construct();
    }

    public function exportData($columns, $sessionKey = null)
    {
        foreach ($columns as $column) {
            $select[] = $this->helper->getQuerySelect4JsonData($column);
        }

        $log = $this->helper->logQuery($this);

        return $log->select($select)->get()->toArray();
    }

    protected function exportExtendColumns($selectedColumnsByUser)
    {
        return array_intersect_key(
            $this->helper->getExportableColumns(),
            $selectedColumnsByUser
        );
    }
}
