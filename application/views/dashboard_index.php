<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-12 content">
			<div class="row">
				<div class="col-sm-9">
					<h1>Dashboard</h1>
					<?php shout(); ?>

					<div class="alert alert-info">
						Selamat datang ke Mahuni.com, cara mudah dan berkesan menggunakan FB Ads!
					</div>
				</div>

				<?php $this->load->view('dashboard_sidebar');?>

			</div>
		</div></div>
	</div>
<?php $this->load->view('footer');?>