<?php echo '<script>var loggedin = ' . ($this->session->userdata('site_logintype') == 'users' ? 'true' : 'false') . ';</script>' ?>
<?php echo '<script>var loginurl = "' . site_url('network/login/users') . '";</script>' ?>
<?php echo '<script>var joinurl = "' . site_url('network/join') . '";</script>' ?>
<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<?php echo '<script>var checkbankaccount="' . site_url('cart/checkbankaccount') . '";</script>' ?>
<?php echo '<script>var itemsurl="' . site_url('site/items') . '";</script>' ?>
<?php echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>
<?php echo '<script>var getpriceperqtydetails="' . site_url('site/getpriceperqtydetails') . '";</script>' ?>
<?php echo '<script>var getnewprice="' . site_url('site/getnewprice') . '";</script>' ?>
<?php echo '<script>var checksubscriberemail="' . site_url('subscriber/checksubscriberemail') . '";</script>' ?>
<?php echo '<script>var quoteurl = "' . site_url('site/getquotes') . '";</script>' ?>
<?php echo '<script>var costcodeurl = "' . site_url('site/getcostcodes') . '";</script>' ?>
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>
<?php echo '<script>var companycommentsurl = "' . site_url('company/getcompanycomments') . '";</script>' ?>

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
			 $('.bxslider').bxSlider({
			 adaptiveHeight: true,
  			 mode: 'fade',
			});
			});
		</script>

		<script type="text/javascript">
			$(document).ready(function() {
				$(".fancybox").fancybox();				
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
$lat = $supplier->com_lat;
$long = $supplier->com_lng;
?>


		<style>

		     .gallery-photos {
					padding: 6px;

				}
				.gallery-photos .big-photo {
					display: block;
					background-color: #ffffff;
					padding: 1px;
					border: 1px solid #e7e7e7;
				}
				.gallery-photos .big-photo img {
					display: block;
					max-width: 100%;
					height: auto;
					margin: 0 auto;
				}
				.gallery-photos .photo-thumbnails {
					width: 800px;
				}
				.gallery-photos .photo-thumbnails .thumbnail {
					float: left;
					box-sizing: border-box;
					-moz-box-sizing: border-box;
					-webkit-box-sizing: border-box;
					width: 120px;
					height: 80px;
					cursor: pointer;
					padding: 1px;
					border: 1px solid #e7e7e7;
					margin-left: 1.33333%;
					margin-bottom: 6px;
				}
				.gallery-photos .photo-thumbnails .thumbnail.current {
					background-color: #ffffff;
				}
				.gallery-photos .photo-thumbnails .thumbnail .thumbnail-inner {
					height: 100%;
					overflow: hidden;
				}
				.gallery-photos .photo-thumbnails .thumbnail img {
					display: block;
					width: auto;
					max-height: 100%;
					margin: 0 auto;
					opacity: 1;
				}

		</style>

<style>

#parent
{
	font-size:17px;
	color:white;
	text-align:center;
	font-weight:bold;
}
.supplier_new1 .price {
padding:5px 0px;
}

 .ui-tooltip {
	padding: 8px;
	font-size:19px !important;
	font-weight:bold !important;
	position: absolute;
	z-index: 9999;
	max-width: 300px;
	-webkit-box-shadow: 0 0 5px #aaa;
	box-shadow: 0 0 5px #aaa;
	color:#06A7EA !important;
}

 
 
 #logo
{
	width: 505px;
	margin: 0 auto;
	text-align: center;
}
#pgtitle
{
	margin: 0px 0px 20px;
	font-size: 18pt;
}
#container2
{
	display: block;
	width: 1236px;
	height: 100px;
	margin: 0 auto;
}
#imgtag
{
	position: relative;
	min-width: 300px;
	min-height: 300px;
	float: none;
	border: 3px solid #FFF;
	cursor: crosshair;
	text-align: center;
}
.tagview
{
	border: 1px solid #F10303;
	width: 50px;
	height: 50px;
	position: absolute;
/*display:none;*/	
	color: #FFFFFF;
	text-align: center;
}
.square
{
	display: block;
	height: 79px;
	opacity: 1.0;
}
.person
{
	background: #282828;
	border-top: 1px solid #F10303;
}
#tagit
{
	position: absolute;
	top: 0;
	left: 0;
	width: 288px;
	border: 1px solid #D7C7C7;
}
#tagit .box
{
	border: 1px solid #F10303;
	width: 10px;
	height: 10px;
	float: left;
}
#tagit .name
{
	float: left;
	background-color: #C5C5C5;
	width: 280px;
	height: 180px;
	padding: 5px;
	font-size: 10pt;
}
#tagit DIV.text
{
	margin-bottom: 5px;
}
#tagit INPUT[type=text]
{
	margin-bottom: 5px;
}
#tagit #tagname
{
	width: 110px;
}
#taglist
{
	width: 30px;
	min-height: 200px;
	height: auto !important;
	height: 20px;
	float: left;
	padding: 10px;
	margin-left: 20px;
	color: #000;
}
#taglist OL
{
	padding: 0 20px;
	float: left;
	cursor: pointer;
}
#taglist OL A
{
}
#taglist OL A:hover
{
	text-decoration: underline;
}
.tagtitle
{
	font-size: 14px;
	text-align: center;
	width: 100%;
	float: left;
}
 
.tp_circle {
    background: none repeat scroll 0 0 #ACC70A;
    border: 2px solid rgba(255, 255, 255, 0.75);
    border-radius: 50% 50% 50% 50%;
    box-shadow: 0 0 10px #000000;
    color: #FFFFFF;    
    height: 16px;
    line-height: 13px;
    padding-top: 4px;
    /*position: absolute;
    text-align: center;*/
    width: 20px;
    z-index: 2;
}

#tableinnerid {
	background: none repeat scroll 0 0 #F5F5F5;
    border-collapse: separate;
    box-shadow: 0 1px 0 #FFFFFF inset;
    font-size: 12px;
    line-height: 24px;
    margin: 0 auto;
    text-align: left;

}

.modal{
left:22%;
width:92%;
}


.btn-green {
background-color: #ACC70A;
background-image: linear-gradient(to bottom, #BACF0B, #98BA09);
background-repeat: repeat-x;
border-color: #7C9710
}

</style>


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
	$("#dialog-form").hide();
        $(".join-newsletter").click(function(){
        	$( "#dialog-form" ).dialog();
            });
        $("#subscribe").click(function(){
            	if(confirm("Please confirm you wish to subscribe to this users newsletter.")){

            		if($('#email').val() !=""){

            			var data = "email="+$('#email').val();

            			$.ajax({
            				type:"post",
            				data: data,
            				sync: false,
            				url: checksubscriberemail
            			}).done(function(data){
            				if(data!=1){
            					$("#form-addsubscriber").submit();
            				}else
            				alert('Subscriber E-Mail already exists');
            			});
            		}	
				}


            });
            
       $("#daterequested").datepicker();     
    });

    function industryitems(id)
    {
    	$("#typei").val(id);
    	$("#supplierform").submit();
    }

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
                icon: 'http://html.realia.byaviators.com/assets/img/marker-transparent.png'
            });

            var myOptions = {
                content: '<div class="infobox"><div class="image"><img src="<?php if($supplier->logo) { echo base_url(); ?>uploads/logo/thumbs/<?php echo $supplier->logo; } else { echo base_url(); ?>templates/site/assets/img/default/big.png <?php } ?>" alt="" width="100"></div><div class="title"><a href=""><?php echo $supplier->title; ?></a></div><div class="area"><div class="price">&nbsp;</div><span class="key"><?php echo $supplier->contact; ?><br/><?php echo $supplier->city.",&nbsp;".$supplier->state; ?></span><span class="value"></span></div><div class="link"><a href="<?php echo site_url('store/items/' . $supplier->username); ?>">Go to Store</a></div></div>',
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
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jRating.jquery.js"></script>

<script>
$(document).ready(function() {
	$("#dialog-form").hide();

	$('.fixedrating').jRating({
		length:5,
		bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
		nbRates : 0,
		isDisabled:true,
		sendRequest: false,
		canRateAgain : false,
		decimalLength:1,
		 onClick : function(element,rate) {

	        },
		onError : function(){
			alert('Error : please retry');
		}
	});
	
	var counter = 0;
    var mouseX = 0;
    var mouseY = 0;
	
	// mouseover the tagboxes that is already there but opacity is 0.
	/*$( '#tagbox' ).on( 'mouseover', '.tagview', function( ) {
		var pos = $( this ).position();
		 id = $(this).attr("id");	
		 // alert(id);	 
		//$(this).css({ opacity: 1.0 }); // div appears when opacity is set to 1.
		$('#' + id+'_').css({ opacity: 1.0 });
	}).on( 'mouseout', '.tagview', function( ) {
		//$(this).css({ opacity: 0.0 }); // hide the div by setting opacity to 0.
		$('#' + id+'_').css({ opacity: 0.0 });
	});*/

	
	// load the tags for the image when page loads.
    var img = $('#imgtag').find( 'img' );
	var id = $( img ).attr( 'id' );	
	var company = $('#company').val();	
	viewtag(id,company);   
	
});


function viewtagdescription(id){
	
	$('#' + id+'_').css({ opacity: 1.0 });
}


function hidetagdescription(id){
	
	$('#' + id+'_').css({ opacity: 0.0 });
}

   
function viewtag(pic_id,company)
{	
	// get the tag list with action remove and tag boxes and place it on the image.
	$.post( "<?php echo site_url('site/taglistsupplier');?>" ,  "pic_id=" + pic_id + "&company=" + company, function( data ) {
		$('#taglist').html(data.lists);
		$('#tagbox').html(data.boxes);
	}, "json");	
}

</script>

<script>
    function addtocart(itemid, companyid, price, minqty, unit, itemcode, itemname, isdeal)
    {
    	if(typeof(minqty)==='undefined') minqty = 0;
    	if(typeof(isdeal)==='undefined') isdeal = 0;
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
		$('#cartqtydiv').html('');
		$("#cartsavediv").html('');
		$("#qtypricebox").html('');
		$("#itemnamebox").html('');
       	$("#hiddenprice").val(price);
        $("#cartprice").modal();
        var selected = "";
        $("#itemnamebox").html(itemcode+"  /  "+itemname);
        $("#unitbox").html("Unit Type: "+unit+"<br/>");
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:80px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+','+isdeal+');">';
        for (i = 1; i <=500; i++) {
        	if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			}
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);

        if(!isdeal) {

        	var data = "itemid="+itemid+"&companyid="+companyid+"&price="+price;
        	$("#qtypricebox").html("");
        	$.ajax({
        		type:"post",
        		data: data,
        		sync: false,
        		url: getpriceqtydetails
        	}).done(function(data){
        		if(data){

        			$("#qtypricebox").html(data);
        		}
        	});

        	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+minqty+"&price="+price;

        	$.ajax({
        		type:"post",
        		data: data2,
        		sync: false,
        		url: getpriceperqtydetails
        	}).done(function(data){
        		if(data){

        			$("#cartsavediv").html("");
        			$("#cartsavediv").html(data);
        		}
        	});

        	$.ajax({
        		type:"post",
        		data: data2,
        		url: getnewprice,
        		sync:false
        	}).done(function(data){
        		if(data){

        			if(data!="norecord")
        			$("#hiddenprice").val(data);
        		}
        	});
        }

    }

    function showmodifiedprice(itemid, companyid, price, isdeal){

    	qty = ($('#qtycart').val());
    	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+qty+"&price="+price;
    	if(!isdeal) {
    		$.ajax({
    			type:"post",
    			data: data2,
    			sync: false,
    			url: getpriceperqtydetails
    		}).done(function(data){
    			if(data){

    				$("#cartsavediv").html("");
    				$("#cartsavediv").html(data);
    			}
    		});

    		$.ajax({
    			type:"post",
    			data: data2,
    			url: getnewprice,
    			sync:false
    		}).done(function(data){
    			if(data){

    				if(data!="norecord")
    				$("#hiddenprice").val(data);
    			}
    		});
    	}
    }

    function addtocart2(itemid, companyid, price, minqty, isdeal){

    	qty = ($('#qtycart').val());

    	if(isNaN(parseInt(qty)))
        {
            return false;
        }
        if(qty < minqty)
        {
            alert('Minimum quantity to order is '+ minqty);
            return false;
        }
        
         var data2 = "company=" + companyid;
        
        $.ajax({
            type: "post",
            data: data2,
            url: checkbankaccount,
            sync:false
        }).done(function(data) {
            if(data!='true'){            	
            	alert('Supplier has not set bank account settings');
            	return false;
            }else{
            	
            	var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + $("#hiddenprice").val() + "&qty=" + qty + "&isdeal=" + isdeal;
            	//alert(data); return false;
            	$.ajax({
            		type: "post",
            		data: data,
            		url: addtocarturl,
            		sync:false
            	}).done(function(data) {
            		alert(data);
            		window.location = window.location;
            	});
            	
            }
            
        });

    }

    function displayeventindetail(eventid){

    	$("#eventdetails"+eventid).modal();

    }
    
    
    function showimagewithtag(imgid,imagsrc,imagename){
    
    	$("#imgmodaltag").modal();
    	var img = $('#imgtag').find( 'img' );
		$( img ).attr( 'id' ,imgid);	
		$( img ).attr( 'src' , imagsrc);
		$('#headimagename').html('<h3>'+imagename+'</h3>');
		$('.fb-like').attr('data-href', '<?php echo base_url(); ?>site/designbookdetail/'+imgid);
		$('.fb-like').attr('href', '<?php echo base_url(); ?>site/designbookdetail/'+imgid);
		var frame = $('#fbshareandlike').find('iframe');		
		var str = $( frame ).attr('src');
		var res = str.split("href=");
		var res2 = res[1].split("&layout=");
		var res3 = res2[0].split("designbookdetail%2F");
		$( frame ).attr('src',res[0]+"href="+res3[0]+"designbookdetail%2F"+imgid+"&layout="+res2[1]);		
		var company = $('#company').val();	
		viewtag(imgid,company);   
		//viewtag(imgid); 
    }

    
    function openrfqpopup(){ 
    	$('#imgmodaltag').modal('hide');
    	$('#createmodal').modal();
    	
    }
    
    
    function addtopo(itemid)
	{
		$('#imgmodaltag').modal('hide');
		$("#addform").trigger("reset");
		$("#additemid").val(itemid);
		//$('#additemproject').attr('selectedIndex',0);
		//$('#additemproject option:first-child').attr("selected", "selected");
		//document.getElementById('additemproject').value=2;
		$('#additemqty').val('');
		$("#additempo").html('<select name="quote" required></select>');
		$('#additemcostcode').html('<select name="costcode" required></select>');
		getquotecombo();
		getcostcodecombo()
		$("#addtoquotemodal").modal();
	}
    
	function getquotecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: quoteurl,
            data: d
        }).done(function(data) {
            $("#additempo").html(data);
        	//document.getElementById("additempo").innerHTML = data;
        });

    }
	
    function getcostcodecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: costcodeurl,
            data: d
        }).done(function(data) {
            $("#additemcostcode").html(data);
        });
    }
    
    function addtopo1(quote)
	{
		//var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;

		var string = '<h3>RFQ created for the item.</h3><div><a target="_blank" href="<?php echo site_url("admin/quote/update/"); ?>/'+quote+'">Click here to view the Quote</a><br/><br/><br/><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>';
		$("#modalhtm").html(string);
		$("#addtoquotemodal1").modal();

	}
    
    
    function showimgmodal(){
    	
    	$('#imgmodaltag').modal();   	
    }
    
    
    function rfqformsubmit()
	{
		var d = $("#addtoquoteform").serialize();
		var quote = $('[name="quote"]').val();

        $.ajax({
            type: "post",
            url: rfqurl,
            data: d
        }).done(function(data) {
            if (data == 'Success')
            {
               addtopo1(quote);
            }
            else
            {
                alert(data);
            }
            $("#addtoquotemodal").modal('hide');
        });
        return false;
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
		$("#targetLayer").html(data);
		},
		error: function()
		{
		}
		});
		}));
		
		
		var companyid = $("#company").val();
		d = "companyid="+companyid;
		$.ajax({
			type: "post",
			url: companycommentsurl,
			data: d
		}).done(function(data) {
			var obj = $.parseJSON(data);
			$("#targetLayer").html(obj.fbwall);
		});

	});
  
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
                <p style="line-height:40px;font-family:Helvetica;">
                Existing & New Customers - Join our Business to Business Network for seamless quotes,ordering and account solutions.</p></div>
              		<div>
	                  <button type="button" id="button" class="btn btn-primary btn-lg" onclick="changetab('walltab');" style="border-radius: 10px;">
	                   <strong>View Wall</strong></button>	                	
                 	</div>
                 <h3 class="titlebox">
                	<table width="100%">
                    	<tr>
                        	<td align="left"><a href="#" onclick="changetab('suppliertab');">                       	
                        	<h2 class="page-header" style="padding:0px 0px 0px 7px"><?php echo $supplier->title;?></h2></a></td>
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
                    <div class="carousel property">
                    </div>
                  
                  <div class="nonfbdiv">
                    <div class="property-detail">
                        <div class="pull-left overview effect5" style="float:left;">
                            <div class="row">
                                <div class="span4" id="mydiv">
                                    <p style="text-align:center">
                                        <?php if($supplier->logo !=""){?>
                                                <img width="60" src="<?php echo site_url('uploads/logo/'.$supplier->logo);?>"/>
                                                <?php } else {?>
                                                <img width="60" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
                                        <?php } ?>

                                    </p>
                                    <h2 class="name"><?php echo $supplier->title;?>
                                   </h2>
                                    <table width="100%" style="font-size: 12px;">
                                        <tr>
                                            <td>Join Date:</td>
                                            <td><?php echo date('m/d/Y',strtotime($supplier->regdate)); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Location:</td>
                                            <td><?php echo nl2br($supplier->address); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Email:</td>
                                            <td><?php echo $supplier->primaryemail; ?></td>
                                        </tr>
                                        <?php if($supplier->phone){?>
                                        <tr>
                                            <td>Tel:</td>
                                            <td><?php echo $supplier->phone; ?></td>
                                        </tr>
                                        <?php }?>
                                        <?php if($supplier->fax){?>
                                        <tr>
                                            <td>Fax:</td>
                                            <td><?php echo $supplier->fax; ?></td>
                                        </tr>
                                        <?php }?>
                                        <tr>
                                            <td width="27%">Contact Person:</td>
                                            <td><?php echo $supplier->contact; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <br/>
                              <div class="invoice-button-action-set">
                                  <p style="float: left;line-height: 17px;">
                                      <button type="button" class="btn btn-primary" onclick="PrintElem(mydiv)" style="border-radius: 2px;padding: 0 10px;">
                                        Print
                                      </button>
                                </p>
                                 <!-- <div style="float: left;margin-left: 20px;" class="addthis_toolbox addthis_default_style ">
                                     <a class="addthis_counter addthis_pill_style" addthis:url="<?php // echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];?>#mydiv" addthis:title=""></a>
                        </div>-->
                                <!--<a class="shareEmail" href="" title="Share by Email"><img src="http://png-2.findicons.com/files/icons/573/must_have/48/mail.png"/></a>-->
                              </div>

                        </div>

                        <!-- <div id="example-1" class="thumbs" style="float:left; height:310px;width:395px; overflow-x:auto;">
						<ul>
                           <?php  if(isset($image) && count($image)>0)  foreach($image as $items) { ?>
                          <li style="margin-bottom:3px;">
                         	<a href="<?php echo site_url('uploads/gallery/'.$items->imagename);?>">
							<img src="<?php echo site_url('uploads/gallery/'.$items->imagename);?>" width="100%x" class="img-thumbnail"/></a></li>
                           <?php }   ?>
                        </ul>
                      </div>
