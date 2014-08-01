<?php //echo '<pre>'; print_r($inventory);die;?>
<?php echo '<script>var addtocarturl="' . site_url('cart/addtocart') . '";</script>' ?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/site/assets/js/jquery.elevatezoom.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.timepicker.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<link href="<?php echo base_url(); ?>templates/admin/css/jquery.timepicker.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<style type="text/css">

 .videocontent {
	width:80%;
	max-width: 640px;
	margin: 0 auto;
}
 
 object {
position:absolute;
top:0px;
left:0px;}


</style>

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/jquery.jqzoom.css" type="text/css">
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
<script>
	$(document).ready(function(){
		/*$('.jqzoom').jqzoom({
            zoomType: 'innerzoom',
            preloadImages: false,
            alwaysOn:false
        });*/
		
		jwplayer("myvideo5").setup({
        	file: "<?php echo site_url('uploads/item/' . $item->images[0]->filename) ?>"               
    });
    
		<?php if(isset($item->zoom) && $item->zoom==1) {  ?> $("#bigimage").elevateZoom(); <?php } ?>
		$("#contentimage").elevateZoom(); 
	});
	function setmiles(miles)
	{
		$("#radirange").val(miles);
		$("#searchform").submit();
	}
	
	function getExtension(filename) {
		var parts = filename.split('.');
		return parts[parts.length - 1];
	}

	function isVideo(filename) {
		var ext = getExtension(filename);
		switch (ext.toLowerCase()) {
			case 'm4v':
			case 'avi':
			case 'mpg':
			case 'mp4':
			case 'flv':
			case 'wmv':
			case 'movie':
			case 'mpeg':
			case 'mpe':
			case 'qt':
			case 'mov':
			// etc
			return true;
		}
		return false;
	}
	
	function changeimage(source)
	{
		if(isVideo(source)){
			$('#videolist').css({display: "block"});
			$('#imagelist').css({display: "none"});
			/*params = document.getElementsByTagName('param');
			console.log(params);
			params[0].value = source;*/
			
			jwplayer("myvideo5").setup({
        	file: source               
    });
    
		}else {
		$('#imagelist').css({display: "block"});
		$('#videolist').css({display: "none"});
		$("#bigimage").attr('src',source);
		<?php if(isset($item->zoom) && $item->zoom==1) {  ?> $("#bigimage").elevateZoom(); <?php } ?>
        $("#bigimage").attr('data-zoom-image1',source); 
        <?php if(isset($item->zoom) && $item->zoom==1) {  ?> $("#bigimage").elevateZoom(); <?php } ?>
		}
	}
</script>
<script type="text/javascript" charset="utf-8">
    $(document).ready( function() {
    	$("#daterequested").datepicker();
        $("#day").datepicker();
        $("#time").timepicker({
            'minTime': '6:00am',
            'maxTime': '11:30pm',
            'showDuration': false
        });
        
    	$('#datatable').dataTable( {
    		"sPaginationType": "full_numbers",
    		"aaSorting": [[ 8, "asc" ]],
    		"aoColumns": [
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		null,
            		{ "bSortable": false }
    			],
   			 "aoColumnDefs": [
                { "sType": 'currency', "aTargets": [5] }
                ]
    		} );

    	var validChars = "$��c" + "0123456789" + ".-,'";
        
        // Init the regex just once for speed - it is "closure locked"
        var str = jQuery.fn.dataTableExt.oApi._fnEscapeRegex( validChars ), re = new RegExp('[^'+str+']');
          
          
        jQuery.fn.dataTableExt.aTypes.unshift(
             function ( data )
                {
                        if ( typeof data !== 'string' || re.test(data) ) {
                                return null;
                        }
          
                        return 'currency';
                }
        );
         
        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
                "currency-pre": function ( a ) {
                        a = (a==="-") ? 0 : a.replace( /[^\d\-\.]/g, "" );
                        return parseFloat( a );
                },
          
                "currency-asc": function ( a, b ) {
                        return a - b;
                },
          
                "currency-desc": function ( a, b ) {
                        return b - a;
                }
        } );
	
   	
     	 
   	 	 $('.dataTables_length').hide();
      	 $('#datatable_filter').hide();
      	 $('#datatable_paginate').hide();
    	 $('#datatable_info').hide();
    })
                       
