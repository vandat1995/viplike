
<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Thêm mới vip like</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>UID <span class="text-danger">*</span></label>
                            <input type="text" id="uid" class="form-control" placeholder=""> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Số mắt <span class="text-danger">*</span></label>
                            <!-- <input type="number" id="quantity" class="form-control" placeholder="100" value="100">  -->
                            <select id="quantity" class="form-control">
                                <option></option>
                                <?php 
                                    if($prices) 
                                    {
                                        foreach($prices as $price)
                                        {
                                            echo '<option value="'. $price->quantity .'">' . $price->quantity . '</option>';
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Số ngày thuê <span class="text-danger">*</span></label>
                            <select id="time" class="form-control">
                                <option value="1">30</option>
                                <option value="2">60</option>
                                <option value="3">90</option>
                                <option value="4">120</option>
                            </select>
                            <!-- <input type="number" id="time" class="form-control" placeholder="" min="1" value="30">  -->
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
                <h6 class="m-0">Thông tin đơn hàng</h6>
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
                        <strong class="mr-1">Số lượng mắt:</strong>
                        <strong class="ml-auto" id="bill_quantity">0</strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Số ngày thuê:</strong>
                        <strong class="ml-auto" id="bill_time">0</strong><strong>&nbsp;tháng</strong>
                    </span>
                </li>
                <li class="list-group-item p-3">
                    <span class="d-flex mb-2">
                        <strong class="mr-1">Tổng tiền phải trả:</strong>
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
				<h6 class="m-0">Danh sách Vip đã cài</h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
							<th scope="col" class="border-bottom-0">uid</th>
                            <th scope="col" class="border-bottom-0">Số lượng mắt</th>
                            <th scope="col" class="border-bottom-0">Trạng thái</th>
                            <th scope="col" class="border-bottom-0">Ngày bắt đầu</th>
                            <th scope="col" class="border-bottom-0">Ngày kết thúc</th>
                            <th scope="col" class="border-bottom-0">Hành động</th>
						</tr>
					</thead>
					<tbody id="vip_uid">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Chỉnh sửa gói vip</h4>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="form-group col-md-12">
						<label>Tốc độ like mỗi 5 phút <span class="text-danger">*</span></label>
						<input type="number" id="equantity_per_cron" class="form-control" min="1">
                        <input type="hidden" id="eid" class="form-control"> 
					</div>
				</div>
				<label>Loại cảm xúc <span class="text-danger">*</span></label>
				
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
			<button type="button" onclick="editVip('btn_edit')" id="btn_edit" class="btn btn-primary">Xác nhận</button>
		</div>
	</div>
</div>
</div>

<script>
    const prices = JSON.parse('<?php echo json_encode($prices); ?>');
    let table;
    var list_id = [];
    $(document).ready(() => {
        $("#elike").prop("disabled",true);
        $("#like").prop("disabled",true);
        $("#btn_submit").on("click", () => {
            main();
        });

        $("#quantity, #time").on("change", () => {
            let quantity = $("#quantity").val().trim();
            let time = $("#time").val().trim();
            $("#bill_uid").text($("#uid").val().trim());
            $("#bill_quantity").text(quantity);
            $("#bill_time").text(time);
            let price;
            for(let pri of prices) {
                if(pri.quantity == quantity) {
                    price = pri.price_per_month;
                    break;
                }
            }
            $("#bill_amount").text(parseInt(time) * price);
        });

        table = $(".table").DataTable({
            dom: '<"datatable-header"Bf><"datatable-scroll"t>ip<"datatable-footer">',
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
                        loadListVip();
                    }
                }       
            ]
        });

    
        
        loadListVip();

    });

    function main() {
        let uid = $("#uid").val().trim() || false;
        let quantity = $("#quantity").val().trim() || false;
        let time = $("#time").val().trim() || false;
        
        
        if(!uid || !quantity || !time) {
            Swal({
                text: `Invalid data`,
                type: 'error',
                animation: false,
                customClass: 'animated tada'
            });
            return false;
        }
        addVip(uid, quantity, time);
    }

    function addVip(uid, quantity, time) {
        $.ajax({
            url: "<?php echo base_url('VipMat/addTask'); ?>",
            type: "POST",
            dataType: "json",
            data: {
                uid: uid,
                quantity: quantity,
                time: time
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

    function loadListVip() {
        table.clear().draw();
        $.ajax({
            url: "VipMat/listTask",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.data) {
                let i = 1;
                for(let vip of res.data) {
                    list_id.push(vip.id);
                    table.row.add({
                        "0": i,
                        "1": vip.uid,
                        "2": vip.quantity,
                        "3": `${parseInt(vip.expired) > 1 ? '<label class="badge badge-success">đang chạy</label>' : '<label class="badge badge-warning">hết hạn</label>'}`,
                        "4": vip.start_day,
                        "5": vip.end_day,
                        "6": `<button type="button" onclick="showEditVip(${vip.id})" class="mb-2 btn btn-sm btn-warning mr-1"><i class="material-icons">edit</i></button><button onclick="deleteVip(${vip.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`
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

    function deleteVip(id) {
        if(confirm("Bạn có chắc muốn xóa?")) {
            
            $.ajax({
                url: "<?php echo base_url('VipMat/deleteTask'); ?>",
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

    function showEditVip(id) {
        $.getJSON(`VipLikeSetting/getTaskById?task_id=${id}`, (res) => {
            if (res.error) {
                Swal({
                    text: res.error.message,
                    type: 'error'
                });
            } else {
                $("#elike, #ewow, #elove, #ehaha, #esad, #eangry").prop("checked", false);
                $("#equantity_per_cron").val(res.data.quantity_per_cron);
                $("#eid").val(res.data.id);
                $reactions = Object.keys(JSON.parse(res.data.reactions));
                for (react of $reactions) {
                    $(`#e${react.toLocaleLowerCase()}`).prop("checked", true);
                }
                $('#myModal').modal('show');
            }
        }).fail(() => alert("Lỗi server, thử lại sau."));

    }

    function editVip(btn_id) {
        let id = $("#eid").val();
        let quantity_per_cron = $("#equantity_per_cron").val();
        let reactions = [];
        $('input[name="ereactions"]:checked').map((i, e) => {
            return reactions.push($(e).val());
        });
        $.ajax({
            url: "VipLikeSetting/updateTask",
            method: "POST",
            dataType: "json",
            data: {
                task_id: id,
                quantity_per_cron : quantity_per_cron,
                reactions: reactions
            },
            beforSend: () => loading(btn_id, "show", "Processing")
        }).done((res) => {
            if (res.error) {
                Swal({
                    text: res.error.message,
                    type: 'error'
                });
            } else {
                Swal({
                    text: 'Chỉnh sửa thành công.',
                    type: 'success'
                });
                $('#myModal').modal('hide');
            }
        }).fail(() => {
            Swal({
                text: "Server đang gặp lỗi vui lòng thử lại sau",
                type: 'error'
            });
        }).always(() => {
            loading(btn_id, "hide", "Xác nhận");
            
        });
    }


</script>