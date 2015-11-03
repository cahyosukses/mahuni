<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-12 content">
			<div class="row">
				<div class="col-sm-9">
					<h1>
						<?= $title; ?>

						<?php
							if(ISSET($new)) echo $new;
							if(ISSET($top_button)) echo $top_button;
						?>
					</h1>
					<?php shout(); ?>

					<?php
						if(count($rows) === 0){
							echo '<p class="alert alert-warning">Tiada data daripada sistem.</p>';
						}else{
							echo '<table class="table table-bordered table-condensed table-striped">';
							echo '<thead><tr>';
							foreach($rows[0] as $head=>$value){
								echo '<th>'.$head.'</th>';
							}
							echo '</tr></thead>';
							echo '<tbody>';
							foreach($rows as $row){
								echo '<tr>';
								foreach($row as $value){
									echo '<td>'.$value.'</td>';
								}
								echo '</tr>';
							}
							echo '</tbody>';
							echo '</table>';
						}
					?>

				</div>

				<?php $this->load->view('dashboard_sidebar');?>

			</div>
		</div></div>
	</div>
<?php $this->load->view('footer');?>