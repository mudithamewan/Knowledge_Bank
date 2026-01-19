<form id="CHANGE_PASSWORD_FORM">
    @csrf
    <input type="hidden" name="USER_ID" id="USER_ID" value="{{$USER_DETAILS->su_id}}">

    <div class="row">
        <div class="col-lg-4 mt-2">
            <label for="">NEW PASSWORD</label>
            <input type="password" name="PASSWORD" id="PASSWORD" class="form-control" required>
        </div>
        <div class="col-lg-4 mt-2">
            <label for="">CONFIRM PASSWORD</label>
            <input type="password" name="CONFIRM_PASSWORD" id="CONFIRM_PASSWORD" class="form-control" required>
        </div>


        <div class="col-lg-4 mt-2">
            <div id="CHANGE_PASSWORD_FORM_BTN" style="margin-top: 27px;">
                <button class="btn btn-primary w-100" type="submit">CHANGE</button>
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

        $('#CHANGE_PASSWORD_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#CHANGE_PASSWORD_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/chnage_user_password",
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
                        $('#CHANGE_PASSWORD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">CHANGE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#CHANGE_PASSWORD_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">CHANGE</button>');
                }
            });
        });
    });
</script>