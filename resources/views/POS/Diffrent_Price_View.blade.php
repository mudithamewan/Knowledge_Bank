<style>
    .table-active {
        background-color: #e9f5ff !important;
        font-weight: 500;
    }
</style>

<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <tr>
            <th>PRODUCT CODE</th>
            <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
            <td>{{str_pad($PRODUCTS[0]->p_id, 5, '0', STR_PAD_LEFT)}}</td>
            <th>ISBN</th>
            <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
            <td>{{$PRODUCTS[0]->p_isbn}}</td>
        </tr>
        <tr>
            <th>NAME</th>
            <td><i class="bx bx-right-arrow-alt font-size-16 text-primary align-middle ms-2"></i></td>
            <td colspan="4">{{$PRODUCTS[0]->p_name}}</td>
        </tr>
    </table>

    <table class="table table-sm mt-4 table-bordered">
        <tr class="table-light">
            <th style="text-align: center;">PRODUCT CODE</th>
            <th style="text-align: center;">PRODUCT ISBN</th>
            <th style="text-align: center;">PRODUCT NAME</th>
            <th style="text-align: center;">PRICE</th>
            <th style="text-align: center;">ACTION</th>
        </tr>
        @foreach($PRODUCTS as $index => $PRODUCT)
        <tr class="price-row" data-index="{{ $index }}">
            <td>{{str_pad($PRODUCT->p_id, 5, '0', STR_PAD_LEFT)}}</td>
            <td>{{$PRODUCT->p_isbn}}</td>
            <td>{{$PRODUCT->p_name}}</td>
            <td style="text-align: right;">{{number_format($PRODUCT->as_selling_price,2)}}</td>
            <td style="text-align: center;">
                <button class="btn btn-sm btn-success add-btn" data-id="{{$PRODUCT->as_id}}" data-as_selling_price="{{$PRODUCT->as_selling_price}}" data-as_available_qty="{{$PRODUCT->as_available_qty}}" data-p_id="{{$PRODUCT->p_id}}" data-name="{{$PRODUCT->p_name}}" data-code="{{$PRODUCT->p_isbn}}">
                    Add
                </button>
            </td>
        </tr>
        @endforeach
    </table>
</div>



<script>
    // ================== GLOBAL VARIABLE ==================
    let currentPriceIndex = -1;

    // ================== MODAL KEYBOARD CONTROL ==================
    $(document).on('keydown', '#different_prices_modal', function(e) {
        let rows = $('#LOAD_DIFFRENT_PRICES_VIEW .price-row');
        if (rows.length === 0) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentPriceIndex = (currentPriceIndex + 1) % rows.length;
            rows.removeClass('table-active');
            rows.eq(currentPriceIndex).addClass('table-active');
        }

        if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentPriceIndex = (currentPriceIndex - 1 + rows.length) % rows.length;
            rows.removeClass('table-active');
            rows.eq(currentPriceIndex).addClass('table-active');
        }

        if (e.key === 'Enter') {
            e.preventDefault();
            if (currentPriceIndex >= 0) {
                rows.eq(currentPriceIndex).find('.add-btn').trigger('click');
                const modal = bootstrap.Modal.getInstance(document.getElementById('different_prices_modal'));
                if (modal) modal.hide();
                currentPriceIndex = -1;
            }
        }
    });

    $('#different_prices_modal').on('shown.bs.modal', function() {
        currentPriceIndex = 0;
        const rows = $('#LOAD_DIFFRENT_PRICES_VIEW .price-row');
        rows.removeClass('table-active');
        rows.eq(currentPriceIndex).addClass('table-active');
        $('#different_prices_modal').focus();
    });

    $('#different_prices_modal').on('hidden.bs.modal', function() {
        currentPriceIndex = -1;
        $('#LOAD_DIFFRENT_PRICES_VIEW .price-row').removeClass('table-active');
        $('#productSearch').focus();
    });
</script>