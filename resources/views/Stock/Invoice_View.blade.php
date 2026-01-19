<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /* =============================
           GLOBAL CONTAINER STYLES
        ============================== */
        .invoice_content * {
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }

        .invoice_content {
            margin: 0;
            padding: 30px;
            color: #333;
        }

        .invoice_content h2 {
            color: #3A47D5;
            margin-bottom: 5px;
        }

        .invoice_content .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .invoice_content .company-details {
            text-align: right;
            font-size: 13px;
        }

        .invoice_content .company-details strong {
            font-size: 15px;
        }

        .invoice_content hr {
            border: none;
            border-top: 2px solid #3A47D5;
            margin: 20px 0;
        }

        .invoice_content .invoice-info {
            margin-bottom: 20px;
        }

        .invoice_content .invoice-info p {
            margin: 4px 0;
            font-size: 13px;
        }

        .invoice_content .invoice-info strong {
            color: #3A47D5;
        }

        .invoice_content .billed-to {
            margin-top: 5px;
            font-size: 13px;
        }

        /* =============================
           TABLE STYLES (scoped to .tbl)
        ============================== */
        .tbl table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .tbl table th,
        .tbl table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }

        .tbl table th {
            background-color: #3A47D5;
            color: white;
            font-weight: 600;
        }

        .tbl table td {
            vertical-align: top;
        }

        .tbl .text-right {
            text-align: right;
        }

        /* =============================
           TOTAL BOX STYLES
        ============================== */
        .invoice_content .total-box {
            width: 250px;
            margin-left: auto;
            margin-top: 20px;
            font-size: 13px;
            border: 1px solid #ddd;
        }

        .invoice_content .total-box table {
            border: none;
            width: 100%;
        }

        .invoice_content .total-box td {
            border: none;
            padding: 8px 10px;
        }

        .invoice_content .total-box tr:nth-child(3) td {
            background-color: #3A47D5;
            color: #fff;
            font-weight: bold;
        }

        .invoice_content .text-right {
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="invoice_content">
        <div class="header">
            <h2 style="font-weight: 600;">INVOICE</h2>
            <div class="company-details">
                <strong>Knowledge Bank Publisher</strong><br>
                VAT No: 102274292 - 7000<br>
                No 7A Sethsiri Place, Pannipitiya, Sri Lanka<br>
                Email: knowledgebank2013@gmail.com<br>
                Hot Line: 0712 100 111 / 075 5100 111
            </div>
        </div>

        <hr>

        <div class="invoice-info">
            <p><strong>Invoice No:</strong> {{$INVOICE_DATA->in_invoice_no}}</p>
            <p><strong>Date:</strong> {{$INVOICE_DATA->in_inserted_date}}</p>
            <p><strong>Payment Method:</strong> {{$INVOICE_DATA->mpt_name}}</p>

            @if(!empty($CUSTOMER_DETAILS))
            <div class="billed-to">
                <strong>Billed To:</strong>
                @if(isset($CUSTOMER_DETAILS->o_id))
                {{$CUSTOMER_DETAILS->o_name}}
                {{$CUSTOMER_DETAILS->o_address}}
                @else
                {{$CUSTOMER_DETAILS->c_title}} {{$CUSTOMER_DETAILS->c_name}}
                {{$CUSTOMER_DETAILS->c_address}}
                @endif
            </div>
            @endif
        </div>

        <div class="tbl">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ITEM DESCRIPTION</th>
                        <th>UNIT PRICE (LKR)</th>
                        <th>QTY</th>

                        @if($INVOICE_DATA->in_is_returned == 1 || $INVOICE_DATA->in_is_partial_returned == 1)
                        <th>RETURNED QTY</th>
                        @endif

                        <th>PRICE (LKR)</th>
                        <th>DIS.%</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($INVOICE_ITEMS_DATA as $index => $data)
                    <tr>
                        <td>{{$index+1}}
                            @if($data->ini_is_returned == 1)
                            <span class="badge badge-pill badge-soft-danger font-size-5">RETURNED</span>
                            @endif
                        </td>
                        <td>{{$data->p_name}}</td>
                        <td class="text-right">{{number_format($data->ini_selling_price,2)}}</td>
                        <td class="text-right">{{$data->ini_qty}}</td>

                        @if($INVOICE_DATA->in_is_returned == 1 || $INVOICE_DATA->in_is_partial_returned == 1)
                        <td class="text-right">{{$data->ini_returned_qty}}</td>
                        @endif

                        <td class="text-right">{{number_format(($data->ini_selling_price * $data->ini_qty),2)}}</td>
                        <td class="text-right">{{$data->ini_discount_percentage}}</td>
                        <td class="text-right">{{number_format(($data->ini_final_price * $data->ini_qty),2)}}</td>

                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <div class="total-box">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{number_format($INVOICE_DATA->in_sub_total,2)}}</td>
                </tr>
                <tr>
                    <td>Discount (0%)</td>
                    <td class="text-right"><small>({{$INVOICE_DATA->in_discount_percentage}}%)</small> {{number_format($INVOICE_DATA->in_discount_amount,2)}}</td>
                </tr>
                <tr>
                    <td>Total Payable</td>
                    <td class="text-right">{{number_format($INVOICE_DATA->in_total_payable,2)}}</td>
                </tr>
                <tr>
                    <td>Paid</td>
                    <td class="text-right">{{number_format($INVOICE_DATA->in_total_paid_amount,2)}}</td>
                </tr>
                <tr>
                    <td>Balance</td>
                    <td class="text-right">{{number_format($INVOICE_DATA->in_total_balance,2)}}</td>
                </tr>
            </table>
        </div>

        <hr>
        <button type="button" class="btn btn-success waves-effect btn-label waves-light w-lg" onclick="loadPrintInvoice2(`{{url('/')}}/PrintInvoice/{{urlencode(base64_encode($INVOICE_DATA->in_id))}}`)"><i class="bx bx-printer label-icon"></i> PRINT</button>
        <a href="{{url('/')}}/Normal_Invoice/{{urlencode(base64_encode($INVOICE_DATA->in_id))}}" class="btn btn-success waves-effect btn-label waves-light w-lg"><i class="mdi mdi-download label-icon"></i> INVOICE</a>
        <a href="{{url('/')}}/VAT_Invoice/{{urlencode(base64_encode($INVOICE_DATA->in_id))}}" class="btn btn-success waves-effect btn-label waves-light w-lg"><i class="bx bx-receipt label-icon"></i> VAT INVOICE</a>

    </div>


</body>

</html>


<script>
    function loadPrintInvoice(pdfUrl) {
        // open modal
        const modal = new bootstrap.Modal(document.getElementById('invoice_view2'));
        modal.show();

        // hide any previous view
        const pdfFrame = document.getElementById('pdfFrame');
        pdfFrame.style.display = 'block';
        pdfFrame.src = pdfUrl;

        // wait till PDF loads, then print
        pdfFrame.onload = function() {
            pdfFrame.contentWindow.focus();
            pdfFrame.contentWindow.print();
        };
    }
</script>