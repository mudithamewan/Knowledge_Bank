<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\OrdersController;


// Authentication Controller
Route::get('/test', [AuthenticationController::class, 'test'])->name('/test');
Route::get('/', [AuthenticationController::class, 'login'])->name('/login');
Route::post('/authentication', [AuthenticationController::class, 'authentication'])->name('/authentication');
Route::get('/logout', [AuthenticationController::class, 'logout'])->name('/logout');

// Dashboard Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Dashboard', [DashboardController::class, 'index'])->name('/Dashboard')->middleware('checkAccess:1');
});

// User Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Create_New_User', [UserController::class, 'Create_New_User'])->name('/Create_New_User')->middleware('checkAccess:13');
    Route::get('/Manage_Users', [UserController::class, 'Manage_Users'])->name('/Manage_Users')->middleware('checkAccess:2');
    Route::post('/save_user', [UserController::class, 'save_user'])->name('/save_user')->middleware('checkAccess:13');
    Route::post('/get_user_filter_result', [UserController::class, 'get_user_filter_result'])->name('/get_user_filter_result')->middleware('checkAccess:2');
    Route::post('/get_user_filter_result_table', [UserController::class, 'get_user_filter_result_table'])->name('/get_user_filter_result_table')->middleware('checkAccess:2');
    Route::get('/User_Profile/{USER_ID}', [UserController::class, 'User_Profile'])->name('/User_Profile')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_edit_profile_view', [UserController::class, 'load_edit_profile_view'])->name('/load_edit_profile_view')->middleware('checkAccess:3');
    Route::post('/update_user', [UserController::class, 'update_user'])->name('/update_user')->middleware('checkAccess:3');
    Route::post('/load_change_password_view', [UserController::class, 'load_change_password_view'])->name('/load_change_password_view')->middleware('checkAccess:4');
    Route::post('/chnage_user_password', [UserController::class, 'chnage_user_password'])->name('/chnage_user_password')->middleware('checkAccess:4');
    Route::get('/Create_New_Organization', [UserController::class, 'Create_New_Organization'])->name('/Create_New_Organization')->middleware('checkAccess:10');
    Route::post('/save_organization', [UserController::class, 'save_organization'])->name('/save_organization')->middleware('checkAccess:10');
    Route::get('/Organization_Profile/{ORG_ID}', [UserController::class, 'Organization_Profile'])->name('/Organization_Profile')->middleware('checkAccess:10||11||12');
    Route::post('/load_edit_org_profile_view', [UserController::class, 'load_edit_org_profile_view'])->name('/load_edit_org_profile_view')->middleware('checkAccess:11');
    Route::post('/update_organization', [UserController::class, 'update_organization'])->name('/update_organization')->middleware('checkAccess:11');
    Route::post('/remove_org_doc', [UserController::class, 'remove_org_doc'])->name('/remove_org_doc')->middleware('checkAccess:11');
    Route::post('/upload_document_organization', [UserController::class, 'upload_document_organization'])->name('/upload_document_organization')->middleware('checkAccess:11');
    Route::get('/Manage_Organizations', [UserController::class, 'Manage_Organizations'])->name('/Manage_Organizations')->middleware('checkAccess:12');
    Route::post('/get_organization_filter_result', [UserController::class, 'get_organization_filter_result'])->name('/get_organization_filter_result')->middleware('checkAccess:12');
    Route::post('/get_organization_filter_result_table', [UserController::class, 'get_organization_filter_result_table'])->name('/get_organization_filter_result_table')->middleware('checkAccess:12');
    Route::post('/load_invoices_by_user', [UserController::class, 'load_invoices_by_user'])->name('/load_invoices_by_user')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_invoices_by_user_table', [UserController::class, 'load_invoices_by_user_table'])->name('/load_invoices_by_user_table')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_invoices_by_user_table_data', [UserController::class, 'load_invoices_by_user_table_data'])->name('/load_invoices_by_user_table_data')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_timeline_by_user', [UserController::class, 'load_timeline_by_user'])->name('/load_timeline_by_user')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_timeline_by_user_table', [UserController::class, 'load_timeline_by_user_table'])->name('/load_timeline_by_user_table')->middleware('checkAccess:2||3||4||13||14');
    Route::post('/load_timeline_by_user_table_data', [UserController::class, 'load_timeline_by_user_table_data'])->name('/load_timeline_by_user_table_data')->middleware('checkAccess:2||3||4||13||14');
    Route::get('/Create_New_Customer', [UserController::class, 'Create_New_Customer'])->name('/Create_New_Customer')->middleware('checkAccess:33');
    Route::post('/save_customer', [UserController::class, 'save_customer'])->name('/save_customer')->middleware('checkAccess:33');
    Route::get('/Customer_Profile/{CUS_ID}', [UserController::class, 'Customer_Profile'])->name('/Customer_Profile')->middleware('checkAccess:32');
    Route::post('/load_invoices_by_customer', [UserController::class, 'load_invoices_by_customer'])->name('/load_invoices_by_customer')->middleware('checkAccess:32');
    Route::post('/load_invoices_by_customer_table', [UserController::class, 'load_invoices_by_customer_table'])->name('/load_invoices_by_customer_table')->middleware('checkAccess:32');
    Route::post('/load_invoices_by_customer_table_data', [UserController::class, 'load_invoices_by_customer_table_data'])->name('/load_invoices_by_customer_table_data')->middleware('checkAccess:32');
    Route::get('/Manage_Customer', [UserController::class, 'Manage_Customer'])->name('/Manage_Customer')->middleware('checkAccess:32||33||34');
    Route::post('/get_customer_filter_result', [UserController::class, 'get_customer_filter_result'])->name('/get_customer_filter_result')->middleware('checkAccess:32||33||34');
    Route::post('/get_customer_filter_result_table', [UserController::class, 'get_customer_filter_result_table'])->name('/get_customer_filter_result_table')->middleware('checkAccess:32||33||34');
    Route::post('/load_edit_customer_view', [UserController::class, 'load_edit_customer_view'])->name('/load_edit_customer_view')->middleware('checkAccess:34');
    Route::post('/update_customer', [UserController::class, 'update_customer'])->name('/update_customer')->middleware('checkAccess:34');
});

