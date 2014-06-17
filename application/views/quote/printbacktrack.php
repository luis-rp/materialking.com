
<?php //print_r($quoteitems);die;?>
<link href="<?php echo base_url(); ?>templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
	
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('.daterequested').datepicker();
	$('.substituterow').hide();
	window.print();
	window.close();
});

//-->
</script>

<section class="row-fluid">
	<h3 class="box-header">
		Please Update the date available for following items
		<span class="pull-right">
			<a class="btn" href="javascript:void(0)" onclick="window.open('<?php echo site_url('home/backtrack/'.$invitation.'/print');?>')">Print</a>
			&nbsp;&nbsp;&nbsp;
		</span>
	</h3>
	<div class="box">
		<div class="span12">
			  <strong>
		      PO#: <?php echo $quote->ponum;?>
		      <br/>
		      Due: <?php echo $quote->duedate;?>
		      <br/>
		      Company: <?php echo $company->title;?>
		      <br/>
		      Contact: <?php echo $company->contact;?>
		      </strong>
		      <br/><br/>
		      Please enter Date Available and add any Notes you may <br/>
have related to each item. When you are finished, Click the Save button.<br/><br/>
Thank You,<br/>
Kreit Mechanical Assocaites, Inc.
		     <br/><br/><br/>
		  <div class="control-group">
		    <table class="table table-bordered" style="width: 95%;">
		    	<tr>
		    		<th>Item Name</th>
		    		<th>Qty.</th>
		    		<th>Unit</th>
		    		<th>Date Available</th>
		    		<th>Notes</th>
		    		
		    	</tr>
		    	
				<?php foreach($awarded->items as $q) if($q->company == $company->id){//print_r($q);?>
				
		    	<tr>
		    		<td><?php echo htmlentities($q->itemname);?></td>
		    		<td><?php echo htmlentities($q->quantity - $q->received);?></td>
		    		<td><?php echo $q->unit;?></td>
		    		<td><?php echo $q->daterequested;?></td>
		    		<td><?php echo $q->notes;?></td>
		    		
		    	</tr>
		    	<?php }?>
	    	</table>
	    </div>
    </div>
</section>
