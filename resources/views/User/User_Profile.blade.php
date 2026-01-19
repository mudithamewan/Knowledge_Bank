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

                    <div class="modal fade" id="change_password_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="staticBackdropLabel">CHANGE PASSWORD</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <span id="LOAD_CHANGE_PASSWORD_VIEW" class="VIEW_EMPTY"></span>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">USER PROFILE</h4>
                                <p class="">
                                    @if(in_array("3", session('USER_ACCESS_AREA')))
                                    @php $data_arr = json_encode(array('USER_ID'=>$USER_ID)); @endphp
                                    <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#profile_edit_modal" onclick="ajax_action('<?= url('/') ?>/load_edit_profile_view','LOAD_EDIT_PROFILE','{{$data_arr}}','{{csrf_token()}}')"><i class="bx bxs-edit label-icon"></i>
                                        EDIT
                                    </button>
                                    @endif
                                    @if(in_array("4", session('USER_ACCESS_AREA')))
                                    @php $data_arr = json_encode(array('USER_ID'=>$USER_ID)); @endphp
                                    <button type="button" class="btn btn-danger waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#change_password_modal" onclick="ajax_action('<?= url('/') ?>/load_change_password_view','LOAD_CHANGE_PASSWORD','{{$data_arr}}','{{csrf_token()}}')"><i class="bx bxs-edit label-icon"></i> CHANGE PASSWORD</button>
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
                                            <h5 class="card-title mt-1 mb-0 text-primary">PROFILE DETAILS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <center>
                                        <div class="mb-4">
                                            @if($USER_DETAILS->su_gender == 'Male')
                                            <img src="{{url('/')}}/assets/images/man.jpg" alt="" class="avatar-xl rounded-circle img-thumbnail mb-3">
                                            @else
                                            <img src="{{url('/')}}/assets/images/woman.jpg" alt="" class="avatar-xl rounded-circle img-thumbnail mb-3">
                                            @endif


                                            <h4 class="mb-1">{{$USER_DETAILS->su_name}}</h4>
                                            <p class="mb-0">{{$USER_DETAILS->su_nic}} &nbsp;&nbsp;{!!$USER_DETAILS->su_is_active == 1 ? '<span class="badge badge-pill badge-soft-success font-size-11">ACTIVE</span>' : '<span class="badge badge-pill badge-soft-danger font-size-11">INACTIVE</span>'!!}</p>
                                        </div>
                                    </center>

                                    <div class="table-responsive pt-4 border-top">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>NAME</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>NIC</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_nic}}</td>
                                            </tr>
                                            <tr>
                                                <th>GENDER</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_gender}}</td>
                                            </tr>
                                            <tr>
                                                <th>CONTACT NO.</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_contact_number}}</td>
                                            </tr>
                                            <tr>
                                                <th>EMAIL</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_email}}</td>
                                            </tr>
                                            <tr>
                                                <th>ADDRESS</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$USER_DETAILS->su_address_line_01}} {{$USER_DETAILS->su_address_line_02}} {{$USER_DETAILS->su_address_line_03}}</td>
                                            </tr>
                                        </table>
                                    </div>

                                    <hr>

                                    <div class="mb-2"><b>WAREHOUSES</b></div>

                                    @foreach($WAREHOUSES as $WAREHOUSE)
                                    <span class="badge badge-pill badge-soft-secondary font-size-12 me-1">{{$WAREHOUSE->mw_name}}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-8">

                            <div class="row">

                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class=" bx bx-calendar h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">JOINED DATE</p>
                                            <h5 class="mb-0">{{ date('Y M d', strtotime($USER_DETAILS->su_inserted_date)) }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class=" bx bx-history h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">LAST LOGIN DATE</p>
                                            <h5 class="mb-0">{{ $LAST_LOGIN_DETAILS == true ? date('Y M d', strtotime($LAST_LOGIN_DETAILS->ull_inserted_date)) : '----' }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4">
                                    <div class="card d-flex flex-row align-items-center">
                                        <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-start" style="width: 70px; height: 70px;">
                                            <i class=" bx bx-briefcase h1 text-white mb-0"></i>
                                        </div>
                                        <div class="ms-3">
                                            <p class="text-muted mb-2">TODAY"S WORK</p>
                                            <h5 class="mb-0">{{$TODAY_WORK_COUNT}}</h5>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12">
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
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#INVOICES" role="tab" onclick="ajax_action('<?= url('/') ?>/load_invoices_by_user','INVOICES','<?= htmlspecialchars(json_encode(['USER_ID' => $USER_ID]), ENT_QUOTES, 'UTF-8') ?>','{{ csrf_token() }}')">
                                                        <span class="d-block d-sm-none"><i class="bx bxs-file"></i></span>
                                                        <span class="d-none d-sm-block">INVOICES</span>
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-bs-toggle="tab" href="#TIMELINE" role="tab" onclick="ajax_action('<?= url('/') ?>/load_timeline_by_user','TIMELINE','<?= htmlspecialchars(json_encode(['USER_ID' => $USER_ID]), ENT_QUOTES, 'UTF-8') ?>','{{ csrf_token() }}')">
                                                        <span class="d-block d-sm-none"><i class="bx bxs-file"></i></span>
                                                        <span class="d-none d-sm-block">TIMELINE</span>
                                                    </a>
                                                </li>
                                            </ul>

                                            <div class="tab-content m-2 text-muted">
                                                <div class="tab-pane active" id="INVOICES" role="tabpanel">
                                                    <h6 class="mt-3"><b>INVOICES</b></h6>
                                                    <div id="INVOICES_VIEW" class="border rounded p-3 VIEW_EMPTY" style="margin-top: 8px;"></div>
                                                </div>
                                                <div class="tab-pane" id="TIMELINE" role="tabpanel">
                                                    <h6 class="mt-3"><b>TIMELINE</b></h6>
                                                    <div id="TIMELINE_VIEW" class="border rounded p-3 VIEW_EMPTY" style="margin-top: 8px;"></div>
                                                </div>
                                            </div>


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
    ajax_action('<?= url('/') ?>/load_invoices_by_user', 'INVOICES', '<?= json_encode(['USER_ID' => $USER_ID]) ?>', '{{ csrf_token() }}');
</script>