<?php echo '<script>var loggedin = ' . ($this->session->userdata('site_logintype') == 'users' ? 'true' : 'false') . ';</script>' ?>
<?php echo '<script>var loginurl = "' . site_url('network/login/users') . '";</script>' ?>
<?php echo '<script>var companycommentsurl = "' . site_url('company/getcompanycomments') . '";</script>' ?>
<?php echo '<script>var addpoquoteurl = "' . site_url('site/addpoquote') . '";</script>' ?>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<!--  <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
		<script type="text/javascript" src="http://cdnjs.cloudflare.com/ajax/libs/hammer.js/1.0.5/jquery.hammer.min.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/admin/js/FlameViewportScale.js"></script>
		<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/admin/js/jquery.tosrus.min.all.js"></script>
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jquery.tosrus.all.css" />

		<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/admin/js/jquery.bxslider.min.js"></script>
		<link type="text/css" media="all" rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jquery.bxslider.css" />

		<link type="text/css" rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jquery.fancybox.css?v=2.1.5" title="Featherlight Styles" />
		<script src="<?php echo base_url();?>templates/admin/js/jquery.fancybox.pack.js?v=2.1.5" type="text/javascript" charset="utf-8"></script>

		<script type="text/javascript" language="javascript">
			jQuery(function( $ ) {

				$('#example-1 a').tosrus({
					pagination	: {
						add			: true,
						type		: 'thumbnails'
					}
				});

			});
		</script>

		<script type="text/javascript" language="javascript">
			jQuery(function( $ ) {
			 $(".bxslider").hide();	
			 $('.bxslider').bxSlider({
			 adaptiveHeight: true,
  			 mode: 'fade',
  			 auto: true,
  			 onSliderLoad : function(){
               $(".bxslider").fadeIn();
            }
			});
			});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
							
			});
		</script>

<?php
/*
  $geocode=file_get_contents("http://maps.google.com/maps/api/geocode/json?address=".urlencode(str_replace("\n",", ",$supplier->address))."&sensor=false");
  $output= json_decode($geocode);
  print_r($output);
  $lat = $output->results[0]->geometry->location->lat;
  $long = $output->results[0]->geometry->location->lng;
 */
$lat = $contractor->user_lat;
$long = $contractor->user_lng;
?>


<link type="text/css" media="all" rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/userdefine.css" />		


<!-- <script type="text/javascript" src="https://api.github.com/repos/twbs/bootstrap?callback=callback"></script> -->
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<script>
    $(document).ready(function() {
        InitMap();
        $("#day").datepicker();
        $("#time").timepicker({
            'minTime': '6:00am',
            'maxTime': '11:30pm',
            'showDuration': false
        });        
    });

    function setlabel()
    {
        $type = $("#requresttype").val();
        if($type=='Request Phone Assistance')
        {
            $("#daytd").html('Best Day To Call');
            $("#timetd").html('Best Time To Call');
        }
        else
        {
            $("#daytd").html('Appointment Date Requested');
            $("#timetd").html('Appointment Time Requested');
        }

    }

    function InitMap() {
        google.maps.event.addDomListener(window, 'load', LoadMapProperty);
    }

    function LoadMapProperty() {
        var locations = new Array(
                [<?php echo $lat; ?>,<?php echo $long; ?>]
                );
        var markers = new Array();
        var mapOptions = {
            center: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $long; ?>),
            zoom: 14,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        };

        var map = new google.maps.Map(document.getElementById('property-map'), mapOptions);

        $.each(locations, function(index, location) {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(location[0], location[1]),
                map: map,
                //icon: 'http://html.realia.byaviators.com/assets/img/marker-transparent.png'
                icon: '<?php echo base_url(); ?>templates/site/assets/img/marker.png'
            });

            var myOptions = {
                content: '<div class="infobox"><div class="image"><img src="<?php if($contractor->logo) { echo base_url(); ?>uploads/logo/thumbs/<?php echo $contractor->logo; } else { echo base_url(); ?>templates/site/assets/img/default/big.png <?php } ?>" alt="" width="100"></div><div class="title"><a href=""><?php echo $contractor->companyname; ?></a></div><div class="area"><div class="price">&nbsp;</div><span class="key"><?php echo $contractor->fullname; ?><br/><?php echo $contractor->city.",&nbsp;".$contractor->state; ?></span><span class="value"></span></div></div>',
                disableAutoPan: false,
                maxWidth: 0,
                pixelOffset: new google.maps.Size(-146, -190),
                zIndex: null,
                closeBoxMargin: "",
                closeBoxURL: "http://www.google.com/intl/en_us/mapfiles/close.gif",
                boxStyle: {
                    background: "#fff"
                    ,opacity: 1
                   },
                infoBoxClearance: new google.maps.Size(1, 1),
                position: new google.maps.LatLng(location[0], location[1]),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false
            };
            marker.infobox = new InfoBox(myOptions);
            marker.infobox.isOpen = false;

            var myOptions = {
                draggable: true,
                content: '<div class="marker"><div class="marker-inner"></div></div>',
                disableAutoPan: true,
                pixelOffset: new google.maps.Size(-21, -58),
                position: new google.maps.LatLng(location[0], location[1]),
                closeBoxURL: "",
                isHidden: false,
                // pane: "mapPane",
                enableEventPropagation: true
            };
            marker.marker = new InfoBox(myOptions);
            marker.marker.open(map, marker);
            markers.push(marker);

            google.maps.event.addListener(marker, "click", function(e) {
                var curMarker = this;

                $.each(markers, function(index, marker) {
                    // if marker is not the clicked marker, close the marker
                    if (marker !== curMarker) {
                        marker.infobox.close();
                        marker.infobox.isOpen = false;
                    }
                });

                if (curMarker.infobox.isOpen === false) {
                    curMarker.infobox.open(map, this);
                    curMarker.infobox.isOpen = true;
                    map.panTo(curMarker.getPosition());
                } else {
                    curMarker.infobox.close();
                    curMarker.infobox.isOpen = false;
                }

            });
        });
    }