// Settings Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Create_New_Role', [SettingsController::class, 'Create_New_Role'])->name('/Create_New_Role')->middleware('checkAccess:5');
    Route::get('/Manage_Roles', [SettingsController::class, 'Manage_Roles'])->name('/Manage_Roles')->middleware('checkAccess:5');
    Route::post('/save_user_role', [SettingsController::class, 'save_user_role'])->name('/save_user_role')->middleware('checkAccess:5');
    Route::post('/load_full_role_view', [SettingsController::class, 'load_full_role_view'])->name('/load_full_role_view')->middleware('checkAccess:5');
    Route::post('/remove_access_code_from_role', [SettingsController::class, 'remove_access_code_from_role'])->name('/remove_access_code_from_role')->middleware('checkAccess:5');
    Route::post('/add_module_to_role', [SettingsController::class, 'add_module_to_role'])->name('/add_module_to_role')->middleware('checkAccess:5');
    Route::get('/Manage_Stock_Locations', [SettingsController::class, 'Manage_Stock_Locations'])->name('/Manage_Stock_Locations')->middleware('checkAccess:17');
    Route::post('/get_stock_location_result_table', [SettingsController::class, 'get_stock_location_result_table'])->name('/get_stock_location_result_table')->middleware('checkAccess:17');
    Route::post('/save_new_stock_location', [SettingsController::class, 'save_new_stock_location'])->name('/save_new_stock_location')->middleware('checkAccess:16');
    Route::post('/load_edit_stock_location_view', [SettingsController::class, 'load_edit_stock_location_view'])->name('/load_edit_stock_location_view')->middleware('checkAccess:16');
    Route::post('/update_stock_location', [SettingsController::class, 'update_stock_location'])->name('/update_stock_location')->middleware('checkAccess:16');
});

