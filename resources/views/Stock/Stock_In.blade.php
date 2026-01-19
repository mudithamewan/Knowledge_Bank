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
                                <h4 class="mb-sm-0 font-size-18">Stock In</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <!-- LEFT: SEARCH -->
                        <div class="col-xl-5" style="height: 82vh; overflow-y: auto;">
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
                                    <h5 class="card-title text-primary mb-0">SELECTED ITEMS</h5>
                                </div>

                                <div class="card-body">
                                    <table class="table table-bordered table-sm align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th style="text-align:center">Purchase Price</th>
                                                <th style="text-align:center">Selling Price</th>
                                                <th style="text-align:center">Qty</th>
                                                <th style="text-align:center">Total</th>
                                                <th style="text-align:center"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="selectedItems">
                                        </tbody>
                                    </table>





                                    <div class="row">
                                        <div class="col-xl-4"></div>
                                        <div class="col-xl-8">
                                            <table width="100%">
                                                <tr>
                                                    <th style="text-align: right;">Total Items</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;"><span class="badge bg-primary" id="itemCount">0</span></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right;">
                                                        <h5>Grand Total</h5>
                                                    </th>
                                                    <th>
                                                        <h5>:</h5>
                                                    </th>
                                                    <th style="text-align: right;">
                                                        <h5><span class="text-success" id="grandTotal">0</span></h5>
                                                    </th>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-xl-12">
                                            <div class="border-bottom border-secondary mt-2 mb-2"></div>
                                        </div>
                                        <div class="col-xl-4 mt-2">
                                            <label for="">WAREHOUSE <span class="text-danger">*</span></label>
                                            <select name="MW_ID" id="MW_ID" class="form-select">
                                                <option value="" selected disabled hidden>Choose ...</option>
                                                @foreach($WAREHOUSES as $item)
                                                <option value="{{$item->mw_id}}">{{$item->mw_name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-xl-4 mt-2">
                                            <label for="">GRN <span class="text-danger">*</span></label>
                                            <input type="text" name="GRN" id="GRN" class="form-control">
                                        </div>
                                        <div class="col-xl-4 mt-2">
                                            <label for="">ATTACHMENTS</label>
                                            <input type="file" name="FILES[]" id="FILES" class="form-control" multiple accept=".pdf,application/pdf">
                                        </div>
                                        <div class="col-xl-12 mt-2">
                                            <label for="">REMARK</label>
                                            <textarea name="REMARK" id="REMARK" rows="3" class="form-control"></textarea>
                                        </div>
                                    </div>


                                </div>
                                <div class="card-footer text-end" id="save_btn">
                                    <button id="saveBtn" class="btn btn-primary w-lg mt-2 mb-2">SAVE</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            @include('Layout/footer')

        </div>
    </div>


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>

    <script>
        /* =========================================================
   INPUT MASK
========================================================= */
        function initInputMask() {
            Inputmask({
                alias: "numeric",
                groupSeparator: ",",
                autoGroup: true,
                digits: 2,
                digitsOptional: false,
                placeholder: "0"
            }).mask('.input-mask');
        }

        $(document).ready(function() {
            initInputMask();
        });

        /* =========================================================
           TOTALS
        ========================================================= */
        function updateTotals() {
            let totalItems = 0;
            let grandTotal = 0;

            $("#selectedItems tr").each(function() {
                let qty = parseInt($(this).find(".qty").val()) || 0;
                let price = parseFloat($(this).find(".selling").inputmask('unmaskedvalue')) || 0;

                let rowTotal = qty * price;
                $(this).find(".row-total").text(rowTotal.toFixed(2));

                totalItems += qty;
                grandTotal += rowTotal;
            });

            $("#itemCount").text(totalItems);
            $("#grandTotal").text(grandTotal.toFixed(2));
        }

        /* =========================================================
           ADD PRODUCT
        ========================================================= */
        $(document).on("click", ".add-btn", function() {

            let id = $(this).data("id");
            let name = $(this).data("name");
            let code = $(this).data("code");

            if ($("#selectedItems tr[data-id='" + id + "']").length) {
                Swal.fire('Oops!', 'This product is already added!', 'warning');
                return;
            }

            let row = `
    <tr data-id="${id}">
        <td>${name}<br><small class="text-muted">Code: ${code}</small></td>
        <td><input type="text" class="form-control form-control-sm text-end input-mask purchase"></td>
        <td><input type="text" class="form-control form-control-sm text-end input-mask selling"></td>
        <td><input type="number" class="form-control form-control-sm qty" value="1" min="1"></td>
        <td class="row-total text-end">0.00</td>
        <td>
            <button class="btn btn-sm btn-danger remove-btn">
                <i class="bx bx-trash"></i>
            </button>
        </td>
    </tr>`;

            $("#selectedItems").append(row);

            initInputMask();
            updateTotals();

            $("#selectedItems tr[data-id='" + id + "']").find(".purchase").focus();
        });

        /* =========================================================
           LIVE UPDATE
        ========================================================= */
        $(document).on("input", ".qty, .selling", updateTotals);

        /* =========================================================
           REMOVE ITEM
        ========================================================= */
        $(document).on("click", ".remove-btn", function() {
            $(this).closest("tr").remove();
            updateTotals();
        });

        /* =========================================================
           ENTER KEY NAV INSIDE ROW
        ========================================================= */
        $(document).on("keydown", "#selectedItems input", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                let inputs = $(this).closest("tr").find("input");
                let index = inputs.index(this);
                if (index < inputs.length - 1) {
                    inputs.eq(index + 1).focus();
                }
            }
        });

        /* =========================================================
           SEARCH + KEYBOARD NAV
        ========================================================= */
        let currentIndex = -1;

        /* SEARCH (keyup) */
        $("#productSearch").on("keyup", function(e) {

            if (["ArrowUp", "ArrowDown", "Enter"].includes(e.key)) return;

            let q = $(this).val().trim();
            if (q.length < 2) {
                $("#productList").html(
                    '<div class="text-muted text-center py-3">Keep typing…</div>'
                );
                return;
            }

            $.get("{{ url('/product_search') }}", {
                q
            }, function(data) {

                if (!data.length) {
                    $("#productList").html(
                        '<div class="text-danger text-center py-3">No products found</div>'
                    );
                    return;
                }

                let html = '';
                data.forEach(p => {
                    html += `
            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                <div>
                    <strong>${p.p_name}</strong><br>
                    <small>Barcode: ${p.p_isbn} | Code: ${p.p_id}</small>
                </div>
                <button class="btn btn-sm btn-success add-btn"
                        data-id="${p.p_id}"
                        data-name="${p.p_name}"
                        data-code="${p.p_isbn}">
                    Add
                </button>
            </div>`;
                });

                $("#productList").html(html);
                currentIndex = -1;
            });
        });

        /* KEYBOARD NAV (keydown) */
        $("#productSearch").on("keydown", function(e) {

            let items = $("#productList .list-group-item");
            if (!items.length) return;

            if (e.key === "ArrowDown") {
                e.preventDefault();
                currentIndex = (currentIndex + 1) % items.length;
                items.removeClass("active");
                items.eq(currentIndex).addClass("active");
            }

            if (e.key === "ArrowUp") {
                e.preventDefault();
                currentIndex = (currentIndex - 1 + items.length) % items.length;
                items.removeClass("active");
                items.eq(currentIndex).addClass("active");
            }

            if (e.key === "Enter") {
                e.preventDefault();

                if (currentIndex >= 0) {
                    items.eq(currentIndex).find(".add-btn").trigger("click");
                } else {
                    items.eq(0).find(".add-btn").trigger("click");
                }

                $("#productSearch").val("");
                $("#productList").html(
                    '<div class="text-muted text-center py-3">Start typing to search products…</div>'
                );
                currentIndex = -1;
            }
        });

        /* MOUSE HOVER SYNC */
        $(document).on("mouseenter", "#productList .list-group-item", function() {
            $("#productList .list-group-item").removeClass("active");
            $(this).addClass("active");
            currentIndex = $(this).index();
        });

        /* =========================================================
           SAVE STOCK IN (AJAX)
        ========================================================= */
        $(document).on("click", "#saveBtn", function(e) {
            e.preventDefault();

            let items = [];

            $("#selectedItems tr").each(function() {
                items.push({
                    id: $(this).data("id"),
                    purchase: $(this).find(".purchase").inputmask('unmaskedvalue'),
                    selling: $(this).find(".selling").inputmask('unmaskedvalue'),
                    qty: $(this).find(".qty").val()
                });
            });

            if (!items.length) {
                Swal.fire('Oops!', 'No items selected!', 'warning');
                return;
            }

            let fd = new FormData();
            fd.append('_token', "{{ csrf_token() }}");
            fd.append('items', JSON.stringify(items));
            fd.append('mw_id', $("#MW_ID").val());
            fd.append('grn', $("#GRN").val());
            fd.append('remark', $("#REMARK").val());

            let files = document.getElementById('FILES').files;
            for (let i = 0; i < files.length; i++) {
                fd.append('FILES[]', files[i]);
            }

            $.ajax({
                url: "{{ url('/save_stock_in') }}",
                method: "POST",
                data: fd,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.fire(
                        res.success ? 'Success' : 'Error',
                        res.success || res.error,
                        res.success ? 'success' : 'error'
                    ).then(() => {
                        if (res.success) {
                            location.reload();
                        }
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Something went wrong', 'error');
                }
            });
        });
    </script>


</body>

</html>