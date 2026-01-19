<form id="TIMELINE_FILTER_FORM">
    @csrf
    <input type="hidden" name="USER_ID" id="USER_ID" value="{{$USER_ID}}">
    <div class="row">
        <div class="col-lg-3 mt-2">
            <label for="">FROM DATE</label>
            <input type="date" name="FROM_DATE" id="FROM_DATE" class="form-control" value="{{date('Y-m-d', strtotime(date('Y-m-d') . ' -365 days'))}}">
        </div>
        <div class="col-lg-3 mt-2">
            <label for="">TO DATE</label>
            <input type="date" name="TO_DATE" id="TO_DATE" class="form-control" value="{{date('Y-m-d')}}">
        </div>
        <div class="col-lg-3 mt-2">
            <div id="TIMELINE_FILTER_FORM_BTN" style="margin-top: 27px;">
                <button class="btn btn-primary w-100" type="submit">GET RESULT</button>
            </div>
        </div>
        <div class="col-xl-12">
            <div id="TIMELINE_FILTER_FORM_RESULT"></div>
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

        $('#TIMELINE_FILTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#TIMELINE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/load_timeline_by_user_table",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.result) {
                        $('#TIMELINE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                        $('#TIMELINE_FILTER_FORM_RESULT').html(data.result);
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#TIMELINE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#TIMELINE_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                }
            });
        });
    });

    $(document).ready(function() {
        // triggers the click on your GET RESULT button
        $('#TIMELINE_FILTER_FORM button[type="submit"]').trigger('click');
    });
</script>