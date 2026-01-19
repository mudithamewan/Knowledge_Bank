<div class="card">
    <div class="card-body">
        <form id="RETURN_FORM">
            @csrf
            <input type="hidden" name="MW_ID" value="{{ $MW_ID }}">
            <div class="row">
                <div class="col-xl-3 mt-2">
                    <label for="">Invoice Number</label>
                    <input type="text" name="INCOICE_NUMBER" id="INCOICE_NUMBER" class="form-control">
                </div>
                <div class="col-xl-3 mt-2">
                    <div id="RETURN_FORM_BTN" style="margin-top: 27px;">
                        <button class="btn btn-primary w-lg" type="submit">SUBMIT</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="RETURN_FORM_VIEW"></div>


<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#RETURN_FORM').on('submit', function(e) {
            e.preventDefault();

            $('#RETURN_FORM_VIEW').html('');
            $('#RETURN_FORM_BTN').html('<button class="btn btn-primary w-lg" disabled"><i class="bx bx-loader bx-spin"></i> VERIFYING..</button>');

            const formData = $(this).serialize();

            $.ajax({
                url: "{{ url('/') }}/get_return_invoice_form",
                method: "POST",
                data: formData,
                dataType: "json",
                success: function(data) {

                    if (data.result) {
                        $('#RETURN_FORM_VIEW').html(data.result);
                        $('#RETURN_FORM_BTN').html('<button class="btn btn-primary w-lg" type="submit">SUBMIT</button>');
                    } else if (data.error) {
                        Swal.fire('Error!', data.error, 'error');
                        $('#RETURN_FORM_BTN').html('<button class="btn btn-primary w-lg" type="submit">SUBMIT</button>');
                    } else {
                        Swal.fire('Error!', 'Unexpected response format.', 'error');
                        $('#RETURN_FORM_BTN').html('<button class="btn btn-primary w-lg" type="submit">SUBMIT</button>');
                    }
                },
                error: function(err) {
                    Swal.fire('Error!', err.responseText || 'Unexpected error', 'error');
                    $('#RETURN_FORM_BTN').html('<button class="btn btn-primary w-lg" type="submit">SUBMIT</button>');

                }
            });
        });
    });
</script>