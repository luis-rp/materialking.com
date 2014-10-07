<?php
	//echo '<pre>';print_r($reports);die;
	$tax = $settings->taxpercent;
?>
<script type="text/javascript">
<!--
$(document).ready(function(){
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
                From: <input type="text" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="datefield" style="width: 70px;"/>
                &nbsp;&nbsp;
                To: <input type="text" name="searchto" value="<?php echo @$_POST['searchto']?>" class="datefield" style="width: 70px;"/>
                &nbsp;&nbsp;
                Company:
				<select id="searchcompany" name="searchcompany">
					<option value=''>All Companies</option>
					<?php foreach($companies as $company){?>
						<option value="<?php echo $company->id?>"
							<?php if(@$_POST['searchcompany']==$company->id){echo 'SELECTED';}?>
							>
							<?php echo $company->title?>
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

			  <div>

			  		<strong>DATE:</strong> From <?php echo @$_POST['searchfrom']?> - To <?php echo @$_POST['searchto']?>
			  		<br/>
			  		<strong>TOTAL QUANTITY:</strong> <span id="totalallquantity"></span>
			  		<br/>
			  		<strong>TOTAL AMOUNT:</strong> $<span id="totalallprice"></span>
			  		<br/>
			  		<strong>TOTAL PAID:</strong> $<span id="totalallpaid"></span>
			  		<br/>
			  		<strong>TOTAL REMAINING:</strong> $<span id="totalallremaining"></span>
			  </div>
           <?php }?>
           <hr/>
		   <?php
		   		$totalallquantity = 0;
		   		$totalallprice = 0;
		   		$totalallpaid = 0;
		   		if(!@$reports)
		   			echo 'No Records Found';
		   		else
		   			foreach($reports as $report)
		   			{
			    		if(!$report->totalpaid) $report->totalpaid = 0;
		   				$report->totalpaid = $report->totalpaid + ($report->totalpaid*$tax/100);
		   				$report->totalpaid = round($report->totalpaid,2);

		   				$report->totalprice = $report->totalprice + ($report->totalprice*$tax/100);
		   				$report->totalprice = round($report->totalprice,2);

		   				$totalallquantity+=$report->totalquantity;
			    		$totalallpaid += $report->totalpaid;
		   ?>

			  <div>
			  		<strong>DATE:</strong> <?php echo date('m/d/Y', strtotime($report->receiveddate));?>
			  		<br/>
			  		<strong>TOTAL QUANTITY:</strong> <?php echo $report->totalquantity;?>
			  		<br/>
			  		<strong>TOTAL AMOUNT:</strong> $<?php echo $report->totalprice;?>
			  		<br/>
			  		<strong>TOTAL PAID:</strong> $<?php echo $report->totalpaid;?>
			  		<br/>
			  		<strong>TOTAL REMAINING:</strong> $<?php echo $report->totalprice - $report->totalpaid;?>
			  </div>
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
			    			$amount = $item->quantity * $item->ea;
			    			$amount = round($amount + ($amount*$tax/100),2);
			    			$totalallprice += $amount;
			    	?>
			    	<tr>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->ponum;?></td>
			    		<td><?php echo $item->itemcode;?></td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo $item->quantity;?></td>
			    		<td><?php echo $item->ea;?></td>
			    		<td>$<?php echo round($amount,2);?></td>
			    		<td><?php echo $item->paymentstatus;?></td>
			    		<td><?php echo $item->status;?></td>
			    		<td><?php echo $item->costcode;?></td>
			    		<td><?php echo $item->notes;?></td>
			    		<td><a target="_blank" href="<?php echo site_url('admin/quote/invoices');?>"><?php echo $item->invoicenum;?></a></td>
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
		   			$totalallremaining = $totalallprice - $totalallpaid;

		   			echo '<script>$("#totalallquantity").html("'.$totalallquantity.'");</script>';
		   			echo '<script>$("#totalallprice").html("'.$totalallprice.'");</script>';
		   			echo '<script>$("#totalallpaid").html("'.$totalallpaid.'");</script>';
		   			echo '<script>$("#totalallremaining").html("'.$totalallremaining.'");</script>';
	    	?>
	    </div>
    </div>
</section>