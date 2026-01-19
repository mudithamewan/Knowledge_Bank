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

                    <div class="modal fade" id="profile_edit_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">EDIT PRODUCT PROFILE</h5>
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
                                    <form id="PRODUCT_UPLOAD_FORM" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="P_ID" id="P_ID" value="{{$PRODUCT_DETAILS->p_id}}">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <label for="">Documents</label>
                                                <input type="file" name="DOCUMENT" id="DOCUMENT" class="form-control">
                                            </div>
                                            <div class="col-xl-4 mt-3">
                                                <div id="PRODUCT_UPLOAD_FORM_BTN">
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

                            $('#PRODUCT_UPLOAD_FORM').on('submit', function(e) {
                                e.preventDefault();
                                $('#PRODUCT_UPLOAD_FORM_BTN').html(
                                    '<button class="btn btn-primary w-100" disabled>' +
                                    '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
                                );

                                var formData = new FormData(this);

                                $.ajax({
                                    url: "{{url('/')}}/upload_product_cover",
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
                                            $('#PRODUCT_UPLOAD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPLOAD</button>');
                                        }
                                    },
                                    error: function(error) {
                                        Swal.fire('Error!', error, 'error');
                                        $('#PRODUCT_UPLOAD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPLOAD</button>');
                                    }
                                });
                            });
                        });
                    </script>


                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Product Profile</h4>

                                @if(in_array("7", session('USER_ACCESS_AREA')))
                                <p class="">
                                    @php $data_arr = json_encode(array('PRODUCT_ID'=>$PRODUCT_ID)); @endphp
                                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#profile_edit_modal" onclick="ajax_action('<?= url('/') ?>/load_edit_product_profile_view','LOAD_EDIT_PROFILE','{{$data_arr}}','{{csrf_token()}}')"><i class="bx bxs-edit label-icon"></i>
                                        EDIT
                                    </button>
                                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#upload_document_modal"><i class="bx bx-upload label-icon"></i>
                                        UPLOAD COVER
                                    </button>
                                </p>
                                @endif
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
                                            <h5 class="card-title mt-1 mb-0 text-primary">PRODUCT DETAILS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <center>
                                        <div class="mb-4">
                                            @php $image = "assets/images/empty.jpg"; @endphp
                                            @if(!empty($PRODUCT_DETAILS->pd_file_path))
                                            @php $image = $PRODUCT_DETAILS->pd_file_path; @endphp
                                            @endif

                                            <img src="{{url('/')}}/{{$image}}" alt="" class="" width="60%">

                                            <h4 class="mb-1 mt-3">{{$PRODUCT_DETAILS->p_name}}</h4>
                                            {{$PRODUCT_DETAILS->p_isbn}}&nbsp;&nbsp;&nbsp;{!!$PRODUCT_DETAILS->p_is_active == 1 ? '<span class="badge badge-pill badge-soft-success font-size-11">ACTIVE</span>' : '<span class="badge badge-pill badge-soft-danger font-size-11">INACTIVE</span>'!!}</p>
                                        </div>
                                    </center>
                                    <div class="table-responsive pt-4 border-top">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>NAME</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>ISBN</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_isbn}}</td>
                                            </tr>
                                            <tr>
                                                <th>AUTHOR</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_author}}</td>
                                            </tr>
                                            <tr>
                                                <th>PUBLISHER</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->o_business_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>MEDIUM</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->mm_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>GRADES</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>
                                                    @php
                                                    $text = "";
                                                    @endphp

                                                    @foreach($PRODUCT_GRADES as $GRADE)
                                                    @php
                                                    $text .= $GRADE->mg_name.", ";
                                                    @endphp
                                                    @endforeach

                                                    {{ rtrim($text, ', ') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>SUBJECTS</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>
                                                    @php
                                                    $text = "";
                                                    @endphp

                                                    @foreach($PRODUCT_SUBJECTS as $SUBJECT)
                                                    @php
                                                    $text .= $SUBJECT->ms_name.", ";
                                                    @endphp
                                                    @endforeach

                                                    {{ rtrim($text, ', ') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>CATEGORY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->mc_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>SUB CATEGORY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->msc_name}}</td>
                                            </tr>
                                        </table>

                                        <div class="section-title-wrapper mt-4">
                                            <div class="section-line"></div>
                                            <div class="section-title"><b>ADDITIONAL INFORMATION</b></div>
                                            <div class="section-line"></div>
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <th>EDITION</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_edition}}</td>
                                            </tr>
                                            <tr>
                                                <th>PUBLISHED YEAR</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_published_year}}</td>
                                            </tr>
                                            <tr>
                                                <th>PAGE COUNT</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->p_page_count}}</td>
                                            </tr>
                                            <tr>
                                                <th>FORMAT</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$PRODUCT_DETAILS->mbf_name}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-8">

                            <div class="row">
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-log-in h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY IN COUNT</p>
                                            <h5 class="mb-0">{{$TODAY_STOCK_IN_COUNT}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-log-out h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY OUT COUNT</p>
                                            <h5 class="mb-0">{{$TODAY_STOCK_OUT_COUNT}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class="bx bx-package h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">AVAILABLE COUNT</p>
                                            <h5 class="mb-0">{{$PRODUCT_AVA_COUNT == true? $PRODUCT_AVA_COUNT->as_available_qty : 0}}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>




                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">MORE DETAILS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#PRODUCT_STOCK_IN" role="tab" onclick="ajax_action('<?= url('/') ?>/load_in_list_by_product','PRODUCT_STOCK_IN','<?= htmlspecialchars(json_encode(['PRODUCT_ID' => $PRODUCT_DETAILS->p_id]), ENT_QUOTES, 'UTF-8') ?>','{{ csrf_token() }}')">
                                                <span class="d-block d-sm-none"><i class="bx bxs-file"></i></span>
                                                <span class="d-none d-sm-block">STOCK IN</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#PRODUCT_STOCK_OUT" role="tab" onclick="ajax_action('<?= url('/') ?>/load_out_list_by_product','PRODUCT_STOCK_OUT','<?= htmlspecialchars(json_encode(['PRODUCT_ID' => $PRODUCT_DETAILS->p_id]), ENT_QUOTES, 'UTF-8') ?>','{{ csrf_token() }}')">
                                                <span class="d-block d-sm-none"><i class="bx bxs-calculator"></i></span>
                                                <span class="d-none d-sm-block">STOCK OUT</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#AVAILABLE_STOCK" role="tab" onclick="ajax_action('<?= url('/') ?>/load_available_stock_by_product','AVAILABLE_STOCK','<?= htmlspecialchars(json_encode(['PRODUCT_ID' => $PRODUCT_DETAILS->p_id]), ENT_QUOTES, 'UTF-8') ?>','{{ csrf_token() }}')">
                                                <span class="d-block d-sm-none"><i class="bx bxs-calculator"></i></span>
                                                <span class="d-none d-sm-block">AVAILABLE STOCK</span>
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content m-2 text-muted">
                                        <div class="tab-pane active" id="PRODUCT_STOCK_IN" role="tabpanel">
                                            <h6 class="mt-3"><b>STOCK IN</b></h6>
                                            <div id="PRODUCT_STOCK_IN_VIEW" class="border rounded p-3 VIEW_EMPTY" style="margin-top: 8px;"></div>
                                        </div>
                                        <div class="tab-pane " id="PRODUCT_STOCK_OUT" role="tabpanel">
                                            <h6 class="mt-3"><b>STOCK OUT</b></h6>
                                            <div id="PRODUCT_STOCK_OUT_VIEW" class="border rounded p-3 VIEW_EMPTY" style="margin-top: 8px;"></div>
                                        </div>
                                        <div class="tab-pane " id="AVAILABLE_STOCK" role="tabpanel">
                                            <h6 class="mt-3"><b>AVAILABLE STOCK</b></h6>
                                            <div id="AVAILABLE_STOCK_VIEW" class="border rounded p-3 VIEW_EMPTY" style="margin-top: 8px;"></div>
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
    ajax_action('<?= url('/') ?>/load_in_list_by_product', 'PRODUCT_STOCK_IN', '<?= json_encode(['PRODUCT_ID' => $PRODUCT_DETAILS->p_id]) ?>', '{{ csrf_token() }}');
</script>