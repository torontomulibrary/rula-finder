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
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	function Admin(){
		parent::__construct();
		
		//Load in the neccessary libraries
		$this->load->model('model_admin');
		$this->load->library('grocery_CRUD');	
		
		//Force a login if user has not done so already
		if($this->session->userdata('logged_in') != "TRUE"){
			$this->session->set_userdata('referrer', current_url());
			redirect('login/');
			die;
		}
	}
	
	function index(){
		$data['title'] = "Draw Stacks";
		$data['floors'] = $this->model_admin->get_all_floors();
		$data['item_types'] = $this->model_admin->get_item_types();
		$data['stack_types'] = $this->model_admin->get_stack_types();
		
		
		
		$this->template->load('admin/theme', 'admin/draw_stacks', $data);
	}
	
	function manage_item_types(){
		if($this->session->userdata('admin_type') !== "Super Admin") redirect('admin');
		$crud = new grocery_CRUD();
		$crud->set_table('tbl_item_type');
		$crud->set_subject('Item Types');
		$crud->required_fields('type_name');
		$crud->required_fields('is_stack');
		
		$crud->display_as('type_name','Item Type Name');
		$crud->display_as('type_desc','Description');
		$crud->display_as('is_stack','Is this item a stack?');
		$crud->columns('type_name','type_desc', 'is_stack');
		
		$data['title'] = "Manage Item Types";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);
	}
	
	function manage_stack_types(){
		if($this->session->userdata('admin_type') !== "Super Admin") redirect('admin');
		
		$crud = new grocery_CRUD();
		$crud->set_table('tbl_stack_type');
		$crud->set_subject('Stack Types');
		$crud->required_fields('catalogue_name');
		
		$crud->display_as('catalogue_name','Catalogue Name');
		$crud->display_as('stack_type_desc','Description');
		$crud->display_as('catalogue_pattern','Catalogue Pattern (% for wildcard)');
		$crud->display_as('priority','Results Priority');
		
		
		$crud->columns('stack_type_name','stack_type_desc', 'catalogue_pattern', 'priority');
		
		$data['title'] = "Manage Stack Types";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);
	}
	
	function manage_buildings(){
		if($this->session->userdata('admin_type') !== "Super Admin") redirect('admin');
		
		$crud = new grocery_CRUD();
		$crud->set_table('tbl_buildings');
		$crud->set_subject('Buildings');
		$crud->required_fields('bldg_name');
		
		$crud->display_as('bldg_name','Building Name');
		$crud->display_as('bldg_desc','Description');
		$crud->columns('bldg_name','bldg_desc');
		
		$data['title'] = "Manage Buildings";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);
	
	}
	
	function manage_floors(){
		if($this->session->userdata('admin_type') !== "Super Admin") redirect('admin');
		$crud = new grocery_CRUD();
		
		$crud->set_table('tbl_floors');
		$crud->set_subject('Manage Floors');
		
		$crud->set_relation('bldg_id','tbl_buildings', 'bldg_name');
		$crud->set_field_upload('img_url', MAPS_DIR);
		
		$crud->required_fields('bldg_id');
		$crud->required_fields('floor_name');
		$crud->required_fields('img_url');
		
		$crud->display_as('floor_name','Floor Name');
		$crud->display_as('img_url','Image File');
		$crud->display_as('bldg_id','Building');
		$crud->display_as('weight', 'Display weight');
		
		$crud->columns('floor_name','img_url', 'bldg_id', 'weight');
		
		$data['title'] = "Manage Floors";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);
	}
	
	function manage_admin(){
		if($this->session->userdata('admin_type') !== "Super Admin") redirect('admin');
		
		
		$crud = new grocery_CRUD();
		$crud->set_table('tbl_admin');
		$crud->set_subject('Administrators');
		$crud->required_fields('matrix_id');
		
		$crud->display_as('matrix_id','Matrix ID');
		$crud->display_as('role','Role');
		
		$crud->callback_field('role', array($this, 'field_callback'));
		
		if(USE_LDAP){
			$crud->columns('matrix_id', 'role');
			$crud->fields('matrix_id','role');
		}
		else{
			$crud->callback_field('password', array($this, 'password_field_callback'));
			$crud->callback_before_insert(array($this,'encode_password_callback'));
			
			$crud->columns('matrix_id','role');
			$crud->edit_fields('matrix_id', 'role');
			$crud->add_fields('matrix_id', 'password', 'role');
			$crud->display_as('password', 'Password');
		}
		
		$data['title'] = "Manage Administrators";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);
	}
	
	function myaccount(){
		$crud = new grocery_CRUD();
		$crud->set_table('tbl_admin');
		$crud->where('matrix_id', $this->session->userdata('matrix_id'));
		$crud->set_subject('Password');
		
		$crud->unset_delete();
		$crud->unset_add();
		
		$crud->required_fields('matrix_id');
		
		$crud->columns('matrix_id', 'role');
		$crud->fields('password');
		
		$crud->callback_field('password', array($this, 'password_field_callback'));
		$crud->callback_before_update(array($this,'encode_password_callback'));

		
		$crud->display_as('password', 'New Password');
		$data['title'] = "Change Password";
		$data['crud'] = $crud->render();
		
		$this->template->load('admin/theme', 'admin/manage_crud', $data);

	}
	
	function usage(){
		
		if(($this->input->post('date_start') !== FALSE) && ($this->input->post('date_end') !== FALSE)){
			$data['start'] = $this->input->post('date_start');
			$data['end'] = $this->input->post('date_end');
		}
		else{
			$data['start'] = date('Y-m-d',mktime(0, 0, 0, date("m")-3  , date("d"), date("Y")));
			$data['end'] = date('Y-m-d');
		}
		
		
		
		$data['stats'] = $this->model_admin->search_stats($data['start'],$data['end'],'',TRUE);
		$data['desktop_stats'] = $this->model_admin->search_stats($data['start'],$data['end'], 'desktop');
		$data['mobile_stats'] = $this->model_admin->search_stats($data['start'],$data['end'], 'mobile');
		
		$data['device'] = $this->model_admin->hits_by_device_type($data['start'],$data['end']);
		$data['top_search'] = $this->model_admin->top_ten_searches($data['start'],$data['end']);
		
		$this->template->load('admin/theme', 'admin/usage_stats', $data);
	}
	
	function logout(){
		$this->session->sess_destroy();
		redirect('login');
	}
	
	function submit_stack(){
		$data['floor'] = str_ireplace('tab', '', $this->input->post('floor'));
		$data['top'] = $this->input->post('top');
		$data['left'] = $this->input->post('left');
		$data['height'] = $this->input->post('height');
		$data['width'] = $this->input->post('width');
		$data['call_start'] =  strtoupper($this->input->post('call_start'));
		$data['call_end'] = strtoupper($this->input->post('call_end'));
		$data['loc_name'] = $this->input->post('loc_name');
		$data['stack_type'] = $this->input->post('stack_type');
		$data['item_type'] = $this->input->post('item_type');
		
		if($this->input->post('loc_id') === false){
			echo $this->model_admin->insert_location($data); //   <--- loc_id
		}
		else{
			$data['loc_id'] = $this->input->post('loc_id');
			
			$this->model_admin->update_stack($data);
		}
	}
	
	function delete_stack(){
		$id = $this->input->post('loc_id');
		$this->model_admin->delete_stack($id);
	}
	
	
	
	/****************************
	*     HELPER FUNCTIONS      *
	****************************/
	public function field_callback($value = NULL){
		$options = array('Admin', 'Super Admin');
		$option_tag = '';
		foreach($options as $option){
			$attribute  = 'value="'.$option.'"';
			if ($option == $value){
			$attribute .= ' selected="selected"';
		}
		$option_tag .= "<option $attribute>$option</option>";
		}
		return '<select name="role">'.$option_tag.'</select>';
	}
	
	public function password_field_callback($value = NULL){
		return '<input type="password" name="password" />';
	}
	
	
	function encode_password_callback($post_array, $primary_key = null){
		if(isset($post_array['matrix_id'])){
			$matrix_id = $post_array['matrix_id'];
		}
		else{
			$matrix_id = $this->session->userdata('matrix_id');
		}
		$salt = $matrix_id[0].substr($matrix_id,-1);
		$post_array['password'] = md5($salt.$post_array['password']);
		echo $post_array['password'];
		return $post_array;
	}
	
}