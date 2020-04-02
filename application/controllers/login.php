<?php
/*This file belongs to the RULA BookFinder Application.
*
*Bookfinder is free software: you can redistribute it and/or modify
*it under the terms of the GNU General Public License as published by
*the Free Software Foundation, either version 3 of the License, or
*(at your option) any later version.
*
*Bookfinder is distributed in the hope that it will be useful,
*but WITHOUT ANY WARRANTY; without even the implied warranty of
*MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*GNU General Public License for more details.
*
*You should have received a copy of the GNU General Public License
*along with RULA Bookfinder.  If not, see <http://www.gnu.org/licenses/>.
*
*/
?>
<?php

class Login extends CI_Controller{
	function Login(){
		parent::__construct();
		$this->load->model('model_admin');
	}
	
	function index(){
		$this->session->set_userdata('logged_in', 'FALSE');
		$this->session->set_userdata('matrix_id', '');
		$this->session->set_userdata('admin_type', '');
		$this->load->view('login/login_view');
	}
	
	function validate_credentials(){
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		
		$this->db->where('matrix_id', $username);
		$results = $this->db->get('tbl_admin');
		$result = $results->row();
		
		if($results->num_rows() > 0){
			if(!USE_LDAP){
				$matrix_id =  $result->matrix_id;
				$salt =  $matrix_id[0].substr($matrix_id,-1);
				
				//Password will be determined by LDAP once access is granted
				if(md5($salt.$password) == $result->password){
					$this->session->set_userdata('logged_in', 'TRUE');
					$this->session->set_userdata('matrix_id', $result->matrix_id);
					$this->session->set_userdata('admin_type', $result->role);
				}

				//Is admin, but wrong password was entered
				else{
					$this->session->set_flashdata('message', 'Incorrect username/password!');
					redirect('/login');
				}
				
				//If referrer is set, redirect to previous page, otherwise load the root of the map application
				if($this->session->userdata('referrer') != "" ){
					redirect($this->session->userdata('referrer'));
				}
				else{
					redirect("/admin");
				}
			}
			//LDAP Login
			else{
				$this->session->set_flashdata('message', 'ERROR: LDAP NOT IMPLEMENTED');
				redirect('/login');
			}
		}
		//User is not an admin
		else{
			$this->session->set_flashdata('message', 'Sorry, you do not have access to the admin page!');
			redirect('/login');
		}
	
	}
}