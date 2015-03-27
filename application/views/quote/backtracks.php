<script type="text/javascript" charset="utf-8">

function showdetail(quoteid)
{	
	//$(".dclose").css('display','none');
	if($('a','td#cellid_'+quoteid).text() == "Expand"){
		$('a','td#cellid_'+quoteid).text('Collapse');		
		$(jq("tab_"+quoteid)).css('display','table-row');
	}else{
		$('a','td#cellid_'+quoteid).text('Expand');
		$(jq("tab_"+quoteid)).css('display','none');
	}
}

function jq( myid ) {
 
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
 
}

</script>


    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Bid Back Orders </h3>		
		</div>		
	   <div id="container">
                     <div class="combofixed">       
                       <form method="post" class="form-inline" action="<?php echo site_url('quote/backtracks') ?>">
                        <div class="form-group">
                        <label class="form-label">Select Company</label>
                        <span>
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->companyname;?></option>
                            	<?php }?>
                            </select>
                        </span>
                      </div>
                     </form>
					</div>
		
		<?php 
		    	if($backtracks)
		    	{
		    ?>
		<div class="row">
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                               
                            </div>
                            
                            <div class="grid-body no-border">
                                    <table class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:9%">PO Number</th>
                                                <th style="width:22%">PO Date</th>
                                                <th style="width:6%">Details</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php 
									    	$i = 0;
									    	foreach($backtracks as $bck)
									    	{
									    		$bck2 = $bck['quote'];
									    		$i++;
									      ?>
                                            <tr>
                                                <td id="cellid_<?php echo @$bck2->id;?>" class="v-align-middle"><?php echo $bck2->ponum;?>
                                                <a href="javascript:void(0)" onclick="showdetail('<?php echo @$bck2->id;?>');">Expand</a>						                                    </td>
                                                <td class="v-align-middle"><?php echo $bck2->podate;?></td>
                                                <td class="v-align-middle">
                                                	<a class="btn btn-primary btn-xs btn-mini" href="<?php echo site_url('quote/viewbacktrack/'.$bck2->id);?>">Details</a>
                                                </td>
                                            </tr>
                                            <tr id="tab_<?php echo @$bck2->id;?>" style="display:none;" ><td colspan="3">
                                            
                                    <table class="table no-more-tables general" style="width: 95%;">
							    	<tr>
							    		<th width="8%">Item Image</th>
							    		<th width="20%">Item Name</th>
							    		<th width="6%">Qty. Req'd</th>
							    		<th width="6%">Qty. Due</th>
							    		<th width="7%">Unit</th>
							    		<th width="7%">Price EA</th>
							    		<th width="7%">Total Price</th>
							    		<th width="7%">Date Available</th>
							    		<th width="10%">Notes</th>
							    		<th width="10%">History</th>
							    	</tr>
							    	<form id="olditemform" class="form-horizontal" method="post">

									<?php  foreach($bck['items'] as $q) { //echo "<pre>data-"; print_r($quote->id); die;
									      
								        if(isset($q->itemimage->item_img) && $q->itemimage->item_img!= "" && file_exists("./uploads/item/".$q->itemimage->item_img)) 
							    		{
							    			$imgName = site_url('uploads/item/'.$q->itemimage->item_img);  
							    		} 
                                        else 
                                        { 
                                        	 $imgName = site_url('uploads/item/big.png');  
                                        } 
									      ?>
							    	<tr>
							    		<td><img src="<?php echo $imgName;?>" width="90px" height="90px"> </td>
							    		<td><?php echo htmlentities($q->itemname);?></td>
							    		<td><?php echo $q->quantity;?></td>
							    		<td><?php echo $q->quantity - $q->received;?>
							    		 <?php if(@$q->pendingshipments){?>
                                        <br/><?php echo $q->pendingshipments;?> - Pending Acknowledgement
                                        <?php }?>
							    		</td>
							    		<td><?php echo $q->unit;?></td>
							    		<td>$<?php echo $q->ea;?></td>
							    		<td>$<?php echo round($q->ea * ($q->quantity - $q->received), 2);?></td>
							    		<td><input readonly type="text" class="span daterequested highlight" name="daterequested<?php echo $q->id;?>" value="<?php echo $q->daterequested;?>" data-date-format="mm/dd/yyyy" style="width:90px;" /></td>
							    		<td><textarea readonly style="width: 85px;height:110px;" id="notes<?php echo $q->id;?>" name="notes<?php echo $q->id;?>" class="highlight"><?php echo $q->notes;?></textarea></td>
							    		<td>
							    		<?php if($q->etalog){?>
							    			<a href="javascript:void(0)" onclick="$('#etalogmodal<?php echo $q->id?>').modal();">
							    				<i class="icon icon-search"></i>View
							    			</a>
							    		<?php }?>
							    		</td>
							    	</tr>
							    	<?php }?>
							    	<!-- <tr>
							    		<td colspan="8">
										<input type="button" value="Update" class="btn btn-primary" onclick="$('#olditemform').submit();"/>&nbsp;&nbsp;
										<a href="<?php echo site_url('quote/track/'.$quote->id.'/'.$q->award);?>" target="_blank">
										<input type="button" value="Track/Send Shipment" class="btn btn-primary"/></a>
							    		</td>
							    	</tr> -->
							    	</form>
						    	</table>
                                            
                                            </td></tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    
                                      <div class="row">
                                     	<ul class="pagination pull-right">
                                    	<?php echo @$pagination; ?>
                                    	</ul>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else {?>
                	    <div class="errordiv">
      				<div class="alert alert-info">
                  <button data-dismiss="alert" class="close"></button>
                  <div class="msgBox">
                  No Backorder Items Detected.
                  </div>
                 </div>
      </div>
      
                <?php }?>
			
		</div>
	  </div> 
	  
	  
	  
	  <?php // echo "<pre>",print_r($backtracks); die;
	  foreach($backtracks as $bck){ 	 //echo "<pre>",print_r($bck['items']); die;
	  foreach($bck['items'] as $q) if($q->etalog) {?>
  <div id="etalogmodal<?php echo $q->id?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none; min-width: 700px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <table style="border:0px !important;" class="no-border">
           <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <tr><td style="border:0px;"><h3>ETA Update History</td></h3> <td style="border:0px;"><b>PO#: </b><?php if(isset($q->ponum)) echo $q->ponum; ?></td> <td style="border:0px;">Order Qty <?php if(isset($q->quantity)) echo number_format($q->quantity,0); ?></td></tr>
          <tr><td style="border:0px;"><b>Item Code:</b> <?php if(isset($q->itemcode)) echo $q->itemcode; ?></td> <td style="border:0px;"><b>Item Name: </b><?php if(isset($q->itemname)) echo $q->itemname ; ?></td> <td style="border:0px;"><b>Received Qty: </b><?php if(isset($q->received)) echo number_format($q->received,0) ; ?></td></tr>
          <tr><td style="border:0px;">&nbsp;</td> <td style="border:0px;"><b>Company: </b><?php if(isset($company->title)) echo $company->title; ?> </td> <td style="border:0px;"><b>Due Qty: </b><?php if(isset($q->quantity) && isset($q->received) ) { echo number_format(($q->quantity - $q->received),0); }?><?php if(@$q->pendingshipments){?> <br/><?php echo $q->pendingshipments;?> - Pending Acknowledgement <?php }?>
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
<?php } }?>