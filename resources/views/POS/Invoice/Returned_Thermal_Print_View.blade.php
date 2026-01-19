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
                <div style="font-size: 7px">Retruned Invoice No: {{$INVOICE_DATA->in_invoice_no}}</div>
                <div style="font-size: 7px">Date: {{$RETURNED_INVOICE_DATA->in_inserted_date}}</div>
            </td>
        </tr>
    </table>




    <table>
        <tr style="border-top: 0.3 solid #000;">
            <td class="bold" style="font-size: 7px; padding-top:2px;">RETURNED AMOUNT</td>
            <td class="bold" style="text-align:right; font-size: 7px; padding-top:2px;">{{number_format($RETURNED_INVOICE_DATA->ri_amount,2)}}</td>
        </tr>
    </table>

    <hr>

    <div class="center bold" style="font-size: 8px">THANK YOU, COME AGAIN!</div>

    <hr>
</body>

</html>