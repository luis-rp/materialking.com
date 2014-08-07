 <script type="text/javascript">
 $(document).ready(function(){
 tour2 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Backorder Items"
	  },
	 
   	  
	]
	});

	


	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour2.restart();
			// Initialize the tour
			tour2.init();
			start();
		});
	$('#canceltour').live('click',endTour);
 });
 function start(){
	 
		// Start the tour
			tour2.start();
		 }
 function endTour(){
	 
	 $("#tourcontrols").remove();
	 tour2.end();
		}
 </script>
<?php 
    $quotes = array();
    if(@$backtracks)
    foreach($backtracks as $ponum=>$backtrack)
        if(@$backtrack['items'])
            $quotes[] = $backtrack['quote']->ponum;
?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div>

<section class="row-fluid">
	<h3 class="box-header" style="display:inline" id="step1"><?php echo @$heading; ?> - <?php echo $this->session->userdata('managedprojectdetails')->title?></h3>
	<div class="box">
	<div class="span12"><a id="step10" href="<?php echo site_url('admin/backtrack/export')?>" class="btn btn-green">Export</a>
		<div class="span12">
		    <?php echo $this->session->flashdata('message'); ?>
		   <br/>
		   
		   <form class="form-inline" action="<?php echo site_url('admin/backtrack')?>" method="post">
                Item: <input type="text" name="searchitem" value="<?php echo @$_POST['searchitem']?>"/>
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
                PO:
				<select id="searchponum" name="searchponum">
					<option value=''>All PO#</option>
					<?php foreach($quotes as $ponum){?>
						<option value="<?php echo $ponum?>"
							<?php if(@$_POST['searchponum']==$ponum){echo 'SELECTED';}?>
							>
							<?php echo $ponum?>
						</option>
					<?php }?>
				</select>
                &nbsp;&nbsp;
                <input type="submit" value="Filter" class="btn btn-primary"/>
                <a href="<?php echo site_url('admin/backtrack');?>">
                	<input type="button" value="Show All" class="btn btn-primary"/>
                </a>
           </form>
		   <br/>
		   <?php 
		   	if(!@$backtracks) 
		   		echo 'No Backorders Found'; 
		   	else 
		   		foreach($backtracks as $backtrack)
		   			if(@$backtrack['items'])
		   			{
		   				
						$combocompanies = array();
						foreach($backtrack['items'] as $q)
						{
							if($q->received < $q->quantity)
							{
								$combocompanies[$q->company] = array();
								$combocompanies[$q->company]['value'] = $q->company;
								$combocompanies[$q->company]['label'] = $q->companyname;
							}
						}
						//print_r($combocompanies);die;
		   ?>
		   
			  <div>
			  	
			  		<span class="poheading">PO#: <?php echo $backtrack['quote']->ponum;?></span>
			  		<?php if($this->session->userdata('usertype_id')<3){?>
			  		<a class="btn btn-primary" href="<?php echo site_url('admin/quote/track/'.$backtrack['quote']->id);?>">Track</a>
			  		<?php }?>
				  <div class="pull-right">
				  	<form action="<?php echo site_url('admin/backtrack/sendbacktrack/'.$backtrack['quote']->id);?>" method="post" class="pull-right form-horizontal">
				  	<strong>Send ETA request for PO# <?php echo $backtrack['quote']->ponum;?> to: &nbsp;</strong>
				  	<select id="combocompany" name="company" required>
			       		<option value=''>Select Company</option>
			       		<?php foreach($combocompanies as $combocompany){?>
			       		<option value="<?php echo $combocompany['value'];?>"><?php echo $combocompany['label']?></option>
			       		<?php }?>
			       	</select>
			       	<input type="submit" value="Request ETA" class="btn btn-primary"/>
 			       	</form>
				  </div>
			  </div>
			 
			    <table class="table table-bordered">
			    	<tr>
			    		<th width="170">Item Code</th>
			    		<th width="200">Item Name</th>
			    		<th width="200">Company</th>
			    		<th width="60">Due Qty.</th>
			    		<th width="50">Unit</th>
			    		<th width="75">ETA</th>
			    		<th width="190">Cost Code</th>
			    		<th>Notes</th>
			    	</tr>
			    	<?php 
			    		
			    		foreach($backtrack['items'] as $item)
			    		{
			    	?>
			    	<tr>
			    		<td><?php echo $item->itemcode;?></td>
			    		<td><?php echo $item->itemname;?></td>
			    		<td><?php echo $item->companyname;?></td>
			    		<td><?php echo $item->duequantity;?></td>
			    		<td><?php echo $item->unit;?></td>
			    		<td><?php echo $item->daterequested;?></td>
			    		<td><?php echo $item->costcode;?></td>
			    		<td><?php echo $item->notes;?></td>
			    	</tr>
			    	<?php }?>
		      </table>
		      <br/>
		      
			    <?php 
			    	if(@$backtrack['messages'])
			    	foreach($backtrack['messages'] as $c)
			    	if(@$c['messages'])
			    	{
			    ?>
			    <strong>Messages for <?php echo $c['companyname']?> regarding PO# <?php echo $backtrack['quote']->ponum;?></strong>
			    <table class="table table-bordered" >
				    <tr>
				    	<th>From</th>
				    	<th>To</th>
				    	<th>Message</th>
				    	<th>Date/Time</th>
                        <th>&nbsp;</th>
				    </tr>
				    <?php
				    	
				    	foreach($c['messages'] as $msg)
				    	{
				    ?>
				    <tr>
				    	<td><?php echo $msg->from;?></td>
				    	<td><?php echo $msg->to;?></td>
				    	<td><?php echo $msg->message;?></td>
				    	<td><?php echo $msg->senton;?></td>
                        <td>&nbsp;
                            <?php if($msg->user_attachment!=''){?>
                            <a href="<?php echo site_url('uploads/messages').'/'.$msg->user_attachment;?>" target="_blank" title="View Attachment"><?php echo 'View Attachment';?></a>
                                 <?php }?>

                        </td>
				    </tr>
				    <?php
				    	}
				    ?>
			    </table>
			    <?php 
			    	}
			    ?>
			    <br/>
	<div class=" well">
	<form class="form-horizontal" method="post" enctype="multipart/form-data" action="<?php echo site_url('admin/message/sendmessage/'.$backtrack['quote']->id.'/backtrack')?>" onsubmit="this.to.value=this.company.options[this.company.selectedIndex].innerHTML">
    <input type="hidden" name="quote" value="<?php echo $backtrack['quote']->id;?>"/>
	<input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname')?> (Admin)"/>
	<input type="hidden" name="to" value=""/>
	<input type="hidden" name="ponum" value="<?php echo $backtrack['quote']->ponum;?>"/>
				    	
    <div class="control-group">
    <label class="control-label" for="company">Send Message To:</label>
    <div class="controls">
    <select name="company" required>
		<?php foreach($combocompanies as $combocompany){?>
		<option value="<?php echo $combocompany['value'];?>"><?php echo $combocompany['label']?></option>
		<?php }?>
	</select>
    </div>
    </div>
    
    <div class="control-group">
    <label class="control-label" for="message">Message</label>
    <div class="controls">
   		<textarea name="message" class="span8" rows="5" required></textarea>
    </div>
    </div>
    
     <div class="control-group">
    <label class="control-label" for="userfile">Attachment</label>
    <div class="controls">
   		 <input type="file" name="userfile" size="13" />
    </div>
    </div>
    
    
    <div class="control-group">
    <label class="control-label" for="">&nbsp;</label>
    <div class="controls">
   		 <input type="submit" value="Send" class="btn btn-primary"/>
    </div>
    </div>
    </form>
 </div>
		      <br/> <hr/>
	    	<?php }?>
	    </div>
    </div>
</section>