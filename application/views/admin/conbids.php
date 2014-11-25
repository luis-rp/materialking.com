<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
<?php
	//print_r($bids);die;

	$maxcountitems = count($quoteitems);

	$isawarded = $isawarded=='Yes'?true:false;
	$checkedarray = array();
	echo '<script>var tax='.$config['taxpercent'].';</script>';
	$awardeditemcompany = array();
	if($isawarded)
	{
		$awardedtotal = 0;
		if(@$awarded->items)
		foreach($awarded->items as $ai)
		{
			$awardeditemcompany[]=$ai->itemcode . $ai->company;
			$awardedtotal+=$ai->ea;
 		}
 		$awardedtotal = round($awardedtotal,2);
 		$awardedtax = $awardedtotal * $config['taxpercent'] / 100;
 		$awardedtax = round($awardedtax,2);
 		$awardedtotalwithtax = $awardedtotal + $awardedtax;
 		$awardedtotalwithtax = round($awardedtotalwithtax,2);
		$highTotal =array_sum($maximum);
		$totalsaved =0;
		if($highTotal > $awardedtotal){
 			$totalsaved = $highTotal + (($highTotal)*$config['taxpercent']/100) - $awardedtotalwithtax;
		}

	}
?>
<style>

.findtext
{
	color: #999;
	font-family: Century Gothic;
	font-size: 12px;
	padding-right: 10px;
}

.awarded-item, .awarded-table
{
	background: #eedddd;
}
.substitute-item
{
	background: #BFEFFF;
}
</style>

<script>
$(document).ready(function(){
	$('.selection-item').click(function(){
		refreshtotal();
	});
	var mintotal = refreshtotal();
	$(".mintotal").html(mintotal);
});
function refreshtotal()
{
	var total = 0;
	$('.selection-item:checked').each(function(obj) {
		var selectionid = $(this).attr('id');
		var eaid = selectionid.replace('selection','ea');
		
		var ea = Number($("#"+eaid).html());
		total += ea;
		//alert(total);
    });
    var taxtotal = total * tax / 100;
    var grandtotal = total + taxtotal;

	total1 = Math.round(total*100)/100;
	total=total1.toFixed(2);
	taxtotal = Math.round(taxtotal*100)/100;
	grandtotal = Math.round(grandtotal*100)/100;

    $("#selectedsubtotal").html(total);
    $("#selectedtax").html(taxtotal);
    $("#selectedtotal").html(grandtotal);
    return grandtotal;
}
function awardbidbyid(bidid)
{
	$("#itemids").val('');
	$("#awardbid").val(bidid);
	$("#awardmodal").modal();
}

function awardbiditems()
{
	var total = parseInt($('#selectedtotal').html());
	if(total==0)
	{
		alert('Total amount cannot be 0');
		return false;
	}
	var ids = [];
	$('.selection-item:checked').each(function() {
		ids.push($(this).val());
    });
	$("#awardbid").val('');
    $("#itemids").val(ids.join(','));
	$("#awardmodal").modal();
}
function usedefaultaddresscheckchange()
{
	if($("#usedefaultaddress").attr('checked'))
		$("#shipto").val('<?php echo implode(' ',explode("\n",$project->address));?>');
}
function updateqty(id)
{
	var qty = Number($("#quantity"+id).val());
	var costcode = $("#costcode"+id).val();
	var ea = Number($("#ea"+id).html());
	var total = qty * ea;
	url = '<?php echo base_url()?>admin/quote/editbiditemqty/'+id+'/'+qty+'/'+total;
	//alert(url);return false;
	$.ajax({
      type:"GET",
      url: url,
    }).done(function(data){
		$("#itemtotal"+id).html(total);
		document.location.href='<?php echo base_url()?>admin/quote/bids/<?php echo $quote->id;?>';
        //alert('Quantity changed.');
    });
}
function updatecostcode(id)
{
	var costcode = $("#costcode"+id).val();
	url = '<?php echo base_url()?>admin/quote/editbiditemcostcode/'+id+'/'+costcode;
	//alert(url);return false;
	$.ajax({
      type:"GET",
      url: url,
    }).done(function(data){
        //alert(data);
    });
}
function saveminimum(itemid, companyid, price)
{
	url = '<?php echo base_url()?>admin/quote/saveminimum/'+itemid+'/'+companyid+'/'+price;
	//alert(url);return false;
	$.ajax({
      type:"GET",
      url: url,
    }).done(function(data){
        alert('Minimum Price Saved');
    });
}

