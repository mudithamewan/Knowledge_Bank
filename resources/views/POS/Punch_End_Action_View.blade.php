<div class="row">
    <div class="col-xl-12">
        <table class="table table-sm">
            <tr>
                <th>START DATE</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{$PUNCH_DETAILS->pu_inserted_date}}</td>
                <th>START BY</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{$PUNCH_DETAILS->su_name}}</td>
            </tr>
            <tr>
                <th>POS</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{$PUNCH_DETAILS->mw_name}}</td>
                <th>STARTING VALUE</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{number_format($PUNCH_DETAILS->pu_cash_on_hand,2)}}</td>
            </tr>
            <tr>
                <th>TOTAL AMOUNT</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{number_format($PUNCH_DETAILS->pu_amount,2)}}</td>
                <th>STAGE</th>
                <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
                <td>{{$PUNCH_DETAILS->pus_name}}</td>
            </tr>
        </table>
    </div>

    <div class="col-xl-12">
        <form class="repeater" enctype="multipart/form-data" id="END_FORM">
            @csrf
            <input type="hidden" name="PU_ID" id="PU_ID" value="{{$PU_ID}}">

            <div data-repeater-list="group-a">
                <div data-repeater-item class="row">

                    <div class="col-xl-3 mt-2">
                        <label for="">Payment Type <span class="text-danger">*</span></label>
                        <select name="TYPE" class="form-select payment_type">
                            <option value="" disabled hidden selected>CHOOSE ..</option>
                            <option value="BANK">BANK</option>
                            <option value="TRANSFER">TRANSFER</option>
                        </select>
                    </div>

                    <div class="col-xl-4 mt-2">
                        <label for="name">Amount <span class="text-danger">*</span></label>
                        <input type="text" name="TYPE_AMOUNT" class="form-control input-mask text-start amounts" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'" placeholder="0.00" />
                    </div>

                    <div class="col-xl-3 mt-2 transfer_to_area" style="display:none;">
                        <label for="name">Transfer To <span class="text-danger">*</span></label>
                        <select name="TRANSFER_TO" class="form-select">
                            <option value="" disabled hidden selected>CHOOSE ..</option>
                            @foreach($USERS as $USER)
                            <option value="{{$USER->su_id}}">{{$USER->su_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 mt-2 align-self-center">
                        <div class="d-grid" style="margin-top: 27px;">
                            <input data-repeater-delete type="button" class="btn btn-primary" value="Delete" />
                        </div>
                    </div>

                </div>
            </div>

            <input data-repeater-create type="button" class="btn btn-success btn-sm mt-2" value="Add" />

            <br><br>
            <div id="END_FORM_BTN">
                <button class="btn btn-success w-lg" type="submit">SUBMIT</button>
            </div>
        </form>
    </div>
</div>

<script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src="{{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>
<script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
<script src="{{url('/')}}/assets/libs/jquery.repeater/jquery.repeater.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/form-repeater.int.js"></script>

<script>
    $(document).ready(function() {

        // toggle transfer_to_area on select change
        $(document).on('change', '.payment_type', function() {

            let row = $(this).closest('[data-repeater-item]');
            let value = $(this).val();

            if (value === 'TRANSFER') {
                row.find('.transfer_to_area').show();
            } else {
                row.find('.transfer_to_area').hide();
                row.find('.transfer_to_area select').val('');
            }
        });

    });

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#END_FORM').on('submit', function(e) {
            e.preventDefault();

            $('#END_FORM_BTN').html('<button class="btn btn-success w-lg" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING...</button>');

            e.preventDefault();
            var formData = $(this).serialize();
            // Submit form data via Ajax
            $.ajax({
                url: "{{url('/')}}/punch_end_action",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        setTimeout(() => {
                            $('#END_FORM_BTN').html('<button class="btn btn-success w-lg" disabled>DONE</button>');
                        }, 400);

                        const closeButton = document.querySelector('#punching_modal .btn[data-bs-dismiss="modal"]');
                        if (closeButton) {
                            closeButton.click();
                        }

                        Swal.fire(
                            'Success!',
                            data.success,
                            'success'
                        );
                        $('#PUNCH_FILTER_FORM button[type="submit"]').trigger('click');
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#END_FORM_BTN').html('<button class="btn btn-success w-lg" type="submit">SUBMIT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#END_FORM_BTN').html('<button class="btn btn-success w-lg" type="submit">SUBMIT</button>');
                }
            });
        });
    });
</script>