// Product Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Category_Medium', [ProductController::class, 'Category_Medium'])->name('/Category_Medium')->middleware('checkAccess:9');
    Route::post('/get_medium_result_table', [ProductController::class, 'get_medium_result_table'])->name('/get_medium_result_table')->middleware('checkAccess:9');
    Route::post('/save_new_medium', [ProductController::class, 'save_new_medium'])->name('/save_new_medium')->middleware('checkAccess:9');
    Route::post('/load_edit_medium_view', [ProductController::class, 'load_edit_medium_view'])->name('/load_edit_medium_view')->middleware('checkAccess:9');
    Route::post('/update_medium', [ProductController::class, 'update_medium'])->name('/update_medium')->middleware('checkAccess:9');
    Route::get('/Category_Grades', [ProductController::class, 'Category_Grades'])->name('/Category_Grades')->middleware('checkAccess:9');
    Route::post('/get_grade_result_table', [ProductController::class, 'get_grade_result_table'])->name('/get_grade_result_table')->middleware('checkAccess:9');
    Route::post('/save_new_grade', [ProductController::class, 'save_new_grade'])->name('/save_new_grade')->middleware('checkAccess:9');
    Route::post('/load_edit_grade_view', [ProductController::class, 'load_edit_grade_view'])->name('/load_edit_grade_view')->middleware('checkAccess:9');
    Route::post('/update_grade', [ProductController::class, 'update_grade'])->name('/update_grade')->middleware('checkAccess:9');
    Route::get('/Category_Subjects', [ProductController::class, 'Category_Subjects'])->name('/Category_Subjects')->middleware('checkAccess:9');
    Route::post('/get_subject_result_table', [ProductController::class, 'get_subject_result_table'])->name('/get_subject_result_table')->middleware('checkAccess:9');
    Route::post('/save_new_subject', [ProductController::class, 'save_new_subject'])->name('/save_new_subject')->middleware('checkAccess:9');
    Route::post('/load_edit_subject_view', [ProductController::class, 'load_edit_subject_view'])->name('/load_edit_subject_view')->middleware('checkAccess:9');
    Route::post('/update_subject', [ProductController::class, 'update_subject'])->name('/update_subject')->middleware('checkAccess:9');
    Route::get('/Category', [ProductController::class, 'Category'])->name('/Category')->middleware('checkAccess:9');
    Route::post('/get_category_result_table', [ProductController::class, 'get_category_result_table'])->name('/get_category_result_table')->middleware('checkAccess:9');
    Route::post('/save_new_category', [ProductController::class, 'save_new_category'])->name('/save_new_category')->middleware('checkAccess:9');
    Route::post('/load_edit_category_view', [ProductController::class, 'load_edit_category_view'])->name('/load_edit_category_view')->middleware('checkAccess:9');
    Route::post('/update_category', [ProductController::class, 'update_category'])->name('/update_category')->middleware('checkAccess:9');
    Route::get('/Category_Sub_Category', [ProductController::class, 'Category_Sub_Category'])->name('/Category_Sub_Category')->middleware('checkAccess:9');
    Route::post('/get_sub_category_result_table', [ProductController::class, 'get_sub_category_result_table'])->name('/get_sub_category_result_table')->middleware('checkAccess:9');
    Route::post('/save_new_sub_category', [ProductController::class, 'save_new_sub_category'])->name('/save_new_sub_category')->middleware('checkAccess:9');
    Route::post('/load_edit_sub_category_view', [ProductController::class, 'load_edit_sub_category_view'])->name('/load_edit_sub_category_view')->middleware('checkAccess:9');
    Route::post('/update_sub_category', [ProductController::class, 'update_sub_category'])->name('/update_sub_category')->middleware('checkAccess:9');
    Route::get('/Add_New_Product', [ProductController::class, 'Add_New_Product'])->name('/Add_New_Product')->middleware('checkAccess:6');
    Route::get('/get_sub_categories/{MM_ID}', [ProductController::class, 'get_sub_categories'])->name('/get_sub_categories')->middleware('checkAccess:6');
    Route::post('/save_product', [ProductController::class, 'save_product'])->name('/save_product')->middleware('checkAccess:6');
    Route::get('/Product_Profile/{PRODUCT_ID}', [ProductController::class, 'Product_Profile'])->name('/Product_Profile')->middleware('checkAccess:6||7||8');
    Route::post('/load_edit_product_profile_view', [ProductController::class, 'load_edit_product_profile_view'])->name('/load_edit_product_profile_view')->middleware('checkAccess:7');
    Route::post('/update_product', [ProductController::class, 'update_product'])->name('/update_product')->middleware('checkAccess:7');
    Route::get('/Manage_Products', [ProductController::class, 'Manage_Products'])->name('/Manage_Products')->middleware('checkAccess:8');
    Route::post('/get_product_filter_result', [ProductController::class, 'get_product_filter_result'])->name('/get_product_filter_result')->middleware('checkAccess:8');
    Route::post('/get_product_filter_result_table', [ProductController::class, 'get_product_filter_result_table'])->name('/get_product_filter_result_table')->middleware('checkAccess:8');
    Route::post('/load_in_list_by_product', [ProductController::class, 'load_in_list_by_product'])->name('/load_in_list_by_product')->middleware('checkAccess:6||7||8');
    Route::post('/load_in_list_by_product_table', [ProductController::class, 'load_in_list_by_product_table'])->name('/load_in_list_by_product_table')->middleware('checkAccess:6||7||8');
    Route::post('/load_in_list_by_product_table_data', [ProductController::class, 'load_in_list_by_product_table_data'])->name('/load_in_list_by_product_table_data')->middleware('checkAccess:6||7||8');
    Route::post('/load_out_list_by_product', [ProductController::class, 'load_out_list_by_product'])->name('/load_out_list_by_product')->middleware('checkAccess:6||7||8');
    Route::post('/load_out_list_by_product_table', [ProductController::class, 'load_out_list_by_product_table'])->name('/load_out_list_by_product_table')->middleware('checkAccess:6||7||8');
    Route::post('/load_out_list_by_product_table_data', [ProductController::class, 'load_out_list_by_product_table_data'])->name('/load_out_list_by_product_table_data')->middleware('checkAccess:6||7||8');
    Route::post('/load_available_stock_by_product', [ProductController::class, 'load_available_stock_by_product'])->name('/load_available_stock_by_product')->middleware('checkAccess:6||7||8');
    Route::post('/load_available_stock_by_product_table', [ProductController::class, 'load_available_stock_by_product_table'])->name('/load_available_stock_by_product_table')->middleware('checkAccess:6||7||8');
    Route::post('/load_available_stock_by_product_table_data', [ProductController::class, 'load_available_stock_by_product_table_data'])->name('/load_available_stock_by_product_table_data')->middleware('checkAccess:6||7||8');
    Route::post('/upload_product_cover', [ProductController::class, 'upload_product_cover'])->name('/upload_product_cover')->middleware('checkAccess:7');
});

