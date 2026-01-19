    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>CREATED BY</th>
                <th>CATEGORY</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->mc_inserted_date}}</td>
                <td>{{$data->su_name}} ({{$data->su_nic}})</td>
                <td>{{$data->mc_name}}</td>
                <td>{{$data->mc_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>