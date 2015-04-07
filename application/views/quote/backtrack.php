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
//$(document).ready(function(){
	//$('.daterequested').datepicker();
//});
//-->
</script>

<script type="text/javascript">
$.noConflict();
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

function caloclick(){
		$('.daterequested').datepicker();
	}
</script>

    <div class="content">
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title"><a href="<?php echo site_url('quote/viewbacktrack_export').'/'.$quoteid->id; ?>" class="btn btn-green">Export</a> &nbsp;&nbsp;<a href="<?php echo site_url('quote/viewbacktrack_pdf').'/'.$quoteid->id; ?>" class="btn btn-green">View PDF</a><br />
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

							    <table class="table">
							    	<tr>
							    		<th width="15%">Item Name</th>
							    		<th width="15%">Item Image</th>
							    		<th width="8%">Qty. Req'd</th>
							    		<th width="8%">Qty. Due</th>
							    		<th width="8%">Unit</th>
							    		<th width="8%">Price EA</th>
							    		<th width="8%">Total Price</th>
							    		<th width="12%">Date Available</th>
							    		<th width="12%">Notes</th>
							    		<th width="6%">History</th>
							    	</tr>
							    	<form id="olditemform"  method="post" action="<?php echo base_url(); ?>quote/updateeta/<?php echo $quote->id;?>">

									<?php foreach($backtrack['items'] as $q) { 
										
										 if(isset($q->item_img) && $q->item_img!= "" && file_exists("./uploads/item/".$q->item_img)) 
								    		{ ?>
	                                        <?php $imgName = site_url('uploads/item/'.$q->item_img);  } 
	                                        else { ?>
	                                        <?php $imgName = site_url('uploads/item/big.png');  } 
										?>
							    	<tr>
							    		<td style="word-break:break-all;"><?php echo htmlentities($q->itemname);?></td>
							    		<td><img style="max-height: 120px; padding: 5px;" height="120" width="120" src="<?php echo $imgName;?>" alt="<?php echo $imgName;?>"></td>
							    		<td style="word-break:break-all;"><?php echo $q->quantity;?></td>
							    		<td style="word-break:break-all;"><?php echo $q->quantity - $q->received;?>
							    		 <?php if($pendingshipments){?>
                                        <br/><?php echo $pendingshipments;?> - Pending Acknowledgement
                                        <?php }?>
							    		</td>
							    		<td style="word-break:break-all;"><?php echo $q->unit;?></td>
							    		<td style="word-break:break-all;">$<?php echo $q->ea;?></td>
							    		<td style="word-break:break-all;">$<?php echo round($q->ea * ($q->quantity - $q->received), 2);?></td>
							    		<td style="word-break:break-all;"><input type="text" style="width:100%;" class="span daterequested highlight" onmouseover="caloclick()" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" onchange="clearnotes('notes<?php echo $q->id;?>');" />
							    		<?php $datediff = (strtotime($q->daterequested) - time()); $datediff = abs(floor($datediff/(60*60*24))); echo "Item is ".@$datediff." Days Late";?>
							    		</td>
							    		<td style="word-break:break-all;"><textarea id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" style="width:100%;" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		<td style="word-break:break-all;">
							    		<?php if($q->etalog){?>
							    			<a href="javascript:void(0)" onclick="$('#etalogmodal<?php echo $q->id?>').modal();">
							    				<i class="icon icon-search"></i>View
							    			</a>
							    		<?php }?>
							    		</td>
							    	</tr>
							    	<?php }?>
							    	<tr>
							    		<td colspan="10">
										<input type="button" value="Update" class="btn btn-primary" onclick="$('#olditemform').submit();"/>&nbsp;&nbsp;
										<a href="<?php echo site_url('quote/track/'.$quote->id.'/'.$q->award);?>" target="_blank">
										<input type="button" value="Track/Send Shipment" class="btn btn-primary"/></a>
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
          <table style="border:0px !important;" class="no-border">
           <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <tr><td style="border:0px;"><h3>ETA Update History</td></h3> <td style="border:0px;"><b>PO#: </b><?php if(isset($quote->ponum)) echo $quote->ponum; ?></td> <td style="border:0px;">Order Qty <?php if(isset($q->quantity)) echo number_format($q->quantity,0); ?></td></tr>
          <tr><td style="border:0px;"><b>Item Code:</b> <?php if(isset($q->itemcode)) echo $q->itemcode; ?></td> <td style="border:0px;"><b>Item Name: </b><?php if(isset($q->itemname)) echo $q->itemname ; ?></td> <td style="border:0px;"><b>Received Qty: </b><?php if(isset($q->received)) echo number_format($q->received,0) ; ?></td></tr>
          <tr><td style="border:0px;">&nbsp;</td> <td style="border:0px;"><b>Company: </b><?php if(isset($company->title)) echo $company->title; ?> </td> <td style="border:0px;"><b>Due Qty: </b><?php if(isset($q->quantity) && isset($q->received) ) { echo number_format(($q->quantity - $q->received),0); }?><?php if($pendingshipments){?> <br/><?php echo $pendingshipments;?> - Pending Acknowledgement <?php }?>
          </td></tr><table>


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
          		<td><?php if ($i==0) echo "changed from ".$q->quotedaterequested->daterequested." to ".$l->daterequested; else echo "changed from ".$olddate." to ".$l->daterequested; ?></td>
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