    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>CREATED BY</th>
                <th>NAME</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->mm_inserted_date}}</td>
                <td>{{$data->su_name}} ({{$data->su_nic}})</td>
                <td>{{$data->mm_name}}</td>
                <td>{{$data->mm_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>