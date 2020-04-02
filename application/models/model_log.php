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
class Model_Log extends CI_Model {
	function __construct(){
        parent::__construct();
		$this->load->library('user_agent');
		
    }
	
	function log_activity($query, $action, $ip_addr=''){
		if($ip_addr === ''){
			$ip_addr = $this->input->ip_address();
		}
		
		if ($this->agent->is_browser()){
			$ua = $this->agent->browser().' '.$this->agent->version();
		}
		elseif ($this->agent->is_robot()){
			$ua = $this->agent->robot();
		}
		elseif ($this->agent->is_mobile()){
			$ua = $this->agent->mobile();
		}
		else{
			$ua = 'Unidentified User Agent';
		}

		
		$data = array(
			'ip_address'	=>	$ip_addr,
			'action'		=> $action,
			'query'			=>	$query,
			'browser'		=> $ua,
			'is_mobile'		=> $this->agent->is_mobile(),
			'user_agent'	=> $this->agent->agent_string()
		);
		
		$this->db->insert('tbl_log', $data);
	}	
}
