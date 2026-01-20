<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
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
                                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">

                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-cart h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY SALE</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($TODAY_DATA->total_sale?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-wallet h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY COLLECTION</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($TODAY_DATA->total_collection?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-money h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY CREDIT</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($TODAY_DATA->total_credit?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <span id="daily_status"></span>
                        </div>

                        <div class="col-xl-6">
                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-cart h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">MONTH SALE</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($MONTH_DATA->total_sale?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-wallet h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">MONTH COLLECTION</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($MONTH_DATA->total_collection?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-danger text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-money h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">MONTH CREDIT</p>
                                            <h5 class="mb-0 counter" data-value="{{ ($MONTH_DATA->total_credit?? 0) }}">
                                                0.00
                                            </h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <span id="monthly_status"></span>
                        </div>


                        <div class="col-xl-6">

                            <div class="card">
                                <div class="card-body">
                                    <div class="clearfix">
                                        <div class="float-end">
                                            <div class="input-group input-group-sm">
                                                <select class="form-select form-select-sm" id="MW_ID" onchange="loadWidget('warehouse_status')">
                                                    @foreach($WAREHOUSES as $WAREHOUSE)
                                                    <option value="{{$WAREHOUSE->mw_id}}">{{$WAREHOUSE->mw_name}}</option>
                                                    @endforeach
                                                </select>
                                                <label class="input-group-text">Locations</label>
                                            </div>
                                        </div>
                                        <h4 class="card-title text-primary mb-4">STOCK LOCATIONS WISE SALES OVERVIEW</h4>
                                    </div>
                                    <span id="warehouse_status"></span>
                                </div>
                            </div>

                        </div>

                        <div class="col-xl-6">

                            <div class="card">
                                <div class="card-body">
                                    <div class="clearfix">
                                        <div class="float-end">
                                            <div class="input-group input-group-sm">
                                                <select class="form-select form-select-sm" id="MW_ID2" onchange="loadWidget('product_stock')">
                                                    @foreach($WAREHOUSES as $WAREHOUSE)
                                                    <option value="{{$WAREHOUSE->mw_id}}">{{$WAREHOUSE->mw_name}}</option>
                                                    @endforeach
                                                </select>
                                                <label class="input-group-text">Locations</label>
                                            </div>
                                        </div>
                                        <h4 class="card-title text-primary mb-4">PRODUCT STOCKS</h4>
                                    </div>
                                    <div style="height: 41vh; overflow-y: auto;">
                                        <span id="product_stock"></span>
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
    document.addEventListener("DOMContentLoaded", () => {

        document.querySelectorAll(".counter").forEach(counter => {

            const target = parseFloat(counter.dataset.value);
            let current = 0;
            const duration = 800; // ms (speed)
            const stepTime = 20;
            const steps = duration / stepTime;
            const increment = target / steps;

            const timer = setInterval(() => {
                current += increment;

                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }

                counter.innerText = current.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

            }, stepTime);
        });

    });


    function loadWidget(type) {
        $('#' + type).html('<center><h1 class="display-4 mb-3" style="color:#efedfc"><i class="bx bx-loader bx-spin align-middle me-2"></i> Loading...</h1></center>');
        var link = '<?php echo url('/')  ?>/Load_Widget';
        const formData = new FormData();
        formData.append('type', type);
        formData.append('_token', "<?= csrf_token() ?>");

        if (type == 'warehouse_status') {
            formData.append('MW_ID', $('#MW_ID').val());
        } else if (type == 'product_stock') {
            formData.append('MW_ID', $('#MW_ID2').val());
        }

        $.ajax({
            type: 'POST',
            url: link,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.result) {
                    $('#' + type).html(data.result);
                }
                if (data.error) {
                    Swal.fire(
                        'Error!',
                        data.error,
                        'error'
                    );
                }

            }
        });
    }

    loadWidget('daily_status');
    loadWidget('monthly_status');
    loadWidget('warehouse_status');
    loadWidget('product_stock');
</script>