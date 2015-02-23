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
			$awardedtotal+=$ai->quantity * $ai->ea;
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
	var cctotal = 0;
	$('.selection-item:checked').each(function(obj) {
		var selectionid = $(this).attr('id');
		var quantityid = selectionid.replace('selection','quantity');
		var eaid = selectionid.replace('selection','ea');
		var creditonlyval = selectionid.replace('selection','creditonly');
		
		var quantity = Number($("#"+quantityid).val());
		var ea = Number($("#"+eaid).html());
		var creditonly = $("#"+creditonlyval).val();
		
		if(creditonly==1)
		cctotal += quantity*ea;
		
		total += quantity*ea;
		//alert(total);
    });
    var taxtotal = total * tax / 100;
    var grandtotal = total + taxtotal;

	total1 = Math.round(total*100)/100;
	total=total1.toFixed(2);
	taxtotal = Math.round(taxtotal*100)/100;
	grandtotal = Math.round(grandtotal*100)/100;

	
	var cctaxtotal = cctotal * tax / 100;
    var ccgrandtotal = cctotal + cctaxtotal;	
	ccgrandtotal = Math.round(ccgrandtotal*100)/100;
	
    $("#selectedsubtotal").html(total);
    $("#selectedtax").html(taxtotal);
    $("#selectedtotal").html(grandtotal);
    $("#selectedcctotal").val(ccgrandtotal);
    return grandtotal;
}
function awardbidbyid(bidid,grandtotal,creditonly)
{
	$("#itemids").val('');
	$("#awardbid").val(bidid);
	$('#paytype').val('awardbidbyid');
	
	$('#paybtn').css('display','none');
	$('#awardbtn').css('display','none');
	
	$('#creditcardnote').css('display','none');
	
	if(creditonly==1){
		$('#paybtn').css('display','block');
		$('#awardbtn').css('display','none');
		$('#creditcardnotebycompany').html('This Supplier has set your account to credit card only. Awarding any item to this supplier will require upfront credit card payment to that items value');
	}else{
		$('#paybtn').css('display','none');
		$('#creditcardnotebycompany').html('');
		$('#awardbtn').css('display','block');
	}
	$("#grandtotal").val(grandtotal);
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
    $('#paybtn').css('display','none');
	$('#awardbtn').css('display','none');	
	$('#creditcardnotebycompany').html('');
	
    $('#paytype').val('awardbiditems');
    var tot = $('#selectedcctotal').val();
    if(tot>0){
		$('#paybtn').css('display','block');
		$('#awardbtn').css('display','none');
		$('#creditcardnote').css('display','block');
	}else{
		$('#paybtn').css('display','none');
		$('#awardbtn').css('display','block');
		$('#creditcardnote').css('display','none');
	}
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
function viewitems(itemid, bidid)
{
	var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid+'/'+bidid;
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

function validatecc()
	{
	  cn = $("#card").val();
	  ct =  $("#creditcardtypes").val();
	  //alert(cn+'-'+ct);return false;
	  if (!checkCreditCard (cn,ct)) {
		alert (ccErrors[ccErrorNo]);
		return false;
	  }
	  cvc = $("#cvc").val();
	  if(cvc.length != 3 || isNaN(cvc))
	  {
		  alert('Wrong cvc code');
		  return false;
	  }
	  return true;
	}


function paycc(bankaccounarr, bankcnt)
{			
	   if(bankcnt>0){
	   		alert(" You can't proceed. These Supplier/s have not set their bank account details: "+bankaccounarr);	
	   }else{
		
	    if($('#paytype').val() == "awardbiditems"){
	    	amount = $('#selectedcctotal').val();
	    }
	    
	    if($('#paytype').val() == "awardbidbyid"){
	    	amount = $('#grandtotal').val();
	    }
	    
		var invoicenumber = $('#quoteidcopy').val();
		$("#ccpayinvoicenumber").val(invoicenumber);
		$("#ccpayinvoiceamount").val(amount);
		$("#ccpayamountshow").html(amount);
		$('#shiptocopy').val($('#shipto').val());
		$('#awardbidcopy').val($('#awardbid').val());
		$('#itemidscopy').val($('#itemids').val());		
		$("#awardmodal").hide();	
		$("#paymodal").modal();	
	   }
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
	<h3 class="box-header"><?php echo @$heading; ?> <?php if(!$isawarded){ if(empty($invitations)){ ?> &nbsp;&nbsp;<a class="btn btn-green" style="font-size:12px;font-weight:normal;" target="_blank" href="<?php echo site_url().'admin/quote/update/'.$quote->id;?>">Edit Quote</a> <?php } } ?>
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
		   <a href="<?php echo site_url('admin/quote/track/'.$quote->id);?>">Track</a>
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
			   <tr><td>Total:</td><td>$<span id="selectedtotal"></span>
			   <input type="hidden" id="selectedcctotal">
			   </td>
			   <tr><td colspan="2"><input type="button" class="btn btn-primary" onclick="awardbiditems();" value="Award"/></td>
		   </table>
		   
		   <div id="container-highchart" class="span4" style="min-width: 200px ;height: 300px; margin-top: -70px; margin-left:auto; margin-right:auto; margin-bottom:0px;width:60%"></div>
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
                   	var textsubtitle1 = '<table class="table table-bordered"><tr><td>HI VS LOW SAVINGS = ('+Math.abs(save)+'$) </td></tr><tr><td>Split P.O. Savings = ('+Math.abs(savepo)+'$) </td></tr>';
                   	var textsubtitle2 = "";
                   	<?php foreach($bids as $bid) if($bid->items){                   		
                   		if($maxcountitems > count($bid->items))
			      		{?>
			      			textsubtitle2 = '<tr><td>*Low did not bid all items</td></tr>';
			      		<?php } } ?>
                   	var textsubtitle = textsubtitle1 + textsubtitle2 +'</table>';	
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
                       x: 5
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
                       y: 200,
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
				     <?php if($bid->creditonly=='1') {?>
						 &nbsp;&nbsp;&nbsp;<span style="color:red;font-weight:bold;">*Credit Card Only Account.</span>
				  <?php } ?>
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
					    		<th>Item Code</th>
					    		<th>Item Name</th>
					    		<th>Qty.</th>
				    		</tr>
				    		<?php
				    			foreach($quoteitems as $quoteitem)
				    			{
				    				$notbid = true;
				    				foreach($bid->items as $biditem)
				    					if($quoteitem->itemcode == $biditem->itemcode)
				    						$notbid = false;

				    			if($notbid)
				    			{
				    		?>
						      	<tr>
						    		<td><?php echo $quoteitem->itemcode?></td>
						    		<td><?php echo $quoteitem->itemname?></td>
						    		<td><?php echo $quoteitem->quantity?></td>
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
				    		<th>Item Code</th>
				    		<th>Item Name</th>
				    		<th>Qty.</th>
				    		<th>Unit</th>
				    		<th>60 day Low. Price</th>
				    		<th>Price EA</th>
				    		<th>Price Requested</th>
				    		<th>Total Price</th>
				    		<th>Date Available</th>
				    		<th>Cost Code</th>
				    		<th>Notes</th>
				    		<th>Compare</th>
				    		<th>Del</th>
				    	</tr>
				    	<?php $alltotal=0; foreach($bid->items as $q) if($q->itemcode){?>
				    	<?php $alltotal += $q->quantity * $q->ea;?>
		    			<?php
							$key = $q->itemcode;
		    				$diff = $q->ea - $minimum[$key];
		    				$diff = number_format($diff,2);
		    			?>
				    	<tr class="<?php if(in_array($q->itemcode.$bid->company, $awardeditemcompany)){echo 'awarded-item';} elseif($q->substitute){echo 'substitute-item';}?>">
				    		<td><?php echo $sn++;?>
				    		<input type="hidden" id="creditonly<?php echo $q->id;?>" value="<?php echo $bid->creditonly;?>" >
				    		</td>
				    		<?php if(!$isawarded){?>
				    		<td>
				    			<input type="radio" id="selection<?php echo $q->id;?>" class="selection-item" value="<?php echo $q->id;?>" name="<?php echo $key;?>" <?php if($diff==0 && !isset($checkedarray[$key])){ echo 'checked';$checkedarray[$key]='1'; }?>/>
				    		</td>
				    		<?php }?>
				    		<td>
				    			<?php echo $q->substitute?$q->itemcode: "<a href='javascript:void(0)' onclick=\"viewitems('$q->itemid','$q->bid')\">$q->itemcode</a>"; ?>
				    			<?php if($q->substitute){?><small><span class="label label-red">Substitute</span></small><?php }?>
				    		</td>
				    		<td><?php echo $q->itemname;?></td>
				    		<td><input class="span12" type="text" id="quantity<?php echo $q->id;?>" value="<?php echo $q->quantity;?>" onblur="updateqty('<?php echo $q->id;?>')" <?php if($isawarded){echo 'readonly';}?>></td>
				    		<td><?php echo $q->unit;?></td>
				    		<td><?php echo $q->minprice;?></td>
				    		<td>
				    			<?php if($diff=='0'){echo '<span class="label label-success">';}?>
				    			$ <span id="ea<?php echo $q->id;?>"><?php echo $q->ea;?></span>
				    			<?php if($diff=='0'){echo '</span>';}?>

				    			<?php if($q->minprice >= $q->ea){?>
				    			<br/>*New Low Price
				    			<?php }?>
				    		</td>
				    		<td><?php
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
							?></td>
				    		<td>$<span id="itemtotal<?php echo $q->id;?>"><?php echo number_format($q->quantity * $q->ea,2);?></span></td>
				    		<td>
				    			<?php echo $q->daterequested;?>
				    			<?php if(@$q->originaldate) if(@$q->originaldate != $q->daterequested){ echo '<br/><span style="color:red">Req:'.$q->originaldate.'</span>';}?>
				    		</td>
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
				    		<td><?php echo $q->notes;?>&nbsp;</td>
				    		<td <?php if($diff==0){ echo 'class="minimum"';}?>><?php echo ($diff==0?'<span class="label label-success">'.$diff==0?'Lowest Unit Price':$diff.'</span>':($diff<0?'- $':'+ $'.$diff));?></td>
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
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Subtotal: </td>
				    		<td colspan="5"><?php if($diff=='0'){echo '<span class="label label-success">';}?>$ <?php echo number_format($alltotal,2);?><?php if($diff=='0'){echo '</span>';}?></td>
				    		<td <?php if($diff==0){ echo 'class="minimum"';}?>><?php echo ($diff==0?'<span class="label label-success">'.$diff==0?'Lowest Subtotal':$diff.'</span>':($diff<0?'- $':'+ $'.$diff));?></td>
				    		<td>&nbsp;</td>
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Tax: </td>
				    		<td colspan="7">$ <?php echo number_format($taxtotal,2);?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Total: </td>
				    		<td colspan="7">$ <span class="total-value"><?php echo number_format($grandtotal,2);?></span></td>
				    	</tr>
				    	<?php if(!$isawarded){?>
				    	<tr>
				    		<td colspan="<?php echo $isawarded?7:8;?>" style="text-align:right">Lowest Price Possible: </td>
				    		<td colspan="7">$ <span class="mintotal"></span></td>
				    	</tr>
				    	<?php }?>
				    </table>
				    <?php if(!$isawarded){?>
				    <div align="right">
					    <form method="post" action="<?php echo site_url('admin/quote/awardbid')?>">
					    <input type="button" value="Accept <?php echo $bid->companyname;?>" onclick="awardbidbyid('<?php echo $bid->id;?>','<?php echo $grandtotal;?>','<?php echo $bid->creditonly;?>')" class="btn btn-primary"/>
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
					    <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?php echo site_url('admin/message/sendmessage/'.$bid->quote)?>">
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
	    <input type="hidden" id="paytype"/>
	    <input type="hidden" id="grandtotal"/>
    </div>
</section>
        <div id="awardmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	<form id="editform" class="stylemoduleform" method="post" action="<?php echo site_url('admin/quote/awardbid');?>">
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
        	<span id="creditcardnotebycompany"></span>
        	<span id="creditcardnote"><?php if(count($creditaccarray)>0){ echo "Supplier/s (".implode(",",$creditaccarray).") has set your account to credit card only. Awarding any item to this supplier/s will require upfront credit card payment to that items value.<br/>"; }?></span>
        	<input type="button" id="paybtn"  style="display:none;" class="btn btn-primary" value="Award&Pay" onclick="paycc('<?php if(count($bankaccarray)>0){ echo implode(",",$bankaccarray); }else echo ""; ?>','<?php echo count($bankaccarray);?>')"; />&nbsp;
        		<input id="awardbtn" style="display:none;" type="submit" class="btn btn-primary" value="Award"/>&nbsp;
        	&nbsp;<input type="button" data-dismiss="modal" class="close btn btn-primary" value="Cancel">&nbsp;	
        		
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
        
        
        
        
   <div id="paymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
    	<h3>
    	Pay by credit card
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
		</h3>
	</div>
	<div class="modal-body" id="quoteitems">
        <form method="post" action="<?php echo site_url('admin/quote/payquotebidbycc/');?>" onsubmit="return validatecc();">
	        <input type="hidden" id="ccpayinvoicenumber" name="invoicenum"/>
	        <input type="hidden" id="ccpayinvoiceamount" name="amount"/>
            <div class="control-group">
                <label class="control-label" for="card">
                   Total Amount to pay
                </label>
                <div class="controls">
                   $<span id="ccpayamountshow"></span>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Number
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="card" name="card" required style="width: 250px;">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="card">
                   Credit Card Type
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
		            <select id="creditcardtypes" name="creditcardtypes">
			            <option value="visa">Visa</option>
			            <option value="mastercard">Master Card</option>
			            <option value="amex">American Express</option>
			            <option value="dinersclub">Diners club</option>
			            <option value="discover">Discover</option>
		            </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputEmail">
                   CVC Code:
                    <span class="form-required" title="This field is required.">*</span>
                </label>
                <div class="controls">
                    <input type="text" id="cvc" name="cvc" required>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputMessage">
                    Expiry Date
                </label>
                <div class="controls">
                    <select id="month" name="month" style="width: 95px;">
                    	<?php for($i=1; $i<13; $i++){?>
	                    <option value="<?php echo str_pad($i, 2, '0',STR_PAD_LEFT);?>"><?php echo str_pad($i, 2, '0',STR_PAD_LEFT);?></option>
	                    <?php }?>
                    </select>
                    <select id="year" name="year" style="width: 125px;">
                    	<?php for($i = date('Y'); $i < date('Y')+10; $i++){?>
	                    <option value="<?php echo $i;?>"><?php echo $i;?></option>
	                    <?php }?>
                    </select>
                </div>
            </div>
            <div class="form-actions">
            	<input type="hidden" id="shiptocopy" name="shiptocopy"></textarea>
            	<input type="hidden" id="awardbidcopy" name="bidcopy">
                <input type="hidden" id="quoteidcopy" name="quotecopy" value="<?php echo $quote->id;?>">
                <input type="hidden" id="pidcopy" name="pidcopy" value="<?php echo $quote->pid;?>">
                <input type="hidden" id="itemidscopy" name="itemidscopy">
                <input type="submit" class="btn btn-primary arrow-right" value="Process">
            </div>
        </form>
	</div>

</div>