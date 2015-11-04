<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {

	public function create(){
		// select a template

		// edit the content

		// paste pixel / analytics code,

		// give it slug

		// save into database --> show slug

		$this->load->view('page/create');
	}

	public function upload_file(){
		echo 'yihaaa!';
		dumper($_FILES);
	}

	// public function list(){

	// }

}