-->
                   <?php  if(isset($image) && count($image)>0) { ?>
                     <div style="float:left; height:310px;width:395px;">
                        <ul class="bxslider">
						   <?php foreach($image as $items) { ?>
						  <li>
						  <a class="fancybox" rel="group" href="<?php echo site_url('uploads/gallery/'.$items->imagename);?>">
						 <img src="<?php echo site_url('uploads/gallery/'.$items->imagename);?>" /></a>
						  </li>
						  <?php } ?>
						</ul>
                      </div>
                   <?php }   ?>
                      <br><br><br>

                         <div class="row expe" style="margin-left: 3px;">
                            <p><?php echo $supplier->about; ?></p>
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
									      <a href="<?php echo site_url('uploads/imagegallery/'.$items->imagename);?>">
									      <!-- <a onclick="showimagewithtag('<?php echo $items->id;?>','<?php echo site_url('uploads/imagegallery/'.$items->imagename);?>');"> -->
									      <img src="<?php echo site_url('uploads/imagegallery/'.$items->imagename);?>"/>
									      </a>
								      </div>
								    </div>
								     <?php }   ?>
								  </div>
							</div>
							</div>
						</div>
						<?php } ?>

 <?php  if(isset($designbook) && count($designbook)>0) { ?>
					 <div class="content">
                         <h3 class="titlebox" style="padding:0px 0px 0px 8px">Design Book</h3>
                          <div class="thumbs">
							<div class="gallery-photos clearfix">
								  <div class="photo-thumbnails">
								   <?php  if(isset($designbook) && count($designbook)>0)  foreach($designbook as $items) { ?>
								    <div class="thumbnail current">
								      <div class="thumbnail-inner">
			<!--<a id="image" onclick="showimagewithtag('<?php //echo $items->id;?>','<?php e//cho site_url('uploads/designbook/'.$items->imagename);?>');">-->
										<a href="<?php echo site_url('site/designbookdetail/'.$items->id); ?>" target="_blank">
									      <img src="<?php echo site_url('uploads/designbook/'.$items->imagename);?>" alt="nothing"/>
									    </a>									   								      
								      </div>
								      <a id="image" href="#" onmouseover="showimagewithtag('<?php echo $items->id;?>','<?php echo site_url('uploads/designbook/'.$items->imagename);?>','<?php echo htmlspecialchars(addslashes($items->name));?>');">
                                        <span class="zoom" title="Click here to zoom the image"><i class="icon-search"></i></span></a>					      			
								    </div>
								    
								     <?php }   ?>
								  </div>
								  <input type="hidden" name="company" id="company" value="<?php echo $supplier->id;?>"/> 
							</div>
							</div>
						</div>
						<?php } ?>



                        <?php if(@$members){?>
                        <div>

                              <h3 class="titlebox" style="padding:0px 0px 0px 8px; margin:0px;">
                           Meet The Team:</h3>
                           <div class="meet_team" style="border-bottom-style:none;border-left-style:none;border-right-style:none;">
                            <table>

                            	<?php $key = 0; foreach($members as $member){?>
                            	<?php if($key==0){?>
                            	<tr>
                            	<?php }?>

                            	<td style="position:relative;">

                             <!--    <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><h1><?php echo $member->name;?>&nbsp; <?php echo $member->title;?></h1></td>
  </tr>
  <tr>
    <td><img src="<?php echo base_url("uploads/companyMembers/".$member->picture);?>" alt="Profile Image" style="height:225px;width:190px;"/></td>
  </tr>
  <tr>
   <td style="word-wrap: break-word"><h2><?php echo $member->phone;?><br/><?php echo $member->email;?></h2></td>
  </tr> -->
  
  <div>
  <div style="height:40px;width:200px;"><h1 style="overflow:auto;"><?php echo $member->name;?>&nbsp;<?php echo $member->title;?></h1></div>
  <div style="height:180px;width:200px;overflow:hidden;"><img src="<?php echo base_url("uploads/companyMembers/".$member->picture);?>" alt="Profile Image"/></div>
  <div style="height:40px;width:200px;"><h2 style="overflow:auto;font-family: Arial, Helvetica, sans-serif;font-size:15px;"><?php echo $member->phone;?><br/><?php echo $member->email;?></h2></div>
 </div>  
<!-- </table> -->
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
                        <?php //if(isset($types[0]->category) && $types[0]->category!="") {
								if($types){
									$band = false;
									foreach ($types as $type){
										if ($type->category == 'Manufacturer')
											$band=true;
									}
									if($band){
							?>
                        <div class="content">

                         <p>&nbsp;</p>

                         <h3 class="titlebox" style="padding:0px 0px 0px 8px">Manufacturers Carried:</h3>

                            <ul style="float:left; display:inline-block;list-style-type: none; margin:0px; padding:0px;;text-align:center">
            			    <?php
            			        foreach ($types as $type)
                                    if ($type->category == 'Manufacturer') {
                            ?>
                            	<li style="display:inline;padding-right:5px;text-align:center">
                                    <a style="text-decoration:none;" href="<?php echo site_url('store/items/'.$supplier->username.'/'.$type->id);?>">
                                    <?php if($type->image){?>
                                    <img src="<?php echo site_url('uploads/type/thumbs/'. $type->image);?>" alt="<?php echo $type->title; ?>"/>
                                    <?php }else{?>
                                    <?php echo $type->title; ?>
                                    <?php }?>
                                    </a>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
						<?php } } ?>
						<?php if($feedbacks){?>
                        <div>
                            <p>&nbsp;</p><br/>
                              <h3 class="titlebox" style="padding:0px 0px 0px 8px">
                           Feedback:</h3>
                            <table class="table">
                            	<?php foreach($feedbacks as $feedback){?>
                            	<tr>
                            		<td width="200"><?php echo $feedback->companyname;?></td>
                            		<td>
                                		<div class="fixedrating" data-average="<?php echo $feedback->rating;?>" data-id="1"></div>
                                		(<?php echo number_format($feedback->rating,2);?> / 5.00)
                            		</td>
                            		<td><?php echo $feedback->feedback;?></td>
                            	</tr>
                            	<?php }?>
                            </table>
                        </div>
                        <?php }?>



                        <?php if(@$dealfeed){?>
                        	<br/>
                            <h3 class="titlebox2" style="padding:0px 0px 0px 8px">Supplier Deals</h3>

                            <?php
                        	foreach($dealfeed as $di)
                        	{
								$diff = abs(strtotime(date('Y-m-d H:i')) - strtotime($di->dealdate));

								$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

								$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
								$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
								$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
								$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

                                $remaining = "$days days, $hours hrs, $minuts mins";
                        	?>
                            <div class="supplier_new">
                            <h3>Hot Deal </h3>
                            <div class="property">
                                <div class="image span2">
                                    <div class="content">
                                        <a href="<?php echo site_url("site/item/".$di->url);?>">
                                        <?php if($di->image) {?>
                        			<img style="width: 81px;height:80px" src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="81" height="80">
                        			<?php } else {?>
                        		<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        		<?php }?>
                        			<br/>
                        			<?php echo $di->itemname?>
                        			</a>
                        			<?php if($di->filename){?>
                        			<br/>
                        			<a href="<?php echo site_url("uploads/item/".$di->filename);?>" target="_blank">
                        			View Details
                        			</a>
                        			<?php }?>
                                     </div>
                                </div>

                                <div class="body1 span6">
                                    <div class="title-price row">
                                        <div class="title1 span5">
                                            <h2><a href="<?php echo site_url('site/item/'.$di->url);?>"><?php echo $di->itemcode;?></a>  </h2>
                                            <p><?php echo $di->dealnote;?></p>
                                            <div class="area">
                                                <span class="key"><strong>Quantity Available:</strong></span>
                                                <span class="value"><?php echo $di->qtyavailable;?></span>

                                                <span class="key"><strong>Time Remaning:</strong></span>
                                                <span class="value"><?php echo $remaining;?></span>

                                                <br>
                                                <span class="key"><strong>Minimum Qty:</strong></span>
                                                <span class="value"><?php echo $di->qtyreqd;?></span>

                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value">1234567</span>
                                                </div>
                                        </div>
                                        <div class="price">
                                        <span>   $<?php echo $di->dealprice;?> &nbsp; <?php echo $di->unit;?></span>

                                             <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,'<?php echo $di->unit ? $di->unit : '';?>','<?php echo htmlspecialchars(addslashes($di->itemcode));?>', '<?php echo htmlspecialchars(addslashes($di->itemname));?>',1)">
                                    <i class="icon icon-plus"></i> Buy
                                </a>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </div>
                        <?php } ?>


                        <?php }?>


                        <?php if ($this->session->userdata('pms_site_cart')) { ?>
                        <div class="pull-right">
                            <a href="<?php echo site_url('cart'); ?>">
                                <img src="<?php echo site_url('templates/site/assets/img/shopping_cart.png');?>"/>
                            </a>
                        </div>
                        <?php } ?>

                        <?php if($inventory){?>
                        	<br/>
                        	  <h3 class="titlebox2" style="padding:0px 0px 0px 8px">Featured Items</h3>
                        	<?php
                                    foreach ($inventory as $inv)
                                    if ($inv->ea)
                                    {
                                       $price = $inv->ea;
                                       $inv->qtyreqd = 0;
                                ?>

                              <div class="supplier_new1">
                                 <h2><a href="<?php echo site_url('site/item/'.$inv->url);?>"><?php echo $inv->itemcode; ?></a></h2>
                            <div class="property">
                                <div class="image span2">
                                    <div class="content">
                                    <?php if($inv->image){?>
                                     <img alt="<?php echo urlencode($inv->itemname); ?>" src="<?php echo site_url('uploads/item/thumbs/'. $inv->image);?>" style="max-height: 120px; padding: 20px;width:120px; height:120px" width="120" height="120">
                                     <?php } else {?>
                                     <img src="<?php echo site_url('uploads/item/big.png');?>" style="max-height: 120px; padding: 20px;width:120px; height:120px;" width="120" height="120">
                                     <?php }?>
                                    </div>
                                </div>

                                <div class="body1 span6">
                                    <div class="title-price row">
                                        <div class="title1 span5">

                                            <?php if(!empty($inv->companynotes)){?>
                                            <div class="company-note" style="height:120px;overflow:auto;">
                                                <section><?php echo $inv->companynotes; ?></section>
                                            </div>
                                            <?php } ?>

                                            <div class="area">
                                                <span class="key"><strong>Item Name:</strong></span>
                                                <span class="value"><?php echo $inv->itemname ?></span>

                                                <span class="key"><strong>Unit:</strong></span>
                                                <span class="value">EA</span>

                                                <br>
                                                <?php if($inv->manufacturername) { ?>
 												 <span class="key"><strong>Manufacturer:</strong></span>
												 <span class="value"><?php echo $inv->manufacturername; ?></span>
												 <?php } ?>

                                                <?php if($inv->partnum) { ?>
                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value"><?php echo $inv->partnum ?></span>
                                                <?php } ?>

                                                 <?php if($inv->qtyavailable) { ?>
										        <span class="key"><strong>Stock:</strong></span>
										        <span class="value"><?php echo $inv->qtyavailable;?></span>
										        <?php } ?>

                                                <br/>
                                                <span class="key"><strong>Availability:</strong></span>
                                                <span class="value"><?php echo $inv->instock?'Available':'Not Available';?></span>

                                                <span class="key"><strong>Min Order Qty:</strong></span>
                                                <span class="value"><?php echo $inv->minqty ?></span>

                                                </div>

                                        </div>

                                        <div class="price">

                                           <?php if($inv->price){?>
                                           <div id="parent">
                                        	<img style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>" title="<?php if(isset($supplier->phone)) echo $supplier->phone; ?>" />Call for Price</div>
                                       <?php }else{?>
                                            <span> <?php echo '$'.$inv->ea;?> <?php echo $inv->unit;?></span>
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $price ? $price : 0; ?>,  <?php echo $inv->minqty ? $inv->minqty : 0; ?>,'<?php echo $inv->unit ? $inv->unit : '';?>','<?php echo htmlspecialchars(addslashes($inv->itemcode));?>', '<?php echo htmlspecialchars(addslashes($inv->itemname));?>')">
                                            <i class="icon icon-plus"></i> Buy
                                        </a>
                                        <?php } ?>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        </div>
                         <?php } ?>
                        <?php }?>
						<br/>

                       <h3 class="titlebox" style="padding:0px 0px 0px 8px">Map
                        <a name="map" id="map"></a>
                        <?php $addressarray = explode(" ",$supplier->address);
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
                        <span style=" color: #fff;float: right;padding: 0 10px 0 8px;"><a target="_blank" style="color:#fff" href="<?php echo 'https://maps.google.com/maps?daddr='.$addresslink; ?>">Driving Directions</a></span></h3>



                        <div id="property-map"></div>

                        <a name="form"></a>
                          <h3 class="titlebox" style="padding:0px 0px 0px 8px">Request Assistance</h3>
                        <?php // echo $this->session->flashdata('message'); ?>
        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$supplier->id);?>">
        					<input type="hidden" name="redirect" value="supplier/<?php echo $supplier->username?>"/>
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
                    <div id="fbwall">
                       <div class="property-detail">
                            <div class="row">                
								 <div class="content" style="padding-left:15px;">
                         			<h3 class="titlebox" style="padding:0px 0px 0px 8px">Wall Gallery</h3>
                         
                         		 <div class="maindiv">                                              
			                         <div class="pull-left">                      
			                           <p style="text-align:center"><?php if($supplier->logo !=""){?>
			                                   <img width="200" height="400" src="<?php echo site_url('uploads/logo/'.$supplier->logo);?>"/>
			                                     <?php } else {?>
			                                   <img width="200" height="400" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
			                                     <?php } ?>
			                           </p>
			                          </div>
		                         
			                          <div class="pull-right">
			                            <p style="padding-right:30px;font-weight:bolder;font-size:20px;line-height:20px;color:black;"><?php echo $supplier->title; ?></p>	                                                        
		                              </div>                                                   
                                 </div><!-- End of maindiv-->
                                 <div style="clear:both;"></div>
                                 <div class="comment">
                                 <form id="fbwallform" method="post"> 
                                 <p><strong>Comment:</strong></p>                                                			                            	
		                            	 <textarea rows="2" cols="5" class="form-control ckeditor" id="about" name="about"><?php echo @$company->about;?></textarea>
		                                <!--<button id="save" type="submit" name="">Save</button>-->
		                            </form> 
								<div id="targetLayer"></div>
								</div>
                   
						</div><!-- End of Content -->
						</div>
						</div>
                    
                    </div><!-- End of  fbwall -->
                    
                    
                </div>

                <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Main Menu</h2>
                        </div>

                        <div class="content_sup">
                        	<table width="100%" cellpadding="4">
                        		<tr>
                        			<td><b>Newsletter:</b> </td>
                        			<td><h4><a href="#" class="join-newsletter" id="join-newsletter">Join</a></h4></td>
                        		</tr>
                        		<tr>
                        			<td><b>Connection:</b> </td>
                        			<td><?php echo $supplier->joinstatus?$supplier->joinstatus:'Guest';?></td>
                        		</tr>
                        		<tr>
                        			<?php if(isset($supplier->shortdetail) && $supplier->shortdetail!="") {?>
                        			<td colspan="2"><b>About Us:</b><br/><?php echo $supplier->shortdetail;?></td><?php } ?>
                        		</tr>
                        		<tr>
                        			<td><h4><a href="#form">Contact</a></h4></td>
                        			<td></td>
                        		</tr>
                        		<tr>
                        			<td><h4><a href="#map">Location</a></h4></td>
                        			<td></td>
                        		</tr>
                        		<?php if(0){?>
                        		<tr>
                        			<td>Manufacturer Carried: </td>
                        			<td>
                        			    <?php foreach ($types as $type)
                                            if ($type->category == 'Manufacturer') {
                                                ?>
                                                <a href="<?php echo site_url('store/items/'.$supplier->username.'/'.$type->id);?>">
                                                <?php echo $type->title; ?>
                                                </a>
                                                <br/>
                                        <?php } ?>

                        			</td>
                        		</tr>
                        		<?php }?>
                        		<?php  $i=0;if($types[$i]->category == 'Industry') {?>
                        		<tr>
                        			<td><b>Industry:</b></td>
                        		</tr>
                        		<tr>
                        			<td colspan="2">
                        			<ul class="inlist">
                        			    <?php foreach ($types as $type)
                                            if ($type->category == 'Industry') {
                                                ?>
                                                <li>
                                                <a href="javascript:void(0)" onclick="industryitems('<?php echo $type->id;?>')">
                                                <h4><?php echo $type->title; ?></h4>
                                                </a>
                                                </li>
                                        <?php } ?>
                                        </ul>
                        			</td>
                        		</tr>
                        		<?php } $i++; ?>
                        		<?php if($rating){?>
                        		<tr>
                        			<td>Reviews: </td>
                        			<td><?php echo $rating;?> <?php echo number_format($ratingvalue,2);?> / 5.00</td>
                        		</tr>
                        		<?php }?>
                        		<tr>
                        			<td colspan="2">
                            			<a href="<?php echo site_url('store/items/'.$supplier->username);?>">
                            			<h4>Go to Store</h4>
                            			<img src="<?php echo site_url('templates/site/assets/img/shopping_cart.png');?>"/>
                            			</a>
                        			</td>
                        		</tr>
                        	</table>

                        </div>
                    </div>
                </div>

                
                
                 <?php if(@$businesshrs){ ?>
                 <?php if(@$businesshrs[0]->start != '' || @$businesshrs[1]->start != '' || @$businesshrs[2]->start != '' || @$businesshrs[3]->start != '' || @$businesshrs[4]->start != '' || @$businesshrs[5]->start != '' || @$businesshrs[6]->start != ''){ ?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 align="center" class="block-title"><img style="height:20px;" src="<?php echo base_url(); ?>uploads/logo/time.png"/>&nbsp;Business Hours</h2>
                        </div>
                        <div class="content_sup">
                                <div class="control-group">

                                    <div class="controls">
                    <table border="1" cellpadding="7">
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
                
                
                
                <?php /* if(@$dealfeed){?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Supplier Deals</h2>
                        </div>
                        <div class="content_sup">

                        	<table>
                        	<?php
                        	foreach($dealfeed as $di)
                        	{
								$diff = abs(strtotime(date('Y-m-d H:i')) - strtotime($di->dealdate));
								$years = floor($diff / (365*60*60*24)); $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

								$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
								$hours = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60));
								$minuts = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60);
								$seconds = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60 - $minuts*60));

                                $remaining = "$days days, $hours hrs, $minuts mins";
                        	?>


                              	<tr>
                        		<td  style="text-align:center"><?php if($di->image){?>
                        			<img src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="80" height="80">
                        			<?php } else {?>
                        			<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        			<?php }?></td>
                        	</tr>

                              	<tr>
                        		<td style="text-align:center"><a href="<?php echo site_url("site/item/".$di->url);?>"><?php echo $di->itemname?></a></td>
                        	</tr>


                        	<tr>
                        		<td style="text-align:center"> <?php echo $remaining;?> remaining</td>
                        	</tr>
                        	<tr>
                        		<td style="text-align:center">Hurry up, only <span class="red"><?php echo $di->qtyavailable;?> items</span> Remaining</td>
                        	</tr>
                            <tr>
                        		<td  class="siteprices" style="text-align:center">($<?php echo $di->dealprice;?> Min. Qty: <?php echo $di->qtyreqd;?>) 	<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,'<?php echo $di->unit ? $di->unit : '';?>','<?php echo htmlspecialchars(addslashes($di->itemcode));?>', '<?php echo htmlspecialchars(addslashes($di->itemname));?>',1)">
                                    <i class="icon icon-plus"></i>
                                </a></td>
                        	</tr>
                        	<?php }?>
                        	</table>
                        </div>
                    </div>
                 </div>
                 <?php } */?>

                 <?php if(@$similarsuppliers){?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Similar Suppliers</h2>
                        </div>
                        <div class="content_sup">
                                <div class="control-group">

                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($similarsuppliers as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ri->logo){?>
                                    				<img width="40" src="<?php echo site_url('uploads/logo/'.$ri->logo);?>"/>
                                    				<?php } else {?>
                                    				<img width="45" height="45" src="<?php echo base_url(); ?>templates/site/assets/img/logo.png"/>
                                    				<?php } ?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/supplier/'.$ri->username);?>" target="_blank"><?php echo $ri->title?></a>
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

                <?php if($adforsupplier){?>

      <script src="<?php echo base_url(); ?>templates/site/assets/js/jquery.bxslider.min.js"></script>
<!-- bxSlider CSS file -->
<link href="<?php echo base_url(); ?>templates/site/assets/css/jquery.bxslider.css" rel="stylesheet" />

        <script type="text/javascript">
			$(function() {$('.bxslider').bxSlider({

				});

			});
		</script>

                    <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Suppliers Classified Listings</h2>
                        </div>
                        <div class="content_sup">

                                <div class="control-group">

                                   <div class="controls bxcontainer">
                                   		<ul class="bxslider">
                                    	<?php foreach($adforsupplier as $key=>$ad){?>
                                    	<li><img  src="<?php
                                    	$pathinfo = pathinfo($ad->image);
                                    	echo base_url("/uploads/ads/".$pathinfo["filename"]."_thumb.".$pathinfo["extension"]);?>" alt="image<?php echo $key;?>"/><h4><?php echo $ad->title;?> $<?php echo $ad->price;?></h4><p><a href="<?php echo base_url("/site/ad/".$ad->id);?>" class="btn btn-primary">Details</a></p></li>
                                     	<?php } ?>
                                    	</ul>

                                     </div>
                                </div>

                        </div>
                    </div>
                 </div>
                <?php }?>


                 <?php if(@$upcomingevents){ ?>
                 <div class="sidebar span3">
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Upcoming Events</h2>
                        </div>
                        <div class="content_sup">
                                <div class="control-group">

                                    <div class="controls">
                                    	<table cellpadding="5">
                                    	<?php foreach($upcomingevents as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<b><?php echo date('D, d M ',strtotime($ri->evtdate)); ?></b>
                                    			</td>
                                    			<td>
                                    				<?php echo $ri->eventstart."&nbsp;&nbsp;".$ri->title."&nbsp;&nbsp;".$ri->notes;
                                    				echo "&nbsp;&nbsp; <a href='#' onclick='displayeventindetail(".$ri->id."); return false;'>more...</a>" ?>
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
                        <?php $arr=explode('.',$ri->filename); $ext=end($arr);
                                 if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>
                                 	<a target="_blank" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/PictureLogo.png');?>"></a>
                        <?php } else { ?>
                       			    <a target="_blank" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/FileLogo.jpg'); ?>" ></a>
								<?php } ?>
                        <a target="_blank" style="text-decoration:none;" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>"><?php echo $ri->filename;?></a></b>
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
                                    			<?php $arr=explode('.',$ri->filename); $ext=end($arr);
                                 if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>
                                 	<a target="_blank" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/PictureLogo.png');?>"></a>
                        <?php } else { ?>
                       			    <a target="_blank" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>">
                                    	<img width="30" src="<?php echo site_url('uploads/logo/FileLogo.jpg'); ?>" ></a>
								<?php } ?>
                        <a target="_blank" style="text-decoration:none;" href="<?php echo site_url('uploads/filegallery/'.$ri->filename);?>"><?php echo $ri->filename;?></a></b>
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
               <div class="fb-like-box" data-href="<?php if(isset($supplier->fbpageurl)) echo $supplier->fbpageurl; ?>" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="true" data-show-border="true"></div>
               </div></div>


                              <!-- Start Dialog Form -->
               <div class="dialog-form" id="dialog-form">

				            		<form  role="form" method="post" name="form-addsubscriber" id="form-addsubscriber" action="<?php echo base_url();?>subscriber/addsubscriber">
				                     <div class="col-md-6 col-sm-6 col-xs-6">
				                     <div class="form-group">
				    						<label for="label" class="form-label">Name:</label>
				    						<div class="controls">
				    							<input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
				    						</div>
				                     </div>

				                     <div class="form-group">
				    						<label for="label" class="form-label">Email:</label>
				    						<div class="controls">
				    							<input type="text" class="form-control" id="email" name="email" placeholder="email" required >
				    						</div>
				                     </div>
				 					<?php  foreach($fields as $key=>$field) { $name_id=trim($field->Label);  ?><br>
				 					<div class="form-group">
				    						<label for="label" class="form-label"><?php echo $field->Label ?></label>
				    						<div class="controls">
				      					    <?php if($field->FieldType == 'text' || $field->FieldType == 'email' || $field->FieldType == 'password') {?>
				      							<?php if($field->FieldType == 'email' ){?>
				      							<input type="<?php echo $field->FieldType ?>" class="form-control" id="<?php echo $name_id; ?>" name="email" placeholder="<?php echo $field->Label; ?>" required value="<?php echo $field->Value;?>"><br>
				      							<?php }else{?>
				      							<input type="<?php echo $field->FieldType ?>" class="form-control" id="<?php echo $name_id; ?>" name="<?php echo $field->Name; ?>" placeholder="<?php echo $field->Label; ?>" required value="<?php echo $field->Value;?>"><br>
				      							<?php } ?>
				      					<?php  }  ?>

				      					<?php if($field->FieldType == 'dropdown') { $dropdownValues = explode(",",$field->FieldValue); $k= array_search($field->Value,$dropdownValues); ?>
				      					<select id="<?php echo $name_id; ?>" name="<?php echo $field->Name; ?>"><?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?><option value="<?php echo $dropdownValues[$i];?>" <?php if($dropdownValues[$i]==$field->Value) { echo " selected ";} else { echo " "; } ?>><?php echo $dropdownValues[$i];?></option> <?php  } } ?></select><br>

				    							<?php   } ?>
										<?php if($field->FieldType == 'radio') { $dropdownValues = explode(",",$field->FieldValue); ?> <?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?>

										<br><input type="radio" name="<?php echo $field->Name; ?>" id="<?php echo $dropdownValues[$i];?>"
                                         value="<?php echo $dropdownValues[$i];?>" <?php if($field->Value ==$dropdownValues[$i]) echo 'checked'; ?>>
										<?php echo $dropdownValues[$i];?> <?php  } } ?>

				 					    		<?php  } ?>
				 					    <?php if($field->FieldType == 'checkbox') { $dropdownValues = explode(",",$field->FieldValue); ?> <?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?><br><input type="checkbox" name="<?php echo $field->Name; ?>" id="<?php echo $name_id; ?>"  value="<?php echo $dropdownValues[$i];?>" <?php if($field->Value ==$dropdownValues[$i]) echo 'selected'; ?>><?php echo $dropdownValues[$i];?><?php  } } ?>

				 					    		<?php } ?>
				 					    <?php if($field->FieldType == 'textarea') { ?> <br><textarea id="<?php echo $name_id;?>" name="<?php echo $field->Name; ?>"><?php echo $field->Value;?></textarea>
				 					    		<?php  }  ?>
				 					    	</div>
				 					    </div>
										<?php } ?>
				 					    <div class="form-group">
					                        <label class="form-label"></label>
					                        <div class="controls">
					                        	  <input type="hidden" name="cid" class="cid" value="<?php echo $supplier->id;?>">
					                              <input type="button" value="Subscribe" name="subscribe" id="subscribe" class="btn btn-primary btn-lg">
					                        </div>
					                    </div>
				 						 </div>
									</form>

				</div><!-- End Dialog Form -->

            </div>
        </div>
    </div>
