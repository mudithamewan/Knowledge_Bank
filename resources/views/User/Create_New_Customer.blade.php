<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <style>
        .section-title-wrapper {
            display: flex;
            align-items: center;
            text-align: center;
            margin-bottom: 1rem;
        }

        .section-line {
            flex: 1;
            height: 2px;
            background-color: #ccc;
        }

        .section-title {
            margin: 0 10px;
            white-space: nowrap;
            /* font-weight: 600; */
            /* font-size: 1rem; */
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
                                <h4 class="mb-sm-0 font-size-18">CREATE NEW USER</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-6">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">USER CREATION FORM</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form id="NEW_USER_CREATE_FORM">
                                        @csrf

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="section-title-wrapper">
                                                    <div class="section-line"></div>
                                                    <div class="section-title">PERSONAL INFORMATION</div>
                                                    <div class="section-line"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 mt-2">
                                                <label for="">TITLE <span class="text-danger">*</span></label>
                                                <select name="TITLE" id="TITLE" class="form-select">
                                                    <option value="" disabled hidden selected>Choose .. </option>
                                                    <option value="Mr">Mr.</option>
                                                    <option value="Mrs">Mrs.</option>
                                                    <option value="Miss">Miss.</option>
                                                    <option value="Ms">Ms.</option>
                                                    <option value="Dr">Dr.</option>
                                                    <option value="Prof">Prof.</option>
                                                    <option value="Rev">Rev.</option>
                                                    <option value="Hon">Hon.</option>
                                                    <option value="Sir">Sir</option>
                                                    <option value="Lady">Lady</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-7 mt-2">
                                                <label for="">NAME <span class="text-danger">*</span></label>
                                                <input type="text" name="NAME" id="NAME" class="form-control" required>
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <label for="">DOB <span class="text-danger">*</span></label>
                                                <input type="date" name="DOB" id="DOB" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">NIC</label>
                                                <input type="text" name="NIC" id="NIC" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">CONTACT NUMBER <span class="text-danger">*</span></label>
                                                <input type="text" name="CONTACT_NUMBER" id="CONTACT_NUMBER" class="form-control" required>
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">GENDER <span class="text-danger">*</span></label> <br>
                                                <input type="radio" name="GENDER" id="MALE" value="M" checked> <label for="MALE">MALE</label> &nbsp;&nbsp;&nbsp;
                                                <input type="radio" name="GENDER" id="FEMALE" value="F"> <label for="FEMALE">FEMALE</label>
                                            </div>
                                            <div class="col-lg-8 mt-2">
                                                <label for="">EMAIL </label>
                                                <input type="text" name="EMAIL" id="EMAIL" class="form-control" required>
                                            </div>
                                            <div class="col-lg-12 mt-2">
                                                <label for="">ADDRESS <span class="text-danger">*</span></label>
                                                <textarea name="ADDRESS" id="ADDRESS" class="form-control" rows="4"></textarea>
                                            </div>

                                            <div class="col-lg-4 mt-3">
                                                <div id="NEW_USER_CREATE_FORM_BTN">
                                                    <button class="btn btn-primary w-100" type="submit">SAVE</button>
                                                </div>
                                            </div>
                                        </div>

                                    </form>
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#NEW_USER_CREATE_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#NEW_USER_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: "{{url('/')}}/save_customer",
                type: "POST",
                data: formData,
                processData: false, // don’t convert to query string
                contentType: false, // let browser set boundary
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        Swal.fire(
                            'Success!',
                            data.success,
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                window.location.assign("{{url('/')}}/Customer_Profile/" + encodeURIComponent(btoa(data.cus_id)));
                            } else {
                                window.location.assign("{{url('/')}}/Customer_Profile/" + encodeURIComponent(btoa(data.cus_id)));
                            }
                        });
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#NEW_USER_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#NEW_USER_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                }
            });
        });
    });
</script>