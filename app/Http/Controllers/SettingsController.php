<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\SettingsModel;
use App\Models\ValidationModel;
use App\Models\CommonModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class SettingsController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Create_New_Role()
    {
        $SettingsModel = new SettingsModel();

        $MODULES = $SettingsModel->get_access_areas();
        return view('Settings/Create_New_Role', [
            'MODULES' => $MODULES
        ]);
    }

    public function save_user_role(Request $request)
    {
        $NAME = trim($request->input('NAME'));
        $MODULES = $request->input('MODULES');

        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        if ($ValidationModel->is_invalid_data($NAME)) {
            return json_encode(array('error' => 'Role Name cannot be empty'));
        }
        if (empty($MODULES)) {
            return json_encode(array('error' => 'Modules cannot be empty'));
        }

        DB::beginTransaction();
        try {

            $data1 = array(
                'sr_status' => 1,
                'sr_inserted_date' => date('Y-m-d H:i:s'),
                'sr_inserted_by' => session('USER_ID'),
                'sr_name' => $NAME,
            );
            $ROLE_ID = DB::table('system_roles')->insertGetId($data1);

            // add dashboard defult
            $data2 = array(
                'sra_status' => 1,
                'sra_inserted_date' => date('Y-m-d H:i:s'),
                'sra_inserted_by' => session('USER_ID'),
                'sra_sr_id' => $ROLE_ID,
                'sra_saa_id' => 1,
            );
            DB::table('system_role_access')->insert($data2);

            foreach ($MODULES as $module) {
                $data2 = array(
                    'sra_status' => 1,
                    'sra_inserted_date' => date('Y-m-d H:i:s'),
                    'sra_inserted_by' => session('USER_ID'),
                    'sra_sr_id' => $ROLE_ID,
                    'sra_saa_id' => $module,
                );
                DB::table('system_role_access')->insert($data2);
            }

            $CommonModel->update_work_log(session('USER_ID'), 'New Role Created. Role ID:' . $ROLE_ID);

            DB::commit();
            return json_encode(array('success' => 'Role has been created'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Manage_Roles()
    {
        $UserModel = new UserModel();

        $ROLES = $UserModel->get_roles();

        return view('Settings/Manage_Roles', [
            'ROLES' => $ROLES
        ]);
    }

    public function load_full_role_view(Request $request)
    {
        $SettingsModel = new SettingsModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $ROLE_ID = $DATA->ROLE_ID;

        $ACCESS_MODULES = $SettingsModel->get_access_areas();
        $ROLE_DETAILS = $SettingsModel->get_role_details_by_id($ROLE_ID);
        $ROLE_MODULE_IDS = array();
        foreach ($ROLE_DETAILS as $data1) {
            array_push($ROLE_MODULE_IDS, $data1->saa_id);
        }

        $ROLE = $SettingsModel->get_role_data($ROLE_ID);

        $view = (string)view('Settings/Load_Full_Role_View', [
            'ROLE_ID' => $ROLE_ID,
            'ROLE_DETAILS' => $ROLE_DETAILS,
            'ACCESS_MODULES' => $ACCESS_MODULES,
            'ROLE_MODULE_IDS' => $ROLE_MODULE_IDS,
            'ROLE' => $ROLE,
        ]);
        return json_encode(array('result' => $view));
    }

    public function remove_access_code_from_role(Request $request)
    {
        $CommonModel = new CommonModel();

        DB::beginTransaction();
        try {
            $ID = trim($request->input('ID'));

            $data1 = array(
                'sra_updated_date' => date('Y-m-d H:i:s'),
                'sra_updated_by' => session('USER_ID'),
                'sra_status' => 0,
            );
            DB::table('system_role_access')
                ->where('sra_id', $ID)
                ->update($data1);

            $CommonModel->update_work_log(session('USER_ID'), 'Access has been removed from the role. Role ID: ' . $ID);

            DB::commit();
            return json_encode(array('success' => 'Access has been removed'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function add_module_to_role(Request $request)
    {
        $CommonModel = new CommonModel();

        $MODULES = $request->input('MODULES');
        $STATUS = trim($request->input('STATUS'));
        $ROLE_NAME = trim($request->input('ROLE_NAME'));

        if (empty($ROLE_NAME)) {
            return json_encode(array('error' => 'Role name cannot be empty'));
        }

        $CommonModel = new CommonModel();
        // if (empty($MODULES)) {
        //     return json_encode(array('error' => 'Modules cannot be empty'));
        // }

        DB::beginTransaction();
        try {
            $ROLE_ID = trim($request->input('ROLE_ID'));

            $data1 = array(
                'sr_is_active' => $STATUS,
                'sr_name' => $ROLE_NAME,
            );
            DB::table('system_roles')
                ->where('sr_id', $ROLE_ID)
                ->update($data1);

            if (!empty($MODULES)) {
                foreach ($MODULES as $module) {
                    $data2 = array(
                        'sra_status' => 1,
                        'sra_inserted_date' => date('Y-m-d H:i:s'),
                        'sra_inserted_by' => session('USER_ID'),
                        'sra_sr_id' => $ROLE_ID,
                        'sra_saa_id' => $module,
                    );
                    DB::table('system_role_access')->insert($data2);
                }
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Role details have been updated. Role ID: ' . $ROLE_ID);

            DB::commit();
            return json_encode(array('success' => 'Role details have been updated.'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Manage_Stock_Locations()
    {
        $SettingsModel = new SettingsModel();

        $STOCK_LOCATION_TYPES = $SettingsModel->get_stock_location_types();
        $USERS = $SettingsModel->get_system_users();

        return view('Settings/Manage_Stock_Locations', [
            'STOCK_LOCATION_TYPES' => $STOCK_LOCATION_TYPES,
            'USERS' => $USERS,
        ]);
    }

    public function save_new_stock_location(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();
        $UserModel = new UserModel();

        $error = "";
        $NAME = trim($request->input('NAME'));
        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
        $EMAIL = trim($request->input('EMAIL'));
        $ADDRESS = trim($request->input('ADDRESS'));
        $TYPE = trim($request->input('TYPE'));
        $VEHICLE_NO = trim($request->input('VEHICLE_NO'));
        $USER_ID = trim($request->input('USER_ID'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }

        if ($ValidationModel->is_invalid_data($TYPE)) {
            $error .= "- Type cannot be empty<br>";
        } else if ($TYPE == 3) {
            if ($ValidationModel->is_invalid_data($VEHICLE_NO)) {
                $error .= "- Vehicle Number cannot be empty<br>";
            }
            if ($ValidationModel->is_invalid_data($USER_ID)) {
                $error .= "- User cannot be empty<br>";
            }
        } else {
            if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
                // $error .= "- Contact number cannot be empty<br>";
            } else if ($ValidationModel->is_invalid_sl_contact($CONTACT_NUMBER)) {
                $error .= "- Invalid Contact number address!<br>";
            }
            if ($ValidationModel->is_invalid_data($ADDRESS)) {
                $error .= "- Address cannot be empty<br>";
            }
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                if ($TYPE == 3) {
                    $USER_INFO = $UserModel->get_user_data($USER_ID);
                    $CONTACT_NUMBER = $USER_INFO->su_contact_number;
                    $EMAIL = $USER_INFO->su_email;
                    $ADDRESS = $USER_INFO->su_address_line_01 . " " . $USER_INFO->su_address_line_02 . " " . $USER_INFO->su_address_line_03;
                }

                $data1 = array(
                    'mw_status' => 1,
                    'mw_inserted_date' => date('Y-m-d H:i:s'),
                    'mw_inserted_by' => session('USER_ID'),
                    'mw_name' => ucwords($NAME),
                    'mw_contact_number' => $CONTACT_NUMBER,
                    'mw_email' => strtolower($EMAIL),
                    'mw_address' => ucwords($ADDRESS),
                    'mw_vehicle_no' => $TYPE == 3 ? strtoupper($VEHICLE_NO) : null,
                    'mw_mwt_id' => $TYPE,
                    'mw_is_active' => 1,
                );
                $WAREHOUSE_ID = DB::table('master_warehouses')->insertGetId($data1);

                if ($TYPE == 3) {
                    $data3 = array(
                        'suw_status' => 1,
                        'suw_inserted_date' => date('Y-m-d H:i:s'),
                        'suw_inserted_by' => session('USER_ID'),
                        'suw_su_id' => $USER_ID,
                        'suw_mw_id' => $WAREHOUSE_ID,
                    );
                    DB::table('system_user_warehouses')->insert($data3);
                }

                $CommonModel->update_work_log(session('USER_ID'), 'New Stock Location has been created. Stock Location ID:' . $WAREHOUSE_ID);

                DB::commit();
                return json_encode(array('success' => 'New Stock Location has been created.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function get_stock_location_result_table(Request $request)
    {
        $DOWNLOAD = trim($request->input('DOWNLOAD'));

        $SettingsModel = new SettingsModel();
        $result = $SettingsModel->get_stock_locations();

        if ($DOWNLOAD == 'YES') {
            $view = (string)view('Settings/Download/Stock_Locations_Download', [
                'result' => $result,
            ]);
            header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
            header("Content-Disposition: attachment; filename=Stock Location List Download.xls");
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

    public function load_edit_stock_location_view(Request $request)
    {
        $ValidationModel = new ValidationModel();

        $error = "";
        $DATA = json_decode(trim($request->input('DATA')));
        $WAREHOUSE_ID = $DATA->WAREHOUSE_ID;
        if ($ValidationModel->is_invalid_data($WAREHOUSE_ID)) {
            $error .= "- Something went wrong!<br>";
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            $SettingsModel = new SettingsModel();
            $WAREHOUSE_DETAILS = $SettingsModel->get_stock_location_details_by_id($WAREHOUSE_ID);
            $STOCK_LOCATION_TYPES = $SettingsModel->get_stock_location_types();
            $USERS = $SettingsModel->get_system_users();

            $view = (string)view('Settings/Load_Edit_Stock_Location_View', [
                'WAREHOUSE_ID' => $WAREHOUSE_ID,
                'WAREHOUSE_DETAILS' => $WAREHOUSE_DETAILS,
                'STOCK_LOCATION_TYPES' => $STOCK_LOCATION_TYPES,
                'USERS' => $USERS,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function update_stock_location(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $UserModel = new UserModel();

        $error = "";
        $MW_ID = trim($request->input('MW_ID'));
        $NAME = trim($request->input('NAME'));
        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
        $EMAIL = trim($request->input('EMAIL'));
        $STATUS = trim($request->input('STATUS'));
        $ADDRESS = trim($request->input('ADDRESS'));
        $TYPE = trim($request->input('TYPE'));
        $VEHICLE_NO = trim($request->input('VEHICLE_NO'));
        $USER_ID = trim($request->input('USER_ID'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }

        if ($STATUS == '1' || $STATUS == '0') {
        } else {
            $error .= "- Invalid Status Code.<br>";
        }
        if ($ValidationModel->is_invalid_data($TYPE)) {
            $error .= "- Type cannot be empty<br>";
        } else if ($TYPE == 3) {
            if ($ValidationModel->is_invalid_data($VEHICLE_NO)) {
                $error .= "- Vehicle Number cannot be empty<br>";
            }
            if ($ValidationModel->is_invalid_data($VEHICLE_NO)) {
                $error .= "- Vehicle Number cannot be empty<br>";
            }
            if ($ValidationModel->is_invalid_data($USER_ID)) {
                $error .= "- User cannot be empty<br>";
            }
        } else {
            if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
                // $error .= "- Contact number cannot be empty<br>";
            } else if ($ValidationModel->is_invalid_sl_contact($CONTACT_NUMBER)) {
                $error .= "- Invalid Contact number address!<br>";
            }
            if ($ValidationModel->is_invalid_data($ADDRESS)) {
                $error .= "- Address cannot be empty<br>";
            }
        }

        if (!empty($error)) {
            return json_encode(array('error' => $error));
        } else {
            DB::beginTransaction();
            try {

                $CommonModel = new CommonModel();

                if ($TYPE == 3) {
                    $USER_INFO = $UserModel->get_user_data($USER_ID);
                    $CONTACT_NUMBER = $USER_INFO->su_contact_number;
                    $EMAIL = $USER_INFO->su_email;
                    $ADDRESS = $USER_INFO->su_address_line_01 . " " . $USER_INFO->su_address_line_02 . " " . $USER_INFO->su_address_line_03;
                }

                $data1 = array(
                    'mw_updated_date' => date('Y-m-d H:i:s'),
                    'mw_updated_by' => session('USER_ID'),
                    'mw_is_active' => $STATUS,
                    'mw_name' => ucwords($NAME),
                    'mw_contact_number' => $CONTACT_NUMBER,
                    'mw_email' => strtolower($EMAIL),
                    'mw_address' => ucwords($ADDRESS),
                    'mw_vehicle_no' => $TYPE == 3 ? strtoupper($VEHICLE_NO) : null,
                    'mw_mwt_id' => $TYPE,
                );
                DB::table('master_warehouses')
                    ->where('mw_id', $MW_ID)
                    ->update($data1);


                if ($TYPE == 3) {
                    $data2 = array(
                        'suw_status' => 0,
                        'suw_updated_date' => date('Y-m-d H:i:s'),
                        'suw_updated_by' => session('USER_ID'),
                    );
                    DB::table('system_user_warehouses')
                        ->where('suw_su_id', $USER_ID)
                        ->where('suw_mw_id', $MW_ID)
                        ->update($data2);

                    $data3 = array(
                        'suw_status' => 1,
                        'suw_inserted_date' => date('Y-m-d H:i:s'),
                        'suw_inserted_by' => session('USER_ID'),
                        'suw_su_id' => $USER_ID,
                        'suw_mw_id' => $MW_ID,
                    );
                    DB::table('system_user_warehouses')->insert($data3);
                }

                $CommonModel->update_work_log(session('USER_ID'), 'Stock Location details has been updated. Stock Location ID:' . $MW_ID);

                DB::commit();
                return json_encode(array('success' => 'Stock Location details has been updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }
}
