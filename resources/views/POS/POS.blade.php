<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <style>
        /* Active row highlight */
        #productList .list-group-item.active {
            background-color: #eff2f7 !important;
            color: #495057 !important;
        }

        /* Remove Bootstrap default focus border */
        #productList .list-group-item:focus,
        #productList .list-group-item-action:focus,
        #productList .list-group-item-action.active {
            outline: none !important;
            box-shadow: none !important;
            border-color: transparent !important;
        }

        /* Hover effect */
        #productList .list-group-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
    </style>
</head>

<body data-sidebar="dark">

    <div id="layout-wrapper">
        @include('Layout/header')
        @include('Layout/sideMenu')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Stock Out (POS)</h4>

                                <div class="row align-items-center">

                                    <div class="col-auto">
                                        <div id="return_modal" class="modal fade" tabindex="-1" aria-labelledby="#convert_voucher_modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-fullscreen">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="calculate_modalLabel">RETURN VIEW</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body" style="background-color: #f8f8fb;">
                                                        <span id="LOAD_RETURN_VIEW" class="VIEW_EMPTY"></span>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary waves-effect" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn-danger waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#return_modal" onclick="ajax_action(`{{url('/')}}/load_return_view`,`LOAD_RETURN`,`{{json_encode(['MW_ID' =>session('POS_WAREHOUSE')])}}`,`{{csrf_token()}}`)">
                                            <i class="bx bx-rotate-left label-icon"></i>
                                            Return Invoice
                                        </button>
                                    </div>

                                    <div class="col-auto">
                                        <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#change_warehouse_modal" onclick="remove_set_warehouse_form()">
                                            <i class="bx bx-edit label-icon"></i>
                                            <span id="warehouse_name"></span>
                                        </button>
                                    </div>

                                </div>


                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <!-- Change Warehouse Modal -->
                    <div class="modal fade" id="change_warehouse_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-sm modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">CHANGE WAREHOUSE</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <label for="">WAREHOUSE <span class="text-danger">*</span></label>
                                            <select name="WAREHOUSE" id="WAREHOUSE" class="form-select">
                                                <option value="0||NO SELECTED||0" selected hidden>No Selected</option>
                                                @foreach($WAREHOUSES as $item)

                                                @if($item->mw_id == session('POS_WAREHOUSE'))
                                                <option value="{{$item->mw_id}}||{{$item->mw_name}}||{{$item->mw_mwt_id}}" selected>{{$item->mw_name}}</option>
                                                @else
                                                <option value="{{$item->mw_id}}||{{$item->mw_name}}||{{$item->mw_mwt_id}}">{{$item->mw_name}}</option>
                                                @endif

                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <button class="btn btn-primary w-100" onclick="changewarehouse()" id="change_warehouse_btn">CHANGE</button>
                                        </div>
                                        <div class="col-xl-12">
                                            <div id="set_warehouse_form"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
                            </div>
                        </div>
                    </div>

                    @if(empty(session('POS_WAREHOUSE')))
                    <script>
                        document.addEventListener("DOMContentLoaded", () => {
                            const modal = new bootstrap.Modal(document.getElementById('change_warehouse_modal'));
                            modal.show();
                        });
                    </script>
                    @endif

                    <!-- Customer Modal -->
                    <div class="modal fade" id="customer_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">CUSTOMER</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div id="LOAD_CUSTOMER_VIEW"></div>
                                </div>
                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- LEFT: SEARCH -->
                        <div class="col-xl-5" style="height: 82vh; overflow-y: auto;">

                            <div class="card" id="customer_view_area" style="display: none;">
                                <div class="card-body">

                                    <div class="d-flex">
                                        <div class="flex-shrink-0 me-3">
                                            <img src="{{url('/')}}/assets/images/man.jpg" alt="" class="rounded-circle avatar-sm">
                                        </div>
                                        <div class="flex-grow-1 align-self-center">
                                            <div class="text-muted">
                                                <h5 class="mb-1" id="cus_name"></h5>
                                                <p class="mb-0" id="cus_title"></p>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="cus_order_view"></div>

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title text-primary mb-0">SEARCH PRODUCTS</h5>
                                </div>

                                <div class="card-body">
                                    <!-- Search Input -->
                                    <div class="input-group mb-3">
                                        <input type="text" id="productSearch" class="form-control" placeholder="Search by Name, Barcode, or Product Code">
                                        <button class="btn btn-outline-primary" type="button">Search</button>
                                    </div>

                                    <!-- Product List (dynamic) -->
                                    <div class="list-group" id="productList" style="max-height: 400px; overflow-y: auto;">
                                        <div class="text-muted text-center py-3" id="noProducts">
                                            <i class="bx bx-search"></i> Start typing to search products...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: SELECTED ITEMS -->
                        <div class="col-xl-7">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="row">
                                        <div class="col-9 d-flex align-items-center ">
                                            <h5 class="card-title text-primary mb-0">SELECTED ITEMS</h5>
                                        </div>
                                        <div class="col-3" style="text-align: right;">
                                            <button onclick="ajax_action('<?= url('/') ?>/load_customer_view', 'LOAD_CUSTOMER', '', '{{ csrf_token() }}')" class="btn btn-secondary btn-sm waves-effect btn-label waves-light w-lg" data-bs-toggle="modal" data-bs-target="#customer_modal">
                                                <i class="bx bx-user label-icon"></i> CUSTOMER
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <input type="hidden" name="MW_ID" id="MW_ID" value="{{session('POS_WAREHOUSE')}}">
                                    <input type="hidden" name="CUS_ID" id="CUS_ID">
                                    <input type="hidden" name="IS_CORPARATE" id="IS_CORPARATE" value="0">

                                    <table class="table table-bordered table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="30%">Item</th>
                                                <th width="15%" style="text-align:center">Selling Price</th>
                                                <th width="15%" style="text-align:center">Qty</th>
                                                <th width="15%" style="text-align:center">Discount (%)</th>
                                                <th width="15%" style="text-align:center">Total</th>
                                                <th width="5%" style="text-align:center"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedItems"></tbody>
                                    </table>

                                    <div class="row">
                                        <div class="col-xl-4"></div>
                                        <div class="col-xl-8">
                                            <table width="100%">
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Total Items</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%"><span class="badge bg-primary" id="itemCount">0</span></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Item Discount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%"><span class="text-danger" id="item_discount">0.00</span></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Sub Total</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%"><span class="text-primary" id="sub_total">0.00</span></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Discount (%)</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <input type="text" class="form-control text-end form-control-sm input-mask text-start" id="DISCOUNT_PERCENTAGE" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'" value="0.00">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Discount Amount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <input type="text" class="form-control text-end form-control-sm input-mask text-start" id="DISCOUNT_AMOUNT" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0.00'" value="0.00">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Returned Invoice</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <span class="text-primary" id="returned_invoice_amount">0.00</span>
                                                        <input type="hidden" id="RETURNED_INVOICE_AMOUNT" value="0">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">
                                                        <span style="font-size: 20px;">Total Payable</span>
                                                    </th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <span style="font-size: 20px;" class="text-success" id="grandTotal">0.00</span>
                                                    </th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="mt-3 mb-2" style="border-top:5px solid #eff2f7"></div>
                                        </div>
                                        <div class="col-xl-4"></div>
                                        <div class="col-xl-8">

                                            <table width="100%" class="mt-3">
                                                <tr>
                                                    <th colspan="3" style="text-align: right;">
                                                        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                                            @foreach($PAYMENT_TYPES as $index => $TYPE)
                                                            <input type="radio" class="btn-check" name="MPT_ID" id="MPT_ID{{$TYPE->mpt_id}}" value="{{$TYPE->mpt_id}}" autocomplete="off">
                                                            <label class="btn btn-outline-secondary" for="MPT_ID{{$TYPE->mpt_id}}">{{strtoupper($TYPE->mpt_name)}}</label>
                                                            @endforeach
                                                        </div>
                                                    </th>
                                                </tr>
                                            </table>

                                            <table width="100%" class="mt-1">
                                                <tr id="CASH_AMOUNT_ROW" style="display: none;">
                                                    <th style="text-align: right;" class="p-1">Cash Amount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <input type="text" class="form-control text-end form-control-sm input-mask text-start" id="CASH_PAID" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'">
                                                    </th>
                                                </tr>
                                                <tr id="CARD_AMOUNT_ROW" style="display: none;">
                                                    <th style="text-align: right;" class="p-1">Card Amount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <input type="text" class="form-control text-end form-control-sm input-mask text-start" id="CARD_PAID" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'">
                                                    </th>
                                                </tr>
                                                <tr id="OTHER_AMOUNT_ROW" style="display: none;">
                                                    <th style="text-align: right;" class="p-1">Paid Amount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <input type="text" class="form-control text-end form-control-sm input-mask text-start" id="OTHER_AMOUNT" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Paid Amount</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <span class="text-primary" id="TOTAL_PAID_AMOUNT_TEXT">0.00</span>
                                                        <input type="hidden" name="TOTAL_PAID_AMOUNT" id="TOTAL_PAID_AMOUNT" value="0">
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Balance</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%">
                                                        <span class="text-danger" id="BALANCE">0.00</span>
                                                        <input type="hidden" name="BALANCE_AMOUNT" id="BALANCE_AMOUNT" value="0">
                                                    </th>
                                                </tr>
                                            </table>


                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-end" id="save_btn">
                                    <button id="saveBtn" class="btn btn-primary w-lg mt-2 mb-2">COMPLETE</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @include('Layout/footer')

        </div>
    </div>

    <div class="modal fade" id="different_prices_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">DIFFRENT PRICES</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="LOAD_DIFFRENT_PRICES_VIEW"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoice_view2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">INVOICE</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="pdfFrame" src="" width="100%" height="700px" style="border:none; display:none;"></iframe>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>


    <script>
        function loadPrintInvoice(pdfUrl) {
            // const modal = new bootstrap.Modal(document.getElementById('invoice_view2'));
            // modal.show();

            const pdfFrame = document.getElementById('pdfFrame');
            pdfFrame.style.display = 'block';
            pdfFrame.src = pdfUrl;

            pdfFrame.onload = function() {
                pdfFrame.contentWindow.focus();
                pdfFrame.contentWindow.print();
            };
        }

        // ============ RESET POS AFTER PRINT ============ //
        function resetPOS() {
            // clear items table
            $("#selectedItems").empty();

            // reset totals
            $("#itemCount").text(0);
            $("#item_discount").text("0.00");
            $("#sub_total").text("0.00");
            $("#grandTotal").text("0.00");

            // reset discounts
            $("#DISCOUNT_PERCENTAGE").val("0");
            $("#DISCOUNT_AMOUNT").val("0");

            // reset payment types & amounts
            $("input[name='MPT_ID']").prop("checked", false);
            $("#CASH_PAID").val("0");
            $("#CARD_PAID").val("0");
            $("#OTHER_AMOUNT").val("0");
            $("#TOTAL_PAID_AMOUNT_TEXT").text("0.00");
            $("#TOTAL_PAID_AMOUNT").val("0");
            $("#BALANCE").text("0.00");

            // reset customer
            $("#CUS_ID").val("");
            $("#IS_CORPARATE").val("0");
            $("#customer_view_area").hide();
            $("#cus_name").text("");
            $("#cus_title").text("");
            $("#cus_order_view").html("");

            // reset search
            resetSearch(true);

            // recalc payment totals safely
            updatePaymentTotals();
        }

        // ========================= GLOBAL INITIALIZATION ========================= //
        $(document).ready(function() {
            initWarehouse();
            initProductSearch();
            initListeners();
            initKeyboardShortcuts();
            fiill_total();
        });

        // ========================= WAREHOUSE HANDLING ========================= //
        function initWarehouse() {
            const warehouse = $("#WAREHOUSE").val();
            if (!warehouse) return;
            const [mw_id, mw_name] = warehouse.split("||");
            $("#MW_ID").val(mw_id);
            $("#warehouse_name").text(mw_name);
        }

        function remove_set_warehouse_form() {
            $('#set_warehouse_form').html('');
        }

        function changewarehouse() {
            $('#set_warehouse_form').html('');
            const $btn = $("#change_warehouse_btn");
            $btn.html('<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> CHANGING..');

            const [mw_id, mw_name, mw_mwt_id] = $("#WAREHOUSE").val().split("||");

            $.ajax({
                url: "/set_warehouse_session",
                method: "POST",
                dataType: "json",
                data: {
                    mw_id: mw_id,
                    _token: '{{csrf_token()}}'
                },
                success: function(response) {
                    $btn.html('CHANGE');

                    if (response.success) {
                        $("#MW_ID").val(mw_id);
                        $("#warehouse_name").text(mw_name);

                        setTimeout(() => {
                            $btn.text("CHANGED");
                            $("#change_warehouse_modal .btn[data-bs-dismiss='modal']").click();
                        }, 400);
                    }
                    if (response.result) {
                        $('#set_warehouse_form').html(response.result);
                    }

                    if (response.error) {
                        Swal.fire("Error!", response.error, "error");
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire("Error!", "Something went wrong while saving.", "error");
                    resetSaveButton();
                }
            });
        }

        // ========================= PAYMENT TYPE & AMOUNTS ========================= //
        function fiill_total() {
            const grandTotal = parseFloat($("#grandTotal").text()) || 0;
            const MPT_ID = $("input[name='MPT_ID']:checked").val();

            $("#CASH_AMOUNT_ROW, #CARD_AMOUNT_ROW, #OTHER_AMOUNT_ROW").hide();

            if (MPT_ID !== "1") $("#CASH_PAID").val("0");
            if (MPT_ID !== "2") $("#CARD_PAID").val("0");
            if (MPT_ID !== "4") $("#OTHER_AMOUNT").val("0");

            if (MPT_ID === "1") {
                $("#CASH_AMOUNT_ROW").show();
                $("#CASH_PAID").val(grandTotal.toFixed(2));
            } else if (MPT_ID === "2") {
                $("#CARD_AMOUNT_ROW").show();
                $("#CARD_PAID").val(grandTotal.toFixed(2));
            } else if (MPT_ID === "3") {
                $("#CASH_AMOUNT_ROW, #CARD_AMOUNT_ROW").show();
            } else if (MPT_ID) {
                $("#OTHER_AMOUNT_ROW").show();
                $("#OTHER_AMOUNT").val(grandTotal.toFixed(2));
            }

            updatePaymentTotals();
        }

        function updatePaymentTotals() {
            const grandTotal = parseFloat($("#grandTotal").text()) || 0;

            const cashPaid = parseFloat($("#CASH_PAID").val()) || 0;
            const cardPaid = parseFloat($("#CARD_PAID").val()) || 0;
            const otherPaid = parseFloat($("#OTHER_AMOUNT").val()) || 0;

            const totalPaid = cashPaid + cardPaid + otherPaid;
            const balance = totalPaid - grandTotal; // 👈 REAL balance

            $("#TOTAL_PAID_AMOUNT_TEXT").text(totalPaid.toFixed(2));
            $("#TOTAL_PAID_AMOUNT").val(totalPaid.toFixed(2));

            // Styling vibes 😎
            if (balance > 0) {
                $("#BALANCE").text(balance.toFixed(2)).removeClass("text-danger").addClass("text-success");
                $("#BALANCE_AMOUNT").val(balance.toFixed(2));
            } else if (balance < 0) {
                $("#BALANCE").text(balance.toFixed(2)).removeClass("text-success").addClass("text-danger");
                $("#BALANCE_AMOUNT").val(balance.toFixed(2));
            } else {
                $("#BALANCE").text("0.00").removeClass("text-success text-danger");
                $("#BALANCE_AMOUNT").val(0);
            }
        }

        // ========================= ITEM CALCULATIONS ========================= //
        function updateTotals() {
            let totalItems = 0,
                itemDiscount = 0,
                subTotal = 0;

            $("#selectedItems tr").each(function() {
                const $row = $(this);
                const qty = parseFloat($row.find(".qty").val()) || 0;
                const price = parseFloat($row.find(".selling-price").text()) || 0;
                const discount = Math.min(Math.max(parseFloat($row.find(".discount").val()) || 0, 0), 100);

                const rowSub = qty * price;
                const rowDisc = (rowSub * discount) / 100;
                const rowTotal = rowSub - rowDisc;

                $row.find(".row-total").text(rowTotal.toFixed(2));
                totalItems += qty;
                itemDiscount += rowDisc;
                subTotal += rowTotal;
            });

            const discPercent = parseFloat($("#DISCOUNT_PERCENTAGE").val()) || 0;
            const discAmount = parseFloat($("#DISCOUNT_AMOUNT").val()) || 0;
            const returned_invoice_amount = parseFloat($("#RETURNED_INVOICE_AMOUNT").val()) || 0;

            let overallDisc = 0;
            if ($("#DISCOUNT_PERCENTAGE").is(":focus")) {
                overallDisc = (subTotal * discPercent) / 100;
                $("#DISCOUNT_AMOUNT").val(overallDisc.toFixed(2));
            } else if ($("#DISCOUNT_AMOUNT").is(":focus")) {
                overallDisc = discAmount;
                if (subTotal > 0) {
                    $("#DISCOUNT_PERCENTAGE").val(((overallDisc / subTotal) * 100).toFixed(2));
                }
            } else {
                overallDisc = discPercent > 0 ? (subTotal * discPercent) / 100 : discAmount;
                $("#DISCOUNT_AMOUNT").val(overallDisc.toFixed(2));
            }

            const grandTotal = subTotal - (overallDisc + returned_invoice_amount);
            $("#itemCount").text(totalItems);
            $("#item_discount").text(itemDiscount.toFixed(2));
            $("#sub_total").text(subTotal.toFixed(2));
            $("#grandTotal").text(grandTotal.toFixed(2));

            updatePaymentTotals();
        }

        // ========================= PRODUCT HANDLING ========================= //
        $(document).on("click", ".add-btn", function() {
            const $btn = $(this);
            const id = $btn.data("id");
            const name = $btn.data("name");
            const code = $btn.data("code");
            const price = parseFloat($btn.data("as_selling_price")) || 0;
            const maxQty = $btn.data("as_available_qty");

            const $existing = $(`#selectedItems tr[data-id='${id}']`);
            let $qtyInput;

            if ($existing.length) {
                $qtyInput = $existing.find(".qty");
                $qtyInput.val((parseFloat($qtyInput.val()) || 0) + 1);
                updateTotals();
            } else {
                const row = `
            <tr data-id="${id}">
                <td>${name}<br><small class="text-muted">Code: ${code}</small></td>
                <td style="text-align:right" class="selling-price">${price.toFixed(2)}</td>
                <td><input type="number" class="form-control form-control-sm text-end qty" value="1" min="1" max="${maxQty}"></td>
                <td><input type="text" class="form-control form-control-sm text-end discount" placeholder="0.00"></td>
                <td class="row-total text-end">${price.toFixed(2)}</td>
                <td class="text-center"><button class="btn btn-sm btn-danger remove-btn"><i class="bx bx-trash"></i></button></td>
            </tr>`;
                $("#selectedItems").append(row);
                updateTotals();
                $qtyInput = $("#selectedItems tr").last().find(".qty");
            }

            if ($qtyInput && $qtyInput.length) {
                $qtyInput.focus().select();
            }

            const modal = bootstrap.Modal.getInstance(document.getElementById("different_prices_modal"));
            if (modal) modal.hide();
            resetSearch(false);
        });

        $(document).on("click", ".remove-btn", function() {
            $(this).closest("tr").remove();
            updateTotals();
        });

        function resetSearch(focus = true) {
            $("#productList").html('<div class="text-muted text-center py-3"><i class="bx bx-search"></i> Start typing to search products...</div>');
            $("#productSearch").val("");
            if (focus) {
                $("#productSearch").focus();
            }
        }

        // ========================= PRODUCT SEARCH ========================= //
        function initProductSearch() {
            let currentIndex = -1;

            $("#productSearch").on("keyup", function(e) {
                const query = $(this).val().trim();
                if (["ArrowUp", "ArrowDown", "Enter"].includes(e.key)) return;

                if (query.length < 2) {
                    $("#productList").html('<div class="text-muted text-center py-3"><i class="bx bx-search"></i> Keep typing...</div>');
                    return;
                }

                $.get("{{ url('/product_search_in_pos') }}", {
                    q: query,
                    mw_id: $("#MW_ID").val()
                }, function(data) {
                    if (!data.length) {
                        $("#productList").html('<div class="text-danger text-center py-3"><i class="bx bx-file-blank"></i> No products found</div>');
                        return;
                    }

                    const html = data.map(product => {
                        const hasManyPrices = product.price_count > 1;

                        let price = product.as_selling_price;
                        price = price ? price.toFixed(2) : '0.00';

                        const priceText = hasManyPrices ? "Different Prices" : price;
                        const disabled = !product.as_available_qty ? "disabled" : "";

                        const addButton = hasManyPrices ?
                            `<button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#different_prices_modal" onclick="load_diffrent_prices('${product.p_id}','${product.as_mw_id}')">Add</button>` :
                            `<button class="btn btn-sm btn-success add-btn" data-id="${product.as_id}" data-as_selling_price="${price}" data-as_available_qty="${product.as_available_qty}" data-p_id="${product.p_id}" data-name="${product.p_name}" data-code="${product.p_isbn}" ${disabled}>Add</button>`;

                        return `
                        <div class="list-group-item d-flex justify-content-between align-items-center list-group-item-action">
                            <div>
                                <h6 class="mb-1">${product.p_name}</h6>
                                <small>Barcode: ${product.p_isbn} | Code: ${product.p_id} | AV Qty: ${product.as_available_qty} | <span class="text-primary">Price: ${priceText}</span></small>
                            </div>
                            ${addButton}
                        </div>`;
                    }).join("");

                    $("#productList").html(html);
                    currentIndex = -1;

                    // BARCODE AUTO-ADD
                    const isProbablyBarcode = /^\d{4,}$/.test(query);
                    const $addBtns = $("#productList .add-btn:not([disabled])");
                    const $modalBtns = $("#productList [data-bs-toggle='modal']");

                    if (isProbablyBarcode && $addBtns.length === 1 && $modalBtns.length === 0) {
                        $addBtns.eq(0).trigger("click");
                    }
                });
            });

            $("#productSearch").on("keydown", function(e) {
                const $items = $("#productList .list-group-item");
                if (!$items.length) return;

                if (e.key === "ArrowDown" || e.key === "ArrowUp") {
                    e.preventDefault();
                    currentIndex = (e.key === "ArrowDown") ?
                        (currentIndex + 1) % $items.length :
                        (currentIndex - 1 + $items.length) % $items.length;
                    $items.removeClass("active").eq(currentIndex).addClass("active");
                }

                if (e.key === "Enter") {
                    e.preventDefault();
                    if (currentIndex < 0) currentIndex = 0;
                    const $active = $items.eq(currentIndex);
                    const $modalBtn = $active.find("[data-bs-toggle='modal']");
                    const $addBtn = $active.find(".add-btn");
                    if ($modalBtn.length) {
                        $modalBtn.trigger("click");
                    } else if ($addBtn.length) {
                        $addBtn.trigger("click");
                    }
                    currentIndex = -1;
                }
            });
        }

        // ========================= COMMON EVENT LISTENERS ========================= //
        function initListeners() {
            $(document).on("input", ".qty, .discount, #DISCOUNT_PERCENTAGE, #DISCOUNT_AMOUNT, #CASH_PAID, #CARD_PAID, #OTHER_AMOUNT", updateTotals);
            $(document).on("change", "input[name='MPT_ID']", fiill_total);
        }

        // ========================= KEYBOARD SHORTCUTS ========================= //
        function initKeyboardShortcuts() {
            $(document).on("keydown", function(e) {
                const tag = e.target.tagName.toLowerCase();
                const isTypingField = ["input", "textarea", "select"].includes(tag);

                if (e.key === "F2") {
                    e.preventDefault();
                    $("#productSearch").focus().select();
                    return;
                }

                if (!isTypingField && (e.key === "r" || e.key === "R")) {
                    e.preventDefault();
                    $("[data-bs-target='#return_modal']").first().trigger("click");
                    return;
                }

                if (!isTypingField && (e.key === "c" || e.key === "C")) {
                    e.preventDefault();
                    $("[data-bs-target='#customer_modal']").first().trigger("click");
                    return;
                }

                if (e.key === "F8") {
                    e.preventDefault();
                    $("[data-bs-target='#change_warehouse_modal']").first().trigger("click");
                    return;
                }

                if (e.key === "F9") {
                    e.preventDefault();
                    $("#saveBtn").trigger("click");
                    return;
                }
            });

            $(document).on("keydown", ".qty, .discount", function(e) {
                if (!["ArrowUp", "ArrowDown", "Enter"].includes(e.key)) return;

                const $currentRow = $(this).closest("tr");
                const cls = $(this).hasClass("qty") ? "qty" : "discount";

                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#productSearch").focus().select();
                    return;
                }

                e.preventDefault();
                const $targetRow = e.key === "ArrowUp" ? $currentRow.prev("tr") : $currentRow.next("tr");
                if ($targetRow.length) {
                    $targetRow.find("." + cls).focus().select();
                }
            });

            $(document).on("keydown", "#CASH_PAID, #CARD_PAID, #OTHER_AMOUNT", function(e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    $("#saveBtn").trigger("click");
                }
            });
        }

        // ========================= PRICE MODAL LOADER ========================= //
        function load_diffrent_prices(p_id, mw_id) {
            ajax_action("{{url('/')}}/load_diffrent_price_view", "LOAD_DIFFRENT_PRICES", JSON.stringify({
                P_ID: p_id,
                MW_ID: mw_id
            }), "{{ csrf_token() }}");
        }

        // ================== SAVE STOCK OUT ================== //
        $(document).on("click", "#saveBtn", function(e) {
            e.preventDefault();

            const $btnContainer = $("#save_btn");
            $btnContainer.html(`
            <a class="btn btn-primary w-lg mt-2 mb-2" disabled>
                <i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> PROCESSING..
            </a>
        `);

            const items = [];
            $("#selectedItems tr").each(function() {
                items.push({
                    id: $(this).data("id"),
                    discount: $(this).find(".discount").val(),
                    qty: $(this).find(".qty").val()
                });
            });

            if (!items.length) {
                Swal.fire("Oops!", "No items selected!", "warning");
                resetSaveButton();
                return;
            }

            const MW_ID = $("#MW_ID").val();
            const CUS_ID = $("#CUS_ID").val();
            const IS_CORPARATE = $("#IS_CORPARATE").val();
            const DISCOUNT_PERCENTAGE = $("#DISCOUNT_PERCENTAGE").val();
            const DISCOUNT_AMOUNT = $("#DISCOUNT_AMOUNT").val();
            const RETURNED_INVOICE_AMOUNT = $("#RETURNED_INVOICE_AMOUNT").val();
            const GRAND_TOTAL = parseFloat($("#grandTotal").text()) || 0;
            const MPT_ID = $("input[name='MPT_ID']:checked").val();
            const CASH_PAID = parseFloat($("#CASH_PAID").val()) || 0;
            const CARD_PAID = parseFloat($("#CARD_PAID").val()) || 0;
            const OTHER_PAID = parseFloat($("#OTHER_AMOUNT").val()) || 0;
            const TOTAL_PAID_AMOUNT = CASH_PAID + CARD_PAID + OTHER_PAID;

            if (!MW_ID) {
                Swal.fire("Warning!", "Please select a warehouse before saving.", "warning");
                resetSaveButton();
                return;
            }

            if (TOTAL_PAID_AMOUNT < GRAND_TOTAL && MPT_ID != 4) {
                // your previous validation was commented out, keeping same
            }

            $.ajax({
                url: "{{ url('/save_invoice') }}",
                method: "POST",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    items,
                    MW_ID,
                    CUS_ID,
                    IS_CORPARATE,
                    DISCOUNT_PERCENTAGE,
                    DISCOUNT_AMOUNT,
                    RETURNED_INVOICE_AMOUNT,
                    GRAND_TOTAL,
                    MPT_ID,
                    CASH_PAID,
                    CARD_PAID,
                    OTHER_PAID,
                    TOTAL_PAID_AMOUNT,
                },
                success: function(response) {
                    if (response.success) {

                        // show & print invoice
                        loadPrintInvoice(`{{url('/')}}/PrintInvoice/` + encodeURIComponent(btoa(response.in_id)));
                        // then clear POS for next bill
                        resetPOS();
                    } else if (response.error) {
                        Swal.fire("Error!", response.error, "error");
                    } else {
                        Swal.fire("Error!", "Unexpected server response.", "error");
                    }
                    resetSaveButton();
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire("Error!", "Something went wrong while saving.", "error");
                    resetSaveButton();
                }
            });
        });

        function resetSaveButton() {
            $("#save_btn").html('<button id="saveBtn" class="btn btn-primary w-lg mt-2 mb-2">COMPLETE</button>');
        }
    </script>


</body>

</html>