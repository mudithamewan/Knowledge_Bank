<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Component\VarDumper\VarDumper;

class UserModel extends Model
{
    use HasFactory;

    public function get_roles()
    {
        $data = DB::table('system_roles')
            ->select('*')
            ->where('sr_status', 1)
            ->get();

        return $data;
    }

    public function get_system_users($FROM_DATE, $TO_DATE, $NIC)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('system_users')
            ->select('*')
            ->join('system_user_roles', function ($join) {
                $join->on('system_user_roles.sur_su_id', '=', 'system_users.su_id')
                    ->where('system_user_roles.sur_status', 1);
            })
            ->join('system_roles', 'system_roles.sr_id', '=', 'system_user_roles.sur_sr_id')
            ->where('system_users.su_status', 1);

        if (empty($NIC)) {
            $query->whereBetween('system_users.su_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('system_users.su_nic', $NIC);
        }

        $result = $query->orderBy('system_users.su_inserted_date', 'desc')->get();

        return $result;
    }

    public function get_organizations($FROM_DATE, $TO_DATE, $BR)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('organizations as o')
            ->join('organization_types as ot', function ($join) {
                $join->on('ot.ot_o_id', '=', 'o.o_id')
                    ->where('ot.ot_status', 1);
            })
            ->join('master_organization_types as mot', 'mot.mot_id', '=', 'ot.ot_mot_id')

            ->leftJoin('system_users as in_user', 'in_user.su_id', '=', 'o.o_inserted_by')
            ->leftJoin('system_users as out_user', 'out_user.su_id', '=', 'o.o_updated_by')

            ->select(
                'o.*',
                'in_user.su_name as inserted_user',
                'out_user.su_name as updated_user',
                DB::raw("GROUP_CONCAT(mot.mot_name ORDER BY mot.mot_name SEPARATOR ', ') as types")
            )
            ->where('o.o_id', '!=', 0);

        if (empty($BR)) {
            $query->whereBetween('o.o_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('o.o_br_number', $BR);
        }
        $result = $query->where('o.o_status', 1)->groupBy('o.o_id')->get();


        return $result;
    }

    public function get_user_data($USER_ID)
    {
        $result = DB::table('system_users')
            ->select('*')
            ->join('system_user_roles', function ($join) {
                $join->on('system_user_roles.sur_su_id', '=', 'system_users.su_id')
                    ->where('system_user_roles.sur_status', 1);
            })
            ->join('system_roles', 'system_roles.sr_id', '=', 'system_user_roles.sur_sr_id')
            ->where('system_users.su_id', $USER_ID)
            ->first();

        return $result;
    }

    public function get_last_login_details($USER_ID)
    {
        $result = DB::table('user_login_logs')
            ->where('ull_status', 1)
            ->where('ull_su_id', $USER_ID)
            ->orderBy('ull_inserted_date', 'desc')
            ->first();

        return $result;
    }

    public function get_today_work_count($USER_ID)
    {
        $count = DB::table('user_work_log')
            ->where('uwl_status', 1)
            ->where('uwl_su_id', $USER_ID)
            ->whereDate('uwl_inserted_date', date('Y-m-d'))
            ->count();

        return $count;
    }

    public function get_active_organization_type()
    {
        $data = DB::table('master_organization_types')
            ->where('mot_status', 1)
            ->get();

        return  $data;
    }

    public function get_business_types()
    {
        $data = DB::table('master_business_types')
            ->where('mbt_status', 1)
            ->get();

        return  $data;
    }

    public function get_organization_data($ORG_ID)
    {
        $data = DB::table('organizations')
            ->where('o_id', $ORG_ID)
            ->first();

        return  $data;
    }

    public function get_organization_type_data($ORG_ID)
    {
        $data = DB::table('organization_types')
            ->join('organizations', 'organizations.o_id', '=', 'organization_types.ot_o_id')
            ->join('master_organization_types', 'master_organization_types.mot_id', '=', 'organization_types.ot_mot_id')
            ->where('organization_types.ot_status', 1)
            ->where('organizations.o_id', $ORG_ID)
            ->select('organization_types.*', 'organizations.*', 'master_organization_types.*')
            ->get();

        return  $data;
    }

    public function get_organization_documents_data($ORG_ID)
    {
        $data = DB::table('organization_documents')
            ->join('organizations', 'organizations.o_id', '=', 'organization_documents.od_o_id')
            ->where('organization_documents.od_status', 1)
            ->where('organizations.o_id', $ORG_ID)
            ->select('organization_documents.*', 'organizations.*')
            ->get();

        return  $data;
    }

