<?php $this->load->view('header');?>
	<div class="container">
		<div class="row"><div class="col-sm-12 content">
			<div class="row">
				<div class="col-sm-9">
					<h1>Create Page</h1>
					<?php 
						shout(); 
					?>

					<div class="alert alert-info">
						Klik untuk Pilih template yang ingin digunakan &rarr;
					</div>

					<div id="select_template">
						<div class="row">
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=magnet1" data-template-id="leadmagnet1" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=magnet2" data-template-id="leadmagnet2" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=magnet3" data-template-id="leadmagnet3" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=magnet4" data-template-id="leadmagnet4" class="template img-thumbnail"></div>
						</div>
						<br/>
						<div class="row">
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=page1" data-template-id="landingpage1" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=page2" data-template-id="landingpage2" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=page3" data-template-id="landingpage3" class="template img-thumbnail"></div>
							<div class="col-sm-3"><img src="http://placehold.it/200x200?text=page4" data-template-id="landingpage4" class="template img-thumbnail"></div>
						</div>
					</div>

					<div id="edit_template">
							<h1 textedit>Ini adalah tajuk Sekadar Ada</h1>
							<p textedit>Ini pula cuma details pendek pun sekadar ada</p>
					</div>
				</div>

				<?php $this->load->view('dashboard_sidebar');?>

			</div>
		</div></div>
	</div>

<style>
#edit_template{
	border: 1px #aaa dashed;
	padding: 15px;
	border-radius: 6px;
}

[textedit]{
	cursor: pointer;
}
</style>
<?php $this->load->view('footer');?>

<script>

$(document).ready(function(){
	$('#edit_template').hide();
	$('#select_template img.template').on('click', function(){
		var templat_id = $(this).attr('data-template-id');

		$('#select_template').fadeOut(function(){
			$('#edit_template').fadeIn();
			$('.alert').removeClass('alert-info').addClass('alert-success').html('Click to edit elements &rarr;');
		});
	});

	var current_border = {};

	$('#edit_template').on('mouseenter', '[textedit]', function(){
		current_border = border_info($(this));
		
		hover_border($(this));
		// console.log($(this).html());
	});

	$('#edit_template').on('mouseleave', '[textedit]', function(){
		
		// console.log($(this).html());
		old_border($(this), current_border);
	});

	var ori_text = '';
	$('#edit_template').on('click', '[textedit]', function(){
		ori_text = $(this).html();

		
	});
});


function hover_border(node){

	var width = '1px';
	var color = '#00baff';
	var style = 'solid';

	// $(node).css('padding', '15px');
	// $(node).css('margin', '-15px');

	$(node).css('border-top-width', width);
	$(node).css('border-top-color', color);
	$(node).css('border-top-style', style);
	$(node).css('border-right-width', width);
	$(node).css('border-right-color', color);
	$(node).css('border-right-style', style);
	$(node).css('border-bottom-width', width);
	$(node).css('border-bottom-color', color);
	$(node).css('border-bottom-style', style);
	$(node).css('border-left-width', width);
	$(node).css('border-left-color', color);
	$(node).css('border-left-style', style);
}

function old_border(node, ori){
	$(node).css('border-top-width', ori['top-width']);
	$(node).css('border-top-color', ori['top-color']);
	$(node).css('border-top-style', ori['top-style']);
	$(node).css('border-right-width', ori['right-width']);
	$(node).css('border-right-color', ori['right-color']);
	$(node).css('border-right-style', ori['right-style']);
	$(node).css('border-bottom-width', ori['bottom-width']);
	$(node).css('border-bottom-color', ori['bottom-color']);
	$(node).css('border-bottom-style', ori['bottom-style']);
	$(node).css('border-left-width', ori['left-width']);
	$(node).css('border-left-color', ori['left-color']);
	$(node).css('border-left-style', ori['left-style']);
}

function border_info(node){
	
	var border = {};

	border['top-width'] = $(node).css('border-top-width');
	border['top-color'] = $(node).css('border-top-color');
	border['top-style'] = $(node).css('border-top-style');
	border['right-width'] = $(node).css('border-right-width');
	border['right-color'] = $(node).css('border-right-color');
	border['right-style'] = $(node).css('border-right-style');
	border['bottom-width'] = $(node).css('border-bottom-width');
	border['bottom-color'] = $(node).css('border-bottom-color');
	border['bottom-style'] = $(node).css('border-bottom-style');
	border['left-width'] = $(node).css('border-left-width');
	border['left-color'] = $(node).css('border-left-color');
	border['left-style'] = $(node).css('border-left-style');
	// border.top.color = $(node).css('border-top-color');
	// border.top.style = $(node).css('border-top-style');

	return border;
}

</script>