<form id="PRODUCT_AVAILABLE_STOCK_FILTER_FORM">
    @csrf
    <input type="hidden" name="PRODUCT_ID" id="PRODUCT_ID" value="{{$PRODUCT_ID}}">
    <div class="row">
        <div class="col-lg-4 mt-2">
            <label for="">WAREHOUSE</label>
            <select name="MW_ID" id="MW_ID" class="form-select">
                <option value="ALL" selected>ALL</option>
                @foreach($WAREHOUSES as $WAREHOUSE)
                <option value="{{$WAREHOUSE->mw_id}}">{{$WAREHOUSE->mw_name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 mt-2">
            <div id="PRODUCT_AVAILABLE_STOCK_FILTER_FORM_BTN" style="margin-top: 27px;">
                <button class="btn btn-primary w-100" type="submit">GET RESULT</button>
            </div>
        </div>
        <div class="col-xl-12">
            <div id="PRODUCT_MANAGE_FILTER_RESULT"></div>
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

        $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM').on('submit', function(e) {
            e.preventDefault();
            $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

            e.preventDefault();
            var formData = $(this).serialize();

            $.ajax({
                url: "{{url('/')}}/load_available_stock_by_product_table",
                type: "POST",
                data: formData,
                dataType: 'json',
                success: function(data) {

                    if (data.result) {
                        $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                        $('#PRODUCT_MANAGE_FILTER_RESULT').html(data.result);
                    }

                    if (data.error) {
                        Swal.fire(
                            'Error!',
                            data.error,
                            'error'
                        );
                        $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                    }
                },
                error: function(error) {
                    Swal.fire(
                        'Error!',
                        error,
                        'error'
                    );
                    $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">GET RESULT</button>');
                }
            });
        });
    });

    $(document).ready(function() {
        // triggers the click on your GET RESULT button
        $('#PRODUCT_AVAILABLE_STOCK_FILTER_FORM button[type="submit"]').trigger('click');
    });
</script>