function showOriginal(quote,itemcode)
{
    $("#itemmodal").modal();
	url = '<?php echo base_url()?>admin/quote/getquoteitem';
	//alert(url);return false;
	$.ajax({
      type:"post",
      data:"quote="+quote+"&itemcode="+itemcode,
      url: url,
    }).done(function(data){
    	$("#itemdetails").html(data);
    });
}
function viewitems(itemid)
{
	var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;
	//alert(quoteid);
    $("#quoteitems").html('loading ...');
    $("#itemsmodal").modal();
        $.ajax({
	      type:"post",
	      url: serviceurl,
          }).done(function(data){
	        $("#quoteitems").html(data);
	        //$("#itemsmodal").modal();
	    });
}
</script>

<script type="text/javascript">
$(function() {

    //autocomplete
    $(".costcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/findcostcode",
        minLength: 1
    });

});
</script>


<section class="row-fluid">
	<h3 class="box-header"><?php echo @$heading; ?> <?php if(!$isawarded){?> &nbsp;&nbsp;<a class="btn btn-green" style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/update/'.$quote->id;?>">Edit Quote</a> <?php } ?>
			&nbsp;&nbsp;<a class="btn btn-green" style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url('admin/message/messages/'.$quote->id);?>">View Messages</a></h3>
	<div class="box">
		<div class="span12">
		   <a class="btn btn-green" href="<?php echo site_url('admin/quote/index/'.$quote->pid);?>">&lt;&lt; Back</a>
		   &nbsp;&nbsp;<a href="<?php echo site_url('admin/quote/bids_export').'/'.$quote->id; ?>" class="btn btn-green">Export</a>&nbsp;&nbsp;<a href="<?php echo site_url('admin/quote/bids_pdf').'/'.$quote->id; ?>" class="btn btn-green">View PDF</a><br />
		   <br/>
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>

		   <span class="poheading"><?php echo $quote->potype=='Direct'?'Direct':'Via Quote';?></span>
		   <?php if($isawarded){?>
		   <h4>
		    Awarded on <?php $olddate=strtotime($awarded->awardedon); $newdate = date('m/d/Y', $olddate); echo $newdate; ?>
		   <a href="<?php echo site_url('admin/quote/contracttrack/'.$quote->id);?>">Track</a>
		   </h4>
		   <div class="span12">
		   <table class="table table-bordered span4 awarded-table">
			   <tr><td class="span4">Subtotal:</td><td class="span8">$<?php echo number_format($awardedtotal,2);?></td>
               <td class="span4">Total Saved:</td><td class="span8">$<?php echo number_format($totalsaved,2);?></td>
			   <tr><td>Tax:</td><td>$<?php echo number_format($awardedtax,2);?></td>
			   <tr><td>Total:</td><td>$<?php echo number_format($awardedtotalwithtax,2);?></td>
		   </table>

		   </div>

		   <?php }else{?>
		   <div class="span12">
		   <table class="table table-bordered span4">
			   <tr><th colspan="2"><strong>Select items below</strong></th></tr>
			   <tr><td class="span4">Subtotal:</td><td class="span8">$<span id="selectedsubtotal"></span></td>
			   <tr><td>Tax:</td><td>$<span id="selectedtax"></span></td>
			   <tr><td>Total:</td><td>$<span id="selectedtotal"></span></td>
			   <tr><td colspan="2"><input type="button" class="btn btn-primary" onclick="awardbiditems();" value="Award"/></td>
		   </table>
		   <div id="container-highchart" class="span4" style="min-width: 200px ;height: 200px; margin: 0 auto; width:60%"></div>
		   <script type="text/javascript">
		   Array.prototype.max = function() {
			   var max = this[0];
			   var len = this.length;
			   for (var i = 1; i < len; i++) if (this[i] > max) max = this[i];
			   return max;
			   }
			   Array.prototype.min = function() {
			   var min = this[0];
			   var len = this.length;
			   for (var i = 1; i < len; i++) if (this[i] < min) min = this[i];
			   return min;
			   }
           $(function () {
               var cat = new Array;
               var val = new Array;
               $(".company-name").each(function(index){ cat.push($( this ).text() );});
               $(".total-value").each(function(index){
            	   var valuetxt = $( this ).text();
                   val.push(valuetxt);
                   });
				var ser = new Array();
               for(var index=0;index<cat.length;index++){
               	   val[index] = val[index].replace(",","");
            	   myfloat = parseFloat(val[index]);
				ser[index] = {"name":cat[index],"data":[myfloat]};
                   }

                   if(cat.length>1) {
                   	ser.push({"name":"Split P.O.","data": [parseFloat($("#selectedtotal").text())]});

                   	var save = val.max() - val.min();
                   	save = save.toFixed(2);
                   	//var savepo = val.max() - parseFloat($("#selectedtotal").text());
                   	<?php $highTotal =array_sum($maximum);?>
                   	var savepo = <?php echo $highTotal;?> + (tax*<?php echo $highTotal;?>/100) - parseFloat($("#selectedtotal").text());
                   	savepo = savepo.toFixed(2);
                   	var textsubtitle = '*HI VS LOW SAVINGS '+save+'$'+'<br />*Split P.O. Savings '+savepo+'$';
                   }else
                   var textsubtitle = "";

               $('#container-highchart').highcharts({
                   chart: {
                       type: 'column',

                   },
                   title: {
                       text: 'Comparison'
                   },
                   subtitle: {
                	   text: textsubtitle,
                       useHTML:true,
                       align: 'right',
                       x: -50
                   },
                   xAxis: {
                       categories: ["Companies"],
                       title: {
                           text: null
                       }
                   },
                   yAxis: {
                       min: 0,
                       title: {
                           text: 'Price(cost)',
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
                       series: {
                           borderWidth: 0,
                           dataLabels: {
                               enabled: true,
                               useHTML: true,
                               format: '$ {point.y:.f}',
                               style: {
        							fontFamily: 'monospace',
       								color: "#f00"
    							}
                           }
                       }
                   },
                   legend: {
                       layout: 'vertical',
                       align: 'right',
                       verticalAlign: 'top',
                       x: 0,
                       y: 100,
                       floating: false,
                       borderWidth: 1,
                       backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor || '#FFFFFF'),
                       shadow: true
                   },
                   credits: {
                       enabled: false
                   },
                   series: ser
               });
           });
           </script>
		   </div>
		   <?php }?>
		  <?php
		  	foreach($bids as $bid)
		  	if($bid->items)
		  	{
		  		$sn = 1;
		  ?>
		      <div class="control-group">
			    <div class="controls">
                <h3 class="box-header"><strong>PO #:<?php echo $quote->ponum; ?>
			      &nbsp; &nbsp;
			      Company:   <span class="company-name"><?php echo $bid->companyname;?></span> &nbsp; &nbsp;
			      Submitted:  <?php echo date('m/d/Y', strtotime($bid->submitdate));?>&nbsp;
			      <?php if($bid->quotefile){?>
			      	<a href="<?php echo site_url('uploads/quotefile/'.$bid->quotefile);?>" target="_blank">View Attachment</a>
			      <?php }?>
			      </strong>

				  <?php if($bid->draft=='Yes'){ ?>
				    <span class="label label-pink">Draft</span>
				  <?php }?>
				   <strong>
				  &nbsp; &nbsp; &nbsp;<?php if(isset($bid->revisionno) && $bid->revisionno>1) echo "Revision ".$bid->revisionno; else echo "Original bid"; ?>
				   </strong>
			      <?php
			      	if($maxcountitems > count($bid->items))
			      	{
			      ?></h3>

			      	  <div style="color:red">*This company did not bid some items: <span class="btn btn-mini btn-red" onclick="$('#notbid<?php echo $bid->id;?>').modal();">Show</span></div>

					  <div id="notbid<?php echo $bid->id;?>" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
					  	<div class="modal-header">
					  		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
					  		<h4>Items not bid by <?php echo $bid->companyname;?></h4>
					  	</div>
					  	<div class="modal-body">
					      <table class="table table-bordered">
					      	<tr>
					    		<th>File Name</th>
					    		<th>Description</th>
					    		<!--<th>Qty.</th>-->
				    		</tr>
				    		<?php
				    			foreach($quoteitems as $quoteitem)
				    			{
				    				$notbid = true;
				    				foreach($bid->items as $biditem)
				    					if($quoteitem->id == $biditem->itemid)
				    						$notbid = false;

				    			if($notbid)
				    			{
				    		?>
						      	<tr>
						    		<td><?php echo $quoteitem->attach?></td>
						    		<td><?php echo $quoteitem->itemname?></td>
						    		<!--<td><?php //echo $quoteitem->quantity?></td>-->
					    		</tr>
				    		<?php
				    			}
				    			}
				    		?>
					      </table>
					      </div>
			   		  </div>
				  </div>
			      <?php
			      	}
			      	else
			      	{
			      		echo '<br/><br/>';
			      	}
			      ?>
		      </div>

			  <div class="control-group">
				    <table class="table table-bordered">
				    	<tr>
				    		<th>#</th>
				    		<?php if(!$isawarded){?>
				    		<th>Select</th>
				    		<?php }?>
				    		<th>Files</th>	
				    		<th>Item Description</th>				    		
				    		<th>Price EA</th>
				    		<!--<th>Price Requested</th>-->
				    		<th>Total Price</th>
				    		<!-- <th>Date Available</th> -->
				    		<th>Cost Code</th>
				    		<!--<th>Notes</th>
				    		<th>Compare</th>-->
				    		<th>Del</th>
				    	</tr>
<?php $alltotal=0; foreach($bid->items as $q) { // if($q->itemcode){?>
				    	<?php $alltotal += $q->totalprice;?>
		    			<?php
							$key = $q->itemid;
		    				$diff = $q->ea - $minimum[$key];
		    				$diff = number_format($diff,2);
		    			?>
				    	<tr class="<?php if(in_array($q->itemcode.$bid->company, $awardeditemcompany)){echo 'awarded-item';} elseif($q->substitute){echo 'substitute-item';}?>">
				    		<td><?php echo $sn++;?></td>
				    		<?php if(!$isawarded){?>
				    		<td>
				    			<input type="radio" id="selection<?php echo $q->id;?>" class="selection-item" value="<?php echo $q->id;?>" name="<?php echo $key;?>" <?php if($diff==0 && !isset($checkedarray[$key])){ echo 'checked';$checkedarray[$key]='1'; }?>/>
				    		</td>
				    		<?php }?>
				    		<td>
				    			<!--<?php if(@$q->attach && file_exists("./uploads/quote/".$q->attach)){?>
				                        	<br>
				                        	<a href="<?php echo site_url('uploads/quote').'/'.@$q->attach ;?>" target="_blank">  &nbsp;
				                        	View File
				                          	</a>
				                <?php }?>-->
				    			<?php if(@$q->attach){ $files=""; $files=explode(',',@$q->attach); foreach ($files as $file) {?>
				    			<?php if($file && file_exists("./uploads/quote/".$file)){?>
	                        	<br>
	                        	<a href="<?php echo site_url('uploads/quote').'/'.$file ;?>" target="_blank">  &nbsp;
	                        	View File
	                          	</a>
	                          	<?php } } }?>    
				    		</td>
				    		<td><?php echo $q->itemname;?></td>				    		
				    		<!-- <td><?php echo $q->minprice;?></td> -->
				    		<td>
				    			<?php if($diff=='0'){echo '<span class="label label-success">';}?>
				    			$ <span id="ea<?php echo $q->id;?>"><?php echo $q->ea;?></span>
				    			<?php if($diff=='0'){echo '</span>';}?>

				    			<?php if($q->minprice >= $q->ea){?>
				    			<br/>*New Low Price
				    			<?php }?>
				    		</td>
				    		<!-- <td><?php
									echo $q->reqprice;
									if($q->reqprice > 0)
									{
    								 	if($q->ea > $q->reqprice){
    										echo '<span class="label label-red"><strong>!</strong></span>';
    									} else {
                                        	echo '<span class="label label-success"><i class="icon-ok"></i></span>';

    									}
									}
									else
									{
									    echo ' (RFQ)';
									}
							?></td> -->
				    		<td>$<span id="itemtotal<?php echo $q->id;?>"><?php echo number_format($q->totalprice,2);?></span></td> 				    		<!-- <td>
				    			<?php echo $q->daterequested;?>
				    			<?php if(@$q->originaldate) if(@$q->originaldate != $q->daterequested){ echo '<br/><span style="color:red">Req:'.$q->originaldate.'</span>';}?>
				    		</td> -->
				    		<td>
				    			<?php if($isawarded){ echo $q->costcode;} else {?>
				    			<select id="costcode<?php echo $q->id;?>" name="costcode<?php echo $q->id;?>" class="input-medium costcode" onblur="updatecostcode('<?php echo $q->id;?>')">
				    				<?php foreach($costcodes as $costcode){?>
			    						<option value="<?php echo $costcode->code;?>"
			    						<?php if($q->costcode==$costcode->code){echo 'SELECTED';}?>>
			    						<?php echo $costcode->code;?>
			    						</option>
		    						<?php }?>
		    					</select>
				    			<?php }?>
				    		</td>
				    		<!-- <td><?php echo $q->notes;?>&nbsp;</td>
				    		<td <?php if($diff==0){ echo 'class="minimum"';}?>><?php echo ($diff==0?'<span class="label label-success">'.$diff==0?'Lowest Unit Price':$diff.'</span>':($diff<0?'- $':'+ $'.$diff));?></td>-->
				    		<td>
					    		<a href="<?php echo site_url('admin/quote/delbiditem/'.$q->id.'/'.$quote->id);?>">
					    		<span class="icon icon-trash"></span>
					    		</a>
				    		</td> 
				    	</tr>
				    	<?php }?>
				    	<?php
				    		$alltotal = round($alltotal,2);
							$taxtotal = $alltotal * $config['taxpercent'] / 100;
				    		$taxtotal = round($taxtotal,2);
							$grandtotal = $alltotal + $taxtotal;
				    		$grandtotal = round($grandtotal,2);
							$diff = $alltotal - $minimum['totalprice'];

				    	?>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?3:4;?>" style="text-align:right">Subtotal: </td>
				    		<td colspan="4"><?php if($diff=='0'){echo '<span class="label label-success">';}?>$ <?php echo number_format($alltotal,2);?><?php if($diff=='0'){echo '</span>';}?></td>
				    		<!--<td <?php if($diff==0){ echo 'class="minimum"';}?>><?php echo ($diff==0?'<span class="label label-success">'.$diff==0?'Lowest Subtotal':$diff.'</span>':($diff<0?'- $':'+ $'.$diff));?></td>
				    		<td>&nbsp;</td>-->
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?3:4;?>" style="text-align:right">Tax: </td>
				    		<td colspan="4">$ <?php echo number_format($taxtotal,2);?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?3:4;?>" style="text-align:right">Total: </td>
				    		<td colspan="4">$ <span class="total-value"><?php echo number_format($grandtotal,2);?></span></td>
				    	</tr>
				    	<?php if(!$isawarded){?>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?3:4;?>" style="text-align:right">Lowest Price Possible: </td>
				    		<td colspan="4">$ <span class="mintotal"></span></td>
				    	</tr>
				    	<?php }?>
				    </table>
				    <?php if(!$isawarded){?>
				    <div align="right">
					    <form method="post" action="<?php echo site_url('admin/quote/awardbid')?>">
					    <input type="button" value="Accept <?php echo $bid->companyname;?>" onclick="awardbidbyid('<?php echo $bid->id;?>')" class="btn btn-primary"/>
					    </form>
				    </div>
				    <?php }?>

				    <?php
				    	if($bid->messages)
				    	{
				    ?>
				  <h3 class="box-header" style="display:inline;"">  Messages:</h3>
				    <table class="table table-bordered" >
					    <tr>
					    	<th>From</th>
					    	<th>To</th>
					    	<th>Message</th>
					    	<th>Date/Time</th>
                                                <th>&nbsp;</th>
					    </tr>
					    <?php
					    	foreach($bid->messages as $msg)
					    	{
					    ?>
					    <tr>
					    	<td><?php echo $msg->from;?></td>
					    	<td><?php echo $msg->to;?></td>
					    	<td><?php echo $msg->message;?></td>
					    	<td><?php echo date('m/d/Y',strtotime($msg->senton));?></td>
                            <td>
                            <?php if($msg->user_attachment!=''){?>
                            <a href="<?php echo site_url('uploads/messages').'/'.$msg->user_attachment;?>" target="_blank" title="View Attachment"><?php echo 'View Attachment';?></a>
                                 <?php }?>

                        </td>
					    </tr>
					    <?php
					    	}
					    ?>
				    </table>
				    <?php
				    	}
				    ?>


				    <div class="well">
					    <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url('admin/message/sendcontractmessage/'.$bid->quote)?>">
					    	<input type="hidden" name="quote" value="<?php echo $bid->quote;?>"/>
					    	<input type="hidden" name="company" value="<?php echo $bid->company;?>"/>
					    	<input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname')?> (Admin)"/>
					    	<input type="hidden" name="to" value="<?php echo $bid->companyname;?>"/>
					    	<input type="hidden" name="ponum" value="<?php echo $quote->ponum;?>"/>


					    	<div class="control-group">
                            <label class="control-label" for="company">Send Message To:</label>
                            <div class="controls">
                            <?php echo $bid->companyname;?>
                            </div>
                            </div>

                            <div class="control-group">
                            <label class="control-label" for="message">Message</label>
                            <div class="controls">
                           		<textarea name="message" class="span8" rows="5" required></textarea>
                            </div>
                            </div>

                            <div class="control-group">
                            <label class="control-label" for="userfile">Attachment</label>
                            <div class="controls">
                           		 <input type="file" name="userfile" size="10"  />
                            </div>
                            </div>

                            <div class="control-group">
                            <label class="control-label" for="">&nbsp;</label>
                            <div class="controls">
                           		 <input type="submit" value="Send" class="btn btn-primary"/>
                            </div>
                            </div>


					    </form>
				    </div>

<!--
                                    <div align="right" id="div_xml_type6">
             <form id="olditemform" class="form-horizontal" enctype="multipart/form-data" method="post"
		    	action="<?php echo base_url(); ?>admin/quote/updateattach">
			  	<input type="hidden" name="quoteid" value="<?php echo $this->validation->id;?>"/>
                    <div class="control-group">

		    <div class="controls">
		   Add Attachment   <input type="file" name="userfile" size="20"  />
                      </div>
		    </div> <input type="submit" value="Upload" class="btn btn-primary"/>
                    <br><a href="<?php echo site_url('uploads/quote').'/'.@$this->validation->quoteattachment ;?>" target="_blank">  <?php echo @$this->validation->quoteattachment;?>
                      </a>
</form></div>-->
			    </div>

			    <br/>
			    <br/>
		    <?php }?>
	    </div>
	    <?php if(!$isawarded){?>
	    <div class="span12">
	    <span class="poheading">Lowest Price Possible: $ <span class="mintotal"></span></span>

	    </div>
	    <?php }?>
    </div>
</section>
        <div id="awardmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	<form id="editform" class="stylemoduleform" method="post" action="<?php echo site_url('admin/quote/awardcontractbid');?>">
			<input type="hidden" id="awardbid" name="bid">
            <input type="hidden" id="quoteid" name="quote" value="<?php echo $quote->id;?>">
            <input type="hidden" id="pid" name="pid" value="<?php echo $quote->pid;?>">
            <input type="hidden" id="itemids" name="itemids">
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Award Bid</h3>
        	</div>
        	<div class="modal-body">

        	<table>
	        	<tr>
		        	<td colspan="2"><strong>Shipping Address:</strong> <br/>
			        	<input type="checkbox" value="1" id="usedefaultaddress" name="usedefaultaddress" onchange="usedefaultaddresscheckchange()"/>
						<label for="usedefaultaddress">Use Default Project Address?</label>
			        	<textarea class="span6" rows="2" id="shipto" name="shipto"></textarea>
		        	</td>
	        	</tr>

        	</table>

        	</div>
        	<div class="modal-footer">
        		<input type="submit" class="btn btn-primary" value="Award"/>
        	</div>
            </form>
        </div>

        <div id="itemsmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        	</div>
        	<div class="modal-body" id="quoteitems">
        	</div>

        </div>