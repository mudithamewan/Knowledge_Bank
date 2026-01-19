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

   <script>
       function display_vat_form() {
           let isVatYes = $('#VAT_YES').is(':checked');

           if (isVatYes) {
               $('#vat_info_form').show();
           } else {
               $('#vat_info_form').hide();
           }
       }
       display_vat_form();
   </script>
   <link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
   <link href="{{url('/')}}/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <link href="{{url('/')}}/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
   <link href="{{url('/')}}/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

   <form id="NEW_EDIT_ORGANIZATION_FORM" enctype="multipart/form-data">
       @csrf
       <input type="hidden" name="ORG_ID" id="ORG_ID" value="{{$ORGANIZATION_DETAILS->o_id}}">

       <div class="row">
           <div class="col-lg-12">
               <div class="section-title-wrapper">
                   <div class="section-line"></div>
                   <div class="section-title">BASIC INFORMATION</div>
                   <div class="section-line"></div>
               </div>
           </div>
           <div class="col-lg-6 mt-2">
               <label for="">NAME <span class="text-danger">*</span></label>
               <input type="text" name="NAME" id="NAME" class="form-control" value="{{$ORGANIZATION_DETAILS->o_name}}">
           </div>
           <div class="col-lg-6 mt-2">
               <label for="">BUSINESS NAME <span class="text-danger">*</span></label>
               <input type="text" name="BUSINESS_NAME" id="BUSINESS_NAME" class="form-control" value="{{$ORGANIZATION_DETAILS->o_business_name}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">CONTACT <span class="text-danger">*</span></label>
               <input type="text" name="CONTACT" id="CONTACT" class="form-control" value="{{$ORGANIZATION_DETAILS->o_contact}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">EMAIL</label>
               <input type="email" name="EMAIL" id="EMAIL" class="form-control" value="{{$ORGANIZATION_DETAILS->o_email}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">ADDRESS <span class="text-danger">*</span></label>
               <input type="text" name="ADDRESS" id="ADDRESS" class="form-control" value="{{$ORGANIZATION_DETAILS->o_address}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">STATUS <span class="text-danger">*</span></label>
               <select name="ACTIVE_STATUS" id="ACTIVE_STATUS" class="form-select" required>
                   @if($ORGANIZATION_DETAILS->o_is_active == 1)
                   <option value="ACTIVE" selected>ACTIVE</option>
                   <option value="INACTIVE">INACTIVE</option>
                   @else
                   <option value="ACTIVE">ACTIVE</option>
                   <option value="INACTIVE" selected>INACTIVE</option>
                   @endif
               </select>
           </div>



           <div class="col-lg-12">
               <div class="section-title-wrapper mt-5">
                   <div class="section-line"></div>
                   <div class="section-title">BUSINESS INFORMATION</div>
                   <div class="section-line"></div>
               </div>
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">BR NUMBER</label>
               <input type="text" name="BR_NUMBER" id="BR_NUMBER" class="form-control" value="{{$ORGANIZATION_DETAILS->o_br_number}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">BUSINESS TYPE <span class="text-danger">*</span></label>
               <select name="BUSINESS_TYPE" id="BUSINESS_TYPE" class="form-select">
                   @foreach($BUSINESS_TYPE as $TYPE)
                   @if($TYPE->mbt_id == $ORGANIZATION_DETAILS->o_mbt_id)
                   <option value="{{$TYPE->mbt_id}}" selected>{{$TYPE->mbt_name}}</option>
                   @else
                   <option value="{{$TYPE->mbt_id}}">{{$TYPE->mbt_name}}</option>
                   @endif
                   @endforeach
               </select>
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">VAT REGISTERED? </label><br>
               <div class="mt-2">
                   <input type="radio" name="VAT" id="VAT_YES" value="YES" <?= $ORGANIZATION_DETAILS->o_is_vat_registered == 1 ? 'checked' : '' ?> onchange="display_vat_form()">
                   <label class="ms-1" for="VAT_YES">YES</label> &nbsp;&nbsp;&nbsp;&nbsp;
                   <input type="radio" name="VAT" id="VAT_NO" value="NO" <?= $ORGANIZATION_DETAILS->o_is_vat_registered == 0 ? 'checked' : '' ?> onchange="display_vat_form()">
                   <label class="ms-1" for="VAT_NO">NO</label>
               </div>
           </div>

           <div class="col-xl-8">
               <div class="row" id="vat_info_form" style="display: none;">
                   <div class="col-lg-6 mt-2">
                       <label for="">VAT REG. NO. <span class="text-danger">*</span></label>
                       <input type="text" name="VAT_REG_NO" id="VAT_REG_NO" class="form-control" value="{{$ORGANIZATION_DETAILS->o_vat_registered_number}}">
                   </div>
                   <div class="col-lg-6 mt-2">
                       <label for="">DATE OF VAT REG.</label>
                       <input type="date" name="VAT_REG_DATE" id="VAT_REG_DATE" class="form-control" value="{{$ORGANIZATION_DETAILS->o_vat_registered_date}}">
                   </div>
               </div>
           </div>

           <div class="col-lg-12">
               <div class="section-title-wrapper mt-5">
                   <div class="section-line"></div>
                   <div class="section-title">BANKING INFORMATION</div>
                   <div class="section-line"></div>
               </div>
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">BANK CODE</label>
               <input type="text" name="BANK_CODE" id="BANK_CODE" class="form-control" value="{{$ORGANIZATION_DETAILS->o_bank_code}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">BRANCH CODE</label>
               <input type="text" name="BRANCH_CODE" id="BRANCH_CODE" class="form-control" value="{{$ORGANIZATION_DETAILS->o_bank_branch_code}}">
           </div>
           <div class="col-lg-4 mt-2">
               <label for="">ACCOUNT NUMBER</label>
               <input type="text" name="ACCOUNT_NUMBER" id="ACCOUNT_NUMBER" class="form-control" value="{{$ORGANIZATION_DETAILS->o_account_number}}">
           </div>

           <div class="col-lg-12">
               <div class="section-title-wrapper mt-5">
                   <div class="section-line"></div>
                   <div class="section-title">TAGGING INFORMATION</div>
                   <div class="section-line"></div>
               </div>
           </div>
           <div class="col-xl-12 mt-2">
               <label for="">Tag <span class="text-danger">*</span></label>
               <div id="selectMenus">
                   <select class="select2 form-select select2-multiple mt-2" multiple="multiple" id="TYPES" data-placeholder="Choose ..." style="width:100%" name="TYPES[]">
                       @foreach($ORGANIZATION_TYPE as $DATA)
                       @if (in_array($DATA->mot_id, $ORGANIZATION_TYPE_IDS))
                       <option value="{{ $DATA->mot_id }}" selected>{{ $DATA->mot_name }}</option>
                       @else
                       <option value="{{ $DATA->mot_id }}">{{ $DATA->mot_name }}</option>
                       @endif
                       @endforeach
                   </select>
               </div>
           </div>

           <div class="col-lg-4 mt-4">
               <div id="NEW_EDIT_ORGANIZATION_FORM_BTN">
                   <button class="btn btn-primary w-100" type="submit">UPDATE</button>
               </div>
           </div>
       </div>
   </form>

   <script>
       $(document).ready(function() {
           $("#TYPES").select2({
               dropdownParent: $("#selectMenus")
           });
       });
   </script>
   <script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
   <script src="{{url('/')}}/assets/libs/select2/js/select2.min.js"></script>
   <script src="{{url('/')}}/assets/js/pages/form-advanced.init.js"></script>
   <script>
       $(document).ready(function() {
           $.ajaxSetup({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               }
           });

           $('#NEW_EDIT_ORGANIZATION_FORM').on('submit', function(e) {
               e.preventDefault();
               $('#NEW_EDIT_ORGANIZATION_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

               e.preventDefault();
               var formData = $(this).serialize();

               $.ajax({
                   url: "{{url('/')}}/update_organization",
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
                           $('#NEW_EDIT_ORGANIZATION_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                       }
                   },
                   error: function(error) {
                       Swal.fire(
                           'Error!',
                           error,
                           'error'
                       );
                       $('#NEW_EDIT_ORGANIZATION_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                   }
               });
           });
       });
   </script>