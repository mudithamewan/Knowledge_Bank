<!doctype html>
<html lang="en">
@php use Illuminate\Support\Facades\Session; @endphp

<head>

    <meta charset="utf-8" />
    <title>IntraKB</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{url('/')}}/assets/images/favicon.png">

    <!-- owl.carousel css -->
    <link rel="stylesheet" href="{{url('/')}}/assets/libs/owl.carousel/assets/owl.carousel.min.css">

    <link rel="stylesheet" href="{{url('/')}}/assets/libs/owl.carousel/assets/owl.theme.default.min.css">

    <!-- Bootstrap Css -->
    <link href="{{url('/')}}/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{url('/')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{url('/')}}/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

</head>

<body class="auth-body-bg">

    <div>
        <div class="container-fluid p-0">
            <div class="row g-0">

                <div class="col-xl-9 d-none d-lg-block d-flex justify-content-center">
                    <div class="auth-full-bg pt-lg-5 p-4">
                        <div class="w-100">
                            <div class="bg-overlay"></div>
                            <div class="d-flex h-100 flex-column">

                                <div class="p-4 mt-auto">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-7">
                                            <div class="text-center">


                                                <div dir="ltr">
                                                    <div class="owl-carousel owl-theme auth-review-carousel" id="auth-review-carousel">

                                                        <div class="item">
                                                            <h4 class="mb-3">Our Vision</h4>
                                                            <div class="py-3">
                                                                <p class="font-size-16 mb-4">We will be the one stop platform of education with ease of access in empowering the future generations with information, knowledge and educational resources with affordability and innovation.</p>
                                                            </div>
                                                        </div>

                                                        <div class="item">
                                                            <h4 class="mb-3">Our Mission</h4>
                                                            <div class="py-3">
                                                                <p class="font-size-16 mb-4">

                                                                    - Be the trusted provider of model papers and answers on all exams in Sri Lanka <br>
                                                                    - Be the best online higher education service provider <br>
                                                                    - Be the valued portal of guiding Sri Lankan students for a pathway until their career <br>
                                                                    - Become the central hub of information related to education, career and knowledge related products and events.

                                                                </p>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end col -->

                <div class="col-xl-3">
                    <div class="auth-full-page-content p-md-5 p-4">
                        <div class="w-100">
                            <div class="d-flex flex-column h-100">

                                <div class="my-auto">
                                    <div class="mb-5">
                                        <center>
                                            <a href="{{url('/')}}">
                                                <img src="assets/images/logo-dark.png" alt="" height="70" class="auth-logo-dark">
                                            </a>
                                        </center>
                                    </div>

                                    @if (Session::has('success'))
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        {{Session::get("success")}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif
                                    @if (Session::has("error"))
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        {{Session::get("error")}}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                    @endif



                                    <div>
                                        <h5 class="text-dark">Welcome Back !</h5>
                                        <p class="text-muted">Sign in to continue to Process.</p>
                                    </div>

                                    <div class="mt-4">
                                        <form id="LOGIN_FORM" method="post" action="{{url('/')}}/authentication">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Email</label>
                                                <input type="text" class="form-control" id="EMAIL" name="EMAIL" placeholder="Enter username">
                                            </div>
                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <a href="auth-recoverpw-2.html" class="text-muted">Forgot password?</a>
                                                </div>
                                                <label class="form-label">Password</label>
                                                <div class="input-group auth-pass-inputgroup">
                                                    <input type="password" class="form-control" id="PASSWORD" name="PASSWORD" placeholder="Enter password" aria-label="Password" aria-describedby="password-addon">
                                                    <button class="btn btn-light " type="button" id="password-addon"><i class="mdi mdi-eye-outline"></i></button>
                                                </div>
                                            </div>
                                            <div class="mt-3 d-grid">
                                                <div id="LOGIN_FORM_SUBMIT">
                                                    <button class="btn btn-dark waves-effect waves-light w-100" type="submit">Log In</button>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                </div>

                                <div class="mt-4 mt-md-5 text-center">
                                    <p>© <script>
                                            document.write(new Date().getFullYear())
                                        </script> Knowledge Bank Publisher. Design & Develop by MOne</p>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container-fluid -->
    </div>

    <!-- JAVASCRIPT -->
    <script src="{{url('/')}}/assets/js/sweetAlert.js"></script>
    <script src="{{url('/')}}/assets/libs/jquery/jquery.min.js"></script>
    <script src="{{url('/')}}/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{url('/')}}/assets/libs/metismenu/metisMenu.min.js"></script>
    <script src="{{url('/')}}/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="{{url('/')}}/assets/libs/node-waves/waves.min.js"></script>
    <script src="{{url('/')}}/assets/libs/owl.carousel/owl.carousel.min.js"></script>
    <script src="{{url('/')}}/assets/js/pages/auth-2-carousel.init.js"></script>
    <script src="{{url('/')}}/assets/js/app.js"></script>



</body>

</html>