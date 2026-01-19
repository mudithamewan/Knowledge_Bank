<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <style>
        /* Customize Select2 dropdown */
        .select2-container--default .select2-selection--single {
            height: 38px !important;
            border: 1px solid #ced4da !important;
            border-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: 10px !important;
            color: #495057 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            border-left: none !important;
            border-top-right-radius: 4px !important;
            border-bottom-right-radius: 4px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #495057 transparent transparent !important;
            border-style: solid !important;
            border-width: 5px 5px 0 5px !important;
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
                                <h4 class="mb-sm-0 font-size-18">CREATE NEW USER ROLE</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="card">
                        <div class="card-header bg-transparent border-bottom">
                            <div class="d-flex flex-wrap align-items-start">
                                <div class="me-2">
                                    <h5 class="card-title mt-1 mb-0 text-primary">USER ROLE CREATION FORM</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form id="NEW_ROLE_CREATE_FORM">
                                @csrf

                                <div class="row">
                                    <div class="col-lg-4 mt-2">
                                        <label for="">ROLE NAME <span class="text-danger">*</span></label>
                                        <input type="text" name="NAME" id="NAME" class="form-control" required>
                                    </div>
                                    <div class="col-sm-5 mt-2">
                                        <label for="">Select Modules <span class="text-danger">*</span></label>
                                        <div id="selectMenus">
                                            @php $cat = null; @endphp
                                            <select class="select2 form-select select2-multiple mt-2" multiple="multiple" id="MODULES" data-placeholder="Choose ..." style="width:100%" name="MODULES[]">
                                                @foreach ($MODULES as $module)
                                                @if($cat != $module->saa_category)
                                                @if($cat !== null)
                                                </optgroup> {{-- close previous group --}}
                                                @endif
                                                <optgroup label="{{ $module->saa_category }}">
                                                    @php $cat = $module->saa_category; @endphp
                                                    @endif

                                                    <option value="{{ $module->saa_id }}">{{ $module->saa_name }}</option>
                                                    @endforeach

                                                    @if($cat !== null)
                                                </optgroup> {{-- close last group --}}
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 mt-2">
                                        <div id="NEW_ROLE_CREATE_FORM_BTN" style="margin-top: 27px;">
                                            <button class="btn btn-primary w-100" type="submit">SAVE</button>
                                        </div>
                                    </div>
                                </div>

                            </form>
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
        $("#MENUS").select2({
            dropdownParent: $("#selectMenus")
        });
    });
</script>

<script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#NEW_ROLE_CREATE_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#NEW_ROLE_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/save_user_role",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        Swal.fire(
                            'Success!',
                            data.success,
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                window.location.assign("{{url('/')}}/Manage_Roles");
                            } else {
                                window.location.assign("{{url('/')}}/Manage_Roles");
                            }
                        });
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#NEW_ROLE_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#NEW_ROLE_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                }
            });
        });
    });
</script>