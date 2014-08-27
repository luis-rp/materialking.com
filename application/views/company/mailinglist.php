
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
					   		<p><a href="<?php echo base_url("company/newtemplate");?>">New Template</a></p>        
							<p><a href="<?php echo base_url("company/listtemplates");?>">View Newsletter Templates</a></p>
							<p><a href="<?php echo base_url("company/listsubscribers");?>">View Newsletter Subscribers</a></p>
							</div>
						</div>
						</div>
			</div>
		</div>
	  </div> 