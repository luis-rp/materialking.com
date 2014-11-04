<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<?php echo '<script>var checkbankaccount="' . site_url('cart/checkbankaccount') . '";</script>' ?>
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


    function addtocart(itemid, companyid, price, minqty, unit, itemcode, itemname, isdeal)
    {
    	if(itemcode=="")
    	{
    		itemcode=itemname;
    	}
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

</script>



<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script>
$(function() {
$( document ).tooltip();
});
</script>

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
                       
                <div class="span9">
                   <h3 class="titlebox" style="padding:0px 0px 0px 0px">
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

                        <?php foreach ($inventory as $item) if ($item->ea) {  ?>
                        <div class="property span9 PlumbingSupply">
                       <?php if(isset($item->itemcode)) {
                        $count=strlen($item->itemcode); if($count<=20){ ?>
                        <h2 class="title_top1">
                        <?php //echo $item->itemcode;
                          $arr1="";
                          $str="";
                          $arr1 = str_split($item->itemcode);
                             for($i = 0; $i < count($arr1);$i++){
							     if ($arr1[$i] == '1' || $arr1[$i] == '2' || $arr1[$i] == '3'  || $arr1[$i] == '4'  || $arr1[$i] == '5'  || $arr1[$i] == '6'  || $arr1[$i] == '7'  || $arr1[$i] == '8'  || $arr1[$i] == '9'  || $arr1[$i] == '0' || $arr1[$i] == '/' || $arr1[$i] == '-'){
								    $arr1[$i] = "<span style='color:red;'>".$arr1[$i]."</span>";}}
								    $str=implode("",$arr1);
                                    echo $str; ?>
                         </h2>
                        <?php } else {?>
                         <h2 class="title_top1"  style="word-wrap:break-word;line-height:20px;">
                         <?php //echo $item->itemcode;
								$arr1="";
                                  $str="";
                                    $arr1 = str_split($item->itemcode);
                                      for($i = 0; $i < count($arr1);$i++)
                                        {
										 if ($arr1[$i] == '1' || $arr1[$i] == '2' || $arr1[$i] == '3'  || $arr1[$i] == '4'  || $arr1[$i] == '5'  || $arr1[$i] == '6'  || $arr1[$i] == '7'  || $arr1[$i] == '8'  || $arr1[$i] == '9'  || $arr1[$i] == '0' || $arr1[$i] == '/' || $arr1[$i] == '-'){
										      $arr1[$i] = "<span style='color:red;'>".$arr1[$i]."</span>";
										}}
										$str=implode("",$arr1);
                                        echo $str;
                        ?></h2>
                       <?php } } else { ?>  <h2 class="title_top1" style="word-wrap:break-word;"><?php echo  $item->itemname; } ?></h2>
                            <div class="row">
                                <div class="image span3">
                                    <div class="content">
                                       <div class="sidepan"> <?php if ($item->image) { ?>
                                            <img style="max-height: 120px; padding: 20px;" src="<?php echo site_url('uploads/item/' . $item->image) ?>" alt="">
                                        <?php } else { ?>
                                            <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                        <?php } ?>
										</div>
                                    </div>
                                </div>

                                <div class="body2 span6 ">
                                    <div class="title-price row">
                                        <div class="title2 span4 ">

                                            <?php if(!empty($item->companynotes)){?>
                                            <div class="company-note" style="height:120px;overflow:auto;">
                                                <section><?php echo $item->companynotes; ?></section>
                                            </div>
                                            <?php } ?>
                                            <div class="area">
                                                <span class="key"><strong>Item Name:</strong></span>
                                                <span class="value"> <?php // echo $item->itemname;
                        						$arr2="";
                                                $str2="";
                                                $arr2 = str_split($item->itemname);
                                      			for($i = 0; $i < count($arr2);$i++)
                                        		{
												if ($arr2[$i] == '1' || $arr2[$i] == '2' || $arr2[$i] == '3'  || $arr2[$i] == '4'  || $arr2[$i] == '5'  || $arr2[$i] == '6'  || $arr2[$i] == '7'  || $arr2[$i] == '8'  || $arr2[$i] == '9'  || $arr2[$i] == '0' || $arr2[$i] == '/' || $arr2[$i] == '-'){
										      	$arr2[$i] = "<span style='color:red;'>".$arr2[$i]."</span>";
												}}
												$str2=implode("",$arr2);
                                        		echo $str2;
												?></span>

                                                <span class="key"><strong>Unit:</strong></span>
                                                <span class="value"><?php echo $item->unit; ?></span>

                                                <br/>

                                                <?php if($item->manufacturername) { ?>
												<span class="key"><strong>Manufacturer:</strong></span>
												<span class="value"> <?php echo $item->manufacturername; ?></span>
												<?php }?>

                                               <?php if($item->partnum) { ?>
                                                <span class="key"><strong>Part #:</strong></span>
                                                <span class="value"><?php echo $item->partnum; ?></span>
                                               <?php }?>
                                                <br/><br/>

                                                <a href="<?php echo site_url('site/item/'.$item->url);?>">View Item</a>
                                                <?php if($item->filename){?>
                                                	&nbsp;&nbsp;<a href="<?php echo site_url('uploads/item/'.$item->filename);?>" target="_blank">View Attachment</a>
                                                <?php }?>
                                            </div>
                                                <span class="pull-right1">
                                                	<?php echo $item->instock?'Available':'Not Available';?>
                                                	<br/>
                                                	Stock: <?php if($item->instock) { echo "Yes ";} echo $item->qtyavailable;?>
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
                                            <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $item->itemid; ?>, <?php echo $item->company; ?>, <?php echo $item->ea; ?>,<?php echo $item->minqty;?>,'<?php echo $item->unit ? $item->unit : '';?>','<?php echo htmlspecialchars(addslashes($item->itemcode));?>', '<?php echo htmlspecialchars(addslashes($item->itemname));?>')">
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
								<?php if(@$rating) {?>
                        	<tr>
                        			<td colspan="">Reviews:

                        			<?php echo $rating;?> <?php echo number_format($ratingvalue,2);?> / 5.00
                        			</td>
                        		</tr>
								<?php } ?>		
                        		 <tr>
                        			<td colspan="2">
                            			<a href="<?php echo site_url('site/supplier/'.$company->username);?>">
                            			<h4>Back to Profile</h4>
                            			</a>
                        			</td>
                        		</tr>
                        	</tbody></table>

                        </div>
                    </div>
                    
                    
                    <?php if(isset($inventory) && count($inventory)>0) {?>
                    <?php if(isset($breadcrumb2) && $breadcrumb2!="") {
                    	?>
                		 <h2>Sub Categories</h2>
                		   <div class="content_sup" style="height:72px;">
                   			 <div style="clear:both;"></div>
								<div class="breadcrumb-pms" style="width:200px;" >
								<ul class="" style="margin-left: -8px;"><?php if(isset($breadcrumb2) && $breadcrumb2!="") echo $breadcrumb2;?></ul>
								</div>
					      </div>
					<?php } } ?><br>
                    
                    <?php if(@$businesshrs){ ?>       
                    <?php if(@$businesshrs[0]->start != '' || @$businesshrs[1]->start != '' || @$businesshrs[2]->start != '' || @$businesshrs[3]->start != '' || @$businesshrs[4]->start != '' || @$businesshrs[5]->start != '' || @$businesshrs[6]->start != ''){ ?>                               
                    <div class="widget contact">
                    <div class="title">
                            <h2 align="center" class="block-title"><img style="height:20px;" src="<?php echo base_url(); ?>uploads/logo/time.png"/>&nbsp;Business Hours</h2>
                        </div>
                    <div class="content_sup">
                                
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
				   		$current_time = date('g:i a');
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
                 <?php } }?>
                                    
                  
			<?php if($types){
						$band = false;
						foreach ($types as $type){
							if ($type->category == 'Manufacturer')
								$band=true;
						}
					if($band){
				?>
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
            			        foreach ($types as $type){
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
                            <?php 
									}
                                                }
                                                 ?>
                             </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                 </div>
                 </div><!--pull right-->
			<?php } }?>


<div class="pull-right">

                 <div class="sidebar span3">


                    <?php if(@$adforsupplier){?>
                   <script src="<?php echo base_url(); ?>templates/site/assets/js/jquery.bxslider.min.js"></script>
					<!-- bxSlider CSS file -->
					<link href="<?php echo base_url(); ?>templates/site/assets/css/jquery.bxslider.css" rel="stylesheet" />

					        <script type="text/javascript">
								$(function() {$('.bxslider').bxSlider({

									});

								});
							</script>

                    <div class="widget contact">
                    <div class="title">
                            <h2 class="block-title">Classifieds by Supplier</h2>
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
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,'<?php echo $di->unit ? $di->unit : '';?>','<?php echo htmlspecialchars(addslashes($di->itemcode));?>', '<?php echo htmlspecialchars(addslashes($di->itemname));?>',1)">
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
                    
                    
               <div class="sidebar span3">
               <div class="widget contact">
               <div class="fb-like-box" data-href="<?php if(isset($company->fbpageurl)) echo $company->fbpageurl; ?>" data-width="200" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="true" data-show-border="true"></div>
               </div></div>
                    
               
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
          <div id="itemnamebox"></div>
          <br> Select Quantity
          </h4>
          <br>
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