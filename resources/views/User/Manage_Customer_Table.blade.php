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
        /* changed: float above scroll area */
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 10px;
        z-index: 9999;
        /* keep it on top */
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

<div class="card">
    <div class="card-header bg-transparent border-bottom">
        <div class="d-flex flex-wrap align-items-start">
            <div class="me-2">
                <h5 class="card-title mt-2 text-primary">CUSTOMER DETAILS</h5>
            </div>
            <div class="ms-auto">
                <form action="{{url('/')}}/get_customer_filter_result_table" method="post">
                    @csrf
                    <input type="hidden" name="FROM_DATE" value="{{$FROM_DATE}}">
                    <input type="hidden" name="TO_DATE" value="{{$TO_DATE}}">
                    <input type="hidden" name="CONTACT_NUMBER" value="{{$CONTACT_NUMBER}}">
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
            <table id="org_table" class="table table-sm">
                <thead>
                    <tr>
                        <th>CONTACT <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>NAME <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>DOB <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>NIC <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>EMAIL <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>ADDRESS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>GENDER <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>STATUS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
    function get_org_table_data() {

        // safe destroy
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#org_table')) {
            $('#org_table').DataTable().clear().destroy();
        }

        function renderNA(data) {
            return data && data.toString().trim() !== '' ? data : 'N/A';
        }

        var table = $('#org_table').DataTable({
            serverSide: false,
            processing: true,
            orderCellsTop: true,
            columnDefs: [{
                orderable: true,
                targets: "_all"
            }],
            language: {
                processing: '<i class="bx bx-loader bx-spin h1 text-primary"></i>'
            },
            ajax: {
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                url: "{{url('/')}}/get_customer_filter_result_table",
                data: {
                    FROM_DATE: '{{$FROM_DATE}}',
                    TO_DATE: '{{$TO_DATE}}',
                    CONTACT_NUMBER: '{{$CONTACT_NUMBER}}',
                    DOWNLOAD: 'NO'
                },
                dataSrc: "data"
            },
            order: [],
            columns: [{
                    data: 'c_contact',
                    render: renderNA
                },
                {
                    data: 'c_name',
                    render: renderNA
                },
                {
                    data: 'c_dob',
                    render: renderNA
                },
                {
                    data: 'c_nic',
                    render: renderNA
                },
                {
                    data: 'c_email',
                    render: renderNA
                },
                {
                    data: 'c_address',
                    render: renderNA
                },
                {
                    data: 'c_gender',
                    render: renderNA
                },
                {
                    data: 'c_is_suspend',
                    render: function(d) {
                        if (d == 1) return '<span class="badge badge-pill badge-soft-danger font-size-11">Suspend</span>';
                        if (d == 0) return '<span class="badge badge-pill badge-soft-success font-size-11">Active</span>';
                        return 'N/A';
                    }
                },
                {
                    data: 'c_id',
                    render: d =>
                        d && d.toString().trim() !== '' ?
                        '<a href="<?= url('/') ?>/Customer_Profile/' + encodeURIComponent(btoa(d)) + '" class="btn btn-outline-primary btn-sm">PROFILE</a>' : 'N/A'
                }
            ],

            initComplete: function() {
                var api = this.api();

                $('#org_table thead th').each(function(i) {

                    if (i === 8) return; // skip ACTION column

                    var $th = $(this);
                    var dropdown = $('<div class="dropdown-filter"></div>');

                    // attach dropdown to body so it's not clipped by table scroll
                    $('body').append(dropdown);

                    // search box
                    var searchBox = $('<input type="text" placeholder="Search...">').appendTo(dropdown);

                    // select / deselect all
                    var actions = $('<div class="actions"></div>').prependTo(dropdown);
                    $('<button type="button" class="btn btn-sm btn-light">Select All</button>')
                        .appendTo(actions)
                        .click(function() {
                            dropdown.find('input[type="checkbox"]').prop('checked', true).trigger('change');
                        });

                    $('<button type="button" class="btn btn-sm btn-light">Deselect All</button>')
                        .appendTo(actions)
                        .click(function() {
                            dropdown.find('input[type="checkbox"]').prop('checked', false).trigger('change');
                        });

                    // unique values for this column
                    api.column(i).data().unique().sort().each(function(d) {
                        $('<label><input type="checkbox" value="' + d + '"> ' + d + '</label>').appendTo(dropdown);
                    });

                    // sort buttons
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

                    // toggle dropdown + position under filter icon
                    $th.find('.filter-icon').on('click', function(e) {
                        e.stopPropagation();
                        $('.dropdown-filter').not(dropdown).hide();

                        var rect = this.getBoundingClientRect();

                        dropdown.css({
                            top: rect.bottom + window.scrollY,
                            left: rect.left + window.scrollX
                        }).toggle();
                    });

                    // keep dropdown open when clicking inside
                    dropdown.on('click', function(e) {
                        e.stopPropagation();
                    });

                    // apply filter
                    dropdown.on('change', 'input[type="checkbox"]', function() {
                        var vals = dropdown.find('input:checked')
                            .map(function() {
                                return '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$';
                            }).get().join('|');

                        api.column(i).search(vals, true, false).draw();
                    });

                    // search inside dropdown
                    searchBox.on('keyup', function() {
                        var val = this.value.toLowerCase();
                        dropdown.find('label').each(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                        });
                    });
                });

                // close all dropdowns on outside click
                $(document).on('click', function() {
                    $('.dropdown-filter').hide();
                });
            }
        });
    }

    $(document).ready(get_org_table_data);
</script>