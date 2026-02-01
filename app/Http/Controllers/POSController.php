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

class POSController extends Controller
{

    public function pos()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();
        $PAYMENT_TYPES = $StockModel->get_payment_types();

        return view('POS/POS', [
            'WAREHOUSES' => $WAREHOUSES,
            'PAYMENT_TYPES' => $PAYMENT_TYPES,
        ]);
    }

    public function product_search_in_pos(Request $request)
    {
        $StockModel = new StockModel();

        $q = $request->get('q');
        $MW_ID = $request->get('mw_id');

        $PRODUCTS = $StockModel->search_available_products($q, $MW_ID);

        return response()->json($PRODUCTS);
    }

    public function load_diffrent_price_view(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));
        $P_ID = $DATA->P_ID;
        $MW_ID = $DATA->MW_ID;

        $PRODUCTS =  $StockModel->get_diffrent_price_list($P_ID, $MW_ID);

        $view = (string)view('POS/Diffrent_Price_View', [
            'P_ID' => $P_ID,
            'MW_ID' => $MW_ID,
            'PRODUCTS' => $PRODUCTS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function get_individual_customer(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $StockModel = new StockModel();

        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
        if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
            return json_encode(array('error' => 'Contact Number not found!'));
        }

        $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_contact($CONTACT_NUMBER);
        if ($CUSTOMER_DETAILS == true) {
            return json_encode(array('have_customer' => true, 'customer_id' => $CUSTOMER_DETAILS->c_id, 'customer_name' => $CUSTOMER_DETAILS->c_name, 'customer_title' => $CUSTOMER_DETAILS->c_contact));
        } else {
            $view = (string)view('POS/Quick_Customer_Register', [
                'CONTACT_NUMBER' => $CONTACT_NUMBER,
            ]);
            return json_encode(array('havent_customer' => true, 'view' => $view));
        }
    }

    public function load_customer_view(Request $request)
    {
        $view = (string)view('POS/Customer_Selection_View');
        return json_encode(array('result' => $view));
    }

    public function save_quich_customer_form(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();

        $NAME = trim($request->input('NAME'));
        $CONTACT = trim($request->input('CONTACT'));
        $DOB = trim($request->input('DOB'));
        $NIC = trim($request->input('NIC'));
        $ADDRESS = trim($request->input('ADDRESS'));
        $EMAIL = trim($request->input('EMAIL'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            return json_encode(array('error' => 'Name not found!'));
        }
        if ($ValidationModel->is_invalid_data($CONTACT)) {
            return json_encode(array('error' => 'Contact Number not found!'));
        }

        DB::beginTransaction();
        try {
            $data = array(
                'c_status' => 1,
                'c_inserted_date' => date('Y-m-d H:i:s'),
                'c_inserted_by' => session('USER_ID'),
                'c_updated_date' => date('Y-m-d H:i:s'),
                'c_updated_by' => session('USER_ID'),
                'c_is_suspend' => 0,
                'c_contact' => $CONTACT,
                'c_title' => null,
                'c_name' => $NAME,
                'c_dob' => $DOB,
                'c_nic' => $NIC,
                'c_email' => $EMAIL,
                'c_address' => $ADDRESS,
            );
            $CUS_ID = DB::table('customers')->insertGetId($data);

            $CommonModel->update_work_log(session('USER_ID'), 'New Member has been registered. Customer ID:' . $CUS_ID);

            $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_contact($CONTACT);

            DB::commit();
            return json_encode(array('success' => 'New Member has been registered.', 'customer_id' => $CUSTOMER_DETAILS->c_id, 'customer_name' => $CUSTOMER_DETAILS->c_name, 'customer_title' => $CUSTOMER_DETAILS->c_contact));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function get_corporate_customer_list(Request $request)
    {
        $UserModel = new UserModel();
        $search = $_GET['q'];
        $content = array();

        $customers = $UserModel->get_corporate_customer($search);

        foreach ($customers as $customer) {
            $data = array(
                "cus_id" => $customer->o_id,
                "name" => strtoupper($customer->o_business_name) . "  (" . $customer->o_contact . ") | Customer ID : " . $customer->o_id
            );
            array_push($content, $data);
        }

        header('Content-Type: application/json');
        echo json_encode(
            $content
        );
    }

    public function get_corporate_customer(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $UserModel = new UserModel();
        $OrdersModel = new OrdersModel();

        $CUS_ID = trim($request->input('CUST_ID'));
        if ($ValidationModel->is_invalid_data($CUS_ID)) {
            return json_encode(array('error' => 'Customer not selected!'));
        }

        $CUSTOMER_DETAILS = $UserModel->get_organization_data($CUS_ID);;
        $MW_ID = session('POS_WAREHOUSE');
        $ORDERS = $OrdersModel->get_collected_orders_by_customer_id($CUS_ID, $MW_ID);
        $view = null;
        if (count($ORDERS) > 0) {
            $view = (string)view('POS/Order_View', ['ORDERS' => $ORDERS, 'customer_id' => $CUSTOMER_DETAILS->o_id]);
        }

        return json_encode(array('have_customer' => true, 'customer_id' => $CUSTOMER_DETAILS->o_id, 'customer_name' => $CUSTOMER_DETAILS->o_business_name, 'customer_title' => $CUSTOMER_DETAILS->o_br_number, 'order_view' => $view));
    }

    public function PrintInvoice($INVOICE_ID)
    {
        $INVOICE_ID = urldecode(base64_decode($INVOICE_ID));

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


        $pdf = Pdf::loadView('POS.Invoice.Thermal_Print_View', [
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
        ])
            ->setPaper([0, 0, 226.77, 1000], 'portrait') // 80mm width
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->stream('Invoice.pdf'); // or ->download('invoice.pdf')
    }

    public function set_warehouse_session(Request $request)
    {
        $SettingsModel = new SettingsModel();

        $mw_id = trim($request->input('mw_id'));


        $WAREHOUSE = $SettingsModel->get_stock_location_details_by_id($mw_id);

        if ($WAREHOUSE->mwt_id == 2 || $WAREHOUSE->mwt_id == 4) {

            $show_view = 0;
            $LAST_PUNCH = $SettingsModel->get_last_punch_detail(session('USER_ID'));
            if ($LAST_PUNCH == false) {
                $show_view = 1;
            } else if ($LAST_PUNCH->pu_pus_id == 2) {
                $show_view = 1;
            } else if ($LAST_PUNCH->pu_pus_id == 1 && $LAST_PUNCH->pu_mw_id == $mw_id) {
                $request->session()->put('POS_WAREHOUSE', $mw_id);
                return response()->json(['success' => 'success']);
            } else if ($LAST_PUNCH->pu_pus_id == 1) {
                return response()->json(['error' => 'You already have an active punch. Please end the current punch before starting a new one.']);
            }

            if ($show_view == 1) {
                $view = (string)view('POS/Set_Warehouse_View', [
                    'WAREHOUSE' => $WAREHOUSE,
                    'MW_ID' => $mw_id,
                ]);
                return response()->json(['result' => $view]);
            }
        } else {
            $request->session()->put('POS_WAREHOUSE', $mw_id);
            return response()->json(['success' => 'success']);
        }
    }

    public function save_new_punch(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $SettingsModel = new SettingsModel();

        $MW_ID = trim($request->input('MW_ID'));
        $CASH_ON_HAND = trim($request->input('CASH_ON_HAND'));

        if ($ValidationModel->is_invalid_data($CASH_ON_HAND)) {
            return json_encode(array('error' => 'Cash on hand not found!'));
        }
        if ($ValidationModel->is_invalid_data($MW_ID)) {
            return json_encode(array('error' => 'Warehouse not found!'));
        }

        DB::beginTransaction();
        try {
            $request->session()->put('POS_WAREHOUSE', $MW_ID);

            $data = array(
                'pu_status' => 1,
                'pu_inserted_date' => date('Y-m-d H:i:s'),
                'pu_inserted_by' => session('USER_ID'),
                'pu_pus_id' => 1,
                'pu_mw_id' => $MW_ID,
                'pu_su_id' => session('USER_ID'),
                'pu_cash_on_hand' => $CASH_ON_HAND,
                'pu_amount' => $CASH_ON_HAND,
            );
            DB::table('punches')->insert($data);

            $CommonModel->update_work_log(session('USER_ID'), 'Punching Session Initiated');

            $WAREHOUSE = $SettingsModel->get_stock_location_details_by_id($MW_ID);

            DB::commit();
            return json_encode(array('success' => 'Punching Session Initiated', 'mw_id' => $WAREHOUSE->mw_id, 'mw_name' => $WAREHOUSE->mw_name));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }


    public function ALL_Punch_List()
    {
        $SettingsModel = new SettingsModel();

        $WAREHOUSES = $SettingsModel->get_all_locations();

        return view('POS/ALL_Punch_List', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function My_Punch_List()
    {
        $StockModel = new StockModel();

        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('POS/My_Punch_List', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function get_punch_filterd_table_validation($request)
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


        return $error;
    }

    public function get_punch_filterd_table(Request $request)
    {
        $status = $this->get_punch_filterd_table_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('POS/ALL_Punch_List_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_my_punch_filterd_table(Request $request)
    {
        $status = $this->get_punch_filterd_table_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));

            $view = (string)view('POS/My_Punch_List_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_punch_filterd_table_data(Request $request)
    {
        $status = $this->get_punch_filterd_table_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $SettingsModel = new SettingsModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $SettingsModel->get_punch_details($FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Products/Download/Manage_Products_Table_Download', [
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

    public function get_my_punch_filterd_table_data(Request $request)
    {
        $status = $this->get_punch_filterd_table_validation($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $SettingsModel = new SettingsModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $SettingsModel->get_punch_details($FROM_DATE, $TO_DATE, $MW_ID, 1);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('Products/Download/Manage_Products_Table_Download', [
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

    public function load_end_punch_view(Request $request)
    {
        $SettingsModel = new SettingsModel();

        $DATA = json_decode(trim($request->input('DATA')));
        $PU_ID = $DATA->PU_ID;

        $PUNCH_DETAILS = $SettingsModel->get_punch_details_by_id($PU_ID);
        $USERS = $SettingsModel->get_system_users();

        $view = (string)view('POS/Punch_End_Action_View', [
            'PU_ID' => $PU_ID,
            'PUNCH_DETAILS' => $PUNCH_DETAILS,
            'USERS' => $USERS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function punch_end_action(Request $request)
    {
        $SettingsModel = new SettingsModel();
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $PU_ID = json_decode(trim($request->input('PU_ID')));
        $LIST = $request->input('group-a');

        $PUNCH_DETAILS = $SettingsModel->get_punch_details_by_id($PU_ID);

        if ($PUNCH_DETAILS == false) {
            return json_encode(array('error' => 'Something went wrong!'));
        } else if ($PUNCH_DETAILS->pus_id == 2) {
            return json_encode(array('error' => 'This is already ended!'));
        } else if (empty($LIST)) {
            return json_encode(array('error' => 'Data not found!'));
        }

        $TOTAL = 0;
        foreach ($LIST as $item) {
            if ($ValidationModel->is_invalid_data($item['TYPE'])) {
                return json_encode(array('error' => 'Type not selected!'));
            } else if (!isset($item['TYPE_AMOUNT']) || $ValidationModel->is_invalid_data($item['TYPE_AMOUNT'])) {
                return json_encode(array('error' => 'Amount not Found!'));
            } else if ($item['TYPE'] == 'TRANSFER') {
                if (!isset($item['TRANSFER_TO']) || $ValidationModel->is_invalid_data($item['TRANSFER_TO'])) {
                    return json_encode(array('error' => 'Transfer to not selected!'));
                }
            }

            $TOTAL = $TOTAL  + $item['TYPE_AMOUNT'];
        }

        if ($TOTAL != $PUNCH_DETAILS->pu_amount) {
            return json_encode(array('error' => 'Entered amount does not match the full amount!'));
        }

        DB::beginTransaction();
        try {

            foreach ($LIST as $item) {
                $data1 = array(
                    'pued_status' => 1,
                    'pued_inserted_date' => date('Y-m-d H:i:s'),
                    'pued_inserted_by' => session('USER_ID'),
                    'pued_pu_id' => session('USER_ID'),
                    'pued_type' => $item['TYPE'],
                    'pued_amount' => $item['TYPE_AMOUNT'],
                    'pued_transfer_su_id' => isset($item['TRANSFER_TO']) ? $item['TRANSFER_TO'] : null,
                    'pued_transfer_is_accept' => 0,
                );
                DB::table('punch_end_details')->insert($data1);
            }

            $data2 = array(
                'pu_pus_id' => 2,
                'pu_end_date' => date('Y-m-d H:i:s'),
                'pu_end_by' => session('USER_ID'),
            );
            DB::table('punches')
                ->where('pu_id', $PU_ID)
                ->update($data2);

            $request->session()->forget('POS_WAREHOUSE');

            $CommonModel->update_work_log(session('USER_ID'), 'Punching Session has been ended');

            DB::commit();
            return json_encode(array('success' => 'Punching Session has been ended'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function load_return_view(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));
        $MW_ID = $DATA->MW_ID;

        if (empty($MW_ID)) {
            $view = ' <div class="alert alert-danger" role="alert">
                                               WAREHOUSE NOT SELECTED
                                            </div>';
            return json_encode(array('result' => $view));
        } else {
            $view = (string)view('POS/Return_View', [
                'MW_ID' => $MW_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_return_invoice_form(Request $request)
    {
        $INCOICE_NUMBER = trim($request->input('INCOICE_NUMBER'));
        $MW_ID = trim($request->input('MW_ID'));

        $StockModel = new StockModel();
        $UserModel = new UserModel();
        $ValidationModel = new ValidationModel();

        if ($ValidationModel->is_invalid_data($INCOICE_NUMBER)) {
            return json_encode(array('error' => 'Invoice Number not found!'));
        }

        $INVOICE_DATA = $StockModel->get_invoice_data_by_invoice_number($INCOICE_NUMBER);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($INVOICE_DATA->in_id);

        if ($INVOICE_DATA->in_is_corparate == 1) {
            $CUSTOMER_DETAILS = $UserModel->get_organization_data($INVOICE_DATA->in_customer_id);
        } else if (!empty($INVOICE_DATA->in_customer_id)) {
            $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($INVOICE_DATA->in_customer_id);
        } else {
            $CUSTOMER_DETAILS = [];
        }

        $view = (string)view('POS/Return_Form_View', [
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
            'MW_ID' => $MW_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function return_invoice_action(Request $request)
    {
        $StockModel = new StockModel();
        $CommonModel = new CommonModel();
        $SettingsModel = new SettingsModel();

        $INVOICE_ID = trim($request->input('INVOICE_ID'));
        $REMARK = trim($request->input('REMARK'));
        $MW_ID = trim($request->input('MW_ID'));
        $INVOICE_AMOUNT = trim($request->input('INVOICE_AMOUNT'));
        $RETURNED_ITEMS_AMOUNT = trim($request->input('RETURNED_ITEMS_AMOUNT'));
        $RETURNED_ITEMS = explode(",", trim($request->input('RETURNED_ITEMS')));
        $TYPE = trim($request->input('TYPE'));

        if ($RETURNED_ITEMS_AMOUNT <= 0) {
            return json_encode(array('error' => "Returned items not selected."));
        }

        if (empty($TYPE)) {
            return json_encode(array('error' => "Retrun type not selected."));
        }

        $INVOICE_DATA = $StockModel->get_invoice_data_by_id($INVOICE_ID);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($INVOICE_ID);

        DB::beginTransaction();
        try {

            if ($INVOICE_AMOUNT == $RETURNED_ITEMS_AMOUNT) {
                $full_return = 1;
                $partial_return = 0;
                $data1 = array(
                    'in_is_returned' => 1,
                    'in_returned_date' => date('Y-m-d H:i:s'),
                    'in_returned_by' => session('USER_ID'),
                    'in_returned_mw_id' => $MW_ID,
                    'in_returned_remark' => $REMARK,
                    'in_returned_type' => $TYPE,
                );
            } else {
                $full_return = 0;
                $partial_return = 1;
                $data1 = array(
                    'in_is_partial_returned' => 1,
                    'in_returned_date' => date('Y-m-d H:i:s'),
                    'in_returned_by' => session('USER_ID'),
                    'in_returned_mw_id' => $MW_ID,
                    'in_returned_remark' => $REMARK,
                    'in_returned_type' => $TYPE,
                );
            }
            DB::table('invoices')
                ->where('in_id', $INVOICE_ID)
                ->update($data1);



            $RETURNED_ITEMS_ARRAY = [];
            $RETURNED_ITEMS_QTY_ARRAY = [];
            foreach ($RETURNED_ITEMS as $item) {
                [$iniId, $qty] = explode(':', $item);

                $RETURNED_ITEMS_ARRAY[] = $iniId;
                $RETURNED_ITEMS_QTY_ARRAY[$iniId] = $qty;
            }

            $ITEMS = array();
            $TOTAL_PURCHASE_AMOUNT = 0;
            $TOTAL_SELLING_AMOUNT = 0;
            $TOTAL_QTY = 0;
            $TOTAL_PU_AMT = 0;
            foreach ($INVOICE_ITEMS_DATA as $data) {

                if (in_array($data->ini_id, $RETURNED_ITEMS_ARRAY)) {
                    $data2 = array(
                        'ini_is_returned' => 1,
                        'ini_updated_date' => date('Y-m-d H:i:s'),
                        'ini_updated_by' => session('USER_ID'),
                        'ini_returned_qty' => $RETURNED_ITEMS_QTY_ARRAY[$data->ini_id],
                    );
                    DB::table('invoice_items')
                        ->where('ini_id', $data->ini_id)
                        ->update($data2);

                    $ITEMS[] = array(
                        'id' => $data->ini_p_id,
                        'purchase' => 0,
                        'selling' => $data->ini_selling_price,
                        'qty' => $RETURNED_ITEMS_QTY_ARRAY[$data->ini_id],
                    );
                    $TOTAL_SELLING_AMOUNT = $TOTAL_SELLING_AMOUNT + $data->ini_selling_price;
                    $TOTAL_QTY = $TOTAL_QTY + $RETURNED_ITEMS_QTY_ARRAY[$data->ini_id];
                    $TOTAL_PU_AMT = $TOTAL_PU_AMT + ($data->ini_final_price * $RETURNED_ITEMS_QTY_ARRAY[$data->ini_id]);
                }
            }

            $RI_ID = 0;
            $PRINT_RETURN_INVOICE = false;
            if ($TYPE == 'RETURN_MONEY') {
                $WAREHOUSE_DATA = $SettingsModel->get_stock_location_details_by_id($MW_ID);
                if (!in_array($WAREHOUSE_DATA->mwt_id, [1, 3])) {
                    $PUNCH = $SettingsModel->get_active_punch($WAREHOUSE_DATA->mw_id);
                    if ($PUNCH == false) {
                        return json_encode(array('error' => "You havent active punch."));
                    } else {
                        $data = array(
                            'pu_amount' => $PUNCH->pu_amount + $TOTAL_PU_AMT
                        );
                        DB::table('punches')
                            ->where('pu_id', $PUNCH->pu_id)
                            ->update($data);
                    }
                }
            } else if ($TYPE == 'RETURN_INVOICE') {
                $PRINT_RETURN_INVOICE = true;

                $data3 = array(
                    'ri_status' => 1,
                    'ri_inserted_date' => date('Y-m-d H:i:s'),
                    'ri_inserted_by' => session('USER_ID'),
                    'ri_in_id' => $INVOICE_ID,
                    'ri_amount' => $TOTAL_PU_AMT,
                    'ri_is_returned' => $full_return,
                    'ri_is_partial_returned' => $partial_return,
                    'ri_mw_id' => $MW_ID,
                );
                $RI_ID = DB::table('returned_invoices')->insertGetId($data3);

                foreach ($INVOICE_ITEMS_DATA as $data) {
                    if (in_array($data->ini_id, $RETURNED_ITEMS_ARRAY)) {
                        $data4 = array(
                            'rii_status' => 1,
                            'rii_inserted_date' => date('Y-m-d H:i:s'),
                            'rii_inserted_by' => session('USER_ID'),
                            'rii_ri_id' => $RI_ID,
                            'rii_p_id' => $data->ini_p_id,
                            'rii_selling_amount' => $data->ini_selling_price,
                            'rii_qty' => $RETURNED_ITEMS_QTY_ARRAY[$data->ini_id],
                        );
                        DB::table('returned_invoice_items')->insert($data4);
                    }
                }
            }


            $STOCK_IN_ID = $StockModel->stock_in($REMARK, $ITEMS, $TOTAL_PURCHASE_AMOUNT, $TOTAL_SELLING_AMOUNT, $TOTAL_QTY, $MW_ID, 'RETURNED');

            if ($STOCK_IN_ID == false) {
                return json_encode(array('error' => "Invalid Stock amount."));
            }

            $CommonModel->update_work_log(session('USER_ID'), 'An invoice has been returned. Invoice: ' . $INVOICE_DATA->in_invoice_no);
            DB::commit();
            return json_encode(array('in_id' => $INVOICE_DATA->in_id, 'success' => 'An invoice has been returned.', 'print_invoice' => $PRINT_RETURN_INVOICE, 'ri_id' => $RI_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function load_order_to_pos(Request $request)
    {
        $OrdersModel = new OrdersModel();
        $StockModel = new StockModel();

        $OR_ID = trim($request->input('or_id'));
        $C_ID = trim($request->input('customer_id'));

        $ORDER = $OrdersModel->get_order_details($OR_ID);
        $ORDER_ITEMS = $OrdersModel->get_order_items($OR_ID, $C_ID);

        $final_array = array();
        foreach ($ORDER_ITEMS as $item) {
            $av_products = $StockModel->get_av_stocks($item->p_id, $ORDER->mw_id);
            $req_qty = $item->ori_qty;
            foreach ($av_products as $av_product) {
                if ($req_qty <= $av_product->as_available_qty) {
                    array_push($final_array, [
                        'id' => $av_product->as_id,
                        'as_selling_price' => $av_product->as_selling_price,
                        'as_available_qty' => $item->ori_qty,
                        'name' => $av_product->p_name,
                        'discount' => 0,
                    ]);
                    break;
                } else {
                    array_push($final_array, [
                        'id' => $av_product->as_id,
                        'as_selling_price' => $av_product->as_selling_price,
                        'as_available_qty' => $av_product->as_available_qty,
                        'name' => $av_product->p_name,
                        'discount' => 0,
                    ]);
                    $req_qty = $req_qty - $av_product->as_available_qty;
                }
            }
        }

        return json_encode(array('success' => "Stock has been loaded", 'ORDER_ITEMS' => $final_array, 'ORDER' => $ORDER));
    }

    public function PrintReturnInvoice($RI_ID)
    {
        $RI_ID = urldecode(base64_decode($RI_ID));

        $StockModel = new StockModel();

        $RETURNED_INVOICE_DATA = $StockModel->get_returned_invoice_data_by_id($RI_ID);
        $RETURNED_INVOICE_ITEMS_DATA = $StockModel->get_returned_invoice_items_by_id($RI_ID);

        $INVOICE_DATA = $StockModel->get_invoice_data_by_id($RETURNED_INVOICE_DATA->ri_in_id);
        $INVOICE_ITEMS_DATA = $StockModel->get_invoice_items_by_id($RETURNED_INVOICE_DATA->ri_in_id);


        $pdf = Pdf::loadView('POS.Invoice.Returned_Thermal_Print_View', [
            'RETURNED_INVOICE_DATA' => $RETURNED_INVOICE_DATA,
            'RETURNED_INVOICE_ITEMS_DATA' => $RETURNED_INVOICE_ITEMS_DATA,
            'INVOICE_DATA' => $INVOICE_DATA,
            'INVOICE_ITEMS_DATA' => $INVOICE_ITEMS_DATA,
        ])
            ->setPaper([0, 0, 226.77, 1000], 'portrait') // 80mm width
            ->setOption('margin-top', 0)
            ->setOption('margin-right', 0)
            ->setOption('margin-bottom', 0)
            ->setOption('margin-left', 0);

        return $pdf->stream('Invoice.pdf'); // or ->download('invoice.pdf')
    }
}
