    <link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

    <form id="EDIT_PRODUCT_FORM" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="P_ID" id="P_ID" value="{{$PRODUCT_DETAILS->p_id}}">

        <div class="row">
            <div class="col-xl-12">
                <div class="section-title-wrapper">
                    <div class="section-line"></div>
                    <div class="section-title">BASIC INFORMATION</div>
                    <div class="section-line"></div>
                </div>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">PRODUCT NAME</label>
                <input type="text" name="NAME" id="NAME" class="form-control" value="{{$PRODUCT_DETAILS->p_name}}">
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">ISBN</label>
                <input type="text" name="ISBN" id="ISBN" class="form-control" value="{{$PRODUCT_DETAILS->p_isbn}}">
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">AUTHOR</label>
                <input type="text" name="AUTHOR" id="AUTHOR" class="form-control" value="{{$PRODUCT_DETAILS->p_author}}">
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">PUBLISHER</label>
                <select name="PUBLISHER_ID" id="PUBLISHER_ID" class="form-control select2" width="100%">
                    @foreach($PUBLISHERS as $PUBLISHER)
                    @if($PRODUCT_DETAILS->p_publisher_id == $PUBLISHER->o_id)
                    <option value="{{$PUBLISHER->o_id}}" selected>{{$PUBLISHER->o_business_name}} ({{$PUBLISHER->o_name}})</option>
                    @else
                    <option value="{{$PUBLISHER->o_id}}">{{$PUBLISHER->o_business_name}} ({{$PUBLISHER->o_name}})</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">MEDIUM</label>
                <select name="MM_ID" id="MM_ID" class="form-control select2" width="100%">
                    @foreach($MEDIUMS as $MEDIUM)
                    @if($PRODUCT_DETAILS->p_mm_id == $MEDIUM->mm_id)
                    <option value="{{$MEDIUM->mm_id}}" selected>{{$MEDIUM->mm_name}}</option>
                    @else
                    <option value="{{$MEDIUM->mm_id}}">{{$MEDIUM->mm_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">GRADES</label>
                <div id="selectGrades">
                    <select class="select2 form-select select2-multiple" multiple="multiple" id="MG_ID" data-placeholder="Choose ..." style="width:100%" name="MG_ID[]">
                        @foreach($GRADES as $DATA)
                        @if (in_array($DATA->mg_id, $PRODUCT_GRADES_IDS))
                        <option value="{{ $DATA->mg_id }}" selected>{{ $DATA->mg_name }}</option>
                        @else
                        <option value="{{ $DATA->mg_id }}">{{ $DATA->mg_name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">SUBJECTS</label>
                <div id="selectSubjects">
                    <select class="select2 form-select select2-multiple" multiple="multiple" id="MS_ID" data-placeholder="Choose ..." style="width:100%" name="MS_ID[]">
                        @foreach($SUBJECTS as $DATA)
                        @if (in_array($DATA->ms_id, $PRODUCT_SUBJECTS_IDS))
                        <option value="{{ $DATA->ms_id }}" selected>{{ $DATA->ms_name }}</option>
                        @else
                        <option value="{{ $DATA->ms_id }}">{{ $DATA->ms_name }}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">CATEGORY</label>
                <select name="MC_ID" id="MC_ID" class="form-control select2" width="100%">
                    @foreach($CATEGORIES as $CATEGORY)
                    @if($PRODUCT_DETAILS->p_mc_id == $CATEGORY->mc_id)
                    <option value="{{$CATEGORY->mc_id}}" selected>{{$CATEGORY->mc_name}}</option>
                    @else
                    <option value="{{$CATEGORY->mc_id}}">{{$CATEGORY->mc_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">SUB CATEGORY</label>
                <select class="js-example-basic-single form-control" data-placeholder="Choose ..." style="width:100%" name="MSC_ID" id="MSC_ID">
                    @if($PRODUCT_DETAILS->p_msc_id)
                    <option value="{{ $PRODUCT_DETAILS->p_msc_id }}" selected>
                        {{ $PRODUCT_DETAILS->msc_name  }}
                    </option>
                    @endif
                </select>
            </div>
            <div class="col-lg-6 mt-2">
                <label for="">STATUS <span class="text-danger">*</span></label>
                <select name="ACTIVE_STATUS" id="ACTIVE_STATUS" class="form-select" required>
                    @if($PRODUCT_DETAILS->p_is_active == 1)
                    <option value="ACTIVE" selected>ACTIVE</option>
                    <option value="INACTIVE">INACTIVE</option>
                    @else
                    <option value="ACTIVE">ACTIVE</option>
                    <option value="INACTIVE" selected>INACTIVE</option>
                    @endif
                </select>
            </div>
            <div class="col-xl-12 mt-2">
                <label for="">DESCRIPTION</label>
                <textarea name="DESCRIPTION" id="DESCRIPTION" class="form-control" rows="4">{{$PRODUCT_DETAILS->p_description}}</textarea>
            </div>

            <div class="col-xl-12 mt-4">
                <div class="section-title-wrapper">
                    <div class="section-line"></div>
                    <div class="section-title">ADDITIONAL INFORMATION</div>
                    <div class="section-line"></div>
                </div>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">EDITION</label>
                <input type="text" name="EDITION" id="EDITION" class="form-control" value="{{$PRODUCT_DETAILS->p_edition}}">
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">PUB. YEAR</label>
                <select name="PUBLICATION_YEAR" id="PUBLICATION_YEAR" class="form-select">
                    <option value="">Select Year</option>
                    <?php for ($y = date("Y"); $y >= 1900; $y--) : ?>
                        @if($PRODUCT_DETAILS->p_published_year == $y)
                        <option value="<?= $y ?>" selected><?= $y ?></option>
                        @else
                        <option value="<?= $y ?>"><?= $y ?></option>
                        @endif
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">PAGE COUNT</label>
                <input type="number" name="PAGE_COUNT" id="PAGE_COUNT" class="form-control" value="{{$PRODUCT_DETAILS->p_page_count}}">
            </div>
            <div class="col-xl-6 mt-2">
                <label for="">FORMAT</label>
                <select name="FORMAT" id="FORMAT" class="form-select">
                    @foreach($BOOK_FORMATS as $FORMAT)
                    @if($PRODUCT_DETAILS->p_mbf_id == $FORMAT->mbf_id)
                    <option value="{{$FORMAT->mbf_id}}" selected>{{$FORMAT->mbf_name}}</option>
                    @else
                    <option value="{{$FORMAT->mbf_id}}">{{$FORMAT->mbf_name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>

            <div class="col-xl-4 mt-3">
                <div id="EDIT_PRODUCT_FORM_BTN">
                    <button class="btn btn-primary w-100">UPDATE</button>
                </div>
            </div>
        </div>

    </form>

    <script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#EDIT_PRODUCT_FORM').on('submit', function(e) {
                e.preventDefault();
                $('#EDIT_PRODUCT_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    url: "{{url('/')}}/update_product",
                    type: "POST",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {

                        if (data.success) {
                            Swal.fire(
                                'Success!',
                                data.success,
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                } else {
                                    location.reload();
                                }
                            });
                        }

                        if (data.error) {
                            Swal.fire(
                                'Error!',
                                data.error,
                                'error'
                            );
                            $('#EDIT_PRODUCT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                        }
                    },
                    error: function(error) {
                        Swal.fire(
                            'Error!',
                            error,
                            'error'
                        );
                        $('#EDIT_PRODUCT_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                    }
                });
            });
        });


        $('#profile_edit_modal').on('shown.bs.modal', function() {
            // re-init select2 for all selects inside this modal
            $(this).find('.select2').select2({
                dropdownParent: $('#profile_edit_modal')
            });
        });

        $('#profile_edit_modal').on('shown.bs.modal', function() {
            let modal = $(this);

            // Small delay ensures AJAX content is in DOM
            setTimeout(function() {
                // Destroy any old select2 (avoids duplicates)
                modal.find('#MSC_ID.select2-hidden-accessible').select2('destroy');

                // Init MSC_ID
                modal.find('#MSC_ID').select2({
                    dropdownParent: modal,
                    placeholder: "Choose...",
                    ajax: {
                        url: function() {
                            return '<?= url('/') ?>/get_sub_categories/' + $('#MC_ID').val();
                        },
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(function(item) {
                                    return {
                                        id: item.msc_id,
                                        text: item.msc_name
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1
                });

                // Rebind MC_ID change
                modal.find('#MC_ID').off('change').on('change', function() {
                    modal.find('#MSC_ID').val(null).trigger('change');
                });

            }, 200); // delay to let AJAX inject content
        });
    </script>