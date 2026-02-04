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

        .selected-items-body {
            max-height: 300px;
            overflow-y: auto;
        }

        .selected-items-body table {
            width: 100%;
            table-layout: fixed;
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
                                <h4 class="mb-sm-0 font-size-18">REQUEST DESTROY</h4>
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

                                    <div class="row">
                                        <div class="col-lg-4 mb-2">
                                            <select name="MW_ID" id="MW_ID" class="form-select">
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

                                        <div class="col-lg-8 mb-2">

                                            <!-- Search Input -->
                                            <div class="input-group mb-3">
                                                <input type="text" id="productSearch" class="form-control" placeholder="Search by Name, Barcode, or Product Code">
                                                <button class="btn btn-outline-primary" type="button">Search</button>
                                            </div>
                                        </div>
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
                                            <h5 class="card-title text-primary mb-0">DESTROY ITEMS</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">

                                    <table class="table  table-sm align-middle mb-0">
                                        <colgroup>
                                            <col style="width:30%">
                                            <col style="width:15%">
                                            <col style="width:15%">
                                            <col style="width:15%">
                                            <col style="width:5%">
                                        </colgroup>
                                        <thead class="table-light">
                                            <tr>
                                                <th>Item</th>
                                                <th class="text-center">Selling Price</th>
                                                <th class="text-center">Qty</th>
                                                <th class="text-center">Total</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                    </table>
                                    <div class="selected-items-body">
                                        <table class="table table-bordered table-sm align-middle mb-0">
                                            <colgroup>
                                                <col style="width:30%">
                                                <col style="width:15%">
                                                <col style="width:15%">
                                                <col style="width:15%">
                                                <col style="width:5%">
                                            </colgroup>
                                            <tbody id="selectedItems"></tbody>
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="mt-3 mb-2" style="border-top:3px solid #eff2f7"></div>
                                        </div>
                                        <div class="col-xl-4"></div>
                                        <div class="col-xl-8">
                                            <table width="100%">
                                                <tr>
                                                    <th style="text-align: right;" class="p-1">Total Items</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%"><span class="badge bg-primary" id="itemCount">0</span></th>
                                                </tr>
                                                <tr>
                                                    <th style="text-align: right; font-size: 20px;" class="p-1">Sub Total</th>
                                                    <th>:</th>
                                                    <th style="text-align: right;" class="p-1" width="40%"><span style="font-size: 20px;" class="text-primary" id="sub_total">0.00</span></th>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer text-end" id="save_btn">
                                    <button id="saveBtn" class="btn btn-primary w-lg mt-2 mb-2">REQUEST</button>
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


    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
    <script src="{{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>


    <script>
        // ============ RESET POS AFTER PRINT ============ //
        function resetPOS() {
            $("#selectedItems").empty();
            $("#itemCount").text(0);
            $("#sub_total").text("0.00");
            resetSearch(true);
        }

        // ========================= GLOBAL INITIALIZATION ========================= //
        $(document).ready(function() {
            initProductSearch();
            initListeners();
        });

        // ========================= ITEM CALCULATIONS ========================= //
        function updateTotals() {
            let totalItems = 0,
                subTotal = 0;

            $("#selectedItems tr").each(function() {
                const $row = $(this);
                const qty = parseFloat($row.find(".qty").val()) || 0;
                const price = parseFloat($row.find(".selling-price").text()) || 0;

                const rowSub = qty * price;
                const rowTotal = rowSub;

                $row.find(".row-total").text(rowTotal.toFixed(2));
                totalItems += qty;
                subTotal += rowTotal;
            });

            $("#itemCount").text(totalItems);
            $("#sub_total").text(subTotal.toFixed(2));

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
                }, function(response) {

                    // 🔥 CASE 2: Normal product list
                    const data = response.data;

                    if (!data.length) {
                        $("#productList").html(
                            '<div class="text-danger text-center py-3">' +
                            '<i class="bx bx-file-blank"></i> No products found</div>'
                        );
                        return;
                    }

                    const html = data.map(product => {
                        const hasManyPrices = product.price_count > 1;

                        let price = product.as_selling_price;
                        price = price ? price.toFixed(2) : '0.00';

                        const priceText = hasManyPrices ? "Different Prices" : price;
                        const disabled = !product.as_available_qty ? "disabled" : "";

                        const addButton = hasManyPrices ?
                            `<button class="btn btn-sm btn-success" data-bs-toggle="modal"
                data-bs-target="#different_prices_modal"
                onclick="load_diffrent_prices('${product.p_id}','${product.as_mw_id}')">Add</button>` :
                            `<button class="btn btn-sm btn-success add-btn"
                data-id="${product.as_id}"
                data-as_selling_price="${price}"
                data-as_available_qty="${product.as_available_qty}"
                data-p_id="${product.p_id}"
                data-name="${product.p_name}"
                data-code="${product.p_isbn}" ${disabled}>Add</button>`;

                        return `
            <div class="list-group-item d-flex justify-content-between align-items-center list-group-item-action">
                <div>
                    <h6 class="mb-1">${product.p_name}</h6>
                    <small>
                        Barcode: ${product.p_isbn} |
                        Code: ${product.p_id} |
                        AV Qty: ${product.as_available_qty ? product.as_available_qty : '<span class="text-danger">Out of Stock</span>'} |
                        <span class="text-primary">Price: ${priceText}</span>
                    </small>
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
            $(document).on("input", ".qty", updateTotals);
            $(document).on("change");
        }

        // ========================= KEYBOARD SHORTCUTS ========================= //


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
                    discount: 0,
                    qty: $(this).find(".qty").val()
                });
            });

            if (!items.length) {
                Swal.fire("Oops!", "No items selected!", "warning");
                resetSaveButton();
                return;
            }

            const MW_ID = $("#MW_ID").val();

            if (!MW_ID) {
                Swal.fire("Warning!", "Please select a warehouse before saving.", "warning");
                resetSaveButton();
                return;
            }

            $.ajax({
                url: "{{ url('/save_destroy_request') }}",
                method: "POST",
                dataType: "json",
                data: {
                    _token: "{{ csrf_token() }}",
                    items,
                    MW_ID,
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Success!', response.success, 'success')
                            .then(() => {
                                window.location.assign("{{url('/')}}/Destroy_Request_View/" + encodeURIComponent(btoa(response.req_id)));
                            });
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
            $("#save_btn").html('<button id="saveBtn" class="btn btn-primary w-lg mt-2 mb-2">REQUEST</button>');
        }
    </script>


</body>

</html>