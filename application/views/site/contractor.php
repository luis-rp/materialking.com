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
			 $('.bxslider').bxSlider({
			 adaptiveHeight: true,
  			 mode: 'fade',
  			 auto: true
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
	$("#dialog-form").hide();
        $(".join-newsletter").click(function(){
        	$( "#dialog-form" ).dialog();
            });
        $("#subscribe").click(function(){
            	if(confirm("Please confirm you wish to subscribe to this users newsletter.")){

            		if($('#email').val() !=""){

            			var data = "email="+$('#email').val()+"&cid="+$('#cid').val();

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
	
	$('#deliverydate').datepicker();
	$('#podate').datepicker();
	$('#duedate').datepicker();
		
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
    function addtocart(itemid, companyid, price, minqty, unit, itemcode, itemname, increment, imgname, isdeal)
    {
    	if(typeof(minqty)==='undefined') minqty = 0;
    	if(typeof(isdeal)==='undefined') isdeal = 0;
    	if(increment==0) { increment=1;} 
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
		$('#cartqtydiv').html('');
		$("#cartsavediv").html('');
		$("#qtypricebox").html('');
		$("#itemnamebox").html('');
       	$("#hiddenprice").val(price);
        $("#cartprice").modal();
        var selected = "";
        $("#itemnamebox").html(itemcode+"  /  "+itemname);
        $("#ftqtypricebox").html("Price "+unit+" : $"+ price);
        $("#itemimage").html('<img width="120" height="120" style="max-height: 120px; padding: 20px;width:120px; height:120px;float:right;" src='+imgname+'>');
        $("#unitbox").html("Unit Type: "+unit+"<br/>");
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:80px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+','+isdeal+');">';
       /* for (i = 1; i <=500; i++) {
        	if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			}*/
       
       increment = parseInt(increment);
   		i = increment;
       	while(i <=500){
       		if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
       		i=i+increment;	
       	}	 	
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);

       // if(!isdeal) {

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
       // }

    }

    function showmodifiedprice(itemid, companyid, price, isdeal){

    	qty = ($('#qtycart').val());
    	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+qty+"&price="+price;
    	//if(!isdeal) {
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
    	//}
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
    
    
    function addtopo(itemid, increment)
	{
		$('#imgmodaltag').modal('hide');
		$("#addform").trigger("reset");
		$("#additemid").val(itemid);
		if(increment>0){
		$("#additemqty").val(increment);
		$("#incrementqty").val(increment);
		}else{
		$('#additemqty').val('');
		$("#incrementqty").val(1);
		}
		//$('#additemproject').attr('selectedIndex',0);
		//$('#additemproject option:first-child').attr("selected", "selected");
		//document.getElementById('additemproject').value=2;	
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
		
		if($('#additemqty').val()%$("#incrementqty").val()!=0){
			alert('Sorry this item is only available in increments of '+$("#incrementqty").val());
			return false;
		}
		
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
	
	
	    function addpo(){

    	var pid=$('#additemproject').val();
    	if(pid){

    		$('#addtoquotemodal').modal();
    		//$('#additemproject').val('');
    		$('#addtoquotemodal').modal('hide');
    		$('#Addpomodal').modal();
    	}
    }
    
    
    function savepo(){
    	
    	var pid=$('#additemproject').val();
    	var ponum = $("#ponum").val();
    	if(ponum=="")
    		alert("Please Enter PO");
    	
    	var podate = $("#podate").val();
		var duedate = $('#duedate').val();
		var deliverydate = $('#deliverydate').val();
		
		var d = "pid="+pid+"&ponum="+ponum+"&podate="+podate+"&duedate="+duedate+"&deliverydate="+deliverydate;
		
		$.ajax({
			type: "post",
			url: addpoquoteurl,
			dataType: 'json',
			data: d
		}).done(function(data) {			
			if(data=="Duplicate PO#"){
				alert(data);
			}else{		
					var option = new Option(data.ponum, data.poid);
					$('[name="quote"]').append($(option));
					$('[name="quote"]').val(data.poid);
					$('#Addpomodal').modal('hide');
					$("#addtoquotemodal").modal();
				
			}
		});
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
                                  <div>
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
                        <?php // echo $this->session->flashdata('message'); ?>
        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$contractor->id);?>">
        					<input type="hidden" name="redirect" value="supplier/<?php echo $contractor->username?>"/>
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
        						<!--<tr>
        							<td></td>
        							<td><input type="submit" class="btn btn-primary" value="Send"/></td>
        						</tr>-->
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



               <div class="sidebar span3">
                  <div class="widget contact">
                      <div class="fb-like-box" data-href="<?php if(isset($supplier->fbpageurl)) echo $supplier->fbpageurl; ?>" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="true" data-show-border="true"></div>
                 </div>
              </div>
              </div>
            

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
					                        	  <input type="hidden" name="cid" id="cid" class="cid" value="<?php echo $supplier->id;?>">
					                              <input type="button" value="Subscribe" name="subscribe" id="subscribe" class="btn btn-primary btn-lg">
					                        </div>
					                    </div>
				 						 </div>
									</form>

				</div><!-- End Dialog Form -->

            </div><!--End of Container -->
        </div><!-- End of Content -->
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
          <div id="itemimage"></div>
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
        <div id="ftqtypricebox"></div>	
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
				  			<img id="pic1" src=""/>
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

                            <!-- <a href="javascript:void(0)" target="_blank" onclick="var pid=$('#additemproject').val();if(pid){$(this).attr('href','<?php echo site_url('admin/quote/add/');?>/'+pid);$('#additemproject').val('');$('#addtoquotemodal').modal('hide');}else{return false;}">Add PO</a> -->

                            <a href="javascript:void(0)" onclick="addpo()">Add PO</a>
                            
                            <h4>Quantity</h4>
                            <input type="text" id="additemqty" name="quantity" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" required/>
                            <input type="hidden" id="incrementqty" name="incrementqty" />
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
        
        
        
         <div id="Addpomodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            <h3>Please Add Your P.O. Now</h3>
        	</div>
        	<div class="modal-body">
        	
        	<div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
                  <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" >		</div>
		    </div>
		    <br><br>		    
		    <div class="control-group">
			    <div class="controlss">
			      Delivery or Pick-Up Date: &nbsp; &nbsp;
			      <input type="text" id="deliverydate" name="deliverydate" class="input small span2" 
			      	data-date-format="mm/dd/yyyy">			      
			       &nbsp; &nbsp; <br><br>
			      PO Date: &nbsp; &nbsp; 
			      <input type="text" id="podate" name="podate" class="input small span2"
			      	data-date-format="mm/dd/yyyy">
			      	&nbsp; &nbsp; &nbsp; &nbsp; <br><br>
			     Bid Due Date: &nbsp; &nbsp; 
			      <input type="text" id="duedate" name="duedate" class="input small span2"
			      data-date-format="mm/dd/yyyy">
			      <input name="add" type="button" class="btn btn-primary" value="Save" onclick="savepo();"/>
			    </div>			   
		    </div>
        	
        	</div>

   </div>   