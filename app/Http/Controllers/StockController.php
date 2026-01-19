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

        $ITEMS = $request->input('items');
        $MW_ID = trim($request->input('MW_ID'));
        $REMARK = "";
        $DISCOUNT_PERCENTAGE = trim($request->input('DISCOUNT_PERCENTAGE'));
        $DISCOUNT_AMOUNT = trim($request->input('DISCOUNT_AMOUNT'));
        $RETURNED_INVOICE_AMOUNT = trim($request->input('RETURNED_INVOICE_AMOUNT'));
        $MPT_ID = trim($request->input('MPT_ID'));
        $CASH_PAID = trim($request->input('CASH_PAID'));
        $CARD_PAID = trim($request->input('CARD_PAID'));
        $TOTAL_PAID_AMOUNT = trim($request->input('TOTAL_PAID_AMOUNT'));
        $BALANCE_AMOUNT = trim($request->input('BALANCE_AMOUNT'));
        $CUS_ID = trim($request->input('CUS_ID'));
        $IS_CORPARATE = trim($request->input('IS_CORPARATE'));

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


        DB::beginTransaction();
        try {

            $data1 = array(
                'in_status' => 1,
                'in_inserted_date' => date('Y-m-d H:i:s'),
                'in_inserted_by' => session('USER_ID'),
                'in_updated_date' => date('Y-m-d H:i:s'),
                'in_updated_by' => session('USER_ID'),
                'in_invoice_no' => $StockModel->generateInvoiceNo(),
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

    public function load_invoice(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));
        $INVOICE_ID = $DATA->INVOICE_ID;

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

        $view = (string)view('Stock/Invoice_View', [
            'INVOICE_ID' => $INVOICE_ID,
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
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

        return $pdf->download('Invoice - ' . $INVOICE_DATA->in_invoice_no . '.pdf');
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

        return $pdf->download('VAT Invoice - ' . $INVOICE_DATA->in_invoice_no . '.pdf');
    }
}
