<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>

<?php echo '<script>var getpriceqtydetails="' . site_url('site/getpriceqtydetails') . '";</script>' ?>

<?php echo '<script>var getpriceperqtydetails="' . site_url('site/getpriceperqtydetails') . '";</script>' ?>

<?php echo '<script>var getnewprice="' . site_url('site/getnewprice') . '";</script>' ?>
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
	.title_top1{   color: #fff;
    font-size: 18px;
    font-weight: bold;}
	.PlumbingSupply{ min-height:450px; max-height:1100px}
	</style>

<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script>
$(function() {
$( document ).tooltip();
});
</script>

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.js"></script>
<script>
    $(document).ready(function() {
        InitChosen();
    });

    function industryitems(id)
    {
    	$("#typei").val(id);
    	$("#supplierform").submit();
    }


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

</script>

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jRating.jquery.js"></script>

<script>
$(document).ready(function() {
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
});
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

<script>

 
    function addtocart(itemid, companyid, price, minqty, unit, isdeal)
    {
    	if(typeof(minqty)==='undefined') minqty = 0;
    	if(typeof(isdeal)==='undefined') isdeal = 0;
        //var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");

       	$("#hiddenprice").val(price);
        $("#cartprice").modal();
        var selected = "";
        $("#unitbox").html("Unit Type: "+unit+"<br/>");
        var strselect = ('Qty');
        strselect += '&nbsp;<select style="width:80px;" id="qtycart" onchange="showmodifiedprice('+itemid+','+companyid+','+price+');">';
        for (i = 1; i <=100; i++) {
        	if(i == minqty)
        	selected = 'selected';
        	else
        	selected = "";
           	strselect += '<option value="'+i+'"'+selected+'>'+i+'</option>';
   			}
   		strselect += '</select>&nbsp;&nbsp; <input type="button" class="btn btn-primary" value="Add to cart" onclick="addtocart2('+itemid+','+companyid+','+price+','+minqty+','+isdeal+')" id="addtocart" name="addtocart"/>';
        $('#cartqtydiv').html(strselect);

        var data = "itemid="+itemid+"&companyid="+companyid;
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

    function showmodifiedprice(itemid, companyid, price){

    	qty = ($('#qtycart').val());
    	var data2 = "itemid="+itemid+"&companyid="+companyid+"&qty="+qty+"&price="+price;

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
        var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + $("#hiddenprice").val() + "&qty=" + qty + "&isdeal=" + isdeal;
        //alert(data); return false;
        $.ajax({
            type: "post",
            data: data,
            url: addtocarturl
        }).done(function(data) {
            alert(data);
            window.location = window.location;
        });

    }

</script>    



<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script>
$(function() {
$( document ).tooltip();
});
</script>

<form id="supplierform" method="post" action="<?php echo site_url('site/suppliers')?>">
	<input type="hidden" id="typei" name="typei"/>
</form>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                   <h3 class="titlebox" style="padding:0px 0px 0px 8px">
                    <img style="height:100px; width:100px; position:relative;" src="<?php if($supplier->logo) { echo base_url(); ?>uploads/logo/thumbs/<?php echo $supplier->logo; }
                    else { echo base_url(); ?>templates/site/assets/img/default/big.png <?php } ?>" alt="<?php echo $supplier->logo; ?>">&nbsp;
                    Welcome
                    <?php if($this->session->userdata('site_loggedin')){echo $this->session->userdata('site_loggedin')->companyname;}else{echo 'Guest';}?>,
                    to
                    <?php echo $company->title; ?> Store
                    </h3>
                    <h3 class="titlebox" style="padding:0px 0px 0px 8px"><a href="<?php echo site_url('site/supplier/'.$company->username);?>">Back to Profile</a></h3>
                    <br/>

                	<div class="breadcrumb-pms"><?php echo @$breadcrumb;?></div>
                	<br/>
                	<?php echo @$norecords;?>

                    <div class="properties-rows">
                      <div class="row">

                        <?php
                            foreach ($inventory as $item)
                            if ($item->ea) {
                        ?>
                        <div class="property span9 PlumbingSupply">
                        <h2 class="title_top1"><?php echo $item->itemcode; ?></h2>
                            <div class="row">
                                <div class="image span3">
                                    <div class="content">
                                       <div class="sidepan"> <?php if ($item->image) { ?>
                                            <img style="max-height: 120px; padding: 20px;" src="<?php echo site_url('uploads/item/' . $item->image) ?>" alt=""></div>
                                        <?php } else { ?>
                                            <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                        <?php } ?>

                                    </div>
                                </div>

                                <div class="body2 span6 ">
                                    <div class="title-price row">
                                        <div class="title2 span4 ">
                                           
                                            <?php if(!empty($item->companynotes)){?>
                                            <div class="company-note" style="height:120px;overflow:auto;">
                                                <?php echo $item->companynotes; ?>
                                            </div>
                                            <?php } ?>
                                            <div class="area">
                                                <span class="key"><strong>Item Name:</strong></span>
                                                <span class="value"> <?php echo $item->itemname; ?></span>

                                                <span class="key"><strong>Unit:</strong></span>
                                                <span class="value"><?php echo $item->unit; ?></span>

                                                <br/>

                                                <span class="key"><strong>Manufacturer:</strong></span>
                                                <span class="value"> <?php echo $item->manufacturername; ?></span>

                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value"><?php echo $item->partnum; ?></span>
                                                <br/><br/>

                                                <a href="<?php echo site_url('site/item/'.$item->url);?>">View Item</a>
                                                <?php if($item->filename){?>
                                                	&nbsp;&nbsp;<a href="<?php echo site_url('uploads/item/'.$item->filename);?>" target="_blank">View Attachment</a>
                                                <?php }?>
                                            </div>
                                                <span class="pull-right1">
                                                	<?php echo $item->instock?'Available':'Not Available';?>
                                                	<br/>
                                                	Stock: <?php echo $item->qtyavailable;?>
                                                	<br/>
                                                	Min. Order. Qty.: <?php echo $item->minqty;?>
                                                </span>
                                        </div>
                                        <div class="price">

                                            <?php if($item->price){?>
                                        	<div id="parent">
          									<img  style="height:30px;widht:30px;" src="<?php echo site_url('templates/front/assets/img/icon/phone.png');?>"  title="<?php if(isset($supplier->phone)) echo $supplier->phone; ?>"  />&nbsp;Call for Price</div>

                                       <?php }else{?>
                                    	$<?php echo $item->ea; ?>
                                            <!--<br/><br/>-->
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $item->itemid; ?>, <?php echo $item->company; ?>, <?php echo $item->ea; ?>,<?php echo $item->minqty;?>,'<?php echo $item->unit ? $item->unit : '';?>')">
                                                <i class="icon icon-plus"></i>
                                            </a>
                                        <?php } ?>

                                        </div>										
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php }?>
                        <div class="pagination pagination-centered">
                            <?php $this->view('site/paging'); ?>
                        </div>

                    	</div>
                    </div>

                </div>


                <div class="sidebar span3">
                	<?php if(@$categorymenu != '<ul></ul>'){?>
                    <h2>Item Filter</h2>

                    <div class="content_sup" style=" padding-bottom:35px;">
                        <form id="categorysearchform" name="categorysearchform" method="post" action="<?php echo base_url('store/items/'.$company->username);?>">
                            <input type="hidden" name="keyword" value="<?php echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumb" name="breadcrumb"/>
                            <input type="hidden" id="formcategory" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:"";?>"/>

                            <div class="location control-group">
                            	<?php $this->load->view('site/catmenu.php');?>
                            </div>
                        </form>

                        <form id="categorysearchform2" action="<?php echo base_url('store/items/'.$company->username);?>" method="post">
                    	<input type="hidden" id="searchbreadcrumbcategory" name="searchbreadcrumbcategory" />
                    	
                    </form>
                        
                    </div>
                    <?php }?>
                    <div style="clear:both;"></div>
