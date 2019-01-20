<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Create New user</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Password <span class="text-danger">*</span></label>
                            <input type="password" id="password" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Full name</label>
                            <input type="text" id="fullname" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Link avatar</label>
                            <input type="text" id="avatar" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Permissions <span class="text-danger">*</span></label>
                            <select id="permissions" class="form-control">
                                <option></option>
                                <option value="1">Admin</option>
                                <option value="2">CTV</option>
                                <option value="3">Member</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Balance</label>
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
                <h6 class="m-0">Deposit</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Username <span class="text-danger">*</span></label>
                            <input type="text" id="dep_user" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Amount <span class="text-danger">*</span></label>
                            <input type="number" id="dep_amount" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <button type="button" id="btn_deposit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">List User 
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">username</th>
                            <th scope="col" class="border-bottom-0">full name</th>
                            <th scope="col" class="border-bottom-0">permissions</th>
							<th scope="col" class="border-bottom-0">status</th>
                            <th scope="col" class="border-bottom-0">balance</th>
                            <th scope="col" class="border-bottom-0">created</th>
                            <th scope="col" class="border-bottom-0">actions</th>
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
</script>