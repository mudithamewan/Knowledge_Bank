<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\DashboardModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class DashboardController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $DashboardModel = new DashboardModel();

        $TODAY_DATA = $DashboardModel->get_collection_data(0, date('Y-m-d'), date('Y-m-d'));
        $MONTH_DATA = $DashboardModel->get_collection_data(0, date('Y-m-01'), date('Y-m-t'));

        return view('Dashboard/Dashboard', [
            'TODAY_DATA' => $TODAY_DATA,
            'MONTH_DATA' => $MONTH_DATA,
        ]);
    }

    public function Load_Widget(Request $request)
    {
        $TYPE = $request->input('type');
        $function = "get_" . $TYPE;

        $view = $this->$function();

        return json_encode(array('result' => $view));
    }

    public function get_daily_status()
    {
        $DashboardModel = new DashboardModel();

        $sale_amounts = "";
        $collection_amount = "";
        $credit_amount = "";
        $dates = "";

        for ($i = 0; $i <= 10; $i++) {
            $originalDate = date('Y-m-d');
            $newDate = date('Y-m-d', strtotime('-' . $i . ' day', strtotime($originalDate)));

            $DAY_DATA = $DashboardModel->get_collection_data(0, $newDate, $newDate);

            $dates .= "\"" . $newDate . "\",";
            $sale_amounts .= $DAY_DATA->total_sale . ",";
            $collection_amount .= $DAY_DATA->total_collection . ",";
            $credit_amount .=  $DAY_DATA->total_credit . ",";
        }

        $view = (string)view('Dashboard/Daily_Status', [
            'dates' => $dates,
            'sale_amounts' => $sale_amounts,
            'collection_amount' => $collection_amount,
            'credit_amount' => $credit_amount,
        ]);
        return $view;
    }

    public function get_monthly_status()
    {
        $DashboardModel = new DashboardModel();

        $sale_amounts = "";
        $collection_amount = "";
        $credit_amount = "";
        $dates = "";

        // 🔁 LOOP FROM 11 MONTHS AGO → CURRENT MONTH
        for ($i = 12; $i >= 0; $i--) {

            // Month start & end
            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd   = date('Y-m-t', strtotime("-$i months"));

            // Get month-wise data
            $MONTH_DATA = $DashboardModel->get_collection_data(0, $monthStart, $monthEnd);

            // Label like "Jan 2026"
            $label = date('M Y', strtotime($monthStart));

            $dates .= '"' . $label . '",';
            $sale_amounts .= ($MONTH_DATA->total_sale ?? 0) . ",";
            $collection_amount .= ($MONTH_DATA->total_collection ?? 0) . ",";
            $credit_amount .= ($MONTH_DATA->total_credit ?? 0) . ",";
        }

        $view = (string)view('Dashboard/Monthly_Status', [
            'dates' => rtrim($dates, ','),
            'sale_amounts' => rtrim($sale_amounts, ','),
            'collection_amount' => rtrim($collection_amount, ','),
            'credit_amount' => rtrim($credit_amount, ','),
        ]);
        return $view;
    }
}
