        
        <main class="main-content col-lg-10 col-md-9 col-sm-12 p-0 offset-lg-2 offset-md-3">
          <div class="main-navbar sticky-top bg-white">
            <!-- Main Navbar -->
            <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0">
              <form action="#" class="main-navbar__search w-100 d-none d-md-flex d-lg-flex">
                <div class="input-group input-group-seamless ml-3">
                  <div class="input-group-prepend">
                    <div class="input-group-text">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                  <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search"> </div>
              </form>
              <ul class="navbar-nav border-left flex-row">
                <li class="nav-item border-right dropdown notifications">
                  <a class="nav-link nav-link-icon text-center" href="javascript:void(0)" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="nav-link-icon__wrapper">
                      <i class="material-icons">&#xE7F4;</i>
                      <span class="badge badge-pill badge-danger">1</span>
                    </div>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item" href="javascript:void(0)">
                      <div class="notification__icon-wrapper">
                        <div class="notification__icon">
                          <i class="material-icons">&#xE6E1;</i>
                        </div>
                      </div>
                      <div class="notification__content">
                        <span class="notification__category">Noti</span>
                        <p>Chào mừng bạn đến với hệ thống vip like của Huỳnh Tài
                        </p>
                      </div>
                    </a>
                    
                    <a class="dropdown-item notification__all text-center" href="javascript:void(0)"> View all Notifications </a>
                  </div>
                </li>
                <li class="nav-item dropdown">
                  <a class="nav-link dropdown-toggle text-nowrap px-3" data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true" aria-expanded="false">
                    <img class="user-avatar rounded-circle mr-2" src="<?= !empty($this->session->userdata('avatar')) ? $this->session->userdata('avatar') : 'https://i.imgur.com/cMznN9G.png' ?>" alt="User Avatar">
                    <span class="d-none d-md-inline-block"><?= $this->session->userdata('full_name') ?></span>
                  </a>
                  <div class="dropdown-menu dropdown-menu-small">
                    <a class="dropdown-item" href="javascript:void(0)">
                      <i class="material-icons">attach_money</i> <label class="badge badge-success"><?= $this->session->userdata("balance") ?> VND</label></a>
                    <a class="dropdown-item" href="user">
                      <i class="material-icons">&#xE7FD;</i> Profile</a>
                    <?php if($this->session->userdata('role_id') == 1) { ?>
                    <a class="dropdown-item" href="Token">
                      <i class="material-icons">note_add</i> Add Tokens Vip</a>
                    <?php } ?>
                    <?php if($this->session->userdata('role_id') == 1) { ?>
                    <a class="dropdown-item" href="Token/buff">
                      <i class="material-icons">note_add</i> Add Tokens Buff</a>
                    <?php } ?>
                    <?php if($this->session->userdata('role_id') == 1) { ?>
                    <a class="dropdown-item" href="setting">
                      <i class="material-icons">settings</i> Setting</a>
                    <?php } ?>
                    
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="logout">
                      <i class="material-icons text-danger">&#xE879;</i> Logout </a>
                  </div>
                </li>
              </ul>
              <nav class="nav">
                <a href="javascript:void(0)" class="nav-link nav-link-icon toggle-sidebar d-md-inline d-lg-none text-center border-left" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                  <i class="material-icons">&#xE5D2;</i>
                </a>
              </nav>
            </nav>
          </div>
          <!-- / .main-navbar -->
          <div class="main-content-container container-fluid px-4">
            <!-- Page Header -->
            <div class="page-header row no-gutters py-4">
              <div class="col-12 col-sm-4 text-center text-sm-left mb-0">
                <span class="text-uppercase page-subtitle"><?= $sub_title ?></span>
                <h3 class="page-title"><?= $page_title ?></h3>
              </div>
            </div>
            <!-- End Page Header -->
