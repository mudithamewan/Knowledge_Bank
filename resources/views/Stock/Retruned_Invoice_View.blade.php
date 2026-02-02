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
            color: #f46a6a;
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
            border-top: 2px solid #24272b;
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
            color: #24272b;
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
            padding: 5px;
            text-align: left;
            font-size: 13px;
        }

        .tbl table th {
            background-color: #f46a6a;
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
            padding: 5px;
        }

        .invoice_content .total-box tr:nth-child(1) td {
            background-color: #f46a6a;
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
            <h2 style="font-weight: 600;">RETURNED INVOICE</h2>
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
            <p><strong>Invoice No:</strong> {{$RETURNED_INVOICE[0]->in_invoice_no}}</p>
            <p><strong>Returned Invoice No:</strong> {{$RETURNED_INVOICE[0]->ri_invoice_no}}</p>
            <p><strong>Returned Date:</strong> {{$RETURNED_INVOICE[0]->ri_inserted_date}}</p>
            <p><strong>Status:</strong> {!!$RETURNED_INVOICE[0]->ri_claim_status == 1 ? '<span class="badge badge-pill badge-soft-success font-size-11">CLAIMED</span>' : '<span class="badge badge-pill badge-soft-warning font-size-11">PENDING</span>'!!}</p>

        </div>

        <div class="tbl">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>RETURNED ITEM CODE</th>
                        <th>RETURNED ITEM</th>
                        <th>QTY</th>
                        <th>PRICE</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>

                    @php $tot = 0; @endphp
                    @foreach ($RETURNED_INVOICE as $index => $data)
                    @php $tot = $tot + ($data->rii_selling_amount * $data->rii_qty); @endphp
                    <tr>
                        <td>{{$index+1}}</td>
                        <td>{{$data->p_isbn}}</td>
                        <td>{{$data->p_name}}</td>
                        <td style="text-align: center;">{{$data->rii_qty}}</td>
                        <td style="text-align: right;">{{number_format($data->rii_selling_amount,2)}}</td>
                        <td style="text-align: right;">{{number_format($data->rii_selling_amount * $data->rii_qty,2)}}</td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>

        <div class="total-box">
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">{{number_format($tot,2)}}</td>
                </tr>
            </table>
        </div>

        <hr>

        <button type="button" class="btn btn-danger waves-effect btn-label waves-light w-lg" onclick="loadPrintInvoice(`{{url('/')}}/PrintReturnInvoice/{{urlencode(base64_encode($RETURNED_INVOICE[0]->ri_id))}}`)"><i class="bx bx-printer label-icon"></i> RETURNED INVOICE</button>

    </div>


</body>

</html>


<script>
    function loadPrintInvoice(pdfUrl) {
        // open modal
        // const modal = new bootstrap.Modal(document.getElementById('invoice_view2'));
        // modal.show();

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