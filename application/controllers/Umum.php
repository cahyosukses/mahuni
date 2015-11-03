<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Umum extends CI_Controller {

	public function sandbox(){
		echo base_url();
	}

	public function mahu(){

		$slug = $this->uri->segment(2);
		
		// if not nak, redirect to nak first
		if($this->uri->segment(2) == 'mahu'){
			$this->session->set_userdata('referer', $_SERVER['HTTP_REFERER']);
			redirect('nak/'.$this->uri->segment(3));
		}

		// if got segment, means there is tracker. Save it then redirect it
		// to make the url seems clean
		if($this->uri->segment(3) != false){
			$track['device_id'] = $this->uri->segment(3);
			$track['poe'] = $this->uri->segment(4);
			$track['affiliate_tag'] = $this->uri->segment(5);
			$track['referer'] = $_SERVER['HTTP_REFERER'];

			// dumper($_SERVER['HTTP_REFERRER'])
			if($this->session->userdata('referer')) $track['referer'] = $this->session->userdata('referer');
			// dumper($_SESSION['ref']);
			// dumper($_SERVER['HTTP_REFERER']);
			$this->session->set_userdata('tracker', $track);

			redirect('nak/'.$this->uri->segment(2));
		}

		if($slug){
			$this->load->model('Item');

			$item = $this->Item->get_by_slug($slug);

			$book['title'] = $item['title'];
			$book['price'] = $item['price'];
			$book['paypal_email'] = $item['paypal_email'];
			$book['description'] = $item['description'];
			$book['manual_bank_transfer_instruction'] = $item['manual_bank_transfer_instruction'];
			$book['cover'] = $item['cover'];
			$book['key'] = $item['key'];
			$book['slug'] = $item['slug'];
			
			$data['item'] = $book;


			$panggilan['tuan'] = 'Tuan';
			$panggilan['cikpuan'] = 'Cikpuan';

			$order_inputs['title'] = array('type'=>'dropdown', 'label'=>'Panggilan', 'rules'=>'required', 'options'=>$panggilan);
			$order_inputs['name'] = array('type'=>'input', 'label'=>'Nama', 'rules'=>'required');
			$order_inputs['email'] = array('type'=>'input', 'label'=>'Emel', 'rules'=>'required');
			$order_inputs['hp'] = array('type'=>'input', 'label'=>'No Tel', 'rules'=>'required');

			$data['order_inputs'] = $order_inputs;

			if(rbt_valid_post($order_inputs)){
				$_POST['item_id'] = $item['id'];

				$_POST['device_id'] = '';
				$_POST['poe'] = '';
				$_POST['affiliate_tag'] = '';
				$_POST['affiliate_id'] = '';
				$_POST['referer'] = '';

				if($this->session->userdata('tracker')){

					$tracker = $this->session->userdata('tracker');
					// dumper($tracker);
					$_POST['device_id'] = $tracker['device_id'];
					$_POST['poe'] = $tracker['poe'];
					$_POST['affiliate_tag'] = $tracker['affiliate_tag'];
					$_POST['affiliate_id'] = '';
					$_POST['referer'] = $tracker['referer'];

				}
				// $_POST['device_id'] = $this->uri->segment(3);
				// $_POST['poe'] = $this->uri->segment(4);
				// $_POST['affiliate_tag'] = $this->uri->segment(5);
				// $_POST['affiliate_id'] = null;

				if($_POST['affiliate_tag']){
					$this->db->where('item_id', $item['id']);
					$this->db->where('tag', $_POST['affiliate_tag']);
					$query = $this->db->get('affiliate_item');

					$aff = $query->row_array();

					$_POST['affiliate_id'] = $aff['user_id'];
				}

				$_POST['form_url'] = base_url(uri_string());
				$_POST['ordered_at'] = date('Y-m-d H:i:s');
				$_POST['created_at'] = date('Y-m-d H:i:s');
				$_POST['key'] = md5(rand().time());

				// put into purchase, then redirect to umum_purchase
				$this->db->insert('purchases', $this->input->post());

				// hantar email link bayaran
				// $this->load->model('Purchase');
				// $purchase = $this->Purchase->get_details($this->db->insert_id());

				// dumper($purchase);


				$this->load->model('Despatch');
				$this->Despatch->send_invoice($this->db->insert_id());
				// redirect to purchase page
				// toshout_success('Tempahan '.$this->input->post('title').' Telah diterima! Pilih cara pembayaran seperti pilihan di bawah:');
				redirect(site_url('bayar/'.$_POST['key']));
			}

			$this->load->view('umum_mahu', $data);
		}
	}

	public function bayar(){

		$key = $this->uri->segment(2);
		
		if($this->uri->segment(2) == 'bayar') redirect('bayar/'.$this->uri->segment(3));

		if($key){
			
			$inputs['upload_receipt'] = array('type'=>'upload', 'label'=>'Upload Resit', 'rules'=>'upload_path:locked/receipts|allowed_types:gif,png,jpeg,jpg,pdf|max_size:1000|overwrite:TRUE');
			$inputs['manual_receipt'] = array('type'=>'textarea', 'label'=>'ATAU Taip Maklumat Resit', 'rules'=>'');

			$data['inputs'] = $inputs;
			
			$this->load->model('Purchase');
			
			if(rbt_valid_post($inputs)){
				toshout_success('Maklumat Bayaran telah selamat kami terima. Kami akan sahkan bayaran tuan secepat mungkin. <b>Emel berserta link download</b> akan di hantar setelah bayaran di sahkan. Terima kasih!');

				$res = $this->Purchase->approve_manual($key, $this->input->post());
				// redirect('umum/purchase/'.$this->uri->segment(3));
			}

			$purchase = $this->Purchase->get_details($key);

			// dumper($purchase);

			$book['title'] = $purchase['item_title'];
			$book['price'] = $purchase['item_price'];
			$book['paypal_email'] = $purchase['paypal_email'];
			$book['description'] = $purchase['item_description'];
			$book['manual_bank_transfer_instruction'] = $purchase['manual_bank_transfer_instruction'];
			$book['cover'] = $purchase['item_cover'];
			// $book['key'] = $purchase['key'];
			// $book['slug'] = $purchase['slug'];
			
			$data['item'] = $book;
			$data['purchase'] = $purchase;

			$this->load->view('umum_bayar', $data);
		}
	}

	public function activate(){
		if($this->uri->segment(3)){
			$this->db->where('key',$this->uri->segment(3));
			$this->db->update('users', array('status'=>'activated'));
			// $query = $this->db->get('users');
			// $user = $query->row_array();
			toshout_success('Activation berjaya. Mohon login:');
			redirect('umum/login');
		}
	}

	public function daftar_affiliate(){

		$hons['Tuan'] = 'tuan';
		$hons['Cikpuan'] = 'cikpuan';

		$input['honorific'] = array('type'=>'dropdown', 'label'=>'Honorific', 'rules'=>'required', 'options'=>$hons);
		$input['display_name'] = array('type'=>'input', 'label'=>'Name', 'rules'=>'required');
		$input['username'] = array('type'=>'input', 'label'=>'Username', 'rules'=>'required');
		$input['password'] = array('type'=>'password', 'label'=>'Password', 'rules'=>'required');
		$input['email'] = array('type'=>'input', 'label'=>'Email', 'rules'=>'required|valid_email');
		$input['hp'] = array('type'=>'input', 'label'=>'H/P', 'rules'=>'required');

		if(rbt_valid_post($input)){
			// dumper($this->input->post());
			// add to user
			$_POST['password'] = hashim($_POST['password']);
			$_POST['key'] = md5(microtime().json_encode($_POST));

			$insert['honorific'] = $this->input->post('honorific');
			$insert['display_name'] = $this->input->post('display_name');
			$insert['username'] = $this->input->post('username');
			$insert['password'] = $this->input->post('password');
			$insert['email'] = $this->input->post('email');
			$insert['hp'] = $this->input->post('hp');
			$insert['key'] = $this->input->post('key');

			$this->db->insert('users', $insert);
			$user_id = $this->db->insert_id();

			// add to user_group
			$param['user_id'] = $user_id;
			$param['group_id'] = 4;

			$this->db->insert('user_group', $param);

			$this->load->model('Despatch');
			$this->Despatch->activation($user_id);

			toshout_success('Mohon semak email dan klik link activation untuk activate account');

			redirect('umum/daftar_affiliate');
		}

		$data['inputs'] = $input;
		$data['title'] = 'Daftar Sebagai Affiliate';
		$data['defaults'] = array();
		$this->load->view('umum_form', $data);
	}

	// drip mail subscription form
	public function subscribe(){
		if($this->uri->segment(3)){
			$this->db->where('slug', $this->uri->segment(3));
			$query = $this->db->get('items');

			$data['item'] = $query->row_array();




			$titles['Tuan'] = 'Tuan';
			$titles['Cik Puan'] = 'Cik Puan';

			$inputs['title'] = array('type'=>'dropdown', 'label'=>'Panggilan', 'rules'=>'required', 'options'=>$titles);
			$inputs['name'] = array('type'=>'input', 'label'=>'Nama', 'rules'=>'required');
			$inputs['hp'] = array('type'=>'input', 'label'=>'No Telefon', 'rules'=>'required');
			$inputs['email'] = array('type'=>'input', 'label'=>'Emel', 'rules'=>'required');
			$inputs['sequence'] = array('type'=>'hidden', 'label'=>'', 'rules'=>'required');

			$data['inputs'] = $inputs;

			if(rbt_valid_post($inputs)){
				if(ISSET($_POST['sequence']) == false) $_POST['sequence'] = $this->uri->segment(3);
				
				$param['data'] = json_encode($this->input->post());
				$param['queue'] = 'subscribe_dripmail';
				$param['created_at'] = date('Y-m-d H:i:s');

				$this->db->insert('job_pool', $param);

				toshout_success('Berjaya! Nantikan emel pertama dalam sedikit masa ya.');
				
				redirect('umum/subscribe/'.$this->uri->segment(3));
			}

			$this->load->view('umum_subscribe', $data);
		}
	}

	// item order form
	public function buy(){

	}

	public function logout(){
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('group');

		redirect('login');
	}

	public function login()
	{	
		$inputs['username'] = array('type'=>'input', 'label'=>'Username', 'rules'=>'required');
		$inputs['password'] = array('type'=>'password', 'label'=>'Password', 'rules'=>'required');

		if(rbt_valid_post($inputs)){
			$this->load->model('users');
			$response = $this->users->login($this->input->post('username'), $this->input->post('password'));

			
			if($response['status']){
				toshout(array($response['message']=>'success'));
				redirect('dashboard/index');
			}else{
				toshout(array($response['message']=>'danger'));
			}

			// show_sess();
		}

		$data['inputs'] = $inputs;
		$this->load->view('umum_login', $data);
	}

	public function purchase(){

		$inputs['upload_receipt'] = array('type'=>'upload', 'label'=>'Upload Resit', 'rules'=>'upload_path:locked/receipts|allowed_types:gif,png,jpeg,jpg,pdf|max_size:1000|overwrite:TRUE');
		$inputs['manual_receipt'] = array('type'=>'textarea', 'label'=>'ATAU Taip Maklumat Resit', 'rules'=>'');

		$data['inputs'] = $inputs;
		
		$this->load->model('Purchase');
		
		if(rbt_valid_post($inputs)){
			toshout_success('Maklumat Bayaran telah selamat kami terima. Kami akan sahkan bayaran tuan secepat mungkin. <b>Emel berserta link download</b> akan di hantar setelah bayaran di sahkan. Terima kasih!');

			$res = $this->Purchase->approve_manual($this->uri->segment(3), $this->input->post());
			// redirect('umum/purchase/'.$this->uri->segment(3));
		}

		$purchase = $this->Purchase->get_details($this->uri->segment(3));
		
		// will send status as pending
		$data['purchase']  = $purchase;

		// if(rbt_valid_post($inputs)){
		// 	// tukar status as payment_confirmed ... so boleh redirect download terus
		// 	$this->db->where('key', $this->uri->segment(3));
		// 	$this->db->update('purchases', array('status'=>'payment_confirmed'));
		// }

		$this->load->view('umum_purchase', $data);
	}


	public function pay(){
		$method = $this->uri->segment(3);
		$purchase_key = $this->uri->segment(4);

		$valids = true;

		if(!$purchase_key){
			// missing purchase_key error
			$valids = false;
			$err[] = 'no purchase key detected';
		}

		if(!$method){
			// missing method error
			$valids = false;
			$err[] = 'no payment method detected';
		}

		if($valids){ // if no error, proceed
			// $purchase_key = $purchase_key.'==';
			// $purchase_id = base64_decode($purchase_key.'==');
			// $purchase_id = robot($purchase_key);

			// dapatkan maklumat purchase:
			$this->load->model('Purchase');
			$purchase = $this->Purchase->get_details($purchase_key);

			// generate link
			if($method == 'paypal'){
				$link = 'https://www.paypal.com/cgi-bin/webscr?business='.$purchase['paypal_email'].'&cmd=_xclick&currency_code=MYR&amount='.$purchase['item_price'].'&item_name='.urlencode($purchase['item_title']).'&landing_page=login&return='.site_url('umum/proof_of_payment/'.$purchase_key.'/'.$method);

				redirect($link);
				// dumper($link);
			}
			elseif($method == 'cc_paypal' OR $method == 'debit_paypal'){
				$link = 'https://www.paypal.com/cgi-bin/webscr?business='.$purchase['paypal_email'].'&cmd=_xclick&currency_code=MYR&amount='.$purchase['item_price'].'&item_name='.urlencode($purchase['item_title']).'&landing_page=billing&return='.site_url('umum/proof_of_payment/'.$purchase_key.'/'.$method);

				redirect($link);
			}elseif($method = 'online' OR $method == 'cdm'){

				dumper($method);

			}
		}
	}

	public function testing(){
		// $this->load->model('Purchase');
		// $purchase = $this->Purchase->get_details('49f463320a796a9a443157780d29d585');

		// dumper($purchase['item_download_link']);
		require('application/libraries/Mixpanel/Mixpanel.php');
		// dumper(scandir('./application/libraries/Mixpanel'));
		// $this->load->library('Mixpanel/Mixpanel.php', 'a50829b5f6db182b5dafa61c12baa58b');

		// get the Mixpanel class instance, replace with your
		// project token
		$mp = Mixpanel::getInstance("a50829b5f6db182b5dafa61c12baa58b");

		// track an event
		// $mp->identify('robotys@2gmail.com');
		dumper($mp->track("testing library", array("label" => "dev-test")));

	}

	public function proof_of_payment(){
		
		$purchase_key = $this->uri->segment(3);
		$method = $this->uri->segment(4);
		// $purchase_id = base64_decode($purchase_key.'==');

		if($purchase_key !== null){

			// $purchase_id = robot($purchase_key);

			$param = $_GET;

			// $param['purchase_key'] = $purchase_key;
			$param['purchase_key'] = $purchase_key;
			$param['method'] = $method;

			$this->session->set_flashdata('payment_param', $param);

			// dumper($param);

			redirect('umum/proof_of_payment');

		}else{

			$param = $this->session->flashdata('payment_param');

			// dumper('processing');
			// dumper($param);

			// check, kalau amount sama, then it is true
			$this->load->model('Purchase');
			$purchase = $this->Purchase->get_details($param['purchase_key']);

			if($purchase['item_price'] == $param['amt'] && strtolower($param['st']) == 'completed'){

				$this->Purchase->approve_payment($param);
				// send mixpanel data
				$this->load->model('Mxtracker');

				// set mx_status: paid
				// set rm
				$this->Mxtracker->identify($purchase['device_id']);
				$this->Mxtracker->people_set($purchase['device_id'], array('mx_status'=>'close_sale','rm'=>$purchase['item_price']));
				$this->Mxtracker->trackcharge($purchase['device_id'], $purchase['item_price'], date('Y-m-d H:i:s'));
				// $this->Mxtracker->track('close_sale');
				$this->Mxtracker->track('paypal_sale');

				$this->load->model('Despatch');

				$this->Purchase->email_download_link($param['purchase_key']);

				toshout(array('Download Email has been sent. Check your email ('.$purchase['order_email'].')'=>'success'));

			}else{
				toshout(array('Payments not approved. Something is wrong with your data. Please contact admin'=>'error'));
			}

			// show_sess();

			// shout();
			// redirect('umum/purchase/'.$param['purchase_key']);
			redirect('bayar/'.$param['purchase_key']);

		}
	}

	public function download(){
		$purchase_key = $this->uri->segment(3);
		// $purchase_id = robot($purchase_key);

		$this->load->model('Purchase');
		$purchase = $this->Purchase->get_details($purchase_key);

		//validate download via email
		if(trim($purchase['order_email']) == $this->uri->segment(4)){

			// log download details
			if($purchase['download_count'] === '0'){
				$param['first_downloaded_at'] = date('Y-m-d H:i:s');
			}
			$param['last_downloaded_at'] = date('Y-m-d H:i:s');
			$param['download_count'] = $purchase['download_count']+1;

			$this->db->where('key', $purchase_key);
			$this->db->update('purchases', $param);

			// prep pdf
			// $purchase['pdf'] = 'test.zip';
			$loc = base_url('locked/pdf/'.$purchase['pdf']);
			$pdf = file_get_contents($loc);

			if(strpos($loc, '.pdf') !== FALSE){
				// download the pdf
				$this->load->helper('download');
				force_download($purchase['pdf'], $pdf);
			}else{
				// redirect($loc);
				echo '<iframe src="'.$loc.'" style="width: 0px; heigh: 0px; border: none;"></iframe>';
			}

			
			
		}else{
			echo 'Oops, something is wrong with your purchased email. Contact admin via replying to your download link email';
		}
	}
}
