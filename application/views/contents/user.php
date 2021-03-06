<?php if ($this->session->userdata("role_id") == 1) { ?>
<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tạo mới user</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" id="password" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Họ tên</label>
                            <input type="text" id="fullname" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Link avatar</label>
                            <input type="text" id="avatar" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quyền <span class="text-danger">*</span></label>
                            <select id="permissions" class="form-control">
                                <option></option>
                                <option value="1">Admin</option>
                                <option value="2">CTV</option>
                                <option value="3">Member</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tài khoản</label>
                            <input type="number" id="balance" class="form-control" placeholder="" value="0"> 
                        </div>
                    </div>
                    
                    <button type="button" id="btn_submit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Nạp tiền cho user</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="dep_user" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Số tiền <span class="text-danger">*</span></label>
                            <input type="number" id="dep_amount" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <button type="button" id="btn_deposit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Chỉnh sửa user</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="e_username" class="form-control" disabled> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" id="e_password" class="form-control" placeholder="" disabled> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Họ tên</label>
                            <input type="text" id="e_fullname" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Link avatar</label>
                            <input type="text" id="e_avatar" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Quyền <span class="text-danger">*</span></label>
                            <select id="e_permissions" class="form-control">
                                <option></option>
                                <option value="1">Admin</option>
                                <option value="2">CTV</option>
                                <option value="3">Member</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Tài khoản</label>
                            <input type="number" id="e_balance" class="form-control" placeholder="" value="0"> 
                        </div>
                    </div>
                    <button type="button" id="btn_edit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">Danh sách user
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">username</th>
                            <th scope="col" class="border-bottom-0">Họ tên</th>
                            <th scope="col" class="border-bottom-0">Quyền</th>
							<th scope="col" class="border-bottom-0">Trạng thái</th>
                            <th scope="col" class="border-bottom-0">Tài khoản</th>
                            <th scope="col" class="border-bottom-0">Ngày tạo</th>
                            <th scope="col" class="border-bottom-0">Hành động</th>
						</tr>
					</thead>
					<tbody id="result">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(() => {
        loadListUser();

        $("#btn_submit").on("click", () => {
            createUser();
        });

        $("#btn_deposit").on("click", () => {
            deposit();
        });

        $("#btn_edit").on("click", () => {
            submitEditUser();
        });
    });

    function createUser() {
        let username    = $("#username").val().trim();
        let password    = $("#password").val().trim();
        let fullname    = $("#fullname").val().trim();
        let avatar      = $("#avatar").val().trim();
        let per         = $("#permissions").val().trim();
        let bl          = parseInt($("#balance").val().trim());
        if( !username || !password || !fullname || !per || bl < 0 ) {
            Swal({
                text: `Invalid data`,
                type: 'error',
                animation: false,
                customClass: 'animated tada'
            });
            return;
        }
        $.ajax({
            url: "User/create",
            type: "POST",
            dataType: "json",
            data: {
                username: username,
                password: password,
                fullname: fullname,
                avatar: avatar,
                permissions: per,
                balance: bl
            },
            beforeSend: () => loading("btn_submit", "show", "Processing")
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error',
                    animation: false,
                    customClass: 'animated tada'
                });
            } else {
                Swal({
                    html: `${res.message}`,
                    type: 'success',
                });
            }
        }).fail((xhr, textStatus, errorThrown) => {
            Swal({
                html: `${xhr.status} ${textStatus}: ${errorThrown}`,
                type: 'error',
                animation: false,
                customClass: 'animated tada'
            });
        }).always(() => {
            loadListUser();
            loading("btn_submit", "hide", "Submit");
        });
    }

    function deposit() {
        let username    = $("#dep_user").val().trim();
        let amount      = $("#dep_amount").val().trim();
        $.ajax({
            url: "User/deposit",
            type: "POST",
            dataType: "json",
            data: {
                username: username,
                amount: amount
            },
            beforeSend: () => loading("btn_deposit", "show", "Processing")
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error',
                    animation: false,
                    customClass: 'animated tada'
                });
            } else {
                Swal({
                    html: `${res.message}`,
                    type: 'success',
                });
            }
        }).fail((xhr, textStatus, errorThrown) => {
            Swal({
                html: `${xhr.status} ${textStatus}: ${errorThrown}`,
                type: 'error',
                animation: false,
                customClass: 'animated tada'
            });
        }).always(() => {
            loadListUser();
            loading("btn_deposit", "hide", "Submit");
        });
    }

    function deleteUser(user_id) {
        if( confirm("Are you sure?") ) {
            $.ajax({
                url: "User/delete",
                type: "POST",
                dataType: "json",
                data: {
                    user_id: user_id
                }
            }).done((res) => {
                if(res.error) {
                    Swal({
                        html: `${res.error.message}`,
                        type: 'error',
                        animation: false,
                        customClass: 'animated tada'
                    });
                } else {
                    Swal({
                        html: `${res.message}`,
                        type: 'success',
                    });
                }
            }).fail((xhr, textStatus, errorThrown) => {
                Swal({
                    html: `${xhr} ${textStatus}: ${errorThrown}`,
                    type: 'error',
                });
            }).always(() => {
                loadListUser(); 
            });
        }
    }

    function loadListUser() {
        $("#result").empty();
        $.getJSON("User/listUser", (res) => {
            if( res.data ) {
                let i = 1;
                for(let user of res.data) {
                    $("#result").append($("<tr>")
                        .append($("<td>").html(i))
                        .append($("<td>").html(user.username))
                        .append($("<td>").html(user.full_name))
                        .append($("<td>").html(user.role_name))
                        .append($("<td>").html(`${user.active == 1 ? '<label class="badge badge-success">active</label>' : '<label class="badge badge-danger">deactive</label>'}`))
                        .append($("<td>").html(user.balance))
                        .append($("<td>").html(user.created_at))
                        .append($("<td>").html(`<button onclick="editUser(${user.id})" type="button" class="mb-2 btn btn-sm btn-warning mr-1"><i class="material-icons">edit</i></button> <button onclick="deleteUser(${user.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`))
                    );
                    i++;
                }
            }
        });
    }

    function editUser(user_id) {
        $.ajax({
            url: "User/getInfoById",
            method: "GET", 
            dataType: "json",
            data: {
                user_id: user_id
            }
        }).done((res) => {
            if (res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error',
                });
            } else {
                $("#e_username").val(res.data.username);
                $("#e_fullname").val(res.data.full_name);
                $("#e_avatar").val(res.data.avatar);
                $("#e_permissions").val(res.data.role_id);
                $("#e_balance").val(res.data.balance);
                $("#e_password").val("xxxxxx");
            }
        }).fail((xhr, textStatus, errorThrown) => {
            Swal({
                html: `${status} ${textStatus}: ${errorThrown}`,
                type: 'error',
            });
        });
    }

    function submitEditUser() {
        let username    = $("#e_username").val().trim();
        let fullname    = $("#e_fullname").val().trim();
        let avatar      = $("#e_avatar").val().trim();
        let per         = $("#e_permissions").val().trim();
        let bl          = parseInt($("#e_balance").val().trim());
        if( !username || !fullname || !per || bl < 0 ) {
            Swal({
                text: `Invalid data`,
                type: 'error',
            });
            return;
        }
        $.ajax({
            url: "User/edit",
            type: "POST",
            dataType: "json",
            data: {
                username: username,
                fullname: fullname,
                avatar: avatar,
                permissions: per,
                balance: bl
            },
            beforeSend: () => loading("btn_edit", "show", "Processing")
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error'
                });
            } else {
                Swal({
                    html: `${res.message}`,
                    type: 'success'
                });
            }
        }).fail((xhr, textStatus, errorThrown) => {
            Swal({
                html: `${xhr.status} ${textStatus}: ${errorThrown}`,
                type: 'error'
            });
        }).always(() => {
            loadListUser();
            loading("btn_edit", "hide", "Submit");
        });
    }

