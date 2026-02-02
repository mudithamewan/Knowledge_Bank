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
                                <h4 class="mb-sm-0 font-size-18">ORDER VIEW</h4>
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
                                    @if(in_array("26", session('USER_ACCESS_AREA')))

                                    @php $AA_ID = 0; @endphp
                                    @foreach($APPROVALS as $APPROVAL)
                                    @if($APPROVAL->aa_id == 1)
                                    @php $AA_ID = $APPROVAL->ora_id; @endphp
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
                                                        <input type="hidden" name="ORA_ID" id="ORA_ID" value="{{$AA_ID}}">
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
                                                $.post("{{url('/')}}/order_approve_action", $(this).serialize(), function(data) {
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




                                    @if(in_array("28", session('USER_ACCESS_AREA')) && $ORDER->os_id == 2)
                                    <form id="COLLECT_FORM">
                                        @csrf
                                        <input type="hidden" name="OR_ID" id="OR_ID" value="{{$ORDER->or_id}}">
                                        <div id="COLLECT_FORM_BTN">
                                            <button class="btn btn-primary w-100" type="submit">COLLECTED</button>
                                        </div>
                                    </form>
                                    <script>
                                        // Ajax for add grade
                                        $(document).ready(function() {
                                            $('#COLLECT_FORM').on('submit', function(e) {
                                                e.preventDefault();
                                                $('#COLLECT_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16"></i> VERIFYING..</button>');
                                                $.post("{{url('/')}}/order_collect_action", $(this).serialize(), function(data) {
                                                    if (data.success) {
                                                        Swal.fire('Success!', data.success, 'success').then(() => {
                                                            location.reload();
                                                        });
                                                    }
                                                    if (data.error) {
                                                        Swal.fire('Error!', data.error, 'error');
                                                        $('#COLLECT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">COLLECTED</button>');
                                                    }
                                                }, 'json').fail(function(err) {
                                                    Swal.fire('Error!', 'Something went wrong', 'error');
                                                    $('#COLLECT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">COLLECTED</button>');
                                                });
                                            });
                                        });
                                    </script>
                                    @endif

                                    @if(in_array("31", session('USER_ACCESS_AREA')) && ($ORDER->os_id == 2 || $ORDER->os_id == 4))
                                    <a href="{{url('/')}}/Download_Order_Form/{{urlencode(base64_encode($ORDER->or_id))}}" class="btn btn-success w-100 mt-2 waves-effect btn-label waves-light"><i class="bx bx-download label-icon"></i> ORDER FORM</a>
                                    @endif

                                </div>
                            </div>


                            <div class="card">
                                <div class="card-header bg-transparent border-bottom">
                                    <div class="d-flex flex-wrap align-items-start">
                                        <div class="me-2">
                                            <h5 class="card-title mt-1 mb-0 text-primary">ORDER DETAILS</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">

                                    <center>
                                        <div class="mb-4">
                                            <img src="{{url('/')}}/assets/3500833.png" alt="" class="" width="30%"> <br>
                                            <span class="badge badge-pill badge-soft-success font-size-11 mt-3">ACTIVE</span>
                                        </div>
                                    </center>

                                    <div class="table-responsive pt-4 border-top">
                                        <table class="table table-sm">
                                            <tr>
                                                <th>ORDER CODE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{str_pad($ORDER->or_id, 5, '0', STR_PAD_LEFT)}}</td>
                                            </tr>
                                            <tr>
                                                <th>ORDER DATE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->or_inserted_date}}</td>
                                            </tr>
                                            <tr>
                                                <th>ORDER BY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->su_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>OUTLET</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->mw_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>STAGE</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->os_name}}</td>
                                            </tr>
                                            <tr>
                                                <th>CUSTOMER COUNT</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->or_customers_count}}</td>
                                            </tr>
                                            <tr>
                                                <th>TOTAL QTY</th>
                                                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                                                <td>{{$ORDER->or_total_qty}}</td>
                                            </tr>
                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-xl-9">
                            <div class="card">


                                <div class="card-header d-flex justify-content-between align-items-center bg-transparent border-bottom">
                                    <h5 class="card-title text-primary mb-0">ORDER ITEMS</h5>

                                    @if(in_array("27", session('USER_ACCESS_AREA')) && $ORDER->os_id == 1)
                                    <div id="edit_btn_view">
                                        <button class="btn btn-sm btn-primary" onclick="show_edit_view('open')">EDIT</button>
                                    </div>
                                    @endif

                                </div>


                                <div class="card-body">
                                    <form id="ORDER_EDIT_FORM">
                                        @csrf()
                                        <input type="hidden" name="OR_ID" id="OR_ID" value="{{$OR_ID}}">
                                        <div class="table-responsive pb-3 border-bottom border-primary">

                                            <table class="table table-bordered table-sm">
                                                @php $CUSTOMER_ID = '-1'; @endphp

                                                @foreach($ORDER_ITEM as $ITEM)

                                                @if($CUSTOMER_ID != $ITEM->ori_o_id)
                                                @php $CUSTOMER_ID = $ITEM->ori_o_id; @endphp
                                                <tr class="table-light">
                                                    <th colspan="7" class="p-3">{{strtoupper($ITEM->o_business_name)}} ({{$ITEM->o_br_number}})</th>
                                                </tr>
                                                <tr style="background-color: #f8f9fa;">
                                                    <th>ISBN</th>
                                                    <th>PRODUCT</th>
                                                    <th>CATEGORY</th>
                                                    <th>REQUESTED QTY</th>
                                                    <th>QTY</th>
                                                    @if($ORDER->os_id != 2)
                                                    <th>AVAILABLE QTY</th>
                                                    @endif
                                                    <th style="display: none;" class="edit_view_item">EDIT QTY</th>
                                                </tr>
                                                @endif

                                                <tr>
                                                    <td>{{$ITEM->p_isbn}} {!!$ITEM->ori_complete == 1? '<span title="Delivered"><i class="bx bx-check text-success"></i></span>' : ''!!}</td>
                                                    <td>{{$ITEM->p_name}}</td>
                                                    <td>{{$ITEM->mc_name}}</td>
                                                    <td style="text-align:right;" width="10%">{{$ITEM->ori_in_qty}}</td>
                                                    <td style="text-align:right;" width="10%">{{$ITEM->ori_qty}}</td>
                                                    @if($ORDER->os_id != 2)
                                                    <td style="text-align:right;" width="10%">{{$ITEM->available_qty}}</td>
                                                    @endif
                                                    <td style="text-align:right; display: none;" width="10%" class="edit_view_item">
                                                        @php
                                                        $qty = $ITEM->ori_qty;
                                                        if($ITEM->available_qty < $ITEM->ori_qty){
                                                            $qty = $ITEM->available_qty;
                                                            }
                                                            @endphp
                                                            <input type="text" name="{{$ITEM->ori_id}}_ITEM_QTY" id="{{$ITEM->ori_id}}_ITEM_QTY}}" value="{{$qty}}" class="form-control form-control-sm input-mask text-start" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 0, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'">
                                                    </td>
                                                </tr>

                                                @endforeach

                                            </table>

                                            <div style="text-align: right; display: none;" id="ORDER_EDIT_FORM_BTN" class="edit_view_item">
                                                <button class="btn btn-success w-lg" type="submit">UPDATE</button>
                                            </div>

                                        </div>
                                    </form>


                                    <div class="me-2 mb-3 mt-3 ">
                                        <h5 class="card-title mt-1 mb-0 text-primary">ORDER SUMMARY</h5>
                                    </div>

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <tr class="table-light">
                                                <th>ISBN</th>
                                                <th>PRODUCT</th>
                                                <th>CATEGORY</th>
                                                <th>QTY</th>
                                                <th>AVAILABLE QTY</th>
                                            </tr>

                                            @php $total_qty = 0; @endphp
                                            @foreach($ORDER_ITEM_FOR_SUMMARY as $ITEM)
                                            @php $total_qty = $total_qty + $ITEM->total_qty; @endphp
                                            <tr>
                                                <td>{{$ITEM->p_isbn}}</td>
                                                <td>{{$ITEM->p_name}}</td>
                                                <td>{{$ITEM->mc_name}}</td>
                                                <td style="text-align:right;" width="10%">{{$ITEM->total_qty}}</td>
                                                <td style="text-align:right;" width="10%">{{$ITEM->available_qty}}</td>
                                            </tr>
                                            @endforeach

                                            <tr>
                                                <th colspan="2"></th>
                                                <th style="background-color: #f8f9fa;">TOTAL QTY</th>
                                                <th style="text-align:right; background-color: #f8f9fa;">{{$total_qty}}</th>
                                                <th style="background-color: #f8f9fa;"></th>
                                            </tr>
                                        </table>
                                    </div>


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
                                                        <h5 class="font-size-12"><small><i>{{$APPROVAL->ora_inserted_date}}</i></small>
                                                            <i class="bx bx-right-arrow-alt font-size-16 text-primary  ms-2"></i>
                                                        </h5>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span class="badge badge-pill badge-soft-{{$APPROVAL->aa_color}} font-size-11"> {{$APPROVAL->aa_name}}</span>
                                                        &nbsp;&nbsp;&nbsp;&nbsp;<small>{{$APPROVAL->ora_action_date}}</small>

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
                                                                            {{empty($APPROVAL->ora_remark)?'N/A':$APPROVAL->ora_remark }}
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
                                                        <div class="text-primary mb-1">{{ $val->ori_inserted_date }}</div>
                                                        <h5 class="mb-4">{{ $val->ori_title }}</h5>
                                                    </div>
                                                    <div class="event-down-icon">
                                                        <i class="bx bx-down-arrow-circle h1 text-primary down-arrow-icon"></i>
                                                    </div>

                                                    <div class="mt-3 px-3">
                                                        <p class="text-muted">
                                                            {{ $val->ori_description }} <br>
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
                // Ajax for add grade
                $(document).ready(function() {
                    $('#ORDER_EDIT_FORM').on('submit', function(e) {
                        e.preventDefault();
                        $('#ORDER_EDIT_FORM_BTN').html('<button class="btn btn-success w-lg" disabled><i class="bx bx-loader bx-spin font-size-16"></i> VERIFYING..</button>');
                        $.post("{{url('/')}}/update_order_items", $(this).serialize(), function(data) {
                            if (data.success) {
                                Swal.fire('Success!', data.success, 'success')
                                    .then(() => {
                                        location.reload();
                                    });
                            }
                            if (data.error) {
                                Swal.fire('Error!', data.error, 'error');
                                $('#ORDER_EDIT_FORM_BTN').html('<button class="btn btn-success w-lg" type="submit">UPDATE</button>');
                            }
                        }, 'json').fail(function(err) {
                            Swal.fire('Error!', 'Something went wrong', 'error');
                            $('#ORDER_EDIT_FORM_BTN').html('<button class="btn btn-success w-lg" type="submit">UPDATE</button>');
                        });
                    });
                });
            </script>

        </div>
    </div>


</body>



</html>