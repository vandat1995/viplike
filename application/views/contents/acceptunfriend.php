<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Create Task</h6>
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
                            <label>Type <span class="text-danger">*</span></label>
                            <select id="type" class="form-control">
                                <option></option>
                                <option value="accept">Accept</option>
                                <option value="unfriend">UnFriends</option>
                            </select>
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
							<th scope="col" class="border-bottom-0">id</th>
                            <th scope="col" class="border-bottom-0">uid</th>
                            <th scope="col" class="border-bottom-0">type</th>
                            <th scope="col" class="border-bottom-0">status</th>
							<th scope="col" class="border-bottom-0">created at</th>
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
            let type = $("#type").val();
            if(!list_token || !type) {
                Swal({
                    text: `Invalid data input`,
                    type: 'error',
                });
                return;
            }
            let live = 0, die = 0;
            for(let token of list_token) {
                try {
                    let data = await checkLive(token);
                    saveDb(type, token, data);
                    live++;

                } catch(e) {
                    die++;
                }
            }
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
                    className: 'btn btn-warning',
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
            url: "FriendManagement/list",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.data) {
                for(let task of res.data) {
                    table.row.add({
                        "0": task.id,
                        "1": task.uid,
                        "2": task.type,
                        "3": `${task.is_done == 1 ? '<label class="badge badge-success">done</label>' : '<label class="badge badge-info">running</label>'}`,
                        "4": task.created_at
                    });
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
                url: "FriendManagement/deleteTask",
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

    function checkLive(token) {
        return new Promise((resolve, reject) => {
    		$.getJSON(`https://graph.facebook.com/v3.2/me?fields=id%2Cname%2Cpicture%7Burl%7D%2Cgender&access_token=${token}`, res => resolve(res))
            .fail(() => reject('Token die'));
    	});
    }

    function saveDb(type, token, data) {
        $.ajax({
            url: "FriendManagement/import",
            type: "POST",
            dataType: "json",
            data: {
                type: type,
                data: JSON.stringify(data),
                token: token
            }
        });
    }

</script>