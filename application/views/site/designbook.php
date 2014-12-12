<?php //print_r(@$_POST);die;?>


<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.js"></script>

<style type="text/css">

	#menuLog { font-size:1.0em; margin:10px 20px 20px; }
	.hidden { position:absolute; top:0; left:-9999px; width:1px; height:1px; overflow:hidden; }

	.fg-button { clear:left; margin:0 4px 40px 0px; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.fg-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	a.fg-button { float:left;  }
	button.fg-button { width:auto; overflow:visible; } /* removes extra button width in IE */

	.fg-button-icon-left { padding-left: 2.1em; }
	.fg-button-icon-right { padding-right: 2.1em; }
	.fg-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
	.fg-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
	.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */

	.fg-button.ui-state-loading .ui-icon { background: url(spinner_bar.gif) no-repeat 0 0; }
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
	width: 850px;
	height: 300px;
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

.ui-autocomplete {
  z-index:2147483647;
}

</style>


<?php echo '<script>var costcodeurl = "' . site_url('site/getcostcodes') . '";</script>' ?>
<?php echo '<script>var quoteurl = "' . site_url('site/getquotes') . '";</script>' ?>

<script>
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

</script>

<script>
    $(document).ready(function() {
        InitChosen();
        $("#daterequested").datepicker();
        
         <?php if($gallery) { foreach($gallery as $gall) { ?>
        // get the tag list with action remove and tag boxes and place it on the image.
	$.post( "<?php echo site_url('site/taglist');?>" ,  "pic_id=" + <?php echo $gall->id;?>, function( data ) {
		//$('#taglist_<?php echo $gall->id;?>').html(data.lists);
		$('#tagbox_<?php echo $gall->id;?>').html(data.boxes);
	}, "json");
        
    <?php } } ?>
    
    
    	// mouseover the tagboxes that is already there but opacity is 0.
	$( '#tagbox' ).on( 'mouseover', '.tagview', function( ) {
		var pos = $( this ).position();
		 id = $(this).attr("id");	
		 // alert(id);	 
		//$(this).css({ opacity: 1.0 }); // div appears when opacity is set to 1.
		$('#' + id+'_').css({ opacity: 1.0 });
	}).on( 'mouseout', '.tagview', function( ) {
		//$(this).css({ opacity: 0.0 }); // hide the div by setting opacity to 0.
		$('#' + id+'_').css({ opacity: 0.0 });
	});
    
        
    });


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

</script>

<script type="text/javascript">
    $(function(){
    	// BUTTONS
    	$('.fg-button').hover(
    		function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
    		function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
    	);

    	// MENUS
		$('#hierarchy').menu({
			content: $('#hierarchy').next().html(),
			crumbDefaultText: ' '
		});

		$('#hierarchybreadcrumb').menu({
			content: $('#hierarchybreadcrumb').next().html(),
			backLink: false
		});
    });
</script>
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>
<script>
	function addtopo(itemid, increment)
	{
		$("#addform").trigger("reset");
		$("#additemid").val(itemid);
		if(increment>0){
		$("#additemqty").val(increment);
		$("#incrementqty").val(increment);
		}else
		$('#additemqty').val('');
		//$('#additemproject').attr('selectedIndex',0);
		//$('#additemproject option:first-child').attr("selected", "selected");
		//document.getElementById('additemproject').value=2;		
		$("#additempo").html('<select name="quote" required></select>');
		$('#additemcostcode').html('<select name="costcode" required></select>');
		getquotecombo();
		getcostcodecombo()
		$("#addtoquotemodal").modal();
	}

	function addtopo1(quote)
	{
		//var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;

		var string = '<h3>RFQ created for the item.</h3><div><a target="_blank" href="<?php echo site_url("admin/quote/update/"); ?>/'+quote+'">Click here to view the Quote</a><br/><br/><br/><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>';
		$("#modalhtm").html(string);
		$("#addtoquotemodal1").modal();

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

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script>
	function getlatlong()
	{
		var address = $("#inputLocation").val();
		//alert(address);
		if(address)
		{
    		var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    $("#latitude").val(latitude);
                    $("#longitude").val(longitude);
                    //alert("Latitude: " + latitude + "\nLongitude: " + longitude);
                } else {
                    //alert("Request failed.")
                }
            });
		}
		$("#searchform").submit();
        return true;
	}
