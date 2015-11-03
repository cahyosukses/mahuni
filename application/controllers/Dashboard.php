<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct(){
		parent::__construct();

		verify_access();
	}

	public function index(){
		$this->load->view('dashboard_index');
	}

	public function sandbox(){
		// bindkan traffic_log poe with purchase device id
		// $this->load->model('Purchase');
		// $this->db->like('email', '% %');
		// $query = $this->db->get('purchases');

		// dumper($query->result_array());
		// dumper($this->Purchase->email_download_link('49f463320a796a9a443157780d29d585'));

		// 842f31b58a4c9a311a5afa2e782c5f6f
		
	}

	public function aff_sales_kit(){
		$this->load->model('Item');

		$item = $this->Item->get_by_key($this->uri->segment(3));

		$inputs['sales_kit'] = array('type'=>'textarea', 'label'=>'Sales Kits Guides:', 'required'=>'');

		$sales_kit = str_replace('{{aff_tag}}', $this->uri->segment(4), $item['sales_kit']);

		$defaults['sales_kit'] = $sales_kit;

		$data['defaults'] = $defaults;
		$data['inputs'] = $inputs;
		$data['title'] = 'Sales Kit Guides';

		$this->load->view('dashboard_form', $data);
	}

	public function top_affiliate(){

	}

	public function channel_report(){
		$this->db->distinct('device_key');
		$this->db->like('referrer', 'lookusaha');
		$query = $this->db->get('traffic_log');

		foreach($query->result_array() as $row){
			$devices[] = $row['device_key'];
		}

		$dev = '( `device_id` = \''.implode('\' OR `device_id` = \'', $devices).'\')';
		$this->db->where('status', 'payment_confirmed');
		$this->db->where($dev);
		$this->db->select('COUNT(*) as count, DATE(paid_at) as paid');
		$this->db->group_by('paid');
		$this->db->order_by('paid', 'DESC');
		$query = $this->db->get('purchases');

		echo '<br/>FB Ads sales:';
		$total = 0;
		foreach($query->result_array() as $row){
			echo '<br/>'.$row['paid'].' = '.$row['count'];
			$total += $row['count'];
		}

		echo '<br>TOTAL = '.$total;
	}

	public function aff_sales(){
		$user = $this->session->userdata('user');

		// dapatkan items for these user
		$this->load->model('Sales');
		// $all = $this->Sales->affiliate(21);
		$all = $this->Sales->affiliate($user['id']);

		$count = 0;
		$rows = array();
		foreach($all as $key=>$row){
			$arr['#'] = ++$count;
			$arr['Title'] = $row['title'];
			$arr['Pending'] = $row['pending'];
			$arr['Sold'] = $row['sold'];
			$arr['RM/sale'] = $row['profit'];
			$arr['Total Sales'] = 'RM '.number_format($row['total_RM'], 2);

			$rows[] = $arr;
		}
		$data['rows'] = $rows;
		$data['title'] = 'Hasil Jualan Produk Affiliate';

		$this->load->view('dashboard_list', $data);
	}

	public function create_aff_link(){
		if($this->uri->segment(3)){

			$this->load->model('Item');
			$item = $this->Item->get_by_key($this->uri->segment(3));

			$user = $this->session->userdata('user');

			if($this->input->post()){
				$tag = $this->input->post('tag');

				$param['tag'] = $tag;
				$param['item_id'] = $item['id'];
				$param['user_id'] = $user['id'];

				// check, tag ni available tak dalam aff_item
				$this->db->where('tag', $tag);
				$query = $this->db->get('affiliate_item');

				if($query->num_rows() === 0){
					$this->db->insert('affiliate_item', $param);
					toshout_success('Your promo link for '.$item['title'].' has been created.');
					redirect('dashboard/aff_products');
				}else{
					toshout_error('Your promo tag of <b>'.$tag.'</b> has been used. Please choose other tag:');
				}
			}

			// dumper($item);
			$data['link'] = $item['landing_page_link'];
			$data['item_title'] = $item['title'];

			$this->load->view('dashboard_create_aff_link', $data);

		}else{
			toshout_error('Please select any item to create its promo link');
			redirect('dashboard/aff_products');
		}
	}

	public function aff_delete_tag(){
		$this->db->where('tag', $this->uri->segment(3));
		$this->db->delete('affiliate_item');

		redirect('dashboard/aff_products');
	}

	public function aff_products(){
		$this->load->model('Item');
		$items = $this->Item->get_all_affiliate_items();
		$promoted = $this->Item->get_promoted_items();
		foreach($items as $i=>$item){
			$arr['#'] = $i+1;
			$arr['Title'] = $item['title'];
			// $arr['Unpaid'] = '0.00';
			// $arr['Paid'] = '0.00';
			$arr['By'] = $item['owner_fullname'].' ('.$item['owner_email'].')';
			$arr['Price'] = $item['price'];
			$arr['RM/sale'] = $item['affiliate_profit'];
			if(array_key_exists($item['id'], $promoted) === FALSE) $arr['action'] = '<a href="'.site_url('dashboard/create_aff_link/'.$item['key']).'" class="btn btn-xs btn-success">create promo link</a>';
			else $arr['action'] = '<a href="'.site_url('dashboard/aff_delete_tag').'/'.$promoted[$item['id']].'" class="btn btn-xs btn-danger">delete tag</a> <a href="'.$item['landing_page_link'].'?tag='.$promoted[$item['id']].'" class="btn btn-xs btn-primary" target="_blank">promo link</a> <a href="'.site_url('dashboard/aff_sales_kit/'.$item['key']).'/'.$promoted[$item['id']].'" class="btn btn-xs btn-default">sales kit &raquo;</a>';
			$rows[] = $arr;
		}

		$data['title'] = 'Senarai Produk Affiliate';
		$data['rows'] = $rows;

		$this->load->view('dashboard_list', $data);
	}

	public function sequence(){
		if($this->uri->segment(3)){
			$user = $this->session->userdata('user');
			$path = $user['id'].'-'.$this->uri->segment(3);
			$this->load->model('Dripmail');
			$sequence = $this->Dripmail->get_sequence($path);

			$data['sequence'] = $sequence;
			$this->load->view('dashboard_sequence', $data);

		}else{
			toshout_error('Please select a sequences &raquo;');
			redirect('dashboard/dripmail');
		}
	}

	public function dripmail(){

		$user = $this->session->userdata('user');

		$this->load->model('Dripmail');
		$sequences = $this->Dripmail->get_all_for_user();

		$rows = array();
		foreach($sequences as $i=>$seq){
			$arr['#'] = $i+1;
			$arr['name'] = str_replace($user['id'].'-', '', $seq);
			$arr['action'] = '<a href="'.site_url('dashboard/sequence/'.$arr['name']).'" class="btn btn-xs btn-default">view &raquo;</a>';

			$rows[] = $arr;
		}

		$data['rows'] = $rows;
		$data['title'] = 'All Dripmail Sequences';
		
		// $data['top_button'] = '<a href="'.site_url('api/send_missed/'.$this->uri->segment(3)).'" class="btn btn-xs btn-default pull-right">Send Missed</a>';

		$this->load->view('dashboard_list', $data);
	}

	public function report(){

		$user = $this->session->userdata('user');

		$this->load->model('Purchase');
		$response = $this->Purchase->get_all_report($user['id']);

		$data['totals'] = $response['total'];
		$data['rows'] = $response['rows'];
		$data['item_keys'] = $response['item_keys'];
		$this->load->view('dashboard_report', $data);

	}

	public function purchases(){

		$title = '';
		$rows = array();
		if($this->uri->segment(3)){
			$key = $this->uri->segment(3);

			$this->load->model('Purchase');
			$rows = $this->Purchase->get_item_purchases($key);

			foreach($rows as $arr){
				if($arr['status'] == 'pending'){
					// $total['pending']++;
					// $arr['action'] .= ' approve';
					// $arr['status'] = 'pending';
					$arr['action'] = '<a href="'.site_url('dashboard/approve_purchase/'.$arr['key']).'">approve</a>';
					// $pending[] = $arr;	
				}elseif($arr['status'] == 'approved'){
					// $total['approved']++;
					// $arr['status'] = 'approved';
					$arr['action'] = '<a href="'.site_url('dashboard/resend/'.$arr['key']).'">resend</a>';
					// $confirmed[] = $arr;
				}else{
					// $total['new']++;
					$arr['status'] = 'new';
					$arr['action'] = '<a href="#">remind</a> <a href="'.site_url('dashboard/approve_purchase/'.$arr['key']).'">approve</a>';
					// $new[] = $arr;
				}
				unset($arr['key']);
				$arrs[] = $arr;
			}
		}

		$data['rows'] = $arrs;
		$data['title'] = 'Purchases for '.$title;
		
		$data['top_button'] = '<a href="'.site_url('api/send_missed/'.$this->uri->segment(3)).'" class="btn btn-xs btn-default pull-right">Send Missed</a>';

		$this->load->view('dashboard_list', $data);

	}

	public function approve_purchase(){
		$key = $this->uri->segment(3);

		if($key){
			$this->load->model('Purchase');
			
			$this->Purchase->approve_auto($key);
			$this->Purchase->email_download_link($key);

			toshout_success('Yay, that order has been approved!');
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

	public function resend(){
		$key = $this->uri->segment(3);

		if($key){
			$this->load->model('Purchase');
			
			$this->Purchase->email_download_link($key);

			toshout_success('Yay, that ebook has been resend!');
		}

		redirect($_SERVER['HTTP_REFERER']);
	}

}