<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-4 col-sm-offset-4 content">
			<h1>Login</h1>
			<?php 
				shout();
			?>
			<?php rbt_make_form($inputs);?>
			<!-- <a href="<?= site_url('umum/register')?>">&laquo; register</a> -->
		</div></div>
	</div>
<?php $this->load->view('footer');?>