</script>

<?php } else { ?>
    
    <div class="col-lg-6 mb-6">
        
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Chỉnh sửa thông tin cá nhân</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="e_username" class="form-control" disabled> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" id="e_password" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Họ tên</label>
                            <input type="text" id="e_fullname" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Link avatar</label>
                            <input type="text" id="e_avatar" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    
                    <button type="button" id="btn_edit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(() => {
        loadInfo();
        $("#btn_edit").on("click", () => {
            changeInfo();
        });
    });

    function loadInfo() {
        $.ajax({
            method: "GET",
            url: "User/info",
            dataType: "json"
        }).done(res => {
            if (res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error'
                });
            } else {
                $("#e_username").val(res.data.username);
                $("#e_fullname").val(res.data.full_name);
                $("#e_avatar").val(res.data.avatar);
            }
        }).fail(() => {
            Swal({
                html: `Kết nối tới server thất bại. Vui lòng thử lại`,
                type: 'error'
            });
        });
    }

    function changeInfo() {
        let password    = $("#e_password").val().trim();
        let fullname    = $("#e_fullname").val().trim();
        let avatar      = $("#e_avatar").val().trim();
        $.ajax({
            method: "POST",
            url: "User/changeInfoProfile",
            dataType: "json",
            data: {
                password: password,
                fullname: fullname,
                avatar: avatar
            }
        }).done(res => {
            if (res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error'
                });
            } else {
                Swal({
                    html: `Cập nhật thông tin thành công`,
                    type: 'success'
                });
            }
        }).fail(() => {
            Swal({
                html: `Kết nối tới server thất bại. Vui lòng thử lại`,
                type: 'error'
            });
        });
    }

</script>

<?php } ?>