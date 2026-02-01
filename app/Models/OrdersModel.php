<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class OrdersModel extends Model
{
    use HasFactory;

    public function get_order_warehouses()
    {
        $data = DB::table('master_warehouses')
            ->select('*')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->where('master_warehouses.mw_status', 1)
            ->where('master_warehouse_types.mwt_id', '!=', 1)
            ->whereIn('master_warehouses.mw_id', session('USER_WAREHOUSES'))
            ->get();

        return $data;
    }

    public function get_filtered_order_details($FROM_DATE, $TO_DATE, $OR_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('orders')
            ->select('*')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'orders.or_mw_id')
            ->join('order_stages', 'order_stages.os_id', '=', 'orders.or_os_id')
            ->join('system_users', 'system_users.su_id', '=', 'orders.or_inserted_by')
            ->where('orders.or_status', 1);

        if (empty($P_ID)) {
            $query->whereBetween('orders.or_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('orders.or_id', $P_ID);
        }

        $data =  $query->orderBy('orders.or_inserted_date')->get();

        return $data;
    }

    public function get_order_details($OR_ID)
    {
        $data = DB::table('orders')
            ->select('*')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'orders.or_mw_id')
            ->join('order_stages', 'order_stages.os_id', '=', 'orders.or_os_id')
            ->join('system_users', 'system_users.su_id', '=', 'orders.or_inserted_by')
            ->where('orders.or_id', $OR_ID)
            ->first();

        return $data;
    }

    public function get_order_item_details($OR_ID)
    {
        $data = DB::table('order_items')
            ->select(
                'order_items.*',
                'orders.*',
                'organizations.*',
                'products.*',
                'master_medium.*',
                'master_categories.*',
                'master_sub_categories.*',
                'master_book_formats.*',
                DB::raw('(SELECT
                        SUM( available_stock.as_available_qty ) 
                    FROM
                        available_stock 
                        join master_warehouses on master_warehouses.mw_id = available_stock.as_mw_id and master_warehouses.mw_mwt_id = 1
                    WHERE
                        available_stock.as_p_id = products.p_id 
                        AND available_stock.as_status = 1) AS available_qty')
            )
            ->join('orders', 'orders.or_id', '=', 'order_items.ori_or_id')
            ->join('organizations', 'organizations.o_id', '=', 'order_items.ori_o_id')
            ->join('products', 'products.p_id', '=', 'order_items.ori_p_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->join('master_book_formats', 'master_book_formats.mbf_id', '=', 'products.p_mbf_id')
            ->where('order_items.ori_status', 1)
            ->where('order_items.ori_or_id', $OR_ID)
            ->orderBy('order_items.ori_o_id')
            ->get();


        return $data;
    }

    public function get_order_item_details_for_summary($OR_ID)
    {
        $data = DB::table('order_items')
            ->select(
                'products.p_id',
                'products.p_name',
                'products.p_isbn',
                DB::raw('MAX(master_categories.mc_name) AS mc_name'),
                DB::raw('SUM(order_items.ori_qty) AS total_qty'),
                DB::raw('(SELECT
                        SUM( available_stock.as_available_qty ) 
                    FROM
                        available_stock 
                        join master_warehouses on master_warehouses.mw_id = available_stock.as_mw_id and master_warehouses.mw_mwt_id = 1
                    WHERE
                        available_stock.as_p_id = products.p_id 
                        AND available_stock.as_status = 1) AS available_qty')
            )
            ->join('orders', 'orders.or_id', '=', 'order_items.ori_or_id')
            ->join('organizations', 'organizations.o_id', '=', 'order_items.ori_o_id')
            ->join('products', 'products.p_id', '=', 'order_items.ori_p_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->join('master_book_formats', 'master_book_formats.mbf_id', '=', 'products.p_mbf_id')
            ->where('order_items.ori_status', 1)
            ->where('order_items.ori_or_id', $OR_ID)
            ->groupBy('products.p_id', 'products.p_name', 'products.p_isbn')
            ->orderBy('order_items.ori_o_id')
            ->get();


        return $data;
    }

    public function get_approvals($OR_ID)
    {
        $data = DB::table('order_approvals')
            ->select(
                'order_approvals.*',
                'orders.*',
                'approval_actions.*',
                'iu.su_name as insert_user',
                'iu.su_nic as insert_nic',
                'au.su_name as action_user',
                'au.su_nic as action_nic'
            )
            ->join('orders', 'orders.or_id', '=', 'order_approvals.ora_or_id')
            ->join('approval_actions', 'approval_actions.aa_id', '=', 'order_approvals.ora_aa_id')
            ->join('system_users as iu', 'iu.su_id', '=', 'order_approvals.ora_inserted_by')
            ->leftJoin('system_users as au', 'au.su_id', '=', 'order_approvals.ora_action_by')
            ->where('order_approvals.ora_status', 1)
            ->where('orders.or_id', $OR_ID)
            ->get();

        return $data;
    }

    public function get_approval_actions()
    {
        $data = DB::table('approval_actions')
            ->select('*')
            ->where('approval_actions.aa_status', 1)
            ->get();

        return $data;
    }

    public function get_approval_details($ORA_ID)
    {
        $data = DB::table('order_approvals')
            ->select('*')
            ->join('approval_actions', 'approval_actions.aa_id', '=', 'order_approvals.ora_aa_id')
            ->where('order_approvals.ora_id', $ORA_ID)
            ->first();

        return $data;
    }

    public function get_order_stock($OR_ID)
    {
        $data = DB::table('order_stock')
            ->select('*')
            ->where('order_stock.os_status', 1)
            ->where('order_stock.os_or_id', $OR_ID)
            ->orderBy('order_stock.os_mw_id')
            ->get();


        return $data;
    }

    public function update_order_timeline($OR_ID, $TITLE, $DESCRIPTION)
    {
        $data = array(
            'ori_status' => 1,
            'ori_inserted_date' => date('Y-m-d H:i:s'),
            'ori_inserted_by' => session('USER_ID'),
            'ori_or_id' => $OR_ID,
            'ori_title' => $TITLE,
            'ori_description' => $DESCRIPTION,
        );
        DB::table('order_timeline')->insert($data);

        return 1;
    }

    public function get_timeline_by_order_id($OR_ID)
    {
        $data = DB::table('order_timeline')
            ->select('*')
            ->join('system_users', 'system_users.su_id', '=', 'order_timeline.ori_inserted_by')
            ->where('order_timeline.ori_status', 1)
            ->where('order_timeline.ori_or_id', $OR_ID)
            ->get();

        return $data;
    }

    public function get_order_approval($TYPE, $IS_COUNT, $FROM_DATE = null, $TO_DATE = null, $OR_ID = null)
    {
        $query = DB::table('order_approvals')
            ->select('*')
            ->join('orders', 'orders.or_id', '=', 'order_approvals.ora_or_id')
            ->join('order_stages', 'order_stages.os_id', '=', 'orders.or_os_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'orders.or_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'orders.or_inserted_by')
            ->where('orders.or_status', 1);

        if (empty($OR_ID)) {
            $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
            $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

            $query->whereBetween('orders.or_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('orders.or_id', $OR_ID);
        }

        if ($TYPE == 1) {
            $query->where('order_approvals.ora_aa_id', 1)
                ->where('order_approvals.ora_status', 1);
        } else if ($TYPE == 2) {
            $query->where('order_approvals.ora_aa_id', 3)
                ->where('order_approvals.ora_status', 1);
        } else if ($TYPE == 3) {
            $query->where('order_approvals.ora_aa_id', 2)
                ->where('order_approvals.ora_status', 1);
        }

        if ($IS_COUNT == 0) {
            $data = $query->get();
        } else {
            $data = (string)$query->count();
        }
        return $data;
    }

    public function get_order_form_data($OR_ID, $MW_ID)
    {
        $results = DB::table('orders')
            ->join('order_stock', 'order_stock.os_or_id', '=', 'orders.or_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'orders.or_mw_id')
            ->join('products', 'products.p_id', '=', 'order_stock.os_p_id')
            ->join('organizations', 'organizations.o_id', '=', 'products.p_publisher_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->where('orders.or_id', $OR_ID)
            ->where('orders.or_status', 1)
            ->where('order_stock.os_mw_id', $MW_ID)
            ->select(
                'products.p_isbn',
                'products.p_name',
                DB::raw('SUM(order_stock.os_qty) AS os_qty'),
                DB::raw('MAX(order_stock.os_selling_amount) AS os_selling_amount'),
                DB::raw("(
                    SELECT GROUP_CONCAT(DISTINCT master_grades.mg_name ORDER BY master_grades.mg_name SEPARATOR ', ')
                    FROM product_grades
                    JOIN master_grades ON master_grades.mg_id = product_grades.pg_mg_id
                    WHERE product_grades.pg_p_id = products.p_id
                    AND product_grades.pg_status = 1
                ) AS grades")
            )
            ->groupBy('products.p_id', 'products.p_name', 'products.p_isbn')
            ->orderBy('grades')
            ->get();

        return $results;
    }

    public function get_collected_orders_by_customer_id($CUS_ID, $MW_ID)
    {
        $result = DB::table('orders')
            ->select(
                'orders.*',
                DB::raw('COUNT(order_items.ori_id) AS item_count')
            )
            ->join('order_items', function ($join) use ($CUS_ID) {
                $join->on('order_items.ori_or_id', '=', 'orders.or_id')
                    ->where('order_items.ori_status', 1)
                    ->where('order_items.ori_o_id', $CUS_ID);
            })
            ->where('orders.or_mw_id', $MW_ID)
            ->where('orders.or_status', 1)
            ->where('orders.or_os_id', 4)
            ->groupBy('orders.or_id')
            ->having(DB::raw('COUNT(order_items.ori_id)'), '!=', 0)
            ->orderBy('orders.or_inserted_date', 'desc')
            ->get();

        return $result;
    }
}
