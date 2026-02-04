<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\StockModel;
use App\Models\ValidationModel;
use App\Models\CommonModel;
use App\Models\UserModel;
use App\Models\SettingsModel;
use App\Models\OrdersModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;
use Barryvdh\DomPDF\Facade\Pdf;

class StockController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Stock_In()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations_by_type(1);

        return view('Stock/Stock_In', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function product_search(Request $request)
    {
        $StockModel = new StockModel();

        $q = $request->get('q');

        $PRODUCTS = $StockModel->search_products($q);

        return response()->json($PRODUCTS);
    }

    public function save_stock_in(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel     = new CommonModel();
        $StockModel      = new StockModel();

        // ---------- BASIC INPUTS ----------
        $ITEMS  = json_decode($request->input('items'), true);
        $REMARK = trim($request->input('remark'));
        $MW_ID  = trim($request->input('mw_id'));
        $GRN    = trim($request->input('grn'));

        // ---------- BASIC VALIDATIONS ----------
        if (empty($ITEMS) || !is_array($ITEMS)) {
            return response()->json(['error' => 'Items not found!']);
        }

        if ($ValidationModel->is_invalid_data($MW_ID)) {
            return response()->json(['error' => 'Please select warehouse!']);
        }

        if ($ValidationModel->is_invalid_data($GRN)) {
            return response()->json(['error' => 'GRN not found!']);
        }

        // ---------- TOTALS ----------
        $TOTAL_PURCHASE_AMOUNT = 0;
        $TOTAL_SELLING_AMOUNT  = 0;
        $TOTAL_QTY             = 0;

        // ---------- ITEM VALIDATIONS ----------
        foreach ($ITEMS as $index => $item) {

            $PRODUCT_ID     = $item['id'] ?? null;
            $PURCHASE_PRICE = $item['purchase'] ?? null;
            $SELLING_PRICE  = $item['selling'] ?? null;
            $QTY            = $item['qty'] ?? null;

            if ($ValidationModel->is_invalid_data($PRODUCT_ID) || !is_numeric($PRODUCT_ID)) {
                return response()->json(['error' => 'Invalid Product ID at row ' . ($index + 1)]);
            }

            if ($ValidationModel->is_invalid_data($PURCHASE_PRICE) || !is_numeric($PURCHASE_PRICE) || $PURCHASE_PRICE < 0) {
                return response()->json(['error' => 'Invalid Purchase Price at row ' . ($index + 1)]);
            }

            if ($ValidationModel->is_invalid_data($SELLING_PRICE) || !is_numeric($SELLING_PRICE) || $SELLING_PRICE < 0) {
                return response()->json(['error' => 'Invalid Selling Price at row ' . ($index + 1)]);
            }

            if ($ValidationModel->is_invalid_data($QTY) || !is_numeric($QTY) || $QTY <= 0) {
                return response()->json(['error' => 'Invalid Quantity at row ' . ($index + 1)]);
            }

            // totals
            $TOTAL_PURCHASE_AMOUNT += ($PURCHASE_PRICE * $QTY);
            $TOTAL_SELLING_AMOUNT  += ($SELLING_PRICE * $QTY);
            $TOTAL_QTY             += $QTY;
        }

        // ---------- DB TRANSACTION ----------
        DB::beginTransaction();
        try {

            // Save stock in
            $STOCK_IN_ID = $StockModel->stock_in(
                $REMARK,
                $ITEMS,
                $TOTAL_PURCHASE_AMOUNT,
                $TOTAL_SELLING_AMOUNT,
                $TOTAL_QTY,
                $MW_ID,
                'IN',
                $GRN
            );

            if (!$STOCK_IN_ID) {
                DB::rollBack();
                return response()->json(['error' => 'Invalid Stock amount.']);
            }

            // ---------- FILE UPLOADS ----------
            if ($request->hasFile('FILES')) {

                foreach ($request->file('FILES') as $file) {

                    // ❌ Validate MIME type
                    if ($file->getMimeType() !== 'application/pdf') {
                        DB::rollBack();
                        return response()->json([
                            'error' => 'Only PDF files are allowed (File #' . ($index + 1) . ')'
                        ]);
                    }

                    // ❌ Optional: Validate extension too (extra safety)
                    if (strtolower($file->getClientOriginalExtension()) !== 'pdf') {
                        DB::rollBack();
                        return response()->json([
                            'error' => 'Invalid file extension. Only PDF allowed.'
                        ]);
                    }



                    $filename = time() . '_' . $file->getClientOriginalName();
                    $folderPath = 'Stock_in_attachments/' . $STOCK_IN_ID;
                    $filePath   = $folderPath . '/' . $filename;
                    $file->move(public_path($folderPath), $filename);

                    DB::table('stock_in_list_uploads')->insert([
                        'silu_status'        => 1,
                        'silu_inserted_date' => date('Y-m-d H:i:s'),
                        'silu_inserted_by'   => session('USER_ID'),
                        'silu_file_path'     => $filePath,
                        'silu_file_name'     => $file->getClientOriginalName(),
                        'silu_sil_id'        => $STOCK_IN_ID
                    ]);
                }
            }

            // work log
            $CommonModel->update_work_log(
                session('USER_ID'),
                'Stock In saved successfully. Stock In ID: ' . $STOCK_IN_ID
            );

            DB::commit();
            return response()->json(['success' => 'Stock In saved successfully.']);
        } catch (\Exception $e) {

            DB::rollBack();
            return response()->json([
                'error' => 'An error occurred. Rollback executed.',
                'debug' => $e->getMessage()
            ]);
        }
    }


    public function Stock_In_History()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Stock/Stock_In_History', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function get_stock_in_filter_validation($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $MW_ID = trim($request->input('MW_ID'));

        $is_set_from_date = true;
        $is_set_to_date = true;

        if ($ValidationModel->is_invalid_data($FROM_DATE)) {
            $is_set_from_date = false;
            $error .= "- From Date cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($TO_DATE)) {
            $is_set_to_date = false;
            $error .= "- To Date cannot be empty<br>";
        }

        if ($is_set_from_date == true && $is_set_to_date == true) {
            if ($ValidationModel->is_invalid_date_range($FROM_DATE, $TO_DATE)) {
                $error .= "- Invalid date range<br>";
            }
        }
        if ($ValidationModel->is_invalid_data($MW_ID)) {
            $error .= "- Warehouse cannot be empty<br>";
        }


        return $error;
    }

    public function get_stock_in_filter_result(Request $request)
    {
        $status = $this->get_stock_in_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('Stock/Stock_In_History_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_stock_in_filter_result_table(Request $request)
    {
        $status = $this->get_stock_in_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_stock_in_history_details($FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Stock/Download/Stock_In_History_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Stock In History Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function load_stock_in_detail_view(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));
        $SIL_ID = $DATA->SIL_ID;

        $STOCK_IN_DETAILS = $StockModel->get_stock_in_details_by_id($SIL_ID);
        $STOCK_IN_ITEM_DETAILS = $StockModel->get_stock_items_in_details_by_id($SIL_ID);
        $STOCK_IN_DOCUMENTS = $StockModel->get_stock_in_documents($SIL_ID);

        $view = (string)view('Stock/Stock_In_Detail_View', [
            'SIL_ID' => $SIL_ID,
            'STOCK_IN_DETAILS' => $STOCK_IN_DETAILS,
            'STOCK_IN_ITEM_DETAILS' => $STOCK_IN_ITEM_DETAILS,
            'STOCK_IN_DOCUMENTS' => $STOCK_IN_DOCUMENTS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function save_invoice(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();
        $SettingsModel = new SettingsModel();
        $UserModel = new UserModel();

        $ITEMS = $request->input('items');
        $MW_ID = trim($request->input('MW_ID'));
        $REMARK = "";
        $DISCOUNT_PERCENTAGE = empty(trim($request->input('DISCOUNT_PERCENTAGE'))) ? 0 : trim($request->input('DISCOUNT_PERCENTAGE'));
        $DISCOUNT_AMOUNT = empty(trim($request->input('DISCOUNT_AMOUNT'))) ? 0 : trim($request->input('DISCOUNT_AMOUNT'));
        $RETURNED_INVOICE_AMOUNT = empty(trim($request->input('RETURNED_INVOICE_AMOUNT'))) ? 0 : trim($request->input('RETURNED_INVOICE_AMOUNT'));
        $MPT_ID = trim($request->input('MPT_ID'));
        $CASH_PAID = trim($request->input('CASH_PAID'));
        $CARD_PAID = trim($request->input('CARD_PAID'));
        $TOTAL_PAID_AMOUNT = trim($request->input('TOTAL_PAID_AMOUNT'));
        $BALANCE_AMOUNT = trim($request->input('BALANCE_AMOUNT'));
        $CUS_ID = trim($request->input('CUS_ID'));
        $IS_CORPARATE = trim($request->input('IS_CORPARATE'));
        $RI_ID = trim($request->input('RI_ID'));
        $OR_ID = trim($request->input('OR_ID'));
        $MCP_ID = trim($request->input('MCP_ID'));

        if (empty($ITEMS)) {
            return json_encode(array('error' => 'Items not found!'));
        }
        if ($ValidationModel->is_invalid_data($MW_ID)) {
            return json_encode(array('error' => 'Please select warehouse!'));
        }

        $TOTAL_SELLING_AMOUNT = 0;
        $TOTAL_QTY = 0;
        $TOTAL_DISCOUNT = 0;
        $SUB_TOTAL = 0;

        foreach ($ITEMS as $item) {
            $ID = $item['id'];
            $DISCOUNT = $item['discount'];
            $QTY = $item['qty'];

            if ($ValidationModel->is_invalid_data($DISCOUNT)) {
                // return json_encode(array('error' => 'Discount not found!'));
            } else if (!is_numeric($DISCOUNT)) {
                return json_encode(array('error' => 'Invalid Discount price!'));
            }
            if ($ValidationModel->is_invalid_data($QTY)) {
                return json_encode(array('error' => 'Quntiity not found!'));
            } else if (!is_numeric($QTY)) {
                return json_encode(array('error' => 'Invalid Quntiity!'));
            } else if ($QTY <= 0) {
                return json_encode(array('error' => 'Invalid Quntiity!'));
            }


            $AB_DETAILS = $StockModel->get_available_stock_data_by_id($ID);

            if ($AB_DETAILS == false) {
                return json_encode(array('error' => 'Available Stock cannot be accessed!'));
            } else {
                $AVAILABLE_QTY = $AB_DETAILS->as_available_qty;
                if ($AVAILABLE_QTY < $QTY) {
                    return json_encode(array(
                        'error' => "Insufficient stock for {$AB_DETAILS->p_name} (Code: {$AB_DETAILS->p_id}). " .
                            "Only {$AB_DETAILS->as_available_qty} units available at Rs. " . number_format($AB_DETAILS->as_selling_price, 2) . " each."
                    ));
                }
            }

            $TOTAL_SELLING_AMOUNT = $TOTAL_SELLING_AMOUNT + ($AB_DETAILS->as_selling_price * $QTY);
            $TOTAL_QTY = $TOTAL_QTY + $QTY;
            $DISCOUNT_PRICE = 0;
            if ($DISCOUNT != 0) {
                $DISCOUNT_PRICE = ($AB_DETAILS->as_selling_price * $QTY) * $DISCOUNT / 100;
                $TOTAL_DISCOUNT = $TOTAL_DISCOUNT +  $DISCOUNT_PRICE;
            }

            $SUB_TOTAL = $SUB_TOTAL + (($AB_DETAILS->as_selling_price * $QTY) - $DISCOUNT_PRICE);
        }


        if ($MPT_ID != 5) {
            if ($SUB_TOTAL <= 0) {
                return json_encode(array('error' => 'Invalid total amount. Total payable must be greater than 0!'));
            } else if ($SUB_TOTAL > $TOTAL_PAID_AMOUNT) {
                return json_encode(array('error' => 'Insufficient payment. Total paid must match the payable amount.'));
            }
        } else {
            if ($ValidationModel->is_invalid_data($MCP_ID) || $MCP_ID == 0) {
                return json_encode(array('error' => 'Please select credit period.'));
            }
        }

        $VAT_INVOICE = false;
        $CUSTOMER_DETAILS = $UserModel->get_organization_data($CUS_ID);
        if ($CUSTOMER_DETAILS == true) {
            if ($CUSTOMER_DETAILS->o_is_vat_registered == 1) {
                $VAT_INVOICE = true;
            }
        }

        DB::beginTransaction();
        try {

            $data1 = array(
                'in_status' => 1,
                'in_inserted_date' => date('Y-m-d H:i:s'),
                'in_inserted_by' => session('USER_ID'),
                'in_updated_date' => date('Y-m-d H:i:s'),
                'in_updated_by' => session('USER_ID'),
                'in_invoice_no' => $StockModel->generateInvoiceNo($VAT_INVOICE),
                'in_sub_total' => $SUB_TOTAL,
                'in_discount_percentage' => $DISCOUNT_PERCENTAGE,
                'in_discount_amount' => $DISCOUNT_AMOUNT,
                'in_returned_amount' => $RETURNED_INVOICE_AMOUNT,
                'in_total_payable' => $SUB_TOTAL - ($DISCOUNT_AMOUNT + $RETURNED_INVOICE_AMOUNT),
                'in_mpt_id' => $MPT_ID,
                'in_cash_paid' => $CASH_PAID,
                'in_card_paid' => $CARD_PAID,
                'in_total_paid_amount' => $TOTAL_PAID_AMOUNT,
                'in_total_balance' => ($SUB_TOTAL - ($DISCOUNT_AMOUNT + $RETURNED_INVOICE_AMOUNT)) -  $TOTAL_PAID_AMOUNT,
                'in_customer_id' => $CUS_ID,
                'in_is_corparate' => $IS_CORPARATE,
                'in_is_returned' => 0,
                'in_mw_id' => $MW_ID,
                'in_vat_rate' => config('constants.VAT_RATE'),
                'in_mcp_id' => $MPT_ID == 5 ? $MCP_ID : null,
                'in_is_credit_settled' => 0,
                'in_is_vat_invoice' => $VAT_INVOICE == true ? 1 : 0,
            );
            $IN_ID = DB::table('invoices')->insertGetId($data1);

            foreach ($ITEMS as $item) {
                $ID = $item['id'];
                $DISCOUNT = $item['discount'];
                $QTY = $item['qty'];

                $AB_DETAILS = $StockModel->get_available_stock_data_by_id($ID);
                $DISCOUNT_PRICE = 0;
                if ($DISCOUNT != 0) {
                    $DISCOUNT_PRICE = $AB_DETAILS->as_selling_price * $DISCOUNT / 100;
                }
                $DISCOUNTED_PRICE = $AB_DETAILS->as_selling_price - $DISCOUNT_PRICE;


                $data2 = array(
                    'ini_status' => 1,
                    'ini_inserted_date' => date('Y-m-d H:i:s'),
                    'ini_inserted_by' => session('USER_ID'),
                    'ini_in_id' => $IN_ID,
                    'ini_p_id' => $AB_DETAILS->p_id,
                    'ini_qty' => $QTY,
                    'ini_selling_price' => $AB_DETAILS->as_selling_price,
                    'ini_discount_percentage' => $DISCOUNT,
                    'ini_final_price' => $DISCOUNTED_PRICE,
                    'ini_is_returned' => 0,
                );
                DB::table('invoice_items')->insert($data2);
            }

            $STOCK_OUT_ID = $StockModel->stock_out($REMARK, $ITEMS, $TOTAL_SELLING_AMOUNT, $TOTAL_QTY, $TOTAL_DISCOUNT, $MW_ID);

            if ($STOCK_OUT_ID == false) {
                return json_encode(array('error' => "Invalid Stock amount."));
            }

            // update punch data
            $WAREHOUSE_DATA = $SettingsModel->get_stock_location_details_by_id($MW_ID);
            if (!in_array($WAREHOUSE_DATA->mwt_id, [1, 3])) {
                $PUNCH = $SettingsModel->get_active_punch($WAREHOUSE_DATA->mw_id);
                if ($PUNCH == false) {
                    return json_encode(array('error' => "You havent active punch."));
                } else {
                    $data = array(
                        'pu_amount' => $PUNCH->pu_amount + $TOTAL_PAID_AMOUNT
                    );
                    DB::table('punches')
                        ->where('pu_id', $PUNCH->pu_id)
                        ->update($data);
                }
            }

            $RETURNED_INVOICE = $StockModel->get_returned_invoice_data_by_id($RI_ID);
            if ($RETURNED_INVOICE == true) {
                $update_data = array(
                    'ri_claim_status' => 1,
                    'ri_claim_date' => date('Y-m-d H:i:s'),
                    'ri_claim_by' => session('USER_ID'),
                );
                DB::table('returned_invoices')
                    ->where('ri_id', $RI_ID)
                    ->update($update_data);
            }

            // order complete action
            $OrdersModel = new OrdersModel();
            $ORDER = $OrdersModel->get_order_details($OR_ID);
            $ORDER_ITEMS = $OrdersModel->get_order_items($OR_ID, $CUS_ID);
            foreach ($ORDER_ITEMS as $item) {
                $data_update = array(
                    'ori_updated_date' => date('Y-m-d H:i:s'),
                    'ori_updated_by' => session('USER_ID'),
                    'ori_complete' => 1,
                );
                DB::table('order_items')
                    ->where('ori_id', $item->ori_id)
                    ->update($data_update);
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Stock In saved successfully. Stock Out ID:' . $STOCK_OUT_ID);

            DB::commit();
            return json_encode(array('success' => 'Stock In saved successfully.', 'in_id' => $IN_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Stock(Request $request)
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Stock/Stock', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function get_stock_filter_validation($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $MW_ID = trim($request->input('MW_ID'));

        $is_set_from_date = true;
        $is_set_to_date = true;

        if ($ValidationModel->is_invalid_data($FROM_DATE)) {
            $is_set_from_date = false;
            $error .= "- From Date cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($TO_DATE)) {
            $is_set_to_date = false;
            $error .= "- To Date cannot be empty<br>";
        }

        if ($is_set_from_date == true && $is_set_to_date == true) {
            if ($ValidationModel->is_invalid_date_range($FROM_DATE, $TO_DATE)) {
                $error .= "- Invalid date range<br>";
            }
        }
        if ($ValidationModel->is_invalid_data($MW_ID)) {
            $error .= "- Warehouse cannot be empty<br>";
        }


        return $error;
    }

    public function get_stock_filter_result(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('Stock/Stock_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_stock_filter_result_table(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_stock_details($FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Stock/Download/Stock_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Stock Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function Invoices(Request $request)
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Stock/Invoices', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function Returned_Invoices(Request $request)
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Stock/Returned_Invoices', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function get_invoice_filter_result(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('Stock/Invoices_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_returned_invoice_filter_result(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('Stock/Returned_Invoices_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_invoices_filter_result_table(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_invoice_details($FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Stock/Download/Invoice_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Stock Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function get_returned_invoices_filter_result_table(Request $request)
    {
        $status = $this->get_stock_filter_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_returned_invoice_details($FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Stock/Download/Invoice_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Stock Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function load_invoice(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));
        $INVOICE_ID = $DATA->INVOICE_ID;

        $StockModel = new StockModel();
        $UserModel = new UserModel();

        $INVOICE_DATA = $StockModel->get_invoice_data_by_id($INVOICE_ID);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($INVOICE_ID);

        $RETURNED_INVOICE = $StockModel->get_returned_invoice_by_in_no($INVOICE_ID);

        if ($INVOICE_DATA->in_is_corparate == 1) {
            $CUSTOMER_DETAILS = $UserModel->get_organization_data($INVOICE_DATA->in_customer_id);
        } else if (!empty($INVOICE_DATA->in_customer_id)) {
            $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($INVOICE_DATA->in_customer_id);
        } else {
            $CUSTOMER_DETAILS = [];
        }

        $view = (string)view('Stock/Invoice_View', [
            'INVOICE_ID' => $INVOICE_ID,
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
            'RETURNED_INVOICE' => $RETURNED_INVOICE,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_returned_invoice(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));
        $RETURNED_INVOICE_ID = $DATA->INVOICE_ID;

        $StockModel = new StockModel();

        $RETURNED_INVOICE = $StockModel->get_returned_invoice_by_in_no($RETURNED_INVOICE_ID);

        $view = (string)view('Stock/Retruned_Invoice_View', [
            'RETURNED_INVOICE_ID' => $RETURNED_INVOICE_ID,
            'RETURNED_INVOICE' => $RETURNED_INVOICE,
        ]);
        return json_encode(array('result' => $view));
    }

    public function Normal_Invoice($IN_ID)
    {
        $INVOICE_ID = urldecode(base64_decode($IN_ID));

        $StockModel = new StockModel();
        $UserModel = new UserModel();

        $INVOICE_DATA = $StockModel->get_invoice_data_by_id($INVOICE_ID);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($INVOICE_ID);

        if ($INVOICE_DATA->in_is_corparate == 1) {
            $CUSTOMER_DETAILS = $UserModel->get_organization_data($INVOICE_DATA->in_customer_id);
        } else if (!empty($INVOICE_DATA->in_customer_id)) {
            $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($INVOICE_DATA->in_customer_id);
        } else {
            $CUSTOMER_DETAILS = [];
        }


        $pdf = Pdf::loadView('POS.Invoice.Normal_Invoice_View', [
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->stream('Invoice - ' . $INVOICE_DATA->in_invoice_no . '.pdf');
    }

    public function VAT_Invoice($IN_ID)
    {
        $INVOICE_ID = urldecode(base64_decode($IN_ID));

        $StockModel = new StockModel();
        $UserModel = new UserModel();

        $INVOICE_DATA = $StockModel->get_invoice_data_by_id($INVOICE_ID);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($INVOICE_ID);

        if ($INVOICE_DATA->in_is_corparate == 1) {
            $CUSTOMER_DETAILS = $UserModel->get_organization_data($INVOICE_DATA->in_customer_id);
        } else if (!empty($INVOICE_DATA->in_customer_id)) {
            $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($INVOICE_DATA->in_customer_id);
        } else {
            $CUSTOMER_DETAILS = [];
        }

        $pdf = Pdf::loadView('POS.Invoice.VAT_Invoice_View', [
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->stream('VAT Invoice - ' . $INVOICE_DATA->in_invoice_no . '.pdf');
    }

    public function Request_Destroy()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Destroy/Request_Destroy', [
            'WAREHOUSES' => $WAREHOUSES
        ]);
    }

    public function save_destroy_request(Request $request)
    {
        $StockModel = new StockModel();

        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();

        $ITEMS = $request->input('items');
        $MW_ID = trim($request->input('MW_ID'));

        $TOTAL_SELLING_AMOUNT = 0;
        $TOTAL_QTY = 0;

        if ($ValidationModel->is_invalid_data($MW_ID) || $MW_ID == 0) {
            return json_encode(array('error' => 'Warehouse not selected.'));
        }
        if (count($ITEMS) <= 0) {
            return json_encode(array('error' => 'Products not selected.'));
        }

        foreach ($ITEMS as $item) {
            $ID = $item['id'];
            $QTY = $item['qty'];

            if ($ValidationModel->is_invalid_data($QTY)) {
                return json_encode(array('error' => 'Quntiity not found!'));
            } else if (!is_numeric($QTY)) {
                return json_encode(array('error' => 'Invalid Quntiity!'));
            } else if ($QTY <= 0) {
                return json_encode(array('error' => 'Invalid Quntiity!'));
            }


            $AB_DETAILS = $StockModel->get_available_stock_data_by_id($ID);

            if ($AB_DETAILS == false) {
                return json_encode(array('error' => 'Available Stock cannot be accessed!'));
            } else {
                $AVAILABLE_QTY = $AB_DETAILS->as_available_qty;
                if ($AVAILABLE_QTY < $QTY) {
                    return json_encode(array(
                        'error' => "Insufficient stock for {$AB_DETAILS->p_name} (Code: {$AB_DETAILS->p_id}). " .
                            "Only {$AB_DETAILS->as_available_qty} units available at Rs. " . number_format($AB_DETAILS->as_selling_price, 2) . " each."
                    ));
                }
            }

            $TOTAL_SELLING_AMOUNT = $TOTAL_SELLING_AMOUNT + ($AB_DETAILS->as_selling_price * $QTY);
            $TOTAL_QTY = $TOTAL_QTY + $QTY;
        }

        DB::beginTransaction();
        try {

            $data1 = array(
                'dr_status' => 1,
                'dr_inserted_date' => date('Y-m-d H:i:s'),
                'dr_inserted_by' => session('USER_ID'),
                'dr_updated_date' => date('Y-m-d H:i:s'),
                'dr_updated_by' => session('USER_ID'),
                'dr_drs_id' => 1,
                'dr_mw_id' => $MW_ID,
                'dr_item_count' => count($ITEMS),
                'dr_tot_amount' => $TOTAL_SELLING_AMOUNT,
                'dr_tot_qty' => $TOTAL_QTY,
            );
            $REQ_ID = DB::table('destroy_requests')->insertGetId($data1);

            foreach ($ITEMS as $item) {
                $ID = $item['id'];
                $QTY = $item['qty'];

                $AB_DETAILS = $StockModel->get_available_stock_data_by_id($ID);

                $data2 = array(
                    'dri_status' => 1,
                    'dri_inserted_date' => date('Y-m-d H:i:s'),
                    'dri_inserted_by' => session('USER_ID'),
                    'dri_updated_date' => date('Y-m-d H:i:s'),
                    'dri_updated_by' => session('USER_ID'),
                    'dri_dr_id' => $REQ_ID,
                    'dri_p_id' => $AB_DETAILS->p_id,
                    'dri_qty' => $QTY,
                    'dri_selling_amount' => $AB_DETAILS->as_selling_price,
                    'dri_is_removed' => 0,
                );
                DB::table('destroy_request_items')->insert($data2);
            }

            $STOCK_OUT_ID = $StockModel->stock_out('', $ITEMS, $TOTAL_SELLING_AMOUNT, $TOTAL_QTY, 0, $MW_ID, true, 'DESTROY');
            if ($STOCK_OUT_ID == false) {
                return json_encode(array('error' => "Invalid Stock amount."));
            }

            $data3 = array(
                'dra_status' => 1,
                'dra_inserted_date' => date('Y-m-d H:i:s'),
                'dra_inserted_by' => session('USER_ID'),
                'dra_is_active' => 1,
                'dra_dr_id' => $REQ_ID,
                'dra_aa_id' => 1
            );
            DB::table('destroy_request_approvals')->insert($data3);

            $StockModel->update_destroy_request_timeline($REQ_ID, 'REQUEST CREATED', 'Request has been created.');

            $CommonModel->update_work_log(session('USER_ID'), 'Destroy Request has been created. Request ID:' . $REQ_ID);

            DB::commit();
            return json_encode(array('success' => 'Destroy Request has been created. Request ID:' . $REQ_ID, 'req_id' => $REQ_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Destroy_Request_View($REQ_ID)
    {
        $StockModel = new StockModel();
        $OrdersModel = new OrdersModel();

        $REQ_ID = base64_decode(urldecode($REQ_ID));

        $DESTROY_DETAILS = $StockModel->get_destroy_data_by_id($REQ_ID);
        $DESTROY_ITEMS = $StockModel->get_destroy_items_by_id($REQ_ID, 0);
        $REMOVED_DESTROY_ITEMS = $StockModel->get_destroy_items_by_id($REQ_ID, 1);
        $TIMELINE = $StockModel->get_destroy_request_timeline_by_id($REQ_ID);
        $APPROVALS = $StockModel->get_destroy_request_approvals($REQ_ID);
        $APPROVALS_ACTION = $OrdersModel->get_approval_actions();

        return view('Destroy/Destroy_Request_View', [
            'REQ_ID' => $REQ_ID,
            'DESTROY_DETAILS' => $DESTROY_DETAILS,
            'DESTROY_ITEMS' => $DESTROY_ITEMS,
            'REMOVED_DESTROY_ITEMS' => $REMOVED_DESTROY_ITEMS,
            'TIMELINE' => $TIMELINE,
            'APPROVALS' => $APPROVALS,
            'APPROVALS_ACTION' => $APPROVALS_ACTION,
        ]);
    }

    public function destroy_request_approve_action(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();

        $DRA_ID = trim($request->input('DRA_ID'));
        $AA_ID = trim($request->input('AA_ID'));
        $REMARK = trim($request->input('REMARK'));

        if ($ValidationModel->is_invalid_data($AA_ID)) {
            return json_encode(array('error' => 'Action not selected!'));
        } else if ($AA_ID == 3) {
            if ($ValidationModel->is_invalid_data($REMARK)) {
                return json_encode(array('error' => 'Remark not found!'));
            }
        }

        $APPROVAL_DETAILS = $StockModel->get_destroy_request_approval_details($DRA_ID);
        $DESTROY_ITEMS = $StockModel->get_destroy_items_by_id($APPROVAL_DETAILS->dra_dr_id, 0);

        $qty_count = 0;
        $item_count = 0;
        $tot_value = 0;
        foreach ($DESTROY_ITEMS as $ITEM) {
            $qty_count = $qty_count + $ITEM->dri_qty;
            $item_count++;
            $tot_value = $tot_value + $ITEM->dri_selling_amount;
        }

        DB::beginTransaction();
        try {
            $data1 = array(
                'dra_action_date' => date('Y-m-d H:i:s'),
                'dra_action_by' => session('USER_ID'),
                'dra_aa_id' => $AA_ID,
                'dra_remark' => $REMARK,
            );
            DB::table('destroy_request_approvals')
                ->where('dra_id', $DRA_ID)
                ->update($data1);

            $data2 = array(
                'dr_updated_date' => date('Y-m-d H:i:s'),
                'dr_updated_by' => session('USER_ID'),
                'dr_drs_id' => $AA_ID == 2 ? 2 : 3,
                'dr_item_count' => $item_count,
                'dr_tot_amount' => $tot_value,
                'dr_tot_qty' => $qty_count,
            );
            DB::table('destroy_requests')
                ->where('dr_id', $APPROVAL_DETAILS->dra_dr_id)
                ->update($data2);

            if ($AA_ID == 2) {
                $DESCRIPTION = "Destroy has been approved.";
            } else {
                $DESTROY_DETAILS = $StockModel->get_destroy_data_by_id($APPROVAL_DETAILS->dra_dr_id);
                $DESTROY_ITEMS = $StockModel->get_destroy_items_by_id($APPROVAL_DETAILS->dra_dr_id, 0);

                if (count($DESTROY_ITEMS) > 0) {
                    $data1 = array(
                        'sil_status' => 1,
                        'sil_inserted_date' => date('Y-m-d H:i:s'),
                        'sil_inserted_by' => session('USER_ID'),
                        'sil_remark' => '',
                        'sil_total_items' => count($DESTROY_ITEMS),
                        'sil_mw_id' => $DESTROY_DETAILS->dr_mw_id,
                        'sil_method' => 'DESTROY',
                    );
                    $STOCK_IN_ID = DB::table('stock_in_list')->insertGetId($data1);

                    $TOTAL_PURCHASE_AMOUNT = 0;
                    $TOTAL_SELLING_AMOUNT = 0;
                    $TOTAL_QTY = 0;
                    foreach ($DESTROY_ITEMS as $ITEM) {
                        $TOTAL_PURCHASE_AMOUNT =   $TOTAL_PURCHASE_AMOUNT + 0;
                        $TOTAL_SELLING_AMOUNT =   $TOTAL_SELLING_AMOUNT + $ITEM->dri_selling_amount;
                        $TOTAL_QTY =   $TOTAL_QTY + $ITEM->dri_qty;

                        $data3 = array(
                            's_status' => 1,
                            's_inserted_date' => date('Y-m-d H:i:s'),
                            's_inserted_by' => session('USER_ID'),
                            's_p_id' => $ITEM->p_id,
                            's_purchase_amount' => 0,
                            's_selling_amount' => $ITEM->dri_selling_amount,
                            's_qty' => $ITEM->dri_qty,
                            's_sil_id' => $STOCK_IN_ID,
                            's_mw_id' => $DESTROY_DETAILS->dr_mw_id,
                        );
                        DB::table('stock')->insert($data3);

                        $STATUS = $StockModel->update_available_stock($ITEM->p_id, $ITEM->dri_selling_amount,  $ITEM->dri_qty, $DESTROY_DETAILS->dr_mw_id, 'IN');
                        if ($STATUS == false) {
                            return false;
                        }
                    }

                    $data4 = array(
                        'sil_purchase_amount' => $TOTAL_PURCHASE_AMOUNT,
                        'sil_selling_amount' => $TOTAL_SELLING_AMOUNT,
                        'sil_total_qty' => $TOTAL_QTY,
                    );
                    DB::table('stock_in_list')
                        ->where('sil_id', $STOCK_IN_ID)
                        ->update($data4);
                }


                $DESCRIPTION = "Destroy has been rejected.";
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Destroy Approval action has been placed. Request Id:' . $APPROVAL_DETAILS->dra_dr_id);

            $StockModel->update_destroy_request_timeline($APPROVAL_DETAILS->dra_dr_id, 'DESTROY APPROVAL',  $DESCRIPTION);

            DB::commit();
            return json_encode(array('success' => 'Destroy Approval action has been placed.', 'dr_id' => $APPROVAL_DETAILS->dra_dr_id));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function update_destroy_items(Request $request)
    {
        $StockModel = new StockModel();
        $CommonModel = new CommonModel();

        $REMOVED_IDS = json_decode($request->input('REMOVED_IDS'), true);
        if (empty($REMOVED_IDS)) {
            $REMOVED_IDS = array();
            return json_encode(array('error' => 'No any change found!'));
        }
        $DR_ID = trim($request->input('DR_ID'));

        $DESTROY_DETAILS = $StockModel->get_destroy_data_by_id($DR_ID);
        $DESTROY_ITEMS = $StockModel->get_destroy_items_by_id($DR_ID, 0);

        DB::beginTransaction();
        try {
            if (count($REMOVED_IDS) > 0) {
                $data1 = array(
                    'sil_status' => 1,
                    'sil_inserted_date' => date('Y-m-d H:i:s'),
                    'sil_inserted_by' => session('USER_ID'),
                    'sil_remark' => '',
                    'sil_total_items' => count($REMOVED_IDS),
                    'sil_mw_id' => $DESTROY_DETAILS->dr_mw_id,
                    'sil_method' => 'DESTROY',
                );
                $STOCK_IN_ID = DB::table('stock_in_list')->insertGetId($data1);
            }

            $TOTAL_PURCHASE_AMOUNT = 0;
            $TOTAL_SELLING_AMOUNT = 0;
            $TOTAL_QTY = 0;
            $item_count = 0;
            $tot_value = 0;
            $qty_count = 0;

            foreach ($DESTROY_ITEMS as $ITEM) {
                if (in_array($ITEM->dri_id, $REMOVED_IDS)) {

                    $TOTAL_PURCHASE_AMOUNT =   $TOTAL_PURCHASE_AMOUNT + 0;
                    $TOTAL_SELLING_AMOUNT =   $TOTAL_SELLING_AMOUNT + $ITEM->dri_selling_amount;
                    $TOTAL_QTY =   $TOTAL_QTY + $ITEM->dri_qty;

                    $data2 = array(
                        'dri_is_removed' => 1,
                        'dri_removed_date' => date('Y-m-d H:i:s'),
                        'dri_removed_by' => session('USER_ID'),
                    );
                    DB::table('destroy_request_items')
                        ->where('dri_id', $ITEM->dri_id)
                        ->update($data2);

                    $data3 = array(
                        's_status' => 1,
                        's_inserted_date' => date('Y-m-d H:i:s'),
                        's_inserted_by' => session('USER_ID'),
                        's_p_id' => $ITEM->p_id,
                        's_purchase_amount' => 0,
                        's_selling_amount' => $ITEM->dri_selling_amount,
                        's_qty' => $ITEM->dri_qty,
                        's_sil_id' => $STOCK_IN_ID,
                        's_mw_id' => $DESTROY_DETAILS->dr_mw_id,
                    );
                    DB::table('stock')->insert($data3);

                    $STATUS = $StockModel->update_available_stock($ITEM->p_id, $ITEM->dri_selling_amount,  $ITEM->dri_qty, $DESTROY_DETAILS->dr_mw_id, 'IN');
                    if ($STATUS == false) {
                        return false;
                    }
                } else {
                    $item_count++;
                    $tot_value =  $tot_value + $ITEM->dri_selling_amount;
                    $qty_count =  $qty_count + $ITEM->dri_qty;
                }
            }

            $data5 = array(
                'dr_updated_date' => date('Y-m-d H:i:s'),
                'dr_updated_by' => session('USER_ID'),
                'dr_item_count' => $item_count,
                'dr_tot_amount' => $tot_value,
                'dr_tot_qty' => $qty_count,
            );
            DB::table('destroy_requests')
                ->where('dr_id', $DR_ID)
                ->update($data5);


            if (count($REMOVED_IDS) > 0) {
                $data4 = array(
                    'sil_purchase_amount' => $TOTAL_PURCHASE_AMOUNT,
                    'sil_selling_amount' => $TOTAL_SELLING_AMOUNT,
                    'sil_total_qty' => $TOTAL_QTY,
                );
                DB::table('stock_in_list')
                    ->where('sil_id', $STOCK_IN_ID)
                    ->update($data4);
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Destroy request has been updated. Request Id:' . $DR_ID);
            $StockModel->update_destroy_request_timeline($DR_ID, 'DESTROY REQUEST EDIT',  'Destroy request has been updated.');

            DB::commit();
            return json_encode(array('success' => 'Destroy request has been updated.', 'dr_id' => $DR_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Destroy_Pending_Approvals()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Destroy/Destroy_Pending_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function get_destroy_request_filter_result_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $DR_ID = trim($request->input('DR_ID'));

        if ($ValidationModel->is_invalid_data($DR_ID)) {
            $is_set_from_date = true;
            $is_set_to_date = true;

            if ($ValidationModel->is_invalid_data($FROM_DATE)) {
                $is_set_from_date = false;
                $error .= "- From Date cannot be empty<br>";
            }
            if ($ValidationModel->is_invalid_data($TO_DATE)) {
                $is_set_to_date = false;
                $error .= "- To Date cannot be empty<br>";
            }

            if ($is_set_from_date == true && $is_set_to_date == true) {
                if ($ValidationModel->is_invalid_date_range($FROM_DATE, $TO_DATE)) {
                    $error .= "- Invalid date range<br>";
                }
            }
        }

        return $error;
    }

    public function get_destroy_request_approval(Request $request)
    {
        $status = $this->get_destroy_request_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $DR_ID = trim($request->input('DR_ID'));
            $TYPE = trim($request->input('TYPE'));

            $view = (string)view('Destroy/Manage_Approval_Destroy_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'DR_ID' => $DR_ID,
                'TYPE' => $TYPE,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_approval_destroy_request_filter_result_table(Request $request)
    {
        $status = $this->get_destroy_request_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $DR_ID = trim($request->input('DR_ID'));
            $TYPE = trim($request->input('TYPE'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_destroy_request_approval($TYPE, 0, $FROM_DATE, $TO_DATE, $DR_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Order/Download/Manage_Order_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Products Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }

    public function get_destroy_request_approval_count(Request $request)
    {
        $StockModel = new StockModel();
        $TYPE = trim($request->input('TYPE'));
        $count = $StockModel->get_destroy_request_approval($TYPE, 1);

        return json_encode(array('result' => $count));
    }

    public  function Destroy_Returned_Approvals()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Destroy/Destroy_Returned_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public  function Destroy_Completed_Approvals()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();;

        return view('Destroy/Destroy_Completed_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function Destroy_Requests()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();;

        return view('Destroy/Destroy_Requests', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function get_destroy_request_filter_result(Request $request)
    {
        $status = $this->get_destroy_request_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $DR_ID = trim($request->input('DR_ID'));

            $view = (string)view('Destroy/Manage_Destroy_Requests_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'DR_ID' => $DR_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_destroy_request_filter_result_table(Request $request)
    {
        $status = $this->get_destroy_request_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $DR_ID = trim($request->input('DR_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $StockModel->get_filtered_destroy_request_details($FROM_DATE, $TO_DATE, $DR_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Order/Download/Manage_Order_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Products Download.xls");
                header("Expires: 0");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Cache-Control: private", false);
                echo $view;
            } else {
                if ($request->ajax()) {
                    return datatables()->of($result)->toJson();
                }
            }
        }
    }
}
