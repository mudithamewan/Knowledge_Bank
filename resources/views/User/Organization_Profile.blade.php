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

                    <div class="modal fade" id="profile_edit_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">EDIT PROFILE</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <span id="LOAD_EDIT_PROFILE_VIEW" class="VIEW_EMPTY"></span>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="upload_document_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">UPLOAD DOCUMENT</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="ORGANIZATION_UPLOAD_FORM" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="ORG_ID" id="ORG_ID" value="{{$ORGANIZATION_DETAILS->o_id}}">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <label for="">Documents</label>
                                                <input type="file" name="DOCUMENTS[]" id="DOCUMENTS" class="form-control" multiple>
                                            </div>
                                            <div class="col-xl-4 mt-3">
                                                <div id="ORGANIZATION_UPLOAD_FORM_BTN">
                                                    <button class="btn btn-primary w-100" type="submit">UPLOAD</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        $(document).ready(function() {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $('#ORGANIZATION_UPLOAD_FORM').on('submit', function(e) {
                                e.preventDefault();
                                $('#ORGANIZATION_UPLOAD_FORM_BTN').html(
                                    '<button class="btn btn-primary w-100" disabled>' +
                                    '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
                                );

                                var formData = new FormData(this);

                                $.ajax({
                                    url: "{{url('/')}}/upload_document_organization",
                                    type: "POST",
                                    data: formData,
                                    processData: false, // required for file uploads
                                    contentType: false, // required for file uploads
                                    dataType: 'json',
                                    success: function(data) {
                                        if (data.success) {
                                            Swal.fire('Success!', data.success, 'success')
                                                .then(() => {
                                                    location.reload();
                                                });
                                        }

                                        if (data.error) {
                                            Swal.fire('Error!', data.error, 'error');
                                            $('#ORGANIZATION_UPLOAD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPLOAD</button>');
                                        }
                                    },
                                    error: function(error) {
                                        Swal.fire('Error!', error, 'error');
                                        $('#ORGANIZATION_UPLOAD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPLOAD</button>');
                                    }
                                });
                            });
                        });
                    </script>



                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">ORGANIZATION PROFILE</h4>
                                <p class="">
                                    @if(in_array("11", session('USER_ACCESS_AREA')))
                                    @php $data_arr = json_encode(array('ORG_ID'=>$ORG_ID)); @endphp
                                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#profile_edit_modal" onclick="ajax_action('<?= url('/') ?>/load_edit_org_profile_view','LOAD_EDIT_PROFILE','{{$data_arr}}','{{csrf_token()}}')"><i class="bx bxs-edit label-icon"></i>
                                        EDIT
                                    </button>
                                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#upload_document_modal"><i class="bx bx-upload label-icon"></i>
                                        UPLOAD DOCUMENT
                                    </button>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-4">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">ORGANIZATION DETAILS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <center>
                                        <div class="mb-4">
                                            <img src="{{url('/')}}/assets/images/organization.jpg" alt="" class="avatar-xl rounded-circle img-thumbnail mb-3">

                                            <h4 class="mb-1">{{$ORGANIZATION_DETAILS->o_business_name}}</h4>
                                            <p class="mb-0">{!!$ORGANIZATION_DETAILS->o_is_active == 1 ? '<span class="badge badge-pill badge-soft-success font-size-11">ACTIVE</span>' : '<span class="badge badge-pill badge-soft-danger font-size-11">INACTIVE</span>'!!}</p>
                                        </div>
                                    </center>
                                    <div class="table-responsive pt-4 border-top">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>NAME</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>BUSINESS NAME</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_business_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>CONTACT</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_contact}}</td>
                                            </tr>
                                            <tr>
                                                <th>EMAIL</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_email}}</td>
                                            </tr>
                                            <tr>
                                                <th>ADDRESS</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_address}}</td>
                                            </tr>
                                            <tr>
                                                <th>BUSINESS REG. NO.</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_br_number}}</td>
                                            </tr>
                                            @if($ORGANIZATION_DETAILS->o_is_vat_registered == 1)
                                            <tr>
                                                <th>VAT REG. NO.</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_vat_registered_number}}</td>
                                            </tr>
                                            <tr>
                                                <th>VAT REG. DATE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_vat_registered_date}}</td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <th>BANK CODE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_bank_code}}</td>
                                            </tr>
                                            <tr>
                                                <th>BRANCH CODE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_bank_branch_code}}</td>
                                            </tr>
                                            <tr>
                                                <th>ACCOUNT NO.</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORGANIZATION_DETAILS->o_account_number}}</td>
                                            </tr>
                                        </table>

                                        <table width="100%">
                                            <tr>
                                                <th>TAGS</th>
                                            </tr>
                                            <tr>
                                                <td style="padding-top:10px">
                                                    @foreach($ORGANIZATION_TYPE_DETAILS as $DATA)
                                                    <span class="badge badge-pill badge-soft-dark font-size-12">{{$DATA->mot_name}}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
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
                                        @if(empty($ORGANIZATION_DOCUMENTS))
                                        Document not attached
                                        @else
                                        @foreach($ORGANIZATION_DOCUMENTS as $DOC)
                                        <div class="col-xl-2 m-2">

                                            <div class="card shadow-sm border-0 mb-0 h-100">
                                                <a href="{{url('/')}}/{{ $DOC->od_file_path }}" target="_blank" download>
                                                    <div class="card-body text-center mb-0">
                                                        <i class="bx bx-file display-5 text-dark"></i>
                                                        <p class="text-truncate text-dark mb-0" title="{{ $DOC->od_original_name }}">
                                                            {{ $DOC->od_original_name }}
                                                        </p>
                                                    </div>
                                                </a>

                                                @if(in_array("11", session('USER_ACCESS_AREA')))
                                                <div class="text-center mb-2">
                                                    <a href="#{{$DOC->od_id}}" class="text-danger" onclick="remove_doc('{{$DOC->od_id}}')">Remove</a>
                                                </div>
                                                @endif

                                            </div>

                                        </div>
                                        @endforeach
                                        @endif
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
    function remove_doc(id) {

        Swal.fire({
            title: "Do you want to remove this document?",
            showDenyButton: true,
            showCancelButton: false,
            confirmButtonText: "YES",
            denyButtonText: "NO"
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                var link = "{{url('/')}}/remove_org_doc";

                const formData = new FormData();
                formData.append('DOC_ID', id);
                formData.append('_token', "{{csrf_token()}}");

                $.ajax({
                    type: 'POST',
                    url: link,
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(data) {

                        if (data.success) {
                            location.reload();
                        }

                        if (data.error) {
                            Swal.fire(
                                'Error!',
                                data.error,
                                'error'
                            );
                        }

                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error!',
                            error,
                            'error'
                        );
                    }
                });
            }
        });



    }
</script>