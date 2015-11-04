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
							<img imgedit src="http://placehold.it/500x200?text=img"/>
					</div>
				</div>

				<?php $this->load->view('dashboard_sidebar');?>

			</div>
		</div></div>
	</div>


<!-- Modal -->
<div class="modal fade" id="filemanager" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">File Manager</h4>
      </div>
      <div class="modal-body">
		<div class="hiddenfile">
			<input name="upload" type="file" id="fileinput"/>
		</div>
      	<div id="upload-result"></div>
      	<div class="upload-list"></div>
        <div class="files-icon">
        	<div class="icon" style="background: #ddd url(http://s3-ap-southeast-1.amazonaws.com/esdownloadcentre/stu_media/demo003/law-of-diffusion.png) center center no-repeat; background-size: 100%;" data-s3="http://s3-ap-southeast-1.amazonaws.com/esdownloadcentre/stu_media/demo003/law-of-diffusion.png"><p>filename</p></div>
        </div>
        <!-- 
        <div class="files-list">
        	<table class="table table-condensed table-bordered-table-striped">
        	<thead>
        		<tr><th>Filename</th><th>Action</th></tr>
        	</thead>
        	</table>
        </div> -->
      </div>
      <div class="modal-footer">
        <!-- <button type="button" class="btn btn-default">Upload</button> -->
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="fileupload">Upload</button>
      </div>
    </div>
  </div>
</div>

<style>

.files-icon{
	display: inline-block;
}

.hiddenfile {
 width: 0px;
 height: 0px;
 overflow: hidden;
}

.icon{
	display: inline-block;
	width: 100px;
	height: 130px;
	border-bottom: 1px #444 solid;
	text-align: center;
	cursor: pointer;
	vertical-align: middle;
	margin-right: 10px;
}

.icon p{
	margin-top: 110px;
	background: #444;
	color: #fff;
}

#edit_template{
	border: 1px #aaa dashed;
	padding: 15px;
	border-radius: 6px;
}

[textedit],[imgedit]{
	cursor: pointer;
}

#edit_buttons, #img_buttons{
	background: #00baff;
	font-size: 12px;
	float:right;
	color: #fff;
	padding-bottom: 1px;
	padding-top: 1px;
}

#edit_buttons i, #img_buttons i{
	padding: 5px;
	cursor: pointer;
	margin-top: -2px;
}

#edit_buttons i:hover, #img_buttons i:hover{
	background: #ff005a;
}
</style>
<?php $this->load->view('footer');?>
<script src="https://sdk.amazonaws.com/js/aws-sdk-2.1.12.min.js"></script>
<script>

var current_border = {};

