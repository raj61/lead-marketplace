jQuery(document).ready(function( $ ) {
/***Listing type***/
	/**$(".wp-submenu-head").each(function()
	{**/
		//alert($(".wp-submenu-head:contains('Listings')").text());
   /** });**/
	$("#edugorilla_list_date_from").datepicker({ dateFormat: 'yy-mm-dd' });
	$("#edugorilla_list_date_to").datepicker({ dateFormat: 'yy-mm-dd' });
	if($(".wp-submenu-head:contains('Listings')").text() == "Listings")
    {
    	$(".wp-submenu-head:contains('Listings')").parent().find("li").each(function(){
        	if($(this).text() != "Listings")
            {
            	var id=$(this).find("a").attr("href").replace("edit.php?post_type=", " ").trim();
            	$('#edugorilla_listing_type').append($("<option></option>").attr("value",id).text($(this).find("a").text()));
            }
        });
  				 
    }


	$(document).on("change","#edugorilla_listing_type",function(){
    	if($(this).val() !== "")
        {
        	$("#edugorilla_keyword").removeAttr("disabled");
        	$("#edugorilla_category").removeAttr("disabled");
        	$("#edugorilla_location").removeAttr("disabled");
        }
    	else
        {
        	$("#edugorilla_keyword").attr("disabled","disabled");
        	$("#edugorilla_category").attr("disabled","disabled");
        	$("#edugorilla_location").attr("disabled","disabled");
        }

    });

$(document).on('click','#edugorilla_filter',function(){
		var institute_data;
		     
		var ptype = $("#edugorilla_listing_type").val();
		var keyword = $('#edugorilla_keyword').val();
		var location = $('#edugorilla_location').val();
		var category = $("#edugorilla_category").val();
		
    		$.ajax({
            	url: ajaxurl,
                type: 'GET',
         		data: {
            			'action':'edugorilla_show_location',
            			'ptype': ptype,
                		'term': keyword,
                        'address' : location,
                		'category': category
          			  },
                dataType: 'json',
                beforeSend: function()
            	{
                },
         		success: function(data) 
            	{
                	var cnfbox = "<table class='widefat fixed' align='center' width='100%' border=1><tr><th>Institude Name</th><th>Email(s)</th><th>SMS(s)</th><th>Flag</th></tr>";
                
                	$("#edugorilla_institute_datas").val(JSON.stringify(data));
                	
                	 if($("#edu_name").val() !== "" && $("#edu_email").val() !== "" && $("#edu_contact_no").val() !== "" && $("#edu_query").val() !== "" &&$("#edugorilla_institute_datas").val() !== "")
                    {
                        $('#save_details_button').attr("rel","modal:open");
                        $('#save_details_button').removeAttr("disabled");
                    }
                var temppoints = [];
                var i= 0;
                $.each(data,function(i,v){
                	temppoints[i] = v.lat;
                	temppoints[i+1] = v.long;
                	i++;
                });
                	
                	var points = {lat: parseFloat(temppoints[0]), lng: parseFloat(temppoints[1])};
        			 	var map = new google.maps.Map(document.getElementById('map'), {
         					 zoom: 10,
        				 	center: points
       				 	});
                
                 var infowindow = new google.maps.InfoWindow();
                	$.each(data,function(i,v){
                    
                    cnfbox += "<tr><td>"+v.title+"</td><td>"+v.emails+"</td><td>"+v.phones+"</td><td>"+v.flag+"</td></tr>";
                    	
        				var marker = new google.maps.Marker({
          						position: new google.maps.LatLng(v.lat, v.long),
         						map: map
        				});
                    	
                    	google.maps.event.addListener(marker, 'click', (function(marker, i) {
       							 return function() {
       									   infowindow.setContent('Institute Name: <b>'+v.title+"</b><br>Address: <b>"+v.address+"</b><br>Latitude <b>"+v.lat+"</b><br>Longitude <b>"+v.long+"</b><br>Flag <b>"+v.flag+"</b>");
      									    infowindow.open(map, marker);
      							  }
      					})(marker, i));
                  
                    	
                    });
         
					cnfbox += "</table><center><button id='confirm' onclick='document.details.submit();'>Confirm</button></center>";
               		$("#confirmation").html(cnfbox);
                },
                error: function(err)
            	{
                	console.log(err);
                }
            });
					 
});


	$('#edugorilla_category').select2({placeholder: 'Select category'});
	$('#edugorilla_location').select2();
	$( "#tabs" ).tabs();
	$( "#list-tabs" ).tabs();
    
       
	
});