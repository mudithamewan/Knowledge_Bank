<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <link href="{{url('/')}}/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
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
                                <h4 class="mb-sm-0 font-size-18">MANAGE USER ROLES</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="card">
                        <div class="card-header bg-transparent border-bottom">
                            <div class="d-flex flex-wrap align-items-start">
                                <div class="me-2">
                                    <h5 class="card-title mt-1 mb-0 text-primary">ROLES</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="modal fade" id="full_view_role" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="staticBackdropLabel">ROLE MANAGER</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="LOAD_FULL_ROLE_VIEW"></div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="table-responsive">
                                <table id="roles" class="table table-sm table-bordered dt-responsive  nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th style="white-space: nowrap;">Role Name</th>
                                            <th style="white-space: nowrap;">Role Created Date</th>
                                            <th style="white-space: nowrap;">Status</th>
                                            <th style="text-align: center; white-space: nowrap;"><i class="bx bx-dots-horizontal-rounded "></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ROLES as $ROLE)
                                        <tr>
                                            <td>{{$ROLE->sr_name}}</td>
                                            <td>{{$ROLE->sr_inserted_date}}</td>
                                            <td>{!!$ROLE->sr_is_active == 1 ? '<span class="badge badge-pill badge-soft-success font-size-11">ACTIVE</span>' : '<span class="badge badge-pill badge-soft-danger font-size-11">INACTIVE</span>'!!}</td>
                                            <td>
                                                @php $data_arr = json_encode(array('ROLE_ID'=>$ROLE->sr_id)); @endphp
                                                <button class="btn btn-outline-primary btn-sm waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#full_view_role" onclick="ajax_action('<?= url('/') ?>/load_full_role_view','LOAD_FULL_ROLE','{{$data_arr}}','{{csrf_token()}}')">View</button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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

<script src="{{url('/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/datatables.init.js"></script>
<script>
    $(document).ready(function() {
        if ($('#roles').length) {
            $('#roles').DataTable();
        }
        if ($('#roles').length) {
            $('#roles').DataTable();
        }
    });
</script>