<script src="assets/scripts/app/app-blog-overview.1.1.0.min.js"></script>
            
            <!-- Small Stats Blocks -->
            <div class="row">
              
              <div class="col-lg col-md-6 col-sm-6 mb-4">
                <div class="stats-small stats-small--1 card card-small">
                  <div class="card-body p-0 d-flex">
                    <div class="d-flex flex-column m-auto">
                      <div class="stats-small__data text-center">
                        <span class="stats-small__label text-uppercase">Tổng VIP UID</span>
                        <h6 class="stats-small__value count my-3"><?= $total_vip ?></h6>
                      </div>
                      <div class="stats-small__data">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-lg col-md-4 col-sm-6 mb-4">
                <div class="stats-small stats-small--1 card card-small">
                  <div class="card-body p-0 d-flex">
                    <div class="d-flex flex-column m-auto">
                      <div class="stats-small__data text-center">
                        <span class="stats-small__label text-uppercase">Tổng bài viết</span>
                        <h6 class="stats-small__value count my-3"><?= $total_process ?></h6>
                      </div>
                      <div class="stats-small__data">
                       
                      </div>
                    </div>
                    
                  </div>
                </div>
              </div>
              
              <div class="col-lg col-md-4 col-sm-12 mb-4">
                <div class="stats-small stats-small--1 card card-small">
                  <div class="card-body p-0 d-flex">
                    <div class="d-flex flex-column m-auto">
                      <div class="stats-small__data text-center">
                        <span class="stats-small__label text-uppercase">Tổng Số lượt đã like</span>
                        <h6 class="stats-small__value count my-3"><?= $total_like_process ?></h6>
                      </div>
                      <div class="stats-small__data">
                        
                      </div>
                    </div>
                    <canvas height="120" class="blog-overview-stats-small-5"></canvas>
                  </div>
                </div>
              </div>
            </div>
            <!-- End Small Stats Blocks -->
            <div class="row">
              <!-- Users Stats -->
              <div class="col-lg-12">
                <div class="card card-small card-post mb-4">
                  <div class="card-body">
                    <h5 class="card-title">Chào Mừng Các Bạn Đến Với Hệ Thống Like Của Huỳnh Tài.</h5>
                    <p class="card-text text-muted"> Để đăng ký CTV hoặc Đại Lý vui lòng chuyển khoản vào !</p>
                    <p>NỘI DUNG: "Tên FB/email/zalo/số điện thoại"</p>
                    <p>Số TK: 0651000784613</p>
                    <p>Chủ TK: Phan Phụng Huỳnh Tài</p>        
                    <p>Ngân hàng Vietcombank</p>
                  </div>
                  <div class="card-footer border-top d-flex">
                    <div class="card-post__author d-flex">
                      <a href="https://www.facebook.com/TaiAndre99" class="card-post__author-avatar card-post__author-avatar--small" style="background-image: url('https://graph.facebook.com/100016789924854/picture');"></a>
                      <div class="d-flex flex-column justify-content-center ml-3">
                        <span class="card-post__author-name">Huỳnh Tài</span>
                        <small class="text-muted"><?php echo date("H:i:s d/m/Y"); ?></small>
                      </div>
                    </div>
                    <div class="my-auto ml-auto">
                      <a class="btn btn-sm btn-white" href="https://www.facebook.com/TaiAndre99">
                        <i class="far fa-bookmark mr-1"></i> Facebook </a>
                    </div>
                  </div>
                </div>
              </div>
              
            </div>