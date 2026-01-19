    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>UPDATED DATE</th>
                <th>NAME</th>
                <th>NIC</th>
                <th>CONTACT NUMBER</th>
                <th>EMAIL</th>
                <th>ADDRESS</th>
                <th>ROLE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->su_inserted_date}}</td>
                <td>{{$data->su_updated_date}}</td>
                <td>{{$data->su_name}}</td>
                <td>{{$data->su_nic}}</td>
                <td>{{$data->su_contact_number}}</td>
                <td>{{$data->su_email}}</td>
                <td>{{$data->su_address_line_01. " ".$data->su_address_line_02." ". $data->su_address_line_03}}</td>
                <td>{{$data->sr_name}}</td>
                <td>{{$data->su_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>