<?php
	function sms($to, $text, $from = false){

		if($to[0] !== '6') $to = '6'.$to;

		$param['api_key'] = '7e618bae';
		$param['api_secret'] = '6d38d19b';
		$param['to'] = $to;
		$param['text'] = $text;
		if(!$from) $from = '601117225383'; // yezza
		$param['from'] = $from;

		$url = 'https://rest.nexmo.com/sms/json?' . http_build_query($param);
		// dumper($to);
		$res = file_get_contents($url);

		// save log
		$log['from'] = $from;
		$log['to'] = $to;
		$log['text'] = $text;
		$log['response'] = $res;

		$CI =& get_instance();

		$CI->db->insert('sms_log', $log);

		return $res;
	}

	function pushover($title, $message){
		$CI =& get_instance();

		$CI->load->library('Curl');

		$CI->curl->create('https://api.pushover.net/1/messages.json');
		
		$param['token'] = 'aGtqpVv8824rdT7SPB3bF8yqqf7jxm';
		$param['user'] = 'uywuod89NCP7wyrNk9J1XiKxaK7iW2';
		$param['device'] = 'iphone5';
		$param['title'] = $title;
		$param['message'] = $message;
		// dumper($param);
		
		$CI->curl->post($param);

		$CI->curl->execute();
		// token: aGtqpVv8824rdT7SPB3bF8yqqf7jxm
		// user key: uywuod89NCP7wyrNk9J1XiKxaK7iW2
		// device_name: iphone5
	}

	function redirect_back(){
		$prev = $_SERVER['HTTP_REFERER'];

		if($prev == '') $prev = base_url();

		redirect($prev);
	}

	function active($uri){
		if(strpos(uri_string(), $uri) !== FALSE) echo 'active';
	}

	function verify_access(){
		if(!is_logged_in()){
			toshout_error('Please login to use that property');
			redirect('login');
		}
	}

	function verify_group($group){
		if(!is_group($group)){
			toshout_error('[TOP SECRET] You cannot access that property');
			redirect('dashboard');
		}
	}

	function is_logged_in(){
		$CI =&get_instance();
		$user = $CI->session->userdata('user');
		if($user === FALSE || $user == NULL) return false;
		else return true;
	}

	function is_group($group_name){

		$CI =& get_instance();
		$group = $CI->session->userdata('group');

		$ret = FALSE;
		if(array_search($group_name, $group) !== FALSE) $ret = TRUE;
		return $ret;
	}

	function is_local(){
		return !is_live();
	}

	function is_live(){
		
		// dumper(base_url());
		return (strpos(base_url(), '.com') !== FALSE OR strpos(base_url(), '.net') !== FALSE);
	}
	
	function toshout_success($text){
		toshout(array($text=>'success'));
	}

	function toshout_error($text){
		toshout(array($text=>'danger'));
	}


	function rrmdir($dir) { 
		if (!file_exists($dir)) return true; 
	    if (!is_dir($dir) || is_link($dir)) return unlink($dir); 
        foreach (scandir($dir) as $item) { 
            if ($item == '.' || $item == '..') continue; 
            if (!rrmdir($dir . "/" . $item)) { 
                chmod($dir . "/" . $item, 0777); 
                if (!rrmdir($dir . "/" . $item)) return false; 
            }; 
        } 
        return rmdir($dir); 
    }

	function redirect_js($url){
		//check for . to determine site_url or not
		if(strpos($url, '.')!==false){
		
			if (IS_SSL) {		
				$url = str_replace('https://', '', $url);
				$url = 'https://'.$url;
			} else {
				$url = str_replace('http://', '', $url);
				$url = 'http://'.$url;
			}

		}else{
			$url = site_url($url);
		}

		echo '<script type="text/javascript">';
		echo 'window.location = "'.$url.'"';
		echo '</script>';
	}

	function set_redirect($flag){
		$CI =& get_instance();
		if($flag == 'to_here'){
			$CI->session->set_userdata('goto', $CI->uri->uri_string());
		}
	}

	function redirector($else){
		$CI =& get_instance();
		if($CI->session->userdata('goto')){
			$goto = $CI->session->userdata('goto');
			$CI->session->unset_userdata('goto');

			redirect($CI->session->userdata('goto'));

		}else{
			redirect($else);
		}
	}
	
	function zip($source, $destination)
	{
	    if (!extension_loaded('zip') || !file_exists($source)) {
	        return false;
	    }

	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        return false;
	    }

	    $source = str_replace('\\', '/', realpath($source));

	    if (is_dir($source) === true)
	    {
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

	        foreach ($files as $file)
	        {
	            $file = str_replace('\\', '/', $file);

	            // Ignore "." and ".." folders
	            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
	                continue;

	            $file = realpath($file);

	            if (is_dir($file) === true)
	            {
	                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	            }
	            else if (is_file($file) === true)
	            {
	                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
	            }
	        }
	    }
	    else if (is_file($source) === true)
	    {
	        $zip->addFromString(basename($source), file_get_contents($source));
	    }

	    return $zip->close();
	}
	function unzip($file_path, $to_dir){
		$zipArchive = new ZipArchive();
		$result = $zipArchive->open($file_path);
		if ($result === TRUE) {
		    $zipArchive ->extractTo($to_dir);
		    $zipArchive ->close();
		    // Do something else on success
		    return TRUE;
		} else {
		    // Do something on error
		    return FALSE;
		}
	}
	
	function rbt_redirect_back(){
		$CI =&get_instance();
		//dumper($_SERVER['HTTP_REFERER']);
		if($CI->input->server('HTTP_REFERER')) $url = $CI->input->server('HTTP_REFERER');
		else $url = site_url('main/dashboard');

		redirect($url);
	}

	function rbt_maketable($tablename,$inputs){
		$CI=&get_instance();
		$CI->load->dbforge();

		//check if tablename can be used;
		if($CI->db->table_exists($tablename) == FALSE){

			
			foreach($inputs as $name=>$input){
				if($input['type'] == 'text' OR $input['type'] == 'select' OR $input['type'] == 'radio'){
						$fields[$name] = array('type'=>'VARCHAR','constraint'=>'255');
				}elseif($input['type'] == 'textarea'){
						$fields[$name] = array('type'=>'TEXT');
				}elseif($input['type'] == 'id'){
						$fields[$name] = array('type'=>'INT', 'constraint'=>'11', 'auto_increment'=>TRUE, 'unsigned'=>TRUE);
				}elseif($input['type'] == 'integer'){
						$fields[$name] = array('type'=>'INT', 'constraint'=>'11');
				}

			}
			
			//$fields['timestamp'] = array('type'=>'TIMESTAMP','default'=>CURRENT_TIMESTAMP);

			if(array_key_exists('id', $fields) === FALSE) $CI->dbforge->add_field('id');
			$CI->dbforge->add_field($fields);
			$CI->dbforge->add_field('`changetime` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL');
			//add timestamps
			$CI->dbforge->add_key('id');
			$CI->dbforge->create_table($tablename);

			return TRUE;

		}else{
			toshout(array('Tablename "'.$tablename.'" exists. Do use another tablename'=>'error'));
			return FALSE;
		}

	}
	
	function rbt_move_upload($inputs){
		//dumper($_FILES);
		foreach($inputs as $name=>$input){
			if($input['type'] == 'upload' && $_FILES[$name]["error"] == FALSE){
		
		    	$uploads_dir = $input['folder'];
		        $tmp_name = $_FILES[$name]["tmp_name"];
		        $name = $_FILES[$name]["name"];

		        /*
		        dumper($uploads_dir);
		        dumper($tmp_name);
		        dumper($name);
		        */
		        move_uploaded_file($tmp_name, "./$uploads_dir/$name");	
			}
		}
	}

	function rbt_valid_post($inputs){

		
		/***********************************
		Self-Notes: 
		Next thing to do here ->
		Need to make sure if only upload form
		is there, the validation still can be
		run as usual. Right now need to make
		workaroud by adding hidden input form
		with nonsense value.
		***********************************/

		$CI =&get_instance();
		$CI->load->library('form_validation');

		$input_rules = false;
		//only on post
		if($CI->input->post()){
			foreach($inputs as $name=>$input){
				//prep and test for normal input fields
				if(array_key_exists('rules', $input) && $input['type']!='upload' && $input['rules'] !== ''){ 
					$CI->form_validation->set_rules($name,$input['label'],$input['rules']);
					$input_rules = true;
				}

				//prepare and test for uploads data from upload fields
				if($input['type'] == 'upload'){
					$uploads[$name] = $input;
				}
			}


			///Validate all form inputs data except uploads form
			if($input_rules){ // if there is validation needed, test for validation
				if($CI->form_validation->run() != FALSE){
					$ret_form = TRUE;
				}else{
					$ret_form = FALSE;
				}
			}else{// if no validation, return true;
				$ret_form = TRUE;
			}

			$ret_upload = TRUE;
			//check validation for uploads
			if(count($_FILES)>0){

				$CI->load->library('upload');

				foreach($uploads as $name=>$upload){
					// $upload_config = array();
					$upload_error = '';
					if($_FILES[$name]['size'] > 0){
						//create rules
						$rules_raw = explode('|',($upload['rules']));
						foreach ($rules_raw as $value) {
							$exp = explode(':',$value);
							if(count($exp) == 1) $to_check[$exp[0]] = TRUE;
							else{
								$upload_config[$exp[0]] = str_replace(',', '|', $exp[1]);
							}
						}
						// dumper($name);
						// dumper($upload_config);
						$CI->upload->initialize($upload_config);
						if(!$CI->upload->do_upload($name)){ // if error
							$upload_error = $CI->upload->display_errors('<span>','</span>');
							$ret_upload = FALSE;
							// dumper($upload_error);
							// dumper($upload_config);
							// dumper($CI->upload->data());
							toshout(array('There is an error for file '.$inputs[$name]['label'].'. Please check your file.' =>'danger'));
						}else{//if success
							//set $_POST to filename
							$data = $CI->upload->data();

							$_POST[$name] = $data['file_name'];

							// dumper($CI->input->post());
						}
					}
				}
			}

			if(count($_FILES)>0) $ret = ($ret_form AND $ret_upload);
			else $ret = $ret_form;
			return $ret;
			// dumper($ret);
		}
	}


	////support for older SACL rbt_makeform
	function rbt_makeform($inputs, $default = array(), $clear_form = false){
		//rbt_open_form();
		rbt_make_form($inputs, $default,$clear_form);
		//rbt_close_form();
	}


	function rbt_make_form($inputs, $default = array(), $clear_form = false){
		rbt_open_form();
		rbt_make_inputs_dev($inputs, $default,$clear_form);

		rbt_close_form();
	}

	function rbt_make_inputs_dev($inputs, $default = array(), $settings = array()){
		shout();

		// echo validation_errors('<div class="alert alert-danger">','</div>');

		foreach($inputs as $name=>$input){
			$form_error = form_error($name, '<span>','</span>');
			$is_error = ($form_error != '');

			// if(array_key_exists($name, $default) === FALSE) $default[$name] = '';

			$set_value = set_value($name);

			// dumper($form_error);
			// prep values
			$default_value = '';
			if(array_key_exists($name, $default)) $default_value = $default[$name];
			if(array_key_exists('id', $input) == FALSE) $input['id'] = '';
			if(array_key_exists('class', $input) == FALSE) $input['class'] = 'form-control';
			if(array_key_exists('label', $input) == FALSE AND array_key_exists('display', $input) !== FALSE ) $input['label'] = $input['display'];
			if(array_key_exists($name, $default)) $default_value = $default[$name];

			// if(array_key_exists('button-block', $settings) !== FALSE) $input['class'] = '';
			if($set_value == "" AND $default_value != "") $set_value = $default_value;
			// generate attributes for form_input
			$input['attr']['id'] = $input['id'];
			$input['attr']['class'] = $input['class'];

			// if($is_error) $input['attr']['class'] = $input['class'].' '

			$attrs = array();
			$attribute = '';
			foreach($input['attr'] as $attr_name=>$value){
				$attrs[] = $attr_name.'="'.$value.'"';
			}

			if(count($attrs) > 0) $attribute = implode(' ', $attrs);

			// generate form function. We are using codeigniter Form helper to generate the html tag for each inputs except for download, datepicker and color

			$form_function = 'form_'.$input['type'];

			

			// generate open and end input paragraph. Put alert if error
			if($input['type'] !== 'hidden'){
				echo '<p class="form-group';
				if($is_error) echo ' has-error';
				echo '">';
				
				echo '<label class="control-label">';
				if($is_error) echo $form_error;
				else echo ucwords($input['label']);
				echo '</label>';
			}
			// if($is_error) echo '<div class="alert alert-danger">'.$form_error; // show error if exists

			// generate input based on type
			if($input['type'] == 'input' OR $input['type'] == 'textarea' OR $input['type'] == 'password' OR $input['type'] == 'hidden'){
				// echo $form_function;
				// echo form_error($name);
				echo $form_function($name, $set_value, $attribute);
			}elseif($input['type'] == 'radio'){
				foreach($input['options'] as $value=>$show){
					echo ': &nbsp; '.$form_function($name, $value, $set_value).' '.$show;
				}
			}elseif($input['type'] == 'dropdown'){
				echo $form_function($name, $input['options'], $set_value, $attribute);
			}elseif($input['type'] == 'upload'){
				// echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				// if(!$clear_form){
					//get file path
					$exp = explode('|', $input['rules']);
					foreach($exp as $ex){
						if(strpos($ex, 'path') !== FALSE){
							$path = str_replace('upload_path:./', '', $ex);
							$path = trim($path,'/').'/'.$default_value;
							$src = base_url($path);

							// echo '<img class="upload_default '.$name.'" src="'.$src.'" style="max-height: 80px"/><br/>';
						}
					}
				// }
				echo '<input type="file" name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';

			}elseif($input['type'] == 'datepicker'){

			}elseif($input['type'] == 'color'){

			}

			// if($is_error) echo '</div>';
			// close input paragraph
			if($input['type'] !== 'hidden') echo '</p>';
		}
	}

	function rbt_make_inputs($inputs, $default = array(), $block = false){
		shout_dev();
		// echo validation_errors('<div class="alert alert-error">','</div>');
		$error_before = '<div class="alert alert-danger">';
		$error_after = '</div>';

		//$CI=&get_instance();
		//$data = $CI->input->post();
		$datepicker = FALSE;
		$color = FALSE;
		foreach($inputs as $name=>$input){
			$default_value = '';
			if(array_key_exists($name, $default)) $default_value = $default[$name];
			if(array_key_exists('id', $input) == FALSE) $input['id'] = '';
			if(array_key_exists('class', $input) == FALSE) $input['class'] = 'form-control';
			if(array_key_exists($name, $default)) $default_value = $default[$name];

			if($input['type'] == 'text'){
				
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				echo '<input type="text" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].'" placeholder="'.$input['display'].'"/></p>';
				// if($clear_form) echo '<input type="text" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].'" placeholder="'.$input['display'].'/></p>';
			}
			if($input['type'] == 'color'){
				
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				echo '<input type="color" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
				// if($clear_form) echo '<input type="color" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';

			}
			if($input['type'] == 'datetime' || $input['type'] == 'date'){
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				echo '<input type="text" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].' datepicker"/></p>';
				// if($clear_form) echo '<input type="text" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].' datepicker"/></p>';
			}	
			if($input['type'] == 'password'){
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				echo '<input type="password" name="'.$name.'" value="'.set_value($name, $default_value).'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
				// if($clear_form) echo '<input type="password" name="'.$name.'" value="" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			if($input['type'] == 'textarea'){
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				echo '<textarea name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'">'.set_value($name, $default_value).'</textarea></p>';
				// if($clear_form) echo '<textarea name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'"></textarea></p>';
			}
			if($input['type'] == 'upload'){
				echo '<p><label>'.$input['display'].'</label><br/>'.form_error($name, $error_before, $error_after);
				if(!$clear_form){
					//get file path
					$exp = explode('|', $input['rules']);
					foreach($exp as $ex){
						if(strpos($ex, 'path') !== FALSE){
							$path = str_replace('upload_path:./', '', $ex);
							$path = trim($path,'/').'/'.$default_value;
							$src = base_url($path);

							echo '<img class="upload_default '.$name.'" src="'.$src.'" style="max-height: 80px"/><br/>';
						}
					}
				}
				echo '<input type="file" name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			if($input['type'] == 'hidden'){
				//echo '<p>'.$input['display'].'<br/>';
				echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'" id="'.$input['id'].'" class="'.$input['class'].'"/></p>';
			}
			if($input['type'] == 'radio'){
				echo '<p><label>'.$input['display'].':</label> ';

				foreach($input['options'] as $disp=>$value){
					$checked = '';
					if($value == $default_value && !$clear_form) $checked = 'checked="checked"';
					echo '<input type="radio" name="'.$name.'" value="'.$value.'" id="'.$input['id'].'" class="'.$input['class'].'" '.set_radio($name, $value).' '.$checked.'> '.ucfirst($disp).' &nbsp;';
				}
				echo '</p>'.form_error($name, $error_before, $error_after);
				//echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'"/></p>';
			}
			if($input['type'] == 'checkbox'){
				echo '<p><label>'.$input['display'].': </label>';

				foreach($input['options'] as $disp=>$value){
					$checked = '';
					if($value == $default_value && !$clear_form) $checked = 'checked="checked"';
					echo '<input type="checkbox" name="'.$name.'" value="'.$value.'" id="'.$input['id'].'" class="'.$input['class'].'" '.set_checkbox($name, $value).' '.$checked.'> '.ucfirst($disp).' &nbsp;';
				}
				echo '</p>'.form_error($name, $error_before, $error_after);
				//echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'"/></p>';
			}
			if($input['type'] == 'select'){
				echo '<p><label>'.$input['display'].': </label>';
				echo '<select name="'.$name.'" id="'.$input['id'].'" class="'.$input['class'].'">';
				foreach($input['options'] as $disp=>$value){
					echo '<option value="'.$value.'"';
					if(!$clear_form && $value == $default_value) echo 'selected="selected"'; 
					echo '> '.ucfirst($disp).'</option>';
				}
				echo '</select></p>'.form_error($name, $error_before, $error_after);
				//echo '<input type="hidden" name="'.$name.'" value="'.$input['value'].'"/></p>';
			}
		

			if($input['id'] == 'datepicker' && $datepicker == FALSE) $datepicker = TRUE;
			if($input['id'] == 'color' && $color == FALSE) $color = TRUE;
		}

		if($datepicker){
			echo '<style type="text/css">@import url('.base_url('assets/css/smoothness/jquery-ui-1.8.10.custom.css').')</style>';
			echo '<script src="'.base_url('assets/js/jquery-ui-1.8.10.custom.min.js').'"></script>';
			echo '<script type="text/javascript">$("#datepicker").datepicker()</script>';
			//echo 'DatePicker On!';
		}
		if($color){
			echo '<style type="text/css">@import url('.base_url('assets/css/spectrum.css').')</style>';
			echo '<script src="'.base_url('assets/js/spectrum.js').'"></script>';
			
		}
	}
	
	function rbt_open_form($id = "", $class=""){
		echo form_open_multipart(null,array('id'=>$id, 'class'=>$class));
		// echo '<form method="post" enctype="multipart/form-data" id="'.$id.'" class="'.$class.'">';
	}

	function rbt_close_form($no_button=FALSE, $button_value = "submit"){
		echo '<p><input type="submit" class="btn btn-primary btn-large" value="'.$button_value.'" id="submit_btn"></p>';
		// echo '</form>';
		echo form_close();
	}

	function curl_post($target, $data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);

		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $output;
	}

	function curl_get($target){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_POST, true);

		//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$output = curl_exec($ch);
		$info = curl_getinfo($ch);
		curl_close($ch);

		return $output;
	}

	function curl_get_https($url){
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    //curl_setopt($ch, CURLOPT_REFERER, $url);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result;
	}

	function roar(){
		echo 'FUS DO RAAAAHHHH!';
	}

	function dumper($multi){
		echo '<pre>';
		var_dump($multi);
		echo '</pre>';
	}

	function show_sess(){
		$CI=&get_instance();
		dumper($CI->session->all_userdata());
	}
	
	function dacl(){
		$CI=&get_instance();
		dumper($CI->session->userdata('debug_acl'));
	}

	function toshout_dev($array){
		if(gettype($array) == 'string') $array = array($array=>'success');

		$CI=&get_instance();
		if($CI->config->item('toshout') != FALSE){
			$current = $CI->config->item('toshout');
			$new = array_merge($current,$array);
		}else{
			$new = $array;
		}

		$CI->config->set_item('toshout', $new);

	}

	function shout_dev(){
		$CI =&get_instance();
		$message = $CI->config->item('toshout');
		$CI->config->set_item('toshout', array());

		if($message != FALSE){
			echo '<p>';
			foreach($message as $msg=>$class){
				echo '<div class="alert alert-'.$class.'">';
				echo $msg;
				echo '</div>';
			}
			echo '</p>';

			
		}else{
			//roar();
		}
	}

	function toshout($array){
		if(gettype($array) == 'string') $array = array($array=>'success');

		$CI=&get_instance();
		if($CI->session->userdata('toshout') != FALSE){
			$current = $CI->session->userdata('toshout');
			$new = array_merge($current,$array);
		}else{
			$new = $array;
		}

		$CI->session->set_userdata('toshout', $new);

	}

	function shout(){
		$CI =&get_instance();
		$message = $CI->session->userdata('toshout');
		$CI->session->set_userdata('toshout', array());

		if($message != FALSE){
			echo '<p>';
			foreach($message as $msg=>$class){
				echo '<div class="alert alert-'.$class.'">';
				echo $msg;
				echo '</div>';
			}
			echo '</p>';

			
		}else{
			//roar();
		}
	}

	function hashim($txt){
		//shadow will map the txt
		$m = mapley();
		$xtx = array();
		foreach(str_split($txt) as $x){
			$xtx[] = $m[$x];
		}
		//hide between trees
		$forest = '';
		foreach(str_split(sha1($txt)) as $i=>$tree){
			if(array_key_exists($i, $xtx)) $forest .= $tree.$xtx[$i];
			else $forest .= $tree;
		}

		if(strlen($txt) < 10) $forest.='0'.strlen($txt).'==';
		else $forest.=strlen($txt).'==';
		return $forest;
	}

	function robot($code){
		$m = mapley();
		$raw = str_split($code);
		$t = (int)($raw[count($raw)-4].$raw[count($raw)-3]);
		$point = '';
		for($i=1; $i <= ($t*2) ; $i+=2){
			$point .= $m[$raw[$i]];
		}

		return $point;
	}

	function mapley(){
		$array = '1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik9ol0pQAZWSXEDCRFVTGBYHNUJMIKOLP';
		$arr = str_split($array);
		foreach($arr as $i=>$s){
			$inverse = $arr[(strlen($array) - ($i+1))];
			$m[$s] = $inverse; 
		}

		return $m;
	}

	function test_post(){
		$CI =&get_instance();
		if($CI->input->post()) dumper($CI->input->post());
	}

	function array_escape($to_escape){
		foreach($to_escape as $key=>$value){
			$return_array[$key] = mysql_escape_string($value);
		}
		
		return $return_array;
	}



?>