$(document).ready(function(){

	// editor codes
	$('#filemanager').modal('show');

	$('#edit_template').hide();
	$('#select_template img.template').on('click', function(){
		var templat_id = $(this).attr('data-template-id');

		$('#select_template').fadeOut(function(){
			$('#edit_template').fadeIn();
			$('.alert').removeClass('alert-info').addClass('alert-success').html('Click to edit elements &rarr;');
		});
	});


	$('#edit_template').on('mouseenter', '[textedit],[imgedit]', function(){
		current_border = border_info($(this));
		
		hover_border($(this));
		// console.log($(this).html());
	});

	$('#edit_template').on('mouseleave', '[textedit],[imgedit]', function(){
		
		// console.log($(this).html());
		old_border($(this), current_border);
	});

	$('#edit_template').on('click', '[imgedit]', function(){
		clear_edit();

		var pos = $(this).position();
		var width = $(this).width();
		var height = $(this).height();

		// var imgupload = '<div class="imgeditor" style="width:'+width+'px; height: '+height+'px; background: rgba(255, 255, 255, 0.5); position: absolute; float: left; z-index: 150; top:'+pos['top']+'px; left:'+pos['left']+'px; text-align: center; "><span class="btn btn-default">upload / select</span></div>';

		// $(this).parent().append(imgupload);

		var buttons = '<span id="img_buttons" style="z-index: 150; position: absolute; top: '+(pos['top']+height+2)+'px; left: '+(pos['left']+width - 86)+'px;"><i class="glyphicon glyphicon-ok"></i><i class="glyphicon glyphicon-open" data-toggle="modal" data-target="#mediamanager"></i><i class="glyphicon glyphicon-duplicate"></i><i class="glyphicon glyphicon-trash"></span>';

		$(this).parent().append(buttons);


		$(this).removeAttr('imgedit');
		$(this).attr('currentedit','');
	});

	var ori_text = '';
	$('#edit_template').on('click', '[textedit]', function(){
		// tutup semua current edit yang lain
		clear_edit();

		ori_text = $(this).html();

		// tukar html ni dengan input kalau he
		var html = '';
		if($(this).is('p')){
			html = '<textarea style="width: 100%; height: 80px; box-sizing:border-box; border: 0px;">'+ori_text+'</textarea>';
		}else{
			html = '<input type="text" value="'+ori_text+'" style="width: 100%; box-sizing: border-box; border: 0px" />';
		}

		$(this).html(html);

		// add small button to save - delete - copy
		$(this).append('<span id="edit_buttons"><i class="glyphicon glyphicon-ok"></i><i class="glyphicon glyphicon-duplicate"></i><i class="glyphicon glyphicon-trash"></span>');

		$(this).removeAttr('textedit');
		$(this).attr('currentedit','');
	});

	$('#edit_template').on('click', '#edit_buttons i', function(){

		// kalau ok, save the string
		if($(this).hasClass('glyphicon-ok')){
			var new_text = '';
			
			var element = $(this).parent().parent();
			if($(element).is('p')){
				new_text = $(element).find('textarea').val();
			}else{
				new_text = $(element).find('input').val();
			}
			

			// $(this).parent().parent().html(new_text);
			
			$(element).html(new_text);
			$(element).removeAttr('currentedit');
			$(element).attr('textedit','');
			old_border(element, current_border);
		}

	});

	$('#edit_template').on('click', '#img_buttons i', function(){

		if($(this).hasClass('glyphicon-ok')){
			$('#img_buttons').remove();

			old_border( $('[currentedit]') , current_border);

			$('[currentedit]').attr('imgedit','').removeAttr('currentedit');
		}

	});


	// upload directly to s3
	AWS.config.region = 'ap-southeast-1'; // 1. Enter your region
	// AWS.config.region = 'ap-northeast-1'; // 1. Enter your region
    AWS.config.credentials = new AWS.CognitoIdentityCredentials({
    	// AccountId: '236627574121', // your AWS account ID
    	// RoleArn: 'arn:aws:s3:::mahuni',
        // IdentityPoolId: 'ap-northeast-1:bb902914-84b2-4a04-af6e-a9694ad80715' // 2. Enter your identity pool
    });
    AWS.config.update({accessKeyId: 'AKIAI7F7WLD4DW6FQMXQ', secretAccessKey: 'r1uORHL2f9UFzv1pPhBgvDjWiMhuyyrNw1iYsdMN'});
    AWS.config.credentials.get(function(err) {
        if (err) console.log(err);
        // console.log(AWS.config.credentials);
    });
    var bucketName = 'mahuni'; // Enter your bucket name
    var bucket = new AWS.S3({
        params: {
            Bucket: bucketName
        }
    });
    var fileChooser = document.getElementById('fileinput');
    var results = document.getElementById('upload-result');
    
    // button.addEventListener('click', function() {
    $('#fileupload').on('click', function(){
		$('#fileinput').focus().trigger('click');
	});

	$('#fileinput').on('change', function(){
        var file = fileChooser.files[0];

        // prepend siap untuk upload
        var s3path = 'https://s3-ap-southeast-1.amazonaws.com/mahuni/'+file.name.split(' ').join('+');

		var reader = new FileReader();

        // if (file) {
        //     results.innerHTML = '';
        //     var objKey = '' + file.name;
        //     var params = {
        //         Key: objKey,
        //         ContentType: file.type,
        //         Body: file,
        //         ACL: 'public-read'
        //     };
        //     bucket.putObject(params, function(err, data) {
        //         if (err) {
        //             results.innerHTML = 'ERROR: ' + err;
        //         } else {
        //         	console.log(data);
        //             // listObjs(); // this function will list all the files which has been uploaded
        //             //here you can also add your code to update your database(MySQL, firebase whatever you are using)
        //         }
        //     });
        // } else {
        //     results.innerHTML = 'Nothing to upload.';
        // }
     });
    // }, false);



    function listObjs() {
        var prefix = 'testing';
        bucket.listObjects({
            Prefix: prefix
        }, function(err, data) {
            if (err) {
                results.innerHTML = 'ERROR: ' + err;
            } else {
                var objKeys = "";
                data.Contents.forEach(function(obj) {
                    objKeys += obj.Key + "<br>";
                });
                results.innerHTML = objKeys;
            }
        });
    }


	// // upload files
	// $('#fileupload').on('click', function(){
	// 	$('#fileinput').focus().trigger('click');
	// });

	// $('#fileinput').on('change', function(){
		
	// 	var reader = new FileReader();
	//     reader.onload = function(){
	//       // var output = document.getElementById('output');
	//       // output.src = reader.result;
	//       // console.log(reader);
	//       var fileinput = document.getElementById('fileinput');
	//       var file = fileinput.files[0];
	// 	  // var filename = fileinput.files[0].name;

	// 	  // console.log(file);

	// 	  // check kat type, hanya image, pdf dan zip dengan saiz kurang daripada 10mb sahaja diterima untuk upload oi.

	//       var src = reader.result;
	//       var img = '<div class="icon" style="background: rgba(0,0,0,0.5) url('+src+') center center no-repeat; background-size: 100%;" data-s3=""><p>'+file.name+'</p></div>';

	//       $('.files-icon').prepend(img);

	//       // upload it
	//       var formData = new FormData();
	//       formData.append('image', file);

	//       // console.log(formData);

	//       // $.ajax({
	//       // 			url:base_url('page/upload_file'),
	//       // 			data:formData,
	//       // 			contentType: false,
	//       // 			processData: false,
	//       // 			success: function(response){
	//       // 				console.log(response);
	//       // 			},
	//       // 		});

	// 		var oReq = new XMLHttpRequest();
	// 		oReq.open("POST", base_url('page/upload_file'), true);
	// 		oReq.onload = function(oEvent) {
	// 			if (oReq.status == 200) {
	// 			  console.log("Uploaded!");
	// 			  console.log(oReq.response);
	// 			} else {
	// 			  console.log("Error " + oReq.status + " occurred when trying to upload your file.<br \/>");
	// 			}
	// 		};
			
	// 		oReq.onprogress = function(oEvent){
	// 			console.log(oReq);
	// 		}

	// 		oReq.send(formData);
	// 		ev.preventDefault();
	//     };
	    
	//     reader.readAsDataURL(event.target.files[0]);
	//     console.log(reader.result);
	// });

	//// end editor codes

});


function base_url(uri){
	return 'http://mahuni.dev/'+uri;
}

function clear_edit(){
	// console.log(current_border);
		that = $('[currentedit]');

		if($(that).is('img')){
			console.log('clear for img');
			$('#img_buttons').remove();

			old_border( that , current_border);

			$(that).attr('imgedit','').removeAttr('currentedit');

		}else if($(that).is('p')){
			console.log('clear for p');
			// just save the latest text. much easier as we
			// can discard a 'memory' type of function
			var latest = $('[currentedit] textarea').val();

			$(that).html(latest);

			$(that).attr('textedit','').removeAttr('currentedit');

			old_border($(that), current_border);
		}else{

			console.log('clear for others');
			// console.log($('[currentedit]'));

			var latest = $('[currentedit] input').val();

			$(that).html(latest);

			$(that).attr('textedit','').removeAttr('currentedit');

			old_border($(that), current_border);
		}
}

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