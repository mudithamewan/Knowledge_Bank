<form id="GRADE_EDIT_FORM">
    @csrf
    <input type="hidden" name="MG_ID" id="MG_ID" value="{{$GRADE_DETAILS->mg_id}}">
    <div class="row">
        <div class="col-lg-6 mt-2">
            <label for="">GRADE <span class="text-danger">*</span></label>
            <input type="text" name="NAME" id="NAME" value="{{$GRADE_DETAILS->mg_name}}" class="form-control" required>
        </div>
        <div class="col-lg-3 mt-2">
            <label for="">STATUS <span class="text-danger">*</span></label>
            <select name="STATUS" id="STATUS" class="form-select">
                @if($GRADE_DETAILS->mg_is_active == 1)
                <option value="1" selected>ACTIVE</option>
                <option value="0">IN-ACTIVE</option>
                @else
                <option value="1">ACTIVE</option>
                <option value="0" selected>IN-ACTIVE</option>
                @endif
            </select>
        </div>
        <div class="col-lg-3 mt-2">
            <div id="GRADE_EDIT_FORM_BTN" style="margin-top: 27px;">
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

        $('#GRADE_EDIT_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#GRADE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/update_grade",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.success) {
                        const closeButton = document.querySelector('#edit_grade_modal .btn[data-bs-dismiss="modal"]');
                        if (closeButton) {
                            closeButton.click();
                        }
                        $('#GRADE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
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
                        $('#GRADE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#GRADE_EDIT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                }
            });
        });
    });
</script>