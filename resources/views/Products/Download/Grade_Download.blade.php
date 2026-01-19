    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>CREATED BY</th>
                <th>GRADE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->mg_inserted_date}}</td>
                <td>{{$data->su_name}} ({{$data->su_nic}})</td>
                <td>{{$data->mg_name}}</td>
                <td>{{$data->mg_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>