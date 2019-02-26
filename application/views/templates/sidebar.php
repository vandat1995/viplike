        <!-- Main Sidebar -->
        <aside class="main-sidebar col-12 col-md-3 col-lg-2 px-0">
          <div class="main-navbar">
            <nav class="navbar align-items-stretch navbar-light bg-white flex-md-nowrap border-bottom p-0">
              <a class="navbar-brand w-100 mr-0" href="dashboard" style="line-height: 25px;">
                <div class="d-table m-auto">
                  <img id="main-logo" class="d-inline-block align-top mr-1" style="max-width: 25px;" src="assets/images/shards-dashboards-logo.svg" alt="Vip like">
                  <span class="d-none d-md-inline ml-1">Vip Yasuo</span>
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
                  <span>Trang Chủ</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'viplikesetting') echo 'active' ?>" href="viplikesetting">
                  <i class="material-icons">thumb_up</i>
                  <span>Vip Like</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'bufflike') echo 'active' ?>" href="bufflike">
                  <i class="material-icons">thumb_up</i>
                  <span>Buff Like</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'botreactions') echo 'active' ?>" href="botreactions">
                  <i class="material-icons">insert_emoticon</i>
                  <span>Bot Cảm Xúc</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'vipcomment') echo 'active' ?>" href="vipcomment">
                  <i class="material-icons">comment</i>
                  <span>Vip Comment</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'price') echo 'active' ?>" href="price">
                  <i class="material-icons">attach_money</i>
                  <span>Bảng Giá</span>
                </a>
              </li>

              <?php if( $this->session->userdata("role_id") == 1 ) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'user') echo 'active' ?>" href="user">
                  <i class="material-icons">people</i>
                  <span>User</span>
                </a>
              </li>
              <?php } ?>

              <?php if( $this->session->userdata("role_id") == 0 ) { ?>
              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'friendmanagement') echo 'active' ?>" href="friendmanagement">
                  <i class="material-icons">person_add</i>
                  <span>Quản lý bạn bè</span>
                </a>
              </li>
              <?php } ?>

              <li class="nav-item">
                <a class="nav-link <?php if(strtolower($this->uri->segment(1)) == 'history') echo 'active' ?>" href="history">
                  <i class="material-icons">history</i>
                  <span>Lịch sử</span>
                </a>
              </li>

            </ul>
          </div>
        </aside>
        <!-- End Main Sidebar -->