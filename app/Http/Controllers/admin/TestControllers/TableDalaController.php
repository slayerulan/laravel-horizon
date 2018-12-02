<?php

namespace App\Http\Controllers\admin\TestControllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Support\Facades\Schema;
use App\LtmTranslation;

class TableDalaController extends Controller
{
    public function index(request $request) {
        $data['db'] = DB::getDatabaseName();
        $data['tables'] = DB::select('SHOW TABLES');
        return view('developer.fetch_table', $data);
    }

    public function postFetchAllTableData(request $request) {
        if ($request->scode == 'Icode@18') {
            $table_name = $request->table_name;
            $limit = $request->limit;
            $page = $request->page;
            $offset = ($page-1)*$limit;

            $columns = $data['columns'] = Schema::getColumnListing($table_name);
            $query = DB::table($table_name)->where(function($sql) use ($request, $columns) {
                if ($request->keyword != '') {
                    foreach ($columns as $column) {
                        $sql->orWhere($column, 'like', '%'.$request->keyword.'%');
                    }
                }
            });
            $total_data = $query->count();
            $data['table_data'] = $query->offset($offset)->limit($limit)->get();
            $data['total_page'] = ceil($total_data/$limit);
            $data['active_page'] = $page;
            return view('developer.show_table_data', $data);
        }
        else{
            echo 'Wrong Code';
        }
    }

    public function setTranslation() {
        $lang_name = [
            'bu' => 'Burmese',
            // 'en' => 'English',
            // 'ja' => 'Japanese',
            'kh' => 'Khmer',
            // 'ko' => 'Korean',
            // 'mn' => 'Mandarin',
            // 'th' => 'Thai',
            // 'vi' => 'Vietnamese'
        ];
        $data = 0;
        // $count = LtmTranslation::where('locale', 'en')->count();
        // for ($offset=0; $offset < $count/1000; $offset++) {
            $values_inEN = LtmTranslation::where('locale', 'en')->get();


            foreach ($values_inEN as $en_value) {
                foreach ($lang_name as $lang_key => $lang) {
                    $ltm_translation = new LtmTranslation;
                    $ltm_translation->locale = $lang_key;
                    $ltm_translation->group = $en_value->group;
                    $ltm_translation->key = $en_value->key;
                    $ltm_translation->value = $en_value->value;
                    $ltm_translation->save();
                    $data++;
                }
            }
        // }

        a($data.'Row inserted.');
    }
}
