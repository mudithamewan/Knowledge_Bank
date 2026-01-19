<style>
    .invoice_content * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .tbl table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .tbl table th,
    .tbl table td {
        border: 1px solid #ddd;
        padding: 5px;
        text-align: left;
        font-size: 12px;
    }
</style>

<div class="invoice_content">
    <table width="100%" style="border-bottom: 1px solid #292561; padding-bottom:5px;">
        <tr>
            <td width="50%" style="vertical-align: top; text-align: left; font-size: 25px; color:#292561; font-weight:bold;">VAT INVOICE</td>
            <td style="font-size: 12px; text-align:right">
                <table width="100%">
                    <tr>
                        <td style="text-align: right; padding-right:5px;">
                            <span style="font-size: 15px; color:#292561;"><strong>Knowledge Bank Publisher</strong></span><br>
                            <span style="color:#383838;">
                                VAT No: 102274292 - 7000<br>
                                No 7A Sethsiri Place, Pannipitiya, Sri Lanka<br>
                                Email: knowledgebank2013@gmail.com<br>
                                Hot Line: 0712 100 111 / 075 5100 111
                            </span>
                        </td>
                        <td width="15%">
                            <img src="{{public_path('assets/company_logo_only.png')}}" alt="" width="100%">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table width="100%" style="margin-top: 15px;">
        <tr>
            <td style="vertical-align: top; font-size: 12px;" width="50%">
                <div style="margin-bottom: 5px;"><span style="color:#292561"><b>DATE :</b></span> {{$INVOICE_DATA->in_inserted_date}}</div>
                <div style="margin-bottom: 5px;"><span style="color:#292561"><b>INVOICE NO :</b></span> {{$INVOICE_DATA->in_invoice_no}}</div>
                <div style="margin-bottom: 5px;"><span style="color:#292561"><b>PAYMENT MODE :</b></span> {{$INVOICE_DATA->mpt_name}}</div>
            </td>
            <td style="vertical-align: top; font-size: 12px; text-align:right" width="50%">

                @if(!empty($CUSTOMER_DETAILS))
                <div style="margin-bottom: 5px;"><span style="color:#292561"><b>BILLED TO :</b></span></div>
                @if(isset($CUSTOMER_DETAILS->o_id))
                {{$CUSTOMER_DETAILS->o_name}}, <br>
                {{$CUSTOMER_DETAILS->o_address}} <br>
                @if($CUSTOMER_DETAILS->o_is_vat_registered == 1)
                <span style="color:#292561"><b>VAT REG NO :</b></span> {{$CUSTOMER_DETAILS->o_vat_registered_number}}
                @endif
                @else
                {{$CUSTOMER_DETAILS->c_title}} {{$CUSTOMER_DETAILS->c_name}} <br>
                {{$CUSTOMER_DETAILS->c_contact}}
                @endif
                @endif
            </td>
        </tr>
    </table>

    <div class="tbl">
        <table width="100%">
            <tr style="font-size: 12px; background-color:#cac8e3; color:#292561">
                <th>#</th>
                <th>ITEM DESCRIPTION</th>
                <th style="text-align: center;">UNIT PRICE (LKR)</th>
                <th style="text-align: center;">QTY</th>
                <th style="text-align: center;">PRICE (LKR)</th>
                <th style="text-align: center;">DIS.%</th>
                <th style="text-align: center;">TOTAL</th>
            </tr>

            @php
            $RATE = $INVOICE_DATA->in_vat_rate / 100;
            @endphp

            @foreach($INVOICE_ITEMS_DATA as $index => $data)
            <tr style="font-size: 12px;">
                <td>{{$index+1}}</td>
                <td>{{ strtoupper($data->p_name)}}</td>
                <td style="text-align: right;">{{number_format($data->ini_selling_price - ($data->ini_selling_price*$RATE),2)}}</td>
                <td style="text-align: center;">{{$data->ini_qty}}</td>
                <td style="text-align: right;">{{number_format((($data->ini_selling_price - ($data->ini_selling_price*$RATE)) * $data->ini_qty),2)}}</td>
                <td style="text-align: right;">{{$data->ini_discount_percentage}}</td>
                <td style="text-align: right;">{{number_format((($data->ini_final_price - ($data->ini_final_price*$RATE)) * $data->ini_qty),2)}}</td>
            </tr>
            @endforeach
        </table>
    </div>

    <table width="100%">
        <tr style="font-size: 12px;">
            <td width="50%"></td>
            <td>
                <div class="tbl">
                    <table width="100%">
                        <tr>
                            <th style="color:#292561">SUBTOTAL</th>
                            <td style="text-align: right;">{{number_format($INVOICE_DATA->in_sub_total - ($data->in_sub_total*$RATE),2)}}</td>
                        </tr>
                        <tr>
                            <th style="color:#292561">DISCOUNT (0%)</th>
                            <td style="text-align: right;"><small>({{$INVOICE_DATA->in_discount_percentage}}%)</small> {{number_format($INVOICE_DATA->in_discount_amount,2)}}</td>
                        </tr>
                        @if($INVOICE_DATA->in_returned_amount > 0)
                        <tr>
                            <th style="color:#292561">RETURNED</th>
                            <td style="text-align: right;">{{number_format($INVOICE_DATA->in_returned_amount,2)}}</td>
                        </tr>
                        @endif
                        <tr>
                            <th style="color:#292561">TOTAL PAYABLE</th>
                            <td style="text-align: right;">{{number_format($INVOICE_DATA->in_total_payable - ($data->in_total_payable*$RATE),2)}}</td>
                        </tr>
                        <tr>
                            <th style="color:#292561">VAT AMOUNT</th>
                            <td style="text-align: right;">{{number_format($data->in_total_payable*$RATE,2)}}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</div>