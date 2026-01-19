<form id="ORG_INVOICE_FILTER_FORM">
    @csrf
    <input type="hidden" name="O_ID" id="O_ID" value="{{$O_ID}}">
    <div class="row">
        <div class="col-lg-4 mt-2">
            <label for="">FROM DATE</label>
            <input type="date" name="FROM_DATE" id="FROM_DATE" class="form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d') . ' -365 days'))}}">
        </div>
        <div class="col-lg-4 mt-2">
            <label for="">TO DATE</label>
            <input type="date" name="TO_DATE" id="TO_DATE" class="form-control" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-lg-4 mt-2">
            <div id="ORG_INVOICE_FILTER_FORM_BTN" style="margin-top: 27px;">
                <button class="btn btn-primary w-100" type="submit">GET RESULT</button>
            </div>
        </div>
        <div class="col-xl-12">
            <div id="ORG_INVOICE_FILTER_RESULT"></div>
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

        $('#ORG_INVOICE_FILTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#ORG_INVOICE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/load_invoiecs_by_organization_table",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.result) {
                        $('#ORG_INVOICE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                        $('#ORG_INVOICE_FILTER_RESULT').html(data.result);
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#ORG_INVOICE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#ORG_INVOICE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                }
            });
        });
    });

    $(document).ready(function() {
        // triggers the click on your GET RESULT button
        $('#ORG_INVOICE_FILTER_FORM button[type="submit"]').trigger('click');
    });
</script>