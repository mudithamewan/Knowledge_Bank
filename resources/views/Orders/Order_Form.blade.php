<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            box-sizing: border-box;
        }

        @page {
            margin: 120px 30px 100px 30px;
        }

        body {
            margin: 0;
            padding: 0;
        }

        header {
            position: fixed;
            top: -100px;
            left: 0;
            right: 0;
            height: 100px;
            text-align: left;
        }

        .header-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 80px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 11px;
            line-height: 1.4;
        }

        footer .page:after {
            content: counter(page);
        }

        footer .pagecount:after {
            content: counter(pages);
        }

        main {
            margin-top: 10px;
        }

        .tbl table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .tbl th,
        .tbl td {
            text-align: left;
        }

        .tbl th {
            font-weight: bold;
            color: #282460;
        }

        .section-title {
            font-weight: bold;
            margin-top: 17px;
            background-color: #f0f0f0;
            padding: 5px;
            border-left: 2px solid #282460;
        }

        .totals {
            margin-top: 10px;
            font-weight: bold;
        }

        .totals p {
            margin: 2px 0;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header>
        <table width="100%" style="border-bottom:1px solid black;">
            <tr>
                <td width="50%">
                    <img src="{{ public_path('assets/company_logo.png') }}" alt="" width="60%">
                </td>
                <td width="50%" style="text-align: right;">
                    <div style="font-size: 15px; font-weight:bold; color:#282460">Knowledge Bank Publisher</div>
                    <div style="font-size: 10px; font-weight:bold; color:#282460">7A, Sethsiri Place, Pannipitiya</div>
                    <div style="font-size: 10px; color:#282460">Tel : 0712 100 111 , 0112 834 834 <br>E-mail :
                        knowledgebank2013@gmail.com
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2"></td>
            </tr>
        </table>
    </header>

    <!-- Footer -->
    <footer>
        <table width="100%">
            <tr>
                <td width="25%" style="font-size: 10px;"> {{ $IP }} @ {{ session('USER_NAME') }} <br></td>
                <td style="text-align: center;" width="50%">
                    <div style="font-size: 10px; font-weight:bold; color:#282460">Order Form</div>
                    <div style="font-size: 9px; color:#282460">Knowledge Bank Publisher</div>
                </td>
                <td width="25%" style="text-align: right; font-size: 10px;"> Page <span class="page"></span> of <span class="pagecount"></span></td>
            </tr>
        </table>
    </footer>

    <!-- Main -->
    <main>
        <table width="100%">
            <tr style="background-color: #282460;">
                <th style="text-align: center; font-size: 15px; color:white" colspan="7">ORDER FORM</th>
            </tr>
            <tr>
                <th style="text-align: left;" width="15%">ORDER ID</th>
                <th style="text-align: left;" width="2%">:</th>
                <td>{{ str_pad($ORDER->or_id, 5, '0', STR_PAD_LEFT) }}</td>
                <td></td>
                <th style="text-align: left;" width="15%">CREATED DATE</th>
                <th style="text-align: left;" width="2%">:</th>
                <td>{{ $ORDER->or_inserted_date }}</td>
            </tr>
            <tr>
                <th style="text-align: left;">CREATED BY</th>
                <th style="text-align: left;">:</th>
                <td>{{ $ORDER->su_name }}</td>
                <td></td>
                <th style="text-align: left;"></th>
                <th style="text-align: left;"></th>
                <td></td>
            </tr>
        </table>

        @php
        $currentGrade = null;
        $total_qty = 0;
        $total_amount = 0;
        @endphp

        @foreach($FULL_ARR as $ARR)

        <div style="border-bottom:1px solid #f69323; margin-top:20px; padding-bottom:5px; "> {{$ARR['WAREHOUSE']->mw_name}} - {{$ARR['WAREHOUSE']->mw_address}} </div>

        @foreach($ARR['ORDER_ITEM'] as $ITEM)

        @php
        $total_qty = $total_qty + $ITEM->os_qty;
        $total_amount = $total_amount + ($ITEM->os_selling_amount * $ITEM->os_qty);
        @endphp


        {{-- new grade block --}}
        @if($currentGrade !== $ITEM->grades)
        {{-- close previous --}}
        @if(!is_null($currentGrade))
        </tbody>
        </table>
        </div>
        @endif

        @php $currentGrade = $ITEM->grades; @endphp
        <div class="section-title">{{ strtoupper($currentGrade) }}</div>
        <div class="tbl">
            <table>
                <thead>
                    <tr style="background-color: #cac8e3;">
                        <th width="40%" style="padding-left: 5px;">Product</th>
                        <th width="14%" style="padding-left: 5px;">ISBN</th>
                        <th width="13.5%" style="padding-left: 5px; text-align: center;">Qty</th>
                        <th width="13.5%" style="padding-left: 5px; text-align: center;">Rate</th>
                        <th width="13.5%" style="padding-left: 5px; text-align: center;">Total</th>
                        <th width="13.5%" style="padding-left: 5px; text-align: center;">Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @endif

                    {{-- alternate row color --}}
                    @php
                    $row_color = $loop->even ? '#f8f9fa' : '#ffffff';
                    @endphp

                    @if($ITEM->os_qty > 0)
                    <tr style="background-color:<?= $row_color ?>;">
                        <td>{{ strtoupper($ITEM->p_name) }}</td>
                        <td>{{ $ITEM->p_isbn }}</td>
                        <td style="text-align: center;">{{ $ITEM->os_qty }}</td>
                        <td style="text-align: right;">{{ number_format($ITEM->os_selling_amount, 2) }}</td>
                        <td style="text-align: right;">{{ number_format($ITEM->os_selling_amount * $ITEM->os_qty, 2) }}</td>
                        <td style="text-align: right;">__________</td>
                    </tr>
                    @endif

                    {{-- close last table --}}
                    @if($loop->last)
                </tbody>
            </table>
        </div>
        @endif
        @endforeach

        @endforeach





        <table width="100%" style="margin-top:30px">
            <tr>
                <td width="50%"></td>
                <td>
                    <table width="100%">
                        <tr>
                            <th>Total Items</th>
                            <th>:</th>
                            <td style="text-align: right;">{{ $total_qty }}</td>
                            <td style="text-align: right;">__________</td>
                        </tr>
                        <tr>
                            <th>Order Total</th>
                            <th>:</th>
                            <td style="text-align: right;">{{ number_format($total_amount, 2) }}</td>
                            <td style="text-align: right;">__________</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

    </main>

</body>

</html>