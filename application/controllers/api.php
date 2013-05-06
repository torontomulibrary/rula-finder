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

class Api extends CI_Controller {

	function Api(){
		parent::__construct();
		
		//Load in the neccessary libraries
		$this->load->model('model_api');
		$this->load->model('model_log');
		
		if($this->uri->segment(3) !== FALSE){
			$this->model_log->log_activity($this->uri->segment(3), 'display');
		}
	}
	
	
	function call_no(){
		$search = $this->uri->segment(3);
		if($search !== false){
			$data['book_data'] = $this->model_api->callno_search($search);
		}
		else{
			$data['book_data'] = array("3");
		}
		$this->load->view('api/location_data', $data);	
	}
	
	function email(){
		$this->load->library('email');
		$this->load->model('model_log');
		
		$this->model_log->log_activity($this->input->post('s_q'), 'search');
		
		
		
		//Email data
		$email_to = $this->input->post('target_email');
		
		//Page content data
		$page_url = urldecode($this->input->post('page_url'));
		$title = strip_tags(urldecode($this->input->post('title')));
		$callno = urldecode($this->input->post('callno'));
		$shelf = urldecode($this->input->post('shelf'));
		$author = urldecode($this->input->post('author'));
		$floor = urldecode($this->input->post('floor'));
		
		if($email_to !== null){
			$this->model_log->log_activity($email_to, 'email');
			
			//$message =  "Hi ".$email_name.",<br /><br />";
			$message = "Here is your information you requested using the RULA BookFinder:<br /><br />";
			$message .= "Item Link: <a href='".$page_url."'>".$page_url."</a><br />";
			
			if(strlen($shelf) > 0){
				$message .= "Shelf Location: ".$floor. ", ".$shelf."<br />";
			}
			
			if(strlen($callno) > 0){
				$message .= $callno."<br />";
			}
			
			if(strlen($title) > 0){
				$message .= "Title: ".$title."<br />";
			}
			
			if(strlen($author) > 0){
				$message .= "Author: ".$author."<br />";
			}
			
			$this->email->from('no-reply@ryerson.ca', 'Ryerson University Library');
			$this->email->to($email_to); 
			$this->email->subject('BookFinder Message');
			$this->email->message($message); 
			$this->email->send();
		}
	}
}