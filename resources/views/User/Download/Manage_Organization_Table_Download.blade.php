    <table border="1">
        <thead>
            <tr>
                <th>CREATED DATE</th>
                <th>UPDATED DATE</th>
                <th>NAME</th>
                <th>BUSINESS NAME</th>
                <th>CONTACT NUMBER</th>
                <th>EMAIL</th>
                <th>ADDRESS</th>
                <th>BR NUMBER</th>
                <th>IS VAT REGISTERED</th>
                <th>VAT REGISTERED NUMBER</th>
                <th>VAT REGISTERED DATE</th>
                <th>BANK CODE</th>
                <th>BRANCH CODE</th>
                <th>ACCOUNT NUMBER</th>
                <th>TAGS</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{$data->o_inserted_date}}</td>
                <td>{{$data->o_updated_date}}</td>
                <td>{{$data->o_name}}</td>
                <td>{{$data->o_business_name}}</td>
                <td>{{$data->o_contact}}</td>
                <td>{{$data->o_email}}</td>
                <td>{{$data->o_address}}</td>
                <td>{{$data->o_br_number}}</td>
                <td>{{$data->o_is_vat_registered == 1 ? 'YES':'NO'}}</td>
                <td>{{$data->o_vat_registered_number}}</td>
                <td>{{$data->o_vat_registered_date}}</td>
                <td>{{$data->o_bank_code}}</td>
                <td>{{$data->o_bank_branch_code}}</td>
                <td>{{$data->o_account_number}}</td>
                <td>{{$data->types}}</td>
                <td>{{$data->o_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>