<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>

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

function addtocart(itemid, companyid, price,minqty)
{
	var min=minqty;
	var qty = prompt("Please enter the quantity you want to buy",minqty);

	if(qty < min)
	{
		alert("Please Enter "+min+" or more value.");
		return false;
	}


	if(isNaN(parseInt(qty)))
	{
		alert("Please Enter Numeric value.");
		return false;
	}

	var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + price + "&qty=" + qty;
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
                        <div class="property span9">
                            <div class="row">
                                <div class="image span3">
                                    <div class="content">
                                        <?php if ($item->image) { ?>
                                            <img style="max-height: 120px; padding: 20px;" src="<?php echo site_url('uploads/item/' . $item->image) ?>" alt="">
                                        <?php } else { ?>
                                            <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                        <?php } ?>

                                    </div>
                                </div>

                                <div class="body2 span6 ">
                                    <div class="title-price row">
                                        <div class="title2 span4 ">
                                            <h2><?php echo $item->itemcode; ?></h2>
                                            <?php if(!empty($item->companynotes)){?>
                                            <div class="company-note" style="height:150px;overflow:auto;">
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
                                            $<?php echo $item->ea; ?>
                                            <!--<br/><br/>-->
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $item->itemid; ?>, <?php echo $item->company; ?>, <?php echo $item->ea; ?>,<?php echo $item->minqty;?>)">
                                                <i class="icon icon-plus"></i>
                                            </a>
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

                    <div>
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
                        <div class="content">
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
                            <h2 class="block-title">Main Menu of</h2>
                        </div>

                        <div class="content">
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
                </div>
</div><!--pullright-->

                    </div>

                     <div style="clear:both;"></div>

               		 </div>

                </div>



        </div>
    </div>
</div>