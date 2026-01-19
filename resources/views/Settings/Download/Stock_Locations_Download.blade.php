    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>NAME</th>
                <th>TYPE</th>
                <th>CONTACT NUMBER</th>
                <th>EMAIL</th>
                <th>ADDRESS</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->mw_inserted_date}}</td>
                <td>{{$data->mw_name}}</td>
                <td>{{$data->mwt_name}}</td>
                <td>{{$data->mw_contact_number}}</td>
                <td>{{$data->mw_email}}</td>
                <td>{{$data->mw_address}}</td>
                <td>{{$data->mw_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>