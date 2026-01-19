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
                                <h4 class="mb-sm-0 font-size-18">CREATE NEW ORGANIZATION</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <form id="NEW_ORGANIZATION_CREATE_FORM" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-xl-7">
                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">ORGANIZATION CREATION FORM</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">


                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="section-title-wrapper">
                                                    <div class="section-line"></div>
                                                    <div class="section-title">BASIC INFORMATION</div>
                                                    <div class="section-line"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 mt-2">
                                                <label for="">NAME <span class="text-danger">*</span></label>
                                                <input type="text" name="NAME" id="NAME" class="form-control">
                                            </div>
                                            <div class="col-lg-6 mt-2">
                                                <label for="">BUSINESS NAME <span class="text-danger">*</span></label>
                                                <input type="text" name="BUSINESS_NAME" id="BUSINESS_NAME" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">CONTACT <span class="text-danger">*</span></label>
                                                <input type="text" name="CONTACT" id="CONTACT" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">EMAIL</label>
                                                <input type="email" name="EMAIL" id="EMAIL" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">ADDRESS <span class="text-danger">*</span></label>
                                                <input type="text" name="ADDRESS" id="ADDRESS" class="form-control">
                                            </div>



                                            <div class="col-lg-12">
                                                <div class="section-title-wrapper mt-5">
                                                    <div class="section-line"></div>
                                                    <div class="section-title">BUSINESS INFORMATION</div>
                                                    <div class="section-line"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">BR NUMBER</label>
                                                <input type="text" name="BR_NUMBER" id="BR_NUMBER" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">BUSINESS TYPE <span class="text-danger">*</span></label>
                                                <select name="BUSINESS_TYPE" id="BUSINESS_TYPE" class="form-select">
                                                    <option value="" disabled hidden selected>Choose ...</option>
                                                    @foreach($BUSINESS_TYPE as $TYPE)
                                                    <option value="{{$TYPE->mbt_id}}">{{$TYPE->mbt_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">VAT REGISTERED? </label><br>
                                                <div class="mt-2">
                                                    <input type="radio" name="VAT" id="VAT_YES" value="YES" checked onchange="display_vat_form()">
                                                    <label class="ms-1" for="VAT_YES">YES</label> &nbsp;&nbsp;&nbsp;&nbsp;
                                                    <input type="radio" name="VAT" id="VAT_NO" value="NO" onchange="display_vat_form()">
                                                    <label class="ms-1" for="VAT_NO">NO</label>
                                                </div>
                                            </div>

                                            <div class="col-xl-8">
                                                <div class="row" id="vat_info_form" style="display: none;">
                                                    <div class="col-lg-6 mt-2">
                                                        <label for="">VAT REG. NO. <span class="text-danger">*</span></label>
                                                        <input type="text" name="VAT_REG_NO" id="VAT_REG_NO" class="form-control">
                                                    </div>
                                                    <div class="col-lg-6 mt-2">
                                                        <label for="">DATE OF VAT REG.</label>
                                                        <input type="date" name="VAT_REG_DATE" id="VAT_REG_DATE" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="section-title-wrapper mt-5">
                                                    <div class="section-line"></div>
                                                    <div class="section-title">BANKING INFORMATION</div>
                                                    <div class="section-line"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">BANK CODE</label>
                                                <input type="text" name="BANK_CODE" id="BANK_CODE" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">BRANCH CODE</label>
                                                <input type="text" name="BRANCH_CODE" id="BRANCH_CODE" class="form-control">
                                            </div>
                                            <div class="col-lg-4 mt-2">
                                                <label for="">ACCOUNT NUMBER</label>
                                                <input type="text" name="ACCOUNT_NUMBER" id="ACCOUNT_NUMBER" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-5">
                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">TAGGING INFORMATION</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <label for="">Tag <span class="text-danger">*</span></label>
                                                <div id="selectMenus">
                                                    <select class="select2 form-select select2-multiple mt-2" multiple="multiple" id="TYPES" data-placeholder="Choose ..." style="width:100%" name="TYPES[]">
                                                        @foreach($ORGANIZATION_TYPE as $DATA)
                                                        <option value="{{ $DATA->mot_id }}">{{ $DATA->mot_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">DOCUMENTS</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <label for="">Documents</label>
                                                <input type="file" name="DOCUMENTS[]" id="DOCUMENTS" class="form-control" multiple>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div id="NEW_ORGANIZATION_CREATE_FORM_BTN">
                                    <button class="btn btn-primary w-100" type="submit">SAVE</button>
                                </div>


                            </div>
                        </div>

                    </form>


                </div>
            </div>

            @include('Layout/footer')
        </div>
    </div>

</body>

</html>


<script>
    $(document).ready(function() {
        $("#TYPES").select2({
            dropdownParent: $("#selectMenus")
        });
    });
</script>
<script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
<script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
<script>
    function display_vat_form() {
        let isVatYes = $('#VAT_YES').is(':checked');

        if (isVatYes) {
            $('#vat_info_form').show();
        } else {
            $('#vat_info_form').hide();
        }
    }
    display_vat_form();

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#NEW_ORGANIZATION_CREATE_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#NEW_ORGANIZATION_CREATE_FORM_BTN').html(
                '<button class="btn btn-primary w-100" disabled>' +
                '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
            );

            var formData = new FormData(this);

            $.ajax({
                url: "{{url('/')}}/save_organization",
                type: "POST",
                data: formData,
                processData: false, // required for file uploads
                contentType: false, // required for file uploads
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        Swal.fire('Success!', data.success, 'success')
                            .then(() => {
                                window.location.assign("{{url('/')}}/Organization_Profile/" + encodeURIComponent(btoa(data.org_id)));
                            });
                    }

                    if (data.error) {
                        Swal.fire('Error!', data.error, 'error');
                        $('#NEW_ORGANIZATION_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire('Error!', error, 'error');
                    $('#NEW_ORGANIZATION_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                }
            });
        });
    });
</script>