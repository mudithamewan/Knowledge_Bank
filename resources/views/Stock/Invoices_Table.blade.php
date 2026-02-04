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
        /* changed from absolute -> fixed */
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
                <h5 class="card-title mt-2 text-primary">INVOICE DETAILS</h5>
            </div>
            <div class="ms-auto">
                <form action="{{url('/')}}/get_invoices_filter_result_table" method="post">
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
                        <th style="white-space: nowrap;">INVOICE CODE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">INVOICE DATE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">INVOICE BY <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">SUB TOTAL <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">DISCOUNT PERCENTAGE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">DISCOUNT AMOUNT <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">TOTAL PAYABLE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">TOTAL PAID AMOUNT <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">BALANCE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">PAYMENT MODE <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">CREDIT PERIOD <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">EXCEEDED DAYS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">STOCK LOCATION <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">STATUS <span class="filter-icon"><i class="bx bx-filter"></i></span></th>
                        <th style="white-space: nowrap;">ACTION</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="invoice_view" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">INVOICE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <span id="LOAD_INVOICE_VIEW" class="VIEW_EMPTY"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="invoice_view2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">INVOICE</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfFrame" src="" width="100%" height="700px" style="border:none; display:none;"></iframe>
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
    function loadPrintInvoice(pdfUrl) {
        // const modal = new bootstrap.Modal(document.getElementById('invoice_view2'));
        // modal.show();

        const pdfFrame = document.getElementById('pdfFrame');
        pdfFrame.style.display = 'block';
        pdfFrame.src = pdfUrl;

        pdfFrame.onload = function() {
            pdfFrame.contentWindow.focus();
            pdfFrame.contentWindow.print();
        };
    }

    function get_table_data() {
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
                url: "{{url('/')}}/get_invoices_filter_result_table",
                data: {
                    FROM_DATE: '{{$FROM_DATE}}',
                    TO_DATE: '{{$TO_DATE}}',
                    MW_ID: '{{$MW_ID}}',
                    DOWNLOAD: 'NO'
                },
                dataSrc: "data"
            },
            order: [],
            language: {
                processing: '<i class="bx bx-loader bx-spin h1 align-middle me-2 text-primary"></i>',
            },
            columns: [{
                    data: 'in_invoice_no',
                    render: renderNA
                },
                {
                    data: 'in_updated_date',
                    render: renderNA
                },
                {
                    data: 'su_name',
                    render: renderNA
                },
                {
                    data: 'in_sub_total',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'in_discount_percentage',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'in_discount_amount',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'in_total_payable',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'in_total_paid_amount',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'in_total_balance',
                    render: function(data, type) {
                        if (!data || isNaN(data)) return 'N/A';
                        let value = parseFloat(data);
                        if (type === 'display') {
                            return `<span data-value="${value}">${value.toLocaleString('en-US', { minimumFractionDigits: 2 })}</span>`;
                        }
                        return value;
                    }
                },
                {
                    data: 'mpt_name',
                    render: renderNA
                },
                {
                    data: 'mcp_name',
                    render: renderNA
                },
                {
                    data: 'exceeded_credit_days',
                    render: renderNA
                },
                {
                    data: 'mw_name',
                    render: renderNA
                },
                {
                    data: 'in_is_returned',
                    render: function(data, type, row) {
                        if (row['in_is_partial_returned'] == 1) {
                            return `<span class="badge badge-pill badge-soft-danger font-size-11">P.RETURNED</span>`;
                        } else if (data == 1) {
                            return `<span class="badge badge-pill badge-soft-danger font-size-11">RETURNED</span>`;
                        } else {
                            return `<span class="badge badge-pill badge-soft-success font-size-11">ACTIVE</span>`;
                        }

                    }

                },
                {
                    data: 'in_id',
                    render: function(d) {
                        const baseUrl = "{{ url('/') }}";
                        const csrf = "{{ csrf_token() }}";
                        return `
                            <div class="d-flex gap-2">
                                <button class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#invoice_view"
                                    onclick="ajax_action(
                                        '${baseUrl}/load_invoice',
                                        'LOAD_INVOICE',
                                        JSON.stringify({ INVOICE_ID: ${d} }),
                                        '${csrf}'
                                    )">
                                    VIEW
                                </button>
                                
                                <a href="javascript:void(0)" 
                                    class="btn btn-success btn-sm"
                                    onclick="loadPrintInvoice('${baseUrl}/PrintInvoice/${btoa(d)}')">
                                    PRINT
                                </a>
                            </div>
                        `;
                    }
                }
            ],
            initComplete: function() {
                var api = this.api();

                $('#result_table thead th').each(function(i) {
                    // skip ACTION column (index 12)
                    if (i === 12) return;

                    var $th = $(this);
                    var dropdown = $('<div class="dropdown-filter"></div>');

                    // attach dropdown to body (not inside table) so it won't be clipped
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

                    // Populate checkboxes with unique values
                    api.column(i).data().unique().sort().each(function(d) {
                        let value = d;
                        let displayText = (d === null || d === '') ? 'N/A' : d;

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

                    // Toggle dropdown with positioning
                    $th.find('.filter-icon').on('click', function(e) {
                        e.stopPropagation();
                        $('.dropdown-filter').not(dropdown).hide();

                        var rect = this.getBoundingClientRect();

                        dropdown.css({
                            top: rect.bottom + window.scrollY,
                            left: rect.left + window.scrollX
                        }).toggle();
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

                    // Filter checkbox list by search box
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