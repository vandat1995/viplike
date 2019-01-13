<div class="row">
    <div class="col-lg-12 mb-6">
        <div class="card card-small overflow-hidden mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Import</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <!-- <label for="feDescription">List Token</label> -->
                            <textarea placeholder="EAAAA..." id="list_token" class="form-control" rows="5" place></textarea>
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
				<h6 class="m-0">List Token</h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">id</th>
							<th scope="col" class="border-bottom-0">token</th>
							<th scope="col" class="border-bottom-0">gender</th>
							<th scope="col" class="border-bottom-0">status</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
    $(document).ready(() => {
        $("#btn_import").on("click", async () => {
            $("#btn_import").text("Processing...").prop("disabled", true);
            let list_token = ($("#list_token").val().trim() && $("#list_token").val().trim().split("\n")) || false;
            if(!list_token) {
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
            for(let token of list_token) {
                try {
                    let data = await checkLive(token);
                    saveDb(token, data);
                    live++;

                } catch(e) {
                    die++;
                }
            }
            $("#btn_import").text("Submit").prop("disabled", false);
            Swal({
                text: `Live: ${live}, Die: ${die}`,
                type: 'success',
            });
            
        });
    });

    function checkLive(token) {
        return new Promise((resolve, reject) => {
    		$.getJSON(`https://graph.facebook.com/v3.2/me?fields=id%2Cname%2Cpicture%7Burl%7D%2Cgender&access_token=${token}`, res => resolve(res))
            .fail(() => reject('Token die'));
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


</script>