</script>
<script type="text/javascript">


    function PrintElem(elem)
    {
        PopupPrint($(elem).html());
    }
    function PopupPrint(data)
    {
        var mywindow = window.open('', 'my div', 'height=500,width=500,left=100,top=100');
        mywindow.document.write('<html><head><title>my div</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();
        return true;
    }
</script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script>
$(function() {
$( document ).tooltip();
});

function changetab(tabname){
		if(tabname == 'walltab'){
			$('.nonfbdiv').css('display','none');
			$("#button").css("background-color","#00bbe4"); 
			$(".titlebox").css("background-color","#00bbe4"); 
			$(".page-header").css("background-color","#00bbe4"); 
			$('#fbwall').css('display','block');
		}else{
			$('.nonfbdiv').css('display','block');
			$("#button").css("background-color","#06a7ea"); 
			$(".titlebox").css("background-color","#06a7ea"); 
			$(".page-header").css("background-color","#06a7ea"); 
			$('#fbwall').css('display','none');
		}		
	}
</script>
        
                          
<script type="text/javascript">	
	
	$(document).ready(function (e) {
		$("#fbwallform").on('submit',(function(e) {
		e.preventDefault();
		for ( instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement();
    	}
		$.ajax({
		url: "<?php echo site_url('company/savefbwall')?>",
		type: "POST",
		data: new FormData(this),
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		success: function(data)
		{
			showcomments();
			for ( instance in CKEDITOR.instances ) {
        		CKEDITOR.instances[instance].setData('');
			}
		},
		error: function()
		{
		}
		});
		}));
		
		
		showcomments();

	});
  
	
	function showcomments(){
		
		var companyid = $("#companyid").val();
		d = "companyid="+companyid;		
		$("#commentdiv").html('');
		$.ajax({
			type: "post",
			url: companycommentsurl,
			dataType: 'json',
			data: d
		}).done(function(data) {
			var htmlcomment = "";			
			$.each(data,function(id,comment){			
				//alert(comment.message);			
				htmlcomment = '<div class="purchaser"><div class="pull-left" style="width:20%;"><p style="text-align:center"><img width="50px" height="80px" src="'+comment.logosrc+'"/></p></div><div class="pull-right" style="width:79%;"><p><strong>'+comment.name+'</strong></p><p>'+comment.message+'</p><br><p>Commented:'+comment.showago+'&nbsp;&nbsp;&nbsp; on:'+comment.showdate+'&nbsp;&nbsp;<input type="button" class="btn btn-primary btn-xs" style="border-radius: 10px;" value="Reply" name="'+comment.id+'" id="'+comment.id+'" onclick="setreplyid(this.id,\''+comment.from+'\',\''+comment.from_type+'\');"></p><br/><div id="replydiv'+comment.id+'" style="display:none;"><textarea rows="2" cols="5" class="form-control ckeditor" id="replysection'+comment.id+'" name="replysection'+comment.id+'"></textarea>&nbsp;<input type="submit" value="Comment" class="btn btn-success btn-xs" name="send'+comment.id+'" id="send'+comment.id+'"></div></div><div style="clear:both;"></div></div>';
				$("#commentdiv").append(htmlcomment);
				var htmlcomment2 = "";
				if( comment.innercomment !== undefined ){ 
					$.each(comment.innercomment,function(id2,comment2){
						htmlcomment2 = '<div style="margin-top:4px;margin-left:96px;"><div class="purchaser"><div class="pull-left" style="width:20%;"><p style="text-align:center"><img width="50px" height="80px" src="'+comment2.logosrc+'"/></p></div><div class="pull-right" style="width:79%;"><p><strong>'+comment2.name+'</strong></p><p>'+comment2.message+'</p><br><p>Commented:'+comment2.showago+'&nbsp;&nbsp;&nbsp; on:'+comment2.showdate+'<!--&nbsp;&nbsp;<input type="button" value="Reply" name="'+comment2.id+'" id="'+comment2.id+'" onclick="setreplyid(this.id,\''+comment2.from+'\',\''+comment2.from_type+'\');"></p><br/><div id="replydiv'+comment2.id+'" style="display:none;"><textarea rows="2" cols="5" class="form-control ckeditor" id="replysection'+comment2.id+'" name="replysection'+comment2.id+'"></textarea>&nbsp;<input type="submit" value="Send" class="btn btn-success btn-xs" name="send'+comment2.id+'" id="send'+comment2.id+'">--></div></div><div style="clear:both;"></div></div></div>';
						$("#commentdiv").append(htmlcomment2);
					});
				}

			});
			//$("#targetLayer").html(htmlcomment);
			//$("#commentsection").html('');
		});
		
	}
  
	function setreplyid(id,from,from_type){
		$('#replydiv'+id).css('display','block');
		$('#messageto').val(from_type);
		$('#receiverid').val(from);
		$('#reply').val(id);
	}
	
	

	/*function sendreply(){
		alert("fffff");	
		$("#fbwallform").on('submit',(function(e) {
		alert('gggggg');	
		e.preventDefault();
		$.ajax({
		url: "<?php echo site_url('company/savefbwall')?>",
		type: "POST",
		data: new FormData(this),
		mimeType:"multipart/form-data",
		contentType: false,
		cache: false,
		processData:false,
		success: function(data)
		{
			showcomments();
		},
		error: function()
		{
		}
		});
		}));
		
		
		//showcomments();
	}*/
	
</script>



<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<HEAD>		
		<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
		<meta http-equiv="cache-control" content="no-cache"> <!-- tells browser not to cache -->
		<meta http-equiv="expires" content="0"> <!-- says that the cache expires 'now' -->
		<meta http-equiv="pragma" content="no-cache"> <!-- says not to use cached stuff, if there is any -->
		<meta property="fb:app_id" content="899376703411658"/> <!-- Facebook App ID for comment system  -->		
		<meta property="og:image" content="<?php if(isset($designbook[0]->imagename)) echo site_url('uploads/designbook/'.$designbook[0]->imagename);?>"/>		
	</HEAD>	
	<BODY>

<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=899376703411658&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<form id="supplierform" method="post" action="<?php echo site_url('site/suppliers')?>">
	<input type="hidden" id="typei" name="typei"/>
</form>
 
<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">                             
	               <div style="background-color:#dff0d8;color:black;font-weight:bolder;border-radius:8px;text-align:center;">
	                  
	               </div>
              	 <div>
              		
	           	                	
            </div><!-- End of row -->
                 <h3 class="titlebox">
                	<table width="100%">
                    	<tr>
                        	<td align="left"><a href="#" onclick="changetab('suppliertab');">                       	
                        	<h2 class="page-header" style="padding:0px 0px 0px 7px"><?php echo $contractor->companyname;?></h2></a></td>
                        	<td align="right">
                            	<!-- AddThis Button BEGIN -->
                            	<div class="addthis_toolbox addthis_default_style ">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>
                            	</div>
                            	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>
                        	</td>
                    	</tr>
                    </table>
                    </h3>
                    
                
               
                  
                  <div class="nonfbdiv">
                    <div class="property-detail">                      
                        <div class="pull-left overview effect5" style="float:left;">
                            <div class="row">
                                <div class="span4" id="mydiv">
                                  <div style="text-align:center">
                                        <?php if($contractor->logo !=""){?>
                                                <img  src="<?php echo site_url('uploads/logo/'.$contractor->logo);?>" class="img-responsive" width="200" height="100" alt="Company Logo"/>
                                                <?php } else {?>
                                          <img src="<?php echo base_url(); ?>templates/site/assets/img/logo.png" class="img-responsive" width="200" height="100" alt="company logo"/>
                                        <?php } ?>
										
                                   </div><br />
                                    <h2 class="name"><?php echo $contractor->companyname;?> </h2>
                                    <table class="table" width="100%" style="font-size: 12px;">
                                        <tr>
                                            <td>Join Date:</td>
                                            <td><?php echo date('m/d/Y',strtotime($contractor->regdate)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Location:</td>
                                            <td><?php echo nl2br($contractor->address); ?></td>
                                            
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><?php echo $contractor->email; ?></td>
                                            
                                        </tr>
                                        <?php if($contractor->phone){?>
                                        <tr>
                                            <td>Tel:</td>
                                            <td><?php echo $contractor->phone; ?></td>
                                             
                                        </tr>
                                        <?php }?>
                                        <?php if($contractor->fax){?>
                                        <tr>
                                            <td>Fax:</td>
                                            <td><?php echo $contractor->fax; ?></td>
                                             
                                        </tr>
                                        <?php }?>
                                        <tr>
                                            <td width="27%">Contact Person:</td>
                                            <td><?php echo $contractor->fullname; ?></td>
                                             
                                        </tr>
                                    </table>
                                </div>
                            </div><!-- End of row of effect 5-->
                            <br/>
                            
                              <div class="invoice-button-action-set">
                                  <p style="float: left;line-height: 17px;">
                                      <button type="button" class="btn btn-primary" onclick="PrintElem(mydiv)" style="border-radius: 2px;padding: 0 10px;">
                                        Print
                                      </button>
                                </p>
                              </div>
                        </div><!-- End of pull-left overview effect5 -->

                   <?php  if(isset($image) && count($image)>0) { ?>
                     <div style="float:left; height:310px;width:395px;">
                        <ul class="bxslider">
						   <?php foreach($image as $items) { ?>
						  <li>
						  <a class="fancybox" rel="group" href="<?php echo site_url('uploads/ContractorImages/'.$items->image);?>">
						 <img src="<?php echo site_url('uploads/ContractorImages/'.$items->image);?>" /></a>
						  </li>
						  <?php } ?>
						</ul>
                      </div>
                   <?php }   ?>
                  
                
                         <div class="row expe" style="margin-left:-8px !important;padding-top:10px;width:100%;">
                            <?php echo $contractor->about; ?>
                        </div>
                     
                       
                        <?php  if(isset($gallery) && count($gallery)>0) { ?>
					 <div class="content">
                         <h3 class="titlebox" style="padding:0px 0px 0px 8px">Gallery</h3>
                          <div id="example-1" class="thumbs">
							<div class="gallery-photos clearfix">
								  <div class="photo-thumbnails">
								   <?php  if(isset($gallery) && count($gallery)>0)  foreach($gallery as $items) { ?>
								    <div class="thumbnail current">
								      <div class="thumbnail-inner">
									      <a href="<?php echo site_url('uploads/ContractorGallery/'.$items->image);?>">
									      <img src="<?php echo site_url('uploads/ContractorGallery/'.$items->image);?>"/>
									      </a>
								      </div>
								    </div>
								     <?php }   ?>
								  </div>
							</div>
							</div>
						</div>
					<?php }   ?>

					
                        <?php if(@$members){?>
                        <div>

                              <h3 class="titlebox" style="padding:0px 0px 0px 8px; margin:0px;">
                           Meet The Team</h3>
                           <div class="meet_team" style="border-bottom-style:none;border-left-style:none;border-right-style:none;">
                            <table>

                            	<?php $key = 0; foreach($members as $member){?>
                            	<?php if($key==0){?>
                            	<tr>
                            	<?php }?>

                            	<td style="position:relative;">

                            
  <div>
  <div style="height:40px;width:200px;"><h1 style="overflow:auto;"><?php echo $member->name;?>&nbsp;<?php echo $member->title;?></h1></div>
  <div style="height:180px;width:200px;overflow:hidden;"><img src="<?php echo base_url("uploads/ContractorTeam/".$member->picture);?>" alt="Profile Image"/></div>
  <div style="height:40px;width:200px;"><h2 style="overflow:auto;font-family: Arial, Helvetica, sans-serif;font-size:15px;"><?php echo $member->phone;?><br/><?php echo $member->email;?></h2></div>
 </div>  

</td>



                            	<?php if($key==4){ $key=0; ?>
                            	</tr>


                            	<?php } $key++;?>
                            	<?php }?>
                            	</tr>
                            </table>
                           </div>
                        </div>
                        <?php }?>				


						<br/>
                       
                      <h3 class="titlebox" style="padding:0px 0px 0px 8px">Map
                        <a name="map" id="map"></a>
                        <?php $addressarray = explode(" ",$contractor->address);
                        		$i=1;
                        		$addresslink = "";
                        		foreach($addressarray as $add){
                        			if(count($addressarray)>$i)
                        				$addresslink .= $add."+";
                        			else
                        				$addresslink .= $add;
                        			$i++;
                        		}

                        ?>
                        <span style=" color: #fff;float: right;padding: 0 10px 0 8px;">
                        <a target="_blank" style="color:#fff" href="<?php echo 'https://maps.google.com/maps?daddr='.$addresslink; ?>">Driving Directions</a></span></h3>
                        <div id="property-map"></div>
                        <a name="form"></a>
                          <h3 class="titlebox" style="padding:0px 0px 0px 8px">Request Assistance</h3>
        				<form method="post" action="<?php echo site_url('site/sendcontractrequest/'.$contractor->id);?>">
        					<input type="hidden" name="redirect" value="contractor/<?php echo $contractor->username?>"/>
                            <div class="newbox">
        					<table>
        						<tr>
        							<td width="200">Type:</td>
        							<td>
        								<select id="requresttype" name="type" onchange="setlabel()">
        									<option value="Request Phone Assistance">Request Phone Assistance</option>
        									<option value="Schedule Appointment">Schedule Appointment</option>
        								</select>
        							</td>
        						</tr>
        						<tr>
        							<td>Name</td>
        							<td><input type="text" name="name" required/></td>
        						</tr>
        						<tr>
        							<td>Email</td>
        							<td><input type="email" name="email" required/></td>
        						</tr>
        						<tr>
        							<td>Phone</td>
        							<td><input type="text" name="phone"  required/></td>
        						</tr>
        						<tr>
        							<td id="daytd">Best day to call</td>
        							<td><input type="text" id="day" name="daytd"/></td>
        						</tr>
        						<tr>
        							<td id="timetd">Best time to call</td>
        							<td><input type="text" id="time" name="timetd"/></td>
        						</tr>

        						<tr>
        							<td>Regarding</td>
        							<td><textarea name="regarding" rows="5" style="width: 350px;"></textarea>
        						</tr>
        						<tr>
        							<td></td>
        							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
        						</tr>
        					</table>
                          </div>
        				</form>
                    </div>
                  </div><!-- End of nonfbdiv -->
                  
                   
                   <div id="fbwall" style="display:none;">
                       <div class="property-detail" style="height:2000px;overflow:auto;">
                          <div class="pull-left overview effect5" style="float:left;">
                            <div class="row">                
								 <div class="content" style="padding-left:15px;">
                         		 <h3 class="titlebox" style="padding:0px 0px 0px 8px">Wall Gallery</h3>
                         
                         		   <div class="maindiv"> 
                         		                                                
			                         <div class="pull-left" style="width:24%;">                      
			                           <p style="text-align:center"><?php if($supplier->logo !=""){?>
			                               <img width="200" height="400" src="<?php echo site_url('uploads/logo/'.$supplier->logo);?>"/><?php } else {?>
			                                <img width="200" height="400" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/><?php } ?>
			                           </p>
			                          </div>
			                          		                         
			                          <div class="pull-right" style="width:70%;">
					                    <p style="padding-right:30px;font-weight:bolder;font-size:20px;line-height:20px;color:black;"><?php echo $supplier->title; ?></p>
					                    <p><?php echo nl2br($supplier->address); ?></p>
					                    <hr>
					                     <p><strong>Comment:</strong></p>
					                     <form id="fbwallform" method="post"> 		 
					               
									 					                    
					                  <div class="comment" style="margin-top:8px;">					                      
		                                  	  	                                                                                 			                            	
						                <textarea rows="2" cols="5" class="form-control ckeditor" id="commentsection" name="commentsection"></textarea><br>
						                <input type="hidden" name="companyid" id="companyid" value="<?php echo @$supplier->id;?>"/>
		                            	<input type="hidden" name="logintype" id="logintype" value="<?php if(@$this->session->userdata('logintype')) { echo $this->session->userdata('logintype'); }elseif($this->session->userdata('site_loggedin')){ echo 'users'; } else echo 'guest'; ?>"/>									
		                            	<input type="hidden" name="senderid" id="senderid" value="<?php if (@$this->session->userdata('logintype') =="company" ) { echo $this->session->userdata('company')->id; } elseif($this->session->userdata('site_loggedin')) { echo $this->session->userdata('site_loggedin')->id; } else echo "guest"; ?>"/>
		                            	<input type="hidden" name="messageto" id="messageto" value="<?php echo "company"; ?>"/>
		                            	<input type="hidden" name="receiverid" id="receiverid" value="<?php echo @$supplier->id;?>"/>
		                            	<input type="hidden" name="reply" id="reply" value="">
						                <input type="submit" id="save" name="save" class="btn btn-success btn-xs" value="Comment">
						                
						                <div id="commentdiv" style="margin-top:6px;width:78%;"></div>
						                
				                       </form>
				                       </div>
				                           										
										</div><!-- End of main Pull-right -->	                                                                                                          
		                              </div><!-- End of maindiv-->
		                              <div style="clear:both;"></div>                 
							  </div><!-- End of Content -->
						   </div><!-- end of row -->
						   </div>
						</div>
                    </div><!-- End of  fbwall -->                   
                </div><!-- End of Main -->
           <div>
                <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Main Menu</h2>
                        </div>

                        <div class="content_sup">
                        	<table width="100%" cellpadding="4">
                        		
                        		<tr>
                        			<?php if(isset($contractor->shortdetail) && $contractor->shortdetail!="") {?>
                        			<td colspan="2"><b>About Us:</b><br/><?php echo $contractor->shortdetail;?></td><?php } ?>
                        		</tr>
                        		<tr>
                        			<td><h4><a href="#form">Contact</a></h4></td>
                        			<td></td>
                        		</tr>
                        		<tr>
                        			<td><h4><a href="#map">Location</a></h4></td>
                        			<td></td>
                        		</tr>
                        	</table>

                        </div>
                    </div>
                </div>

                
                
                
                
<?php if(@$similarcontractors){?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Similar Contractors</h2>
                        </div>
                        <div class="content_sup">
                                <div class="control-group">

                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($similarcontractors as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ri->logo){?>
                                    				<img width="40" src="<?php echo site_url('uploads/logo/'.$ri->logo);?>"/>
                                    				<?php } else {?>
                                    				<img width="45" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
                                    				<?php } ?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/contractor/'.$ri->username);?>" target="_blank">
                                    				<?php echo $ri->companyname?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>

                        </div>
                    </div>
                 </div>
                 <?php }?>
                 
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <?php if(@$filespublic) { ?>
                    <div class="title">
                            <h2 class="block-title">Public Files</h2>
                        </div>
                           <div class="content_sup">
                                <div class="control-group">
                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($filespublic as $ri){?>
                                    		<tr>
                                    			<td>
                        <?php $arr=explode('.',$ri->file); $ext=end($arr);
                                 if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>
                                 	<a target="_blank" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/PictureLogo.png');?>"></a>
                        <?php } else { ?>
                       			    <a target="_blank" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/FileLogo.jpg'); ?>" ></a>
								<?php } ?>
                        <a target="_blank" style="text-decoration:none;" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>"><?php echo $ri->file;?></a></b>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                            </div>
				<?php }?>
				</div>
                 </div>
                 
                 
                 

                    <div class="sidebar span3">
                    <div class="widget contact">
				<?php if(@$filesprivate){ ?>
                        <div class="title">
                            <h2 class="block-title">Private Files</h2>
                        </div>
                        <div class="content_sup">
                                <div class="control-group">
                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($filesprivate as $ri){?>
                                    		<tr>
                                    			<td>
                                    			<?php $arr=explode('.',$ri->file); $ext=end($arr);
                                 if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>
                                 	<a target="_blank" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/PictureLogo.png');?>"></a>
                        <?php } else { ?>
                       			    <a target="_blank" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/FileLogo.jpg'); ?>" ></a>
								<?php } ?>
                        <a target="_blank" style="text-decoration:none;" href="<?php echo site_url('uploads/ContractorFiles/'.$ri->file);?>"><?php echo $ri->file;?></a></b>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                        </div>
                        <?php }?>

                    </div>
                 </div>
                 
                 
                     <?php if(@$businesshrs){ ?>
                 <?php if(@$businesshrs[0]->start != '' || @$businesshrs[1]->start != '' || @$businesshrs[2]->start != '' || @$businesshrs[3]->start != '' || @$businesshrs[4]->start != '' || @$businesshrs[5]->start != '' || @$businesshrs[6]->start != ''){ ?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    	<div class="title">
                            <h2 align="center" class="block-title">
                            <img style="height:20px;" src="<?php echo base_url(); ?>uploads/logo/time.png"/>&nbsp;Business Hours</h2>
                        </div>
                        <div class="content_sup">
                            <div class="control-group">
                                <div class="controls">
                    <table border="1" cellpadding="7" class="table">
				   <?php $todayhtml=''; $bhtml=''; foreach($businesshrs as $bhrs) { 
				   	$bhrs->day = ucfirst($bhrs->day);
				   	$bhtml.='<tr><td>'.$bhrs->day.'</td>';
				   	if(date('D') == $bhrs->day)
				   	$todayhtml.='<tr><td>Today</td>';
				   	if($bhrs->isclosed==1){
				   		$bhtml.='<td colspan="2">closed</td>';
				   		if(date('D') == $bhrs->day)
				   		$todayhtml.='<td colspan="2">closed</td>';
				   	}else{
				   		$bhtml.='<td>'.$bhrs->start.'&nbsp;</td><td>&nbsp'.$bhrs->end.'</td>';
				   		if(date('D') == $bhrs->day)
				   		$todayhtml.='<td>'.$bhrs->start.'&nbsp;</td><td>&nbsp'.$bhrs->end.'</td>';
				   	}
				   	if(date('D') == $bhrs->day)	{
				   		echo 'Current time:'.$current_time = date('g:i a');
				   		$date1 = DateTime::createFromFormat('H:i a', $current_time);
				   		$date2 = DateTime::createFromFormat('H:i a', $bhrs->start);
				   		$date3 = DateTime::createFromFormat('H:i a', $bhrs->end);
				   		if($bhrs->isclosed==1){
				   			$bhtml.='<td>&nbsp;</td></tr>';
				   			$todayhtml.='<td>&nbsp;</td></tr>';
				   		}else {
				   			if ($date1 >= $date2 && $date1 <= $date3)
				   			{
				   				$bhtml.='<td>Open Now</td></tr>';
				   				$todayhtml.='<td>Open Now</td></tr>';
				   			}else {
				   				$bhtml.='<td>Closed Now</td></tr>';
				   				$todayhtml.='<td>Closed Now</td></tr>';
				   			}
				   		}
				   	}else {
				   		$bhtml.='<td>&nbsp;</td></tr>';
				   	}
				   }
				   $todayhtml.='</tr>';
				   echo $todayhtml.''.$bhtml;
					 ?>
					 </table>
                                    </div>
                                </div>

                        </div>
                    </div>
                 </div>
                 <?php } } ?>



               <div class="sidebar span3">
                  <div class="widget contact">
                      <div class="fb-like-box" data-href="<?php if(isset($contractor->fbpageurl)) echo $contractor->fbpageurl; ?>" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="true" data-show-border="true"></div>
                 </div>
              </div>
              </div>
           

            </div><!--End of Container -->
        </div><!-- End of Content -->
    </div>
</div>


  

  
 

       
        
        
        
         