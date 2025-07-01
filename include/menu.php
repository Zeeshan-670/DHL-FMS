<div class="loader-container" id="loader-container">
    <div class="loader" id="loader"></div>
</div>
<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box d-flex align-items-center justify-content-center p-0">
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/img/dhllogo.png" alt="" width="70" />
                    </span>
                    <span class="logo-lg">
                        <img src="assets/img/logo.png" alt="" height="20" />
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect" id="vertical-menu-btn">
                <i class="fa fa-fw fa-bars"></i>
            </button>

            <!-- App Search-->
            <form class="app-search d-none d-lg-flex align-items-center mx-4 mb-0">
                <h3 class="mb-0" style="font-weight: bold;">DHL Fleet Management System</h3>
                <div class="position-relative" style="visibility: hidden;">
                    <input type="text" class="form-control" placeholder="Search..." />
                    <span class="bx bx-search-alt"></span>
                </div>
            </form>
        </div>

        <div class="d-flex">
            <div class="dropdown d-inline-block d d-lg-block ms-2">
                
                <!-- <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="mdi mdi-magnify"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-search-dropdown">
                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ..."
                                    aria-label="Recipient's username" />
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="mdi mdi-magnify"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div> -->
            </div>

            <!-- <div class="dropdown d-none d-lg-inline-block ms-1">
                <button type="button" class="bg-white header-item noti-icon waves-effect" data-bs-toggle="fullscreen">
                    <i class="bx bx-fullscreen"></i>
                </button>
            </div> -->

            <div class="dropdown d-inline-block">
                <!-- <button type="button" class="bg-white mx-1 header-item waves-effect"
                    id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false"> -->
                    <!-- <i class="bx bx-bell bx-tada"></i> -->
                    <!-- <span class="badge bg-danger rounded-pill">0</span>
                </button> -->
                <!-- <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                    aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-3">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="m-0" key="t-notifications">Alerts</h6>
                            </div>
                            <div class="col-auto">
                                <a href="#!" class="small" key="t-view-all"> View All</a>
                            </div>
                        </div>
                    </div>
                    <div data-simplebar style="max-height: 230px">
                        <a href="javascript: void(0);" class="text-reset notification-item">
                            <div class="d-flex">
                                <div class="avatar-xs me-3">
                                    <span class="avatar-title  rounded-circle font-size-16">
                                        <i class="bx bx-cart"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" key="t-your-order">
                                        Your order is placed
                                    </h6>
                                    <div class="font-size-12 text-muted">
                                        <p class="mb-1" key="t-grammer">
                                            If several languages coalesce the grammar
                                        </p>
                                        <p class="mb-0">
                                            <i class="mdi mdi-clock-outline"></i>
                                            <span key="t-min-ago">3 min ago</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>


                    </div>
                    <div class="p-2 border-top d-grid">
                        <a class="btn btn-sm btn-link font-size-14 text-center" href="javascript:void(0)">
                            <i class="mdi mdi-arrow-right-circle me-1"></i>
                            <span key="t-view-more">View More..</span>
                        </a>
                    </div>
                </div> -->
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="bg-white header-item waves-effect" id="page-header-user-dropdown"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user"
                        src="assets/img/user.png" alt="Header Avatar" />
                    <span class="d-none d-xl-inline-block ms-1" key="t-henry">Welcome, <?php echo ($_SESSION['name']); ?></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <!-- item-->
                    <!-- <a class="dropdown-item" href="#"><i class="bx bx-user font-size-16 align-middle me-1"></i>
                        <span key="t-profile">Profile</span></a> -->

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="./"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        <span key="t-logout">Logout</span></a>
                </div>
            </div>


        </div>
</header>