<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class CommonModel extends Model
{
    use HasFactory;

    public function update_work_log($USER_ID, $DESCRIPTION)
    {
        $data3 = array(
            'uwl_status' => 1,
            'uwl_inserted_date' => date('Y-m-d H:i:s'),
            'uwl_inserted_by' => session('USER_ID'),
            'uwl_su_id' => $USER_ID,
            'uwl_description' => $DESCRIPTION,
        );
        DB::table('user_work_log')->insert($data3);

        return true;
    }
}
