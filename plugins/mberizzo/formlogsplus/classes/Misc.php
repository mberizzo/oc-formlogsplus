<?php namespace Mberizzo\FormLogsPlus\Classes;

use Illuminate\Support\Facades\DB;

class Misc
{

    public function mysqlVersion()
    {
        $pdo = DB::connection()->getPdo();
        $version = $pdo->query('select version()')->fetchColumn();
        (float)$version = mb_substr($version, 0, 6);

        return $version;
    }

    public function helpMeToExport($columns, $collection)
    {
        $response = $collection->map(function($item) use ($columns) {
            $data = json_decode($item['form_data'], true);
            $response = [
                'id' => $item->id,
                'created_at' => $item->created_at,
            ];

            foreach ($data as $key => $value) {
                $k = "form_data.{$key}.value";
                $response[$k] = $value['value'];
            }

            return $response;
        });

        return $response->toArray();
    }

}
