<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class ProductModel extends Model
{
    use HasFactory;

    public function get_mediums()
    {
        $data = DB::table('master_medium')
            ->select(
                'master_medium.*',
                'in_user.*',
                DB::raw('up_user.su_name AS update_user')
            )
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_medium.mm_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_medium.mm_updated_by')
            ->where('master_medium.mm_status', 1)
            ->orderByDesc('master_medium.mm_inserted_date')
            ->get();


        return $data;
    }

    public function get_active_mediums()
    {
        $data = DB::table('master_medium')
            ->join('system_users', 'system_users.su_id', '=', 'master_medium.mm_inserted_by')
            ->where('master_medium.mm_status', 1)
            ->where('master_medium.mm_is_active', 1)
            ->select('*')
            ->orderByDesc('master_medium.mm_name')
            ->get();

        return $data;
    }

    public function get_medium_details_by_id($ID)
    {
        $data = DB::table('master_medium')
            ->join('system_users', 'system_users.su_id', '=', 'master_medium.mm_inserted_by')
            ->where('master_medium.mm_id', $ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_gades()
    {
        $data = DB::table('master_grades')
            ->select(
                'master_grades.*',
                'in_user.*',
                DB::raw('up_user.su_name AS update_user')
            )
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_grades.mg_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_grades.mg_updated_by')
            ->where('master_grades.mg_status', 1)
            ->orderBy('master_grades.mg_inserted_date', 'DESC')
            ->get();

        return $data;
    }

    public function get_active_gades()
    {
        $data = DB::table('master_grades')
            ->join('system_users', 'system_users.su_id', '=', 'master_grades.mg_inserted_by')
            ->where('master_grades.mg_status', 1)
            ->where('master_grades.mg_is_active', 1)
            ->select('*')
            ->orderByDesc('master_grades.mg_name')
            ->get();

        return $data;
    }

    public function get_grade_details_by_id($ID)
    {
        $data = DB::table('master_grades')
            ->join('system_users', 'system_users.su_id', '=', 'master_grades.mg_inserted_by')
            ->where('master_grades.mg_id', $ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_subjects()
    {
        $data = DB::table('master_subjects')
            ->select(
                'master_subjects.*',
                'in_user.*',
                DB::raw('up_user.su_name AS update_user')
            )
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_subjects.ms_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_subjects.ms_updated_by')
            ->where('master_subjects.ms_status', 1)
            ->orderByDesc('master_subjects.ms_inserted_date')
            ->get();


        return $data;
    }

    public function get_active_subjects()
    {
        $data = DB::table('master_subjects')
            ->join('system_users', 'system_users.su_id', '=', 'master_subjects.ms_inserted_by')
            ->where('master_subjects.ms_status', 1)
            ->where('master_subjects.ms_is_active', 1)
            ->select('*')
            ->orderByDesc('master_subjects.ms_name')
            ->get();

        return $data;
    }

    public function get_subject_details_by_id($ID)
    {
        $data = DB::table('master_subjects')
            ->join('system_users', 'system_users.su_id', '=', 'master_subjects.ms_inserted_by')
            ->where('master_subjects.ms_id', $ID)
            ->select('*')
            ->first();


        return $data;
    }

    public function get_categories()
    {
        $data = DB::table('master_categories')
            ->select(
                'master_categories.*',
                'in_user.*',
                DB::raw('up_user.su_name AS update_user')
            )
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_categories.mc_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_categories.mc_updated_by')
            ->where('master_categories.mc_status', 1)
            ->orderByDesc('master_categories.mc_inserted_date')
            ->get();


        return $data;
    }

    public function get_active_categories()
    {
        $data = DB::table('master_categories')
            ->join('system_users', 'system_users.su_id', '=', 'master_categories.mc_inserted_by')
            ->where('master_categories.mc_status', 1)
            ->where('master_categories.mc_is_active', 1)
            ->select('*')
            ->orderBy('master_categories.mc_name')
            ->get();

        return $data;
    }

    public function get_category_details_by_id($ID)
    {
        $data = DB::table('master_categories')
            ->join('system_users', 'system_users.su_id', '=', 'master_categories.mc_inserted_by')
            ->where('master_categories.mc_status', 1)
            ->where('master_categories.mc_id', $ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_sub_categories()
    {
        $data = DB::table('master_sub_categories')
            ->join('master_categories', 'master_categories.mc_id', '=', 'master_sub_categories.msc_mc_id')
            ->join('system_users as in_user', 'in_user.su_id', '=', 'master_sub_categories.msc_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'master_sub_categories.msc_updated_by')
            ->where('master_sub_categories.msc_status', 1)
            ->select(
                'master_sub_categories.*',
                'master_categories.*',
                'in_user.*',
                DB::raw('up_user.su_name AS updated_user')
            )
            ->get();

        return $data;
    }

    public function get_sub_category_details_by_id($ID)
    {
        $data = DB::table('master_sub_categories')
            ->join('master_categories', 'master_categories.mc_id', '=', 'master_sub_categories.msc_mc_id')
            ->join('system_users', 'system_users.su_id', '=', 'master_sub_categories.msc_inserted_by')
            ->where('master_sub_categories.msc_id', $ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_sub_category_by_search($TEXT, $MM_ID)
    {
        $result = DB::table('master_sub_categories')
            ->select('*')
            ->where('msc_status', 1)
            ->where('msc_is_active', 1)
            ->where('msc_mc_id', $MM_ID)
            ->whereRaw('LOWER(msc_name) LIKE ?', ['%' . strtolower($TEXT) . '%'])
            ->get();

        return $result;
    }

    public function get_book_formats()
    {
        $data = DB::table('master_book_formats')
            ->where('master_book_formats.mbf_status', 1)
            ->select('*')
            ->get();

        return $data;
    }

    public function get_product_details($P_ID)
    {

        $data = DB::table('products')
            ->leftJoin('product_documents', function ($join) {
                $join->on('product_documents.pd_p_id', '=', 'products.p_id')
                    ->where('product_documents.pd_status', 1);
            })
            ->join('organizations', 'organizations.o_id', '=', 'products.p_publisher_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->leftjoin('master_book_formats', 'master_book_formats.mbf_id', '=', 'products.p_mbf_id')
            ->where('products.p_id', $P_ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_product_grades($P_ID)
    {
        $data = DB::table('product_grades')
            ->join('master_grades', 'master_grades.mg_id', '=', 'product_grades.pg_mg_id')
            ->where('product_grades.pg_status', 1)
            ->where('product_grades.pg_p_id', $P_ID)
            ->select('*')
            ->get();

        return $data;
    }

    public function get_product_subjects($P_ID)
    {
        $data = DB::table('product_subjects')
            ->join('master_subjects', 'master_subjects.ms_id', '=', 'product_subjects.ps_ms_id')
            ->where('product_subjects.ps_status', 1)
            ->where('product_subjects.ps_p_id', $P_ID)
            ->select('*')
            ->get();

        return $data;
    }

    public function get_filtered_product_details($FROM_DATE, $TO_DATE, $P_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('products as p')
            ->select(
                'p.*',
                'o.o_business_name',
                'mm.mm_name',
                'mc.mc_name',
                'msc.msc_name',
                'mbf.mbf_name',
                DB::raw("GROUP_CONCAT(DISTINCT mg.mg_name ORDER BY mg.mg_name SEPARATOR ', ') as grades"),
                DB::raw("GROUP_CONCAT(DISTINCT ms.ms_name ORDER BY ms.ms_name SEPARATOR ', ') as subjects"),
                'in_user.su_name as inserted_by',
                'up_user.su_name as updated_by'
            )
            ->join('master_medium as mm', 'mm.mm_id', '=', 'p.p_mm_id')
            ->join('master_categories as mc', 'mc.mc_id', '=', 'p.p_mc_id')
            ->join('master_sub_categories as msc', 'msc.msc_id', '=', 'p.p_msc_id')
            ->leftJoin('master_book_formats as mbf', 'mbf.mbf_id', '=', 'p.p_mbf_id')
            ->join('organizations as o', 'o.o_id', '=', 'p.p_publisher_id')
            ->leftJoin('product_grades as pg', function ($join) {
                $join->on('pg.pg_p_id', '=', 'p.p_id')
                    ->where('pg.pg_status', 1);
            })
            ->leftJoin('master_grades as mg', 'mg.mg_id', '=', 'pg.pg_mg_id')
            ->leftJoin('product_subjects as ps', function ($join) {
                $join->on('ps.ps_p_id', '=', 'p.p_id')
                    ->where('ps.ps_status', 1);
            })
            ->leftJoin('master_subjects as ms', 'ms.ms_id', '=', 'ps.ps_ms_id')


            ->leftJoin('system_users as in_user', 'in_user.su_id', '=', 'p.p_inserted_by')
            ->leftJoin('system_users as up_user', 'up_user.su_id', '=', 'p.p_updated_by')

            ->where('p.p_status', 1);

        if (empty($P_ID)) {
            $query->whereBetween('p.p_inserted_date', [$formattedFromDate, $formattedToDate]);
        } else {
            $query->where('p.p_id', $P_ID);
        }

        $data =  $query->orderByDesc('p.p_inserted_date')->groupBy('p.p_id')->get();

        return $data;
    }

    public function get_filtered_in_list_products($FROM_DATE, $TO_DATE, $PRODUCT_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $result = DB::table('stock_in_list')
            ->join('stock', function ($join) {
                $join->on('stock.s_sil_id', '=', 'stock_in_list.sil_id')
                    ->where('stock.s_status', 1);
            })
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_in_list.sil_mw_id')
            ->join('products', 'products.p_id', '=', 'stock.s_p_id')
            ->join('system_users', 'system_users.su_id', '=', 'stock.s_inserted_by')
            ->where('products.p_id', $PRODUCT_ID)
            ->whereBetween('stock.s_inserted_date', [$formattedFromDate, $formattedToDate])
            ->select('*')
            ->orderByDesc('stock_in_list.sil_inserted_date')
            ->get();

        return  $result;
    }

    public function get_filtered_out_list_products($FROM_DATE, $TO_DATE, $PRODUCT_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $result = DB::table('stock_out_list')
            ->join('stock', function ($join) {
                $join->on('stock.s_sol_id', '=', 'stock_out_list.sol_id')
                    ->where('stock.s_status', 1);
            })
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_out_list.sol_mw_id')
            ->join('products', 'products.p_id', '=', 'stock.s_p_id')
            ->join('system_users', 'system_users.su_id', '=', 'stock.s_inserted_by')
            ->where('products.p_id', $PRODUCT_ID)
            ->whereBetween('stock.s_inserted_date', [$formattedFromDate, $formattedToDate])
            ->select('*')
            ->orderByDesc('stock_out_list.sol_inserted_date')
            ->get();

        return  $result;
    }

    public function get_product_in_list_count($PRODUCT_ID, $DATE)
    {
        $count = DB::table('stock_in_list')
            ->join('stock', function ($join) {
                $join->on('stock.s_sil_id', '=', 'stock_in_list.sil_id')
                    ->where('stock.s_status', 1);
            })
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_in_list.sil_mw_id')
            ->join('products', 'products.p_id', '=', 'stock.s_p_id')
            ->join('system_users', 'system_users.su_id', '=', 'stock.s_inserted_by')
            ->where('products.p_id', $PRODUCT_ID)
            ->where('stock.s_inserted_date', 'LIKE', $DATE . '%')
            ->select('*')
            ->count();

        return  $count;
    }

    public function get_product_out_list_count($PRODUCT_ID, $DATE)
    {
        $count = DB::table('stock_out_list')
            ->join('stock', function ($join) {
                $join->on('stock.s_sol_id', '=', 'stock_out_list.sol_id')
                    ->where('stock.s_status', 1);
            })
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_out_list.sol_mw_id')
            ->join('products', 'products.p_id', '=', 'stock.s_p_id')
            ->join('system_users', 'system_users.su_id', '=', 'stock.s_inserted_by')
            ->where('products.p_id', $PRODUCT_ID)
            ->where('stock.s_inserted_date', 'LIKE', $DATE . '%')
            ->select('*')
            ->count();

        return  $count;
    }

    public function get_product_available_count($PRODUCT_ID)
    {
        $data = DB::table('available_stock')
            ->select(DB::raw('SUM(available_stock.as_available_qty) as as_available_qty'))
            ->where('available_stock.as_status', 1)
            ->where('available_stock.as_p_id', $PRODUCT_ID)
            ->groupBy('available_stock.as_p_id')
            ->first();

        return  $data;
    }

    public function get_filtered_available_stock_products($MW_ID, $PRODUCT_ID)
    {
        $query = DB::table('available_stock')
            ->join('products', 'products.p_id', '=', 'available_stock.as_p_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'available_stock.as_mw_id')
            ->join('organizations', 'organizations.o_id', '=', 'products.p_publisher_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->where('available_stock.as_status', 1)
            ->where('available_stock.as_p_id', $PRODUCT_ID)
            ->where('available_stock.as_available_qty', '>', 0)
            ->select(
                'available_stock.*',
                'products.*',
                'master_warehouses.*',
                'organizations.*',
                'master_medium.*',
                'master_categories.*',
                'master_sub_categories.*',
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT master_grades.mg_name ORDER BY master_grades.mg_name SEPARATOR ', ')
            FROM product_grades
            JOIN master_grades ON master_grades.mg_id = product_grades.pg_mg_id
            WHERE product_grades.pg_p_id = products.p_id
            AND product_grades.pg_status = 1) AS grades"),
                DB::raw("(SELECT GROUP_CONCAT(DISTINCT master_subjects.ms_name ORDER BY master_subjects.ms_name SEPARATOR ', ')
            FROM product_subjects
            JOIN master_subjects ON master_subjects.ms_id = product_subjects.ps_ms_id
            WHERE product_subjects.ps_p_id = products.p_id
            AND product_subjects.ps_status = 1) AS subjects")
            );

        if ($MW_ID != 'ALL') {
            $query->where('master_warehouses.mw_id', $MW_ID);
        }
        $result =  $query->get();

        return $result;
    }
}
