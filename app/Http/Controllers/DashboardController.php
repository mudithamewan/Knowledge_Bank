<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use App\Http\reqested_item;
use App\Http\Controllers\Controller;
use App\Models\DashboardModel;
use App\Models\StockModel;
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
        $StockModel = new StockModel();

        $TODAY_DATA = $DashboardModel->get_collection_data(0, date('Y-m-d'), date('Y-m-d'));
        $MONTH_DATA = $DashboardModel->get_collection_data(0, date('Y-m-01'), date('Y-m-t'));
        $WAREHOUSES = $StockModel->get_active_stock_locations();

        return view('Dashboard/Dashboard', [
            'TODAY_DATA' => $TODAY_DATA,
            'MONTH_DATA' => $MONTH_DATA,
            'WAREHOUSES' => $WAREHOUSES,
        ]);
    }

    public function Load_Widget(Request $request)
    {
        $TYPE = $request->input('type');
        $function = "get_" . $TYPE;

        if ($TYPE == 'warehouse_status' || $TYPE == 'product_stock') {
            $MW_ID = $request->input('MW_ID');
            $view = $this->$function($MW_ID);
        } else {
            $view = $this->$function();
        }

        return json_encode(array('result' => $view));
    }

    public function get_product_stock($MW_ID)
    {
        $DashboardModel = new DashboardModel();
        $STOCK =  $DashboardModel->get_product_stocks($MW_ID);

        $view = (string) view('Dashboard/Product_Stock', [
            'STOCK' => $STOCK,
        ]);
        return $view;
    }

    public function get_warehouse_status($MW_ID)
    {
        $DashboardModel = new DashboardModel();

        $day_sale_amounts = "";
        $day_collection_amount = "";
        $day_credit_amount = "";
        $dates = "";
        $month_sale_amounts = "";
        $month_collection_amount = "";
        $month_credit_amount = "";
        $months = "";

        for ($i = 13; $i >= 0; $i--) {

            $date = date('Y-m-d', strtotime("-$i days"));

            $DAY_DATA = $DashboardModel->get_collection_data(0, $date, $date, $MW_ID);

            $dates .= '"' . $date . '",';
            $day_sale_amounts .= ($DAY_DATA->total_sale ?? 0) . ",";
            $day_collection_amount .= ($DAY_DATA->total_collection ?? 0) . ",";
            $day_credit_amount .= ($DAY_DATA->total_credit ?? 0) . ",";
        }

        for ($i = 12; $i >= 0; $i--) {

            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd   = date('Y-m-t', strtotime("-$i months"));

            $MONTH_DATA = $DashboardModel->get_collection_data(0, $monthStart, $monthEnd, $MW_ID);

            $label = date('M Y', strtotime($monthStart));

            $months .= '"' . $label . '",';
            $month_sale_amounts .= ($MONTH_DATA->total_sale ?? 0) . ",";
            $month_collection_amount .= ($MONTH_DATA->total_collection ?? 0) . ",";
            $month_credit_amount .= ($MONTH_DATA->total_credit ?? 0) . ",";
        }

        $view = (string) view('Dashboard/Warehouse_Status', [
            'day_sale_amounts' => rtrim($day_sale_amounts, ','),
            'day_collection_amount' => rtrim($day_collection_amount, ','),
            'day_credit_amount' => rtrim($day_credit_amount, ','),
            'dates' => rtrim($dates, ','),

            'month_sale_amounts' => rtrim($month_sale_amounts, ','),
            'month_collection_amount' => rtrim($month_collection_amount, ','),
            'month_credit_amount' => rtrim($month_credit_amount, ','),
            'months' => rtrim($months, ','),
        ]);
        return $view;
    }

    public function get_daily_status()
    {
        $DashboardModel = new DashboardModel();

        $sale_amounts = "";
        $collection_amount = "";
        $credit_amount = "";
        $dates = "";

        for ($i = 13; $i >= 0; $i--) {

            $date = date('Y-m-d', strtotime("-$i days"));

            $DAY_DATA = $DashboardModel->get_collection_data(0, $date, $date);

            $dates .= '"' . $date . '",';
            $sale_amounts .= ($DAY_DATA->total_sale ?? 0) . ",";
            $collection_amount .= ($DAY_DATA->total_collection ?? 0) . ",";
            $credit_amount .= ($DAY_DATA->total_credit ?? 0) . ",";
        }

        $view = (string) view('Dashboard/Daily_Status', [
            'dates' => rtrim($dates, ','),
            'sale_amounts' => rtrim($sale_amounts, ','),
            'collection_amount' => rtrim($collection_amount, ','),
            'credit_amount' => rtrim($credit_amount, ','),
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

        for ($i = 12; $i >= 0; $i--) {

            $monthStart = date('Y-m-01', strtotime("-$i months"));
            $monthEnd   = date('Y-m-t', strtotime("-$i months"));

            $MONTH_DATA = $DashboardModel->get_collection_data(0, $monthStart, $monthEnd);

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
