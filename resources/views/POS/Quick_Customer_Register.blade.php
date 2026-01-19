<form id="QUICK_CUSTOMER_REGISTER_FORM">
    @csrf
    <input type="hidden" name="CONTACT" id="CONTACT" value="{{$CONTACT_NUMBER}}">
    <div class="row">
        <div class="col-xl-6 mt-2">
            <label for="">NAME <span class="text-danger">*</span></label>
            <input type="text" name="NAME" id="NAME" class="form-control" maxlength="255">
        </div>
        <div class="col-xl-6 mt-2">
            <label for="">DOB</label>
            <input type="date" name="DOB" id="DOB" class="form-control" maxlength="15">
        </div>
        <div class="col-xl-6 mt-2">
            <label for="">NIC</label>
            <input type="text" name="NIC" id="NIC" class="form-control" maxlength="15">
        </div>
        <div class="col-xl-6 mt-2">
            <label for="">EMAIL</label>
            <input type="email" name="EMIAL" id="EMIAL" class="form-control" maxlength="200">
        </div>
        <div class="col-xl-12 mt-2">
            <label for="">ADDRESS</label>
            <textarea name="ADDRESS" id="ADDRESS" class="form-control" rows="3"></textarea>
        </div>
        <div class="col-xl-6 mt-3">
            <div id="QUICK_CUSTOMER_REGISTER_FORM_BTN">
                <button class="btn btn-primary w-100" type="submit">SAVE</button>
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

        $('#QUICK_CUSTOMER_REGISTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#QUICK_CUSTOMER_REGISTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/save_quich_customer_form",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        Swal.fire(
                            'Success!',
                            data.success,
                            'success'
                        );
                        $('#customer_view_area').show();
                        $('#CUS_ID').val(data.customer_id);
                        $('#cus_name').html(data.customer_name);
                        $('#cus_title').html(data.customer_title);


                        $('#IS_CORPARATE').val(0);
                        $('#QUICK_CUSTOMER_REGISTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled>SAVED</button>');
                        document.querySelector('#customer_modal .btn[data-bs-dismiss="modal"]').click();
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#QUICK_CUSTOMER_REGISTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#QUICK_CUSTOMER_REGISTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                }
            });
        });
    });
</script>