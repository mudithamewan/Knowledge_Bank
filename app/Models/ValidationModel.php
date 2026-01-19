<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\VarDumper\VarDumper;

class ValidationModel extends Model
{
    use HasFactory;

    public function is_invalid_data($text)
    {
        if (is_null($text) || $text === '' || strtoupper($text) === 'NULL') {
            return true;
        }
        return false;
    }

    public function is_invalid_sl_contact($number)
    {
        // Remove spaces, dashes, plus signs etc.
        $cleanNumber = preg_replace('/\D/', '', $number);

        // Local format: 0XXXXXXXXX (10 digits)
        if (preg_match('/^0\d{9}$/', $cleanNumber)) {
            return false; // valid
        }

        // International format: 94XXXXXXXXX (11 digits)
        if (preg_match('/^94\d{9}$/', $cleanNumber)) {
            return false; // valid
        }

        return true; // invalid
    }

    public function is_invalid_email($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false; // valid email
        }
        return true; // invalid email
    }

    public function is_invalid_password($password)
    {
        // Check minimum length
        if (strlen($password) < 8) {
            return true;
        }

        // Check at least one uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            return true;
        }

        // Check at least one lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            return true;
        }

        // Check at least one number
        if (!preg_match('/[0-9]/', $password)) {
            return true;
        }

        // Check at least one special character
        if (!preg_match('/[\W]/', $password)) {
            return true;
        }

        return false; // password is strong
    }

    public function is_existing_email($email)
    {
        $user = DB::table('system_users')
            ->where('su_status', 1)
            ->where('su_email', $email)
            ->first();

        if ($user == true) {
            return true;
        } else {
            return false;
        }
    }

    public function is_invalid_date_range($from_date, $to_date)
    {
        // Convert to timestamps
        $from = strtotime($from_date);
        $to   = strtotime($to_date);

        // Check if either date is invalid
        if (!$from || !$to) {
            return true; // invalid dates
        }

        // Check if FROM_DATE is after TO_DATE
        if ($from > $to) {
            return true; // invalid range
        }

        return false; // valid range
    }
}
