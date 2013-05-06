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
<!DOCTYPE html> 
<html> 
	<head> 
	<title>Find Books &amp; Media</title> 
	
	<meta name="viewport" content="width=device-width, initial-scale=1"> 

	<link rel="stylesheet" href="<?= base_url() ?>js/libs/jquery_mobile/jquery.mobile-1.1.0.min.css" />
	<script src="<?= base_url() ?>js/libs/jquery-1.7.2.min.js"></script>
	<script src="<?= base_url() ?>js/libs/jquery_mobile/jquery.mobile-1.1.0.min.js"></script>
	
	<script>
		var page = 1;
		var term= "";
		var maxPages;
		var post_data = "<?= $post ?>";
		
		$(function () {
		   
		   $(document).ready(function(){
				if(post_data.length > 0){
					$('#q').val(post_data);
					$('#f').submit();
				}
		   });
		   
		   var recordCount;
		
		   //***** HANDLE BUTTONS ********
		   $('#f').live('submit', function (e){
				$.mobile.showPageLoadingMsg();
				
				$('#page_btns').empty();
				$('#message').empty();
				page = 1;
				doSearch(e);
				
				if(recordCount > 0){
					$('<a href="#" data-role="button" id="next_page">Next</a>').appendTo('#page_btns');
					$('div[data-role=content]').trigger('create');
					
					if(page == maxPages){
						$('#page_btns').empty();
						
						if(page != 1){
							$('<a href="#" data-role="button" id="prev_page">Previous</a>').appendTo('#page_btns');
						}
						$('div[data-role=content]').trigger('create');
					}
				}
				else{
					$('#message').append("<h3 style=\"text-align: center\">Your search for \"" + $('#q').val() + "\" returned no results</h3>");
				}
				
				$.mobile.hidePageLoadingMsg();
		   });
		   
		   $('#next_page').live('click', function (e){
				$.mobile.showPageLoadingMsg();
				page += 1;
				doSearch(e);
				
				$('#page_btns').empty();
				$('<a href="#" data-role="button" id="prev_page">Previous</a>').appendTo('#page_btns');
				$('<a href="#" data-role="button" id="next_page">Next</a>').appendTo('#page_btns');
				$('div[data-role=content]').trigger('create');
				$.mobile.hidePageLoadingMsg();
		   });
		   
		   $('#prev_page').live('click', function (e){
				$.mobile.showPageLoadingMsg();
				page -= 1;
				doSearch(e);
				
				if(page == 1){
					$('#prev_page').remove();
					$('div[data-role=content]').trigger('create');
				}
				$.mobile.hidePageLoadingMsg();
		   });
		   //***** END HANDLE BUTTONS *******
		   
		   //Call the search API and update the page with the results
		   function doSearch(e) {
				e.preventDefault();
				
				if($.trim($('#q').val()).length > 0){
					term = $('#q').val(); 
					$('#results_list').empty();
					
					var params = "s.q=" + encodeURIComponent(term) + "&page="+page+"&callback=?";
					
					$.ajax({
						url: "<?= base_url() ?>search/proxy",						
						data: params,
						dataType: 'json',
						async: false,
						type: 'post',
						success: function(data) {
							
							recordCount = data.recordCount;
							
							var $r = $('#results_list');
							
							for (var i = 0; i < data.documents.length; i++) {
								var d = data.documents[i];
								maxPages = data.pageCount;
								
								if (!d.LCCallNum) continue;
								
								var s = "<li>";
			
								function fold(arr) {
									var f = "";
									$.each(arr, function (idx, el) {
										if (idx > 0) f += ", ";
										f += el;
									});
									return f;
								}

								if (d.link){
									var search ="";
									if(typeof(d.ExternalDocumentID) != 'undefined'){
										search = d.ExternalDocumentID;
										search = search.toString().substring(0,8);
									}
									else search = d.LCCallNum;
									s+= '<a rel="external" href="<?= base_url(); ?>#s=' + search + '">';
								}

								if (d.thumbnail_m){
									s += "<img src=\""+d.thumbnail_s+"\" alt=\"Book Jacket\" />";
								}
								else{
									s += "<img src=\"<?= base_url(); ?>img/no-img.gif\" alt=\"Book Jacket\" />";
								}

								s += "<h3>"+d.Title[0]+"</h3>";
								
								s += "<p>";
								
								if (d.Author){
									s += fold(d.Author);
								}
								
								if (d.Library) s += "<br />"+d.Library;
								//if (d.LCCallNum) s += "<br />"+d.LCCallNum;	
								s += "</p>";
								
								s += "</a></li>";
								$r.append(s);
								$('ul').listview('refresh');
							}
						}
					});
				}	
				
				return false;
			}
		});
		
	</script>

</head> 
<body> 

<div data-role="page">

	<div data-role="header" data-theme="c" >
		<div style="border:0;outline:none"><a href="http://library.ryerson.ca"><img src="<?= base_url() ?>img/rula-sma.png" height="60" style="padding: 0.5em" /></a></div>
	</div>

	<div data-role="content">	
		
		<form id="f" action="#" data-ajax="false">
			Search for: <input id="q" name="q" type="text" x-webkit-speech />
			<input type="submit" id="search_submit" value="Search" />
		</form>
		
		<br />
		
		<ul id="results_list" data-role="listview">

		</ul>
		
		<br />
		
		<div id="message"></div>
		<div data-role="controlgroup" data-type="horizontal" id="page_btns"></div>
		
	</div><!-- /content -->

	<div data-role="footer" data-theme="b" data-position="fixed">
		<h4>&copy; Ryerson University Library</h4>
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>