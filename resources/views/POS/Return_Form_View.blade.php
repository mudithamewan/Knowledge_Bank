<style>
    /* =============================
           GLOBAL CONTAINER STYLES
        ============================== */
    .invoice_content * {
        font-family: 'Poppins', sans-serif;
        box-sizing: border-box;
    }

    .invoice_content {
        margin: 0;
        padding: 30px;
        color: #333;
    }

    .invoice_content h2 {
        color: #3A47D5;
        margin-bottom: 5px;
    }

    .invoice_content .header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .invoice_content .company-details {
        text-align: right;
        font-size: 13px;
    }

    .invoice_content .company-details strong {
        font-size: 15px;
    }

    .invoice_content hr {
        border: none;
        border-top: 2px solid #3A47D5;
        margin: 20px 0;
    }

    .invoice_content .invoice-info {
        margin-bottom: 20px;
    }

    .invoice_content .invoice-info p {
        margin: 4px 0;
        font-size: 13px;
    }

    .invoice_content .invoice-info strong {
        color: #3A47D5;
    }

    .invoice_content .billed-to {
        margin-top: 5px;
        font-size: 13px;
    }

    /* =============================
           TABLE STYLES (scoped to .tbl)
        ============================== */
    .tbl table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .tbl table th,
    .tbl table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
        font-size: 13px;
    }

    .tbl table th {
        background-color: #3A47D5;
        color: white;
        font-weight: 600;
    }

    .tbl table td {
        vertical-align: top;
    }

    .tbl .text-right {
        text-align: right;
    }

    /* =============================
           TOTAL BOX STYLES
        ============================== */
    .invoice_content .total-box {
        width: 250px;
        margin-left: auto;
        margin-top: 20px;
        font-size: 13px;
        border: 1px solid #ddd;
    }

    .invoice_content .total-box table {
        border: none;
        width: 100%;
    }

    .invoice_content .total-box td {
        border: none;
        padding: 8px 10px;
    }

    .invoice_content .total-box tr:nth-child(3) td {
        background-color: #3A47D5;
        color: #fff;
        font-weight: bold;
    }

    .invoice_content .text-right {
        text-align: right;
    }

    /* Keyboard focus styles */
    .return-item:focus {
        outline: 3px solid #3A47D5;
        outline-offset: 3px;
    }

    /* Optional: highlight row when focused */
    .return-item:focus-visible {
        box-shadow: 0 0 0 3px rgba(58, 71, 213, 0.3);
    }
</style>

