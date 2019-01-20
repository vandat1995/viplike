<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login - Vip like</title>
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

</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('assets/login/images/bg-01.jpg');">
			<div class="wrap-login100 p-l-110 p-r-110 p-t-62 p-b-33">
				<form class="login100-form validate-form flex-sb flex-w">
					<span class="login100-form-title p-b-53">
						Sign In With
					</span>

					<a href="javascript:void(0)" class="btn-face m-b-20">
						<i class="fa fa-facebook-official"></i>
						Facebook
					</a>

					<a href="javascript:void(0)" class="btn-google m-b-20">
						<img src="assets/login/images/icons/icon-google.png" alt="GOOGLE">
						Google
					</a>
					
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
							Password
						</span>

						<a href="javascript:void(0)" class="txt2 bo1 m-l-5" tabindex='-1'>
							Forgot?
						</a>
					</div>
					<div class="wrap-input100 validate-input" data-validate = "Password is required">
						<input class="input100" type="password" name="pass" id="password">
						<span class="focus-input100"></span>
                    </div>

					<div class="container-login100-form-btn m-t-17">
						<button type="button" class="login100-form-btn" id="btn_signin">
							Sign In
						</button>
					</div>

					<div class="w-full text-center p-t-55">
						<span class="txt2">
							Not a member?
						</span>

						<a href="javascript:void(0)" class="txt2 bo1">
							Sign up now
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
            $("#btn_signin").on("click", () => {
                signin();
            });

            $("#password").keypress((e) => {
				if(e.which == 13)
                    signin();
			});
        });

        function signin() {
            let username = $("#username").val().trim();
            let password = $("#password").val();
            $.ajax({
                url: "<?php echo base_url('Authentication/validate'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    username: username,
                    password: password
                }
            }).done((res) => {
                if(res.error) {
                    Swal({
                        html: res.error.message,
                        type: 'error',
                        animation: false,
                        customClass: 'animated tada'
                    });
                }
                else {
                    window.location = `<?php echo base_url('${res.url}'); ?>`;
                }
            }).fail((xhr, textStatus) => {
                Swal({
                    text: `Server error: ${textStatus}`,
                    type: 'error',
                    animation: false,
                    customClass: 'animated tada'
                });
            });
        }
        
    </script>

</body>
</html>