<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Add UID Buff Like</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Post ID <span class="text-danger">*</span></label>
                            <input type="text" id="post_id" class="form-control" placeholder=""> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Quantity like <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" class="form-control" placeholder="100" value="100"> 
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
                <h6 class="m-0">Billing Information</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Post ID:</strong>
                        <strong class="ml-auto" id="bill_post_id"></strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Quantity like:</strong>
                        <strong class="ml-auto" id="bill_quantity">0</strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Total amount:</strong>
                        <strong class="ml-auto" id="bill_amount">0</strong><strong>&nbsp;VND</strong>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</div>
<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">List Task</h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">id</th>
							<th scope="col" class="border-bottom-0">post id</th>
                            <th scope="col" class="border-bottom-0">quantity like</th>
                            <th scope="col" class="border-bottom-0">status</th>
                            <th scope="col" class="border-bottom-0">created at</th>
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
    let table;
    const price = "<?= PRICE_PER_LIKE_BUFF ?>";
    $(() => {
        $("#btn_submit").on("click", () => {
            createTask();
        });

        $("#post_id, #quantity").on("keyup", () => {
            let quantity = $("#quantity").val().trim();
            $("#bill_post_id").text($("#post_id").val().trim());
            $("#bill_quantity").text(quantity);
            $("#bill_amount").text(parseInt(quantity) * parseInt(price));
        });

        table = $(".table").DataTable({
            dom: '<"datatable-header"B><"datatable-scroll"t>ip<"datatable-footer">',
            columnDefs: [{
                className: "text-center",
                targets: "_all"
            }],
            lengthMenu: [
                [ 10, 25, 50, -1 ],
                [ '10 rows', '25 rows', '50 rows', 'Show all' ]
            ],
            paging: true,
            select: true,
            pageLength: 25,
            ordering: false,
            responsive: true,
            buttons: [
                'pageLength',
                {
                    text: 'Load Data',
                    className: 'btn btn-success',
                    action: (e, dt, node, config) => {
                        loadListTask();
                    }
                }     
            ]
        });
        loadListTask();

    });

    function createTask() {
        let post_id     = $("#post_id").val().trim() || false;
        let quantity    = $("#quantity").val().trim() || false;
        if(!post_id || !quantity) {
            Swal({
                text: `Invalid data`,
                type: 'error'
            });
            return false;
        }
        $.ajax({
            url: "BuffLike/addTask",
            type: "POST",
            dataType: "json",
            data: {
                post_id: post_id,
                quantity: quantity
            },
            beforeSend: () => {
                loading("btn_submit", "show", "Processing...");
            }
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error',
                });
            }
            else {
                Swal({
                    text: res.message,
                    type: 'success'
                });
            }
        }).fail((xhr, textStatus, thrown) => {
            Swal({
                html: `${xhr} ${textStatus}: ${thrown}`,
                type: 'error',
            });
        }).always(() => {
            loadListTask();
            loading("btn_submit", "hide", "Submit");
        });
    }

    function loadListTask() {
        table.clear().draw();
        $.ajax({
            url: "BuffLike/listTask",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.data) {
                for(let task of res.data) {
                    table.row.add({
                        "0": task.id,
                        "1": task.post_id,
                        "2": task.quantity,
                        "3": `${(task.is_running == "0" && '<label class="badge badge-warning">ready</label>') || ((task.is_running == "1" && task.is_done == "0") && '<label class="badge badge-info">running</label>') || (task.is_done == "1" && '<label class="badge badge-success">done</label>')}`,
                        "4": task.created_at,
                        "5": `<button onclick="deleteTask(${task.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`
                        
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

    function deleteTask(id) {
        if(confirm("Are you sure?")) {
            $.ajax({
                url: "BuffLike/deleteTask",
                type: "POST",
                data: {
                    id: id
                },
                dataType: "json"
            }).done((res) => {
                if(res.error) {
                    Swal({
                        text: `${res.error.message}`,
                        type: 'error',
                        animation: false,
                        customClass: 'animated tada'
                    });
                }
                else {
                    Swal({
                        text: `${res.message}`,
                        type: 'success'
                    });
                }
            }).fail((xhr, textStatus) => {
                Swal({
                    text: `Server error: ${textStatus}`,
                    type: 'error',
                    animation: false,
                    customClass: 'animated tada'
                });
            }).always(() => loadListTask());
        }
    }


</script>