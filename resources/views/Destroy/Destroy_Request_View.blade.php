<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <link rel="stylesheet" href="{{url('/')}}/assets/libs/owl.carousel/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="{{url('/')}}/assets/libs/owl.carousel/assets/owl.theme.default.min.css">
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
                                <h4 class="mb-sm-0 font-size-18">DESTROY REQUEST VIEW</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-3">
                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">ACTIONS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    @if(in_array("43", session('USER_ACCESS_AREA')))

                                    @php $AA_ID = 0; @endphp
                                    @foreach($APPROVALS as $APPROVAL)
                                    @if($APPROVAL->aa_id == 1)
                                    @php $AA_ID = $APPROVAL->dra_id; @endphp
                                    @endif
                                    @endforeach

                                    @if($AA_ID != 0)
                                    <div class="modal fade" id="approval_view_{{$AA_ID}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                                        <div class="modal-dialog modal-sm modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">APPROVAL</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form id="APPROVAL_FORM">
                                                        @csrf
                                                        <input type="hidden" name="DRA_ID" id="DRA_ID" value="{{$AA_ID}}">
                                                        <div class="row">
                                                            <div class="col-lg-12 mt-2">
                                                                <label>ACTION <span class="text-danger">*</span></label>
                                                                <select name="AA_ID" id="AA_ID" class="form-select">
                                                                    <option value="" selected disabled hidden>Choose..</option>
                                                                    @foreach($APPROVALS_ACTION as $ACTION)
                                                                    @if($ACTION->aa_id != 1)
                                                                    <option value="{{$ACTION->aa_id}}">{{$ACTION->aa_name}}</option>
                                                                    @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-lg-12 mt-2">
                                                                <label>REMARK</label>
                                                                <textarea name="REMARK" id="REMARK" rows="3" class="form-control"></textarea>
                                                            </div>
                                                            <div class="col-lg-12 mt-2">
                                                                <div id="APPROVAL_FORM_BTN" style="margin-top: 27px;">
                                                                    <button class="btn btn-primary w-100" type="submit">SUBMIT</button>
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

                                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#approval_view_{{$AA_ID}}">APPROVAL</button>
                                    @endif
                                    <script>
                                        // Ajax for add grade
                                        $(document).ready(function() {
                                            $('#APPROVAL_FORM').on('submit', function(e) {
                                                e.preventDefault();
                                                $('#APPROVAL_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16"></i> VERIFYING..</button>');
                                                $.post("{{url('/')}}/destroy_request_approve_action", $(this).serialize(), function(data) {
                                                    if (data.success) {
                                                        Swal.fire('Success!', data.success, 'success').then(() => {
                                                            location.reload();
                                                        });
                                                    }
                                                    if (data.error) {
                                                        Swal.fire('Error!', data.error, 'error');
                                                        $('#APPROVAL_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                                                    }
                                                }, 'json').fail(function(err) {
                                                    Swal.fire('Error!', 'Something went wrong', 'error');
                                                    $('#APPROVAL_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                                                });
                                            });
                                        });
                                    </script>
                                    @endif

                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">REQUEST DETAILS</h5>

                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <center>
                                        <div class="mb-4">
                                            <img src="{{url('/')}}/assets/destroy.png" alt="" class="" width="45%"><br>
                                            <span class="badge badge-pill badge-soft-primary font-size-11 mt-3">{{$DESTROY_DETAILS->drs_name}}</span>
                                        </div>
                                    </center>

                                    <div class="table-responsive pt-4 border-top">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>REQUEST CODE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{str_pad($DESTROY_DETAILS->dr_id, 5, '0', STR_PAD_LEFT)}}</td>
                                            </tr>
                                            <tr>
                                                <th>REQUEST DATE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$DESTROY_DETAILS->dr_inserted_date}}</td>
                                            </tr>
                                            <tr>
                                                <th>REQUEST BY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$DESTROY_DETAILS->su_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>LOCATION</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$DESTROY_DETAILS->mw_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL ITEMS</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$DESTROY_DETAILS->dr_item_count}}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL QTY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$DESTROY_DETAILS->dr_tot_qty}}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL VALUE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>LKR {{number_format($DESTROY_DETAILS->dr_tot_amount,2)}}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xl-9">
                            <div class="card">


                                <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                    <h5 class="card-title text-primary mb-0">DESTROY ITEMS</h5>

                                    @if(in_array("44", session('USER_ACCESS_AREA')) && $DESTROY_DETAILS->drs_id == 1)
                                    <div id="edit_btn_view">
                                        <button class="btn btn-sm btn-primary" onclick="show_edit_view('open')">EDIT</button>
                                    </div>
                                    @endif

                                </div>

                                <input type="hidden" name="REMOVED_IDS" id="REMOVED_IDS">
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <tr class="table-light">
                                            <th>ISBN</th>
                                            <th>PRODUCT</th>
                                            <th>SELLING PRICE</th>
                                            <th>QTY</th>
                                            <th>TOTAL VALUE</th>
                                            <th class="edit_view_item" style="display: none;">ACTION</th>
                                        </tr>
                                        @php $TOT_VALUE = 0; @endphp
                                        @foreach($DESTROY_ITEMS as $index => $ITEM)
                                        @php $TOT_VALUE = $TOT_VALUE + ($ITEM->dri_selling_amount*$ITEM->dri_qty); @endphp
                                        <tr id="{{$ITEM->dri_id}}_ROW">
                                            <td>{{$ITEM->p_isbn}}</td>
                                            <td>{{$ITEM->p_name}}</td>
                                            <td style="text-align: right;">{{number_format($ITEM->dri_selling_amount,2)}}</td>
                                            <td style="text-align: center;">{{$ITEM->dri_qty}}</td>
                                            <td style="text-align: right;">{{number_format($ITEM->dri_selling_amount*$ITEM->dri_qty,2)}}</td>
                                            <td style="text-align: center; display: none;" class="edit_view_item" id="{{$ITEM->dri_id}}_REMOVE_BTN">
                                                <button class="btn btn-outline-danger waves-effect waves-light btn-sm" onclick="remove_row('{{$ITEM->dri_id}}')"><i class="bx bx-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3"></td>
                                            <th>TOTAL</th>
                                            <th style="text-align: right;">{{number_format($TOT_VALUE,2)}}</th>
                                            <td class="edit_view_item" style="text-align: center; display: none;" id="edit_btn">
                                                <button class="btn btn-success btn-sm w-100" onclick="update_destroy_items()">UPDATE</button>
                                            </td>
                                        </tr>
                                    </table>


                                    @if(count($REMOVED_DESTROY_ITEMS) > 0)
                                    <div class="me-2 mb-3 mt-4">
                                        <h5 class="card-title mt-1 mb-0 text-danger">REMOVED ITEMS</h5>
                                    </div>
                                    <table class="table table-bordered table-sm">
                                        <tr class="table-light">
                                            <th>ISBN</th>
                                            <th>PRODUCT</th>
                                            <th>SELLING PRICE</th>
                                            <th>QTY</th>
                                            <th>TOTAL VALUE</th>
                                        </tr>
                                        @php $TOT_VALUE = 0; @endphp
                                        @foreach($REMOVED_DESTROY_ITEMS as $index => $ITEM)
                                        @php $TOT_VALUE = $TOT_VALUE + ($ITEM->dri_selling_amount*$ITEM->dri_qty); @endphp
                                        <tr>
                                            <td>{{$ITEM->p_isbn}}</td>
                                            <td>{{$ITEM->p_name}}</td>
                                            <td style="text-align: right;">{{number_format($ITEM->dri_selling_amount,2)}}</td>
                                            <td style="text-align: center;">{{$ITEM->dri_qty}}</td>
                                            <td style="text-align: right;">{{number_format($ITEM->dri_selling_amount*$ITEM->dri_qty,2)}}</td>
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="3"></td>
                                            <th>TOTAL</th>
                                            <th style="text-align: right;">{{number_format($TOT_VALUE,2)}}</th>
                                        </tr>
                                    </table>
                                    @endif
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">APPROVALS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div style="overflow-x: auto; white-space: nowrap;">
                                        <ul class="verti-timeline list-unstyled">

                                            @foreach($APPROVALS as $APPROVAL)
                                            <li class="event-list">
                                                <div class="event-timeline-dot">
                                                    <i class="bx bxs-right-arrow  font-size-16"></i>
                                                </div>
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <h5 class="font-size-12"><small><i>{{$APPROVAL->dra_inserted_date}}</i></small>
                                                            <i class="bx bx-right-arrow-alt font-size-16 text-primary  ms-2"></i>
                                                        </h5>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="badge badge-pill badge-soft-{{$APPROVAL->aa_color}} font-size-11"> {{$APPROVAL->aa_name}}</span>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;<small>{{$APPROVAL->dra_action_date}}</small>
                                                        @if($APPROVAL->aa_id != 1)
                                                        <div id="approved_person_4646" class="mb-3">
                                                            <div class="d-flex mt-2">
                                                                <div class="flex-shrink-0 align-self-center me-3"> <img src="{{url('/')}}/assets/images/man.jpg" class="rounded-circle avatar-xs" alt=""> </div>
                                                                <div class="flex-grow-1 overflow-hidden">
                                                                    <h5 class="text-muted font-size-14" style="margin-bottom: 1px;">{{$APPROVAL->action_user}} ({{$APPROVAL->action_nic}})</h5> <small>
                                                                    </small>

                                                                    @if($APPROVAL->aa_color == 'success')
                                                                    @php $color ='#ccf0e3'; @endphp
                                                                    @else
                                                                    @php @endphp
                                                                    @php $color ='#fde4e4'; @endphp
                                                                    @endif

                                                                    <div class="font-size-14 mb-0 p-2 ps-3 mt-2" style="border-left: 3px solid <?= $color ?>; background-color:#f8f8fb">
                                                                        <footer class="blockquote-footer mt-0 mb-0 font-size-12">
                                                                            {{empty($APPROVAL->ora_remark)?'N/A':$APPROVAL->dra_remark }}
                                                                        </footer>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </li>
                                            @endforeach


                                        </ul>
                                    </div>

                                </div>
                            </div>


                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">TIMELINE</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <div class="hori-timeline">
                                        <div class="owl-carousel owl-theme navs-carousel events" id="timeline-carousel-2">
                                            @foreach ($TIMELINE as $val)
                                            <div class="item event-list">
                                                <div>
                                                    <div class="event-date">
                                                        <div class="text-primary mb-1">{{ $val->drt_inserted_date }}</div>
                                                        <h5 class="mb-4">{{ $val->drt_title }}</h5>
                                                    </div>
                                                    <div class="event-down-icon">
                                                        <i class="bx bx-down-arrow-circle h1 text-primary down-arrow-icon"></i>
                                                    </div>

                                                    <div class="mt-3 px-3">
                                                        <p class="text-muted">
                                                            {{ $val->drt_description }} <br>
                                                            <b>By <i class='bx bx-right-arrow-alt text-primary align-middle'></i>{{ $val->su_name }} </b><br><small class='font-5'>{{ $val->su_contact_number }} | {{ $val->su_email }}</small>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>

                    </div>

                </div>
            </div>




            @include('Layout/footer')
            <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
            <script src=" {{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
            <script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>
            <script src="{{url('/')}}/assets/libs/owl.carousel/owl.carousel.min.js"></script>
            <script src="{{url('/')}}/assets/js/pages/timeline.init.js"></script>
            <script>
                $(document).ready(function() {
                    $('#REMOVED_IDS').val('');

                    $('#timeline-carousel-2').owlCarousel({
                        loop: false,
                        nav: true,
                        dots: false,
                        responsive: {
                            0: { // Mobile screens
                                items: 1
                            },
                            768: { // Tablet and larger screens
                                items: 4
                            }
                        }
                    });
                });

                function show_edit_view(type) {
                    if (type == 'open') {
                        $('#edit_btn_view').html(`<button class="btn btn-sm btn-danger" onclick="show_edit_view('close')">CLOSE</button>`);
                        $('.edit_view_item').show();
                    } else {
                        $('#edit_btn_view').html(`<button class="btn btn-sm btn-primary" onclick="show_edit_view('open')">EDIT</button>`);
                        $('.edit_view_item').hide();
                    }
                }

                function getRemovedIds() {
                    let val = $('#REMOVED_IDS').val();
                    return val ? JSON.parse(val) : [];
                }

                function remove_row(id) {

                    // Highlight row
                    document.getElementById(id + "_ROW").style.backgroundColor = "#fcdada";

                    // Change button
                    $('#' + id + '_REMOVE_BTN').html(`
                        <button class="btn btn-outline-success btn-sm" onclick="add_row('${id}')">
                            <i class="bx bx-plus-medical"></i>
                        </button>
                    `);

                    // Get existing array
                    let removedIds = getRemovedIds();

                    // Add if not already added
                    if (!removedIds.includes(id)) {
                        removedIds.push(id);
                    }

                    // Save back to hidden input
                    $('#REMOVED_IDS').val(JSON.stringify(removedIds));
                }

                function add_row(id) {
                    // Reset color
                    document.getElementById(id + "_ROW").style.backgroundColor = "white";

                    // Change button
                    $('#' + id + '_REMOVE_BTN').html(`
                        <button class="btn btn-outline-danger btn-sm" onclick="remove_row('${id}')">
                            <i class="bx bx-trash"></i>
                        </button>
                    `);

                    // Get existing array
                    let removedIds = getRemovedIds();

                    // Remove ID
                    removedIds = removedIds.filter(item => item != id);

                    // Save back
                    $('#REMOVED_IDS').val(JSON.stringify(removedIds));
                }

                function update_destroy_items() {
                    $('#edit_btn').html(`<button class="btn btn-success btn-sm w-100" disabled><i class="bx bx-loader bx-spin font-size-16"></i> UPDATING..</button>`);

                    const formData = new FormData();
                    formData.append('REMOVED_IDS', $('#REMOVED_IDS').val());
                    formData.append('DR_ID', '{{$REQ_ID}}');
                    formData.append('_token', "{{csrf_token()}}");

                    $.ajax({
                        type: 'POST',
                        url: "<?php echo url('/') . "/update_destroy_items" ?>",
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
                                        location.reload();
                                    } else if (result.isDenied) {
                                        location.reload();
                                    } else {
                                        location.reload();
                                    }
                                });
                            }
                            if (data.error) {
                                Swal.fire(
                                    'Error!',
                                    data.error,
                                    'error'
                                );
                                $('#edit_btn').html(`<button class="btn btn-success btn-sm w-100" onclick="update_destroy_items()">UPDATE</button>`);
                            }

                        },
                        error: function(xhr, status, error) {
                            Swal.fire(
                                'Error!',
                                error,
                                'error'
                            );
                            $('#edit_btn').html(`<button class="btn btn-success btn-sm w-100" onclick="update_destroy_items()">UPDATE</button>`);
                        }
                    });
                }
            </script>
        </div>
    </div>


</body>



</html>