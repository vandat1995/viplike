        <!-- Main Sidebar -->
        <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
          <div class="main-navbar">
            <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
              <a class="navbar-brand w-100 mr-0" href="dashboard" style="line-height: 25px;">
                <div class="d-table m-auto">
                  <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 25px;" src="assets/images/shards-dashboards-logo.svg" alt="Vip like">
                  <span class="d-none d-md-inline ml-1">VIP LIKE</span>
                </div>
              </a>
              <a class="toggle-sidebar d-sm-inline d-md-none d-lg-none">
                <i class="material-icons">&#xE5C4;</i>
              </a>
            </nav>
          </div>
          <form action="#" class="main-sidebar__search w-100 border-right d-sm-flex d-md-none d-lg-none">
            <div class="input-group input-group-seamless ml-3">
              <div class="input-group-prepend">
                <div class="input-group-text">
                  <i class="fas fa-search"></i>
                </div>
              </div>
              <input class="navbar-search form-control" type="text" placeholder="Search for something..." aria-label="Search"> </div>
          </form>
          <div class="nav-wrapper">
            <ul class="nav flex-column">
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'dashboard') echo 'active' ?>" href="dashboard">
                  <i class="material-icons">dashboard</i>
                  <span>Dashboard</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'viplikesetting') echo 'active' ?>" href="viplikesetting">
                  <i class="material-icons">thumb_up</i>
                  <span>VIP LIKE</span>
                </a>
              </li>

              <?php if( $this->session->userdata("role_id") == 1 ) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'user') echo 'active' ?>" href="user">
                  <i class="material-icons">people</i>
                  <span>USER</span>
                </a>
              </li>
              <?php } ?>

            </ul>
          </div>
        </aside>
        <!-- End Main Sidebar -->