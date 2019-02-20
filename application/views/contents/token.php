<div class="row">
    <div class="col-lg-12 mb-6">
        <div class="card card-small overflow-hidden mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Import Token or Cookie</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <!-- <label for="feDescription">List Token</label> -->
                            <textarea placeholder="EAAAA... hoặc c_user=000; xs=xxxxx" id="list_token" class="form-control" rows="5" place></textarea>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Chọn loại dữ liệu import <span class="text-danger">*</span></label>
                            <select id="type" class="form-control">
                                <option value="cookie">Cookie</option>
                                <option value="token">Token</option>
                            </select>
                        </div>
                    </div>
                    <button type="button" id="btn_import" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">List Token 
                <button type="button" id="btn_checkLive" class="btn btn-success">Check Live All Token and Delete if Die</button>
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">họ tên</th>
							<th scope="col" class="border-bottom-0">token / cookie</th>
							<th scope="col" class="border-bottom-0">status</th>
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
    var tokens;
    $(document).ready(() => {
        $("#btn_import").on("click", async () => {
            $("#btn_import").text("Processing...").prop("disabled", true);
            let list_token = ($("#list_token").val().trim() && $("#list_token").val().trim().split("\n")) || false;
            let type = $("#type").val().trim();
            if(!list_token || !type) {
                Swal({
                    text: `List token is required`,
                    type: 'error',
                    animation: false,
                    customClass: 'animated tada'
                });
                $("#btn_import").text("Submit").prop("disabled", false);
                return;
            }
            let live = 0, die = 0;
            if(type == "cookie") {
                for(let cookie of list_token) {
                    try {
                        let result = await importCookie(cookie);
                        console.log(result);
                        live++;
                    } catch(e) {
                        die++;
                        console.log(e);
                    }
                }
            }
            else {
                for(let token of list_token) {
                    try {
                        let data = await checkLive(token);
                        saveDb(token, data);
                        live++;

                    } catch(e) {
                        die++;
                    }
                }
            }
            $("#btn_import").text("Submit").prop("disabled", false);
            Swal({
                text: `Live: ${live}, Die: ${die}`,
                type: 'success',
            });
            
        });
        $("#btn_checkLive").on("click", async () => {
            $("#btn_checkLive").text("Processing...").prop("disabled", true);
            let live = 0, die = 0;
            for(let token of tokens) {
                try {
                    let data = await checkLive(token.token);
                    //saveDb(token.token, data);
                    live++;
                } catch(e) {
                    die++;
                    deletTokenDie(token.id);
                }
            }
            $("#btn_checkLive").text("Check Live All Token and Delete if Die").prop("disabled", false);
            Swal({
                text: `Live: ${live}, Die: ${die}`,
                type: 'success',
            });

        });

        loadToken();

    });

    function importCookie(cookie) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: "Token/importCookie",
                method: "POST",
                dataType: "json",
                data: {
                    cookie: cookie
                }
            }).done((res) => {
                if(res.error) {
                    return reject(res.error.message);
                }
                else {
                    return resolve(res.message);
                }
            }).fail(() => reject("Lỗi server"));
        });
    }

    function checkLive(token) {
        return new Promise((resolve, reject) => {
    		$.getJSON(`https://graph.facebook.com/v3.2/me?fields=id%2Cname%2Cpicture%7Burl%7D%2Cgender&access_token=${token}`, res => resolve(res))
            .fail(() => reject('Token die'));
    	});
    }

    function deletTokenDie(id) {
        $.ajax({
            url: "<?php echo base_url('Token/delete'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                id: id
            }
        });
    }

    function saveDb(token, data) {
        $.ajax({
            url: "<?php echo base_url('Token/import'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                data: JSON.stringify(data),
                token: token
            }
        });
    }

    function loadToken() {
        $.ajax({
            url: "<?php echo base_url('Token/getTokens'); ?>",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if( ! res.error) {
                tokens = res.data;
                let i = 1;
                for(let token of res.data) {
                    $("#result").append($("<tr>")
                        .append($("<td>").html(i))
                        .append($("<td>").html(token.fullname))
                        .append($("<td>").html(`<input class="form-control" type="text" value="${token.token ? token.token : token.cookie}">`))
                        .append($("<td>").html(`${token.status == 1 ? '<label class="badge badge-success">live</label>' : '<label class="badge badge-danger">die</label>'}`))
                    );
                    i++;
                }
            }
        }).fail((xhr, textStatus) => {
            console.log(`${xhr}: ${textStatus}`);
        });
    }


</script>