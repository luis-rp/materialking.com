<?php 
	$recsum =0;
    $qntsum =0;
	foreach($backtrack['items'] as $ai)
	{
		$recsum = $recsum + $ai->received;
        $qntsum = $qntsum + $ai->quantity;
        //print_r($ai);die;
	}
	if($qntsum==0) $per=0;
	else $per = number_format(($recsum/$qntsum)*100,2);
    $per .='%';
?>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('.daterequested').datepicker();
});
//-->
</script>
<script>
$(document).ready(function(){
        <?php if($per=='0.00%') { ?>
            $("#timelineid").attr("class","bar madras");
            $("#timelineid").css("width",'100%');
        <?php }else{ ?>
        	$("#timelineid").css("width",'<?php echo $per;?>');
        <?php } ?>
});

function clearnotes(noteid){
	
	$('#'+noteid).val("");
	
}

</script>

    <div class="content"> 
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Please Update the date available for following items</h3>		
		</div>		
	   <div id="container">
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                
                            </div>
                            <div class="grid-body no-border">
                            
                            <div class="progress progress-striped active progress-large">
						    	<div class="progress-bar progress-bar-warning" style="width: 80%;" data-percentage="0%">Bid Progress: 80%, PARTIALLY COMPLETED</div>
							</div>
                            
                            <div class="progress progress-striped active progress-large">
						    	<div class="progress-bar progress-bar-success" style="width: <?php echo $per;?>;" data-percentage="0%"><?php echo $per;?> items received</div>
							</div>
                                   
								<table class="table no-more-tables general">
									<tr>
										<td>
										  <strong>
									      PO#: <?php echo $quote->ponum;?>
									      <br/>
									      Company: <?php echo $company->title;?>
									      <br/>
									      Contact: <?php echo $company->contact;?>
									      </strong>
									      <br/><br/>
									      	Please update us on the estimated delivery dates for the following still due items.<br/><br/>
											Thank You,<br/>
											<strong><?php if(isset($pa->companyname)) echo $pa->companyname?></strong>
									     	<br/><br/>
									     </td>
									 </tr>
								</table>
								
							    <table class="table no-more-tables general" style="width: 95%;">
							    	<tr>
							    		<th>Item Name</th>
							    		<th>Qty. Req'd</th>
							    		<th>Qty. Due</th>
							    		<th>Unit</th>
							    		<th>Price EA</th>
							    		<th>Total Price</th>
							    		<th>Date Available</th>
							    		<th>Notes</th>
							    		<th>History</th>
							    	</tr>
							    	<form id="olditemform" class="form-horizontal" method="post" action="<?php echo base_url(); ?>quote/updateeta/<?php echo $quote->id;?>"> 
								  	
									<?php foreach($backtrack['items'] as $q) {//print_r($q);?>
									
							    	<tr>
							    		<td><?php echo htmlentities($q->itemname);?></td>
							    		<td><?php echo $q->quantity;?></td>
							    		<td><?php echo $q->quantity - $q->received;?></td>
							    		<td><?php echo $q->unit;?></td>
							    		<td>$<?php echo $q->ea;?></td>
							    		<td>$<?php echo round($q->ea * ($q->quantity - $q->received), 2);?></td>
							    		<td><input type="text" class="span daterequested highlight" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" onchange="clearnotes('notes<?php echo $q->id;?>');" /></td>
							    		<td><textarea style="width: 175px" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		<td>
							    		<?php if($q->etalog){?>
							    			<a href="javascript:void(0)" onclick="$('#etalogmodal<?php echo $q->id?>').modal();">
							    				<i class="icon icon-search"></i>View
							    			</a>
							    		<?php }?>
							    		</td>
							    	</tr>
							    	<?php }?>
							    	<tr>
							    		<td colspan="8">
										<input type="button" value="Update" class="btn btn-primary" onclick="$('#olditemform').submit();"/>
							    		</td>
							    	</tr>
							    	</form>
						    	</table>
								
								
                            </div>
                        </div>
                    </div>
                </div>
                
			
		</div>
	  </div> 
<?php foreach($backtrack['items'] as $q) if($q->etalog) {?>  
  <div id="etalogmodal<?php echo $q->id?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none; min-width: 700px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h3>ETA Update History
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          </h3>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
          	<tr>
          		<th>Date</th>
          		<th>Notes</th>
          		<th>Updated</th>      		
          	</tr>
          	<?php $i=0; foreach($q->etalog as $l){?>
          	<tr>
          		<td><?php if ($i==0) echo $l->daterequested; else echo "changed from ".$olddate." to ".$l->daterequested; ?></td>
          		<td><?php echo $l->notes;?></td>
          		<td><?php echo date("m/d/Y", strtotime($l->updated));?></td>
          	</tr>
          	<?php $i++; $olddate = $l->daterequested; }?>
          </table>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<?php }?>