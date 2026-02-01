<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\OrdersModel;
use App\Models\UserModel;
use App\Models\CommonModel;
use App\Models\ValidationModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;
use Barryvdh\DomPDF\Facade\Pdf;

class OrdersController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Add_New_Order()
    {
        $UserModel = new UserModel();
        $OrdersModel = new OrdersModel();

        $CUSTOMERS = $UserModel->get_corporate_customer_list();
        $WAREHOUSES = $OrdersModel->get_order_warehouses();

        return view('Orders/Add_New_Order', [
            'CUSTOMERS' => $CUSTOMERS,
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }



    public function save_order(Request $request)
    {
        $CommonModel = new CommonModel();
        $OrdersModel = new OrdersModel();

        $data = json_decode($request->order_data, true);

        if (empty($data['mw_id'])) {
            return json_encode(array('error' => 'Warehouse not selected.'));
        }

        $customer_count = 0;
        $qty_count = 0;
        foreach ($data['customers'] as $arr_item) {
            $customer_count++;

            foreach ($arr_item['items'] as $item) {
                $qty_count = $qty_count + $item['qty'];
            }
        }

        DB::beginTransaction();
        try {
            $data1 = array(
                'or_inserted_date' => date('Y-m-d H:i:s'),
                'or_inserted_by' => session('USER_ID'),
                'or_updated_date' => date('Y-m-d H:i:s'),
                'or_updated_by' => session('USER_ID'),
                'or_status' => 1,
                'or_mw_id' =>  $data['mw_id'],
                'or_os_id' => 1,
                'or_customers_count' => $customer_count,
                'or_total_qty' => $qty_count,
                'or_remark' => $data['remark'],
                'or_is_active' => 1,
            );
            $ORDER_ID = DB::table('orders')->insertGetId($data1);

            foreach ($data['customers'] as $arr_item) {
                foreach ($arr_item['items'] as $item) {

                    $data2 = array(
                        'ori_inserted_date' => date('Y-m-d H:i:s'),
                        'ori_inserted_by' => session('USER_ID'),
                        'ori_updated_date' => date('Y-m-d H:i:s'),
                        'ori_updated_by' => session('USER_ID'),
                        'ori_status' => 1,
                        'ori_or_id' => $ORDER_ID,
                        'ori_o_id' => $arr_item['customer_id'],
                        'ori_p_id' => $item['product_id'],
                        'ori_qty' => $item['qty'],
                        'ori_in_qty' => $item['qty'],
                    );
                    DB::table('order_items')->insert($data2);
                }
            }

            $data3 = array(
                'ora_status' => 1,
                'ora_inserted_date' => date('Y-m-d H:i:s'),
                'ora_inserted_by' => session('USER_ID'),
                'ora_is_active' => 1,
                'ora_or_id' => $ORDER_ID,
                'ora_aa_id' => 1
            );
            DB::table('order_approvals')->insert($data3);

            $CommonModel->update_work_log(session('USER_ID'), 'Order has been created. Order Id:' . $ORDER_ID);

            $OrdersModel->update_order_timeline($ORDER_ID, 'ORDER CREATED', 'Order has been created.');

            DB::commit();
            return json_encode(array('success' => 'Order has been created.', 'order_id' => $ORDER_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }


    public function get_order_filter_result_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $OR_ID = trim($request->input('OR_ID'));

        if ($ValidationModel->is_invalid_data($OR_ID)) {
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


    public function Manage_Order()
    {
        return view('Orders/Manage_Orders');
    }

    public function get_order_filter_result(Request $request)
    {
        $status = $this->get_order_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $OR_ID = trim($request->input('OR_ID'));

            $view = (string)view('Orders/Manage_Orders_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'OR_ID' => $OR_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_order_filter_result_table(Request $request)
    {
        $status = $this->get_order_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $OrdersModel = new OrdersModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $OR_ID = trim($request->input('OR_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $OrdersModel->get_filtered_order_details($FROM_DATE, $TO_DATE, $OR_ID);

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

    public function Order_View($OR_ID)
    {
        $OrdersModel = new OrdersModel();

        $OR_ID = base64_decode($OR_ID);

        $ORDER = $OrdersModel->get_order_details($OR_ID);
        $ORDER_ITEM = $OrdersModel->get_order_item_details($OR_ID);
        $ORDER_ITEM_FOR_SUMMARY = $OrdersModel->get_order_item_details_for_summary($OR_ID);
        $APPROVALS = $OrdersModel->get_approvals($OR_ID);
        $APPROVALS_ACTION = $OrdersModel->get_approval_actions();
        $TIMELINE = $OrdersModel->get_timeline_by_order_id($OR_ID);

        return view('Orders/Order_View', [
            'ORDER' => $ORDER,
            'ORDER_ITEM' => $ORDER_ITEM,
            'ORDER_ITEM_FOR_SUMMARY' => $ORDER_ITEM_FOR_SUMMARY,
            'APPROVALS' => $APPROVALS,
            'APPROVALS_ACTION' => $APPROVALS_ACTION,
            'OR_ID' => $OR_ID,
            'TIMELINE' => $TIMELINE,
        ]);
    }

    public function order_approve_action(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $OrdersModel = new OrdersModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();

        $ORA_ID = trim($request->input('ORA_ID'));
        $AA_ID = trim($request->input('AA_ID'));
        $REMARK = trim($request->input('REMARK'));

        if ($ValidationModel->is_invalid_data($AA_ID)) {
            return json_encode(array('error' => 'Action not selected!'));
        } else if ($AA_ID == 3) {
            if ($ValidationModel->is_invalid_data($REMARK)) {
                return json_encode(array('error' => 'Remark not found!'));
            }
        }

        $APPROVAL_DETAILS = $OrdersModel->get_approval_details($ORA_ID);
        $ORDER_ITEM = $OrdersModel->get_order_item_details($APPROVAL_DETAILS->ora_or_id);

        $qty_count = 0;
        foreach ($ORDER_ITEM as $ITEM) {
            if ($ITEM->available_qty < $ITEM->ori_qty) {
                return json_encode(array('error' => 'Something went wrong!'));
            }
            $qty_count = $qty_count + $ITEM->ori_qty;
        }

        DB::beginTransaction();
        try {
            $data = array(
                'ora_action_date' => date('Y-m-d H:i:s'),
                'ora_action_by' => session('USER_ID'),
                'ora_aa_id' => $AA_ID,
                'ora_remark' => $REMARK,
            );
            DB::table('order_approvals')
                ->where('ora_id', $ORA_ID)
                ->update($data);

            $data = array(
                'or_updated_date' => date('Y-m-d H:i:s'),
                'or_updated_by' => session('USER_ID'),
                'or_os_id' => $AA_ID == 2 ? 2 : 3,
                'or_total_qty' => $qty_count,
            );
            DB::table('orders')
                ->where('or_id', $APPROVAL_DETAILS->ora_or_id)
                ->update($data);


            if ($AA_ID == 2) {
                $DESCRIPTION = "Order has been approved.";
                foreach ($ORDER_ITEM as $ITEM) {
                    $REQUESTED_QTY = $ITEM->ori_qty;
                    $AV_LIST = $StockModel->get_product_available_list($ITEM->ori_p_id);


                    foreach ($AV_LIST as $LIST) {
                        if ($LIST->as_available_qty > $REQUESTED_QTY) {
                            $StockModel->update_available_stock($ITEM->ori_p_id, $LIST->as_selling_price, $REQUESTED_QTY, $LIST->mw_id, 'OUT');

                            $data = array(
                                'os_status' => 1,
                                'os_inserted_date' =>  date('Y-m-d H:i:s'),
                                'os_inserted_by' => session('USER_ID'),
                                'os_p_id' => $ITEM->ori_p_id,
                                'os_selling_amount' => $LIST->as_selling_price,
                                'os_qty' => $REQUESTED_QTY,
                                'os_or_id' => $ORA_ID,
                                'os_mw_id' => $LIST->mw_id,
                            );
                            DB::table('order_stock')->insert($data);

                            break;
                        } else {
                            $StockModel->update_available_stock($ITEM->ori_p_id, $LIST->as_selling_price,  $LIST->as_available_qty, $LIST->mw_id, 'OUT');

                            $data = array(
                                'os_status' => 1,
                                'os_inserted_date' =>  date('Y-m-d H:i:s'),
                                'os_inserted_by' => session('USER_ID'),
                                'os_p_id' => $ITEM->ori_p_id,
                                'os_selling_amount' => $LIST->as_selling_price,
                                'os_qty' => $LIST->as_available_qty,
                                'os_or_id' => $ORA_ID,
                                'os_mw_id' => $LIST->mw_id,
                            );
                            DB::table('order_stock')->insert($data);

                            $REQUESTED_QTY = $REQUESTED_QTY - $LIST->as_available_qty;
                        }
                    }
                }
            } else {
                $DESCRIPTION = "Order has been rejected.";
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Approval action has been placed. Order Id:' . $APPROVAL_DETAILS->ora_or_id);

            $OrdersModel->update_order_timeline($APPROVAL_DETAILS->ora_or_id, 'ORDER APPROVAL',  $DESCRIPTION);

            DB::commit();
            return json_encode(array('success' => 'Approval action has been placed.', 'order_id' => $APPROVAL_DETAILS->ora_or_id));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function update_order_items(Request $request)
    {
        $OrdersModel = new OrdersModel();
        $CommonModel = new CommonModel();

        $OR_ID = trim($request->input('OR_ID'));

        $ORDER_ITEM = $OrdersModel->get_order_item_details($OR_ID);

        DB::beginTransaction();
        try {

            foreach ($ORDER_ITEM as $ITEM) {
                $ITEM_QTY = trim($request->input($ITEM->ori_id . '_ITEM_QTY'));

                if ($ITEM->available_qty < $ITEM_QTY) {
                    return json_encode(array('error' => 'Something went wrong!'));
                }

                $data = array(
                    'ori_updated_date' => date('Y-m-d H:i:s'),
                    'ori_updated_by' => session('USER_ID'),
                    'ori_qty' => $ITEM_QTY,
                );
                DB::table('order_items')
                    ->where('ori_id', $ITEM->ori_id)
                    ->update($data);
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Order has been updated. Order Id:' . $OR_ID);

            $OrdersModel->update_order_timeline($OR_ID, 'ORDER EDIT', 'Order details have been updated.');

            DB::commit();
            return json_encode(array('success' => 'Order has been updated.', 'order_id' => $OR_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function order_collect_action(Request $request)
    {
        $OrdersModel = new OrdersModel();
        $CommonModel = new CommonModel();
        $StockModel = new StockModel();

        $OR_ID = trim($request->input('OR_ID'));

        DB::beginTransaction();
        try {
            $ORDER = $OrdersModel->get_order_details($OR_ID);
            $ORDER_STOCK = $OrdersModel->get_order_stock($OR_ID);

            $TOTAL_SELLING_AMOUNT = 0;
            $TOTAL_QTY = 0;
            foreach ($ORDER_STOCK as $STOCK) {
                $TOTAL_SELLING_AMOUNT = $TOTAL_SELLING_AMOUNT + $STOCK->os_selling_amount;
                $TOTAL_QTY = $TOTAL_QTY + $STOCK->os_qty;
            }

            $data2 = array(
                'sil_status' => 1,
                'sil_inserted_date' => date('Y-m-d H:i:s'),
                'sil_inserted_by' => session('USER_ID'),
                'sil_remark' => null,
                'sil_total_items' => count($ORDER_STOCK),
                'sil_purchase_amount' => 0,
                'sil_selling_amount' => $TOTAL_SELLING_AMOUNT,
                'sil_total_qty' => $TOTAL_QTY,
                'sil_mw_id' => $ORDER->or_mw_id,
                'sil_method' => 'COLLECT',
            );
            $STOCK_IN_ID = DB::table('stock_in_list')->insertGetId($data2);

            $or_mw_id = 0;
            foreach ($ORDER_STOCK as $item) {
                if ($or_mw_id != $item->os_mw_id) {
                    $or_mw_id = $item->os_mw_id;
                    $data1 = array(
                        'sol_status' => 1,
                        'sol_inserted_date' => date('Y-m-d H:i:s'),
                        'sol_inserted_by' => session('USER_ID'),
                        'sol_remark' => null,
                        'sol_total_items' => count($ORDER_STOCK),
                        'sol_selling_amount' => $TOTAL_SELLING_AMOUNT,
                        'sol_total_qty' => $TOTAL_QTY,
                        'sol_total_discount' => 0,
                        'sol_mw_id' => $item->os_mw_id,
                    );
                    $STOCK_OUT_ID = DB::table('stock_out_list')->insertGetId($data1);
                }
                $data3 = array(
                    's_status' => 1,
                    's_inserted_date' => date('Y-m-d H:i:s'),
                    's_inserted_by' => session('USER_ID'),
                    's_p_id' => $item->os_p_id,
                    's_selling_amount' => $item->os_selling_amount,
                    's_qty' =>  $item->os_qty,
                    's_discount_percentage' =>  0,
                    's_discounted_price' =>  $item->os_selling_amount,
                    's_sol_id' => $STOCK_OUT_ID,
                    's_mw_id' => 1,
                );
                DB::table('stock')->insert($data3);

                $data4 = array(
                    's_status' => 1,
                    's_inserted_date' => date('Y-m-d H:i:s'),
                    's_inserted_by' => session('USER_ID'),
                    's_p_id' => $item->os_p_id,
                    's_purchase_amount' => 0,
                    's_selling_amount' => $item->os_selling_amount,
                    's_qty' =>  $item->os_qty,
                    's_sil_id' => $STOCK_IN_ID,
                    's_mw_id' => $ORDER->or_mw_id,
                );
                DB::table('stock')->insert($data4);

                $STATUS = $StockModel->update_available_stock($item->os_p_id, $item->os_selling_amount, $item->os_qty, $ORDER->or_mw_id, 'IN');
            }

            $data5 = array(
                'or_updated_date' => date('Y-m-d H:i:s'),
                'or_updated_by' => session('USER_ID'),
                'or_os_id' => 4
            );
            DB::table('orders')
                ->where('or_id', $OR_ID)
                ->update($data5);

            $CommonModel->update_work_log(session('USER_ID'), 'Order has been collected. Order Id:' . $OR_ID);
            $OrdersModel->update_order_timeline($OR_ID, 'COLLECT ACTION', 'Order has been collected.');

            DB::commit();
            return json_encode(array('success' => 'Order has been collected.', 'order_id' => $OR_ID));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Pending_Order_Approvals()
    {
        $UserModel = new UserModel();
        $OrdersModel = new OrdersModel();

        $WAREHOUSES = $OrdersModel->get_order_warehouses();

        return view('Orders/Pending_Order_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public  function Retruned_Order_Approvals()
    {
        $UserModel = new UserModel();
        $OrdersModel = new OrdersModel();

        $WAREHOUSES = $OrdersModel->get_order_warehouses();

        return view('Orders/Retruned_Order_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public  function Completed_Order_Approvals()
    {
        $UserModel = new UserModel();
        $OrdersModel = new OrdersModel();

        $WAREHOUSES = $OrdersModel->get_order_warehouses();

        return view('Orders/Completed_Order_Approvals', [
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function get_order_approval_count(Request $request)
    {
        $OrdersModel = new OrdersModel();
        $TYPE = trim($request->input('TYPE'));
        $count = $OrdersModel->get_order_approval($TYPE, 1);

        return json_encode(array('result' => $count));
    }

    public function get_order_approval(Request $request)
    {
        $status = $this->get_order_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $OR_ID = trim($request->input('OR_ID'));
            $TYPE = trim($request->input('TYPE'));

            $view = (string)view('Orders/Manage_Approval_Orders_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'OR_ID' => $OR_ID,
                'TYPE' => $TYPE,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_approval_order_filter_result_table(Request $request)
    {
        $status = $this->get_order_filter_result_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $OrdersModel = new OrdersModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $OR_ID = trim($request->input('OR_ID'));
            $TYPE = trim($request->input('TYPE'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $OrdersModel->get_order_approval($TYPE, 0, $FROM_DATE, $TO_DATE, $OR_ID);

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

    public function Download_Order_Form($OR_ID)
    {
        $OrdersModel = new OrdersModel();
        $StockModel = new StockModel();

        $OR_ID = urldecode(base64_decode($OR_ID));

        $WAREHOUSES = $StockModel->get_active_stock_locations_by_type(1);
        $ORDER = $OrdersModel->get_order_details($OR_ID);

        $FULL_ARR = array();
        foreach ($WAREHOUSES as $WAREHOUSES) {
            $ORDER_ITEM = $OrdersModel->get_order_form_data($OR_ID, $WAREHOUSES->mw_id);
            if (count($ORDER_ITEM) > 0) {
                array_push($FULL_ARR, array(
                    'WAREHOUSE' => $WAREHOUSES,
                    'ORDER_ITEM' => $ORDER_ITEM
                ));
            }
        }

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // Can contain multiple IPs – first one is the real client
            $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        $IP = trim($ip);

        // return view('Orders.Order_Form', [
        //     'ORDER' => $ORDER,
        //     'FULL_ARR' => $FULL_ARR,
        //     'IP' => $IP,
        // ]);

        $pdf = Pdf::loadView('Orders.Order_Form', [
            'ORDER' => $ORDER,
            'FULL_ARR' => $FULL_ARR,
            'IP' => $IP,
        ])->setPaper('a4', 'portrait');
        return $pdf->download('Order_Form_' . $ORDER->or_id . '.pdf');
    }
}
