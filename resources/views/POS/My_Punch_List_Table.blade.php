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
        /* was absolute */
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

<div class="card">
    <div class="card-header bg-transparent border-bottom">
        <div class="d-flex flex-wrap align-items-start">
            <div class="me-2">
                <h5 class="card-title mt-2 text-primary">PUNCH DETAILS</h5>
            </div>
            <div class="ms-auto">
                <form action="{{url('/')}}/get_my_punch_filterd_table_data" method="post">
                    @csrf
                    <input type="hidden" name="FROM_DATE" value="{{$FROM_DATE}}">
                    <input type="hidden" name="TO_DATE" value="{{$TO_DATE}}">
                    <input type="hidden" name="MW_ID" value="{{$MW_ID}}">
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
                        <th style="white-space: nowrap;">START DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">START BY <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">POS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">STARTING VALUE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">AMOUNT <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">STAGE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">END DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">ACTION</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="punching_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ACTION VIEW</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="PUNCHING_MODAL_VIEW"></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button></div>
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
            serverSide: false,
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
                url: "{{url('/')}}/get_my_punch_filterd_table_data",
                data: {
                    FROM_DATE: '{{$FROM_DATE}}',
                    TO_DATE: '{{$TO_DATE}}',
                    MW_ID: '{{$MW_ID}}',
                    DOWNLOAD: 'NO'
                },
                dataSrc: "data"
            },
            order: [],
            columns: [{
                    data: 'pu_inserted_date',
                    render: renderNA
                },
                {
                    data: 'su_name',
                    render: renderNA
                },
                {
                    data: 'mw_name',
                    render: renderNA
                },
                {
                    data: 'pu_cash_on_hand',
                    render: renderNA
                },
                {
                    data: 'pu_amount',
                    render: renderNA
                },
                {
                    data: 'pus_name',
                    render: renderNA
                },
                {
                    data: 'pu_end_date',
                    render: renderNA
                },
                {
                    data: 'pu_id',
                    render: function(data, type, row) {
                        if (row['pus_id'] == 1) {
                            const baseUrl = "{{ url('/') }}";
                            const csrf = "{{ csrf_token() }}";
                            return `
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#punching_modal"
                                        onclick="ajax_action(
                                            '${baseUrl}/load_end_punch_view',
                                            'PUNCHING_MODAL',
                                            JSON.stringify({ PU_ID: ${data} }),
                                            '${csrf}'
                                        )">
                                        END
                                    </button>
                                </div>
                            `;
                        } else {
                            return ``;
                        }
                    }
                }
            ],
            initComplete: function() {
                var api = this.api();

                $('#result_table thead th').each(function(i) {
                    // skip ACTION column (index 7)
                    if (i === 7) return;

                    var $th = $(this);
                    var dropdown = $('<div class="dropdown-filter"></div>');

                    // attach dropdown to body so it's not clipped by table scroll
                    $('body').append(dropdown);

                    // Search box inside dropdown
                    var searchBox = $('<input type="text" placeholder="Search...">').appendTo(dropdown);

                    // Select all / Deselect all
                    var actions = $('<div class="actions"></div>').prependTo(dropdown);
                    $('<button type="button" class="btn btn-sm btn-light">Select All</button>')
                        .appendTo(actions)
                        .click(() => dropdown.find('input[type="checkbox"]').prop('checked', true).trigger('change'));
                    $('<button type="button" class="btn btn-sm btn-light">Deselect All</button>')
                        .appendTo(actions)
                        .click(() => dropdown.find('input[type="checkbox"]').prop('checked', false).trigger('change'));

                    // Unique values from full dataset
                    api.column(i).data().unique().sort().each(function(d) {
                        $('<label><input type="checkbox" value="' + d + '"> ' + d + '</label>').appendTo(dropdown);
                    });

                    // Sorting options
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

                    // Keep dropdown open when clicking inside
                    dropdown.on('click', function(e) {
                        e.stopPropagation();
                    });

                    // Filtering when checkboxes change
                    dropdown.on('change', 'input[type="checkbox"]', function() {
                        var vals = dropdown.find('input:checked').map(function() {
                            return '^' + $.fn.dataTable.util.escapeRegex(this.value) + '$';
                        }).get().join('|');
                        api.column(i).search(vals, true, false).draw();
                    });

                    // Filter checkboxes by search box
                    searchBox.on('keyup', function() {
                        var val = this.value.toLowerCase();
                        dropdown.find('label').each(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(val) > -1);
                        });
                    });
                });

                // Close dropdowns when clicking outside
                $(document).on('click', function() {
                    $('.dropdown-filter').hide();
                });
            }
        });
    }

    $(document).ready(get_table_data);
</script>