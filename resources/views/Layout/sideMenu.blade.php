@php
$role = session('USER_ROLE');
$accessArea = session('USER_ACCESS_AREA');
$ACCESS_AREA_HEADINGS = session('USER_ACCESS_HEADERS');
@endphp

<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">

                <!-- General Options -->
                @if(in_array("1", $accessArea))
                <li>
                    <a href="{{url('/')}}/Dashboard" class="waves-effect">
                        <i class="bx bx-home"></i><span key="t-dashboards">Dashboards</span>
                    </a>
                </li>
                @endif
                @if(in_array("19", $accessArea))
                <li>
                    <a href="{{url('/')}}/POS" class="waves-effect">
                        <i class="mdi mdi-cash-register"></i><span key="t-dashboards">POS</span>
                    </a>
                </li>
                @endif



                @if(in_array("STOCK", $ACCESS_AREA_HEADINGS))
                <li class="menu-title" key="t-apps">STOCK MANAGEMENT</li>
                @endif
                @if(in_array("21", $accessArea))
                <li>
                    <a href="{{url('/')}}/Stock" class="waves-effect">
                        <i class="bx bx-archive"></i><span key="t-dashboards">Stock</span>
                    </a>
                </li>
                @endif
                @if(in_array("15", $accessArea) || in_array("18", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-archive-in"></i>
                        <span key="t-utility">Stock In</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("15", $accessArea))
                        <li><a href="{{url('/')}}/Stock_In">Stock In</a></li>
                        @endif
                        @if(in_array("18", $accessArea))
                        <li><a href="{{url('/')}}/Stock_In_History">Stock In History</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(in_array("24", $accessArea) || in_array("36", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-receipt"></i>
                        <span key="t-utility">Invoices</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("24", $accessArea))
                        <li><a href="{{url('/')}}/Invoices">Invoices</a></li>
                        @endif
                        @if(in_array("37", $accessArea))
                        <li><a href="{{url('/')}}/Returned_Invoices">Returned Invoices</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(in_array("35", $accessArea) || in_array("36", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-stopwatch"></i>
                        <span key="t-utility">Punchings</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("35", $accessArea))
                        <li><a href="{{url('/')}}/My_Punch_List">My Punch List</a></li>
                        @endif
                        @if(in_array("36", $accessArea))
                        <li><a href="{{url('/')}}/ALL_Punch_List">All Punch List</a></li>
                        @endif
                    </ul>
                </li>
                @endif


                @if(in_array("ORDER", $ACCESS_AREA_HEADINGS))
                <li class="menu-title" key="t-apps">ORDER MANAGEMENT</li>
                @endif
                @if(in_array("22", $accessArea))
                <li>
                    <a href="{{url('/')}}/Add_New_Order" class="waves-effect">
                        <i class="bx bx-plus-circle"></i><span key="t-dashboards">Add New Order</span>
                    </a>
                </li>
                @endif
                @if(in_array("25", $accessArea))
                <li>
                    <a href="{{url('/')}}/Manage_Order" class="waves-effect">
                        <i class="bx bxs-shopping-bag"></i><span key="t-dashboards">Manage Order</span>
                    </a>
                </li>
                @endif
                @if(in_array("29", $accessArea) || in_array("30", $accessArea))

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-check-circle"></i>
                        <span key="t-utility">Approvals</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("29", $accessArea))
                        <li><a href="{{url('/')}}/Pending_Order_Approvals">Pending Approvals</a></li>
                        @endif
                        @if(in_array("30", $accessArea))
                        <li><a href="{{url('/')}}/Retruned_Order_Approvals">Returned Approvals</a></li>
                        @endif
                        <li><a href="{{url('/')}}/Completed_Order_Approvals">Completed Approvals</a></li>
                    </ul>
                </li>


                @endif



                @if(in_array("PRODUCT", $ACCESS_AREA_HEADINGS))
                <li class="menu-title" key="t-apps">PRODUCT MANAGEMENT</li>
                @endif
                @if(in_array("6", $accessArea) || in_array("8", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-book-open"></i>
                        <span key="t-utility">Products</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("6", $accessArea))
                        <li><a href="{{url('/')}}/Add_New_Product">Add New Product</a></li>
                        @endif
                        @if(in_array("8", $accessArea))
                        <li><a href="{{url('/')}}/Manage_Products">Manage Products</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(in_array("9", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-purchase-tag"></i>
                        <span key="t-utility">Categories</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li><a href="{{url('/')}}/Category_Medium">Medium</a></li>
                        <li><a href="{{url('/')}}/Category_Grades">Grades</a></li>
                        <li><a href="{{url('/')}}/Category_Subjects">Subjects</a></li>
                        <li><a href="{{url('/')}}/Category">Category</a></li>
                        <li><a href="{{url('/')}}/Category_Sub_Category">Sub Category</a></li>
                    </ul>
                </li>
                @endif

                @if(in_array("USERS", $ACCESS_AREA_HEADINGS))
                <li class="menu-title" key="t-apps">USER MANAGEMENT</li>
                @endif
                @if(in_array("13", $accessArea) || in_array("2", $accessArea) )
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-group"></i>
                        <span key="t-utility">User Manager</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("13", $accessArea))
                        <li><a href="{{url('/')}}/Create_New_User">Create New User</a></li>
                        @endif
                        @if(in_array("2", $accessArea))
                        <li><a href="{{url('/')}}/Manage_Users">Manage Users</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(in_array("10", $accessArea) || in_array("12", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bxs-group"></i>
                        <span key="t-utility">Org. Manager</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("10", $accessArea))
                        <li><a href="{{url('/')}}/Create_New_Organization">Create New Organization</a></li>
                        @endif
                        @if(in_array("12", $accessArea))
                        <li><a href="{{url('/')}}/Manage_Organizations">Manage Organizations</a></li>
                        @endif
                    </ul>
                </li>
                @endif
                @if(in_array("33", $accessArea) || in_array("34", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-user-check"></i>
                        <span key="t-utility">Customer Manager</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        @if(in_array("33", $accessArea))
                        <li><a href="{{url('/')}}/Create_New_Customer">Create New Customer</a></li>
                        @endif
                        @if(in_array("34", $accessArea))
                        <li><a href="{{url('/')}}/Manage_Customer">Manage Customers</a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if(in_array("SETTINGS", $ACCESS_AREA_HEADINGS))
                <li class="menu-title" key="t-apps">SETTINGS</li>
                @endif
                @if(in_array("5", $accessArea))
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect" aria-expanded="false">
                        <i class="bx bx-key"></i>
                        <span key="t-utility">Role Management</span>
                    </a>
                    <ul class="sub-menu mm-collapse" aria-expanded="false">
                        <li><a href="{{url('/')}}/Create_New_Role">Create New Role</a></li>
                        <li><a href="{{url('/')}}/Manage_Roles">Manage Roles</a></li>
                    </ul>
                </li>
                @endif
                @if(in_array("17", $accessArea))
                <li>
                    <a href="{{url('/')}}/Manage_Stock_Locations" class="waves-effect">
                        <i class="bx bx-package"></i><span key="t-dashboards">Manage Stock Locations</span>
                    </a>
                </li>
                @endif



            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>