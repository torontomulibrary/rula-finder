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
class Model_Admin extends CI_Model {
	function get_all_floors(){
		
		return $this->db->query('SELECT * FROM tbl_floors ORDER BY CAST(floor_name as DECIMAL), floor_name ');
	}
	
	function get_floor_data($floor){
		return $this->db->query("SELECT loc.*, itype.type_id, itype.is_stack ".
								"FROM tbl_location loc, tbl_item_type itype ".
								"WHERE 	loc.type_id = itype.type_id AND ".
								"loc.floor_id = ".$this->db->escape($floor));
	}
	
	function get_stack_data($loc_id){
		$this->db->where('loc_id', $loc_id);
		return $this->db->get('tbl_stack_range');
	}
	
	function get_item_types(){
		$this->db->order_by('type_id');
		return $this->db->get('tbl_item_type');
	}

	function get_stack_types(){
		$this->db->order_by('stack_type_id');
		return $this->db->get('tbl_stack_type');
	}

	function insert_location($data){
		$this->load->helper('call_number');
		
		//Check for blank data, prevent insert if data is missing
		foreach($data as $x){
			if($x === false){
				return FALSE;
			}
		}
		
		//Create the location of the stack
		$ins_data = array(
			'floor_id' 			=> $data['floor'], 		
			'type_id'			=> $data['item_type'],
			'loc_name'			=> $data['loc_name'],
			'map_x' 			=> $data['left'],
			'map_y' 			=> $data['top'],
			'height' 			=> $data['height'],
			'width'				=> $data['width'],
		);
		$this->db->insert('tbl_location', $ins_data);
		
		$insert_id = $this->db->insert_id();
		
		//Check to see if location being inserted is a stack
		$this->db->where('type_id', $data['item_type']);
		$is_stack_query = $this->db->get('tbl_item_type');
		$is_stack_result = $is_stack_query->row();
		
		//If item is a stack
		if($is_stack_result->is_stack == 1){
			//Create the stack range
			$ins_data = array(
				'loc_id' 			=> $insert_id, 
				'stack_type_id'		=> $data['stack_type'],
				'call_range_start' 	=> $data['call_start'],
				'call_range_end' 	=> $data['call_end'],
				'call_int_start' 	=> convert_callnum($data['call_start']),
				'call_int_end'		=> convert_callnum($data['call_end']),

			);
			$this->db->insert('tbl_stack_range', $ins_data);
		}
			
		return $insert_id;
	}
	
	function delete_stack($stack_id){
		$this->db->where('loc_id', $stack_id);
		$this->db->delete('tbl_location');
	}
	
	
	//NEEDS TO BE FIXED OR SOMETHING
	function update_stack($data){
		$this->load->helper('call_number');
		
		//Check for blank data, prevent insert if data is missing
		foreach($data as $x){
			if($x === false){
				return FALSE;
			}
		}
		
		//Create the location of the stack
		$ins_data = array(
			'floor_id' 			=> $data['floor'], 		
			'type_id'			=> $data['item_type'],
			'loc_name'			=> $data['loc_name']
		);
		
		$this->db->where('loc_id', $data['loc_id']);
		$this->db->update('tbl_location', $ins_data);
		
		
		//Check to see if location being inserted is a stack
		$this->db->where('type_id', $data['item_type']);
		$is_stack_query = $this->db->get('tbl_item_type');
		$is_stack_result = $is_stack_query->row();
		
		//If item is a stack
		if($is_stack_result->is_stack == 1){
			//Create the stack range
			$ins_data = array(
				'stack_type_id'		=> $data['stack_type'],
				'call_range_start' 	=> $data['call_start'],
				'call_range_end' 	=> $data['call_end'],
				'call_int_start' 	=> convert_callnum($data['call_start']),
				'call_int_end'		=> convert_callnum($data['call_end']),

			);
			
			$this->db->where('loc_id', $data['loc_id']);
			$this->db->update('tbl_stack_range', $ins_data);
		}

		
	}
	
	function search_stats($date_start, $date_end, $type='', $email=FALSE){
		$sql = "SELECT MONTHNAME(a.date_time) as month, count(IF(a.action='search',1,NULL)) AS search, count(IF(a.action = 'display',1,NULL)) AS display"; if($email===TRUE) $sql.= ", count(IF(a.action = 'email',1,NULL)) AS email";
		$sql.="	FROM tbl_log a
				WHERE CAST(date_time AS DATE) >= '".$date_start."' AND CAST(date_time AS DATE) <= '".$date_end."'";
				if($type=="desktop") $sql .= " AND is_mobile=0 ";
				if($type=="mobile") $sql .= " AND is_mobile=1 ";
				$sql .= "GROUP BY month ORDER BY date_time DESC";
		
		return $this->db->query($sql);	
	}
	
	function top_ten_searches($date_start, $date_end){
		$sql = "SELECT count(*) as count, query from tbl_log where action='search' and CAST(date_time AS DATE) BETWEEN '".$date_start."' AND '".$date_end."' group by query order by count desc limit 10";
		
		return $this->db->query($sql);
	
	}
	
	function hits_by_device_type($date_start, $date_end){
		// $sql = "select a.mobile, b.desktop from (
					// select count(*) as mobile from (select * from tbl_log group by ip_address) m where is_mobile = 1 AND CAST(date_time AS DATE) BETWEEN '".$date_start."' AND '".$date_end."'
				// ) a, (
					// select count(*) as desktop from (select * from tbl_log group by ip_address) m2 where is_mobile = 0 AND CAST(date_time AS DATE) BETWEEN '".$date_start."' AND '".$date_end."'
				// ) b";
				
		$sql = "select a.mobile, b.desktop from (
					select count(*) as mobile from tbl_log where is_mobile = 1 AND action='display' AND CAST(date_time AS DATE) BETWEEN '".$date_start."' AND '".$date_end."'
				) a, (
					select count(*) as desktop from tbl_log where is_mobile = 0 AND action='display' AND CAST(date_time AS DATE) BETWEEN '".$date_start."' AND '".$date_end."'
				) b";		
				
		return $this->db->query($sql);	
	}
	
	
}