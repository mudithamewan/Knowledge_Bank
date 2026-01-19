<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\AuthenticationModel;
use App\Models\UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class AuthenticationController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function test()
    {
        return view('/test');
    }

    public function login()
    {
        if (session('STATUS_CODE') == 'SUCESS') {
            return redirect('/Dashboard')->with('status', 'Login!');
        } else {
            return view('Authentication/Login');
        }
    }

    public function authentication(Request $request)
    {
        $AuthenticationModel = new AuthenticationModel();

        $validator = Validator::make($request->all(), [
            'EMAIL' => 'required',
            'PASSWORD' => 'required',
        ]);
        if ($validator->fails()) {
            return Redirect::back()->with('error', 'Somthing went wrong. Try Again!');
        } else {
            Session::flush();

            $EMAIL = trim($request->input('EMAIL'));
            $PASSWORD = trim($request->input('PASSWORD'));
            $PASSWORD = base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode(base64_encode($PASSWORD)))))));

            $USER_INFO = $AuthenticationModel->get_user_info_by_email_and_password($EMAIL, $PASSWORD);

            if ($USER_INFO == false) {
                return Redirect::back()->with('error', 'Invalid email or password. Try Again!');
            } else if ($USER_INFO->su_is_active == 0) {
                return Redirect::back()->with('error', 'Your account has been deactivated temporarily.');
            } else {
                $USER_ROLE = $AuthenticationModel->get_user_role_info($USER_INFO->su_id);
                if (count($USER_ROLE) <= 0) {
                    return Redirect::back()->with('error', 'No modules assigned to your account.');
                } else if ($USER_ROLE[0]->sr_is_active == 0) {
                    return Redirect::back()->with('error', 'Your role has been deactivated temporarily.');
                }

                $USER_ACCESS_AREA = array();
                $USER_ACCESS_HEADERS = array();
                foreach ($USER_ROLE as $role) {
                    array_push($USER_ACCESS_AREA, $role->saa_id);
                    if (!in_array($role->saa_category, $USER_ACCESS_HEADERS)) {
                        array_push($USER_ACCESS_HEADERS, $role->saa_category);
                    }
                }

                if ($USER_INFO->su_gender == 'Male') {
                    $imagePath = url('/') . "/assets/images/man.jpg";
                } else {
                    $imagePath = url('/') . "/assets/images/woman.jpg";
                }

                $UserModel = new UserModel();
                $WAREHOUSES = $UserModel->get_user_warehouses($USER_INFO->su_id);
                $USER_WAREHOUSES_IDS = array();
                foreach ($WAREHOUSES as $DATA) {
                    array_push($USER_WAREHOUSES_IDS, $DATA->mw_id);
                }

                $request->session()->put('STATUS_CODE', 'SUCESS');
                $request->session()->put('USER_WAREHOUSES', $USER_WAREHOUSES_IDS);
                $request->session()->put('USER_ID', $USER_INFO->su_id);
                $request->session()->put('USER_NAME', $USER_INFO->su_name);
                $request->session()->put('USER_EMAIL', $USER_INFO->su_email);
                $request->session()->put('USER_CONTACT', $USER_INFO->su_contact_number);
                $request->session()->put('USER_ADDRESS', $USER_INFO->su_address_line_01 . " " . $USER_INFO->su_address_line_02 . " " . $USER_INFO->su_address_line_03);
                $request->session()->put('IMAGE_PATH', $imagePath);
                $request->session()->put('USER_ACCESS_AREA', $USER_ACCESS_AREA);
                $request->session()->put('USER_ACCESS_HEADERS', $USER_ACCESS_HEADERS);
                $request->session()->put('USER_ROLE', $USER_ROLE[0]->sr_name);

                $data = array(
                    'ull_status' => 1,
                    'ull_inserted_date' => date('Y-m-d H:i:s'),
                    'ull_inserted_by' => $USER_INFO->su_id,
                    'ull_su_id' => $USER_INFO->su_id,
                    'ull_ip' => $AuthenticationModel->getUserIpAddr(),
                    'ull_browser' => $_SERVER['HTTP_USER_AGENT'],
                );
                DB::table('user_login_logs')->insert($data);

                return redirect('/Dashboard')->with('status', 'Login!');
            }
        }
    }

    public function logout(Request $request)
    {
        Session::flush();
        Session::regenerateToken();
        return view('Authentication/Login');
    }
}
