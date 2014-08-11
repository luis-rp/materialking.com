<?php $tax = 0;?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('.datefield').datepicker();
});

function submitForm(val)
{
	$("#invoicenum").val(val);
  	document.forms['invoiceform'].submit()
}
</script>

    <div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
		 <a href="<?php echo site_url('report/export'); ?>" class="btn btn-green">Export</a><br />		
			<h3>Report</h3>		
		</div>
	
	   <div id="container">
	   		<div class="combofixed" style="width:100%">
		   
    		   <form class="form-inline" action="<?php echo site_url('report')?>" method="post">
                    From: <input type="text" name="searchfrom" value="<?php echo @$_POST['searchfrom']?>" class="datefield" style="width: 70px;"/>
                    &nbsp;&nbsp;
                    To: <input type="text" name="searchto" value="<?php echo @$_POST['searchto']?>" class="datefield" style="width: 70px;"/>
                    &nbsp;&nbsp;
                    Company:
    				<select id="purchasingadmin" name="purchasingadmin" class="form-control selectpicker show-tick" style="width: 140px;">
    					<option value=''>All Companies</option>
    					<?php foreach($purchasingadmins as $company){?>
    						<option value="<?php echo $company->id?>"
    							<?php if(@$_POST['purchasingadmin']==$company->id){echo 'SELECTED';}?>
    							>
    							<?php echo $company->companyname?>
    						</option>
    					<?php }?>
    				</select>
                    &nbsp;&nbsp;
                    Sort By:
                    <select id="sortByFilter" name="sortByFilter" class="form-control selectpicker show-tick" style="width: 140px;">
                    	<option value=''>Select </option>
                    	<option value='project' <?php if(@$_POST['sortByFilter']=='project'){echo 'SELECTED';}?>>Project</option>
                    	<option value='paymentstatus' <?php if(@$_POST['sortByFilter']=='paymentstatus'){echo 'SELECTED';}?>>Payment Status</option>
                    	<option value='verificationstatus' <?php if(@$_POST['sortByFilter']=='verificationstatus'){echo 'SELECTED';}?>>Verification Status</option>
                    </select>
                    
                    <input type="submit" value="Filter" class="btn btn-primary"/>
                    <a href="<?php echo site_url('report');?>">
                    	<input type="button" value="Show All" class="btn btn-primary"/>
                    </a>
               </form>
               <br/><br/><br/><br/>
        </div>
           <?php if(@$reports){?>
		   
		<div class="row">
            <div class="col-md-12">
                <div class="grid simple ">
                    <div class="grid-title no-border">
                        <h4>&nbsp;</h4>
                    </div>
                    
                    
                    <div class="grid-body no-border">
			  		 <div>
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
    			  	</div>
           <?php }?>
           <hr/>
		   <?php 
		   		$totalallquantity = 0;
		   		$totalallprice = 0;
		   		$totalallpaid = 0;
		   		$i = 0;
		   		if(!@$reports) 
		   			echo '<br/><br/><br/><br/>No records found'; 
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
		   
    			  <div>
    			  	
    			  		<strong>DATE:</strong> <?php echo $report->receiveddate;?>
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
    			  </div>
			  
			    <table class="table no-more-tables general">
			    	<tr>
			    		<th width="120">Company</th>
			    		<th width="120">Project</th>
			    		<th width="75">PO#</th>
			    		<th width="120">Item Code</th>
			    		<th width="200">Item Name</th>
			    		<th width="50">Unit</th>
			    		<th width="50">Qty.</th>
			    		<th width="50">EA</th>
			    		<th width="50">Total</th>
			    		<th width="50">Payment</th>
			    		<th width="50">Verification</th>
			    		<th>Notes</th>
			    		<th>Invoice#</th>
			    	</tr>
			    	<?php 
			    		
        		   		$totalquantity = 0;
        		   		$totalprice = 0;
        		   		$totalpaid = 0;
        		   		
			    		foreach($report->items as $item)
			    		{
			    			$amount = $item->quantity * $item->ea;
			    			$amount = round($amount + ($amount*$item->taxpercent/100),2);
			    			$totalallprice += $amount;
			    			
			    			$totalquantity += $item->quantity;
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
			    		<td><a href="<?php echo site_url('quote/track/'.$item->quote.'/'.$item->award);?>"><?php echo $item->ponum;?></a>
			    		</td>
			    		<td><?php echo $item->itemcode;?></td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo $item->quantity;?></td>
			    		<td><?php echo $item->ea;?></td>
			    		<td>$<?php echo round($amount,2);?></td>
			    		<td><?php echo $item->paymentstatus;?></td>
			    		<td><?php echo $item->status;?></td>
			    		<td><?php echo $item->notes;?></td>
			    		<td>
			    		<form id="invoiceform" name="invoiceform" action="<?php echo site_url('quote/invoice');?>" method="post">
			    		<input type="hidden" name="invoicenum" id="invoicenum" value="<?php echo $item->invoicenum;?>">
			    		<a href="javascript: submitForm('<?php echo $item->invoicenum;?>');"><?php echo $item->invoicenum;?></a>
			    		</form>
			    		</td>
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