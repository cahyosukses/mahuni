<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends CI_Model {

	public function is_logged_in(){
		return is_logged_in();
	}

	public function login($username, $password){

		// $this->db->select('id, username, email, fullname, hp, ic, address, fb_id, status, key, nexmo_credit');
		$this->db->where('username', $username);
		$this->db->where('password', hashim($password));

		$query = $this->db->get('users');
		if($query->num_rows() === 1){

			$user = $query->row_array();

			$this->session->set_userdata('user', $user);

			// set groups
			// $this->db->select('groups.*');
			// $this->db->where('user_group.user_id', $user['id']);
			// $this->db->join('groups', 'groups.id = user_group.group_id');
			// $query = $this->db->get('user_group');

			// foreach($query->result_array() as $row){
			// 	$groups[$row['id']] = $row['name'];
			// }

			$groups[] = $user['group'];

			$this->session->set_userdata('group', $groups);

			// set organisations
			// $this->db->select('organisations.*');
			// $this->db->where('user_organisation.user_id', $user['id']);
			// $this->db->join('organisations', 'organisations.id = user_organisation.organisation_id');
			// $query = $this->db->get('user_organisation');

			// foreach($query->result_array() as $row){
			// 	$organisations[$row['id']] = $row['name'];
			// }

			// $this->session->set_userdata('orgs', $organisations);

			// set organisaations
			// $this->db->select('organisations.name,organisations.address,organisations.email,organisations.tel');
			// $this->db->where('user_organisation.user_id', $user['user_id']);
			// $this->db->join('organisations', 'organisations.id = user_organisation.organisation_id');

			// $query = $this->db->get('user_organisation');

			// $organisations = $query->result_array();

			// $this->session->set_userdata('organisations', $groups);

			// set response to be sent
			$response['status'] = true;
			$response['message'] = 'Welcome back '.ucwords($user['username']).'!';
			$response['error'] = false;
		}else{
			$response['status'] = false;
			$response['message'] = 'Login denied. Please check your username and password.';
			$response['error'] = false;
		}
		return $response;
	}

	public function register($data){
		$this->db->insert('users', $data);

		$user_id = $this->db->insert_id();
		// insert into group of member
		$this->db->insert('user_group', array('user_id'=>$user_id, 'group_id'=>3));

		return $this->db->insert_id();
	}

	public function get_by_id($user_id){
		$this->db->where('id', $user_id);
		$query = $this->db->get('users');

		if($query->num_rows() > 0) return $query->row_array();
		else return false;
	}

	public function locker(){
		
	}

}