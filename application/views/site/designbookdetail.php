
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.elevatezoom.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">


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
#imgtag2
{
	position: relative;
	
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
	background: #FFF;
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

.btn-green {
background-color: #ACC70A;
background-image: linear-gradient(to bottom, #BACF0B, #98BA09);
background-repeat: repeat-x;
border-color: #7C9710
}
</style>


<script type="text/javascript">

$(document).ready(function() {
		
	var counter = 0;
    var mouseX = 0;
    var mouseY = 0;
	
	$("#imgtag img").click(function(e) { // make sure the image is clicked
  var imgtag = $(this).parent(); // get the div to append the tagging list
  mouseX = ( e.pageX - $(imgtag).offset().left ); // x and y axis
  mouseY = ( e.pageY - $(imgtag).offset().top );
  $( '#tagit' ).remove( ); // remove any tagit div first
  //insert an input box with save and cancel operations.
  $( imgtag ).append( '<div id="tagit"><!-- <div class="box"></div>--><div class="name"><table><tr><td colspan="2">Tag Name</td></tr><tr><td  colspan="2"><input type="text" name="txtname" id="tagname" style="width:200px;" /></td></tr><tr><td  colspan="2">Tag Description</td></tr><tr><td  colspan="2"><textarea name="tagdesc" id="tagdesc"></textarea></td></tr><tr><td><input type="button" name="btnsave" value="Save" id="btnsave" /></td><td><input type="button" name="btncancel" value="Cancel" id="btncancel" /></td></tr></table></div></div>' );
  $( '#tagit' ).css({ top:mouseY, left:mouseX });
   
  $('#tagname').focus();    
});


$( document ).on( 'click',  '#tagit #btnsave', function(){
    name = $('#tagname').val();
    description = $('#tagdesc').val();
var img = $('#imgtag').find( 'img' );
var id = $( img ).attr( 'id' );
  $.ajax({
    type: "POST",
    url: "<?php echo site_url('site/savetag');?>",
    data: "pic_id=" + id + "&name=" + name + "&description=" + description + "&pic_x=" + mouseX + "&pic_y=" + mouseY + "&type=insert",
    cache: true,
    success: function(data){
    //$('#taghidid').val(data);
     var img = $('#imgtag2').find( 'img' );
     var id = $( img ).attr( 'id' );
     //get tags if present
     viewtag( id );
      $('#tagit').fadeOut();
    }
  });
    
});


// Cancel the tag box.
    $( document ).on( 'click', '#tagit #btncancel', function() {
      $('#tagit').fadeOut();
    });
    
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

	
	// load the tags for the image when page loads.
    var img = $('#imgtag2').find( 'img' );
	var id = $( img ).attr( 'id' );
	var companyid = 'company_'+id;
	var company = $('#'+companyid).val();	
	viewtag(id,company);
});



function viewtag(pic_id,company)
{	
	$('#pictureid').val(pic_id);
	// get the tag list with action remove and tag boxes and place it on the image.
	$.post( "<?php echo site_url('site/taglistofdetail');?>" ,  "pic_id=" + pic_id + "&company=" + company, function( data ) {
		//$('#taglist').html(data.lists);
		$('#tagbox').html(data.boxes);
	}, "json");	
}

</script>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">

               <div class="span9">
                    <div class="properties-rows">
                       <div class="row">
                            <?php if (isset($details)) { //echo "<pre>123"; print_r($details->imagename); die; ?>
                                <div class="property span9" style="width:90%;">
                                  <h2 class="title_top1" style="height:40px;font-variant:small-caps;"><?php echo $details->name;?> </h2>
                                  <div class="row">
                                   <div class="body span6">
                                     <div class="title-price row">
                                        <div id="imgtag2" class="title span6">
                                  <?php if(isset($details->imagename) && $details->imagename!= "" && file_exists("./uploads/designbook/".$details->imagename)) { ?>
                                        <img id="<?php echo $details->id;?>" src="<?php echo site_url('uploads/designbook/'.$details->imagename);?>" style="padding:10px;">
                                   <?php } else { ?>
                                         <img id="<?php echo $details->id;?>" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" style="padding:20px;"  alt="">
                                   <?php } ?>
                                         <input type="hidden" name="company_<?php echo $details->id;?>" id="company_<?php echo $details->id;?>" value="<?php echo $details->company;?>"/>
                                   		 <div id="tagbox"></div>
                                         </div>
                                         <div class="price">
                                            <!-- <p>LIKES <br> COMMENTS</p>-->
                                         </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <?php }  else { ?>
                                <div class="alert alert-error" style="margin-left:30px;">
                                    <button data-dismiss="alert" class="close" type="button">X</button>
                                    <strong>No Records Found</strong>
                                </div>
                                <?php   }  ?>

                          </div>
                    </div>
                </div>
                <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title"><h2 class="block-title">Main Menu</h2></div>
                        <div class="content_sup"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>





