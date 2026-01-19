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
        position: absolute;
        background: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        padding: 10px;
        z-index: 1000;
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


<div class="table-responsive mt-4">
    <table id="result_table" class="table table-sm">
        <thead>
            <tr>
                <th style="white-space: nowrap;">DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                <th style="white-space: nowrap;">DESCRIPTION <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>


<script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{url('/')}}/assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>

<script>
    function get_table_data() {
        // safely destroy any existing instance
        if ($.fn.DataTable.isDataTable('#result_table')) {
            $('#result_table').DataTable().clear().destroy();
        }

        function renderNA(data) {
            return data && data.toString().trim() !== '' ? data : 'N/A';
        }

        var table = $('#result_table').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{csrf_token()}}"
                },
                url: "{{url('/')}}/load_timeline_by_user_table_data",
                data: {
                    FROM_DATE: '{{$FROM_DATE}}',
                    TO_DATE: '{{$TO_DATE}}',
                    USER_ID: '{{$USER_ID}}',
                    DOWNLOAD: 'NO'
                },
                dataSrc: "data"
            },
            order: [],
            language: {
                processing: '<i class="bx bx-loader bx-spin h1 align-middle me-2 text-primary"></i>',
            },
            columns: [{
                data: 'uwl_inserted_date',
                render: renderNA
            }, {
                data: 'uwl_description',
                render: renderNA
            }],
            initComplete: function() {
                var api = this.api();

                $('#result_table thead th').each(function(i) {
                    var $th = $(this);
                    var dropdown = $('<div class="dropdown-filter"></div>');

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

                    // Populate checkboxes with unique values
                    api.column(i).data().unique().sort().each(function(d) {
                        let displayText = d;
                        let value = d;

                        // If number column, get raw value
                        if (i === 10 || i === 11) { // selling price or available qty
                            value = parseFloat(d);
                            displayText = isNaN(value) ? 'N/A' : value.toLocaleString('en-US', {
                                minimumFractionDigits: i === 10 ? 2 : 0
                            });
                        }

                        $('<label><input type="checkbox" value="' + value + '"> ' + displayText + '</label>').appendTo(dropdown);
                    });

                    // Sorting buttons
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

                    $th.append(dropdown);

                    // Toggle dropdown
                    $th.find('.filter-icon').on('click', function(e) {
                        e.stopPropagation();
                        $('.dropdown-filter').not(dropdown).hide();
                        dropdown.toggle();
                    });

                    // Keep open when clicking inside
                    dropdown.on('click', function(e) {
                        e.stopPropagation();
                    });

                    // Checkbox filtering
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

    $(document).ready(function() {
        get_table_data();
    });
</script>