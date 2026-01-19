<form id="SET_WAREHOUSE_FORM">
    @csrf
    <input type="hidden" name="MW_ID" name="MW_ID" value="{{$MW_ID}}">
    <div class="row border-top mt-2">
        <div class="col-xl-12 ">
        </div>
        <div class="col-xl-12 mt-2">
            <label for="">Cash on hand</label>
            <input type="text" class="form-control input-mask text-start" id="CASH_ON_HAND" name="CASH_ON_HAND" data-inputmask="'alias': 'numeric', 'groupSeparator': ',', 'digits': 2, 'digitsOptional': false, 'prefix': '', 'placeholder': '0'">
        </div>
        <div class="col-xl-12 mt-2">
            <div id="SET_WAREHOUSE_FORM_BTN">
                <button class="btn btn-primary w-100" type="submit">SAVE</button>
            </div>
        </div>
    </div>
</form>

<script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
<script src=" {{url('/')}}/assets/libs/inputmask/min/jquery.inputmask.bundle.min.js"></script>
<script src="{{url('/')}}/assets/js/pages/form-mask.init.js"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#SET_WAREHOUSE_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#SET_WAREHOUSE_FORM_BTN').html(
                '<button class="btn btn-primary w-100" disabled>' +
                '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
            );

            var formData = new FormData(this);

            $.ajax({
                url: "{{url('/')}}/save_new_punch",
                type: "POST",
                data: formData,
                processData: false, // required for file uploads
                contentType: false, // required for file uploads
                dataType: 'json',
                success: function(data) {
                    if (data.success) {

                        $("#MW_ID").val(data.mw_id);
                        $("#warehouse_name").text(data.mw_name);

                        setTimeout(() => {
                            $('#SET_WAREHOUSE_FORM_BTN').html('<button class="btn btn-primary w-100" disabled>SAVED</button>');
                            $("#change_warehouse_modal .btn[data-bs-dismiss='modal']").click();
                        }, 400);
                    }

                    if (data.error) {
                        Swal.fire('Error!', data.error, 'error');
                        $('#SET_WAREHOUSE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                    }
                },
                error: function(error) {
                    Swal.fire('Error!', error, 'error');
                    $('#SET_WAREHOUSE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                }
            });
        });
    });
</script>