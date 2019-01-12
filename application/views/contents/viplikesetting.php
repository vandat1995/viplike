<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-6">
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
                        
                    </div>
                    <button type="button" id="btn_submit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(document).ready(() => {
        $("#btn_submit").on("click", () => {
            main();
        });
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
            url: "<?php echo base_url('VipLikeSetting/addTask'); ?>",
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
        }).always(() => $("#btn_submit").text("Submit").prop("disabled", false));
    }


</script>