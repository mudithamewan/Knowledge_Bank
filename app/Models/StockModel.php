<?php

namespace App\Models;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Component\VarDumper\VarDumper;

class StockModel extends Model
{
    use HasFactory;

    public function search_products($SEARCH_TEXT)
    {
        $products = DB::table('products')
            ->select('*')
            ->where('p_name', 'LIKE', "%{$SEARCH_TEXT}%")
            ->orWhere('p_isbn', 'LIKE', "%{$SEARCH_TEXT}%")
            ->orWhere('p_id', 'LIKE', "%{$SEARCH_TEXT}%")
            ->get();

        return $products;
    }

    public function search_available_products($SEARCH_TEXT, $MW_ID)
    {
        $products = DB::table('products')
            ->leftJoin('available_stock', function ($join) use ($MW_ID) {
                $join->on('available_stock.as_p_id', '=', 'products.p_id')
                    ->where('available_stock.as_status', 1)
                    ->where('available_stock.as_available_qty', '>', 0)
                    ->where('available_stock.as_mw_id', $MW_ID);
            })
            ->select(
                'products.p_id',

                DB::raw('MAX(products.p_status) AS p_status'),
                DB::raw('MAX(products.p_inserted_date) AS p_inserted_date'),
                DB::raw('MAX(products.p_inserted_by) AS p_inserted_by'),
                DB::raw('MAX(products.p_updated_date) AS p_updated_date'),
                DB::raw('MAX(products.p_updated_by) AS p_updated_by'),
                DB::raw('MAX(products.p_is_active) AS p_is_active'),
                DB::raw('MAX(products.p_name) AS p_name'),
                DB::raw('MAX(products.p_isbn) AS p_isbn'),
                DB::raw('MAX(products.p_author) AS p_author'),
                DB::raw('MAX(products.p_publisher_id) AS p_publisher_id'),
                DB::raw('MAX(products.p_mm_id) AS p_mm_id'),
                DB::raw('MAX(products.p_mc_id) AS p_mc_id'),
                DB::raw('MAX(products.p_msc_id) AS p_msc_id'),
                DB::raw('MAX(products.p_description) AS p_description'),
                DB::raw('MAX(products.p_edition) AS p_edition'),
                DB::raw('MAX(products.p_published_year) AS p_published_year'),
                DB::raw('MAX(products.p_page_count) AS p_page_count'),
                DB::raw('MAX(products.p_mbf_id) AS p_mbf_id'),
                DB::raw('MAX(available_stock.as_id) AS as_id'),
                DB::raw('COUNT(available_stock.as_id) AS price_count'),
                DB::raw('SUM(available_stock.as_available_qty) as as_available_qty'),
                DB::raw('MAX(available_stock.as_status) AS as_status'),
                DB::raw('MAX(available_stock.as_inserted_date) AS as_inserted_date'),
                DB::raw('MAX(available_stock.as_inserted_by) AS as_inserted_by'),
                DB::raw('MAX(available_stock.as_updated_date) AS as_updated_date'),
                DB::raw('MAX(available_stock.as_updated_by) AS as_updated_by'),
                DB::raw('MAX(available_stock.as_p_id) AS as_p_id'),
                DB::raw('MAX(available_stock.as_selling_price) AS as_selling_price'),
                DB::raw('MAX(available_stock.as_mw_id) AS as_mw_id')
            )
            ->where(function ($query) use ($SEARCH_TEXT) {
                $query->where('products.p_name', 'LIKE', "%{$SEARCH_TEXT}%")
                    ->orWhere('products.p_isbn', 'LIKE', "%{$SEARCH_TEXT}%")
                    ->orWhere('products.p_id', 'LIKE', "%{$SEARCH_TEXT}%");
            })
            ->groupBy('products.p_id')
            ->get();


        return $products;
    }


