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
                                <h4 class="mb-sm-0 font-size-18">MANAGE USERS</h4>
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
                                            <h5 class="card-title mt-1 mb-0 text-primary">USERS FILTER</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="USER_MANAGE_FILTER_FORM">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-2 mt-2">
                                                <label for="">FROM DATE</label>
                                                <input type="date" name="FROM_DATE" id="FROM_DATE" class="form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d') . ' -1460 days'))}}">
                                            </div>
                                            <div class="col-lg-2 mt-2">
                                                <label for="">TO DATE</label>
                                                <input type="date" name="TO_DATE" id="TO_DATE" class="form-control" value="{{date('Y-m-d')}}">
                                            </div>
                                            <div class="col-lg-2 mt-2">
                                                <label for="">NIC</label>
                                                <input type="text" name="NIC" id="NIC" class="form-control">
                                            </div>
                                            <div class="col-lg-2 mt-2">
                                                <div id="USER_MANAGE_FILTER_FORM_BTN" style="margin-top: 27px;">
                                                    <button class="btn btn-primary w-100" type="submit">GET RESULT</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div id="USER_MANAGE_FILTER_RESULT"></div>
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

        $('#USER_MANAGE_FILTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#USER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/get_user_filter_result",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.result) {
                        $('#USER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                        $('#USER_MANAGE_FILTER_RESULT').html(data.result);
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#USER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#USER_MANAGE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                }
            });
        });
    });

    $(document).ready(function() {
        // triggers the click on your GET RESULT button
        $('#USER_MANAGE_FILTER_FORM button[type="submit"]').trigger('click');
    });
</script>