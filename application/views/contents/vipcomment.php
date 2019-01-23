<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Add UID Vip Comment</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>UID profile <span class="text-danger">*</span></label>
                            <input type="text" id="uid" class="form-control" placeholder=""> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Quantity comment <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" class="form-control" placeholder="100" value="100"> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Time (days) <span class="text-danger">*</span></label>
                            <input type="number" id="time" class="form-control" placeholder="" min="1" value="30"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Quantity comment per crontab (5 mins) <span class="text-danger">*</span></label>
                            <input type="number" id="quantity_per_cron" class="form-control" min="1" value="20"> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label>Messages comment (1 msg per line, max 4000 char) <span class="text-danger">*</span></label>
                            <textarea id="msg_cmt" class="form-control" rows="5"></textarea>
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
                        <strong class="mr-1">UID:</strong>
                        <strong class="ml-auto" id="bill_uid"></strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Quantity comment:</strong>
                        <strong class="ml-auto" id="bill_quantity">0</strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Expiry day:</strong>
                        <strong class="ml-auto" id="bill_time">0</strong><strong>&nbsp;days</strong>
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
				<h6 class="m-0">List Vip UID</h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">id</th>
							<th scope="col" class="border-bottom-0">uid</th>
                            <th scope="col" class="border-bottom-0">quantity cmt</th>
                            <th scope="col" class="border-bottom-0">like per crontab</th>
                            <th scope="col" class="border-bottom-0">start day</th>
                            <th scope="col" class="border-bottom-0">end day</th>
                            <th scope="col" class="border-bottom-0">actions</th>
						</tr>
					</thead>
					<tbody id="vip_cmt">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
    const price = parseInt("<?= PRICE_PER_CMT ?>");

    $(() => {
        $("#btn_submit").on("click", () => {
            main();
        });
        loadListVip();
        
        $("#uid, #quantity, #time").on("keyup", () => {
            let quantity = $("#quantity").val().trim();
            let time = $("#time").val().trim();
            $("#bill_uid").text($("#uid").val().trim());
            $("#bill_quantity").text(quantity);
            $("#bill_time").text(time);
            $("#bill_amount").text(parseInt(quantity) * parseInt(time) * price);
        });
    });

    function main() {
        let uid                 = $("#uid").val().trim() || false;
        let quantity            = $("#quantity").val().trim() || false;
        let time                = $("#time").val().trim() || false;
        let quantity_per_cron   = $("#quantity_per_cron").val().trim() || false;
        let msg_cmt             = $("#msg_cmt").val().trim() || false;
        
        if(!uid || !quantity || !time || !quantity_per_cron || !msg_cmt) {
            Swal({
                text: `Invalid data`,
                type: 'error'
            });
            return false;
        }
        addVip(uid, quantity, time, quantity_per_cron, msg_cmt);
    }

    function addVip(uid, quantity, time, quantity_per_cron, msg_cmt) {
        $.ajax({
            url: "VipComment/addTask",
            type: "POST",
            dataType: "json",
            data: {
                uid: uid,
                quantity: quantity,
                time: time,
                quantity_per_cron: quantity_per_cron,
                msg_cmt: msg_cmt
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
            loadListVip();
            loading("btn_submit", "hide", "Submit");
        });
    }

    function loadListVip() {
        $("#vip_cmt").empty();
        $.ajax({
            url: "VipComment/listTask",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error',
                });
            }
            else {
                for(let vip of res.data) {
                    $("#vip_cmt")
                        .append($("<tr>")
                            .append($("<td>").html(vip.id))
                            .append($("<td>").html(vip.uid))
                            .append($("<td>").html(vip.quantity))
                            .append($("<td>").html(vip.quantity_per_cron))
                            .append($("<td>").html(vip.start_day))
                            .append($("<td>").html(vip.end_day))
                            .append($("<td>").html(`<button onclick="editVip(${vip.id})" type="button" class="mb-2 btn btn-sm btn-warning mr-1"><i class="material-icons">edit</i></button> <button onclick="deleteVip(${vip.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`))
                        );
                }
            }
        }).fail((xhr, textStatus, thrown) => {
            Swal({
                html: `Server error: ${thrown}`,
                type: 'error'
            });
        });
    }

    function deleteVip(id) {
        if(confirm("Are you sure?")) {
            $.ajax({
                url: "VipComment/deleteTask",
                type: "POST",
                data: {
                    task_id: id
                },
                dataType: "json"
            }).done((res) => {
                if(res.error) {
                    Swal({
                        html: `${res.error.message}`,
                        type: 'error',
                    });
                }
                else {
                    Swal({
                        text: `${res.message}`,
                        type: 'success'
                    });
                }
            }).fail((xhr, textStatus, thrown) => {
                Swal({
                    html: `Server error: ${thrown}`,
                    type: 'error'
                });
            }).always(() => loadListVip());
        }
    }


</script>