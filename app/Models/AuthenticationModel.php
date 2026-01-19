<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class AuthenticationModel extends Model
{
    use HasFactory;

    public function get_user_info_by_email_and_password($EMAIL, $PASSWORD)
    {
        $data = DB::table('system_users')
            ->join('system_user_passwords', function ($join) {
                $join->on('system_user_passwords.sup_su_id', '=', 'system_users.su_id')
                    ->where('system_user_passwords.sup_status', 1);
            })
            ->where('system_users.su_status', 1)
            ->where('system_users.su_email', $EMAIL)
            ->where('system_user_passwords.sup_password', $PASSWORD)
            ->first();

        return $data;
    }

    public function get_user_role_info($SU_ID)
    {
        $data = DB::table('system_users')
            ->join('system_user_roles', function ($join) {
                $join->on('system_user_roles.sur_su_id', '=', 'system_users.su_id')
                    ->where('system_user_roles.sur_status', 1);
            })
            ->join('system_roles', 'system_roles.sr_id', '=', 'system_user_roles.sur_sr_id')
            ->join('system_role_access', function ($join) {
                $join->on('system_role_access.sra_sr_id', '=', 'system_roles.sr_id')
                    ->where('system_role_access.sra_status', 1);
            })
            ->join('system_access_areas', 'system_access_areas.saa_id', '=', 'system_role_access.sra_saa_id')
            ->where('system_users.su_id', $SU_ID)
            ->get();

        return $data;
    }

    public function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP from shared internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP passed from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // Default: remote address
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
