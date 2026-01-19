<form id="EDIT_USER_FORM">
    @csrf
    <input type="hidden" name="USER_ID" id="USER_ID" value="{{$CUSTOMER_DETAILS->c_id}}">

    <div class="row">
        <div class="col-lg-2 mt-2">
            <label for="">TITLE <span class="text-danger">*</span></label>
            <select name="TITLE" id="TITLE" class="form-select">
                <option value="" disabled hidden selected>Choose .. </option>
                @if(!empty($CUSTOMER_DETAILS->c_title))
                <option value="{{$CUSTOMER_DETAILS->c_title}}" selected hidden>{{$CUSTOMER_DETAILS->c_title}}.</option>
                @endif
                <option value="Mr">Mr.</option>
                <option value="Mrs">Mrs.</option>
                <option value="Miss">Miss.</option>
                <option value="Ms">Ms.</option>
                <option value="Dr">Dr.</option>
                <option value="Prof">Prof.</option>
                <option value="Rev">Rev.</option>
                <option value="Hon">Hon.</option>
                <option value="Sir">Sir</option>
                <option value="Lady">Lady</option>
            </select>
        </div>
        <div class="col-lg-7 mt-2">
            <label for="">NAME <span class="text-danger">*</span></label>
            <input type="text" name="NAME" id="NAME" class="form-control" value="{{$CUSTOMER_DETAILS->c_name}}" required>
        </div>
        <div class="col-lg-3 mt-2">
            <label for="">DOB <span class="text-danger">*</span></label>
            <input type="date" name="DOB" id="DOB" class="form-control" value="{{$CUSTOMER_DETAILS->c_dob}}">
        </div>
        <div class="col-lg-4 mt-2">
            <label for="">NIC</label>
            <input type="text" name="NIC" id="NIC" class="form-control" value="{{$CUSTOMER_DETAILS->c_nic}}">
        </div>
        <div class="col-lg-4 mt-2">
            <label for="">CONTACT NUMBER <span class="text-danger">*</span></label>
            <input type="text" name="CONTACT_NUMBER" id="CONTACT_NUMBER" class="form-control" value="{{$CUSTOMER_DETAILS->c_contact}}" required>
        </div>
        <div class="col-lg-4 mt-2">
            <label for="">GENDER <span class="text-danger">*</span></label> <br>
            <input type="radio" name="GENDER" id="MALE" value="M" <?= $CUSTOMER_DETAILS->c_gender == 'Male' ? 'checked' : '' ?>> <label for="MALE">MALE</label> &nbsp;&nbsp;&nbsp;
            <input type="radio" name="GENDER" id="FEMALE" value="F" <?= $CUSTOMER_DETAILS->c_gender == 'Female' ? 'checked' : '' ?>> <label for="FEMALE">FEMALE</label>
        </div>
        <div class="col-lg-8 mt-2">
            <label for="">EMAIL </label>
            <input type="text" name="EMAIL" id="EMAIL" class="form-control" value="{{$CUSTOMER_DETAILS->c_email}}">
        </div>
        <div class="col-lg-12 mt-2">
            <label for="">ADDRESS <span class="text-danger">*</span></label>
            <textarea name="ADDRESS" id="ADDRESS" class="form-control" rows="4">{{$CUSTOMER_DETAILS->c_address}}</textarea>
        </div>



        <div class="col-lg-4 mt-2">
            <label for="">STATUS <span class="text-danger">*</span></label>
            <select name="ACTIVE_STATUS" id="ACTIVE_STATUS" class="form-select" required>
                @if($CUSTOMER_DETAILS->c_is_suspend == 1)
                <option value="SUSPEND" selected>SUSPEND</option>
                <option value="ACTIVE">ACTIVE</option>
                @else
                <option value="SUSPEND">SUSPEND</option>
                <option value="ACTIVE" selected>ACTIVE</option>
                @endif
            </select>
        </div>

        <div class="col-lg-4 mt-2">
            <div id="EDIT_USER_FORM_BTN" style="margin-top: 27px;">
                <button class="btn btn-primary w-100" type="submit">UPDATE</button>
            </div>
        </div>
    </div>

</form>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#EDIT_USER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/update_customer",
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
                        $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                }
            });
        });
    });
</script>