<!-- Start Navbar -->
<nav id="topnav" class="defaultscroll is-sticky">
    <div class="container relative">
        <!-- Logo container-->
        <a class="logo" href="{{ url('/') }}">
            @include('site::includes.brand-mark')
        </a>
        <!-- End Logo container-->

        <!-- Start Mobile Toggle -->
        <div class="menu-extras">
            <div class="menu-item">
                <a class="navbar-toggle" id="isToggle" onclick="toggleMenu()">
                    <div class="lines">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </a>
            </div>
        </div>
        <!-- End Mobile Toggle -->

        <!--Login button Start-->
        <ul class="buy-button list-none mb-0">
            <li class="dropdown inline-block relative pe-1">
                <button data-dropdown-toggle="dropdown" class="dropdown-toggle align-middle inline-flex search-dropdown" type="button">
                    <i data-feather="search" class="size-5 dark-icon"></i>
                    <i data-feather="search" class="size-5 white-icon text-white"></i>
                </button>
                <!-- Dropdown menu -->
                <div class="dropdown-menu absolute overflow-hidden end-0 m-0 mt-5 z-10 md:w-52 w-48 rounded-md bg-white dark:bg-slate-900 shadow-sm dark:shadow-gray-800 hidden" onclick="event.stopPropagation();">
                    <div class="relative">
                        <i data-feather="search" class="size-4 absolute top-[9px] end-3"></i>
                        <input type="text" class="h-9 px-3 pe-10 w-full border-0 focus:ring-0 outline-none" name="s" id="searchItem" placeholder="{{ __('tenant.search_placeholder') }}">
                    </div>
                </div>
            </li>
    
            @include('site::includes.nav-account')
        </ul>
        <!--Login button End-->

        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu nav-right !justify-end nav-light">
                @if (tenant_website_account())
                    @include('site::includes.operator-menu')
                @else
                <li class="has-submenu parent-menu-item">
                    <a href="javascript:void(0)">Hero</a><span class="menu-arrow"></span>
                    <ul class="submenu">
                        <li><a href="{{ url('/') }}" class="sub-menu-item">Tour One</a></li>
                        <li><a href="{{ url('/index-two') }}" class="sub-menu-item">Tour Two</a></li>
                        <li><a href="{{ url('/index-three') }}" class="sub-menu-item">Tour Three</a></li>
                        <li><a href="{{ url('/index-four') }}" class="sub-menu-item">Tour Four</a></li>
                        <li><a href="{{ url('/index-five') }}" class="sub-menu-item">Tour Five</a></li>
                    </ul>
                </li>

                <li class="has-submenu parent-parent-menu-item"><a href="javascript:void(0)"> Listing </a><span class="menu-arrow"></span>
                    <ul class="submenu">
                        <li class="has-submenu parent-menu-item">
                            <a href="javascript:void(0)">Tour Grid </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/grid') }}" class="sub-menu-item">Grid</a></li>
                                <li><a href="{{ url('/grid-left-sidebar') }}" class="sub-menu-item">Grid Left Sidebar</a></li>
                                <li><a href="{{ url('/grid-right-sidebar') }}" class="sub-menu-item">Grid Right Sidebar</a></li>
                            </ul>  
                        </li>

                        <li class="has-submenu parent-menu-item">
                            <a href="javascript:void(0)">Tour List </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/list') }}" class="sub-menu-item">List</a></li>
                                <li><a href="{{ url('/list-left-sidebar') }}" class="sub-menu-item">List Left Sidebar</a></li>
                                <li><a href="{{ url('/list-right-sidebar') }}" class="sub-menu-item">List Right Sidebar</a></li>
                            </ul>  
                        </li>

                        <li class="has-submenu parent-menu-item">
                            <a href="javascript:void(0)"> Tour Detail </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/tour-detail-one') }}" class="sub-menu-item">Tour Detail One</a></li>
                                <li><a href="{{ url('/tour-detail-two') }}" class="sub-menu-item">Tour Detail Two</a></li>
                            </ul>  
                        </li>
                    </ul>
                </li>
        
                <li class="has-submenu parent-parent-menu-item">
                    <a href="javascript:void(0)">Pages</a><span class="menu-arrow"></span>
                    <ul class="submenu">
                        <li><a href="{{ url('/aboutus') }}" class="sub-menu-item">About Us</a></li>

                        <li class="has-submenu parent-menu-item"><a href="javascript:void(0)"> My Account</a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/user-profile') }}" class="sub-menu-item">User Account</a></li>
                                <li><a href="{{ url('/user-billing') }}" class="sub-menu-item">Billing</a></li>
                                <li><a href="{{ url('/user-payment') }}" class="sub-menu-item">Payment</a></li>
                                <li><a href="{{ url('/user-invoice') }}" class="sub-menu-item">Invoice</a></li>
                                <li><a href="{{ url('/user-social') }}" class="sub-menu-item">Social</a></li>
                                <li><a href="{{ url('/user-notification') }}" class="sub-menu-item">Notification</a></li>
                                <li><a href="{{ url('/user-setting') }}" class="sub-menu-item">Setting</a></li>
                            </ul> 
                        </li>
                
                        <li class="has-submenu parent-menu-item">
                            <a href="javascript:void(0)"> Helpcenter </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/helpcenter') }}" class="sub-menu-item">Overview</a></li>
                                <li><a href="{{ url('/helpcenter-faqs') }}" class="sub-menu-item">FAQs</a></li>
                                <li><a href="{{ url('/helpcenter-guides') }}" class="sub-menu-item">Guides</a></li>
                                <li><a href="{{ url('/helpcenter-support') }}" class="sub-menu-item">Support</a></li>
                            </ul>  
                        </li>

                        <li class="has-submenu parent-menu-item"><a href="javascript:void(0)"> Auth Pages </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/login') }}" class="sub-menu-item"> Login</a></li>
                                <li><a href="{{ url('/signup') }}" class="sub-menu-item"> Signup</a></li>
                                <li><a href="{{ url('/forgot-password') }}" class="sub-menu-item"> Forgot Password</a></li>
                                <li><a href="{{ url('/lock-screen') }}" class="sub-menu-item"> Lock Screen</a></li>
                            </ul> 
                        </li>

                        <li class="has-submenu parent-menu-item"><a href="javascript:void(0)"> Utility </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/terms') }}" class="sub-menu-item">Terms of Services</a></li>
                                <li><a href="{{ url('/privacy') }}" class="sub-menu-item">Privacy Policy</a></li>
                            </ul>  
                        </li>

                        <li class="has-submenu parent-menu-item"><a href="javascript:void(0)"> Special </a><span class="submenu-arrow"></span>
                            <ul class="submenu">
                                <li><a href="{{ url('/comingsoon') }}" class="sub-menu-item"> Coming Soon</a></li>
                                <li><a href="{{ url('/maintenance') }}" class="sub-menu-item"> Maintenance</a></li>
                                <li><a href="{{ url('/404') }}" class="sub-menu-item"> 404!</a></li>
                            </ul> 
                        </li>
                    </ul>
                </li>

                <li class="has-submenu parent-menu-item">
                    <a href="javascript:void(0)">Blog</a><span class="menu-arrow"></span>
                    <ul class="submenu">
                        <li><a href="{{ url('/blogs') }}" class="sub-menu-item"> Blogs</a></li>
                        <li><a href="{{ url('/blog-standard') }}" class="sub-menu-item"> Blog Standard</a></li>
                        <li><a href="{{ url('/blog-detail') }}" class="sub-menu-item"> Blog Detail</a></li>
                    </ul> 
                </li>

                <li><a href="{{ url('/contact') }}" class="sub-menu-item">Contact Us</a></li>
                @endif
            </ul><!--end navigation menu-->
        </div><!--end navigation-->
    </div><!--end container-->
</nav><!--end header-->
<!-- End Navbar -->