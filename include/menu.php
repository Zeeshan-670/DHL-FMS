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
            </div>


            <div class="dropdown d-inline-block">
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

                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="./"><i
                            class="bx bx-power-off font-size-16 align-middle me-1 text-danger"></i>
                        <span key="t-logout">Logout</span></a>
                </div>
            </div>


        </div>
</header>