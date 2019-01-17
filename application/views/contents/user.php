<div class="row">
    <div class="col-lg-6 mb-6">
        <div class="card card-small mb-4">
            <div class="card-header border-bottom">
                <h6 class="m-0">Create New user</h6>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item p-3">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="feFirstName">Username <span class="text-danger">*</span></label>
                            <input type="text" id="username" class="form-control"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label for="feLastName">Password<span class="text-danger">*</span></label>
                            <input type="password" id="password" class="form-control" placeholder=""> 
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="feFirstName">Permissions<span class="text-danger">*</span></label>
                            <input type="number" id="time" class="form-control" placeholder="30" min="1"> 
                        </div>
                        <div class="form-group col-md-6">
                            <label for="feFirstName">Balance <span class="text-danger">*</span></label>
                            <input type="number" id="balance" class="form-control" placeholder="" min="0"> 
                        </div>
                    </div>
                    
                    <button type="button" id="btn_submit" class="btn btn-accent">Submit</button>
                </li>
            </ul>
        </div>
    </div>
</div>