<div class="row">
    <div class="col-xl-8">

        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex flex-wrap align-items-start">
                    <div class="me-2">
                        <h5 class="card-title mt-1 mb-0 text-primary">INVOICE VIEW</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">

                <div class="invoice_content">
                    <div class="header">
                        <h2 style="font-weight: 600;">INVOICE</h2>
                        <div class="company-details">
                            <strong>Knowledge Bank Publisher</strong><br>
                            VAT No: 102274292 - 7000<br>
                            No 7A Sethsiri Place, Pannipitiya, Sri Lanka<br>
                            Email: knowledgebank2013@gmail.com<br>
                            Hot Line: 0712 100 111 / 075 5100 111
                        </div>
                    </div>

                    <hr>

                    <div class="invoice-info">
                        <p><strong>Invoice No:</strong> {{$INVOICE_DATA->in_invoice_no}}</p>
                        <p><strong>Date:</strong> {{$INVOICE_DATA->in_inserted_date}}</p>
                        <p><strong>Payment Method:</strong> {{$INVOICE_DATA->mpt_name}}</p>

                        @if(!empty($CUSTOMER_DETAILS))
                        <div class="billed-to">
                            <strong>Billed To:</strong>
                            @if(isset($CUSTOMER_DETAILS->o_id))
                            {{$CUSTOMER_DETAILS->o_name}}
                            {{$CUSTOMER_DETAILS->o_address}}
                            @else
                            {{$CUSTOMER_DETAILS->c_title}} {{$CUSTOMER_DETAILS->c_name}}
                            {{$CUSTOMER_DETAILS->c_address}}
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="tbl">
                        <table>
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>#</th>
                                    <th>ITEM DESCRIPTION</th>
                                    <th>UNIT PRICE (LKR)</th>
                                    <th>QTY</th>
                                    <th>RETURNED QTY</th>
                                    <th>PRICE (LKR)</th>
                                    <th>DIS.%</th>
                                    <th>TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach($INVOICE_ITEMS_DATA as $index => $data)
                                <tr tabindex="-1">
                                    <td>
                                        @if($data->ini_is_returned == 1)
                                        <span class="badge badge-pill badge-soft-danger font-size-11">RETURNED</span>
                                        @else
                                        <input type="checkbox" class="return-item" value="{{$data->ini_id}}" data-total="{{ $data->ini_final_price * $data->ini_qty }}" aria-label="Return {{$data->p_name}}">
                                        @endif
                                    </td>
                                    <td>{{$index+1}}</td>
                                    <td>{{$data->p_name}}</td>
                                    <td class="text-right">{{number_format($data->ini_selling_price,2)}}</td>
                                    <td class="text-right">{{$data->ini_qty}}</td>
                                    <td class="text-right" width="12%">
                                        <input type="number" class="form-control form-control-sm returned-qty" min="1" max="{{$data->ini_qty}}" value="{{$data->ini_qty}}" data-unit-price="{{$data->ini_final_price}}" data-max="{{$data->ini_qty}}">
                                    </td>
                                    <td class="text-right">{{number_format(($data->ini_selling_price * $data->ini_qty),2)}}</td>
                                    <td class="text-right">{{$data->ini_discount_percentage}}</td>
                                    <td class="text-right">{{number_format(($data->ini_final_price * $data->ini_qty),2)}}</td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                    <div class="total-box">
                        <table>
                            <tr>
                                <td>Subtotal</td>
                                <td class="text-right">{{number_format($INVOICE_DATA->in_sub_total,2)}}</td>
                            </tr>
                            <tr>
                                <td>Discount (0%)</td>
                                <td class="text-right"><small>({{$INVOICE_DATA->in_discount_percentage}}%)</small> {{number_format($INVOICE_DATA->in_discount_amount,2)}}</td>
                            </tr>
                            <tr>
                                <td>Total Payable</td>
                                <td class="text-right">{{number_format($INVOICE_DATA->in_total_payable,2)}}</td>
                            </tr>
                            <tr>
                                <td>Paid</td>
                                <td class="text-right">{{number_format($INVOICE_DATA->in_total_paid_amount,2)}}</td>
                            </tr>
                            <tr>
                                <td>Balance</td>
                                <td class="text-right">{{number_format($INVOICE_DATA->in_total_balance,2)}}</td>
                            </tr>
                        </table>
                    </div>

                    <hr>
                    <a href="{{url('/')}}/Normal_Invoice/{{urlencode(base64_encode($INVOICE_DATA->in_id))}}" class="btn btn-success waves-effect btn-label waves-light w-lg"><i class="mdi mdi-download label-icon"></i> INVOICE</a>
                    <a href="{{url('/')}}/VAT_Invoice/{{urlencode(base64_encode($INVOICE_DATA->in_id))}}" class="btn btn-success waves-effect btn-label waves-light w-lg"><i class="bx bx-receipt label-icon"></i> VAT INVOICE</a>

                </div>

            </div>
        </div>

    </div>


    <div class="col-xl-4">

        <div class="card">
            <div class="card-header bg-transparent border-bottom">
                <div class="d-flex flex-wrap align-items-start">
                    <div class="me-2">
                        <h5 class="card-title mt-1 mb-0 text-primary">RETURN FORM</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($INVOICE_DATA->in_is_returned == 1)
                <div class="alert alert-danger" role="alert">
                    The invoice has already been returned!
                </div>
                @else

                <div id="success_msg"></div>

                <form id="RETURN_FORM_SUBMIT">
                    @csrf
                    <input type="hidden" name="INVOICE_ID" value="{{$INVOICE_DATA->in_id}}">
                    <input type="hidden" name="MW_ID" value="{{$MW_ID}}">
                    <input type="hidden" name="RETURNED_ITEMS">
                    <input type="hidden" name="INVOICE_AMOUNT" value="{{$INVOICE_DATA->in_total_payable}}">
                    <input type="hidden" name="RETURNED_ITEMS_AMOUNT" id="RETURNED_ITEMS_AMOUNT" value="{{$INVOICE_DATA->in_total_payable}}">

                    <div class="row">
                        <div class="col-xl-12">
                            <div class="card mini-stats-wid border">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Invoice Amount</p>
                                            <h5 class="mb-0">LKR {{number_format($INVOICE_DATA->in_total_payable,2)}}</h5>
                                        </div>
                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bxs-cart-alt "></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12">
                            <div class="card mini-stats-wid border">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap">
                                        <div class="me-3">
                                            <p class="text-muted mb-2">Returned Amount</p>
                                            <h5 class="mb-0" id="RETURNED_ITEMS_AMT">LKR {{number_format($INVOICE_DATA->in_total_payable,2)}}</h5>
                                        </div>
                                        <div class="avatar-sm ms-auto">
                                            <div class="avatar-title bg-light rounded-circle text-primary font-size-20">
                                                <i class="bx bxs-share"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">

                                <input type="radio" class="btn-check" name="TYPE" id="RETURN_INVOICE" value="RETURN_INVOICE" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="RETURN_INVOICE">RETURN INVOICE</label>

                                <input type="radio" class="btn-check" name="TYPE" id="RETURN_MONEY" value="RETURN_MONEY" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="RETURN_MONEY">RETURN MONEY</label>
                            </div>
                        </div>

                        <div class="col-xl-12">
                            <label for="">Remark</label>
                            <textarea name="REMARK" id="REMARK" class="form-control" rows="3" maxlength="255"></textarea>
                        </div>

                        <div class="col-xl-12 mt-3">
                            <div id="RETURN_FORM_SUBMIT_BTN">
                                <button class="btn btn-danger w-100" type="submit">SUBMIT</button>
                            </div>
                        </div>
                    </div>


                </form>
                @endif
            </div>
        </div>
    </div>


