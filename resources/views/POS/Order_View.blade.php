@if(count($ORDERS) > 0)
<div class="border-top mt-3">
    <p class="text-muted fw-medium mt-2">ORDERS</p>

    @foreach($ORDERS as $ORDER)
    <span id="order_button_{{$ORDER->or_id}}">
        <button type="button" class="btn btn-outline-secondary" onclick="load_order_to_pos('{{$ORDER->or_id}}','{{$ORDER->item_count}}','{{$ORDER->or_inserted_date}}')">
            {{$ORDER->or_inserted_date}} <span class="badge bg-danger ms-1">{{$ORDER->item_count}} Items</span>
        </button>
    </span>
    @endforeach
</div>
@endif

<script>
    function load_order_to_pos(or_id, item_count, or_inserted_date) {
        $('#order_button_' + or_id).html('<button type="button" class="btn btn-outline-secondary" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> PROCESSING..</button>');

        var link = '<?= url('/') ?>/load_order_to_pos';
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('or_id', or_id);

        $.ajax({
            type: 'POST',
            url: link,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {

                if (data.success) {

                }
                if (data.error) {
                    $('#order_button_' + or_id).html('<button type="button" class="btn btn-outline-secondary" onclick="load_order_to_pos(`' + or_id + '`,`' + item_count + '`,`' + or_inserted_date + '`)"> ' + or_inserted_date + ' <span class="badge bg-danger ms-1">' + item_count + ' Items</span></button>');
                }

            },
            error: function(xhr, status, error) {
                $('#order_button_' + or_id).html('<button type="button" class="btn btn-outline-secondary" onclick="load_order_to_pos(`' + or_id + '`,`' + item_count + '`,`' + or_inserted_date + '`)"> ' + or_inserted_date + ' <span class="badge bg-danger ms-1">' + item_count + ' Items</span></button>');
            }
        });

    }
</script>