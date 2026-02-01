<div class="row">
    <div class="col-xl-6">
        <table class="table table-sm">
            <tr>
                <th>GRN</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{$STOCK_IN_DETAILS->sil_grn}}</td>
            </tr>
            <tr>
                <th>WAREHOUSE</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{$STOCK_IN_DETAILS->mw_name}}</td>
            </tr>
            <tr>
                <th>INSERTED DATE</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{$STOCK_IN_DETAILS->sil_inserted_date}}</td>
            </tr>
            <tr>
                <th>INSERTED USER</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{$STOCK_IN_DETAILS->su_name}}</td>
            </tr>
            <tr>
                <th>UNIT PURCHASE AMT.</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{number_format($STOCK_IN_DETAILS->sil_purchase_amount,2)}}</td>
            </tr>
            <tr>
                <th>UNIT SELLING AMT.</th>
                <th><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></th>
                <td>{{number_format($STOCK_IN_DETAILS->sil_selling_amount,2)}}</td>
            </tr>
        </table>
    </div>
    <div class="col-xl-6">
        <div class="row">
            <div class="col-xl-6">
                <div class="border rounded p-3">
                    <center>
                        <p class="text-muted text-truncate mb-2">TOTAL ITEMS</p>
                        <h3 class="mb-0">{{$STOCK_IN_DETAILS->sil_total_items}}</h3>
                    </center>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="border rounded p-3">
                    <center>
                        <p class="text-muted text-truncate mb-2">TOTAL QTY</p>
                        <h3 class="mb-0">{{$STOCK_IN_DETAILS->sil_total_qty}}</h3>
                    </center>
                </div>
            </div>

            <div class="col-xl-12 mt-2">
                <div class="border rounded p-3">
                    <b>DOCUMENTS</b>
                    <div class="row mt-1">
                        @foreach($STOCK_IN_DOCUMENTS as $DOCUMENT)
                        <div class="col-auto">
                            <a href="{{url('/')}}/{{$DOCUMENT->silu_file_path}}" target="_blank">
                                <i class="bx bx-file"></i> {{$DOCUMENT->silu_file_name}}
                            </a>
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-12 mt-3">
        <div class="table-responsive">
            <table class="table table-sm">
                <tr class="table-light">
                    <th>#</th>
                    <th>PRODUCT CODE</th>
                    <th>PRODUCT NAME</th>
                    <th>ISBN</th>
                    <th>UNIT PURCHASE AMOUNT</th>
                    <th>UNIT SELLING AMOUNT</th>
                    <th>QTY</th>
                    <th style="text-align: center;"><i class="bx bx-dots-vertical-rounded"></i></th>
                </tr>
                @php
                $total_purchase_amount = 0;
                $total_selling_amount = 0;
                $total_qty = 0;
                @endphp
                @foreach($STOCK_IN_ITEM_DETAILS as $index => $DATA)
                @php
                $total_purchase_amount = $total_purchase_amount + $DATA->s_purchase_amount;
                $total_selling_amount = $total_selling_amount + $DATA->s_selling_amount;
                $total_qty = $total_qty + $DATA->s_qty;
                @endphp
                <tr>
                    <td>{{$index+1}}</td>
                    <td>{{str_pad($DATA->p_id, 5, '0', STR_PAD_LEFT)}}</td>
                    <td>{{$DATA->p_name}}</td>
                    <td>{{$DATA->p_isbn}}</td>
                    <td style="text-align: right;">{{number_format($DATA->s_purchase_amount,2)}}</td>
                    <td style="text-align: right;">{{number_format($DATA->s_selling_amount,2)}}</td>
                    <td style="text-align: right;">{{$DATA->s_qty}}</td>
                    <td style="text-align: right;">
                        <a href="{{url('/')}}/Product_Profile/{{urlencode(base64_encode($DATA->p_id))}}" class="btn btn-outline-primary btn-sm">PRODUCT VIEW</a>
                    </td>
                </tr>
                @endforeach
                <tr>
                    <th colspan="4"></th>
                    <th style="text-align: right; background-color:#eff2f7">{{number_format( $total_purchase_amount,2)}}</th>
                    <th style="text-align: right; background-color:#eff2f7">{{number_format( $total_selling_amount,2)}}</th>
                    <th style="text-align: right; background-color:#eff2f7">{{$total_qty}}</th>
                    <th style="text-align: right; background-color:#eff2f7"></th>
                </tr>
            </table>
        </div>
    </div>
</div>