    <table border="1">
        <thead>
            <tr>
                <th>PRODUCT CODE</th>
                <th>CREATED DATE</th>
                <th>UPDATED DATE</th>
                <th>NAME</th>
                <th>ISBN</th>
                <th>AUTHOR</th>
                <th>PUBLISHER</th>
                <th>MEDIUM</th>
                <th>GRADES</th>
                <th>SUBJECTS</th>
                <th>CATEGORY</th>
                <th>SUB CATEGORY</th>
                <th>EDITION</th>
                <th>PUBLISHED YEAR</th>
                <th>PAGE COUNT</th>
                <th>FORMAT</th>
                <th>DESCRIPTION</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            @foreach($result as $data)
            <tr>
                <td>{{str_pad($data->p_id, 5, '0', STR_PAD_LEFT)}}</td>
                <td>{{$data->p_inserted_date}}</td>
                <td>{{$data->p_updated_date}}</td>
                <td>{{$data->p_name}}</td>
                <td>{{$data->p_isbn}}</td>
                <td>{{$data->p_author}}</td>
                <td>{{$data->o_business_name}}</td>
                <td>{{$data->mm_name}}</td>
                <td>{{$data->grades}}</td>
                <td>{{$data->subjects}}</td>
                <td>{{$data->mc_name}}</td>
                <td>{{$data->msc_name}}</td>
                <td>{{$data->p_edition}}</td>
                <td>{{$data->p_published_year}}</td>
                <td>{{$data->p_page_count}}</td>
                <td>{{$data->mbf_name}}</td>
                <td>{{$data->p_description}}</td>
                <td>{{$data->p_is_active == 1 ? 'ACTIVE':'INACTIVE'}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>