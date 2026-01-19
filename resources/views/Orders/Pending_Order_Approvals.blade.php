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
                                <h4 class="mb-sm-0 font-size-18">PENDING APPROVALS</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-lg_12">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">PENDING ORDER FILTER</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <div class="col-xl-8">
                                            <form id="ORDER_MANAGE_FILTER_FORM">
                                                @csrf
                                                <input type="hidden" name="TYPE" id="TYPE" value="1">

                                                <div class="row">
                                                    <div class="col-lg-3 mt-2">
                                                        <label for="">FROM DATE</label>
                                                        <input type="date" name="FROM_DATE" id="FROM_DATE" class="form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d') . ' -1460 days'))}}">
                                                    </div>
                                                    <div class="col-lg-3 mt-2">
                                                        <label for="">TO DATE</label>
                                                        <input type="date" name="TO_DATE" id="TO_DATE" class="form-control" value="{{date('Y-m-d')}}">
                                                    </div>
                                                    <div class="col-lg-3 mt-2">
                                                        <label for="">ORDER CODE</label>
                                                        <input type="text" name="OR_ID" id="OR_ID" class="form-control">
                                                    </div>
                                                    <div class="col-lg-3 mt-2">
                                                        <div id="ORDER_MANAGE_FILTER_FORM_BTN" style="margin-top: 27px;">
                                                            <button class="btn btn-primary w-100" type="submit">GET RESULT</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>


                                        <div class="col-xl-4">
                                            <div class="card d-flex flex-row align-items-center border shadow-none mt-3">
                                                <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                                    <i class="bx bx-hourglass h1 text-white mb-0"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <p class="text-muted mb-2">PENDING COUNT</p>
                                                    <h5 class="mb-0">
                                                        <span id="PENDING_COUNT">12</span>
                                                    </h5>
                                                </div>
                                            </div>
                                        </div>


                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div id="ORDER_MANAGE_FILTER_RESULT"></div>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#ORDER_MANAGE_FILTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#ORDER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/get_order_approval",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.result) {
                        $('#ORDER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                        $('#ORDER_MANAGE_FILTER_RESULT').html(data.result);
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#ORDER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#ORDER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                }
            });
        });
    });

    $(document).ready(function() {
        // triggers the click on your GET RESULT button
        $('#ORDER_MANAGE_FILTER_FORM button[type="submit"]').trigger('click');
    });

    function load_count() {
        $('#PENDING_COUNT').html('<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i>');

        const formData = new FormData();
        formData.append('_token', "{{csrf_token()}}");
        formData.append('TYPE', "1");

        $.ajax({
            type: 'POST',
            url: "<?php echo url('/') . "/get_order_approval_count" ?>",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {
                if (data.result) {
                    $('#PENDING_COUNT').html(data.result);
                }
                if (data.error) {
                    Swal.fire(
                        'Error!',
                        data.error,
                        'error'
                    );
                    $('#PENDING_COUNT').html('<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i>');
                }

            },
            error: function(xhr, status, error) {
                Swal.fire(
                    'Error!',
                    error,
                    'error'
                );
                $('#PENDING_COUNT').html('<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i>');
            }
        });
    }

    load_count();
</script>