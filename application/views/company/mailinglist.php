
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript">

$(document).ready(function(){
	$('.date').datepicker();
	

	
});

</script>

    <div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
		 
			<h3>Mailing List</h3>		
		</div>
	
	   <div id="container">
	  		 <div class="row">
	  		         <div class="col-md-12">
                        <div class="grid simple ">
                        	<div class="grid-body no-border">
   <a href="<?php echo base_url("company/newtemplate");?>"><button type="button" class="btn btn-primary">New Template</button></a><br><br>
   <a href="<?php echo base_url("company/createformsubscriptions");?>"><button type="button" class="btn btn-primary">Create/Edit Form Subscriptions</button></a><br><br>
   <a href="<?php echo base_url("company/createformsubscriptions");?>"><button type="button" class="btn btn-primary">View Your Newsletter Templates</button></a><br><br>
   <!--<p><a href="<?php //echo base_url("company/listpretemplates");?>">View Predefined Templates</a></p>-->
   <a href="<?php echo base_url("company/listsubscribers");?>"><button type="button" class="btn btn-primary">View Newsletter Subscribers</button></a>
							</div>
						</div>
						</div>
			</div>
		</div>
	  </div> 