</div>


        <div id="cartprice" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;width:365px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>

          <h4 class="semi-bold" id="myModalLabel">
          <div id="itemnamebox"></div>
          <br> Select Quantity
          </h4>
          <br/>
          <div id="unitbox"></div>
        </div>
        <div class="modal-body">

        <div id="qtypricebox"></div>

        <div>
            <div id="cartqtydiv" class="col-md-8">
            </div>
            <div class="col-md-4">
              <span id="qtylistprice"></span>
            </div>
          </div>

        <div id="cartsavediv"></div>

        </div>
        <div class="modal-footer">
          <input type="hidden" name="hiddenprice" id="hiddenprice" />
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>


  <?php if(@$upcomingevents) { foreach($upcomingevents as $event){?>
  <div id="eventdetails<?php echo $event->id;?>" aria-hidden="true" aria-labelledby="myModalLabel2" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
      	 <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <br>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">
          <div id="itemnamebox"></div>
           <?php echo $event->title; ?>
          </h4>
        </div>
        <div class="modal-body">

	  	<table class="table table-bordered  col-lg-10">
	  		<tr>
	  			<td><strong>Title</strong></td>
	  			<td><?php echo $event->title;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Date</strong></td>
	  			<td><?php echo date("m/d/Y", strtotime( $event->evtdate));?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Start</strong></td>
	  			<td><?php echo $event->eventstart;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>End</strong></td>
	  			<td><?php echo $event->eventend;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Location</strong></td>
	  			<td><?php echo $event->location;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Notes</strong></td>
	  			<td><?php echo $event->notes;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Contact Name</strong></td>
	  			<td><?php echo $event->contactname;?></td>
	  		</tr>
	  		<tr>
	  			<td><strong>Contact Phone</strong></td>
	  			<td><?php echo $event->contactphone;?></td>
	  		</tr>
	  	</table>

      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>

  <?php } } ?>
  
  
  
  
  
  



	<div id="imgmodaltag" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
		 <div class="modal-dialog">
		     <div class="modal-content">
		      
		        <div class="modal-header">
		        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button><i class="icon-credit-card icon-7x"></i>                 
		        	<div style="text-align:center;" id="headimagename"></div>          
		        </div>
		        
		        <div class="modal-body">        
		          <div id="container2">		            
				        <div id="imgtag" style="float:left;">  
				  			<img id="pic1" src="" style="margin-left:27%;"/>
				 		    <div id="tagbox"></div>
						</div>								
					<div  style="width:400px;height:300px;overflow-y:scroll;padding-left:150px;">
						<div id="taglist"></div>
					</div>
					<div style="clear:both;"></div>			
				  </div>
		        </div>
		        
		        <div class="modal-footer">          
		        	<div class="fb-like" id="fbshareandlike" style="z-index: 9999;" data-href="<?php echo base_url(); ?>site/designbookdetail/<?php echo $designbook[0]->id;?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div> &nbsp;
		          	<button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
		        </div>
		        
		     </div>
		  </div>
	 </div>
  
  
  
   <!-- Modal -->
        <div class="modal hide fade" id="addtoquotemodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title nobottompadding" id="myModalLabel">Request for quote</h3>
                    </div>
                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" onsubmit="rfqformsubmit(); return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                            <h4>Select Project</h4>
                            <select id="additemproject" onchange="getquotecombo();getcostcodecombo();">
                                <option value="">Select</option>
                                <?php foreach($projects as $up){?>
                                	<option value="<?php echo $up->id?>"><?php echo $up->title;?></option>
                                <?php }?>
                            </select>

                            <h4>Select PO</h4>
                            <span id="additempo">
                            <select name="quote" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>

                            <a href="javascript:void(0)" target="_blank" onclick="var pid=$('#additemproject').val();if(pid){$(this).attr('href','<?php echo site_url('admin/quote/add/');?>/'+pid);$('#additemproject').val('');$('#addtoquotemodal').modal('hide');}else{return false;}">Add PO</a>

                            <h4>Quantity</h4>
                            <input type="text" id="additemqty" name="quantity" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" required/>
                            <h4>Costcode</h4>
                            <span id="additemcostcode">
                            <select name="costcode" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>

                            <h4>Date Requested</h4>
                            <input type="text" id="daterequested" name="daterequested"/>

                            <br/><br/>
                            <div>
                            <button type="button" class="btn btn-primary" onclick="showimgmodal();" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="rfqformsubmit();">Add</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal hide fade" id="addtoquotemodal1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                        <div id="modalhtm">

                        </div>
                        </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>