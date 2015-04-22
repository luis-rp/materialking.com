
<script type="text/javascript">
//$.noConflict();
 </script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

                
<script type="text/javascript" charset="utf-8">
    $(document).ready( function() {
    	$('#datatable').dataTable( {
    		"sPaginationType": "full_numbers",
    		"aaSorting": [[ 2, "desc" ]],
    		"aoColumns": [
            		null,
            		null,
            		null,
            		null,
            		null,
            		{ "bSortable": false},
            		{ "bSortable": false },
            		{ "bSortable": false }
    		
    			]
    		} );
   	 $('.dataTables_length').hide();
    });

</script>
 
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Bid Invitations </h3>		
		</div>		
	   <div id="container">
                     <div class="combofixed">       
                       <form method="post" class="form-inline" action="<?php echo site_url('quote') ?>">
                        <div class="form-group">
                        <label class="form-label">Select Status</label>
                        <span>
                         	<select name="searchstatus" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<option value='New' <?php if(@$_POST['searchstatus'] =='New'){echo 'SELECTED';}?>>New</option>
                            	<option value='Processing' <?php if(@$_POST['searchstatus'] =='Processing'){echo 'SELECTED';}?>>Processing</option>
                            	<option value='PO Closed - 0 items won' <?php if(@$_POST['searchstatus'] =='PO Closed - 0 items won'){echo 'SELECTED';}?>>PO Closed - 0 items won</option>
                            	<option value='Awarded' <?php if(@$_POST['searchstatus'] =='Awarded'){echo 'SELECTED';}?>>Awarded</option>
                            	<option value='Partially Awarded' <?php if(@$_POST['searchstatus'] =='Partially Awarded'){echo 'SELECTED';}?>>Partially Awarded</option>
                            	<option value='Partially Completed' <?php if(@$_POST['searchstatus'] =='Partially Completed'){echo 'SELECTED';}?>>Partially Completed</option>
                            	<option value='Completed' <?php if(@$_POST['searchstatus'] =='Completed'){echo 'SELECTED';}?>>Completed</option>
                            </select>
                        </span>
                      </div>
                      
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
                      <?php if(@$_POST['searchpurchasingadmin']){?>
                      <div class="form-group">
                        <label class="form-label">Select Project</label>
                        <span>
                         	<select name="searchproject" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<?php foreach($projects as $p){?>
                            	<option value='<?php echo $p->id;?>' <?php if(@$_POST['searchproject'] ==$p->id){echo 'SELECTED';}?>><?php echo $p->title;?></option>
                            	<?php }?>
                            </select>
                        </span>
                      </div>
                      <?php }?>
                      
                     </form>
					</div>
		
    		    <?php 
    		    	if($invitations)
    		    	{
    		    ?>
        		<div class="row">
                    <div class="col-md-12">
                    
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                                
                            </div>
                            
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">PO#</th>
                                                <th style="width:10%">Quote Ref#</th>
                                                <th style="width:10%">Total Value</th>
                                                <th style="width:10%">Date Received</th>
                                                <th style="width:10%">Date Sent</th>
                                                <th style="width:10%">Status</th>
                                                <th style="width:15%">Bid</th>
                                                <th style="width:30%">PO Progress</th>
                                                <th style="width:10%">Actions</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
							             
									    	$i = 0;
									    	foreach($invitations as $inv)
									    	{
									    		$i++;
									      ?>
                                            <tr>
                                                <td class="v-align-middle">
                                                <?php if(!($inv->status == 'New'||$inv->status == 'Processing')){?>
	                                                <a href="<?php echo site_url('quote/items/'.$inv->quote);?>">
	                                                	<?php echo $inv->ponum;?>
	                                                </a>
                                                <?php }else{?>
                                                	<?php echo $inv->ponum;?>
                                                <?php }?>
                                                </td>
                                                <td class="v-align-middle"><?php echo $inv->quotenum;?></td>
                                                <td class="v-align-middle">$ <?php echo @$inv->totalvalue;?></td>
                                                <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->senton));?></td>
                                                <td class="v-align-middle"><?php if(isset($inv->submitdate)) echo date('m/d/Y',strtotime($inv->submitdate));?></td>
                                                <td class="v-align-middle"><?php echo $inv->status;?></td>
                                                <td>
                                                	<?php if($inv->status == 'New'||$inv->status == 'Processing'){?>
                                                		<?php if($inv->quotedetails->potype=='Bid'){?>
                                                    	<a href="<?php echo site_url('quote/invitation/'.$inv->invitation);?>">
    										    			<span class="label label-success">BID</span>
    										    		</a>
    										    		<?php }elseif($inv->quotedetails->potype=='Direct'){?>
                                                    	<a href="<?php echo site_url('quote/direct/'.$inv->invitation);?>">
    										    			<span class="label label-success">REVIEW</span>
    										    		</a>
    										    		<?php }?>
										    		<?php }elseif($inv->awardedtothis){?>
                                                	<a href="<?php echo site_url('quote/track/'.$inv->quote.'/'.$inv->award);?>">
										    			<span class="label label-success">TRACK</span>
										    		</a>
										    		<?php }?>
										    		<span class="label label-important"><?php echo $inv->status?></span>
										    	</td>
                                                <td class="v-align-middle">
                                                    <div class="progress progress-striped active progress-large">
                      									<div class="progress-bar <?php echo $inv->mark; ?>" style="width: <?php echo $inv->progress?>%;" data-percentage="<?php echo $inv->progress?>%"><?php echo $inv->progress?>% </div>
                   									</div>
                    
                                                </td>
                                                <td class="v-align-middle"> 	
                                                <?php 
                                                if($inv->status == 'New')
                                                { $bidorquoteid = (@$inv->bidid)?$inv->bidid:$inv->quote;
                                                  $usebidorquote = (@$inv->bidid)?"Bid":"Quote"; ?>
                                                	<a href="<?php echo site_url('quote/rejectquote/'.@$bidorquoteid.'/'.$usebidorquote);?>">Reject </a> &nbsp;
                                                	<a href="<?php echo site_url('quote/removequote/'.$inv->quotedetails->id);?>">Remove </a>
                                             <?php }
                                             if($inv->status == 'Completed')
                                                {?>                                            
		                                                <a href="<?php echo site_url('quote/archivequote/'.$inv->quotedetails->id);?>">Archive </a>		                                  <?php }      
		                                        
		                                       if($inv->status == 'PO Closed - 0 items won')
                                                {  ?>     
                                                	     <a href="<?php echo site_url('quote/archivequote/'.$inv->quotedetails->id);?>">Archive </a>     
                                          <?php }
                                                        
		                                        if($inv->status == 'New'||$inv->status == 'Processing'){?>
                                                		<?php if($inv->quotedetails->potype=='Bid'){?>
                                                    	
                                                    	<?php if($inv->status != 'New'){ ?>
                                                    		<a href="<?php echo site_url('quote/invitation/'.$inv->invitation);?>">
    										    			<span class="label label-success">CHECK YOUR SCORE</span>
    										    		</a>
    										    		<?php } ?>
    										    		<?php }elseif($inv->quotedetails->potype=='Direct'){?>
                                                    	<a href="<?php echo site_url('quote/direct/'.$inv->invitation);?>">    			
    										    		</a>
    										    		<?php }?>
										    		<?php }  ?>      
		                                                   
                                                </td>
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
                      No Quotations Detected on System.
                      </div>
                     </div>
      				</div>
                <?php }?>
			
		</div>
	  </div> 