
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<HEAD>		
	    <link rel="image_src" href="<?php if(isset($details->imagename)) echo site_url('uploads/designbook/'.$details->imagename);?>" />
		<META HTTP-EQUIV="imagetoolbar" CONTENT="no">
		<meta http-equiv="cache-control" content="no-cache"> <!-- tells browser not to cache -->
		<meta http-equiv="expires" content="0"> <!-- says that the cache expires 'now' -->
		<meta http-equiv="pragma" content="no-cache"> <!-- says not to use cached stuff, if there is any -->
		<meta property="fb:app_id" content="899376703411658"/> <!-- Facebook App ID for comment system  -->
		<meta property="og:url" content="<?php echo base_url(); ?>site/designbookdetail/<?php echo $details->id;?>"/>		
		<meta property="og:image" content="<?php if(isset($details->imagename)) echo site_url('uploads/designbook/'.$details->imagename);?>"/>		
	</HEAD>	

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.elevatezoom.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<?php echo '<script>var quoteurl = "' . site_url('site/getquotes') . '";</script>' ?>
<?php echo '<script>var costcodeurl = "' . site_url('site/getcostcodes') . '";</script>' ?>
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>

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
    var img = $('#imgtag2').find( 'img' );
	var id = $( img ).attr( 'id' );
	var companyid = 'company_'+id;
	var company = $('#'+companyid).val();	
	viewtag(id,company);
	
	 $("#daterequested").datepicker();    
});

function viewtagdescription(id){
	
	$('#' + id+'_').css({ opacity: 1.0 });
}


function hidetagdescription(id){
	
	$('#' + id+'_').css({ opacity: 0.0 });
}

function viewtag(pic_id,company)
{	
	$('#pictureid').val(pic_id);
	// get the tag list with action remove and tag boxes and place it on the image.
	$.post( "<?php echo site_url('site/taglistofdetail');?>" ,  "pic_id=" + pic_id + "&company=" + company, function( data ) {
		$('#taglist').html(data.lists);
		$('#tagbox').html(data.boxes);
	}, "json");	
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



<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=899376703411658&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
	<BODY>
                                         
  <div id="content">
    <div class="container">
        <div id="main">
            <div class="row">

               <div class="span9" style="width:100%">
                    <div class="properties-rows">
                       <div class="row">
                            <?php if (isset($details)) { ?>
                                <div class="property span9" style="width:100%">
                                  <h2 class="title_top1" style="height:40px;font-variant:small-caps;"><?php echo $details->name;?> </h2>
                                </div>
                             <?php }  ?>    
                       </div>
                    </div>
               </div> <br>
               
               <div class="pull-left" style="height:100%;width:55%;overflow:auto;">
               <?php if (isset($details)) { ?>
               	 <div id="imgtag2" class="title span6" style="width:100%">
                   <?php if(isset($details->imagename) && $details->imagename!= "" && file_exists("./uploads/designbook/".$details->imagename)) { ?>
                        <img id="<?php echo $details->id;?>" src="<?php echo site_url('uploads/designbook/'.$details->imagename);?>" style="padding:10px;">
                    <?php } else { ?>
                         <img id="<?php echo $details->id;?>" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" style="padding:20px;"  alt="">
                    <?php } ?>                  
                    <div id="tagbox"></div>                        
                 </div>
                <?php }  ?>
                </div>
                <br>
                 <div class="pull-right">  <br>             	
                 	<div class="pull-left"> 
                 		<div class="fb-comments" style="position:relative;margin-top:0px;width: 200px; height: 400px;color:black;" data-href="<?php echo base_url(); ?>site/designbookdetail/<?php echo $details->id;?>" data-width="200"  data-height="100" data-numposts="1" data-colorscheme="light">This is Comment</div> 
                	</div>
                                
                 	<div class="pull-right">
                       <?php if(isset($supplier)) { ?>        
                           <div class="span9 category-box">
                               <span><strong>Design Book Created By:</strong></span>
 								   <h2 class="supplier_new_sa">
 									  <a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>"><?php echo $supplier->title; ?></a></h2>
                                    	<div class="row">
                                    	
	                                        <div class="image span3">
	                                            <div class="content">
	                                                <?php if ($supplier->logo) { ?>
	                        <img style="padding-top: 5px; width:175px; height:160px" src="<?php echo site_url('uploads/logo/thumbs/'.$supplier->logo) ?>" alt="">
	                                                <?php } else { ?>
	                        <img style="padding-top: 5px; width:175px; height:160px" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
	                                                <?php } ?>
	                                            </div>
	                                        </div>

	                                        <div class="body_home span6">
	                                            <div class="title-price row">                                      
	                                                <div class="price">
	                                                    <?php echo $supplier->address; ?>
	                                                </div>
	                                            </div>                                           
	                                        </div>
                                        
                                    </div>                                    
                                  </div><br>
                                  
                            <?php } ?> 
                            <div style="text-align:right">     
                            <?php $maindesignurl = "<a href = '".site_url('site/designbook')."' >Go to Main Design Book</a>" ?>
		                    <?php if(isset($previd->id)) { ?><a href="<?php echo site_url('site/designbookdetail/'.$previd->id); ?>">< View Previous</a>
		                    <?php } else { echo $maindesignurl; $maindesignurl=""; } ?>
		                    &nbsp; &nbsp; &nbsp;     
		                    <?php if(isset($nextid->id)) { ?><a href="<?php echo site_url('site/designbookdetail/'.$nextid->id); ?>">View Next ></a>  	
		                    <?php } else echo $maindesignurl; ?>                                          
		           <input type="hidden" name="company_<?php echo $details->id;?>" id="company_<?php echo $details->id;?>" value="<?php echo $details->company;?>"/>
		                   </div>             
                    </div>                 
                  <div style="clear:both;"></div>    
                  <br> 
               </div>
               </div>
               <div style="width:50%;height:300px;overflow:auto;">
               <div id="taglist"></div>
               </div> 
             
              
               <div class="span9" style="width:100%;padding-top:100px;">
                    <div class="properties-rows">
                       <div class="row">
                            <?php if (isset($details)) { ?>
                                <div class="property span9" style="width:100%">                                         
                                  <div class="row">
                                     <div class="title-price row">
                                         <div class="price">
                                            <div class="fb-like" data-href="<?php echo base_url(); ?>site/designbookdetail/<?php echo $details->id;?>" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
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
                
            </div>
            
       <!-- </div>-->
        
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

</BODY>
</HTML>