// Stock Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Stock_In', [StockController::class, 'Stock_In'])->name('/Stock_In')->middleware('checkAccess:15');
    Route::get('/product_search', [StockController::class, 'product_search'])->name('/product_search')->middleware('checkAccess:15');
    Route::post('/save_stock_in', [StockController::class, 'save_stock_in'])->name('/save_stock_in')->middleware('checkAccess:15');
    Route::post('/save_invoice', [StockController::class, 'save_invoice'])->name('/save_invoice')->middleware('checkAccess:19');
    Route::get('/Stock_In_History', [StockController::class, 'Stock_In_History'])->name('/Stock_In_History')->middleware('checkAccess:18');
    Route::post('/get_stock_in_filter_result', [StockController::class, 'get_stock_in_filter_result'])->name('/get_stock_in_filter_result')->middleware('checkAccess:18');
    Route::post('/get_stock_in_filter_result_table', [StockController::class, 'get_stock_in_filter_result_table'])->name('/get_stock_in_filter_result_table')->middleware('checkAccess:18');
    Route::post('/load_stock_in_detail_view', [StockController::class, 'load_stock_in_detail_view'])->name('/load_stock_in_detail_view')->middleware('checkAccess:18');
    Route::get('/Stock', [StockController::class, 'Stock'])->name('/Stock')->middleware('checkAccess:21');
    Route::post('/get_stock_filter_result', [StockController::class, 'get_stock_filter_result'])->name('/get_stock_filter_result')->middleware('checkAccess:21');
    Route::post('/get_stock_filter_result_table', [StockController::class, 'get_stock_filter_result_table'])->name('/get_stock_filter_result_table')->middleware('checkAccess:21');
    Route::get('/Invoices', [StockController::class, 'Invoices'])->name('/Invoices')->middleware('checkAccess:24');
    Route::post('/get_invoice_filter_result', [StockController::class, 'get_invoice_filter_result'])->name('/get_invoice_filter_result')->middleware('checkAccess:24');
    Route::post('/get_invoices_filter_result_table', [StockController::class, 'get_invoices_filter_result_table'])->name('/get_invoices_filter_result_table')->middleware('checkAccess:24');
    Route::post('/load_invoice', [StockController::class, 'load_invoice'])->name('/load_invoice')->middleware('checkAccess:24');
    Route::get('/Normal_Invoice/{IN_ID}', [StockController::class, 'Normal_Invoice'])->name('/Normal_Invoice');
    Route::get('/VAT_Invoice/{IN_ID}', [StockController::class, 'VAT_Invoice'])->name('/VAT_Invoice');
});

