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
                                <h4 class="mb-sm-0 font-size-18">ADD NEW ORDER</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->


                    <div class="row">




                        <!-- LEFT: Search products -->
                        <div class="col-lg-5" style="height:70vh; overflow-y:auto;">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <h5 class="card-title text-primary mb-0">SEARCH PRODUCTS</h5>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-2">
                                        <input id="productSearch" type="text" class="form-control" placeholder="Name, Barcode or Code">
                                        <button class="btn btn-outline-primary" id="btnSearch">Search</button>
                                    </div>
                                    <div id="productList" class="list-group" style="max-height:55vh; overflow-y:auto;">
                                        <div class="text-muted text-center py-3" id="noProducts"><i class="bx bx-search"></i> Start typing to search products</div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- RIGHT: Order (customers + customer cart) -->
                        <div class="col-lg-7" style="height:70vh; overflow-y:auto;">
                            <div class="card">

                                <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                    <h5 class="card-title text-primary mb-0">ORDER</h5>

                                    <div class="modal fade" id="add_customer_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">ADD CUSTOMER</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="text" id="customerSearch" class="form-control mb-2" placeholder="Search customer...">
                                                    <div id="customerList">
                                                        <div class="table-responsive">
                                                            <table class="table table-nowrap align-middle table-hover mb-0">
                                                                <tbody>
                                                                    @foreach($CUSTOMERS as $c)
                                                                    <tr class="customer-card" data-id="{{ $c->o_id }}" data-name="{{ $c->o_business_name }}">
                                                                        <td>
                                                                            <h5 class="text-truncate font-size-14 mb-1">
                                                                                <a href="javascript:void(0);" class="text-dark">{{ $c->o_business_name }}</a>
                                                                            </h5>
                                                                            <p class="text-muted mb-0">{{ $c->o_contact ?? '' }}</p>
                                                                        </td>
                                                                        <td style="width: 90px; text-align:right">
                                                                            <div>
                                                                                <button class="btn btn-sm btn-primary add-customer-btn">Add</button>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- jQuery search script -->
                                                <script>
                                                    $(document).ready(function() {
                                                        $('#customerSearch').on('keyup', function() {
                                                            var value = $(this).val().toLowerCase();
                                                            $('#customerList tbody tr').filter(function() {
                                                                $(this).toggle($(this).data('name').toLowerCase().includes(value));
                                                            });
                                                        });
                                                    });
                                                </script>
                                                <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#add_customer_modal">ADD CUTOMER</button>
                                </div>



                                <div class="card-body">
                                    <div id="orderCustomers" class="pb-3 mb-3 border-bottom">
                                        <div class="text-muted">No customers added yet. Add from left.</div>
                                    </div>

                                    <div id="customerCartArea" style="display:none;">
                                        <h6 id="activeCustomerName"></h6>
                                        <table class="table table-sm table-bordered">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="70%">Item</th>
                                                    <th width="20%" style="text-align:center">Qty</th>
                                                    <th width="10%"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="customerCart"></tbody>
                                        </table>

                                        <div class="d-flex justify-content-between">
                                            <div><small id="customerItemCount">0 items</small></div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-xl-12 mt-2">
                                            <label>Remark</label>
                                            <textarea id="remark" class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="col-xl-6 mt-2">
                                            <label>Outlet</label>
                                            <select name="MW_ID" id="MW_ID" class="form-select">
                                                <option value="" disabled hidden selected>SELECT OUTLET</option>
                                                @foreach($WAREHOUSES as $WAREHOUSE)
                                                <option value="{{$WAREHOUSE->mw_id}}">{{$WAREHOUSE->mw_name}} - ({{$WAREHOUSE->mwt_name}})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>




                                </div>

                                <div class="card-footer text-end">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="p-2 bg-secondary rounded text-white" id="orderCustomerCount">0 Customers</span>
                                        </div>
                                        <div>
                                            <button id="saveOrderBtn" class="btn btn-success w-lg">SAVE ORDER</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                    </div>

                </div>
            </div>




            @include('Layout/footer')
        </div>
    </div>


</body>



