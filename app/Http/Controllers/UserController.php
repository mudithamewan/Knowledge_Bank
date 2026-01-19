<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\UserModel;
use App\Models\ValidationModel;
use App\Models\CommonModel;
use App\Models\StockModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class UserController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function Create_New_User()
    {
        $UserModel = new UserModel();

        $USER_ROLES = $UserModel->get_roles();

        return view('User/Create_New_User', [
            'USER_ROLES' => $USER_ROLES
        ]);
    }

    public function Manage_Users()
    {
        return view('User/Manage_Users');
    }

    public function validate_save_user($request, $isEdit = false)
    {
        $ValidationModel = new ValidationModel();
        $error = "";
        $is_set_password_1 = false;
        $is_set_password_2 = false;
        $is_set_email = false;

        $NAME = trim($request->input('NAME'));
        $NIC = trim($request->input('NIC'));
        $GENDER = trim($request->input('GENDER'));
        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
        $ADDRESS_LINE_01 = trim($request->input('ADDRESS_LINE_01'));
        $ADDRESS_LINE_02 = trim($request->input('ADDRESS_LINE_02'));
        $ADDRESS_LINE_03 = trim($request->input('ADDRESS_LINE_03'));
        $ROLE_ID = trim($request->input('ROLE_ID'));

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
            $error .= "- Contact number cannot be empty<br>";
        } else if ($ValidationModel->is_invalid_sl_contact($CONTACT_NUMBER)) {
            $error .= "- Invalid Contact Number<br>";
        }
        if ($ValidationModel->is_invalid_data($GENDER)) {
            $error .= "- Gender cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($ADDRESS_LINE_01)) {
            $error .= "- Address Line 01 cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($ROLE_ID)) {
            $error .= "- Role cannot be empty<br>";
        }

        if ($isEdit == false) {
            $PASSWORD = trim($request->input('PASSWORD'));
            $CONFIRM_PASSWORD = trim($request->input('CONFIRM_PASSWORD'));
            $EMAIL = trim($request->input('EMAIL'));

            if ($ValidationModel->is_invalid_data($PASSWORD)) {
                $error .= "- Password cannot be empty<br>";
            } else {
                $is_set_password_1 = true;
            }
            if ($ValidationModel->is_invalid_data($CONFIRM_PASSWORD)) {
                $error .= "- Confirm password cannot be empty<br>";
            } else {
                $is_set_password_2 = true;
            }

            if ($is_set_password_1 == true && $is_set_password_2 == true) {
                if ($ValidationModel->is_invalid_password($PASSWORD)) {
                    $error .= "- Password is weak!<br>";
                }
                if ($PASSWORD != $CONFIRM_PASSWORD) {
                    $error .= "- Passwords do not match<br>";
                }
            }

            if ($ValidationModel->is_invalid_data($EMAIL)) {
                $error .= "- Email cannot be empty<br>";
            } else if ($ValidationModel->is_invalid_email($EMAIL)) {
                $error .= "- Invalid Email Address<br>";
            } else if ($ValidationModel->is_existing_email($EMAIL)) {
                $error .= "- Email address already exists<br>";
            }
        } else {
            $ACTIVE_STATUS = trim($request->input('ACTIVE_STATUS'));
            if ($ValidationModel->is_invalid_data($ACTIVE_STATUS)) {
                $error .= "- Status be empty<br>";
            }
        }

        return $error;
    }

    public function save_user(Request $request)
    {
        $status = $this->validate_save_user($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $CommonModel = new CommonModel();

            $NAME = trim($request->input('NAME'));
            $NIC = trim($request->input('NIC'));
            $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
            $EMAIL = trim($request->input('EMAIL'));
            $GENDER = trim($request->input('GENDER'));
            $ADDRESS_LINE_01 = trim($request->input('ADDRESS_LINE_01'));
            $ADDRESS_LINE_02 = trim($request->input('ADDRESS_LINE_02'));
            $ADDRESS_LINE_03 = trim($request->input('ADDRESS_LINE_03'));
            $ROLE_ID = trim($request->input('ROLE_ID'));
            $PASSWORD = trim($request->input('PASSWORD'));
            $PASSWORD =  base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($PASSWORD)))))));

            DB::beginTransaction();
            try {

                $data1 = array(
                    'su_status' => 1,
                    'su_inserted_date' => date('Y-m-d H:i:s'),
                    'su_inserted_by' => session('USER_ID'),
                    'su_name' => ucwords($NAME),
                    'su_nic' => strtoupper($NIC),
                    'su_gender' => $GENDER == 'M' ? 'Male' : 'Female',
                    'su_contact_number' => $CONTACT_NUMBER,
                    'su_email' => strtolower($EMAIL),
                    'su_address_line_01' => ucwords($ADDRESS_LINE_01),
                    'su_address_line_02' => ucwords($ADDRESS_LINE_02),
                    'su_address_line_03' => ucwords($ADDRESS_LINE_03),
                    'su_is_active' => 1
                );
                $USER_ID = DB::table('system_users')->insertGetId($data1);

                $data2 = array(
                    'sur_status' => 1,
                    'sur_inserted_date' => date('Y-m-d H:i:s'),
                    'sur_inserted_by' => session('USER_ID'),
                    'sur_sr_id' => $ROLE_ID,
                    'sur_su_id' => $USER_ID,
                );
                DB::table('system_user_roles')->insert($data2);

                $data3 = array(
                    'sup_status' => 1,
                    'sup_inserted_date' => date('Y-m-d H:i:s'),
                    'sup_inserted_by' => session('USER_ID'),
                    'sup_su_id' => $USER_ID,
                    'sup_password' => $PASSWORD,
                );
                DB::table('system_user_passwords')->insert($data3);

                $CommonModel->update_work_log(session('USER_ID'), 'New user has been created. User ID:' . $USER_ID);

                DB::commit();
                return json_encode(array('success' => 'New user successfully created.', 'user_id' => $USER_ID));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function validate_get_user_filter_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $NIC = trim($request->input('NIC'));

        if ($ValidationModel->is_invalid_data($NIC)) {
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

    public function get_user_filter_result(Request $request)
    {
        $status = $this->validate_get_user_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $NIC = trim($request->input('NIC'));

            $view = (string)view('User/Manage_Users_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'NIC' => $NIC,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_user_filter_result_table(Request $request)
    {
        $status = $this->validate_get_user_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $UserModel = new UserModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $NIC = trim($request->input('NIC'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $UserModel->get_system_users($FROM_DATE, $TO_DATE, $NIC);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Manage_Users_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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

    public function User_Profile($USER_ID)
    {
        $UserModel = new UserModel();

        $USER_ID = base64_decode($USER_ID);

        $only_14 = false;
        if (in_array("14", session('USER_ACCESS_AREA'))) {
            $only_14 = true;
        }
        if (in_array("2", session('USER_ACCESS_AREA'))) {
            $only_14 = false;
        }
        if (in_array("13", session('USER_ACCESS_AREA'))) {
            $only_14 = false;
        }


        if ($only_14 == true && $USER_ID != session('USER_ID')) {
            return redirect('/Dashboard')->with('error', 'You do not have access to this area.');
        }

        $USER_DETAILS = $UserModel->get_user_data($USER_ID);
        $LAST_LOGIN_DETAILS = $UserModel->get_last_login_details($USER_ID);
        $TODAY_WORK_COUNT = $UserModel->get_today_work_count($USER_ID);
        $WAREHOUSES = $UserModel->get_user_warehouses($USER_ID);

        return view('User/User_Profile', [
            'USER_ID' => $USER_ID,
            'USER_DETAILS' => $USER_DETAILS,
            'TODAY_WORK_COUNT' => $TODAY_WORK_COUNT,
            'LAST_LOGIN_DETAILS' => $LAST_LOGIN_DETAILS,
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function load_edit_profile_view(Request $request)
    {
        $UserModel = new UserModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $USER_ID = $DATA->USER_ID;
        $USER_DETAILS = $UserModel->get_user_data($USER_ID);
        $USER_ROLES = $UserModel->get_roles();
        $WAREHOUSES = $UserModel->get_warehouses_for_add_user();

        $USER_WAREHOUSES = $UserModel->get_user_warehouses($USER_ID);
        $USER_WAREHOUSES_IDS = array();
        foreach ($USER_WAREHOUSES as $DATA) {
            array_push($USER_WAREHOUSES_IDS, $DATA->mw_id);
        }

        $view = (string)view('User/Load_Edit_Profile_View', [
            'USER_ID' => $USER_ID,
            'USER_DETAILS' => $USER_DETAILS,
            'USER_ROLES' => $USER_ROLES,
            'WAREHOUSES' => $WAREHOUSES,
            'USER_WAREHOUSES_IDS' => $USER_WAREHOUSES_IDS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function update_user(Request $request)
    {
        $status = $this->validate_save_user($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            DB::beginTransaction();
            try {
                $UserModel = new UserModel();
                $CommonModel = new CommonModel();

                $USER_ID = trim($request->input('USER_ID'));
                $NAME = trim($request->input('NAME'));
                $NIC = trim($request->input('NIC'));
                $GENDER = trim($request->input('GENDER'));
                $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
                $EMAIL = trim($request->input('EMAIL'));
                $ADDRESS_LINE_01 = trim($request->input('ADDRESS_LINE_01'));
                $ADDRESS_LINE_02 = trim($request->input('ADDRESS_LINE_02'));
                $ADDRESS_LINE_03 = trim($request->input('ADDRESS_LINE_03'));
                $ROLE_ID = trim($request->input('ROLE_ID'));
                $ACTIVE_STATUS = trim($request->input('ACTIVE_STATUS'));
                $WAREHOUSES = $request->input('WAREHOUSES');

                $USER_DETAILS = $UserModel->get_user_data($USER_ID);

                $data1 = array(
                    'su_status' => 1,
                    'su_updated_date' => date('Y-m-d H:i:s'),
                    'su_updated_by' => session('USER_ID'),
                    'su_name' => ucwords($NAME),
                    'su_nic' => strtoupper($NIC),
                    'su_gender' => $GENDER == 'M' ? 'Male' : 'Female',
                    'su_contact_number' => $CONTACT_NUMBER,
                    // 'su_email' => strtolower($EMAIL),
                    'su_address_line_01' => ucwords($ADDRESS_LINE_01),
                    'su_address_line_02' => ucwords($ADDRESS_LINE_02),
                    'su_address_line_03' => ucwords($ADDRESS_LINE_03),
                    'su_is_active' => $ACTIVE_STATUS == 'ACTIVE' ? 1 : 0
                );
                DB::table('system_users')
                    ->where('su_id', $USER_ID)
                    ->update($data1);

                if ($USER_DETAILS->sr_id != $ROLE_ID) {
                    $data2 = array(
                        'sur_updated_date' => date('Y-m-d H:i:s'),
                        'sur_updated_by' => session('USER_ID'),
                        'sur_status' => 0,
                    );
                    DB::table('system_user_roles')
                        ->where('sur_su_id', $USER_ID)
                        ->where('sur_status', 1)
                        ->update($data2);

                    $data3 = array(
                        'sur_status' => 1,
                        'sur_inserted_date' => date('Y-m-d H:i:s'),
                        'sur_inserted_by' => session('USER_ID'),
                        'sur_sr_id' => $ROLE_ID,
                        'sur_su_id' => $USER_ID,
                    );
                    DB::table('system_user_roles')->insert($data3);
                }

                $data5 = array(
                    'suw_updated_date' => date('Y-m-d H:i:s'),
                    'suw_updated_by' => session('USER_ID'),
                    'suw_status' => 0,
                );
                DB::table('system_user_warehouses')
                    ->where('suw_mw_id', $USER_ID)
                    ->where('suw_status', 1)
                    ->update($data5);


                foreach ($WAREHOUSES as $WAREHOUSE) {
                    $data3 = array(
                        'suw_status' => 1,
                        'suw_inserted_date' => date('Y-m-d H:i:s'),
                        'suw_inserted_by' => session('USER_ID'),
                        'suw_su_id' => $USER_ID,
                        'suw_mw_id' => $WAREHOUSE,
                    );
                    DB::table('system_user_warehouses')->insert($data3);
                }

                $CommonModel->update_work_log(session('USER_ID'), 'User Details updated. User ID:' . $USER_ID);

                DB::commit();
                return json_encode(array('success' => 'User Details updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_change_password_view(Request $request)
    {
        $UserModel = new UserModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $USER_ID = $DATA->USER_ID;
        $USER_DETAILS = $UserModel->get_user_data($USER_ID);

        $view = (string)view('User/Load_Change_Password_View', [
            'USER_ID' => $USER_ID,
            'USER_DETAILS' => $USER_DETAILS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function chnage_user_password(Request $request)
    {
        $ValidationModel = new ValidationModel();
        $CommonModel = new CommonModel();

        $PASSWORD = trim($request->input('PASSWORD'));
        $CONFIRM_PASSWORD = trim($request->input('CONFIRM_PASSWORD'));
        $USER_ID = trim($request->input('USER_ID'));

        if ($ValidationModel->is_invalid_data($PASSWORD)) {
            return json_encode(array('error' => 'New Password cannot be empty'));
        } else {
            $is_set_password_1 = true;
        }
        if ($ValidationModel->is_invalid_data($CONFIRM_PASSWORD)) {
            return json_encode(array('error' => 'Confirm password cannot be empty'));
        } else {
            $is_set_password_2 = true;
        }

        if ($is_set_password_1 == true && $is_set_password_2 == true) {
            if ($ValidationModel->is_invalid_password($PASSWORD)) {
                return json_encode(array('error' => 'Password is weak!'));
            }
            if ($PASSWORD != $CONFIRM_PASSWORD) {
                return json_encode(array('error' => 'Passwords do not match'));
            }
        }

        DB::beginTransaction();
        try {
            $PASSWORD =  base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($PASSWORD)))))));

            $data1 = array(
                'sup_updated_date' => date('Y-m-d H:i:s'),
                'sup_updated_by' => session('USER_ID'),
                'sup_status' => 0,
            );
            DB::table('system_user_passwords')
                ->where('sup_su_id', $USER_ID)
                ->where('sup_status', 1)
                ->update($data1);

            $data2 = array(
                'sup_status' => 1,
                'sup_inserted_date' => date('Y-m-d H:i:s'),
                'sup_inserted_by' => session('USER_ID'),
                'sup_su_id' => $USER_ID,
                'sup_password' => $PASSWORD,
            );
            DB::table('system_user_passwords')->insert($data2);

            $CommonModel->update_work_log(session('USER_ID'), 'Password has been changed. User ID:' . $USER_ID);

            DB::commit();
            return json_encode(array('success' => 'Password has been changed.'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Create_New_Organization()
    {
        $UserModel = new UserModel();

        $ORGANIZATION_TYPE = $UserModel->get_active_organization_type();
        $BUSINESS_TYPE = $UserModel->get_business_types();

        return view('User/Create_New_Organization', [
            'ORGANIZATION_TYPE' => $ORGANIZATION_TYPE,
            'BUSINESS_TYPE' => $BUSINESS_TYPE,
        ]);
    }

    public function validate_save_organization($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $NAME = trim($request->input('NAME'));
        $BUSINESS_NAME = trim($request->input('BUSINESS_NAME'));
        $CONTACT = trim($request->input('CONTACT'));
        $EMAIL = trim($request->input('EMAIL'));
        $ADDRESS = trim($request->input('ADDRESS'));
        $BR_NUMBER = trim($request->input('BR_NUMBER'));
        $BUSINESS_TYPE = trim($request->input('BUSINESS_TYPE'));
        $VAT = trim($request->input('VAT'));
        $VAT_REG_NO = trim($request->input('VAT_REG_NO'));
        $VAT_REG_DATE = trim($request->input('VAT_REG_DATE'));
        $BANK_CODE = trim($request->input('BANK_CODE'));
        $BRANCH_CODE = trim($request->input('BRANCH_CODE'));
        $ACCOUNT_NUMBER = trim($request->input('ACCOUNT_NUMBER'));
        $TYPES = $request->input('TYPES');

        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($BUSINESS_NAME)) {
            $error .= "- Business Name cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($CONTACT)) {
            $error .= "- Contact cannot be empty<br>";
        } else if ($ValidationModel->is_invalid_sl_contact($CONTACT)) {
            $error .= "- Invalid Contact Number<br>";
        }
        if ($ValidationModel->is_invalid_data($EMAIL)) {
        } else if ($ValidationModel->is_invalid_email($EMAIL)) {
            $error .= "- Invalid Email Address<br>";
        }
        if ($ValidationModel->is_invalid_data($ADDRESS)) {
            $error .= "- Address cannot be empty<br>";
        }

        if ($ValidationModel->is_invalid_data($BUSINESS_TYPE)) {
            $error .= "- Business Type cannot be empty<br>";
        }
        if (empty($TYPES)) {
            $error .= "- Tags cannot be empty<br>";
        }

        if ($VAT == 'YES' || $VAT == 'NO') {
            if ($VAT == 'YES') {
                if ($ValidationModel->is_invalid_data($VAT_REG_NO)) {
                    $error .= "- VAT Registration number cannot be empty<br>";
                }
                // if ($ValidationModel->is_invalid_data($VAT_REG_DATE)) {
                //     $error .= "- VAT Registrated date cannot be empty<br>";
                // }
            }
        } else {
            $error .= "- Tags cannot be empty<br>";
        }

        if ($request->hasFile('DOCUMENTS')) {
            foreach ($request->file('DOCUMENTS') as $file) {

                // ✅ Validate file extension
                $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                $extension = strtolower($file->getClientOriginalExtension());

                if (!in_array($extension, $allowedExtensions)) {
                    DB::rollback();
                    return response()->json([
                        $error .= '- Invalid file type. Only PDF and images are allowed.<br>'
                    ]);
                }

                // ✅ Validate file size (5 MB max)
                if ($file->getSize() > 5 * 1024 * 1024) { // bytes
                    DB::rollback();
                    return response()->json([
                        $error .= '- File size must not exceed 5 MB.<br>'
                    ]);
                }
            }
        }

        return $error;
    }

    public function save_organization(Request $request)
    {
        $CommonModel = new CommonModel();

        $status = $this->validate_save_organization($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $NAME = trim($request->input('NAME'));
            $BUSINESS_NAME = trim($request->input('BUSINESS_NAME'));
            $CONTACT = trim($request->input('CONTACT'));
            $EMAIL = trim($request->input('EMAIL'));
            $ADDRESS = trim($request->input('ADDRESS'));
            $BR_NUMBER = trim($request->input('BR_NUMBER'));
            $BUSINESS_TYPE = trim($request->input('BUSINESS_TYPE'));
            $VAT = trim($request->input('VAT'));
            $VAT_REG_NO = trim($request->input('VAT_REG_NO'));
            $VAT_REG_DATE = trim($request->input('VAT_REG_DATE'));
            $BANK_CODE = trim($request->input('BANK_CODE'));
            $BRANCH_CODE = trim($request->input('BRANCH_CODE'));
            $ACCOUNT_NUMBER = trim($request->input('ACCOUNT_NUMBER'));
            $TYPES = $request->input('TYPES');

            DB::beginTransaction();
            try {
                $data1 = array(
                    'o_status' => 1,
                    'o_inserted_date' => date('Y-m-d H:i:s'),
                    'o_inserted_by' => session('USER_ID'),
                    'o_is_active' => 1,
                    'o_name' => ucwords($NAME),
                    'o_business_name' => ucwords($BUSINESS_NAME),
                    'o_contact' => $CONTACT,
                    'o_email' => strtolower($EMAIL),
                    'o_address' => ucwords($ADDRESS),
                    'o_br_number' => $BR_NUMBER,
                    'o_mbt_id' => $BUSINESS_TYPE,
                    'o_is_vat_registered' => $VAT == 'YES' ? 1 : 0,
                    'o_vat_registered_number' => $VAT_REG_NO,
                    'o_vat_registered_date' => $VAT_REG_DATE,
                    'o_bank_code' => $BANK_CODE,
                    'o_bank_branch_code' => $BRANCH_CODE,
                    'o_account_number' => $ACCOUNT_NUMBER,
                );
                $ORG_ID = DB::table('organizations')->insertGetId($data1);

                foreach ($TYPES as $TYPE) {
                    $data2 = array(
                        'ot_status' => 1,
                        'ot_inserted_date' => date('Y-m-d H:i:s'),
                        'ot_inserted_by' => session('USER_ID'),
                        'ot_mot_id' => $TYPE,
                        'ot_o_id' => $ORG_ID,
                    );
                    DB::table('organization_types')->insert($data2);
                }

                if ($request->hasFile('DOCUMENTS')) {
                    foreach ($request->file('DOCUMENTS') as $file) {

                        $filename = time() . '_' . $file->getClientOriginalName();
                        $folderPath = 'Organization/' . $ORG_ID;
                        $filePath   = $folderPath . '/' . $filename;
                        $file->move(public_path($folderPath), $filename);

                        $data3 = array(
                            'od_status' => 1,
                            'od_inserted_date' =>  date('Y-m-d H:i:s'),
                            'od_inserted_by' => session('USER_ID'),
                            'od_o_id' => $ORG_ID,
                            'od_file_name' => $filename,
                            'od_original_name' => $file->getClientOriginalName(),
                            'od_file_path' => $filePath,
                        );
                        DB::table('organization_documents')->insert($data3);
                    }
                }

                $CommonModel->update_work_log(session('USER_ID'), 'New organization has been created. Organization ID:' . $ORG_ID);

                DB::commit();
                return json_encode(array('success' => 'New organization has been created.', 'org_id' => $ORG_ID));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Organization_Profile($ORG_ID)
    {
        $UserModel = new UserModel();

        $ORG_ID = base64_decode($ORG_ID);
        $ORGANIZATION_DETAILS = $UserModel->get_organization_data($ORG_ID);
        $ORGANIZATION_TYPE_DETAILS = $UserModel->get_organization_type_data($ORG_ID);
        $ORGANIZATION_DOCUMENTS = $UserModel->get_organization_documents_data($ORG_ID);

        $TODAY_INVOICES_COUNT = $UserModel->get_filtered_invoiecs_organization(1, date('Y-m-d'), date('Y-m-d'), $ORG_ID);
        $TOTAL_CREDIT_AMOUNT = $UserModel->get_total_credit_amount($ORG_ID);

        return view('User/Organization_Profile', [
            'ORG_ID' => $ORG_ID,
            'ORGANIZATION_DETAILS' => $ORGANIZATION_DETAILS,
            'ORGANIZATION_TYPE_DETAILS' => $ORGANIZATION_TYPE_DETAILS,
            'ORGANIZATION_DOCUMENTS' => $ORGANIZATION_DOCUMENTS,
            'TODAY_INVOICES_COUNT' => $TODAY_INVOICES_COUNT,
            'TOTAL_CREDIT_AMOUNT' => $TOTAL_CREDIT_AMOUNT,
        ]);
    }

    public function load_edit_org_profile_view(Request $request)
    {
        $UserModel = new UserModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $ORG_ID = $DATA->ORG_ID;
        $ORGANIZATION_DETAILS = $UserModel->get_organization_data($ORG_ID);
        $ORGANIZATION_TYPE_DETAILS = $UserModel->get_organization_type_data($ORG_ID);
        $ORGANIZATION_DOCUMENTS = $UserModel->get_organization_documents_data($ORG_ID);

        $ORGANIZATION_TYPE = $UserModel->get_active_organization_type();
        $BUSINESS_TYPE = $UserModel->get_business_types();

        $ORGANIZATION_TYPE_IDS = array();
        foreach ($ORGANIZATION_TYPE_DETAILS as $DATA) {
            array_push($ORGANIZATION_TYPE_IDS, $DATA->ot_mot_id);
        }

        $view = (string)view('User/Load_Edit_Organization_Profile_View', [
            'ORG_ID' => $ORG_ID,
            'ORGANIZATION_DETAILS' => $ORGANIZATION_DETAILS,
            'ORGANIZATION_TYPE_DETAILS' => $ORGANIZATION_TYPE_DETAILS,
            'ORGANIZATION_DOCUMENTS' => $ORGANIZATION_DOCUMENTS,
            'ORGANIZATION_TYPE' => $ORGANIZATION_TYPE,
            'BUSINESS_TYPE' => $BUSINESS_TYPE,
            'ORGANIZATION_TYPE_IDS' => $ORGANIZATION_TYPE_IDS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function update_organization(Request $request)
    {
        $CommonModel = new CommonModel();
        $status = $this->validate_save_organization($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            DB::beginTransaction();
            try {
                $ORG_ID = trim($request->input('ORG_ID'));
                $NAME = trim($request->input('NAME'));
                $BUSINESS_NAME = trim($request->input('BUSINESS_NAME'));
                $CONTACT = trim($request->input('CONTACT'));
                $EMAIL = trim($request->input('EMAIL'));
                $ADDRESS = trim($request->input('ADDRESS'));
                $BR_NUMBER = trim($request->input('BR_NUMBER'));
                $BUSINESS_TYPE = trim($request->input('BUSINESS_TYPE'));
                $VAT = trim($request->input('VAT'));
                $VAT_REG_NO = trim($request->input('VAT_REG_NO'));
                $VAT_REG_DATE = trim($request->input('VAT_REG_DATE'));
                $BANK_CODE = trim($request->input('BANK_CODE'));
                $BRANCH_CODE = trim($request->input('BRANCH_CODE'));
                $ACCOUNT_NUMBER = trim($request->input('ACCOUNT_NUMBER'));
                $TYPES = $request->input('TYPES');
                $ACTIVE_STATUS = trim($request->input('ACTIVE_STATUS'));

                $data1 = array(
                    'o_status' => 1,
                    'o_updated_date' => date('Y-m-d H:i:s'),
                    'o_updated_by' => session('USER_ID'),
                    'o_is_active' => $ACTIVE_STATUS == 'ACTIVE' ? 1 : 0,
                    'o_name' => ucwords($NAME),
                    'o_business_name' => ucwords($BUSINESS_NAME),
                    'o_contact' => $CONTACT,
                    'o_email' => strtolower($EMAIL),
                    'o_address' => ucwords($ADDRESS),
                    'o_br_number' => $BR_NUMBER,
                    'o_mbt_id' => $BUSINESS_TYPE,
                    'o_is_vat_registered' => $VAT == 'YES' ? 1 : 0,
                    'o_vat_registered_number' => $VAT_REG_NO,
                    'o_vat_registered_date' => $VAT_REG_DATE,
                    'o_bank_code' => $BANK_CODE,
                    'o_bank_branch_code' => $BRANCH_CODE,
                    'o_account_number' => $ACCOUNT_NUMBER,
                );
                DB::table('organizations')
                    ->where('o_id', $ORG_ID)
                    ->update($data1);

                $data2 = array(
                    'ot_status' => 0,
                    'ot_updated_date' => date('Y-m-d H:i:s'),
                    'ot_updated_by' => session('USER_ID'),
                );
                DB::table('organization_types')
                    ->where('ot_o_id', $ORG_ID)
                    ->update($data2);

                foreach ($TYPES as $TYPE) {
                    $data3 = array(
                        'ot_status' => 1,
                        'ot_inserted_date' => date('Y-m-d H:i:s'),
                        'ot_inserted_by' => session('USER_ID'),
                        'ot_mot_id' => $TYPE,
                        'ot_o_id' => $ORG_ID,
                    );
                    DB::table('organization_types')->insert($data3);
                }

                $CommonModel->update_work_log(session('USER_ID'), 'Organization Details updated. Organization ID:' . $ORG_ID);

                DB::commit();
                return json_encode(array('success' => 'Organization Details updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function remove_org_doc(Request $request)
    {
        $CommonModel = new CommonModel();
        $DOC_ID = trim($request->input('DOC_ID'));

        DB::beginTransaction();
        try {
            $data1 = array(
                'od_status' => 0,
                'od_updated_date' =>  date('Y-m-d H:i:s'),
                'od_updated_by' => session('USER_ID'),
            );
            DB::table('organization_documents')
                ->where('od_id', $DOC_ID)
                ->update($data1);

            $CommonModel->update_work_log(session('USER_ID'), 'Document has been removed. Doc ID:' . $DOC_ID);

            DB::commit();
            return json_encode(array('success' => 'Document has been removed.'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function upload_document_organization(Request $request)
    {
        $CommonModel = new CommonModel();
        $ORG_ID = trim($request->input('ORG_ID'));

        DB::beginTransaction();
        try {
            if ($request->hasFile('DOCUMENTS')) {
                foreach ($request->file('DOCUMENTS') as $file) {
                    // ✅ Validate file extension
                    $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
                    $extension = strtolower($file->getClientOriginalExtension());

                    if (!in_array($extension, $allowedExtensions)) {
                        DB::rollback();
                        return json_encode(array('error' => "Invalid file type. Only PDF and images are allowed."));
                    }

                    // ✅ Validate file size (5 MB max)
                    if ($file->getSize() > 5 * 1024 * 1024) { // bytes
                        DB::rollback();
                        return json_encode(array('error' => "File size must not exceed 5 MB."));
                    }
                }
            } else {
                return json_encode(array('error' => "Document not found!"));
            }

            if ($request->hasFile('DOCUMENTS')) {
                foreach ($request->file('DOCUMENTS') as $file) {

                    $filename = time() . '_' . $file->getClientOriginalName();
                    $folderPath = 'Organization/' . $ORG_ID;
                    $filePath   = $folderPath . '/' . $filename;
                    $file->move(public_path($folderPath), $filename);

                    $data3 = array(
                        'od_status' => 1,
                        'od_inserted_date' =>  date('Y-m-d H:i:s'),
                        'od_inserted_by' => session('USER_ID'),
                        'od_o_id' => $ORG_ID,
                        'od_file_name' => $filename,
                        'od_original_name' => $file->getClientOriginalName(),
                        'od_file_path' => $filePath,
                    );
                    DB::table('organization_documents')->insert($data3);
                }
            }

            $CommonModel->update_work_log(session('USER_ID'), 'Document has been Uploaded to the organization. Organization ID:' . $ORG_ID);

            DB::commit();
            return json_encode(array('success' => 'Document has been Uploaded to the organization.'));
        } catch (\Exception $e) {
            DB::rollback();
            return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
        }
    }

    public function Manage_Organizations()
    {
        return view('User/Manage_Organizations');
    }


    public function validate_get_organization_filter_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $BR = trim($request->input('BR'));

        if ($ValidationModel->is_invalid_data($BR)) {
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

    public function get_organization_filter_result(Request $request)
    {
        $status = $this->validate_get_organization_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $BR = trim($request->input('BR'));

            $view = (string)view('User/Manage_Organization_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'BR' => $BR,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_organization_filter_result_table(Request $request)
    {
        $status = $this->validate_get_organization_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $UserModel = new UserModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $BR = trim($request->input('BR'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $UserModel->get_organizations($FROM_DATE, $TO_DATE, $BR);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Manage_Organization_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Organization Download.xls");
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

    public function Create_New_Customer()
    {
        $UserModel = new UserModel();

        return view('User/Create_New_Customer');
    }

    public function validate_save_customer($request, $isEdit = false)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $TITLE = trim($request->input('TITLE'));
        $NAME = trim($request->input('NAME'));
        $NIC = trim($request->input('NIC'));
        $DOB = trim($request->input('DOB'));
        $GENDER = trim($request->input('GENDER'));
        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
        $EMAIL = trim($request->input('EMAIL'));
        $ADDRESS = trim($request->input('ADDRESS'));

        if ($ValidationModel->is_invalid_data($TITLE)) {
            $error .= "- Title not selected<br>";
        }
        if ($ValidationModel->is_invalid_data($NAME)) {
            $error .= "- Name cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($DOB)) {
            $error .= "- Date of birth cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
            $error .= "- Contact number cannot be empty<br>";
        } else if ($ValidationModel->is_invalid_sl_contact($CONTACT_NUMBER)) {
            $error .= "- Invalid Contact Number<br>";
        }
        if ($ValidationModel->is_invalid_data($GENDER)) {
            $error .= "- Gender cannot be empty<br>";
        }
        if ($ValidationModel->is_invalid_data($EMAIL)) {
        } else if ($ValidationModel->is_invalid_email($EMAIL)) {
            $error .= "- Invalid Email Address<br>";
        }
        if ($ValidationModel->is_invalid_data($ADDRESS)) {
            $error .= "- Address cannot be empty<br>";
        }

        return $error;
    }

    public function save_customer(Request $request)
    {
        $status = $this->validate_save_customer($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $CommonModel = new CommonModel();
            $StockModel = new StockModel();

            $TITLE = trim($request->input('TITLE'));
            $NAME = trim($request->input('NAME'));
            $DOB = trim($request->input('DOB'));
            $NIC = trim($request->input('NIC'));
            $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
            $EMAIL = trim($request->input('EMAIL'));
            $GENDER = trim($request->input('GENDER'));
            $ADDRESS = trim($request->input('ADDRESS'));

            DB::beginTransaction();
            try {

                $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_contact($CONTACT_NUMBER);
                if ($CUSTOMER_DETAILS == true) {
                    return json_encode(array('error' => "Contact number already exsisting"));
                }

                $data1 = array(
                    'c_status' => 1,
                    'c_inserted_date' => date('Y-m-d H:i:s'),
                    'c_inserted_by' => session('USER_ID'),
                    'c_is_suspend' => 0,
                    'c_contact' => $CONTACT_NUMBER,
                    'c_title' => ucwords($TITLE),
                    'c_name' => ucwords($NAME),
                    'c_dob' => $DOB,
                    'c_nic' => strtoupper($NIC),
                    'c_email' => strtolower($EMAIL),
                    'c_address' => ucwords($ADDRESS),
                    'c_gender' => $GENDER == 'M' ? 'Male' : 'Female',
                );
                $CUS_ID = DB::table('customers')->insertGetId($data1);

                $CommonModel->update_work_log(session('USER_ID'), 'New Customer has been created. Customer ID:' . $CUS_ID);

                DB::commit();
                return json_encode(array('success' => 'New Customer successfully created.', 'cus_id' => $CUS_ID));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function Customer_Profile($CUS_ID)
    {
        $StockModel = new StockModel();

        $CUS_ID = base64_decode($CUS_ID);

        $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($CUS_ID);
        $INVOICES_COUNT = $StockModel->get_customer_invoices($CUS_ID, 1);

        return view('User/Customer_Profile', [
            'CUS_ID' => $CUS_ID,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
            'INVOICES_COUNT' => $INVOICES_COUNT,
        ]);
    }



    public function load_invoices_by_customer(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $CUSTOMER_ID = $DATA->CUSTOMER_ID;
        $WAREHOUSES = $StockModel->get_active_stock_locations();

        $view = (string)view('User/Load_Invoices', [
            'WAREHOUSES' => $WAREHOUSES,
            'CUSTOMER_ID' => $CUSTOMER_ID,
        ]);
        return json_encode(array('result' => $view));
    }



    public function validate_invoices_by_customer_filter_result($request)
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

    public function load_invoices_by_customer_table(Request $request)
    {
        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $CUSTOMER_ID = trim($request->input('CUSTOMER_ID'));

            $view = (string)view('User/Load_Invoices_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
                'CUSTOMER_ID' => $CUSTOMER_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_invoices_by_customer_table_data(Request $request)
    {
        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));
            $CUSTOMER_ID = trim($request->input('CUSTOMER_ID'));

            $result = $StockModel->get_customer_invoices($CUSTOMER_ID, 0, $FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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

    public function Manage_Customer()
    {
        return view('User/Manage_Customer');
    }

    public function validate_get_customer_filter_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));
        $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));

        if ($ValidationModel->is_invalid_data($CONTACT_NUMBER)) {
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

    public function get_customer_filter_result(Request $request)
    {
        $status = $this->validate_get_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));

            $view = (string)view('User/Manage_Customer_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'CONTACT_NUMBER' => $CONTACT_NUMBER,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function get_customer_filter_result_table(Request $request)
    {
        $status = $this->validate_get_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $UserModel = new UserModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $UserModel->get_customers($FROM_DATE, $TO_DATE, $CONTACT_NUMBER);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Manage_Users_Table_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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

    public function load_edit_customer_view(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $CUS_ID = $DATA->CUS_ID;
        $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($CUS_ID);

        $view = (string)view('User/Load_Edit_Customer_View', [
            'CUS_ID' => $CUS_ID,
            'CUSTOMER_DETAILS' => $CUSTOMER_DETAILS,
        ]);
        return json_encode(array('result' => $view));
    }

    public function update_customer(Request $request)
    {
        $status = $this->validate_save_customer($request, true);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            DB::beginTransaction();
            try {
                $StockModel = new StockModel();
                $CommonModel = new CommonModel();

                $TITLE = trim($request->input('TITLE'));
                $NAME = trim($request->input('NAME'));
                $DOB = trim($request->input('DOB'));
                $NIC = trim($request->input('NIC'));
                $CONTACT_NUMBER = trim($request->input('CONTACT_NUMBER'));
                $EMAIL = trim($request->input('EMAIL'));
                $GENDER = trim($request->input('GENDER'));
                $ADDRESS = trim($request->input('ADDRESS'));
                $CUS_ID = trim($request->input('USER_ID'));
                $ACTIVE_STATUS = trim($request->input('ACTIVE_STATUS'));

                $CUSTOMER_DETAILS = $StockModel->get_customer_details_by_id($CUS_ID);
                if ($CUSTOMER_DETAILS == true) {
                    if ($CUSTOMER_DETAILS->c_contact != $CONTACT_NUMBER) {
                        $CUSTOMER_DETAILS2 = $StockModel->get_customer_details_by_contact($CONTACT_NUMBER);
                        if ($CUSTOMER_DETAILS2 == true) {
                            return json_encode(array('error' => "Contact number already exsisting"));
                        }
                    }
                }

                $data1 = array(
                    'c_status' => 1,
                    'c_inserted_date' => date('Y-m-d H:i:s'),
                    'c_inserted_by' => session('USER_ID'),
                    'c_is_suspend' => $ACTIVE_STATUS == 'SUSPEND' ? 1 : 0,
                    'c_contact' => $CONTACT_NUMBER,
                    'c_title' => ucwords($TITLE),
                    'c_name' => ucwords($NAME),
                    'c_dob' => $DOB,
                    'c_nic' => strtoupper($NIC),
                    'c_email' => strtolower($EMAIL),
                    'c_address' => ucwords($ADDRESS),
                    'c_gender' => $GENDER == 'M' ? 'Male' : 'Female',
                );
                DB::table('customers')
                    ->where('c_id', $CUS_ID)
                    ->update($data1);


                $CommonModel->update_work_log(session('USER_ID'), 'Customer Details updated. Customer ID:' . $CUS_ID);

                DB::commit();
                return json_encode(array('success' => 'Customer Details updated.'));
            } catch (\Exception $e) {
                DB::rollback();
                return json_encode(array('error' => "An error occurred. Rollback executed. <br> Error: " . $e));
            }
        }
    }

    public function load_invoices_by_user(Request $request)
    {
        $StockModel = new StockModel();

        $DATA = json_decode(trim($request->input('DATA')));

        $USER_ID = $DATA->USER_ID;
        $WAREHOUSES = $StockModel->get_active_stock_locations();

        $view = (string)view('User/Load_User_Invoices', [
            'WAREHOUSES' => $WAREHOUSES,
            'USER_ID' => $USER_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_invoices_by_user_table(Request $request)
    {
        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $USER_ID = trim($request->input('USER_ID'));

            $view = (string)view('User/Load_User_Invoices_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'MW_ID' => $MW_ID,
                'USER_ID' => $USER_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_invoices_by_user_table_data(Request $request)
    {
        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $StockModel = new StockModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $MW_ID = trim($request->input('MW_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));
            $USER_ID = trim($request->input('USER_ID'));

            $result = $StockModel->get_user_invoices($USER_ID, 0, $FROM_DATE, $TO_DATE, $MW_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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

    public function load_timeline_by_user(Request $request)
    {

        $DATA = json_decode(trim($request->input('DATA')));

        $USER_ID = $DATA->USER_ID;

        $view = (string)view('User/Load_Timeline', [
            'USER_ID' => $USER_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_timeline_by_user_table(Request $request)
    {

        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $USER_ID = trim($request->input('USER_ID'));


            $view = (string)view('User/Load_Timeline_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'USER_ID' => $USER_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_timeline_by_user_table_data(Request $request)
    {
        $status = $this->validate_invoices_by_customer_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $UserModel = new UserModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $USER_ID = trim($request->input('USER_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $UserModel->get_user_timeline($USER_ID, $FROM_DATE, $TO_DATE);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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


    public function validate_in_list_by_product_filter_result($request)
    {
        $ValidationModel = new ValidationModel();
        $error = "";

        $FROM_DATE = trim($request->input('FROM_DATE'));
        $TO_DATE = trim($request->input('TO_DATE'));

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
    public function load_invoiecs_by_organization(Request $request)
    {
        $DATA = json_decode(trim($request->input('DATA')));

        $O_ID = $DATA->O_ID;

        $view = (string)view('User/Load_Invoiecs_List', [
            'O_ID' => $O_ID,
        ]);
        return json_encode(array('result' => $view));
    }

    public function load_invoiecs_by_organization_table(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $O_ID = trim($request->input('O_ID'));

            $view = (string)view('User/Load_Invoiecs_List_Table', [
                'FROM_DATE' => $FROM_DATE,
                'TO_DATE' => $TO_DATE,
                'O_ID' => $O_ID,
            ]);
            return json_encode(array('result' => $view));
        }
    }

    public function load_invoiecs_by_organization_table_data(Request $request)
    {
        $status = $this->validate_in_list_by_product_filter_result($request);
        if (!empty($status)) {
            return json_encode(array('error' => $status));
        } else {
            $UserModel = new UserModel();

            $FROM_DATE = trim($request->input('FROM_DATE'));
            $TO_DATE = trim($request->input('TO_DATE'));
            $O_ID = trim($request->input('O_ID'));
            $DOWNLOAD = trim($request->input('DOWNLOAD'));

            $result = $UserModel->get_filtered_invoiecs_organization(0, $FROM_DATE, $TO_DATE, $O_ID);

            if ($DOWNLOAD == 'YES') {
                $view = (string)view('User/Download/Product_In_List_Download', [
                    'result' => $result,
                ]);
                header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
                header("Content-Disposition: attachment; filename=Filtered Users Download.xls");
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
