<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Add UID Vip Like</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="feFirstName">UID profile <span class="text-danger">*</span></label>
                            <input type="text" id="uid" class="form-control" placeholder="10000413926789"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label for="feLastName">Quantity like <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" class="form-control" placeholder="100"> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="feFirstName">Time (days) <span class="text-danger">*</span></label>
                            <input type="number" id="time" class="form-control" placeholder="30" min="1"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label for="feFirstName">Quantity like per crontab (5 mins) <span class="text-danger">*</span></label>
                            <input type="number" id="quantity_per_cron" class="form-control" placeholder="100" min="1"> 
                        </div>
                    </div>
                    <div class="form-row">
                        <style>
                            .custom-checkbox {
                                margin-left: 20px;
                            }
                            .custom-checkbox img{
                                margin-left: -10px;
                            }
                        </style>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="like" value="LIKE" name="reactions" checked>
                            <label class="custom-control-label" for="like"><img src="assets/images/reactions/like.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="love" value="LOVE" name="reactions">
                            <label class="custom-control-label" for="love"><img src="assets/images/reactions/love.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="wow" value="WOW" name="reactions">
                            <label class="custom-control-label" for="wow"><img src="assets/images/reactions/wow.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="haha" value="HAHA" name="reactions">
                            <label class="custom-control-label" for="haha"><img src="assets/images/reactions/haha.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="cry" value="CRY" name="reactions">
                            <label class="custom-control-label" for="cry"><img src="assets/images/reactions/cry.gif" height="50px" width="50px"></label>
                        </div>
                        <div class="custom-control custom-checkbox mb-1">
                            <input type="checkbox" class="custom-control-input" id="angry" value="ANGRY" name="reactions">
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
				<h6 class="m-0">List Vip UID</h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">id</th>
							<th scope="col" class="border-bottom-0">uid</th>
                            <th scope="col" class="border-bottom-0">quantity like</th>
                            <th scope="col" class="border-bottom-0">like per crontab</th>
                            <th scope="col" class="border-bottom-0">start day</th>
                            <th scope="col" class="border-bottom-0">end day</th>
                            <th scope="col" class="border-bottom-0">actions</th>
						</tr>
					</thead>
					<tbody id="vip_uid">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(() => {
        $("#btn_submit").on("click", () => {
            main();
        });
        loadListVip();
    });

    function main() {
        let uid = $("#uid").val().trim() || false;
        let quantity = $("#quantity").val().trim() || false;
        let time = $("#time").val().trim() || false;
        let quantity_per_cron = $("#quantity_per_cron").val().trim() || false;
        let reactions = getReactions();
        
        if(!uid || !quantity || !time || !quantity_per_cron || !reactions) {
            Swal({
                text: `Invalid data`,
                type: 'error',
                animation: false,
                customClass: 'animated tada'
            });
            return false;
        }
        addVip(uid, quantity, time, quantity_per_cron, reactions);
    }

    function addVip(uid, quantity, time, quantity_per_cron, reactions) {
        $.ajax({
            url: "<?php echo base_url('VipLikeSetting/addTask'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                uid: uid,
                quantity: quantity,
                time: time,
                quantity_per_cron: quantity_per_cron,
                reactions: reactions
            },
            beforeSend: () => {
                $("#btn_submit").text("Processing...").prop("disabled", true);
            }
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
                    text: res.message,
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
        }).always(() => {
            $("#btn_submit").text("Submit").prop("disabled", false);
            loadListVip();
        });
    }

    function getReactions() {
        let reactions = [];
        $('input[name="reactions"]:checked').map((i, e) => {
            return reactions.push($(e).val());
        });
        return reactions.length > 0 ? reactions : false;
    }

    function loadListVip() {
        $("#vip_uid").empty();
        $.ajax({
            url: "<?php echo base_url('VipLikeSetting/listTask'); ?>",
            type: "GET",
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
                for(let vip of res.data) {
                    $("#vip_uid")
                        .append($("<tr>")
                            .append($("<td>").html(vip.id))
                            .append($("<td>").html(vip.uid))
                            .append($("<td>").html(vip.quantity_like))
                            .append($("<td>").html(vip.quantity_per_cron))
                            .append($("<td>").html(vip.start_day))
                            .append($("<td>").html(vip.end_day))
                            .append($("<td>").html(`<button onclick="editVip(${vip.id})" type="button" class="mb-2 btn btn-sm btn-warning mr-1"><i class="material-icons">edit</i></button> <button onclick="deleteVip(${vip.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`))
                        );
                }
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

    function deleteVip(id) {
        if(confirm("Are you sure?")) {
            $.ajax({
                url: "<?php echo base_url('VipLikeSetting/deleteTask'); ?>",
                type: "POST",
                data: {
                    task_id: id
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
            }).always(() => loadListVip());
        }
    }

    function editVip(id) {
        console.log("edit");
    }


</script>