</html>
<script>
    $(document).ready(function() {

        let orderCustomers = [];
        let activeCustomerIndex = -1;

        // Add customer
        $(document).on('click', '.add-customer-btn', function() {
            const customerId = $(this).closest('.customer-card').data('id');
            const customerName = $(this).closest('.customer-card').data('name');

            if (orderCustomers.find(c => c.customer_id == customerId)) {
                Swal.fire('Customer already added!', '', 'info');
                return;
            }

            orderCustomers.push({
                customer_id: customerId,
                customer_name: customerName,
                items: []
            });

            activeCustomerIndex = orderCustomers.length - 1;
            renderOrderCustomers();
            renderActiveCustomerCart();
        });

        // Update quantity manually
        $(document).on('input', '.qty-input', function() {
            const idx = $(this).data('idx');
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) val = 1; // minimum 1
            $(this).val(val); // reset invalid values
            orderCustomers[activeCustomerIndex].items[idx].qty = val;
            renderOrderCustomers(); // update order summary if needed
        });

        // Search products
        $('#btnSearch').click(searchProducts);
        $('#productSearch').on('keyup', function(e) {
            if (e.key === 'Enter' || $(this).val().length >= 2) searchProducts();
        });

        function searchProducts() {
            const query = $('#productSearch').val().trim();
            if (!query) {
                $('#productList').html(`<div class="text-muted text-center py-3"><i class="bx bx-search"></i> Type to search</div>`);
                return;
            }

            $.get(`/product_search?q=${query}`, function(res) {
                if (!res.length) {
                    $('#productList').html(`<div class="text-muted text-center py-3">No products found</div>`);
                    return;
                }

                let html = '';
                res.forEach(p => {
                    html += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">${p.p_name}</h6>
                                <small>Barcode: ${p.p_isbn} | Code: ${p.p_id}</small>
                            </div>
                        <button class="btn btn-sm btn-success add-product-btn" data-product="${encodeURIComponent(JSON.stringify(p))}">
                            ADD
                        </button>
                    </div>
                `;
                });
                $('#productList').html(html);
            });
        }

        // Add product to active customer
        $(document).on('click', '.add-product-btn', function() {
            if (activeCustomerIndex === -1) {
                Swal.fire('No customer selected', 'Add or select a customer first', 'warning');
                return;
            }

            const p = JSON.parse(decodeURIComponent($(this).attr('data-product')));
            const oc = orderCustomers[activeCustomerIndex];
            const existing = oc.items.find(x => x.product_id == p.p_id);

            if (existing) {
                existing.qty += 1;
            } else {
                oc.items.push({
                    product_id: p.p_id,
                    product_name: p.p_name,
                    qty: 1
                });
            }

            renderActiveCustomerCart();
            renderOrderCustomers();
        });

        // Remove item
        $(document).on('click', '.remove-item', function() {
            const idx = $(this).data('idx');
            orderCustomers[activeCustomerIndex].items.splice(idx, 1);
            renderActiveCustomerCart();
            renderOrderCustomers();
        });

        // Switch customer
        $(document).on('click', '.order-customer-card', function() {
            activeCustomerIndex = $(this).data('idx');
            renderOrderCustomers();
            renderActiveCustomerCart();
        });

        // Remove customer
        $(document).on('click', '.remove-customer', function(e) {
            e.stopPropagation();
            const idx = $(this).data('idx');
            orderCustomers.splice(idx, 1);
            if (activeCustomerIndex >= orderCustomers.length) activeCustomerIndex = orderCustomers.length - 1;
            renderOrderCustomers();
            renderActiveCustomerCart();
        });

        // Render order customers
        function renderOrderCustomers() {
            if (!orderCustomers.length) {
                $('#orderCustomers').html(`<div class="text-muted">No customers added yet. Add from left.</div>`);
                $('#orderCustomerCount').text('0 Customers');
                $('#customerCartArea').hide();
                return;
            }

            let html = '';
            orderCustomers.forEach((c, i) => {
                const activeClass = i === activeCustomerIndex ? 'border-primary bg-light' : '';
                html += `
                <div class="card mb-1 p-2 d-flex  order-customer-card ${activeClass}" data-idx="${i}">
                    <div class="row">
                        <div class="col-10">
                              <div class="font-size-14 text-dark"><b>${c.customer_name}</b></div>
                        </div>
                         <div class="col-2" style="text-align:right">
                            <button class="btn btn-sm btn-danger remove-customer" data-idx="${i}">
                                <i class="bx bx-trash"></i>
                            </button>
                         </div>
                    </div>
                </div>
            `;
            });

            $('#orderCustomers').html(html);
            $('#orderCustomerCount').text(`${orderCustomers.length} Customer${orderCustomers.length > 1 ? 's' : ''}`);
            $('#customerCartArea').show();
        }

        // Render active customer's cart (qty only)
        function renderActiveCustomerCart() {
            if (activeCustomerIndex === -1 || !orderCustomers[activeCustomerIndex]) {
                $('#customerCartArea').hide();
                return;
            }

            const c = orderCustomers[activeCustomerIndex];
            $('#activeCustomerName').text(c.customer_name);

            if (!c.items.length) {
                $('#customerCart').html(`<tr><td colspan="5" class="text-center text-muted">No items added</td></tr>`);
                $('#customerItemCount').text('0 items');
                return;
            }

            let html = '';
            c.items.forEach((it, i) => {
                html += `
        <tr>
            <td>${it.product_name}</td>
            <td class="text-center">
                <input type="number" min="1" class="form-control form-control-sm qty-input" data-idx="${i}" value="${it.qty}" style="width:70px; margin:auto;">
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-danger remove-item" data-idx="${i}">
                    <i class="bx bx-trash"></i>
                </button>
            </td>
        </tr>
        `;
            });

            $('#customerCart').html(html);
            $('#customerItemCount').text(`${c.items.length} item${c.items.length > 1 ? 's' : ''}`);
        }

        $('#saveOrderBtn').click(function() {
            if (!orderCustomers.length) {
                Swal.fire('No customers added!', 'Add at least one customer before saving.', 'warning');
                return;
            }

            // Prepare data
            const payload = {
                remark: $('#remark').val(),
                mw_id: $('#MW_ID').val(),
                customers: orderCustomers
            };

            Swal.fire({
                title: 'Confirm Save?',
                text: "Do you want to save this order?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, save it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/') }}/save_order",
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}',
                            order_data: JSON.stringify(payload)
                        },
                        beforeSend: function() {
                            $('#saveOrderBtn').prop('disabled', true).text('Saving...');
                        },
                        success: function(res) {

                            if (res.success) {
                                Swal.fire('Success!', res.success, 'success')
                                    .then(() => {
                                        window.location.assign("{{url('/')}}/Order_View/" + encodeURIComponent(btoa(res.order_id)));
                                    });
                            }
                            if (res.error) {
                                Swal.fire('Error!', res.error, 'error');
                            }

                        },
                        error: function(err) {
                            Swal.fire('Error!', 'Something went wrong while saving.', 'error');
                            console.error(err);
                        },
                        complete: function() {
                            $('#saveOrderBtn').prop('disabled', false).text('Save Order');
                        }
                    });
                }
            });
        });

    });
</script>