    public function get_publishers()
    {
        $data = DB::table('organizations')
            ->join('organization_types', function ($join) {
                $join->on('organization_types.ot_o_id', '=', 'organizations.o_id')
                    ->where('organization_types.ot_status', 1);
            })
            ->join('master_organization_types', 'master_organization_types.mot_id', '=', 'organization_types.ot_mot_id')
            ->where('organizations.o_is_active', 1)
            ->where('master_organization_types.mot_id', 1)
            ->where('organizations.o_id', '!=', 0)
            ->select('*')
            ->get();

        return  $data;
    }

    public function get_corporate_customer_list()
    {
        $data = DB::table('organizations')
            ->join('organization_types', function ($join) {
                $join->on('organization_types.ot_o_id', '=', 'organizations.o_id')
                    ->where('organization_types.ot_status', 1);
            })
            ->join('master_organization_types', 'master_organization_types.mot_id', '=', 'organization_types.ot_mot_id')
            ->where('organizations.o_is_active', 1)
            ->where('organizations.o_id', '!=', 0)
            ->where('master_organization_types.mot_id', 2)
            ->select('*')
            ->get();

        return  $data;
    }

    public function get_corporate_customer($SEARCH)
    {
        $organizations = DB::table('organizations')
            ->join('organization_types', function ($join) {
                $join->on('organization_types.ot_o_id', '=', 'organizations.o_id')
                    ->where('organization_types.ot_status', 1);
            })
            ->join('master_organization_types', 'master_organization_types.mot_id', '=', 'organization_types.ot_mot_id')
            ->where('organizations.o_status', 1)
            ->where('master_organization_types.mot_id', 2)
            ->where('organizations.o_id', '!=', 0)
            ->whereRaw('LOWER(organizations.o_business_name) LIKE ?', ['%' . strtolower($SEARCH) . '%'])
            ->get();

        return  $organizations;
    }

    public function get_customers($FROM_DATE, $TO_DATE, $CONTACT_NUMBER)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('customers')
            ->where('customers.c_status', 1);

        if (empty($CONTACT_NUMBER)) {
            $query->whereBetween('customers.c_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('customers.c_contact', $CONTACT_NUMBER);
        }

        $result = $query->orderBy('customers.c_inserted_date', 'desc')->get();

        return $result;
    }

    public function get_user_timeline($USER_ID, $FROM_DATE, $TO_DATE)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $result = DB::table('user_work_log')
            ->join('system_users', 'system_users.su_id', '=', 'user_work_log.uwl_su_id')
            ->where('user_work_log.uwl_status', 1)
            ->where('user_work_log.uwl_su_id', $USER_ID)
            ->whereBetween('user_work_log.uwl_inserted_date', [$formattedFromDate, $formattedToDate])
            ->select('*')
            ->orderByDesc('user_work_log.uwl_inserted_date')
            ->get();

        return $result;
    }

    public function get_warehouses_for_add_user()
    {
        $data = DB::table('master_warehouses')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->where('master_warehouses.mw_status', 1)
            ->select('*')
            ->get();

        return $data;
    }

    public function get_user_warehouses($USER_ID)
    {
        $data = DB::table('system_user_warehouses')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'system_user_warehouses.suw_mw_id')
            ->where('system_user_warehouses.suw_status', 1)
            ->where('system_user_warehouses.suw_su_id', $USER_ID)
            ->get();


        return $data;
    }

    public function get_filtered_invoiecs_organization($IS_COUNT, $FROM_DATE = null, $TO_DATE = null, $O_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $data = DB::table('invoices')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->where('invoices.in_is_corparate', 1)
            ->where('invoices.in_customer_id', $O_ID)
            ->whereBetween('invoices.in_inserted_date', [$formattedFromDate, $formattedToDate])
            ->orderByDesc('invoices.in_inserted_date')
            ->select('*');

        if ($IS_COUNT == 1) {
            $result =  (string)$data->count();
        } else {
            $result =  $data->get();
        }

        return  $result;
    }

    public function get_total_credit_amount($ORG_ID)
    {
        $credit = DB::table('invoices')
            ->where('in_status', 1)
            ->where('in_is_corparate', 1)
            ->where('in_customer_id', $ORG_ID)
            ->where('in_total_balance', '>', 0)
            ->sum('in_total_balance');

        return  $credit;
    }
}
