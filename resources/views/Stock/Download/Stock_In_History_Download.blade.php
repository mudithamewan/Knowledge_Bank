    <table border="1">
        <thead>
            <tr>
                <th>STOCK IN DATE</th>
                <th>STOCK IN BY</th>
                <th>TOTAL ITEMS</th>
                <th>PURCHASE PRICE</th>
                <th>SELLING PRICE</th>
                <th>TOTAL QTY</th>
                <th>WAREHOUSE</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->sil_inserted_date}}</td>
                <td>{{$data->su_name}}</td>
                <td>{{$data->sil_total_items}}</td>
                <td>{{number_format($data->sil_purchase_amount,2)}}</td>
                <td>{{number_format($data->sil_selling_amount,2)}}</td>
                <td>{{$data->sil_total_qty}}</td>
                <td>{{$data->mw_name}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>