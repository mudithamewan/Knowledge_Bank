<!doctype html>
<html lang="en">

<head>
    @include('Layout/head')
    <link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{url('/')}}/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
    <style>
        .section-title-wrapper {
            display: flex;
            align-items: center;
            text-align: center;
            margin-bottom: 1rem;
        }

        .section-line {
            flex: 1;
            height: 2px;
            background-color: #ccc;
        }

        .section-title {
            margin: 0 10px;
            white-space: nowrap;
            /* font-weight: 600; */
            /* font-size: 1rem; */
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

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">ADD NEW PRODUCT</h4>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <form id="NEW_PRODUCT_CREATE_FORM" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">BASIC INFORMATION</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">PRODUCT NAME </label>
                                            <div class="col-md-10">
                                                <input type="text" name="NAME" id="NAME" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">ISBN</label>
                                            <div class="col-md-10">
                                                <input type="text" name="ISBN" id="ISBN" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">AUTHOR</label>
                                            <div class="col-md-10">
                                                <input type="text" name="AUTHOR" id="AUTHOR" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">PUBLISHER</label>
                                            <div class="col-md-10">
                                                <select name="PUBLISHER_ID" id="PUBLISHER_ID" class="form-control select2" width="100%">
                                                    <option value="" hidden selected disabled>Choose..</option>
                                                    @foreach($PUBLISHERS as $PUBLISHER)
                                                    <option value="{{$PUBLISHER->o_id}}">{{$PUBLISHER->o_business_name}} ({{$PUBLISHER->o_name}})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">MEDIUM</label>
                                            <div class="col-md-10">
                                                <select name="MM_ID" id="MM_ID" class="form-control select2" width="100%">
                                                    <option value="" hidden selected disabled>Choose..</option>
                                                    @foreach($MEDIUMS as $MEDIUM)
                                                    <option value="{{$MEDIUM->mm_id}}">{{$MEDIUM->mm_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">GRADES</label>
                                            <div class="col-md-10">
                                                <div id="selectGrades">
                                                    <select class="select2 form-select select2-multiple" multiple="multiple" id="MG_ID" data-placeholder="Choose ..." style="width:100%" name="MG_ID[]">
                                                        @foreach($GRADES as $DATA)
                                                        <option value="{{ $DATA->mg_id }}">{{ $DATA->mg_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">SUBJECTS</label>
                                            <div class="col-md-10">
                                                <div id="selectSubjects">
                                                    <select class="select2 form-select select2-multiple" multiple="multiple" id="MS_ID" data-placeholder="Choose ..." style="width:100%" name="MS_ID[]">
                                                        @foreach($SUBJECTS as $DATA)
                                                        <option value="{{ $DATA->ms_id }}">{{ $DATA->ms_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">CATEGORY</label>
                                            <div class="col-md-10">
                                                <select name="MC_ID" id="MC_ID" class="form-control select2" width="100%">
                                                    <option value="" hidden selected disabled>Choose..</option>
                                                    @foreach($CATEGORIES as $CATEGORY)
                                                    <option value="{{$CATEGORY->mc_id}}">{{$CATEGORY->mc_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">SUB CATEGORY</label>
                                            <div class="col-md-10">
                                                <select class="js-example-basic-single form-control" data-placeholder="Choose ..." style="width:100%" name="MSC_ID" id="MSC_ID">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">DESCRIPTION</label>
                                            <div class="col-md-10">
                                                <textarea name="DESCRIPTION" id="DESCRIPTION" class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-6">
                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">ADDITIONAL INFORMATION</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">

                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">EDITION</label>
                                            <div class="col-md-10">
                                                <input type="text" name="EDITION" id="EDITION" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">PUB. YEAR</label>
                                            <div class="col-md-10">
                                                <select name="PUBLICATION_YEAR" id="PUBLICATION_YEAR" class="form-select">
                                                    <option value="">Select Year</option>
                                                    <?php for ($y = date("Y"); $y >= 1900; $y--) : ?>
                                                        <option value="<?= $y ?>"><?= $y ?></option>
                                                    <?php endfor; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">PAGE COUNT</label>
                                            <div class="col-md-10">
                                                <input type="number" name="PAGE_COUNT" id="PAGE_COUNT" class="form-control">
                                            </div>
                                        </div>
                                        <div class="row mb-2">
                                            <label class="col-md-2 col-form-label">FORMAT</label>
                                            <div class="col-md-10">
                                                <select name="FORMAT" id="FORMAT" class="form-select">
                                                    <option value="" disabled hidden selected>Choose ...</option>
                                                    @foreach($BOOK_FORMATS as $FORMAT)
                                                    <option value="{{$FORMAT->mbf_id}}">{{$FORMAT->mbf_name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-header bg-transparent border-bottom">
                                        <div class="d-flex flex-wrap align-items-start">
                                            <div class="me-2">
                                                <h5 class="card-title mt-1 mb-0 text-primary">DOCUMENTS</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-12">
                                                <label for="">COVER PAGE</label>
                                                <input type="file" name="COVER_PAGE" id="COVER_PAGE" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="NEW_PRODUCT_CREATE_FORM_BTN" class="mb-3">
                                    <button class="btn btn-primary w-50" type="submit">SAVE</button>
                                </div>
                            </div>

                        </div>

                    </form>

                </div>
            </div>


            @include('Layout/footer')
        </div>
    </div>

    <script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize select2 on sub-category
            $('#MSC_ID').select2({
                placeholder: "Choose...",
                ajax: {
                    url: function() {
                        // Always use the current category ID
                        return '<?= url('/') ?>/get_sub_categories/' + $('#MC_ID').val();
                    },
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term // search term
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

            // When category changes, clear and reload sub-categories
            $('#MC_ID').on('change', function() {
                $('#MSC_ID').val(null).trigger('change'); // clear selected
            });
        });



        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#NEW_PRODUCT_CREATE_FORM').on('submit', function(e) {
                e.preventDefault();
                $('#NEW_PRODUCT_CREATE_FORM_BTN').html(
                    '<button class="btn btn-primary w-50" disabled>' +
                    '<i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>'
                );

                var formData = new FormData(this);

                $.ajax({
                    url: "{{url('/')}}/save_product",
                    type: "POST",
                    data: formData,
                    processData: false, // required for file uploads
                    contentType: false, // required for file uploads
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire('Success!', data.success, 'success')
                                .then(() => {
                                    window.location.assign("{{url('/')}}/Product_Profile/" + encodeURIComponent(btoa(data.p_id)));
                                });
                        }

                        if (data.error) {
                            Swal.fire('Error!', data.error, 'error');
                            $('#NEW_PRODUCT_CREATE_FORM_BTN').html('<button class="btn btn-primary w-50" type="submit">SAVE</button>');
                        }
                    },
                    error: function(error) {
                        Swal.fire('Error!', error, 'error');
                        $('#NEW_PRODUCT_CREATE_FORM_BTN').html('<button class="btn btn-primary w-50" type="submit">SAVE</button>');
                    }
                });
            });
        });
    </script>

</body>

</html>