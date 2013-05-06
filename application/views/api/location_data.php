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
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
	//header('Content-type: application/json');
	header('Content-type: text/plain');
	
		
	$returnJSONArray = array("status"=>$book_data[0]);
	
	if(isset($book_data[1]) && is_array($book_data[1])) $returnJSONArray['data'] = $book_data[1]; 
	
	if(isset($_GET['callback'])) echo $_GET['callback'];
	echo '(' .  json_encode($returnJSONArray) . ')';
	
	
?>