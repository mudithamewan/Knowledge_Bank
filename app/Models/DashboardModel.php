<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Expr\FuncCall;
use Symfony\Component\VarDumper\VarDumper;

class DashboardModel extends Model
{
    use HasFactory;

    public function get_collection_data($IS_COUNT, $FROM_DATE = null, $TO_DATE = null, $MW_ID = null)
    {
        $summary = DB::table('invoices')
            ->leftJoin('master_credit_periods', 'master_credit_periods.mcp_id', '=', 'invoices.in_mcp_id')
            ->where('in_status', 1)
            ->selectRaw('
                SUM(in_total_payable) AS total_sale,
                (
                    SUM(in_total_paid_amount)
                    + SUM(
                        CASE
                            WHEN in_total_balance < 0
                            THEN in_total_balance
                            ELSE 0
                        END
                    )
                ) AS total_collection,
                SUM(
                    CASE
                        WHEN in_total_balance > 0
                        THEN in_total_balance
                        ELSE 0
                    END
                ) AS total_credit
            ');

        if (!empty($FROM_DATE) || !empty($TO_DATE)) {
            $formattedFromDate = date('Y-m-d', strtotime($FROM_DATE));
            $formattedToDate = date('Y-m-d', strtotime($TO_DATE . ' +1 days'));
            $summary->whereBetween('invoices.in_inserted_date', [$formattedFromDate, $formattedToDate]);
        }

        if (!empty($MW_ID)) {
            $summary->where('invoices.in_mw_id', $MW_ID);
        }

        $result = $summary->first();

        return $result;
    }

    public function get_product_stocks($MW_ID)
    {
        $result = DB::table('products')
            ->join('master_categories', 'master_categories.mc_id', '=', 'products.p_mc_id')
            ->leftJoin('available_stock', function ($join) use ($MW_ID) {
                $join->on('available_stock.as_p_id', '=', 'products.p_id')
                    ->where('available_stock.as_status', 1)
                    ->where('available_stock.as_mw_id', $MW_ID);
            })
            ->where('products.p_status', 1)
            ->where('products.p_is_discontinued', 0)
            ->groupBy('products.p_id')
            ->select(
                'products.*',
                'master_categories.*',
                DB::raw('SUM(available_stock.as_available_qty) AS as_available_qty')
            )
            ->orderBy('as_available_qty', 'ASC')
            ->limit(50)
            ->get();

        return $result;
    }
}
