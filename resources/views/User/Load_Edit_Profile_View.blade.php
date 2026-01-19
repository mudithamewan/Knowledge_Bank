  <link href="{{url('/')}}/assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
  <link href="{{url('/')}}/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="{{url('/')}}/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="{{url('/')}}/assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <form id="EDIT_USER_FORM">
      @csrf
      <input type="hidden" name="USER_ID" id="USER_ID" value="{{$USER_DETAILS->su_id}}">

      <div class="row">
          <div class="col-lg-8 mt-2">
              <label for="">NAME <span class="text-danger">*</span></label>
              <input type="text" name="NAME" id="NAME" value="{{$USER_DETAILS->su_name}}" class="form-control" required>
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">NIC</label>
              <input type="text" name="NIC" id="NIC" value="{{$USER_DETAILS->su_nic}}" class="form-control">
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">GENDER <span class="text-danger">*</span></label> <br>
              <input type="radio" name="GENDER" id="MALE" value="M" <?= $USER_DETAILS->su_gender == 'Male' ? 'checked' : '' ?>> <label for="MALE">MALE</label> &nbsp;&nbsp;&nbsp;
              <input type="radio" name="GENDER" id="FEMALE" value="F" <?= $USER_DETAILS->su_gender == 'Female' ? 'checked' : '' ?>> <label for="FEMALE">FEMALE</label>
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">CONTACT NUMBER <span class="text-danger">*</span></label>
              <input type="text" name="CONTACT_NUMBER" id="CONTACT_NUMBER" value="{{$USER_DETAILS->su_contact_number}}" class="form-control" required>
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">EMAIL <span class="text-danger">*</span></label>
              <input type="text" name="EMAIL" id="EMAIL" class="form-control" disabled value="{{$USER_DETAILS->su_email}}" required>
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">ADDRESS LINE 01 <span class="text-danger">*</span></label>
              <input type="text" name="ADDRESS_LINE_01" id="ADDRESS_LINE_01" value="{{$USER_DETAILS->su_address_line_01}}" class="form-control" required>
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">ADDRESS LINE 02</label>
              <input type="text" name="ADDRESS_LINE_02" id="ADDRESS_LINE_02" value="{{$USER_DETAILS->su_address_line_02}}" class="form-control">
          </div>
          <div class="col-lg-4 mt-2">
              <label for="">ADDRESS LINE 03</label>
              <input type="text" name="ADDRESS_LINE_03" id="ADDRESS_LINE_03" value="{{$USER_DETAILS->su_address_line_03}}" class="form-control">
          </div>

          <div class="col-lg-4 mt-2">
              <label for="">USER ROLE <span class="text-danger">*</span></label>
              <select name="ROLE_ID" id="ROLE_ID" class="form-select" required>
                  @foreach($USER_ROLES as $role)
                  @if($role->sr_id == $USER_DETAILS->sr_id)
                  <option value="{{$role->sr_id}}" selected>{{$role->sr_name}}</option>
                  @else
                  <option value="{{$role->sr_id}}">{{$role->sr_name}}</option>
                  @endif
                  @endforeach
              </select>
          </div>

          <div class="col-lg-4 mt-2">
              <label for="">STATUS <span class="text-danger">*</span></label>
              <select name="ACTIVE_STATUS" id="ACTIVE_STATUS" class="form-select" required>
                  @if($USER_DETAILS->su_is_active == 1)
                  <option value="ACTIVE" selected>ACTIVE</option>
                  <option value="INACTIVE">INACTIVE</option>
                  @else
                  <option value="ACTIVE">ACTIVE</option>
                  <option value="INACTIVE" selected>INACTIVE</option>
                  @endif
              </select>
          </div>

          <div class="col-xl-12 mt-2">
              <label for="">ACCESS WAREHOUSES <span class="text-danger">*</span></label>
              <div id="selectMenus">
                  <select class="select2 form-select select2-multiple mt-2" multiple="multiple" id="WAREHOUSES" data-placeholder="Choose ..." style="width:100%" name="WAREHOUSES[]">
                      @foreach($WAREHOUSES as $DATA)
                      @if (in_array($DATA->mw_id, $USER_WAREHOUSES_IDS))
                      <option value="{{ $DATA->mw_id }}" selected>{{ $DATA->mw_name }}</option>
                      @else
                      <option value="{{ $DATA->mw_id }}">{{ $DATA->mw_name }}</option>
                      @endif
                      @endforeach
                  </select>
              </div>
          </div>

          <div class="col-lg-4 mt-3">
              <div id="EDIT_USER_FORM_BTN">
                  <button class="btn btn-primary w-100" type="submit">UPDATE</button>
              </div>
          </div>
      </div>

  </form>

  <script>
      $(document).ready(function() {
          $("#WAREHOUSES").select2({
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

          $('#EDIT_USER_FORM').on('submit', function(e) {
              e.preventDefault();
              $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" disabled><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> VERIFYING..</button>');

              e.preventDefault();
              var formData = $(this).serialize();

              $.ajax({
                  url: "{{url('/')}}/update_user",
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
                          $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                      }
                  },
                  error: function(error) {
                      Swal.fire(
                          'Error!',
                          error,
                          'error'
                      );
                      $('#EDIT_USER_FORM_BTN').html('<button class="btn btn-primary w-100" type="submit">UPDATE</button>');
                  }
              });
          });
      });
  </script>