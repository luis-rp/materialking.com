
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

	<style type="text/css">
		.box { padding-bottom: 0; }
		.box > p { margin-bottom: 20px; }

		#popovers li, #tooltips li {
			display: block;
			float: left;
			list-style: none;
			margin-right: 20px;
		}
		
		.adminflare > div { margin-bottom: 20px; }
	</style>
<script type="text/javascript">
<!--
$(document).ready(function(){
	
});
</script>
<script>

	function viewitems(quoteid)
	{
		var serviceurl = '<?php echo base_url()?>admin/quote/getitemsajax/';
		//alert(serviceurl);
		$.ajax({
		      type:"post",
		      url: serviceurl,
		      data: "quote="+quoteid
		    }).done(function(data){
		        $("#quoteitems").html(data);
		        $("#itemsmodal").modal();
		    });
	}
</script>	
	
<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	  <div class="span12">
	   <?php echo $this->session->flashdata('message'); ?>
	   <?php if(!$items){echo 'No Orders Currently Exist With User Permissions.';}else{?> 
	   
            <table class="table table-bordered col-lg-10">
             <thead>
              <tr>
                <th>ID</th>
                <th width="175px">PO#</th>
                <th>Purchase Type</th>
                <th>PO Date</th>
                <th>Status</th>
                <th width="150px">Actions</th>
                <th width="45">Received</th>
                <th width="45">Sent</th>
              </tr>
              <?php
              	foreach($items as $item)
              	{
              ?>
              <tr>
                <td><?php echo $item->id;?></td>
                <td><?php echo $item->ponum;?></td>
                <td><?php echo $item->potype;?></td>
                <td><?php echo $item->podate;?></td>
                <td><?php echo $item->status;?></td>
                <td><?php echo $item->actions;?></td>
                <td><?php echo $item->recived;?></td>
                <td><?php echo $item->sent;?></td>
              </tr>
              <?php 
              	}
              ?>
             </thead>
            </table>
            <div class="pagination pagination-mini"><ul><?php echo @$pagination; ?></ul></div>
            <?php }?>
           </div>
         </div>
         
         
</section>

