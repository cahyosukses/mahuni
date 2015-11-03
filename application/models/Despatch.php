<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Despatch extends CI_Model {
	
	var $debug = false;

	public function send_invoice($purchase_key){

		$this->load->model('Purchase');

		$data = $this->Purchase->get_details($purchase_key);

		$email['to'] = $data['order_email'];
		$email['subject'] = $data['order_email_subject'];
		$email['message'] = $data['order_email_message'];

		if($data['order_email_from'] == '') $email_from = false;
		else $email_from = $data['order_email_from'];

		$admin = $data['admin_name'];

		if($admin == '') $admin = false;

		$email['from'] = $email_from;
		$email['name'] = $admin;

		$ret = $this->sender($email['to'], $email['subject'], $email['message'], false ,$email['from'], $email['name']);

		return $ret;
		// return $email;
	}

	public function activation($user_id){
		$this->load->model('Users');
		$user = $this->Users->get_by_id($user_id);

		$message = $this->generate_activation_message($user);
		$subject = '[Yezza] Aktifkan Akaun Yezza';
		$to = $user['email'];

		// dumper($message);

		$this->sender($to, $subject, $message);
	}

	public function generate_activation_message($user){
		$template = 'Terima kasih kerana mendaftar dengan Yezza.

Klik link di bawah untuk aktifkan akaun ini:

'.site_url('umum/activate/'.$user['key']).'

Terima kasih dan jumpa di sana!';

		return $template;
	}

	public function sender($to, $subject, $message, $html = false, $reply_to = false, $name = false){
		
		$to = trim($to);

		$debug = $this->debug;

		if($reply_to == '') $reply_to = false;
		if($name == '') $name = false;

		if(!$reply_to) $reply_to = 'noreply@bangbot.net';

		$this->load->library('Amazon_ses');
		$this->amazon_ses->debug($debug);

		$this->amazon_ses->to($to);
		$this->amazon_ses->subject($subject);

		if($name) $this->amazon_ses->from($reply_to, $name);
		else $this->amazon_ses->from($reply_to);

		if($html) $this->amazon_ses->message($html);

		$this->amazon_ses->message_alt($message);

		// $this->amazon_ses->message($message);
		if($debug){
			$ret = PHP_EOL.'reply_to: '.$reply_to.PHP_EOL;
			$ret .= 'name: '.$name.PHP_EOL;
			$ret .= 'to: '.$to.PHP_EOL;
			$ret .= 'subject: '.$subject.PHP_EOL;
			$ret .= 'message: '.$message.PHP_EOL;
			$ret .= 'html: '.$html.PHP_EOL;
			$ret .= 'SES debug: '.$this->amazon_ses->send();

			dumper($ret);
		}else{
			// log the despatch
			// $param['to'] = $to;
			// $param['subject'] = $to;
			// $param['reply_to'] = $to;
			// $param['name'] = $to;
			// $param['message'] = $to;
			// $param['html'] = $to;

			// $this->db->insert('email_log', $param);

			$ret = $this->amazon_ses->send();
		}
		
		return $ret;
	}

}