<table class="table table-sm">
    <thead>
        <tr>
            <th>CODE</th>
            <th>NAME</th>
            <th>CATEGORY</th>
            <th style="text-align: center;">AV. STOCK</th>
        </tr>
    </thead>
    <tbody>
        @foreach($STOCK as $PRODUCT)
        <tr>
            <td>{{$PRODUCT->p_id}}</td>
            <td>{{$PRODUCT->p_name}}<br><small class="text-primary">{{$PRODUCT->p_isbn}}</small></td>
            <td>{{$PRODUCT->mc_name}}</td>
            <td style="text-align: center;">{{($PRODUCT->as_available_qty??0)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>