</script>
<script>
    $(document).ready(function() {
        InitChosen();

        $('#search_form').submit(function() {
            $('#search_type').val('suppliers');
            $('#search_form').attr('action', "<?php echo base_url('site/search'); ?>");
        });

    });


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

    function fetchOrder()
    {
        get_by = $("#inputSortBy").val();
        orderdir = $("#inputOrder").val();
        if(get_by=="all"){
            get_by = "";
        }
        $("#get_by").val(get_by);
        $("#filterorderdir").val(orderdir);
        $("#searchform").submit();
        return true;
    }
</script>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
             <form id="categorysearchform" name="categorysearchform" method="post" action="<?php //echo base_url('site/items');?>">
                   <input type="hidden" name="keyword" value="<?php //echo isset($keyword)?$keyword:"";?>"/>
                   <input type="hidden" id="breadcrumb" name="breadcrumb"/>
                   <input type="hidden" id="formcategory" name="category" value="<?php //echo isset($_POST['category'])?$_POST['category']:"";?>"/>
				      <div class="location control-group" style="margin:0% 0% 0% 2.5%; width:97.5%">
                            <?php //$this->load->view('site/catmenu.php');?>
                        </div>
                        </form>

               <div class="span9">
                <?php //if( (isset($searchfor) && $searchfor == "itemandtags") || !(isset($searchfor)))  { ?>
                	<!--<div class="breadcrumb-pms"><ul class="breadcrumb"><?php //echo $breadcrumb;?></ul></div>-->
                    <h3 class="titlebox" style="padding:0px 0px 0px 8px">Design Book</h3>

                    <div class="properties-rows">
                        <div class="row">
                            <?php
                            $i = 3;
                            if (isset($gallery) && count($gallery)>0) {
                            	foreach ($gallery as $item) {
                            	$i++; ?>
                                <div class="property span9">
                                  <?php $count=strlen($item->name); if($count>=20){ ?>
                                  <h2 class="title_top1" style="height:40px;word-wrap:break-word;font-variant:small-caps;overflow:hidden;">
                                  <?php } else { ?>
                                  <h2 class="title_top1" style="height:40px;font-variant:small-caps;">
                                  <?php } ?>
                                   <a href="<?php echo site_url('site/designbookdetail/'.$item->id); ?>" style="text-decoration:none;"><?php echo $item->name;?></a>
                                  </h2>
                                  <div class="row">
                                   <div class="body span6">
                                     <div class="title-price row">
                                        <div class="title span4">
                                      <?php if(isset($item->imagename) && $item->imagename!= "" && file_exists("./uploads/designbook/".$item->imagename)) { ?>

               <img style="height:200px;width:220px; max-height:210px;padding: 10px;" height="120" width="120" src="<?php echo site_url('uploads/designbook/'.$item->imagename);?>" alt="">
                                                <?php } else { ?>
                                    <img style="height:200px;width:220px;max-height: 120px; padding: 20px;" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                                <?php } ?>
                                                <!-- <div id="tagbox_<?php echo $item->id;?>"></div> -->												
												<!-- <div id="taglist_<?php echo $item->id;?>"></div> -->
                                             </div>

                                                <div class="price">
                                                	<!--<p>LIKES <br> COMMENTS</p>-->
                                                </div>

                                          </div>
                                      </div>
                                  </div>
                                </div>
                                <?php } }
                                else { ?>

                                	 <div class="alert alert-error" style="margin-left:30px;">
                                    <button data-dismiss="alert" class="close" type="button">X</button>
                                    <strong>No Records Found</strong>
                                </div>

                             <?php   }


                                ?>
                        </div>
                    </div>

                    <div class="pagination pagination-centered">
                        <?php $this->view('site/paging'); ?>
                    </div><?php //} ?>
                </div>

                <!--<div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title"><h2 class="block-title">Main Menu</h2></div>
                        <div class="content_sup"></div>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
</div>

