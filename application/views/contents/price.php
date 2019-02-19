<div class="row">
	<div class="col">
		<div class="card card-small overflow-hidden mb-4">
			<div class="card-header">
				<h6 class="m-0">Bảng giá VIP Cảm Xúc 
                </h6>
			</div>
			<div class="card-body p-0 pb-3 text-center">
				<table class="table mb-0">
					<thead class="bg-light">
						<tr>
							<th scope="col" class="border-bottom-0">#</th>
                            <th scope="col" class="border-bottom-0">Số lượng cảm xúc</th>
                            <th scope="col" class="border-bottom-0">Giá / 1 tháng</th>
						</tr>
					</thead>
					<tbody id="result">
                    <?php
                        $i = 1;
                        if($prices){
							foreach($prices as $val) {
								echo '<tr>';
								echo '<td>'. $i .'</td>';
								echo '<td>'. $val->quantity .'</td>';
								echo '<td>'. number_format($val->price_per_month) .' đ</td>';
								echo '</tr>';
								$i++;
							}
						}
                    ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>