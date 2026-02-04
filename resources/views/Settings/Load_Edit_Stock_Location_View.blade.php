<form id="WAREHOUSE_EDIT_FORM">
    @csrf
    <input type="hidden" name="MW_ID" id="MW_ID" value="{{$WAREHOUSE_DETAILS->mw_id}}">
    <div class="row">
        <div class="col-lg-3 mt-2">
            <label>TYPE <span class="text-danger">*</span></label>
            <select name="TYPE" id="TYPE2" class="form-select" onclick="load_items2()">
                @foreach($STOCK_LOCATION_TYPES as $TYPE)
                @if($TYPE->mwt_id == $WAREHOUSE_DETAILS->mw_mwt_id)
                <option value="{{$TYPE->mwt_id}}" selected>{{$TYPE->mwt_name}}</option>
                @else
                <option value="{{$TYPE->mwt_id}}">{{$TYPE->mwt_name}}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="col-lg-9 mt-2">
            <label>NAME <span class="text-danger">*</span></label>
            <input type="text" name="NAME" id="NAME" class="form-control" value="{{$WAREHOUSE_DETAILS->mw_name}}">
        </div>
        <div class="col-lg-6 mt-2" id="contact_number_field2">
            <label>CONTACT NUMBER</label>
            <input type="text" name="CONTACT_NUMBER" id="CONTACT_NUMBER" class="form-control" value="{{$WAREHOUSE_DETAILS->mw_contact_number}}">
        </div>
        <div class="col-lg-6 mt-2" id="email_field2">
            <label>EMAIL</label>
            <input type="email" name="EMAIL" id="EMAIL" class="form-control" value="{{$WAREHOUSE_DETAILS->mw_email}}">
        </div>

        <div class="col-lg-3 mt-2" id="vehicle_no2" style="display:none">
            <label>VEHICLE NO <span class="text-danger">*</span></label>
            <input type="text" name="VEHICLE_NO" id="VEHICLE_NO" class="form-control" value="{{$WAREHOUSE_DETAILS->mw_vehicle_no}}" maxlength="20">
        </div>

        <div class="col-lg-9 mt-2" id="users_id2" style="display: none;">
            <label>USER <span class="text-danger">*</span></label>
            <select name="USER_ID" id="USER_ID" class="form-select">
                @foreach($USERS as $user)
                @if($user->su_email == $WAREHOUSE_DETAILS->mw_email)
                <option value="{{$user->su_id}}" selected>{{$user->su_name}} ({{$user->su_nic}})</option>
                @else
                <option value="{{$user->su_id}}">{{$user->su_name}} ({{$user->su_nic}})</option>
                @endif
                @endforeach
            </select>
        </div>


        <div class="col-lg-3 mt-2">
            <label for="">STATUS <span class="text-danger">*</span></label>
            <select name="STATUS" id="STATUS" class="form-select">
                @if($WAREHOUSE_DETAILS->mw_is_active == 1)
                <option value="1" selected>ACTIVE</option>
                <option value="0">IN-ACTIVE</option>
                @else
                <option value="1">ACTIVE</option>
                <option value="0" selected>IN-ACTIVE</option>
                @endif
            </select>
        </div>
        <div class="col-lg-12 mt-2" id="address_field2">
            <label>ADDRESS <span class="text-danger">*</span></label>
            <textarea name="ADDRESS" id="ADDRESS" class="form-control" rows="4">{{$WAREHOUSE_DETAILS->mw_address}}</textarea>
        </div>
        <div class="col-xl-12"></div>
        <div class="col-lg-3 mt-2">
            <div id="WAREHOUSE_EDIT_FORM_BTN" class="mt-2">
                <button class="btn btn-primary w-100" type="submit">UPDATE</button>
            </div>
        </div>
    </div>
</form>

<script>
    function load_items2() {

        $('#vehicle_no2').hide();
        $('#users_id2').hide();
        $('#contact_number_field2').show();
        $('#email_field2').show();
        $('#address_field2').show();
        var type = $('#TYPE2').val();
        if (type == 3) {
            $('#vehicle_no2').show();
            $('#users_id2').show();
            $('#contact_number_field2').hide();
            $('#email_field2').hide();
            $('#address_field2').hide();
        }
    }
    load_items2();

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#WAREHOUSE_EDIT_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#WAREHOUSE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/update_stock_location",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        const closeButton = document.querySelector('#edit_stock_locations_modal .btn[data-bs-dismiss="modal"]');
                        if (closeButton) {
                            closeButton.click();
                        }
                        $('#WAREHOUSE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                        Swal.fire(
                            'Success!',
                            data.success,
                            'success'
                        ).then((result) => {
                            if (result.isConfirmed) {
                                get_table_data();
                            } else {
                                get_table_data();
                            }
                        });
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#WAREHOUSE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#WAREHOUSE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                }
            });
        });
    });
</script>