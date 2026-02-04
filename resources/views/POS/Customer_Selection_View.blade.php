<ul class="nav nav-pills nav-justified" role="tablist">
    <li class="nav-item waves-effect waves-light">
        <a class="nav-link active" data-bs-toggle="tab" href="#home-1" role="tab">
            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
            <span class="d-none d-sm-block">INDIVIDUAL CUSTOMER</span>
        </a>
    </li>
    <li class="nav-item waves-effect waves-light">
        <a class="nav-link" data-bs-toggle="tab" href="#profile-1" role="tab">
            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
            <span class="d-none d-sm-block">CORPORATE CUSTOMER</span>
        </a>
    </li>
</ul>

<div class="tab-content mt-3 text-muted">
    <div class="tab-pane active" id="home-1" role="tabpanel">

        <div class="card border shadow-none">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title text-primary mb-0">INDIVIDUAL CUSTOMER</h5>
            </div>
            <div class="card-body">
                <form id="INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM">
                    @csrf
                    <div class="row">
                        <div class="col-xl-7">
                            <label for="">CONTACT NUMBER <span class="text-danger">*</span></label>
                            <input name="CONTACT_NUMBER" id="CONTACT_NUMBER" class="form-control input-mask" data-inputmask="'mask': '070-0000000'" im-insert="true">
                        </div>
                        <div class="col-xl-5">
                            <div id="INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM_BTN" style="margin-top: 27px;">
                                <button class="btn btn-primary w-100" type="submit">SUBMIT</button>
                            </div>
                        </div>
                        <div class="col-xl-12 mt-3">
                            <div id="load_register_form"></div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <div class="tab-pane" id="profile-1" role="tabpanel">

        <div class="card border shadow-none">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="card-title text-primary mb-0">CORPORATE CUSTOMER</h5>
            </div>
            <div class="card-body">
                <form id="CORPORATE_CUSTOMER_FORM">
                    @csrf
                    <div class="row">
                        <div class="col-xl-8">
                            <label for="CUST_ID" class="form-label">SELECT CUSTOMER <span class="text-danger">*</span></label>
                            <select class="js-example-basic-single form-select" data-placeholder="Choose ..." style="width:100%" name="CUST_ID" id="CUST_ID" required>
                            </select>
                        </div>
                        <div class="col-xl-4">
                            <div id="CORPORATE_CUSTOMER_FORM_BTN" style="margin-top: 27px;">
                                <button class="btn btn-primary w-100" type="submit">SUBMIT</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- 1) jQuery (you already have) -->
<script src="{{url('/')}}/assets/js/jquery-3.6.0.min.js"></script>

<!-- 2) Select2 CSS (you already have) -->
<link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" />

<!-- 3) Select2 JS (MUST be after jQuery) -->
<script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>

<!-- Optional: fix z-index if anything overlaps -->
<style>
    /* if dropdown is hidden behind modal/backdrop */
    .select2-container--open {
        z-index: 9999999;
    }

    /* keep your custom select2 styling */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-radius: 4px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 10px !important;
    }
</style>

<script>
    $(function() {
        // Helper: init Select2 on the target select element
        function initCustomerSelect() {
            // destroy if initialized before
            if ($.fn.select2 && $('#CUST_ID').data('select2')) {
                $('#CUST_ID').select2('destroy');
            }

            $('#CUST_ID').select2({
                dropdownParent: $('#customer_modal .modal-content'), // safer target
                width: '100%',
                placeholder: 'Choose ...',
                ajax: {
                    url: "{{url('/')}}/get_corporate_customer_list",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data.map(function(item) {
                                return {
                                    id: item.cus_id,
                                    text: item.name
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength: 1
            }).on('select2:open', function() {
                // optional: ensure search field is focused
                $('.select2-container--open .select2-search__field').focus();
            });
        }

        // Init when modal is shown (works for static or dynamic content)
        $('#customer_modal').on('shown.bs.modal', function(e) {
            // tiny delay can help if content injected asynchronously
            setTimeout(initCustomerSelect, 10);
        });

        // If modal is already visible on page load and select exists, init immediately
        if ($('#customer_modal').is(':visible')) {
            initCustomerSelect();
        }

        // Debugging helper: show errors in console if ajax fails
        $(document).ajaxError(function(event, jqxhr, settings, thrownError) {
            console.warn('AJAX error:', settings.url, thrownError);
        });

    });
</script>

<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#CORPORATE_CUSTOMER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#CORPORATE_CUSTOMER_FORM_BTN').html(
                '<button class="btn btn-primary w-100" disabled>' +
                '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
            );

            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/get_corporate_customer",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.have_customer) {

                        if (data.credit_allow == 1) {
                            $('.5_PAYMENT_CODE').show();
                        } else {
                            $('.5_PAYMENT_CODE').hide();
                        }

                        $('#cus_order_view').html('');
                        $('#customer_view_area').show();
                        $('#CUS_ID').val(data.customer_id).trigger('change');
                        $('#cus_name').html(data.customer_name);
                        $('#cus_title').html(data.customer_title);
                        $('#IS_CORPARATE').val(1);
                        if (data.order_view != null) {
                            $('#cus_order_view').html(data.order_view);
                        }
                        if (data.is_vat_registered == 1) {
                            $('#VAT_CUSTOMER').val(1);
                        } else {
                            $('#VAT_CUSTOMER').val(0);
                        }
                        document.querySelector('#customer_modal .btn[data-bs-dismiss="modal"]').click();
                    }

                    if (data.havent_customer) {
                        $('.5_PAYMENT_CODE').hide();
                        $('#load_register_form').html(data.view);
                        $('#CORPORATE_CUSTOMER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                    }

                    if (data.error) {
                        $('.5_PAYMENT_CODE').hide();
                        Swal.fire('Error!', data.error, 'error');
                        $('#CORPORATE_CUSTOMER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                    }
                },
                error: function(error) {
                    $('.5_PAYMENT_CODE').hide();
                    Swal.fire('Error!', error, 'error');
                    $('#CORPORATE_CUSTOMER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                }
            });
        });





        // --- your AJAX form logic below ---
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM_BTN').html(
                '<button class="btn btn-primary w-100" disabled>' +
                '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
            );

            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/get_individual_customer",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.have_customer) {
                        $('.5_PAYMENT_CODE').hide();
                        $('#cus_order_view').html('');
                        $('#customer_view_area').show();
                        $('#CUS_ID').val(data.customer_id).trigger('change');
                        $('#cus_name').html(data.customer_name);
                        $('#cus_title').html(data.customer_title);
                        $('#IS_CORPARATE').val(0);
                        $('#VAT_CUSTOMER').val(0);
                        document.querySelector('#customer_modal .btn[data-bs-dismiss="modal"]').click();
                    }

                    if (data.havent_customer) {
                        $('.5_PAYMENT_CODE').hide();
                        $('#load_register_form').html(data.view);
                        $('#INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                    }

                    if (data.error) {
                        $('.5_PAYMENT_CODE').hide();
                        Swal.fire('Error!', data.error, 'error');
                        $('#INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                    }
                },
                error: function(error) {
                    $('.5_PAYMENT_CODE').hide();
                    Swal.fire('Error!', error, 'error');
                    $('#INDIVIDUAL_CUSTOMER_CONTACT_NUMBER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SUBMIT</button>');
                }
            });
        });
    });
</script>