// POS Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/POS', [POSController::class, 'pos'])->name('/POS')->middleware('checkAccess:19');
    Route::get('/product_search_in_pos', [POSController::class, 'product_search_in_pos'])->name('/product_search_in_pos')->middleware('checkAccess:19');
    Route::post('/load_diffrent_price_view', [POSController::class, 'load_diffrent_price_view'])->name('/load_diffrent_price_view')->middleware('checkAccess:19');
    Route::post('/load_customer_view', [POSController::class, 'load_customer_view'])->name('/load_customer_view')->middleware('checkAccess:19');
    Route::post('/get_individual_customer', [POSController::class, 'get_individual_customer'])->name('/get_individual_customer')->middleware('checkAccess:19');
    Route::post('/save_quich_customer_form', [POSController::class, 'save_quich_customer_form'])->name('/save_quich_customer_form')->middleware('checkAccess:19');
    Route::get('/get_corporate_customer_list', [POSController::class, 'get_corporate_customer_list'])->name('/get_corporate_customer_list')->middleware('checkAccess:19');
    Route::post('/get_corporate_customer', [POSController::class, 'get_corporate_customer'])->name('/get_corporate_customer')->middleware('checkAccess:19');
    Route::get('/PrintInvoice/{INVOICE_ID}', [POSController::class, 'PrintInvoice'])->name('/PrintInvoice');
    Route::post('/set_warehouse_session', [POSController::class, 'set_warehouse_session'])->name('/set_warehouse_session')->middleware('checkAccess:19');
    Route::post('/save_new_punch', [POSController::class, 'save_new_punch'])->name('/save_new_punch')->middleware('checkAccess:19');
    Route::get('/ALL_Punch_List', [POSController::class, 'ALL_Punch_List'])->name('/ALL_Punch_List')->middleware('checkAccess:36');
    Route::post('/get_punch_filterd_table', [POSController::class, 'get_punch_filterd_table'])->name('/get_punch_filterd_table')->middleware('checkAccess:36');
    Route::post('/get_punch_filterd_table_data', [POSController::class, 'get_punch_filterd_table_data'])->name('/get_punch_filterd_table_data')->middleware('checkAccess:36');
    Route::post('/load_end_punch_view', [POSController::class, 'load_end_punch_view'])->name('/load_end_punch_view')->middleware('checkAccess:35||36');
    Route::post('/punch_end_action', [POSController::class, 'punch_end_action'])->name('/punch_end_action')->middleware('checkAccess:35||36');

    Route::get('/My_Punch_List', [POSController::class, 'My_Punch_List'])->name('/My_Punch_List')->middleware('checkAccess:35');
    Route::post('/get_my_punch_filterd_table', [POSController::class, 'get_my_punch_filterd_table'])->name('/get_my_punch_filterd_table')->middleware('checkAccess:35');
    Route::post('/get_my_punch_filterd_table_data', [POSController::class, 'get_my_punch_filterd_table_data'])->name('/get_my_punch_filterd_table_data')->middleware('checkAccess:35');

    Route::post('/load_return_view', [POSController::class, 'load_return_view'])->name('/load_return_view')->middleware('checkAccess:19');
    Route::post('/get_return_invoice_form', [POSController::class, 'get_return_invoice_form'])->name('/get_return_invoice_form')->middleware('checkAccess:19');
    Route::post('/return_invoice_action', [POSController::class, 'return_invoice_action'])->name('/return_invoice_action')->middleware('checkAccess:19');
    Route::post('/load_order_to_pos', [POSController::class, 'load_order_to_pos'])->name('/load_order_to_pos')->middleware('checkAccess:19');
});

