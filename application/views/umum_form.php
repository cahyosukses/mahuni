<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-4 col-sm-offset-4 content">
			
			<?php 
				if(isset($title)) echo '<h1>'.$title.'</h1>';
				shout();
			?>
			<?php rbt_make_form($inputs);?>
		</div></div>
	</div>
<?php $this->load->view('footer');?>