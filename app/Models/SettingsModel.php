<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class SettingsModel extends Model
{
    use HasFactory;

    public function get_access_areas()
    {
        $areas = DB::table('system_access_areas')
            ->where('saa_status', 1)
            ->where('saa_id', '!=', 1)
            ->orderBy('saa_display_category', 'ASC')
            ->get();

        return $areas;
    }

    public function get_role_data($ROLE_ID)
    {
        $role = DB::table('system_roles')
            ->where('sr_status', 1)
            ->where('sr_id', $ROLE_ID)
            ->first();

        return $role;
    }

    public function get_role_details_by_id($ROLE_ID)
    {
        $roles = DB::table('system_roles')
            ->join('system_role_access', function ($join) {
                $join->on('system_role_access.sra_sr_id', '=', 'system_roles.sr_id')
                    ->where('system_role_access.sra_status', 1);
            })
            ->join('system_access_areas', 'system_access_areas.saa_id', '=', 'system_role_access.sra_saa_id')
            ->where('system_roles.sr_id', $ROLE_ID)
            ->select('*')
            ->orderBy('system_access_areas.saa_display_category', 'ASC')
            ->get();

        return $roles;
    }

    public function get_stock_locations()
    {
        $warehouses = DB::table('master_warehouses')
            ->select(
                'master_warehouses.*',
                'master_warehouse_types.*',
                'in_user.*',
                DB::raw('up_user.su_name AS update_name')
            )
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_warehouses.mw_inserted_by')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_warehouses.mw_updated_by')
            ->where('master_warehouses.mw_status', 1)
            ->orderBy('master_warehouses.mw_id', 'DESC')
            ->get();


        return $warehouses;
    }

    public function get_stock_location_details_by_id($ID)
    {
        $data = DB::table('master_warehouses')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->where('master_warehouses.mw_id', $ID)
            ->first();

        return $data;
    }

    public function get_stock_location_types()
    {
        $warehouseTypes = DB::table('master_warehouse_types')
            ->where('mwt_status', 1)
            ->get();

        return $warehouseTypes;
    }

    public function get_last_punch_detail($USER_ID)
    {
        $data = DB::table('punches')
            ->select('*')
            ->join('punch_stages', 'punch_stages.pus_id', '=', 'punches.pu_pus_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'punches.pu_mw_id')
            ->where('punches.pu_su_id', $USER_ID)
            ->where('punches.pu_status', 1)
            ->orderBy('punches.pu_inserted_date', 'DESC')
            ->first();
        return $data;
    }

    public function get_all_locations()
    {
        $warehouses = DB::table('master_warehouses')
            ->join('system_users', 'system_users.su_id', '=', 'master_warehouses.mw_inserted_by')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->where('master_warehouses.mw_status', 1)
            ->orderBy('master_warehouses.mw_id', 'desc')
            ->get();

        return $warehouses;
    }

    public function get_punch_details($FROM_DATE, $TO_DATE, $MW_ID, $IS_SELF = 0)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('punches')
            ->select('*')
            ->join('system_users', 'system_users.su_id', '=', 'punches.pu_su_id')
            ->join('punch_stages', 'punch_stages.pus_id', '=', 'punches.pu_pus_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'punches.pu_mw_id')
            ->where('punches.pu_status', 1)
            ->whereBetween('punches.pu_inserted_date', [$formattedFromDate, $formattedToDate])
            ->orderBy('punches.pu_inserted_date', 'DESC');

        if ($MW_ID != 'ALL') {
            $query->where('punches.pu_mw_id', $MW_ID);
        }

        if ($IS_SELF == 1) {
            $query->where('punches.pu_su_id', session('USER_ID'));
        }

        $result =   $query->get();

        return $result;
    }

    public function get_punch_details_by_id($PU_ID)
    {
        $result = DB::table('punches')
            ->select('*')
            ->join('system_users', 'system_users.su_id', '=', 'punches.pu_su_id')
            ->join('punch_stages', 'punch_stages.pus_id', '=', 'punches.pu_pus_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'punches.pu_mw_id')
            ->where('punches.pu_id', $PU_ID)
            ->first();

        return $result;
    }

    public function get_system_users()
    {
        $users = DB::table('system_users')
            ->where('system_users.su_status', 1)
            ->get();

        return $users;
    }

    public function get_active_punch($MW_ID)
    {
        $result = DB::table('punches')
            ->select('*')
            ->join('system_users', 'system_users.su_id', '=', 'punches.pu_su_id')
            ->join('punch_stages', 'punch_stages.pus_id', '=', 'punches.pu_pus_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'punches.pu_mw_id')
            ->where('punches.pu_status', 1)
            ->where('punches.pu_su_id', session('USER_ID'))
            ->where('punches.pu_mw_id', $MW_ID)
            ->orderBy('punches.pu_inserted_date', 'DESC')
            ->first();

        return $result;
    }
}