    public function get_active_stock_locations()
    {
        $warehouses = DB::table('master_warehouses')
            ->join('master_warehouse_types', 'master_warehouse_types.mwt_id', '=', 'master_warehouses.mw_mwt_id')
            ->where('mw_is_active', 1)
            ->whereIn('master_warehouses.mw_id', session('USER_WAREHOUSES'))
            ->orderBy('master_warehouses.mw_name')
            ->get();

        return $warehouses;
    }

    public function get_active_stock_locations_by_type($MWT_ID)
    {
        $warehouses = DB::table('master_warehouses')
            ->where('mw_is_active', 1)
            ->where('mw_mwt_id', $MWT_ID)
            ->whereIn('master_warehouses.mw_id', session('USER_WAREHOUSES'))
            ->orderBy('master_warehouses.mw_name')
            ->get();

        return $warehouses;
    }

    public function get_stock_in_history_details($FROM_DATE, $TO_DATE, $MW_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('stock_in_list')
            ->join('system_users', 'system_users.su_id', '=', 'stock_in_list.sil_inserted_by')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_in_list.sil_mw_id')
            ->where('stock_in_list.sil_status', 1)
            ->whereBetween('stock_in_list.sil_inserted_date', [$formattedFromDate, $formattedToDate])
            ->orderByDesc('stock_in_list.sil_inserted_date')
            ->select('*');

        if ($MW_ID != 'ALL') {
            $query->where('stock_in_list.sil_mw_id', $MW_ID);
        }

        $result = $query->get();

        return $result;
    }

    public function get_stock_in_details_by_id($ID)
    {
        $result = DB::table('stock_in_list')
            ->join('system_users', 'system_users.su_id', '=', 'stock_in_list.sil_inserted_by')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_in_list.sil_mw_id')
            ->where('stock_in_list.sil_id', $ID)
            ->select('*')
            ->first();

        return  $result;
    }

    public function get_stock_items_in_details_by_id($ID)
    {
        $result = DB::table('stock_in_list')
            ->join('stock', function ($join) {
                $join->on('stock.s_sil_id', '=', 'stock_in_list.sil_id')
                    ->where('stock.s_status', 1);
            })
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'stock_in_list.sil_mw_id')
            ->join('products', 'products.p_id', '=', 'stock.s_p_id')
            ->where('stock_in_list.sil_id', $ID)
            ->select('*')
            ->get();

