<?php $tax = 0;?>
 <script type="text/javascript">
$.noConflict();
 </script>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('.datefield').datepicker();
});

function submitForm(val,invoicequote)
{
	$("#invoicenum").val(val);
	$("#invoicequote").val(invoicequote);
  	document.forms['invoiceform'].submit()
}
</script>

    <div class="content">
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">

			<h3>Report  <a href="<?php echo site_url('report/export'); ?>" class="btn btn-primary btn-xs btn-mini">Export</a>  <a href="<?php echo site_url('report/report_pdf'); ?>" class="btn btn-primary btn-xs btn-mini">View PDF</a></h3>
		</div>

	   <div id="container">


			<div style="float:left; width:100%; position:relative; z-index:111" >
			  <form class="form-inline" action="<?php echo site_url('report')?>" method="post" style=" margin:0px;">
<table class="table no-more-tables general" width="100%" border="0" cellspacing="0" cellpadding="0" style="text-align:left;margin:0px;"">
  <tr>
    <th >From</th>
    <th >To</th>
    <th >Company</th>
	 <?php if(@$_POST['purchasingadmin']){?>
    <th >Select Project</th>
	<?php } ?>
    <th>Payment Status</th>
    <th >Verification Status</th>
    <th >Select Time</th>
    <th ><input type="submit" value="Filter" class="btn btn-primary"/>
           <a href="<?php echo site_url('report');?>"><input type="button" value="Show All" class="btn btn-primary"/></a></th>
  </tr>
  <tr>
    <td ><input type="text" name="searchfrom" value="<?php echo date("m/d/Y", strtotime(@$_POST['searchfrom']));?>" class="datefield" style="width: 90px;"/></td>
    <td ><input type="text" name="searchto" value="<?php echo date("m/d/Y", strtotime(@$_POST['searchto']));?>" class="datefield" style="width: 90px;"/></td>
    <td ><select id="purchasingadmin" name="purchasingadmin" class="form-control selectpicker show-tick" style="width: 100px;" onchange="this.form.submit()">
    					<option value=''>All Companies</option>
    					<?php foreach($purchasingadmins as $company){?>
    						<option value="<?php echo $company->id?>"
    							<?php if(@$_POST['purchasingadmin']==$company->id){echo 'SELECTED';}?>
    							>
    							<?php echo $company->companyname?>
    						</option>
    					<?php }?>
    				</select></td>

  <?php if(@$_POST['purchasingadmin']){?>

    <td >
		<select name="searchproject" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
            <option value=''>All</option>
			<?php foreach($projects as $p){?>
			<option value='<?php echo $p->id;?>' <?php if(@$_POST['searchproject'] ==$p->id){echo 'SELECTED';}?>><?php echo $p->title;?></option>
			<?php }?>
        </select>
	</td>

	<?php } ?>

	<td style="padding:0px"><select id="searchpaymentstatus" name="searchpaymentstatus" class="form-control selectpicker show-tick" style="width:120px" onchange="this.form.submit()">
                            <option value=''>All</option>
                            <option value="Paid" <?php if (@$_POST['searchpaymentstatus'] == 'Paid') { echo 'SELECTED'; } ?>>Paid</option>
                           <option value="Requested Payment" <?php if (@$_POST['searchpaymentstatus'] == 'Requested Payment') { echo 'SELECTED'; } ?>>Requested Payment</option>
                            <option value="Unpaid" <?php if (@$_POST['searchpaymentstatus'] == 'Unpaid') { echo 'SELECTED'; } ?>>Unpaid</option>
                        </select></td>

    <td style="padding:0px"><select name="verificationstatus" id="verificationstatus" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<option value='Pending' <?php if(@$_POST['verificationstatus'] =='Pending'){echo 'SELECTED';}?>>Pending</option>
                            	<option value='Verified' <?php if(@$_POST['verificationstatus'] =='Verified'){echo 'SELECTED';}?>>Verified</option>
                            	<option value='Error' <?php if(@$_POST['verificationstatus'] =='Error'){echo 'SELECTED';}?>>Error</option>
	           				 </select>
	 </td>
	          
	  <td>
	   <input type="checkbox" id ='checkunpaid' name ='checkunpaid' value="1" />&nbsp;Include Any Un-Paid.&nbsp;	 
	  
	  <select name="datebymonth" id="datebymonth" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>Select Days</option>
                            	<option value='<?php echo date('m/d/Y', strtotime("now -30 days")); ?>' <?php if(isset($seldatebymonth) && $seldatebymonth == date('m/d/Y', strtotime("now -30 days"))) echo ' selected '; else echo '';?>   >Last 30 days</option>
                            	<option value='<?php echo date('m/d/Y', strtotime("now -60 days")); ?>' <?php if(isset($seldatebymonth) && $seldatebymonth == date('m/d/Y', strtotime("now -60 days"))) echo ' selected '; else echo '';?>>Last 60 days</option>
                            	<option value='<?php echo date('m/d/Y', strtotime("now -90 days")); ?>' <?php if(isset($seldatebymonth) && $seldatebymonth == date('m/d/Y', strtotime("now -90 days"))) echo ' selected '; else echo '';?>>Last 90 days</option>
                             	<option value='alltime'>All-Time</option>                         	
	           				 </select><br>          				 
	           				 </td>
    <td style="padding:0px">&nbsp;</td>
  </tr>