<div class="breadcrumb-pms" style="width:200px;" ><ul class="" style="margin-left: -8px;"><?php if(isset($breadcrumb2) && $breadcrumb2!="") echo $breadcrumb2;?></ul></div>
<div class="pull-right">
                <div class="sidebar span3">
                    <div class="widget contact">
                      	<label for="radirange" class="control-label">
                                    	<h5>Manufacturers Carried:</h5>
                                    </label>
                        <div class="content_sup">
                            <form>
                                <div class="control-group">
                              
                                    <div class="controls">

                          <table cellpadding="4" cellspacing="2">
            			    <?php
            			        foreach ($types as $type)
                                    if ($type->category == 'Manufacturer') {
                            ?>
                            	<tr>
                            	<td>
                                    <a style="text-decoration:none;" href="<?php echo site_url('store/items/'.$supplier->username.'/'.$type->id);?>">
                                    <?php if($type->image){?>
                                    <img src="<?php echo site_url('uploads/type/thumbs/'. $type->image);?>" alt="<?php echo $type->title; ?>"/>
                                    <?php }else{?>
                                    <?php echo $type->title; ?>
                                    <?php }?>
                                    </a>
                                    </td>
                              </tr>
                            <?php } ?>
                             </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                 </div>
                 </div><!--pull right-->