</script>

<script>


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
    function addtocart(itemid, companyid, price, minqty, isdeal)
    {
    	if(typeof(minqty)==='undefined') minqty = 0;
    	if(typeof(isdeal)==='undefined') isdeal = 0;
        var qty = prompt("Please enter the quantity you want to buy",minqty?minqty:"1");
        if(isNaN(parseInt(qty)))
        {
            return false;
        }
        if(qty < minqty)
        {
            alert('Minimum quantity to order is '+ minqty);
            return false;
        }
        var data = "itemid=" + itemid + "&company=" + companyid + "&price=" + price + "&qty=" + qty + "&isdeal=" + isdeal;
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
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>
<script>
	function addtopo(itemid)
	{
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
	function rfqformsubmit()
	{
		var d = $("#addtoquoteform").serialize();

        
        $.ajax({
            type: "post",
            url: rfqurl,
            data: d
        }).done(function(data) {
            if (data == 'Success')
            {
                alert('RFQ created for the item.')
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



<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                	<div class="breadcrumb-pms"><ul class="breadcrumb"><?php echo $breadcrumb;?></ul></div>
                	<table width="100%">
                    	<tr>
                        	<td align="left"> <h2 class="page-header"><?php echo $item->itemcode;?></h2></td>
                        	<td align="right">
                            	<!-- AddThis Button BEGIN -->
                            	<div class="addthis_toolbox addthis_default_style ">
                            	<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
                            	<a class="addthis_button_tweet"></a>
                            	<a class="addthis_counter addthis_pill_style"></a>
                            	</div>
                            	<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-536087a3159911fb"></script>
                            	<!-- AddThis Button END -->
                            	
                        	</td>
                    	</tr>
                    </table>
                    <div class="Quotedetail">
                         <?php echo (isset($totalQuote))?$totalQuote:"0";?> Lifetime Buying Leads Posted on MaterialKing.com 
                    </div>
                    <div class="carousel property">
                    </div>

                    <div class="property-detail">
                        <?php  if(isset($cat_image) && $cat_image!= "" && file_exists("./uploads/category-banners/".$cat_image)) { ?>
                        <div class="category-image" style="margin: 0px 0px 5px;">
                            <img src="<?php echo site_url('uploads/category-banners/'.$cat_image);?>" class="cat-image" style="width:830px; height:200px;">
                        </div>
                        <?php } ?>
                        <div class="pull-left overview">
                            <div class="row">
                                <div class="span3" style="text-align: center">
                                
                                    <div class="clearfix">
                                        <div id="imagelist" <?php if($filetype=='video') { ?> style="display:none;" <?php } ?> class="clearfix">
                                            <a href="javascript:void(0);" rel='gal1'>
                                                <img id="bigimage" alt="<?php echo $item->item_img_alt_text;?>" src="<?php echo site_url('uploads/item/' . $item->images[0]->filename) ?>" data-zoom-image1="<?php echo site_url('uploads/item/' . $item->images[0]->filename); ?>"  style="border: 4px solid #666;with:250px;height:250px">
                                            </a>
                                    	
                                        </div>
                                        <div id="videolist" <?php if($filetype=='video') { ?> style="display:block;" <?php } else { ?> style="display:none;" <?php } ?> class="clearfix">
                                        <!-- <object width="338" height="300">
    <param name="src" value="<?php echo site_url('uploads/item/' . $item->images[0]->filename) ?>">
    <param name="autoplay" value="false">
    <param name="controller" value="true">
    <param name="bgcolor" value="#333333">
    <embed TYPE="application/x-mlayer2" src="./video/video.wmv" autostart="false" loop="false" width="250" height="250" controller="true" bgcolor="#333333">
    </embed>
</object> -->
                                        <div width="338" height="300" id="myvideo5"></div>     
                                        </div>
                                    	<br/>
                                        
                                    	<div class="clearfix" >
                                    	<ul id="thumblist" class="clearfix" >
                                    		<?php foreach($item->images as $img){?>
                                    		<li>
                                    		<a class="zoomThumbActive" href='javascript:void(0);'  onclick="changeimage('<?php echo site_url('uploads/item/' . $img->filename) ?>');">
                                    		<img style="width:50px;height:45px;margin-right:3px;"  src='<?php echo site_url('uploads/item/thumbs/' . $img->filename) ?>'  alt="<?php echo $item->item_img_alt_text;?>"></a>
                                    		</li>
                                    		<?php }?>
                                    	</ul>
                                    	</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="min-height: 240px;">
                            <p><?php echo $item->listinfo; ?></p>
                        </div>
                        <br/>
						

                        <div>
                            <?php 
                            if($item->wiki){
                            	$patternsrc = '/src="([^"]*)"/';
                            	preg_match($patternsrc, $item->wiki, $matches);
                            	if(isset($matches[1]) && $matches[1]!=""){
                            		$src = $matches[1];
                            		unset($matches);
                            	}else
                            	$src = "";

                            	$patternalt = '/alt="([^"]*)"/';
                            	preg_match($patternalt, $item->wiki, $matchesalt);
                            	if(isset($matchesalt[1]) && $matchesalt[1]!=""){
                            		$alt = $matchesalt[1];
                            		unset($matchesalt);
                            	}
                            	else
                            	$alt = "";
                           	
                       ?> 
                       <p><?php echo '<img id="contentimage" data-zoom-image3="'.$src.'" src="'.$src.'" alt="'.$alt.'"  '; ?></p> 
                        <?php } else { ?> 
                        <p><?php echo $item->wiki;?> </p> 
                        <?php } ?>
                        </div>
                        
                        <div class="tabbable"> <!-- Only required for left/right tabs -->
                            <ul class="nav nav-tabs">
                                <li class="active"><a href="#tab1" data-toggle="tab">Overview</a></li>
                                <li><a href="#tab2" data-toggle="tab">Description</a></li>
                                <li><a href="#tab3" data-toggle="tab">Details</a></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="tab1">
                                    <p>
                                        <strong> Item Name:</strong> <?php echo $item->itemname; ?>
                                        <br/>
                                        <strong>Unit:</strong> <?php echo $item->unit; ?>
                                        <div><strong>Tags:</strong><?php if(!empty($item->tags)){?><ul class="tags"><?php 
                                        $tags = explode(",",$item->tags);
                                        foreach ($tags as $tag){
										$tag = trim($tag);
										
										?>
                                        <li><a class="tag" href="<?php echo site_url("site/tag/".str_replace('%2F', '/', urlencode($tag)));?>"><?php echo $tag;?></a></li>
                                        <?php } ?></ul><?php } ?></div>
                                    </p>
                                </div>
                                <div class="tab-pane" id="tab2">
                                    <p><?php echo nl2br($item->description); ?></p>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <p><?php echo nl2br($item->details); ?></p>
                                </div>
                            </div>
                        </div>
                          <?php if(@$item->featureditem){?>
                          <br/>
                          <table class="span5">
                              <tr>
                                  <td>Sold By:<?php echo $item->featuredsupplierdetails->title;?> at $<?php echo $item->featureditem->ea;?></td>
                                  <td>
                                      <a class="btn btn-primary" href="javascript:void(o)" onclick="addtocart(<?php echo $item->id; ?>, <?php echo $item->featuredsupplier; ?>, <?php echo $item->featureditem->ea ? $item->featureditem->ea : 0; ?>,<?php echo (isset($item->featureditem->minqty))?$item->featureditem->minqty:'1';?>)">
                                      	<i class="icon icon-shopping-cart"></i> Buy Now
                                      </a>
                                  </td>
                              </tr>
                              <?php if(@$item->featureditem->manufacturername){?>
                              <tr>
                              	<td colspan="2">Manufacturer: <?php echo $item->featureditem->manufacturername;?></td>
                              </tr>
                              <?php }?>
                          </table>
                          <br/>
                      
                          <?php }?>
	
                            <?php
                            if ($filtermanufacturer) {
                            ?>
                            <br/><br/>
                            <table width="90%">
                            <tr>
                            <td>View By Manufacturer:</td>
                            <td>
                                        <form method="post" action="<?php echo site_url('site/item/'.$item->id);?>">
                            	<select id="manufacturer1" onchange="$('#manufacturer').val($('#manufacturer1').val());$('#searchform').submit()">
                            		<option value="">All</option>
                            		<?php foreach($filtermanufacturer as $fimf) if($fimf){?>
                            		<option value="<?php echo $fimf->id;?>" <?php echo $fimf->id==@$_POST['manufacturer']?'SELECTED':'';?>><?php echo $fimf->title;?></option>
                            		<?php }?>
                            	</select>
                                        </form>
                            </td>
                            </tr>
                            </table>
                            <?php }?>
                            <div class="pull-right">
                            <br/><br/>
                            <?php if ($this->session->userdata('site_loggedin')){?>
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtopo(<?php echo $item->id; ?>)">
                                    <i class="icon icon-plus"></i> <br/>Add to RFQ
                                </a>
                            	<br/><br/>
                            <?php }else{?>
                            <a class="btn btn-primary" style="margin-left:30px;" href="javascript:void(0)" onclick="$('#createmodal').modal();">
                            <i class="icon icon-plus"></i> <br/>Add to RFQ
                            </a>
                            <?php } ?>
                                <a href="<?php echo site_url('company/register'); ?>">
                                    Have one to sell? Start selling now!
                                </a>
                            </div>
                            <br/><br/>
                            <h4>
                            	Search Radius(miles):
                            	<a href="javascript:void(0)" onclick="setmiles(20)">20</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(60)">60</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(120)">120</a>&nbsp;&nbsp;
                            	<a href="javascript:void(0)" onclick="setmiles(240)">240</a>&nbsp;&nbsp;
                            </h4>
                            <?php if(!$inventory){?>
                            No nearby suppliers for the item. Try a larger search radius.
                            <?php }?>
                            <table id="datatable" class="table table-bordered smallfont">
                            	<thead>
                                <tr>
                                    <th>Supplier</th>
                                    <th>Code</th>
                                    <th>Item</th>
                                    <th>Manuf.</th>
                                    <th>Part#</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Address</th>
                                    <th>Dist. (mi)</th>
                                    <th>Buy</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if($inventory)
                                    foreach ($inventory as $inv)
                                    if ($inv->ea) 
                                    {
                                        $price = $inv->ea;
                                        
                                ?>
                                <tr>
                                    <td><a href="<?php echo site_url('site/supplier/'.$inv->companydetails->username);?>"><?php echo $inv->companydetails->title . $inv->joinstatus; ?></a> </td>
                                    <td class="tinyfont"><?php echo $inv->itemcode ?> </td>
                                    <td><?php echo $inv->itemname ?> </td>
                                    <td><?php echo $inv->manufacturername ?> </td>
                                    <td class="tinyfont"><?php echo $inv->partnum ?> </td>
                                    <td>
                                    	<?php echo '$'.$inv->ea; ?>
                                    	<?php if($inv->ea != $inv->listprice && $inv->listprice != ''){?>
                                    		<br/><strike>$<?php echo $inv->listprice ?></strike>
                                    	<?php }?>
                                    	<br>Min.Order:<?php echo $inv->minqty;?>
                                    </td>
                                    <td>
                                    <?php echo $inv->instock ? 'Yes' : 'No'; ?>
                                    <?php echo $inv->qtyavailable?'<br>Stock:'.$inv->qtyavailable:'';?>
                                    
                                    </td>
                                    <td class="tinyfont"><?php echo nl2br($inv->companydetails->address); ?> </td>
                                    <td><?php echo @$inv->dist ? number_format($inv->dist, 2) : ' '; ?></td>
                                    <td align="center">
                                        <a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $inv->itemid; ?>, <?php echo $inv->company; ?>, <?php echo $inv->ea; ?>, <?php echo $inv->minqty; ?>)">
                                            <i class="icon icon-plus"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                        <?php if($amazon){ ?>
                        
                        <table class="table table-bordered">
                            <tr>
                                <th>Amazon Item Name</th>
                                <th>Item Price</th>
                                <th>Link to Product</th>
                            </tr>
                            <tr>
                                
                                <td><?php echo $amazon->amazon_name; ?></td>
                                <td>$<?php echo $amazon->amazon_price; ?></td>
                                <td><a target="_blank" href="<?php echo $amazon->amazon_url; ?>">Click here</a></td>
                            </tr>

                        </table>

                        <?php } ?> 
                         <div id="container-highchart" class="span4" style="min-width: 200px ;height: 500px; margin: 0 auto; width:100%"></div>
					    <script type="text/javascript">
					   $(function () {
						   var dataChart = new Array;
						   var suppliers =new Array();
						   var ser = new Array();
						   var total = 0;
						<?php 
						if($inventory)
						foreach ($inventory as $inv)
						if ($inv->ea)
						{
							$price = $inv->ea;
							?>
							ser.push({name:"<?php echo $inv->companydetails->title; ?>",data:[parseFloat("<?php echo $price; ?>")]});
							total = total + <?php echo $price;?>;
							<?php 
						}
						?>
			          var totalSuppliers = <?php echo count($inventory);?>;
			    
					        $('#container-highchart').highcharts({
					            chart: {
					                type: 'column'
					            },
					            title: {
					                text: 'Current Market Prices'
					            },
					            subtitle: {
					                text: 'Fair Market Price = $ '+parseFloat(total/totalSuppliers).toFixed(2)
					            },
					            xAxis: {
					                categories: ["Suppliers"],
					                title: {
					                    text: null
					                }
					            },
					            yAxis: {
					                min: 0,
					                title: {
					                    text: 'Current Price $',
					                    align: 'high'
					                },
					                labels: {
					                    overflow: 'justify'
					                }
					            },
					            tooltip: {
					                valueSuffix: ' $'
					            },
					            plotOptions: {
					                bar: {
					                    dataLabels: {
					                        enabled: true
					                    }
					                }
					            },
					            plotOptions: {
				                       series: {
				                           dataLabels: {
				                        	   verticalAlign: "bottom",
				                               enabled: true,
				                               format: '$ {point.y:.2f}'
				                           }
				                       }
				                   },
					            
					            credits: {
					                enabled: false
					            },
					            series: ser
					        });
					    });
					    
					   </script>
		        <h2>Request Assistance</h2>
                        <a name="form"></a>
                        <?php echo $this->session->flashdata('message'); ?>
                        
        				<form method="post" action="<?php echo site_url('site/sendrequest/'.$item->id);?>">
        					<input type="hidden" name="redirect" value="item/<?php echo $item->url?>"/>
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
        							<td><input type="text" name="phone" required/></td>
        						</tr>
        						
        						<tr>
        							<td id="daytd">Best day to call</td>
        							<td><input type="text" id="day" name="day"/></td>
        						</tr>
        						
        						<tr>
        							<td id="timetd">Best time to call</td>
        							<td><input type="text" id="time" name="time" value="6:00am"/></td>
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
        				</form>
                    </div>
                </div>
                <div class="sidebar span3">
                    <div class="widget contact">
                        <div class="title">
                            <h2 class="block-title">Search Supplier</h2>
                        </div>
                        <div class="content">
                            <form id="searchform" method="post">
                                <div class="control-group">
                                    <label class="control-label" for="address">
                                        To find availability near your job site or current location please enter the address:
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="address" name="address" value="<?php echo @$_POST['address']; ?>">
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="radirange">
                                        Show suppliers within radius of:
                                    </label>
                                    <div class="controls">
                                        <input style="width: 60px" type="text" id="radirange" name="miles" value="<?php echo @$_POST['miles']; ?>"> Miles
                                    </div>
                                </div>
                                <input type="hidden" id="manufacturer" name="manufacturer" value="<?php echo @$_POST['manufacturer'];?>"/>

                                <?php if ($this->session->userdata('site_loggedin')) { ?>
                                    <div class="control-group">
                                        <label class="control-label" for="radirange">
                                            Only Show suppliers in my Network:
                                        </label>
                                        <div class="controls">
                                            <input type="checkbox" value="1" id="innetwork" name="innetwork" <?php echo @$_POST['innetwork'] ? 'checked="checked"' : ''; ?>/>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="form-actions">
                                    <br/>
                                    <input type="submit" class="btn btn-primary arrow-right" value="Search">
                                </div>
                            </form>
                        </div>
                    </div>
                
                    <?php if($item->articles){?>
                    <div class="widget contact">
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5>Information and Resources</h5>
                                    </label>
                                    <div class="controls">
                                    	
                                    	<?php foreach($item->articles as $article){?>
                                    		<a href="<?php echo site_url('site/article/'.$article->url);?>"><?php echo $article->title?></a><br/>
                                    	<?php }?>
                                    </div>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>

                    <?php if(@$relateditems){?>
                    <div class="widget contact">
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5>Related Items</h5>
                                    </label>
                                    <div class="controls">
                                    	<table>
                                    	<?php foreach($relateditems as $ri){?>
                                    		<tr>
                                    			<td>
                                    				<?php if($ri->item_img){?>
                                    				<img style="width:65px;height:65px" src="<?php echo site_url('uploads/item/'.$ri->item_img);?>"/>
                                    				<?php } else {?>
                                    				<img style="width:65px;height:65px" src="<?php echo site_url('uploads/item/big.png');?>"/>
                                    				<?php }?>
                                    			</td>
                                    			<td>
                                    				<a href="<?php echo site_url('site/item/'.$ri->url);?>" target="_blank"><?php echo $ri->itemname?></a>
                                    			</td>
                                    		</tr>
                                    	<?php }?>
                                    	</table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php }?>
                    
                    
                    <?php if(@$dealfeed){//NOT USED NOW?>
                    <div class="widget contact">
                        <div class="content">
                            <label class="control-label" for="radirange">
                                <h5 class="block-title">Supplier Deals</h5>
                            </label>
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
                        		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtocart(<?php echo $di->itemid; ?>, <?php echo $di->company; ?>, <?php echo $di->dealprice ? $di->dealprice : 0; ?>, <?php echo $di->qtyreqd ? $di->qtyreqd : 0; ?>,1)">
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
                
                    <div class="widget contact">
                        <div class="content">
                            <form>
                                <div class="control-group">
                                	<label class="control-label" for="radirange">
                                    	<h5>Free Professional Assistance</h5>
                                    </label>
                                    <div class="controls">
                                    	We are construction procurement professionals and are happy to help.
                                    </div>
                                    <a href="#form">Request</a>
                                </div>
                            </form>
                        </div>
                    </div>                    
                </div>
            </div>




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
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="rfqformsubmit();">Add</button>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>