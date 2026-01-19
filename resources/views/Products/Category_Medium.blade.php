<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <link href="{{url('/')}}/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="{{url('/')}}/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" />

    <style>
        th {
            position: relative;
        }

        .filter-icon {
            cursor: pointer;
            font-size: 14px;
            margin-left: 6px;
            color: #555;
        }

        .dropdown-filter {
            display: none;
            position: fixed;
            /* changed from absolute */
            background: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            padding: 10px;
            z-index: 9999;
            /* above scrollbars */
            min-width: 200px;
            max-height: 250px;
            overflow-y: auto;
        }

        .dropdown-filter input[type="text"] {
            width: 100%;
            margin-bottom: 6px;
            padding: 4px;
        }

        .dropdown-filter label {
            display: block;
        }

        .dropdown-filter .actions {
            margin-bottom: 6px;
        }

        .dropdown-filter .actions button {
            margin-right: 5px;
            font-size: 12px;
        }

        .dropdown-filter .sort-options {
            margin-top: 8px;
            border-top: 1px solid #eee;
            padding-top: 6px;
        }

        .dropdown-filter .sort-options button {
            display: block;
            width: 100%;
            margin: 2px 0;
        }
    </style>
</head>

<body data-sidebar="dark">

    <div id="layout-wrapper">
        @include('Layout/header')
        @include('Layout/sideMenu')

        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- Page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Medium Manager</h4>

                                <button type="button" class="btn btn-primary waves-effect btn-label waves-light" data-bs-toggle="modal" data-bs-target="#add_new_medium_modal">
                                    <i class="bx bx-plus label-icon"></i> NEW MEDIUM
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Add new medium -->
                    <div class="modal fade" id="add_new_medium_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">ADD NEW MEDIUM</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="NEW_MEDIUM_CREATE_FORM">
                                        @csrf
                                        <div class="row">
                                            <div class="col-lg-6 mt-2">
                                                <label>MEDIUM NAME <span class="text-danger">*</span></label>
                                                <input type="text" name="NAME" id="NAME" class="form-control" required>
                                            </div>
                                            <div class="col-lg-3 mt-2">
                                                <div id="NEW_MEDIUM_CREATE_FORM_BTN" style="margin-top: 27px;">
                                                    <button class="btn btn-primary w-100" type="submit">SAVE</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="card mt-3">
                        <div class="card-header bg-transparent border-bottom">
                            <div class="d-flex flex-wrap align-items-start">
                                <div class="me-2">
                                    <h5 class="card-title mt-2 text-primary">MEDIUM DETAILS</h5>
                                </div>
                                <div class="ms-auto">
                                    <form action="{{url('/')}}/get_medium_result_table" method="post">
                                        @csrf
                                        <input type="hidden" name="DOWNLOAD" value="YES">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bx bx-download font-size-16 align-middle me-2"></i> Download
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="result_table" class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th style="white-space: nowrap;">MEDIUM <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">CREATED DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">CREATED BY <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">UPDATED DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">UPDATED BY <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">STATUS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                                            <th style="white-space: nowrap;">ACTION</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @include('Layout/footer')
        </div>
    </div>

    <!-- Modal: Edit medium -->
    <div class="modal fade" id="edit_medium_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">EDIT MEDIUM DETAILS</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="LOAD_EDIT_MEDIUM_VIEW"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
    <script src="{{url('/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="{{url('/')}}/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

    <script>
        function get_table_data() {
            if ($.fn.DataTable.isDataTable('#result_table')) {
                $('#result_table').DataTable().clear().destroy();
            }

            function renderNA(data) {
                return data && data.toString().trim() !== '' ? data : 'N/A';
            }

            var table = $('#result_table').DataTable({
                serverSide: false, // load all data so filters see all values
                processing: true,
                orderCellsTop: true,
                columnDefs: [{
                    orderable: true,
                    targets: "_all"
                }],
                language: {
                    processing: '<i class="bx bx-loader bx-spin h1 align-middle me-2 text-primary"></i>',
                },
                ajax: {
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': "{{csrf_token()}}"
                    },
                    url: "{{url('/')}}/get_medium_result_table",
                    data: {
                        DOWNLOAD: 'NO'
                    },
                    dataSrc: "data"
                },
                order: [],
                columns: [{
                        data: 'mm_name',
                        render: renderNA
                    },
                    {
                        data: 'mm_inserted_date',
                        render: renderNA
                    },
                    {
                        data: 'su_name',
                        render: renderNA
                    },
                    {
                        data: 'mm_updated_date',
                        render: renderNA
                    },
                    {
                        data: 'update_user',
                        render: renderNA
                    },
                    {
                        data: 'mm_is_active',
                        render: d => d == '1' ?
                            '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>' : '<span class="badge badge-pill badge-soft-danger font-size-11">In-Active</span>'
                    },
                    {
                        data: 'mm_id',
                        render: d => `<button class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#edit_medium_modal"
                                onclick="ajax_action(
                                    '{{ url('/') }}/load_edit_medium_view',
                                    'LOAD_EDIT_MEDIUM',
                                    JSON.stringify({ MEDIUM_ID: ${d} }),
                                    '{{ csrf_token() }}'
                                )">EDIT</button>`
                    }
                ],
                initComplete: function() {
                    var api = this.api();

                    $('#result_table thead th').each(function(i) {
                        // skip ACTION column (index 4)
                        if (i === 4) return;

                        var $th = $(this);
                        var dropdown = $('<div class="dropdown-filter"></div>');

                        // attach dropdown to body so it isn't clipped by scroll container
                        $('body').append(dropdown);

                        // Search box
                        var searchBox = $('<input type="text" placeholder="Search...">').appendTo(dropdown);

                        // Select/Deselect all
                        var actions = $('<div class="actions"></div>').prependTo(dropdown);
                        $('<button type="button" class="btn btn-sm btn-light">Select All</button>')
                            .appendTo(actions)
                            .click(() => dropdown.find('input[type="checkbox"]').prop('checked', true).trigger('change'));
                        $('<button type="button" class="btn btn-sm btn-light">Deselect All</button>')
                            .appendTo(actions)
                            .click(() => dropdown.find('input[type="checkbox"]').prop('checked', false).trigger('change'));

                        // Values
                        api.column(i).data().unique().sort().each(d => {
                            $('<label><input type="checkbox" value="' + d + '"> ' + d + '</label>').appendTo(dropdown);
                        });

                        // Sort options
                        var sortBox = $('<div class="sort-options"></div>').appendTo(dropdown);
                        $('<button class="btn btn-sm btn-outline-secondary">Sort Asc</button>')
                            .appendTo(sortBox)
                            .click(() => api.order([i, 'asc']).draw());
                        $('<button class="btn btn-sm btn-outline-secondary">Sort Desc</button>')
                            .appendTo(sortBox)
                            .click(() => api.order([i, 'desc']).draw());
                        $('<button class="btn btn-sm btn-outline-secondary">Clear Sort</button>')
                            .appendTo(sortBox)
                            .click(() => api.order([]).draw());

                        // Toggle dropdown + position it under the filter icon
                        $th.find('.filter-icon').on('click', function(e) {
                            e.stopPropagation();
                            $('.dropdown-filter').not(dropdown).hide();

                            var rect = this.getBoundingClientRect();

                            dropdown.css({
                                top: rect.bottom + window.scrollY,
                                left: rect.left + window.scrollX
                            }).toggle();
                        });

                        // Keep open when interacting
                        dropdown.on('click', e => e.stopPropagation());

                        // Filtering
                        dropdown.on('change', 'input[type="checkbox"]', function() {
                            var vals = dropdown.find('input:checked').map(function() {
                                return '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$';
                            }).get().join('|');
                            api.column(i).search(vals, true, false).draw();
                        });

                        // Search inside dropdown
                        searchBox.on('keyup', function() {
                            var val = this.value.toLowerCase();
                            dropdown.find('label').each(function() {
                                $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                            });
                        });
                    });

                    // Close all dropdowns on outside click
                    $(document).on('click', () => $('.dropdown-filter').hide());
                }
            });
        }

        $(document).ready(get_table_data);

        // Ajax for add new medium
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#NEW_MEDIUM_CREATE_FORM').on('submit', function(e) {
                e.preventDefault();


                $('#NEW_MEDIUM_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" disabled"><i class="bx bx-loader bx-spin"></i> VERIFYING..</button>');

                const formData = $(this).serialize();

                $.ajax({
                    url: "{{ url('/') }}/save_new_medium",
                    method: "POST",
                    data: formData,
                    dataType: "json",
                    success: function(data) {
                        console.log(data);

                        if (data.success) {
                            Swal.fire('Success!', data.success, 'success').then(() => {
                                $('#NAME').val('');
                                document.querySelector('#add_new_medium_modal .btn[data-bs-dismiss="modal"]').click();
                                get_table_data();
                                $('#NEW_MEDIUM_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                            });
                        } else if (data.error) {
                            Swal.fire('Error!', data.error, 'error');
                            $('#NEW_MEDIUM_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                        } else {
                            Swal.fire('Error!', 'Unexpected response format.', 'error');
                            $('#NEW_MEDIUM_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');
                        }
                    },
                    error: function(err) {
                        Swal.fire('Error!', err.responseText || 'Unexpected error', 'error');
                        $('#NEW_MEDIUM_CREATE_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">SAVE</button>');

                    }
                });
            });
        });
    </script>
</body>

</html>