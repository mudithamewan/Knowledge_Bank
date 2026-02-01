<style>
    .invoice_table table,
    td,
    th {
        border: 1px solid;
        padding: 5px;
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
    }

    .invoice_table table {
        border-collapse: collapse;
    }
</style>


<div class="invoice_table">
    <table width="20%" style="margin: 0 auto; text-align: center;">
        <tr>
            <th>Tax Invoice</th>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="50%"><b>Date of Invoice:</b> {{$INVOICE_DATA->in_inserted_date}}</td>
            <td width="50%"><b>Tax Invoice No:</b> {{$INVOICE_DATA->in_invoice_no}}</td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="50%">
                <table style="border: none; padding:0px;">
                    <tr>
                        <td style="border: none;">
                            <b>Supplier's TIN:</b> 102274292 - 7000 <br>
                            <b>Supplier's Name:</b> Knowledge Bank Publisher <br>
                            <b>Address:</b> No 7A Sethsiri Place, Pannipitiya, Sri Lanka <br>
                            <b>Telephone No.:</b> 0712 100 111 / 075 5100 111 <br>
                        </td>
                        <td style="border: none;" width="20%"><img src="{{public_path('assets/company_logo_only.png')}}" alt="" width="100%"></td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                @if(!empty($CUSTOMER_DETAILS))

                @if(isset($CUSTOMER_DETAILS->o_id))
                @if($CUSTOMER_DETAILS->o_is_vat_registered == 1)
                <b>Purchaser's TIN:</b> {{$INVOICE_DATA->o_vat_registered_number}} <br>
                @endif
                <b>Purchaser's Name:</b> {{$CUSTOMER_DETAILS->o_name}} <br>
                <b>Address:</b> {{$CUSTOMER_DETAILS->o_address}} <br>
                <b>Telephone No:</b> {{$CUSTOMER_DETAILS->o_contact}} <br>

                @else

                <b>Purchaser's Name:</b> {{$CUSTOMER_DETAILS->c_title}} {{$CUSTOMER_DETAILS->c_name}} <br>
                <b>Address:</b> {{$CUSTOMER_DETAILS->c_address}} <br>
                <b>Telephone No:</b> {{$CUSTOMER_DETAILS->c_contact}} <br>

                @endif
                @endif
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <td width="50%"><b>Date of Delivery:</b> {{$INVOICE_DATA->in_inserted_date}}</td>
            <td width="50%"><b>Place of Supply:</b> {{$INVOICE_DATA->mw_address}}</td>
        </tr>
        <tr>
            <td colspan="2">
                <b>Additional Information If Any:</b>
            </td>
        </tr>
    </table>

    <table width="100%">
        <tr>
            <th>Reference</th>
            <th>Description of Goods or Services</th>
            <th>Quantity</th>
            <th>Unit Price</th>
            <th>Amount Excluding VAT (Rs.)</th>
        </tr>
        @php
        $RATE = $INVOICE_DATA->in_vat_rate / 100;
        @endphp

        @foreach($INVOICE_ITEMS_DATA as $index => $data)
        <tr>
            <td>{{strtoupper($data->p_isbn)}}</td>
            <td>{{strtoupper($data->p_name)}}</td>
            <td style="text-align: center;">{{strtoupper($data->ini_qty)}}</td>
            <td style="text-align: right;">{{number_format((($data->ini_selling_price - ($data->ini_selling_price*$RATE)) * $data->ini_qty),2)}}</td>
            <td style="text-align: right;">{{number_format((($data->ini_final_price - ($data->ini_final_price*$RATE)) * $data->ini_qty),2)}}</td>
        </tr>
        @endforeach
    </table>

    <table width="100%">
        <tr>
            <th style="text-align: left;">Total Value of Supply:</th>
            <td style="text-align: right;">{{number_format($INVOICE_DATA->in_sub_total - ($data->in_sub_total*$RATE),2)}}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Discount {{$INVOICE_DATA->in_discount_percentage}}%:</th>
            <td style="text-align: right;">{{number_format($INVOICE_DATA->in_discount_amount,2)}}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Gross Amount:</th>
            <td style="text-align: right;">{{number_format(($INVOICE_DATA->in_sub_total - ($data->in_sub_total*$RATE)) - $INVOICE_DATA->in_discount_amount,2)}}</td>
        </tr>
        <tr>
            <th style="text-align: left;">VAT Amount (Total Value of Supply @ {{$INVOICE_DATA->in_vat_rate}}%)</th>
            <td style="text-align: right;">{{number_format(($data->in_total_payable + $INVOICE_DATA->in_discount_amount)*$RATE,2)}}</td>
        </tr>
        <tr>
            <th style="text-align: left;">Total Amount including VAT (Net Amount):</th>
            <td style="text-align: right;">{{number_format(($INVOICE_DATA->in_total_payable),2)}}</td>
        </tr>
    </table>


    <table width="100%">
        <tr>
            <td><b>Total Amount in words:</b>
                @php
                function numberToRupees($number)
                {
                $ones = [
                '', 'one', 'two', 'three', 'four', 'five',
                'six', 'seven', 'eight', 'nine', 'ten',
                'eleven', 'twelve', 'thirteen', 'fourteen',
                'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
                ];

                $tens = [
                '', '', 'twenty', 'thirty', 'forty',
                'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
                ];

                $num = (int) floor($number);

                if ($num === 0) {
                return 'Zero rupees only';
                }

                $words = '';

                // Thousands
                if ($num >= 1000) {
                $words .= $ones[intdiv($num, 1000)] . ' thousand ';
                $num %= 1000;
                }

                // Hundreds
                if ($num >= 100) {
                $words .= $ones[intdiv($num, 100)] . ' hundred ';
                $num %= 100;
                }

                // Tens
                if ($num >= 20) {
                $words .= $tens[intdiv($num, 10)] . ' ';
                $num %= 10;
                }

                // Ones
                if ($num > 0) {
                $words .= $ones[$num] . ' ';
                }

                return ucfirst(trim($words)) . ' rupees only';
                }
                @endphp

                {{ numberToRupees($INVOICE_DATA->in_total_payable) }}


            </td>
        </tr>
        <tr>
            <td><b>Mode of Payment:</b> {{$INVOICE_DATA->mpt_name}}</td>
        </tr>
    </table>

    <br><br><br>
    <table style="border: none;" width="100%">
        <tr>
            <td style="text-align: center; border: none;" width="33.333333%">
                _____________________________ <br>
                Prepared By
            </td>
            <td style="text-align: center; border: none;" width="33.333333%">
                _____________________________ <br>
                Issued By
            </td>
            <td style="text-align: center; border: none;" width="33.333333%">
                _____________________________ <br>
                Received By
            </td>
        </tr>
        <tr>
            <td colspan="3" style="border: none;">
                All cheques should be crossed and drawn in favour of Knowledge Bank Publisher <br>
                Check whether the books you have received tally with the invoice if not, inform within 07 days. <br>
                The goods sold cannot be returned.
            </td>
        </tr>
    </table>

</div>