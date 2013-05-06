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

class Search extends CI_Controller{
	function Search(){
		parent::__construct();
		$this->load->helper('Summon.Class');
		
		
	}
	
	function index(){
		$data['post'] = '';
		
		if($this->input->post('s') !== FALSE){
			$data['post'] = $this->input->post('s');
		}
		
		$this->load->view('search', $data);
	}
	
	function proxy(){
		$this->load->model('model_log');
		
		//Only record page one of search
		if($this->input->post('page') === NULL || $this->input->post('page') == 1){
			$this->model_log->log_activity($this->input->post('s_q'), 'search');
		}
		
		
		// copy all parameters that start with s.
		$query_arr = array();

		// PhP replaces . with _ in _REQUEST keys
		// http://www.php.net/manual/en/language.variables.external.php

		// fallback - simply 'q'
		if ($this->input->post('q') !== FALSE) {
			$query_arr['s.q'] = $this->input->post('q');
		}

		if($this->input->post('page')!== FALSE && is_numeric($this->input->post('page')) && $this->input->post('page') > 0){
			$page = $this->input->post('page');
		}
		else{
			$page = 1;
		}
		
		if (!isset($query_arr['s.q'])) {
			$query_arr['s.q'] = '';
		}

		foreach ($_REQUEST as $key => $value) {
			if (strpos($key, "t_") === 0) {
				$query_arr['s.q'] = $query_arr['s.q'] . " " . substr($key, 2) . ":(" . $value . ")";
				continue;
			}

			// replace the first _ with a .?  
			$count = 1;
			$key2 = str_replace('_', '.', $key, $count);
			$query_arr[$key2] = $value;
		}
		
		$summon = new Summon(SUMMON_API_ID, SUMMON_API_KEY);
		
		$result = $summon->query($query_arr, null, $page);
		header("Content-Type: application/json");

		$reply = $result;
		if ($this->input->post('callback') !== FALSE) {
			$reply = $this->input->post('callback') . "(" . $reply . ")";
		}

		echo $reply;
	}
	
	
}