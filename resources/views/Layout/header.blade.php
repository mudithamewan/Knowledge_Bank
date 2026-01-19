<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->



            <div class="navbar-brand-box">
                <a href="{{url('/')}}/Dashboard" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{url('/')}}/assets/images/logo.png" alt="" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="{{url('/')}}/assets/images/logo-dark.png" alt="" height="17">
                    </span>
                </a>
                <a href="{{url('/')}}/Dashboard" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{url('/')}}/assets/images/logo-light.png" alt="" height="25">
                    </span>
                    <span class="logo-lg">
                        <img src="{{url('/')}}/assets/images/logo-light.png" alt="" height="30">
                    </span>
                </a>
            </div>
            <button type="button" class="hdhd btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>
        </div>

        <div class="d-flex">
            @if(in_array("19",session('USER_ACCESS_AREA')))
            <div class="dropdown d-none d-lg-inline-block ms-1">
                <a href="{{url('/')}}/POS">
                    <button type="button" class="btn btn-secondary  header-item noti-icon waves-effect">
                        <i class="mdi mdi-cash-register text-white font-size-24 ms-2 me-2"></i>
                    </button>
                </a>
            </div>


            @endif

            <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="<?= session('IMAGE_PATH') ?>" alt="Header Avatar">
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry"><?= session('USER_NAME') ?></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    @if(in_array("14", session('USER_ACCESS_AREA')))
                    <a class="dropdown-item" href="{{url('/')}}/User_Profile/{{urlencode(base64_encode(session('USER_ID')))}}"><i class="bx bx-user font-size-16 align-middle me-1"></i> <span key="t-profile">Profile</span></a>
                    @endif
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="{{url('/')}}/logout"><i class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i> <span key="t-logout">Logout</span></a>
                </div>
            </div>


        </div>




    </div>
</header>