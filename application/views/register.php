<!DOCTYPE html>
<html lang="en">
<head>
	<title>Đăng ký</title>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="<?php echo base_url(); ?>">
<!--===============================================================================================-->	
	<link rel="icon" type="image/png" href="assets/login/images/icons/favicon.ico"/>
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/fonts/Linearicons-Free-v1.0.0/icon-font.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/animate/animate.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/select2/select2.min.css">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="assets/login/vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="assets/login/css/util.css">
    <link rel="stylesheet" type="text/css" href="assets/login/css/main.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.min.css">
<!--===============================================================================================-->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('assets/login/images/bg-01.jpg');">
			<div class="wrap-login100 p-l-110 p-r-110 p-t-62 p-b-33">
				<form class="login100-form validate-form flex-sb flex-w">
					<span class="login100-form-title p-b-53">
						Đăng Ký
					</span>
					
					<div class="p-t-31 p-b-9">
						<span class="txt1">
							Username
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Username is required">
						<input class="input100" type="text" name="username" id="username">
						<span class="focus-input100"></span>
					</div>
					
					<div class="p-t-13 p-b-9">
						<span class="txt1">
							Mật khẩu
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="pass" id="password">
						<span class="focus-input100"></span>
                    </div>

                    <div class="p-t-13 p-b-9">
						<span class="txt1">
							Nhập lại mật khẩu
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="re_password" id="re_password">
						<span class="focus-input100"></span>
                    </div>

                    <div class="p-t-31 p-b-9">
						<span class="txt1">
							Họ tên
						</span>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Full name is required">
						<input class="input100" type="text" name="fullname" id="fullname">
						<span class="focus-input100"></span>
					</div>

                    <div class="p-t-31 p-b-9">
                        <div class="g-recaptcha" data-sitekey="6LctA5UUAAAAALSdpbtnU773bUc0EgDbpHl2Q_aY"></div>
					</div>
                                        

					<div class="container-login100-form-btn m-t-17">
						<button type="button" class="login100-form-btn" id="btn_register">
							Đăng Ký
						</button>
					</div>
                    
					<div class="w-full text-center p-t-55">
						<a href="login" class="txt2 bo1">
							Đăng nhập
						</a>
					</div>
				</form>
			</div>
		</div>
	</div>
	
<!--===============================================================================================-->
	<script src="assets/login/vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
	<script src="assets/login/vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
	<script src="assets/login/vendor/bootstrap/js/popper.js"></script>
	<script src="assets/login/vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
	<script src="assets/login/vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
	<script src="assets/login/vendor/daterangepicker/moment.min.js"></script>
	<script src="assets/login/vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
	<script src="assets/login/vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
    <script src="assets/login/js/main.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.33.1/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(() => {
            $("#btn_register").on("click", () => {
                register();
            });
        });

        const register = function() {

            $.ajax({
                method: "POST",
                url: "Register/newUser",
                dataType: "json",
                data: {
                    username: $("#username").val().trim(),
                    password: $("#password").val().trim(),
                    re_password: $("#re_password").val().trim(),
                    fullname: $("#fullname").val().trim(),
                    captcha: $("#g-recaptcha-response").val()
                }
            }).done((res) => {
                if(res.error) {
                    Swal({
                        html: res.error.message,
                        type: 'error'
                    });
                } else {
                    Swal({
                        html: `Đăng ký thành công. Bạn có thể đăng nhập ngay bây giờ`,
                        type: 'success'
                    });
                }
            }).fail(() => {
                Swal({
                    html: `Không thể kết nối tới máy chủ. Vui lòng thử lại sau.`,
                    type: 'error'
                });
            });
        }

        
        
    </script>

</body>
</html>