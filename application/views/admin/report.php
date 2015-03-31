<?php
	//echo '<pre>';print_r($reports);die;
	$tax = $settings->taxpercent;
?>
<script type="text/javascript">
 $(document).ready(function() {
        $('.datefield').datepicker();
    });
</script>
<section class="row-fluid">
	<h3 class="box-header"><?php echo @$heading; ?> - <?php echo $this->session->userdata('managedprojectdetails')->title?> <a href="<?php echo site_url('admin/report/export'); ?>" class="btn btn-green">Export</a> &nbsp;&nbsp; <a href="<?php echo site_url('admin/report/reportpdf'); ?>" class="btn btn-green">View PDF</a></h3>
	<div class="box">
		<div class="span12">
		<br />
		   <br/>
		   <form class="form-inline" action="<?php echo site_url('admin/report')?>" method="post">
		 
		   <?php if(isset($_POST['searchfrom'])) {  $olddate=strtotime($_POST['searchfrom']); $fromdate = date('m/d/Y', $olddate); }
		   else { $fromdate=date('m/d/Y', strtotime("now -30 days") ); }
		   if(isset($_POST['searchto'])) { $olddate1=strtotime($_POST['searchto']); $todate = date('m/d/Y', $olddate1);}  
		   else { $todate=date('m/d/Y'); } ?>
		   
                    </tr>
                From: <input type="text" name="searchfrom" value="<?php echo $fromdate;?>" class="datefield"  style="width: 70px;"/>
                        &nbsp;&nbsp;
                        To: <input type="text" name="searchto" value="<?php echo $todate;?>" class="datefield" style="width: 70px;"/>
                &nbsp;&nbsp;
                Company:
				<select id="searchcompany" name="searchcompany">
					<option value=''>All Companies</option>
					<?php foreach($companylist1 as $company){?>
						<option value="<?php echo $company->id?>"
							<?php if(@$_POST['searchcompany']==$company->id){echo 'SELECTED';}?>
							>
							<?php echo $company->title?>
						</option>
					<?php }?>
				</select>
				<select id="searchcostcode" name="searchcostcode">
					<option value=''>All Costcode</option>
					<?php foreach($costcodelist as $costcode){?>
						<option value="<?php echo $costcode->code?>"
							<?php if(@$_POST['searchcostcode']==$costcode->code){echo 'SELECTED';}?>
							>
							<?php echo $costcode->code?>
						</option>
					<?php }?>
				</select>
                &nbsp;&nbsp;
                <input type="submit" value="Filter" class="btn btn-primary"/>
                <a href="<?php echo site_url('admin/report');?>">
                	<input type="button" value="Show All" class="btn btn-primary"/>
                </a>
           </form>
           <?php if(@$reports){?>

			 <!-- <div>
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

			  <table class="table table-bordered">
			    	<tr>
			    		<th width="20%">DATE</th>
			    		<th width="20%">TOTAL QUANTITY</th>
			    		<th width="20%">TOTAL AMOUNT</th>
			    		<th width="20%">TOTAL PAID</th>
			    		<th width="20%">TOTAL REMAINING</th>
			    	</tr>
			    	<tr>
			    		<td>From <?php echo @$_POST['searchfrom']?> - To <?php echo @$_POST['searchto']?></td>
			    		<td><span id="totalallquantity"></span></td>
			    		<td>$<span id="totalallprice"></td>
			    		<td>$<span id="totalallpaid"></span></td>
			    		<td>$<span id="totalallremaining"></span></td>
			    	</tr>
			    	</table>
           <?php }?>
           <hr/>
		   <?php
		   		$totalallquantity = 0;
		   		$totalallprice = 0;
		   		$totalallpaid = 0;
		   		if(!@$reports)
		   			echo '<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Records Found.</div></div>';
		   		else
		   			foreach($reports as $report)
		   			{
			    		if(!$report->totalpaid) $report->totalpaid = 0;
		   				$report->totalpaid = $report->totalpaid + ($report->totalpaid*$tax/100);		   				

		   				$report->totalprice = $report->totalprice + ($report->totalprice*$tax/100);
		   				
		   				if(@$report->discount_percent){

		   					$report->totalprice = $report->totalprice - ($report->totalprice*$report->discount_percent/100);
		   					$report->totalpaid = $report->totalpaid - ($report->totalpaid*$report->discount_percent/100);
		   				}

		   				if(@$report->penalty_percent){

		   					$report->totalprice = $report->totalprice + (($report->totalprice*$report->penalty_percent/100)*$report->penaltycount);
		   					$report->totalpaid = $report->totalpaid + (($report->totalpaid*$report->penalty_percent/100)*$report->penaltycount);
		   				}
		   				
		   				$report->totalpaid = round($report->totalpaid,2);
		   				
		   				$report->totalprice = round($report->totalprice,2);

		   				$totalallquantity+=$report->totalquantity;
			    		$totalallpaid += $report->totalpaid;
			    		$totalallprice += $report->totalprice;
		   ?>

			<!--  <div>
			  		<strong>DATE:</strong> <?php echo date('m/d/Y', strtotime($report->receiveddate));?>
			  		<br/>
			  		<strong>TOTAL QUANTITY:</strong> <?php echo $report->totalquantity;?>
			  		<br/>
			  		<strong>TOTAL AMOUNT:</strong> $<?php echo $report->totalprice;?>
			  		<br/>
			  		<strong>TOTAL PAID:</strong> $<?php echo $report->totalpaid;?>
			  		<br/>
			  		<strong>TOTAL REMAINING:</strong> $<?php echo $report->totalprice - $report->totalpaid;?>
			  </div>-->

			<table class="table table-bordered">
			    	<tr>
			    		<th width="20%">DATE</th>
			    		<th width="20%">TOTAL QUANTITY</th>
			    		<th width="20%">TOTAL AMOUNT</th>
			    		<th width="20%">TOTAL PAID</th>
			    		<th width="20%">TOTAL REMAINING</th>
			    	</tr>
			    	<tr>
			    		<td><?php echo date('m/d/Y', strtotime($report->receiveddate));?></td>
			    		<td><?php echo $report->totalquantity;?>
			    		<?php if (strpos(@$report->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid'; }?>	
			    		</td>
			    		<td>$<?php echo $report->totalprice;?>
			    		<?php if (strpos(@$report->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid'; }?>	</td>
			    		<td>$<?php echo $report->totalpaid;?>
			    		<?php if (strpos(@$report->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid'; }?>	</td>
			    		<td>$<?php echo $report->totalprice - $report->totalpaid;?></td>
			    	</tr>
			 </table>

			  <hr/>
			    <table class="table table-bordered">
			    	<tr>
			    		<th width="120">Company</th>
			    		<th width="75">PO#</th>
			    		<th width="120">Item Code</th>
			    		<th width="200">Item Name</th>
			    		<th width="50">Unit</th>
			    		<th width="50">Qty.</th>
			    		<th width="50">EA</th>
			    		<th width="50">Total</th>
			    		<th width="50">Payment</th>
			    		<th width="50">Verification</th>
			    		<th width="120">Cost Code</th>
			    		<th>Notes</th>
			    		<th>Invoice#</th>
			    		<th>Due Date</th>
			    	</tr>
			    	<?php
					
			    		foreach($report->items as $item)
			    		{	
			    			if($item->potype == "Contract" )
			    			$amount = $item->ea;
			    			else 
			    			$amount = (($item->invoice_type != "fullpaid")?(($item->invoice_type == "alreadypay")?0:$item->quantity):$item->aiquantity) * $item->ea;
			    			$amount = round($amount + ($amount*$tax/100),2);
			    			
			    			if(@$report->discount_percent){

			    				$amount = $amount - ($amount*$report->discount_percent/100);			    				
			    			}

			    			if(@$report->penalty_percent){

			    				$amount = $amount + (($amount*$report->penalty_percent/100)*$report->penaltycount);			    				
			    			}
			    			
			    			//$totalallprice += $amount;
			    	?>
			    	<tr>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->ponum;?>
			    		<?php echo ($item->invoice_type == "fullpaid" || $item->invoice_type == "alreadypay")?"*Pre-Paid":""; ?>
			    		</td>
			    		<td><?php if($item->IsMyItem == 0) { ?>  <a href="<?php echo site_url("site/item/".$item->itemurl);?>" target="_blank"> <?php echo $item->itemcode;?></a> <?php } else { echo $item->itemcode; }?>
			    		<br>
			    		 <?php if(isset($item->item_img) && $item->item_img!= "" && file_exists("./uploads/item/".$item->item_img)) 
			    		{ ?>
                           <img style="max-height: 120px;max-width: 100px; padding: 5px;" height="120" width="120" src="<?php echo site_url('uploads/item/'.$item->item_img) ?>" alt="<?php echo $item->item_img;?>">
                        <?php } else { ?>
                            <img style="max-height: 120px;max-width: 100px;  padding: 5px;" src="<?php echo site_url('uploads/item/big.png');?>" alt="">
                        <?php } ?>
			    		</td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo ($item->invoice_type == "fullpaid")?$item->aiquantity:$item->quantity;?>
			    		<?php if (strpos(@$item->invoicenum,'paid-in-full-already') !== false) {  echo '<br>*Pre-Paid'; }?>	
			    		</td>
			    		<td>$<?php echo round($item->ea,2);?></td>
			    		<td>$<?php echo round($amount,2);?></td>
			    		<td><?php echo $item->paymentstatus;?>
			    		<?php echo (@$item->invoice_type == "fullpaid" || @$item->invoice_type == "alreadypay")?"*Pre-Paid":""; ?>
			    		</td>
			    		<td><?php echo $item->status;?></td>
			    		<td><?php echo $item->costcode;?></td>
			    		<td><?php echo $item->notes;?></td>
			    		<td>
			    		<?php	if($item->potype=='Contract') { ?>
                      	<a href="<?php echo site_url('admin/quote/contract_invoice/'.$item->invoicenum.'/'.$item->quoteid)?>" target="_blank"><?php echo $item->invoicenum; ?></a>
					  <?php  } else { ?>
			    		<a target="_blank" href="<?php echo site_url('admin/quote/invoice/'.$item->invoicenum.'/'.$item->quoteid);?>"><?php echo $item->invoicenum;?></a>     <?php } ?>
			    		</td>
			    		<td><?php  if(isset($item->datedue) && $item->datedue!="") { echo date("m/d/Y", strtotime($item->datedue)); } else { echo "No Date Set"; }  ?></td>
			    		<?php if(0){?>
			    		<td>
			    		<?php if($item->paymentstatus){?>
			    			<form action="<?php echo site_url('admin/report/payinvoice');?>" method="post">
			    				<input type="hidden" name="invoicenum" value="<?php echo $report->invoicenum?>">
			    				<input type="submit" value="Pay" class="btn btn-primary"/>
			    			</form>
			    		<?php }?>
			    		</td>
			    		<?php }?>
			    	</tr>
			    	<?php
			    		}
			    	?>
		      </table>
		      <br/>
		      <br/>
	    	<?php
		   			}
		   			$totalall = $totalallprice - $totalallpaid;
		   			$totalallremaining = number_format($totalall,2);
		   			echo '<script>$("#totalallquantity").html("'.$totalallquantity.'");</script>';
		   			echo '<script>$("#totalallprice").html("'.$totalallprice.'");</script>';
		   			echo '<script>$("#totalallpaid").html("'.$totalallpaid.'");</script>';
		   			echo '<script>$("#totalallremaining").html("'.$totalallremaining.'");</script>';
	    	?>
	    </div>
    </div>
</section>