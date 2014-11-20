<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
 
<script type="text/javascript" charset="utf-8">
                        $(document).ready( function() {
                        	$('#datatable').dataTable( {
                        		"aaSorting": [],
                        		"bPaginate": false,
                        		"sPaginationType": "full_numbers",
                        		"aoColumns": [
		                        		null,
		                        		null,		                        		
		                        		null,
		                        		null,		                        		
		                        		{ "bSortable": false}
                        		
                        			]
                        		} );
                       	 $('.dataTables_length').hide();
                        })
                       
</script>

<style>
.awarded-to-me td
{
	color: green;
}
.not-awarded-to-me td
{
	color: red;
}

h3 {
	background: none repeat scroll 0 0 #000;
    border-radius: 6px 6px 0 0;
    color: #fff;
    display: inline-block;
    font-size: 18px;
    font-weight: bold;
    padding: 0.5% 0 0.5% 1%;
    position: relative;
    top: 10px;
    width: 100%;
}

label
{
display:none;
}
</style> 
    <div class="content"> 
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			
			<h3>Contract Performance : <?php echo $quote->ponum;?> <font color="green">Items Won : <?php echo $itemswon;?></font> 	 
			<a href="<?php echo site_url('admin/quote/items_export/'.$quoteid); ?>" class="btn btn-success btn-xs btn-mini">Export</a> 	&nbsp;&nbsp;
		    <a href="<?php echo site_url('admin/quote/items_pdf/'.$quoteid); ?>" class="btn btn-success btn-xs btn-mini">View PDF</a> &nbsp;&nbsp;&nbsp;
			<font color="red">Items Lost : <?php echo $itemslost;?></font>
				<?php if($award && $itemswon && @$award->id){?>
					<a class="btn btn-success btn-xs btn-mini" href="<?php echo site_url('admin/quote/trackpurchaser/'.$quote->id.'/'.$award->id);?>">Track</a>
				<?php }?>
						 
				<?php if($bid){?>
					<a class="btn btn-success btn-xs btn-mini" href="<?php echo site_url('admin/quote/viewbid/'.$bid->id); ?>">View Bid as HTML</a>
				<?php }?> </h3>
					
		</div>	
			 
 <?php  if($allawardeditems) { ?>
	<div id="container" style="width:100%;margin-left:15px;margin-top:-55px;"> 
		<div class="row">
           <div class="col-md-12">
                 <div class="grid simple">
                      <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                       </div>
                       <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general" style="width:99%;">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">Files</th>
                                                <th style="width:30%">Description</th>                                                
                                                <th style="width:10%">Price</th>
                                                <th style="width:10%">Total</th>
                                                <!-- <th style="width:10%">Requested</th> -->
                                                <th style="width:30%">Notes</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($allawardeditems as $ai)
									    	{   $bidea = 0;
									    		$i++;
									      ?>
                                            <tr class="<?php echo $ai->company == $company->id?'awarded-to-me':'not-awarded-to-me';?>">
                                                <td class="v-align-middle">
                                               <!-- <?php if(@$ai->attach && file_exists("./uploads/quote/".$ai->attach)){?>
				                        		<br>
				                        		<a href="<?php echo site_url('uploads/quote').'/'.@$ai->attach ;?>" target="_blank">  &nbsp;
				                        		View File
				                          		</a>
				                				<?php }?>-->
                                               <?php if(@$ai->attach){ $files=""; $files=explode(',',@$ai->attach); foreach ($files as $file) {?>
								    			<?php if($file && file_exists("./uploads/quote/".$file)){?>
					                        	<br>
					                        	<a href="<?php echo site_url('uploads/quote').'/'.$file ;?>" target="_blank">  &nbsp;
					                        	View File
					                          	</a>
					                          	<?php } } }?>    
                                                </td>
                                                <td class="v-align-middle"><?php echo $ai->itemname;?></td>                                         
                                                <!-- <td class="v-align-middle">$<?php foreach($biditems as $biditem) {  if($biditem->itemid == $ai->itemid) { echo $biditem->ea; $bidea = $biditem->ea; } } ?></td>
                                                <td class="v-align-middle">$<?php if($bidea!=0) { echo round($ai->quantity * $bidea,2); } else { echo round($ai->quantity * $ai->ea,2); } ?></td> -->
                                                <td class="v-align-middle">$<?php echo $ai->ea;?></td>
                                                <td class="v-align-middle">$<?php echo round($ai->totalprice,2); ?></td>
                                                <!-- <td class="v-align-middle"><?php echo $ai->daterequested;?></td> -->
                                                <td class="v-align-middle"><?php echo $ai->notes;?></td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    <br/>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php } else { ?>
                
                    <div class="errordiv">
      				 <div class="alert alert-info">
                      <button data-dismiss="alert" class="close"></button>
                      <div class="msgBox">
                      No Items Detected on System.
                      </div>
                     </div>
      				</div>
                <?php }?>
			
		</div>
	  </div> 