// Orders Controller
Route::middleware(['check.islog'])->group(function () {
    Route::get('/Add_New_Order', [OrdersController::class, 'Add_New_Order'])->name('/Add_New_Order')->middleware('checkAccess:22');
    Route::post('/save_order', [OrdersController::class, 'save_order'])->name('/save_order')->middleware('checkAccess:22');
    Route::get('/Manage_Order', [OrdersController::class, 'Manage_Order'])->name('/Manage_Order')->middleware('checkAccess:25');
    Route::post('/get_order_filter_result', [OrdersController::class, 'get_order_filter_result'])->name('/get_order_filter_result')->middleware('checkAccess:25');
    Route::post('/get_order_filter_result_table', [OrdersController::class, 'get_order_filter_result_table'])->name('/get_order_filter_result_table')->middleware('checkAccess:25');
    Route::get('/Order_View/{OR_ID}', [OrdersController::class, 'Order_View'])->name('/Order_View')->middleware('checkAccess:25');
    Route::post('/order_approve_action', [OrdersController::class, 'order_approve_action'])->name('/order_approve_action')->middleware('checkAccess:26');
    Route::post('/update_order_items', [OrdersController::class, 'update_order_items'])->name('/update_order_items')->middleware('checkAccess:27');
    Route::post('/order_collect_action', [OrdersController::class, 'order_collect_action'])->name('/order_collect_action')->middleware('checkAccess:28');
    Route::get('/Pending_Order_Approvals', [OrdersController::class, 'Pending_Order_Approvals'])->name('/Pending_Order_Approvals')->middleware('checkAccess:29');
    Route::get('/Retruned_Order_Approvals', [OrdersController::class, 'Retruned_Order_Approvals'])->name('/Retruned_Order_Approvals')->middleware('checkAccess:30');
    Route::get('/Completed_Order_Approvals', [OrdersController::class, 'Completed_Order_Approvals'])->name('/Completed_Order_Approvals')->middleware('checkAccess:29||30');
    Route::post('/get_order_approval', [OrdersController::class, 'get_order_approval'])->name('/get_order_approval')->middleware('checkAccess:29||30');
    Route::post('/get_order_approval_count', [OrdersController::class, 'get_order_approval_count'])->name('/get_order_approval_count')->middleware('checkAccess:29||30');
    Route::post('/get_approval_order_filter_result_table', [OrdersController::class, 'get_approval_order_filter_result_table'])->name('/get_approval_order_filter_result_table')->middleware('checkAccess:29||30');
    Route::get('/Download_Order_Form/{ORDER_ID}', [OrdersController::class, 'Download_Order_Form'])->name('/Download_Order_Form')->middleware('checkAccess:31');
});
