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
<? //HEADER DATA ?>
<?php ob_start();?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart", "table"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        drawTable();
		draw_monthly();
		mobile_desktop();
		desktop_usage();
		mobile_usage();
      }
	  
	  function drawTable(){
		var data = new google.visualization.DataTable();
        data.addColumn('string', 'Search String');
		data.addColumn('number', 'Number of Searches');
		
        
      
        data.addRows([
         <?php
			foreach($top_search->result() as $row){
				print "['".addslashes($row->query)."',".$row->count."],";
			}
		  ?>
        ]);

        var table = new google.visualization.Table(document.getElementById('table_div'));
        table.draw(data, {showRowNumber: true});

	  }
	  
		function draw_monthly(){
			var data = google.visualization.arrayToDataTable([
			  ['Month', 'Searches', 'Location\'s Mapped', 'Emails Sent'],
			  
			  <?php
				foreach($stats->result() as $row){
					print "['".$row->month."',".$row->search.",".$row->display.", ".$row->email."],";
				}
			  ?>
			  
			]);

			var options = {
			  title: 'RULA Bookfinder Usage',
			 
			};

			var chart = new google.visualization.BarChart(document.getElementById('monthly_usage'));
			chart.draw(data, options);
		}
		
		function mobile_desktop() {
			var data = google.visualization.arrayToDataTable([
				['Task', 'Hours per Day'],
				<?
					foreach($device->result() as $row){
						print "['Desktop', ".$row->desktop ."],";
						print "['Mobile', ".$row->mobile ."],";
						
					}
				?>
			]);
			
			var options = {
				title: 'Desktop Vs. Mobile'
			};
			
			var chart = new google.visualization.PieChart(document.getElementById('desk_mobile'));
			chart.draw(data, options);
		}
		
		function desktop_usage(){
			var data = google.visualization.arrayToDataTable([
			  ['Month', 'Searches', 'Location\'s Mapped'],
			  
			  <?php
				foreach($desktop_stats->result() as $row){
					print "['".$row->month."',".addslashes($row->search).",".$row->display."],";
				}
			  ?>
			  
			]);

			var options = {
			  title: 'RULA Desktop Bookfinder Usage',
			 
			};

			var chart = new google.visualization.BarChart(document.getElementById('desk_act'));
			chart.draw(data, options);
		}
		
		function mobile_usage(){
			var data = google.visualization.arrayToDataTable([
			  ['Month', 'Searches', 'Location\'s Mapped'],
			  
			  <?php
				foreach($mobile_stats->result() as $row){
					print "['".$row->month."',".$row->search.",".$row->display."],";
				}
			  ?>
			  
			]);

			var options = {
			  title: 'RULA Mobile Bookfinder Usage',
			 
			};

			var chart = new google.visualization.BarChart(document.getElementById('mobile_act'));
			chart.draw(data, options);
		}
		
		
	  
		$(function() {
			$( ".datepicker" ).datepicker({
				dateFormat: 'yy-mm-dd'
			});
		});
    </script>

<?php $head = ob_get_contents();ob_end_clean();$this->template->set('headers', $head);?>

<? //BODY DATA ?>
<?php ob_start();?>

<h3>Set Date Range</h3>
<form method="POST">
	<?php
		//Set the default start/end date
		if(!isset($start)){
			$start = date('Y-m-d',mktime(0, 0, 0, date("m")-3  , date("d"), date("Y")));
		}
		if(!isset($end)){
			$end = date('Y-m-d');
		}
	?>
	Start: <input type="text" class="datepicker" name="date_start" value="<?=$start ?>"> - End: <input type="text" class="datepicker" name="date_end" value="<?= $end ?>">
	<input type="submit" value="Set" />
</form>

<br /><br /><br /><hr />
<h2>Overall Usage</h2>


<h3>Usage by Month:</h3>
<div id="monthly_usage" style="width: 900px; height: 500px;"></div>
<br />


<h3>Desktop vs. Mobile</h3>
<div id="desk_mobile" style="width: 900px; height: 500px;"></div>
<br />

<br />
<h3>Top 10 Searches:</h3>
<div id="table_div" style="width: 500px; height: 500px;"></div>


<hr />
<h2>Desktop Usage:</h2>
<br /><br />

<h3>Desktop Activity</h3>
<div id="desk_act" style="width: 900px; height: 500px;"></div>
<br />

<hr />	
<h2>Mobile Usage:</h2>	
<h3>Mobile Activity</h3>
<div id="mobile_act" style="width: 900px; height: 500px;"></div>
<br />
<?php $content = ob_get_contents();ob_end_clean();$this->template->set('content', $content);?>
		