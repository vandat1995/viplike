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

<script>
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
    });

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