</table>
</form>

           </div>


           <?php if(@$reports){?>

		<div class="row">
            <div class="col-md-12">
                <div class="grid simple ">
                    <div class="grid-title no-border">
                        <h4>&nbsp;</h4>
                    </div>
                    <div class="grid-body no-border">
                    <table class="table no-more-tables general">
			    	<tr>
			    		<th width="20%">DATE</th>
			    		<th width="20%">TOTAL QUANTITY</th>
			    		<th width="20%">TOTAL AMOUNT</th>
			    		<th width="20%">TOTAL PAID</th>
			    		<th width="20%">TOTAL REMAINING</th>
			    	</tr>
			    	<tr>
			    		<td>From <?php if(@$_POST['datebymonth'] !="" && count($reports)>=2){  echo @$reports[0]->receiveddate;?> - To <?php echo @$reports[count($reports)-1]->receiveddate; } else { echo @$_POST['searchfrom']?> - To <?php echo @$_POST['searchto']; } ?></td>
			    		<td><span id="totalallquantity"></span></td>
			    		<td>$<span id="totalallprice"></span></td>
			    		<td>$<span id="totalallpaid"></span></td>
			    		<td>$<span id="totalallremaining"></span></td>
			    	</tr>
			    	</table>

			  		<!--<div>
			  		 	<br/><br/>
    			  		<strong>DATE:</strong> From <?php echo @$_POST['searchfrom']?> - To <?php echo @$_POST['searchto']?>
    			  		<br/>
    			  		<strong>TOTAL QUANTITY:</strong> <span id="totalallquantity"></span>
    			  		<br/>
    			  		<strong>TOTAL AMOUNT:</strong> $<span id="totalallprice"></span>
    			  		<br/>
    			  		<strong>TOTAL PAID:</strong> $<span id="totalallpaid"></span>
    			  		<br/>
    			  		<strong>TOTAL REMAINING:</strong> $<span id="totalallremaining"></span>
    			  	</div>-->
           <?php }?>
           <hr/>
		   <?php
		   		$totalallquantity = 0;
		   		$totalallprice = 0;
		   		$totalallpaid = 0;
		   		$i = 0;
		   		if(!@$reports)
		   		echo '<br><div class="alert alert-error"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">No records found</div></div>';
		   		else
		   			foreach($reports as $report)
		   			{
		   			    /*
			    		if(!$report->totalpaid) $report->totalpaid = 0;
		   				$report->totalpaid = $report->totalpaid + ($report->totalpaid*$tax/100);
		   				$report->totalpaid = round($report->totalpaid,2);

		   				$report->totalprice = $report->totalprice + ($report->totalprice*$tax/100);
		   				$report->totalprice = round($report->totalprice,2);

		   				$totalallquantity+=$report->totalquantity;
			    		$totalallpaid += $report->totalpaid;
			    		*/
			    		$i++;
		     ?>
		     <table class="table no-more-tables general">
			    	<tr>
			    		<th width="20%">DATE</th>
			    		<th width="20%">TOTAL QUANTITY</th>
			    		<th width="20%">TOTAL AMOUNT</th>
			    		<th width="20%">TOTAL PAID</th>
			    		<th width="20%">TOTAL REMAINING</th>
			    	</tr>

		             <tr>
			    		<td><?php if(@$report->receiveddate) echo date('m/d/Y', strtotime( $report->receiveddate));?></td>
			    		<td><span id="totalquantity<?php echo $i;?>"></span></td>
			    		<td>$<span id="totalprice<?php echo $i;?>"></span></td>
			    		<td>$<span id="totalpaid<?php echo $i;?>"></span></td>
			    		<td>$<span id="totalremaining<?php echo $i;?>"></span></td>
			    	</tr>
			    	</table>

    			<!--  <div>
    			  		<strong>DATE:</strong> <?php echo date('m/d/Y', strtotime( $report->receiveddate));?>
    			  		<br/>
    			  		<strong>TOTAL QUANTITY:</strong>
    			  		<span id="totalquantity<?php echo $i;?>"><?php //echo $report->totalquantity;?></span>
    			  		<br/>
    			  		<strong>TOTAL AMOUNT:</strong> $
    			  		<span id="totalprice<?php echo $i;?>"><?php //echo $report->totalprice;?></span>
    			  		<br/>
    			  		<strong>TOTAL PAID:</strong> $
    			  		<span id="totalpaid<?php echo $i;?>"><?php //echo $report->totalpaid;?></span>
    			  		<br/>
    			  		<strong>TOTAL REMAINING:</strong> $
    			  		<span id="totalremaining<?php echo $i;?>">
    			  		    <?php //echo $report->totalprice - $report->totalpaid;?>
    			  		</span>
    			  </div>-->

			    <table id="datatable" class="table no-more-tables general">
			    	<tr>
			    		<th>Company</th>
			    		<th>Project</th>
			    		<th>PO#</th>
			    		<th>Item Code</th>
			    		<th>Item Name</th>
			    		<th>Unit</th>
			    		<th>Qty.</th>
			    		<th>EA</th>
			    		<th>Total</th>
			    		<th>Payment</th>
			    		<th style="word-break:break-all;">Verification</th>
			    		<th>Notes</th>
			    		<th>Invoice#</th>
			    	</tr>
			    	<?php

        		   		$totalquantity = 0;
        		   		$totalprice = 0;
        		   		$totalpaid = 0;

			    		foreach($report->items as $item)
			    		{
			    			$amount = (($item->invoice_type != "fullpaid")?(($item->invoice_type == "alreadypay")?0:$item->quantity):$item->aiquantity) * $item->ea;
			    			$amount = round($amount + ($amount*$item->taxpercent/100),2);
			    			$totalallprice += $amount;

			    			$totalquantity += ($item->invoice_type != "fullpaid")?(($item->invoice_type == "alreadypay")?0:$item->quantity):$item->aiquantity;
			    			$totalprice += $amount;
			    			if($item->paymentstatus=='Paid')
			    			{
			    			    $totalpaid += $amount;
			    			    $totalallpaid += $amount;
			    			}
			    	?>
			    	<tr>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->title;?></td>
			    		<td style="word-break:break-all;"><a href="<?php echo site_url('quote/track/'.$item->quote.'/'.$item->award);?>"><?php echo $item->ponum;?></a></td>
			    		<td style="word-break:break-all;"><?php echo $item->itemcode;?></td>
			    		<td style="word-break:break-all;"><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo ($item->invoice_type == "fullpaid")?$item->aiquantity:$item->quantity;?>
			    		<?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid'; }?>	
			    		</td>
			    		<td><?php echo $item->ea;?></td>
			    		<td>$<?php echo round($amount,2);?></td>
			    		<td><?php echo $item->paymentstatus;?></td>
			    		<td><?php echo $item->status;?></td>
			    		<td>
			    		 <?php if(isset($item->item_img) && $item->item_img!= "" && file_exists("./uploads/item/".$item->item_img)) 
			    		{ ?>
                           <img style="max-height: 100px; padding: 5px;" height="80" width="120" src="<?php echo site_url('uploads/item/'.$item->item_img) ?>" alt="<?php echo $item->item_img;?>">
                        <?php } else { ?>
                            <img style="max-height: 100px; padding: 5px;" src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                        <?php } ?>
                        <br>
			    		<?php echo $item->notes;?>
			    		</td>
			    		<td>
			    		<form id="invoiceform" class="animated fadeIn" name="invoiceform" action="<?php echo site_url('quote/invoice');?>" method="post">
			    		<input type="hidden" name="invoicenum" id="invoicenum" value="<?php echo $item->invoicenum;?>">
			    		<input type="hidden" name="invoicequote" id="invoicequote" value="<?php echo $item->quote;?>">
			    		<a href="javascript: submitForm('<?php echo $item->invoicenum;?>','<?php echo $item->quote;?>');"><?php echo $item->invoicenum;?></a>
			    		</form>
			    		</td>
			    		<?php if(0){?>
			    		<td>
			    		<?php if($item->paymentstatus){?>
			    			<form class="animated fadeIn" action="<?php echo site_url('admin/report/payinvoice');?>" method="post">
			    				<input type="hidden" name="invoicenum" value="<?php echo $report->invoicenum?>">
			    				<input type="submit" value="Pay" class="btn btn-primary"/>
			    			</form>
			    		<?php }?>
			    		</td>
			    		<?php }?>
			    	</tr>
			    	<?php
			    		}
			    		$totalallquantity += $totalquantity;
		   			    $totalremaining = $totalprice - $totalpaid;

    		   			echo '<script>$("#totalquantity'.$i.'").html("'.$totalquantity.'");</script>';
    		   			echo '<script>$("#totalprice'.$i.'").html("'.$totalprice.'");</script>';
    		   			echo '<script>$("#totalpaid'.$i.'").html("'.$totalpaid.'");</script>';
    		   			echo '<script>$("#totalremaining'.$i.'").html("'.$totalremaining.'");</script>';
			    	?>
		      </table>
		      <br/>
		      <br/>
	    	<?php
		   			}
		   			$totalallremaining = $totalallprice - $totalallpaid;

		   			echo '<script>$("#totalallquantity").html("'.$totalallquantity.'");</script>';
		   			echo '<script>$("#totalallprice").html("'.$totalallprice.'");</script>';
		   			echo '<script>$("#totalallpaid").html("'.$totalallpaid.'");</script>';
		   			echo '<script>$("#totalallremaining").html("'.$totalallremaining.'");</script>';
	    	?>
	    	</div>
	    	</div>
	    	</div>
	    	</div>
       </div>
    </div>