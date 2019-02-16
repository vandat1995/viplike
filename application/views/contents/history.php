<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
                    <thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0"></th>
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
        table = $(".table").DataTable({
            dom: '<"datatable-header"><"datatable-scroll"t>ip<"datatable-footer">',
            columnDefs: [{
                className: "text-left",
                targets: "_all"
            }],
            lengthMenu: [
                [ 50, 100, 200, -1 ],
                [ '50 rows', '100 rows', '200 rows', 'Show all' ]
            ],
            paging: true,
            select: true,
            pageLength: 50,
            ordering: false,
            responsive: true
        });
        loadHistory();

    });

    function loadHistory() {
        table.clear().draw();
        $.ajax({
            url: "History/list",
            type: "GET",
            dataType: "json"
        }).done((res) => {
            if(res.data) {
                for(let task of res.data) {
                    table.row.add({
                        "0": `[${task.created_at}] ${task.action == "-" ? `<label class="badge badge-danger">${task.action}${task.amount}</label>` : `<label class="badge badge-success">${task.action}${task.amount}</label>`} ${task.detail}`
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
</script>