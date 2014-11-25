<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery.price_format.js"></script>
<script type="text/javascript" src="<?php // echo base_url();?>templates/admin/js/jquery-ui.js"></script>

<!--<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>-->
		<link href="<?php echo base_url();?>templates/front/assets/plugins/boostrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.2.js"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

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
            		{ "bSortable": false }
    		
    			]
    		} );
   	 $('.dataTables_length').hide();
    })
                       
</script>
 

<section class="row-fluid">
<?php echo $this->session->flashdata('message'); ?>
	<h3 class="box-header">Bid</h3>
	  <div class="box">
	    <div class="span12">    

	      <form class="form-inline" action="<?php echo site_url('admin/quote/contractbids') ?>" method="post">
	      
              <!-- <div class="form-group">-->
                   <label class="form-label">Select Status</label>
                      <!--<span>-->
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
                        <!--</span>
                      </div>-->
                      
                   
                        <!-- <label class="form-label">Select Company</label>
                    
                         	<select name="searchpurchasingadmin" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<?php foreach($purchasingadmins as $pa){?>
                            	<option value='<?php echo $pa->id;?>' <?php if(@$_POST['searchpurchasingadmin'] ==$pa->id){echo 'SELECTED';}?>><?php echo $pa->companyname;?></option>
                            	<?php }?>
                            </select> -->
                        
                      
                      <?php if(@$_POST['searchpurchasingadmin']){?>
                     <!-- <div class="form-group">-->
                        <label class="form-label">Select Project</label>
                       <!-- <span>-->
                         	<select name="searchproject" class="form-control selectpicker show-tick" style="width:auto" onchange="this.form.submit()">
                            	<option value=''>All</option>
                            	<?php foreach($projects as $p){?>
                            	<option value='<?php echo $p->id;?>' <?php if(@$_POST['searchproject'] ==$p->id){echo 'SELECTED';}?>><?php echo $p->title;?></option>
                            	<?php }?>
                            </select>
                        <!--</span>
                      </div>-->
                      <?php }?>                   
                     </form>
					
    		                   <?php if($invitations) {  ?> 		   
                                     <table id="datatable" class="table table-bordered datagrid">
                                        <thead>
                                            <tr>
                                                <th style="width:10%">Title</th>
                                                <th style="width:10%">Date Due</th>
                                                <th style="width:10%">Date Received</th>
                                                <th style="width:10%">Date Sent</th>
                                                <th style="width:10%">Award Date</th>
                                                <th style="width:10%">Project Start Date</th>
                                                <th style="width:15%">Status</th>
                                                <th style="width:15%">Bid</th>
                                                <th style="width:25%">Bid Progress</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;									    	
									    	foreach($invitations as $inv)
									    	{
									    		//echo "<pre>"; print_r($invitations); die;
									    		$i++;
									      ?>
                                            <tr>
                                            
                                                <td class="v-align-middle">
                                                <?php if(!($inv->status == 'New'||$inv->status == 'Processing')){?>
	                                                <a href="<?php echo site_url('admin/quote/contractitems/'.$inv->quote);?>">
	                                                	<?php echo $inv->ponum;?>
	                                                </a>
                                                <?php }else{?>
                                                	<?php echo $inv->ponum;?>
                                                <?php }?>
                                                </td>
                                                
                                                <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->quotedetails->duedate));?></td>                                      								<td class="v-align-middle"><?php if(isset($inv->senton)) echo date('m/d/Y',strtotime($inv->senton));?></td>
                                          <td class="v-align-middle"><?php if(isset($inv->daterequested))   echo date('m/d/Y',strtotime($inv->daterequested));?></td>
                                                <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->quotedetails->podate));?></td>                                                                    <td class="v-align-middle"><?php echo date('m/d/Y',strtotime($inv->quotedetails->startdate));?></td>          
                                                <td class="v-align-middle"><?php echo $inv->status;?></td>
                                                <td>
                                                	<?php if($inv->status == 'New'||$inv->status == 'Processing'){?>
                                                		<?php if($inv->quotedetails->potype=='Contract'){?>
                                                    	<a href="<?php echo site_url('admin/quote/invitation/'.$inv->invitation);?>">
    										    			<span class="label label-success">BID</span>
    										    		</a>    										    		
										    		<?php }elseif($inv->awardedtothis){?>
                                                	<a href="<?php echo site_url('quote/track/'.$inv->quote.'/'.$inv->award);?>">
										    			<span class="label label-success">TRACK</span>
										    		</a>
										    		<?php } } ?>
										    		<span class="label label-important"><?php echo $inv->status?></span>
										    	</td>
                                                <td class="v-align-middle">
                                                    <div class="progress progress-striped active progress-large">
                      									<div class="progress-bar <?php echo $inv->mark; ?>" style="width: <?php echo $inv->progress?>%;" data-percentage="<?php echo $inv->progress?>%"><?php echo $inv->progress?>% </div>
                   									</div>
                    
                                                </td>
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                                 
                <?php }  else { ?>              
                    <div class="errordiv">
      				 <div class="alert alert-info"><button data-dismiss="alert" class="close"></button>
                         <div class="msgBox"> No Quotations Detected on System. </div>                                         
                     </div>
      				</div>
                <?php }?>
		  </div>	
		</div>
		</section>
	