<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
        }

        body {
            width: 58mm;
            margin: 0;
            padding: 0;
        }

        .center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        td {
            padding: 0;
            /* remove the vertical padding */
            vertical-align: top;
            line-height: 1;
            /* reduce space between lines */
        }

        hr {
            border: 0.4px dashed #000;
            margin: 5px 0;
        }
    </style>
</head>

<body>

    <center>
        <img src="{{public_path('assets/company_logo_black_and_white.jpg')}}" alt="" width="40%">
    </center>

    <div class="center bold" style="font-size: 12px;">Knowledge Bank Publisher</div>
    <div class="center" style="font-size: 7px">No 7A, Sethsiri Place, Pannipitiya</div>
    <div class="center" style="font-size: 6px;">Tel: 0712 100 111 / 075 5100 111</div>
    <hr>


    <table>
        <tr>
            <td width="60%">
                <div style="font-size: 7px">Invoice No: {{$INVOICE_DATA->in_invoice_no}}</div>
                <div style="font-size: 7px">Date: {{$INVOICE_DATA->in_inserted_date}}</div>
                <div style="font-size: 7px">Payment Method: {{$INVOICE_DATA->mpt_name}}</div>
            </td>
            <td>
                @if(!empty($CUSTOMER_DETAILS))
                <div style="font-size: 6px">
                    Bill To: <br>
                    @if(isset($CUSTOMER_DETAILS->o_id))
                    {{$CUSTOMER_DETAILS->o_name}}
                    @else
                    {{$CUSTOMER_DETAILS->c_title}} {{$CUSTOMER_DETAILS->c_name}}
                    @endif
                </div>
                @endif
            </td>
        </tr>
    </table>


    <table>
        <thead style="border-top: 0.4px dashed #000; border-bottom: 0.4px dashed #000; ">
            <tr>
                <th class="bold" style="font-size: 7px; text-align:left" width="40%">Item</td>
                <th class="bold" style="text-align:right; font-size: 7px; text-align:center" width="10%">Qty</td>
                <th class="bold" style="text-align:right; font-size: 7px; text-align:center" width="20%">Price</td>
                <th class="bold" style="text-align:right; font-size: 7px; text-align:center" width="10%">Dis%</td>
                <th class="bold" style="text-align:right; font-size: 7px" width="20%">Total</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($INVOICE_ITEMS_DATA as $index => $data)
            <tr>
                <td style="font-size: 7px; <?= $index == 0 ? 'padding-top:2px;' : '' ?>">{{ strtoupper($data->p_name) }}</td>
                <td style="text-align:right; font-size: 7px; text-align:center; <?= $index == 0 ? 'padding-top:2px;' : '' ?>">{{ $data->ini_qty }}</td>
                <td style="text-align:right; font-size: 7px; <?= $index == 0 ? 'padding-top:2px;' : '' ?>">{{ number_format(($data->ini_selling_price * $data->ini_qty),2) }}</td>
                <td style="text-align:right; font-size: 7px; text-align:center; <?= $index == 0 ? 'padding-top:2px;' : '' ?>">{{ empty($data->ini_discount_percentage) ? 0:$data->ini_discount_percentage }}</td>
                <td style=" text-align:right; font-size: 7px; <?= $index == 0 ? 'padding-top:2px;' : '' ?>">{{ number_format($data->ini_final_price *  $data->ini_qty, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table>
        <tr style="border-top: 0.3 solid #000;">
            <td class="bold" style="font-size: 7px; padding-top:2px;">SUBTOTAL</td>
            <td class="bold" style="text-align:right; font-size: 7px; padding-top:2px;">{{number_format($INVOICE_DATA->in_sub_total,2)}}</td>
        </tr>
        <tr style="border-bottom: 0.3px solid #000;">
            <td style="font-size: 7px; padding-bottom:2px;">DISCOUNT ({{$INVOICE_DATA->in_discount_percentage}}%)</td>
            <td style="text-align:right; font-size: 7px; padding-bottom:2px;">{{number_format($INVOICE_DATA->in_discount_amount,2)}}</td>
        </tr>
        @if($INVOICE_DATA->in_returned_amount > 0)
        <tr style="border-bottom: 0.3px solid #000;">
            <td style="font-size: 7px; padding-bottom:2px;">RETURNED</td>
            <td style="text-align:right; font-size: 7px; padding-bottom:2px;">{{number_format($INVOICE_DATA->in_returned_amount,2)}}</td>
        </tr>
        @endif
        <tr>
            <td class="bold" style="font-size: 7px; padding-top:2px;">TOTAL</td>
            <td class="bold" style="text-align:right; font-size: 7px; padding-top:2px;">{{number_format($INVOICE_DATA->in_total_payable,2)}}</td>
        </tr>
        <tr>
            <td style="font-size: 7px">PAID</td>
            <td style="text-align:right; font-size: 7px;">{{number_format($INVOICE_DATA->in_total_paid_amount,2)}}</td>
        </tr>
        <tr>
            <td style="font-size: 7px">BALANCE AMOUNT</td>
            <td style="text-align:right; font-size: 7px;">{{number_format($INVOICE_DATA->in_total_balance,2)}}</td>
        </tr>
    </table>

    <hr>

    <div class="center bold" style="font-size: 8px">THANK YOU, COME AGAIN!</div>
    <div class="center" style="font-size:6px">- NO CASH REFUNDS -</div>

    <hr>

    <div class="center" style="margin-top:6px;">
        {!! DNS1D::getBarcodeHTML($INVOICE_DATA->in_invoice_no, 'C128', 1, 25) !!}
        <div style="font-size:7px; margin-top:2px;">
            {{$INVOICE_DATA->in_invoice_no}}
        </div>
    </div>
</body>

</html>