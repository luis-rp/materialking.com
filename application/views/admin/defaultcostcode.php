<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?> &nbsp;&nbsp; <a href="<?php echo base_url();?>admin/costcode" class="btn btn-green"> Back </a>
	 &nbsp;&nbsp;<?php echo @$addlink; ?> 
	</h3>
		<div class="box">
			<div class="span12">
				 <?php if(@$message){echo '<div class="alert alert-block alert-danger fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>'.$message.'</div>';}?>
				<?php echo $this->session->flashdata('message'); ?>
			    <div style="height:400px;overflow:auto;"> 
   				<table class="table table-striped">
	   				<thead>
	   					<tr>
		   					<th>Sr. No</th>
		   					<th>Cost Code</th>
		   					<th>cost</th>
		   					<th>Detail</th>
		   					<th>Image</th>
		   				</tr>
	   				</thead>
	   				<tbody>
	   					<?php $i=1;foreach($defaultcostcodesdata as $defaultcostcodes) { ?>
	   					<tr>
		   					<td><?php echo $i; ?></td>
	   						<td><?php echo $defaultcostcodes->code; ?></td>
		   					<td><?php echo $defaultcostcodes->cost; ?></td>
		   					<td><?php echo $defaultcostcodes->cdetail; ?></td>
		   					<td><?php  $imgName=""; if(isset($defaultcostcodes->costcode_image) && $defaultcostcodes->costcode_image != '' && file_exists('./uploads/costcodeimages/' . $defaultcostcodes->costcode_image)) { $imgName = site_url('uploads/costcodeimages/'.$defaultcostcodes->costcode_image);?>
		   						<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'>
		   					<?php } else { echo $defaultcostcodes->costcode_image; }?></td>
		   				</tr>
		   				<?php $i++;} ?>
	   				</tbody>  
   				</table>
   				</div>
    		</div>
    	</div>
</section>
