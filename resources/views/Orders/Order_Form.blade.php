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
        }

        footer {
            position: fixed;
            bottom: -80px;
            left: 0;
            right: 0;
            height: 80px;
            border-top: 1px solid #000;
            font-size: 10px;
        }

        footer .page:after {
            content: counter(page);
        }

        footer .pagecount:after {
            content: counter(pages);
        }

        .section-title {
            font-weight: bold;
            margin-top: 18px;
            background-color: #f0f0f0;
            padding: 5px;
            border-left: 3px solid #282460;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            font-weight: bold;
            color: #282460;
        }

        .tbl th,
        .tbl td {
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>

<body>

    {{-- ================= HEADER ================= --}}
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

    {{-- ================= FOOTER ================= --}}
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

    {{-- ================= MAIN ================= --}}
    <main>

        {{-- Order Header --}}
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
        $total_qty = 0;
        $total_amount = 0;
        @endphp

        {{-- ================= ITEMS ================= --}}
        @foreach($FULL_ARR as $ARR)

        <div style="border-bottom:1px solid #f69323; margin-top:20px; padding-bottom:5px; "> {{$ARR['WAREHOUSE']->mw_name}} - {{$ARR['WAREHOUSE']->mw_address}} </div>

        @php
        $groupedItems = collect($ARR['ORDER_ITEM'])->groupBy('grades');
        @endphp

        @foreach($groupedItems as $grade => $items)

        <div class="section-title">{{ strtoupper($grade) }}</div>

        <div class="tbl">
            <table>
                <thead>
                    <tr style="background:#cac8e3;">
                        <th width="40%">Product</th>
                        <th width="14%">ISBN</th>
                        <th width="13.5%" style="text-align:center;">Qty</th>
                        <th width="13.5%" style="text-align:center;">Rate</th>
                        <th width="13.5%" style="text-align:center;">Total</th>
                        <th width="13.5%" style="text-align:center;">Remark</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach($items as $ITEM)
                    @if($ITEM->os_qty > 0)

                    @php
                    $total_qty += $ITEM->os_qty;
                    $lineTotal = $ITEM->os_qty * $ITEM->os_selling_amount;
                    $total_amount += $lineTotal;
                    @endphp

                    <tr>
                        <td>{{ strtoupper($ITEM->p_name) }}</td>
                        <td>{{ $ITEM->p_isbn }}</td>
                        <td style="text-align:center;">{{ $ITEM->os_qty }}</td>
                        <td style="text-align:right;">{{ number_format($ITEM->os_selling_amount, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($lineTotal, 2) }}</td>
                        <td style="text-align: right;">__________</td>
                    </tr>

                    @endif
                    @endforeach

                </tbody>
            </table>
        </div>

        @endforeach

        @endforeach

        {{-- ================= TOTALS ================= --}}
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