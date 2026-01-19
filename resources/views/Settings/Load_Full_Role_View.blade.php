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

    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow-none border">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex flex-wrap align-items-start">
                        <div class="me-2">
                            <h5 class="card-title mt-1 mb-0 text-primary">ROLE ACCESS</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th style="text-align: center; background-color:#eff2f7;">MODULES</th>
                                <th style="text-align: center; background-color:#eff2f7;">ACTION</th>
                            </tr>
                            @php $Heading = ""; @endphp
                            @foreach($ROLE_DETAILS as $MODULE)
                            @if($MODULE->saa_id != 1)

                            @if($MODULE->saa_display_category != $Heading )
                            @php $Heading = $MODULE->saa_display_category; @endphp
                            <tr>
                                <th colspan="2" style="background-color: #eff2f7;">{{$Heading}}</th>
                            </tr>
                            @endif
                            <tr>
                                <td>{{$MODULE->saa_name}}</td>
                                <td style="text-align: center;">
                                    <div id="module_remove_btn_{{$MODULE->sra_id}}">
                                        <button class="btn btn-sm btn-danger" onclick="remove_access('{{$MODULE->sra_id}}')"><i class="bx bx-trash"></i></button>
                                    </div>
                                </td>
                            </tr>

                            @endif
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow-none border">
                <div class="card-header bg-transparent border-bottom">
                    <div class="d-flex flex-wrap align-items-start">
                        <div class="me-2">
                            <h5 class="card-title mt-1 mb-0 text-primary">ADD ACCESS</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form id="EDIT_ROLE_FORM">
                        @csrf
                        <input type="hidden" name="ROLE_ID" id="ROLE_ID" value="{{$ROLE_ID}}">
                        <div class="row">
                            <div class="col-xl-12">
                                <label for="">Role Name</label>
                                <input type="text" name="ROLE_NAME" id="ROLE_NAME" class="form-control" value="{{$ROLE->sr_name}}">
                            </div>
                            <div class="col-xl-12 mt-2">
                                <label for="">Status</label>
                                <select class="form-select" name="STATUS" id="STATUS">
                                    @if($ROLE->sr_is_active == 1)
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                    @else
                                    <option value="1">Active</option>
                                    <option value="0" selected>Inactive</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-xl-12 mt-2">
                                <label for="">Select Modules <span class="text-danger">*</span></label>
                                <div id="selectMenus">
                                    <select class="select2 form-select select2-multiple mt-2" multiple="multiple" id="MODULES" data-placeholder="Choose ..." style="width:100%" name="MODULES[]">
                                        @php $Heading = ""; @endphp

                                        @foreach($ACCESS_MODULES as $DATA)
                                        @if(!in_array($DATA->saa_id, $ROLE_MODULE_IDS))

                                        @if($DATA->saa_display_category != $Heading )
                                        @php $Heading = $DATA->saa_display_category; @endphp
                                        <optgroup label="{{ $Heading }}"> </optgroup>
                                        @endif

                                        <option value="{{ $DATA->saa_id }}">{{ $DATA->saa_name }}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div id="EDIT_ROLE_FORM_BTN" style="margin-top: 27px;">
                                    <button class="btn btn-primary w-100" type="submit">SAVE</button>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#MODULES").select2({
                dropdownParent: $("#selectMenus")
            });
        });
    </script>
    <script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
    @php $data_arr = json_encode(array('ROLE_ID'=>$ROLE_ID)); @endphp
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#EDIT_ROLE_FORM').on('submit', function(e) {
                e.preventDefault();
                $('#EDIT_ROLE_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{url('/')}}/add_module_to_role",
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
                                    ajax_action('<?= url('/') ?>/load_full_role_view', 'LOAD_FULL_ROLE', '<?= $data_arr ?>', '<?= csrf_token() ?>');
                                } else {
                                    ajax_action('<?= url('/') ?>/load_full_role_view', 'LOAD_FULL_ROLE', '<?= $data_arr ?>', '<?= csrf_token() ?>');
                                }
                            });
                        }

                        if (data.error) {
                            Swal.fire(
                                'Error!',
                                data.error,
                                'error'
                            );
                            $('#EDIT_ROLE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                        }
                    },
                    error: function(error) {
                        Swal.fire(
                            'Error!',
                            error,
                            'error'
                        );
                        $('#EDIT_ROLE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                });
            });
        });


        function remove_access(ID) {

            Swal.fire({
                title: "Do you want to remove this access?",
                showDenyButton: true,
                confirmButtonText: "Yes",
                denyButtonText: 'No'
            }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                    $('#module_remove_btn_' + ID).html('<button class="btn btn-sm btn-danger" onclick="remove_access(' + ID + ')"><i class="bx bx-trash"></i></button>');

                    const formData = new FormData();
                    formData.append('ID', ID);
                    formData.append('_token', "{{csrf_token()}}");

                    $.ajax({
                        type: 'POST',
                        url: "{{url('/')}}/remove_access_code_from_role",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(data) {
                            if (data.success) {
                                Swal.fire(
                                    'Success!',
                                    data.success,
                                    'success'
                                ).then((result) => {
                                    if (result.isConfirmed) {
                                        ajax_action('<?= url('/') ?>/load_full_role_view', 'LOAD_FULL_ROLE', '<?= $data_arr ?>', '<?= csrf_token() ?>');
                                    } else {
                                        ajax_action('<?= url('/') ?>/load_full_role_view', 'LOAD_FULL_ROLE', '<?= $data_arr ?>', '<?= csrf_token() ?>');
                                    }
                                });
                            }
                            if (data.error) {
                                Swal.fire(
                                    'Error!',
                                    data.error,
                                    'error'
                                );
                                $('#module_remove_btn_' + ID).html('<button class="btn btn-sm btn-danger" onclick="remove_access(' + ID + ')"><i class="bx bx-trash"></i></button>');
                            }

                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                error,
                                'error'
                            );
                            $('#module_remove_btn_' + ID).html('<button class="btn btn-sm btn-danger" onclick="remove_access(' + ID + ')"><i class="bx bx-trash"></i></button>');
                        }
                    });
                } else if (result.isDenied) {} else {

                }
            });
        }
    </script>