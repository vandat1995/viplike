<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Tạo mới giá vip cảm xúc</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Số lượng cảm xúc <span class="text-danger">*</span></label>
                            <input type="number" id="quantity" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label>Giá / 1 tháng <span class="text-danger">*</span></label>
                            <input type="number" id="price" class="form-control" placeholder=""> 
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
                <h6 class="m-0">Cài đặt MAX UID VIP LIKE</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>Số lượng UID VIP tối đa <span class="text-danger">*</span></label>
                            <input type="number" id="max_uid_vip" class="form-control"> 
                        </div>

                        <div class="form-group col-md-6">
                            <label>On/Off thông báo bảo trì <span class="text-danger">*</span></label>
                            <select class="form-control" id="maintanceMode">
                                <option value="1">On</option>
                                <option value="0">Off</option>
                            </select>
                        </div>
                    </div>
                                        
                    <button type="button" id="btn_submitMaxUid" class="btn btn-accent">Cài đặt</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">Danh sách bảng giá 
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">Số lượng cảm xúc</th>
                            <th scope="col" class="border-bottom-0">Giá / 1 tháng</th>
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
    $(() => {
        $("#btn_submit").on("click", () => {
            createUser();
        });

        $("#btn_submitMaxUid").on("click", () => {
            setMaxUid();
        });
        loadListPrice();
        loadMaxUid();
    });

    function loadMaxUid() {
        $.getJSON('Setting/loadMaxUidVip', (res) => {
            
            $("#max_uid_vip").val(res.data.maxUid);
            $("#maintanceMode").val(res.data.maintanceMode)
        });
    }

    function setMaxUid() {
        $.ajax({
            method: 'POST',
            dataType: 'json',
            url: 'Setting/setMaxUidVip',
            data: {
                maxUid : $("#max_uid_vip").val(),
                maintanceMode: $("#maintanceMode").val()
            }
        }).done(res => {
            if (res.error) {
                Swal({
                    html: res.error.message,
                    type: 'error'
                });
            } else {
                Swal({
                    html: 'Thiết lập thành công.',
                    type: 'success'
                });
            }
        }).fail(() => {
            Swal({
                html: `Không thể kết nối tới server`,
                type: 'error'
            });
        });
    }

    function createUser() {
        let quantity = $("#quantity").val().trim();
        let price    = $("#price").val().trim();
        if( !quantity || !price || price < 1) {
            Swal({
                text: `Invalid data`,
                type: 'error'
            });
            return;
        }
        $.ajax({
            url: "Setting/create",
            type: "POST",
            dataType: "json",
            data: {
                quantity: quantity,
                price: price
            },
            beforeSend: () => loading("btn_submit", "show", "Processing")
        }).done((res) => {
            if(res.error) {
                Swal({
                    html: `${res.error.message}`,
                    type: 'error'
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
                type: 'error'
            });
        }).always(() => {
            loadListPrice();
            loading("btn_submit", "hide", "Submit");
        });
    }

    function loadListPrice() {
        $("#result").empty();
        $.getJSON("Setting/list", (res) => {
            if( res.data ) {
                let i = 1;
                for(let data of res.data) {
                    $("#result").append($("<tr>")
                        .append($("<td>").html(i))
                        .append($("<td>").html(data.quantity))
                        .append($("<td>").html(new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(data.price_per_month)))
                        .append($("<td>").html(data.created_at))
                        .append($("<td>").html(`<button onclick="deletePrice(${data.id})" type="button" class="mb-2 btn btn-sm btn-danger mr-1"><i class="material-icons">delete</i></button>`))
                    );
                    i++;
                }
            }
        });
    }

    function deletePrice(id) {
        if( confirm("Are you sure?") ) {
            $.ajax({
                url: "Setting/delete",
                type: "POST",
                dataType: "json",
                data: {
                    id: id
                }
            }).done((res) => {
                if(res.error) {
                    Swal({
                        html: `${res.error.message}`,
                        type: 'error'
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
                    type: 'error'
                });
            }).always(() => {
                loadListPrice(); 
            });
        }
    }

</script>