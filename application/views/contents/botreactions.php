<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tạo bot mới</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>List token <span class="text-danger">*</span></label>
                            <textarea id="tokens" class="form-control" rows="5" placeholder="EAAAA..."></textarea>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Số ngày thuê <span class="text-danger">*</span></label>
                            <input type="number" id="time" class="form-control" placeholder="" min="1" value="30"> 
                        </div>
                        
                    </div>
                    
                    <label>Loại cảm xúc <span class="text-danger"> *</span></label>
                    <div class="form-row">
                        <style>
                            .custom-radio {
                                margin-left: 20px;
                            }
                            .custom-radio img{
                                margin-left: -10px;
                            }
                        </style>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="like" value="LIKE" name="reactions">
                            <label class="custom-control-label" for="like"><img src="assets/images/reactions/like.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="love" value="LOVE" name="reactions">
                            <label class="custom-control-label" for="love"><img src="assets/images/reactions/love.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="wow" value="WOW" name="reactions">
                            <label class="custom-control-label" for="wow"><img src="assets/images/reactions/wow.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="haha" value="HAHA" name="reactions">
                            <label class="custom-control-label" for="haha"><img src="assets/images/reactions/haha.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="sad" value="SAD" name="reactions">
                            <label class="custom-control-label" for="sad"><img src="assets/images/reactions/sad.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-radio mb-1">
                            <input type="radio" class="custom-control-input reactions" id="angry" value="ANGRY" name="reactions">
                            <label class="custom-control-label" for="angry"><img src="assets/images/reactions/angry.gif" height="50px" width="50px"></label>
                        </div>
                    </div>
                    <button type="button" id="btn_submit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
    
</div>
<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">List Task 
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">Họ tên</th>
                            <th scope="col" class="border-bottom-0">UID</th>
                            <th scope="col" class="border-bottom-0">Cảm xúc</th>
                            <th scope="col" class="border-bottom-0">Trạng thái</th>
							<th scope="col" class="border-bottom-0">Ngày thuê</th>
                            <th scope="col" class="border-bottom-0">Ngày hết hạn</th>
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
    let table;
    $(() => {
        $("#btn_submit").on("click", async () => {
            let list_token = ($("#tokens").val().trim() && $("#tokens").val().trim().split("\n")) || false;
            let reactions = getReactions();
            let duration = $("#time").val().trim();
            if(!list_token || !reactions || !duration) {
                Swal({
                    text: `Invalid data input`,
                    type: 'error',
                });
                return;
            }
            let live = 0, die = 0;
            loading("btn_submit", "show", "Processing...");
            for(let token of list_token) {
                try {
                    let data = await checkLive(token);
                    saveDb(reactions, token, data, duration);
                    live++;
                } catch(e) {
                    die++;
                }
            }
            loading("btn_submit", "hide", "Submit");
            loadTable();
            Swal({
                text: `Live: ${live}, Die: ${die}`,
                type: 'success',
            });
        });

        table = $(".table").DataTable({
            dom: '<"datatable-header">B<"datatable-scroll"t>ip<"datatable-footer">',
            columnDefs: [{
                className: "text-center",
                targets: "_all"
            }],
            paging: true,
            select: true,
            pageLength: 25,
            ordering: false,
            responsive: true,
            buttons: [
                {
                    text: 'Load Data',
                    className: 'btn btn-success',
                    action: (e, dt, node, config) => {
                        loadTable();
                    }
                },
                {
                    text: 'Delete',
                    className: 'btn btn-danger',
                    action: (e, dt, node, config) => {
                        let list_id = [];
                        var rowsData = table.rows('.selected').data();
                        for(let i = 0; i < rowsData.length; i++) {
                            list_id.push(rowsData[i]["0"]);
                        }
                        deleteTask(list_id);
                    }
                }       
            ]
        });

        loadTable();
    });
    
    function loadTable() {
        table.clear().draw();
        $.ajax({
            url: "BotReactions/list",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.data) {
                let i = 1;
                for(let task of res.data) {
                    table.row.add({
                        "0": i,
                        "1": task.name,
                        "2": task.uid,
                        "3": task.reactions,
                        "4": `${task.status == 1 ? '<label class="badge badge-success">live</label>' : '<label class="badge badge-danger">die</label>'}`,
                        "5": task.start_day,
                        "6": task.end_day
                    });
                    i++;
                }
                table.draw();
            }
        }).fail((xhr, textStatus, errorThrown) => {
            Swal({
                text: `${xhr} ${textStatus}: ${errorThrown}`,
                type: 'error'
            });
        });
    }

    function deleteTask(list_id) {
        if(list_id.length <= 0 || !confirm("Are you sure?")) {
            return false;
        }
        else {
            $.ajax({
                url: "BotReactions/delete",
                type: "POST",
                dataType: "json",
                data: {
                    list_id: JSON.stringify(list_id)
                }
            }).done((res) => {
                if(res.error) {
                    Swal({
                        text: `${res.error.message}`,
                        type: 'error'
                    });
                }
                else {
                    Swal({
                        text: `${res.message}`,
                        type: 'success'
                    });
                }
            }).fail((xhr, textStatus, errorThrown) => {
                Swal({
                    text: `${xhr} ${textStatus}: ${errorThrown}`,
                    type: 'error'
                });
            }).always(() => loadTable());
        }
    }

    function saveDb(reactions, token, data, duration) {
        $.ajax({
            url: "BotReactions/import",
            type: "POST",
            dataType: "json",
            data: {
                reactions: reactions,
                duration: duration,
                data: JSON.stringify(data),
                token: token
            }
        });
    }

    function checkLive(token) {
        return new Promise((resolve, reject) => {
    		$.getJSON(`https://graph.facebook.com/v3.2/me?fields=id%2Cname%2Cpicture%7Burl%7D%2Cgender&access_token=${token}`, res => resolve(res))
            .fail(() => reject('Token die'));
    	});
    }

    function getReactions() {
        let radios = document.getElementsByClassName("reactions");
        for(let i = 0; i < radios.length; i++) {
            if(radios[i].checked) {
                return radios[i].value;
            }
        }
        return false;
    }
</script>