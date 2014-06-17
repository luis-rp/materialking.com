
<script>
$(document).ready(function(){
		
});
</script>


<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    
		<div style="margin-bottom:20px;">
			<div>
				<?php echo $addlink;?>
				<br/><br/>
			
				<table id="datatable" class="table table-bordered datagrid">
				  <tr>
					<th width="20%">Manufacturer</th>
					<th width="10%">Actions</th>
				  </tr>
				  <?php foreach($items as $item){?>
				  
				  <tr>
					<td><?php echo $item->title?></td>
					<td><?php echo $item->actions?></td>
				  </tr>
				  <?php }?>
				</table>
			</div>
      </div>
    </div>
</section>