<div class="pull-right">

                 <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Main Menu</h2>
                        </div>

                        <div class="content_sup">
                        	<table width="100%" cellpadding="4">
                        		<tbody><tr>
                        			<td><b>Connection:</b> </td>
                        			<td><?php echo $supplier->joinstatus?$supplier->joinstatus:'Guest';?></td>
                        		</tr>

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
                                                <?php echo $type->title; ?>
                                                </a>
                                                </li>

                                        <?php } ?>
                                        </ul>
                        			</td>
                        		</tr>

                        	<tr>
                        			<td colspan="">Reviews:

                        			<?php echo $rating;?> <?php echo number_format($ratingvalue,2);?> / 5.00
                        			</td>
                        		</tr>

                        		 <tr>
                        			<td colspan="2">
                            			<a href="<?php echo site_url('site/supplier/'.$company->username);?>">
                            			Back to Profile
                            			</a>
                        			</td>
                        		</tr>
                        	</tbody></table>

                        </div>
                    </div>
                    
                    <?php if(@$adforsupplier){?>
                   <script type="text/javascript" src="<?php echo base_url();?>templates/site/assets/js/modernizr.custom.79639.js"></script>
			        <script type="text/javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.windy.js"></script>
			        <script type="text/javascript">	
						$(function() {
			
							var $el = $( '#wi-el' ),
								windy = $el.windy(),
								allownavnext = false,
								allownavprev = false;
			
							$( '#nav-prev' ).on( 'mousedown', function( event ) {
			
								allownavprev = true;
								navprev();
							
							} ).on( 'mouseup mouseleave', function( event ) {
			
								allownavprev = false;
							
							} );
			
							$( '#nav-next' ).on( 'mousedown', function( event ) {
			
								allownavnext = true;
								navnext();
							
							} ).on( 'mouseup mouseleave', function( event ) {
			
								allownavnext = false;
							
							} );
			
							function navnext() {
								if( allownavnext ) {
									windy.next();
									setTimeout( function() {	
										navnext();
									}, 150 );
								}
							}
							
							function navprev() {
								if( allownavprev ) {
									windy.prev();
									setTimeout( function() {	
										navprev();
									}, 150 );
								}
							}
			
						});
					</script>
              
                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Classifieds by Supplier</h2>
                        </div>
                        <div class="content_sup">
                           
                                <div class="control-group">
                               
                                   <div class="controls windy-demo">
                                   		<ul id="wi-el" class="wi-container">
                                    	<?php foreach($adforsupplier as $key=>$ad){?>
                                    	<li><img  src="<?php 
                                    	$pathinfo = pathinfo($ad->image);
                                    	echo base_url("/uploads/ads/".$pathinfo["filename"]."_thumb.".$pathinfo["extension"]);?>" alt="image<?php echo $key;?>"/><h4><?php echo $ad->title;?> $<?php echo $ad->price;?></h4><p><a href="<?php echo base_url("/classified/ad/".$ad->id);?>" class="btn btn-primary">Details</a></p></li>
                                     	<?php } ?>
                                    	</ul>
                                    	<nav>
											<span id="nav-prev">prev</span>
											<span id="nav-next">next</span>
										</nav>
                                     </div>
                                </div>
                            
                        </div>
                    </div>
                
                <?php }?>
                    
                    
                     <?php if(@$dealfeed){//NOT USED NOW?>
                    <div class="widget contact">
                    <label for="radirange" class="control-label">
                                <h5 class="block-title">Deals by Supplier</h5>
                            </label>
                        <div class="content_sup">
                       
                        	<table style="font-size: 12px;">
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
                        		<td colspan="3">
                        		<a href="<?php echo site_url("site/supplier/".$di->companyusername);?>"><?php echo $di->companyname?></a>
                        		</td>
                        	</tr>
                        	<tr>
                        		<td>
                        		<?php if($di->image) {?>
                        			<img style="width: 81px;height:80px" src="<?php echo site_url('uploads/item/thumbs/'.$di->image);?>" width="81" height="80">
                        		<?php } else {?>
                        		<img style="width: 81px;height:80px" width="81" height="80" src="<?php echo site_url('uploads/item/big.png');?>"/>
                        		<?php }?>
                        		</td>
                        		<td>
                        		<a href="<?php echo site_url("site/item/".$di->url);?>"><?php echo $di->itemname?></a>
                        		($<?php echo $di->dealprice;?> Min. Qty: <?php echo $di->qtyreqd;?>)
                        		</td>
                        		<td>
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,'<?php echo $di->unit ? $di->unit : '';?>',1)">
                                    <i class="icon icon-plus"></i>
                                </a>
                                </td>
                        	</tr>
                        	<tr>
                        		<td colspan="3"><?php echo $remaining;?> remaining</td>
                        	</tr>
                        	<tr>
                        		<td colspan="3">Hurry up, only <span class="red"><?php echo $di->qtyavailable;?> items</span> remaining</td>
                        	</tr>
                        	<tr><td colspan="6">&nbsp;</td></tr>
                        	<?php }?>
                        	</table>
                            
                        </div>
                    </div>
                    <?php }?>
                </div>
</div><!--pullright-->

                    </div>

                     <div style="clear:both;"></div>

               		 </div>

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
          Select Quantity  
          </h4>
          <br><br/>
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