<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-12 content">
			<div class="row">
				<div class="col-sm-9">
					<h1>
						<?= $title; ?>
						<?php
							if(ISSET($new)) echo $new;
						?>
					</h1>
					<?php shout(); ?>

					<?php
						rbt_make_form($inputs, $defaults);
					?>

				</div>

				<?php $this->load->view('dashboard_sidebar');?>

			</div>
		</div></div>
	</div>
<?php $this->load->view('footer');?>