        return  $result;
    }

    public function get_stock_in_documents($SI_ID)
    {
        $data = DB::table('stock_in_list_uploads')
            ->where('silu_sil_id', $SI_ID)
            ->where('silu_status', 1)
            ->orderBy('silu_inserted_date', 'DESC')
            ->get();

        return  $data;
    }

    public function get_available_stock_by_product_and_selling_price($PRODUCT_ID, $SELLING_PRICE, $MW_ID)
    {
        $stocks = DB::table('available_stock')
            ->where('as_status', 1)
            ->where('as_p_id', $PRODUCT_ID)
            ->where('as_selling_price', $SELLING_PRICE)
            ->where('as_mw_id', $MW_ID)
            ->first();

        return  $stocks;
    }

    public function update_available_stock($PRODUCT_ID, $SELLING_PRICE, $QTY, $MW_ID, $IN_OR_OUT)
    {
        $AB_STOCK = $this->get_available_stock_by_product_and_selling_price($PRODUCT_ID, $SELLING_PRICE, $MW_ID);

        if ($AB_STOCK == false) {
            $data3 = array(
                'as_status' => 1,
                'as_inserted_date' => date('Y-m-d H:i:s'),
                'as_inserted_by' => session('USER_ID'),
                'as_updated_date' => date('Y-m-d H:i:s'),
                'as_updated_by' => session('USER_ID'),
                'as_p_id' => $PRODUCT_ID,
                'as_selling_price' => $SELLING_PRICE,
                'as_available_qty' => $QTY,
                'as_mw_id' => $MW_ID,
            );
            DB::table('available_stock')->insert($data3);
        } else {

            if ($IN_OR_OUT == 'IN') {
                $AB_QTY = $AB_STOCK->as_available_qty +  $QTY;
            } else {
                if ($AB_STOCK->as_available_qty < $QTY) {
                    return false;
                } else {
                    $AB_QTY = $AB_STOCK->as_available_qty -  $QTY;
                }
            }

            $data3 = array(
                'as_updated_date' => date('Y-m-d H:i:s'),
                'as_updated_by' => session('USER_ID'),
                'as_available_qty' => $AB_QTY,
            );
            DB::table('available_stock')
                ->where('as_id', $AB_STOCK->as_id)
                ->update($data3);
        }

        return true;
    }

    public function get_diffrent_price_list($PRODUCT_ID, $MW_ID)
    {
        $data = DB::table('products')
            ->join('available_stock', function ($join) use ($MW_ID) {
                $join->on('available_stock.as_p_id', '=', 'products.p_id')
                    ->where('available_stock.as_status', 1)
                    ->where('available_stock.as_available_qty', '>', 0)
                    ->where('available_stock.as_mw_id', $MW_ID);
            })
            ->where('products.p_id', $PRODUCT_ID)
            ->select('*')
            ->get();

        return $data;
    }

    public function get_available_stock_data_by_id($AS_ID)
    {
        $data = DB::table('products')
            ->join('available_stock', function ($join) {
                $join->on('available_stock.as_p_id', '=', 'products.p_id')
                    ->where('available_stock.as_status', 1);
            })
            ->where('available_stock.as_id', $AS_ID)
            ->select('*')
            ->first();

        return $data;
    }

    public function get_stock_details($FROM_DATE, $TO_DATE, $MW_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('available_stock')
            ->join('products', 'products.p_id', '=', 'available_stock.as_p_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'available_stock.as_mw_id')
            ->join('organizations', 'organizations.o_id', '=', 'products.p_publisher_id')
            ->join('master_medium', 'master_medium.mm_id', '=', 'products.p_mm_id')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->join('master_sub_categories', 'master_sub_categories.msc_id', '=', 'products.p_msc_id')
            ->where('available_stock.as_status', 1)
            ->where('available_stock.as_available_qty', '>', 0)
            ->whereBetween('available_stock.as_updated_date', [$formattedFromDate, $formattedToDate])
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
            $query->where('available_stock.as_mw_id', $MW_ID);
        }

        $result = $query->get();

        return $result;
    }

    function stock_in($REMARK, $ITEMS, $TOTAL_PURCHASE_AMOUNT, $TOTAL_SELLING_AMOUNT, $TOTAL_QTY, $MW_ID, $METHOD = null, $GRN = null)
    {
        $data1 = array(
            'sil_status' => 1,
            'sil_inserted_date' => date('Y-m-d H:i:s'),
            'sil_inserted_by' => session('USER_ID'),
            'sil_remark' => $REMARK,
            'sil_total_items' => count($ITEMS),
            'sil_purchase_amount' => $TOTAL_PURCHASE_AMOUNT,
            'sil_selling_amount' => $TOTAL_SELLING_AMOUNT,
            'sil_total_qty' => $TOTAL_QTY,
            'sil_mw_id' => $MW_ID,
            'sil_method' => $METHOD,
            'sil_grn' => $GRN,
        );
        $STOCK_IN_ID = DB::table('stock_in_list')->insertGetId($data1);

        foreach ($ITEMS as $item) {
            $PRODUCT_ID = $item['id'];
            $PURCHASE_PRICE = $item['purchase'];
            $SELLING_PRICE = $item['selling'];
            $QTY = $item['qty'];

            $data2 = array(
                's_status' => 1,
                's_inserted_date' => date('Y-m-d H:i:s'),
                's_inserted_by' => session('USER_ID'),
                's_p_id' => $PRODUCT_ID,
                's_purchase_amount' => $PURCHASE_PRICE,
                's_selling_amount' => $SELLING_PRICE,
                's_qty' => $QTY,
                's_sil_id' => $STOCK_IN_ID,
                's_mw_id' => $MW_ID,
            );
            DB::table('stock')->insert($data2);

            $STATUS = $this->update_available_stock($PRODUCT_ID, $SELLING_PRICE, $QTY, $MW_ID, 'IN');
            if ($STATUS == false) {
                return false;
            }
        }

        return $STOCK_IN_ID;
    }

    function stock_out($REMARK, $ITEMS, $TOTAL_SELLING_AMOUNT, $TOTAL_QTY, $TOTAL_DISCOUNT, $MW_ID, $WITH_UPDATE_AVAILABLE_STOCK = true)
    {
        $data1 = array(
            'sol_status' => 1,
            'sol_inserted_date' => date('Y-m-d H:i:s'),
            'sol_inserted_by' => session('USER_ID'),
            'sol_remark' => $REMARK,
            'sol_total_items' => count($ITEMS),
            'sol_selling_amount' => $TOTAL_SELLING_AMOUNT,
            'sol_total_qty' => $TOTAL_QTY,
            'sol_total_discount' => $TOTAL_DISCOUNT,
            'sol_mw_id' => $MW_ID,
            'sol_method' => 'POS',
        );
        $STOCK_OUT_ID = DB::table('stock_out_list')->insertGetId($data1);

        foreach ($ITEMS as $item) {
            $ID = $item['id'];
            $DISCOUNT = $item['discount'];
            $QTY = $item['qty'];

            $AB_DETAILS = $this->get_available_stock_data_by_id($ID);

            $DISCOUNTED_PRICE = 0;
            if ($DISCOUNT != 0) {
                $DISCOUNTED_PRICE = $AB_DETAILS->as_selling_price * $DISCOUNT / 100;
            }

            $data2 = array(
                's_status' => 1,
                's_inserted_date' => date('Y-m-d H:i:s'),
                's_inserted_by' => session('USER_ID'),
                's_p_id' => $AB_DETAILS->p_id,
                's_selling_amount' => $AB_DETAILS->as_selling_price,
                's_qty' =>  $QTY,
                's_discount_percentage' =>  $DISCOUNT,
                's_discounted_price' =>  $AB_DETAILS->as_selling_price - $DISCOUNTED_PRICE,
                's_sol_id' => $STOCK_OUT_ID,
                's_mw_id' => $MW_ID,
            );
            DB::table('stock')->insert($data2);

            if ($WITH_UPDATE_AVAILABLE_STOCK == true) {
                $STATUS = $this->update_available_stock($AB_DETAILS->p_id, $AB_DETAILS->as_selling_price, $QTY, $MW_ID, 'OUT');
                if ($STATUS == false) {
                    return false;
                }
            }
        }
        return $STOCK_OUT_ID;
    }

    public function generateInvoiceNo()
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $micro = microtime(true);
        $rand = mt_rand(1000, 9999);
        $unique = strtoupper(substr(md5($micro . $rand), 0, 6));

        return "{$prefix}-{$date}-{$unique}";
    }



    public function generateReturnedInvoiceNo()
    {
        $prefix = 'RINV';
        $date = date('Ymd');
        $micro = microtime(true);
        $rand = mt_rand(1000, 9999);
        $unique = strtoupper(substr(md5($micro . $rand), 0, 6));

        return "{$prefix}-{$date}-{$unique}";
    }

    public function get_payment_types()
    {
        $warehouses = DB::table('master_payment_types')
            ->where('mpt_status', 1)
            ->get();

        return $warehouses;
    }

    public function get_credit_periods()
    {
        $data = DB::table('master_credit_periods')
            ->where('mcp_status', 1)
            ->get();

        return $data;
    }

    public function get_customer_details_by_contact($CONTACT_NO)
    {
        $customers = DB::table('customers')
            ->where('customers.c_status', 1)
            ->where('customers.c_contact', $CONTACT_NO)
            ->first();

        return $customers;
    }

    public function get_customer_details_by_id($ID)
    {
        $customers = DB::table('customers')
            ->where('customers.c_id', $ID)
            ->first();

        return $customers;
    }

    public function get_invoice_details($FROM_DATE, $TO_DATE, $MW_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('invoices')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->where('invoices.in_status', 1)
            ->whereBetween('invoices.in_updated_date', [$formattedFromDate, $formattedToDate]);

        if ($MW_ID != 'ALL') {
            $query->where('invoices.in_mw_id', $MW_ID);
        }

        $result = $query->orderByDesc('invoices.in_updated_date')->get();

        return $result;
    }

    public function get_returned_invoice_details($FROM_DATE, $TO_DATE, $MW_ID)
    {
        $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
        $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

        $query = DB::table('returned_invoices')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'returned_invoices.ri_mw_id')
            ->join('invoices', 'invoices.in_id', '=', 'returned_invoices.ri_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('system_users', 'system_users.su_id', '=', 'returned_invoices.ri_inserted_by')
            ->where('returned_invoices.ri_status', 1)
            ->whereBetween('returned_invoices.ri_inserted_date', [$formattedFromDate, $formattedToDate]);

        if ($MW_ID != 'ALL') {
            $query->where('returned_invoices.ri_mw_id', $MW_ID);
        }

        $result = $query->orderBy('returned_invoices.ri_inserted_date', 'DESC')
            ->select('*')
            ->get();

        return $result;
    }

    public function get_invoice_data_by_id($INVOICE_ID)
    {
        $result = DB::table('invoices')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->where('invoices.in_id', $INVOICE_ID)
            ->first();

        return $result;
    }

    public function get_returned_invoice_data_by_id($RI_ID)
    {
        $result = DB::table('returned_invoices')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'returned_invoices.ri_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'returned_invoices.ri_inserted_by')
            ->join('invoices', 'invoices.in_id', '=', 'returned_invoices.ri_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->where('returned_invoices.ri_id', $RI_ID)
            ->first();

        return $result;
    }

    public function get_returned_invoice_data_by_invoice_no($RI_INVOICE)
    {
        $result = DB::table('returned_invoices')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'returned_invoices.ri_mw_id')
            ->join('invoices', 'invoices.in_id', '=', 'returned_invoices.ri_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->where('returned_invoices.ri_invoice_no', $RI_INVOICE)
            ->where('returned_invoices.ri_status', 1)
            ->first();

        return $result;
    }

    public function get_returned_invoice_data_by_invoice_no_with_status($RI_INVOICE, $CLAIM)
    {
        $result = DB::table('returned_invoices')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'returned_invoices.ri_mw_id')
            ->join('invoices', 'invoices.in_id', '=', 'returned_invoices.ri_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->where('returned_invoices.ri_invoice_no', $RI_INVOICE)
            ->where('returned_invoices.ri_claim_status', $CLAIM)
            ->where('returned_invoices.ri_status', 1)
            ->first();

        return $result;
    }

    public function get_returned_invoice_items_by_id($RI_ID)
    {
        $result = DB::table('returned_invoice_items')
            ->join('returned_invoices', 'returned_invoices.ri_id', '=', 'returned_invoice_items.rii_ri_id')
            ->join('products', 'products.p_id', '=', 'returned_invoice_items.rii_p_id')
            ->where('returned_invoice_items.rii_status', 1)
            ->where('returned_invoices.ri_id', $RI_ID)
            ->get();

        return $result;
    }

    public function get_invoice_data_by_invoice_number($INVOICE_NUMBER)
    {
        $result = DB::table('invoices')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->where('invoices.in_invoice_no', $INVOICE_NUMBER)
            ->first();

        return $result;
    }

    public function get_invoice_items_by_id($INVOICE_ID)
    {
        $result = DB::table('invoice_items')
            ->join('invoices', 'invoices.in_id', '=', 'invoice_items.ini_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('products', 'products.p_id', '=', 'invoice_items.ini_p_id')
            ->where('invoice_items.ini_status', 1)
            ->where('invoices.in_id', $INVOICE_ID)
            ->get();

        return $result;
    }




    public function get_product_available_list($P_ID)
    {
        $data = DB::table('available_stock')
            ->join('master_warehouses', function ($join) {
                $join->on('master_warehouses.mw_id', '=', 'available_stock.as_mw_id')
                    ->where('master_warehouses.mw_mwt_id', 1)
                    ->where('master_warehouses.mw_status', 1);
            })
            ->where('available_stock.as_status', 1)
            ->where('available_stock.as_p_id', $P_ID)
            ->orderBy('available_stock.as_updated_date', 'asc')
            ->get();

        return $data;
    }

    public function get_customer_invoices($CUS_ID, $IS_COUNT, $FROM_DATE = null, $TO_DATE = null, $MW_ID = null)
    {
        $data = DB::table('invoices')
            ->select('*')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->join('customers', 'customers.c_id', '=', 'invoices.in_customer_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->where('invoices.in_is_corparate', 0)
            ->where('customers.c_id', $CUS_ID);


        if ($IS_COUNT == 0) {
            $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
            $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

            $data->whereBetween('invoices.in_inserted_date', [$formattedFromDate, $formattedToDate]);
            if ($MW_ID != 'ALL') {
                $data->where('invoices.in_mw_id', $MW_ID);
            }

            $result = $data->get();
        } else {
            $result = (string)$data->count();
        }

        return $result;
    }

    public function get_user_invoices($USER_ID, $IS_COUNT, $FROM_DATE = null, $TO_DATE = null, $MW_ID = null)
    {
        $data = DB::table('invoices')
            ->select('*')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('system_users', 'system_users.su_id', '=', 'invoices.in_inserted_by')
            ->join('master_payment_types', 'master_payment_types.mpt_id', '=', 'invoices.in_mpt_id')
            ->leftJoin('customers', 'customers.c_id', '=', 'invoices.in_customer_id')
            ->join('master_warehouses', 'master_warehouses.mw_id', '=', 'invoices.in_mw_id')
            ->where('invoices.in_is_corparate', 0)
            ->where('invoices.in_inserted_by', $USER_ID);


        if ($IS_COUNT == 0) {
            $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
            $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));

            $data->whereBetween('invoices.in_inserted_date', [$formattedFromDate, $formattedToDate]);
            if ($MW_ID != 'ALL') {
                $data->where('invoices.in_mw_id', $MW_ID);
            }

            $result = $data->get();
        } else {
            $result = (string)$data->count();
        }

        return $result;
    }

    public function get_av_stocks($P_ID, $MW_ID)
    {
        $result = DB::table('available_stock')
            ->join(
                'products',
                'products.p_id',
                '=',
                'available_stock.as_p_id'
            )
            ->join(
                'master_warehouses',
                'master_warehouses.mw_id',
                '=',
                'available_stock.as_mw_id'
            )
            ->where('available_stock.as_status', 1)
            ->where('available_stock.as_available_qty', '>', 0)
            ->where('products.p_id', $P_ID)
            ->where('master_warehouses.mw_id', $MW_ID)
            ->orderBy('available_stock.as_inserted_date', 'ASC')
            ->select('*')
            ->get();

        return $result;
    }

    public function get_returned_invoice_by_in_no($INVOICE_ID)
    {
        $result = DB::table('returned_invoices')
            ->join('invoices', 'invoices.in_id', '=', 'returned_invoices.ri_in_id')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->join('returned_invoice_items', 'returned_invoice_items.rii_ri_id', '=', 'returned_invoices.ri_id')
            ->join('products', 'products.p_id', '=', 'returned_invoice_items.rii_p_id')
            ->join('system_users', 'system_users.su_id', '=', 'returned_invoices.ri_inserted_by')
            ->where('returned_invoices.ri_id', $INVOICE_ID)
            ->where('returned_invoices.ri_status', 1)
            ->where('returned_invoice_items.rii_status', 1)
            ->select('*')
            ->get();

        return $result;
    }
}