</div>

<div class="modal fade" id="return_modal_view" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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


<script>
    $(document).ready(function() {

        /* -----------------------------
           INITIAL ROW FOCUS
        ------------------------------ */
        const rows = $('.tbl tbody tr');
        if (rows.length) {
            rows.first().attr('tabindex', '0').focus();
        }

        /* -----------------------------
           ROW KEYBOARD NAVIGATION
        ------------------------------ */
        $(document).on('keydown', '.tbl tbody tr', function(e) {
            const rows = $('.tbl tbody tr');
            const index = rows.index(this);

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (rows.eq(index + 1).length) {
                    rows.eq(index + 1).focus();
                }
            }

            if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (rows.eq(index - 1).length) {
                    rows.eq(index - 1).focus();
                }
            }

            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const checkbox = $(this).find('.return-item');
                checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
                checkbox.focus();
            }
        });

        /* -----------------------------
           ENTER KEY FOR CHECKBOX
        ------------------------------ */
        $(document).on('keydown', '.return-item', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $(this).prop('checked', !$(this).prop('checked')).trigger('change');
            }
        });

        /* -----------------------------
           CALCULATE RETURNED AMOUNT
        ------------------------------ */
        function updateReturnedAmount() {
            let total = 0;
            let returnedItems = [];

            $('.tbl tbody tr').each(function() {

                const row = $(this);
                const checkbox = row.find('.return-item');

                // Skip rows without checkbox or unchecked
                if (!checkbox.length || !checkbox.is(':checked')) {
                    return;
                }

                const itemId = checkbox.val();
                const qtyInput = row.find('.returned-qty');

                const unitPrice = parseFloat(qtyInput.data('unit-price'));
                const maxQty = parseInt(qtyInput.data('max'));

                let returnedQty = parseInt(qtyInput.val()) || 0;

                // Safety guards
                if (returnedQty < 1) returnedQty = 1;
                if (returnedQty > maxQty) returnedQty = maxQty;

                qtyInput.val(returnedQty);

                // Amount calc
                total += unitPrice * returnedQty;

                // Push ID + QTY
                returnedItems.push(itemId + ':' + returnedQty);
            });

            // Update UI
            $('#RETURNED_ITEMS_AMT').text(
                'LKR ' + total.toLocaleString('en-US', {
                    minimumFractionDigits: 2
                })
            );

            // Hidden inputs
            $('#RETURNED_ITEMS_AMOUNT').val(total.toFixed(2));
            $('input[name="RETURNED_ITEMS"]').val(returnedItems.join(','));
        }


        // checkbox click
        $(document).on('change', '.return-item', function() {
            updateReturnedAmount();
        });

        // qty typing
        $(document).on('input change', '.returned-qty', function() {
            updateReturnedAmount();
        });

        // auto-check when qty edited (UX win)
        $(document).on('input', '.returned-qty', function() {
            const row = $(this).closest('tr');
            const checkbox = row.find('.return-item');

            if (checkbox.length && !checkbox.is(':checked')) {
                checkbox.prop('checked', true);
            }
        });

    });


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


    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#RETURN_FORM_SUBMIT').on('submit', function(e) {
            e.preventDefault();

            $('#RETURN_FORM_SUBMIT_BTN').html('<button class="btn btn-danger w-100" disabled"><i class="bx bx-loader bx-spin"></i> VERIFYING..</button>');
            $('#success_msg').html('');

            const formData = $(this).serialize();

            $.ajax({
                url: "{{ url('/') }}/return_invoice_action",
                method: "POST",
                data: formData,
                dataType: "json",
                success: function(data) {

                    if (data.success) {
                        if (data.print_invoice == true) {
                            loadPrintInvoice(`{{url('/')}}/PrintReturnInvoice/` + encodeURIComponent(btoa(data.ri_id)));
                        }
                        $('#RETURN_FORM_SUBMIT_BTN').html('<button class="btn btn-danger w-100" disabled>RETURNED</button>');
                        $('#success_msg').html('<div class="alert alert-success" role="alert">' + data.success + '</div>');

                        const closeButton1 = document.querySelector('#return_modal .btn[data-bs-dismiss="modal"]');
                        if (closeButton1) {
                            closeButton1.click();
                        }
                    } else if (data.error) {
                        Swal.fire('Error!', data.error, 'error');
                        $('#RETURN_FORM_SUBMIT_BTN').html('<button class="btn btn-danger w-100" type="submit">SUBMIT</button>');
                    } else {
                        Swal.fire('Error!', 'Unexpected response format.', 'error');
                        $('#RETURN_FORM_SUBMIT_BTN').html('<button class="btn btn-danger w-100" type="submit">SUBMIT</button>');
                    }
                },
                error: function(err) {
                    Swal.fire('Error!', err.responseText || 'Unexpected error', 'error');
                    $('#RETURN_FORM_SUBMIT_BTN').html('<button class="btn btn-danger w-100" type="submit">SUBMIT</button>');

                }
            });
        });
    });
</script>