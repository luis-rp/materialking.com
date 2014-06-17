<?php if(@$message){echo '<div class="alert alert-block alert-danger fade in"><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>'.$message.'</div>';}?>
<?php echo $this->session->flashdata('message'); ?>

<section class="row-fluid">
	<h3 class="box-header mainheading"><?php echo $heading;?> </h3>
	<div class="box">
		<div class="span12">
			<?php echo $addlink;?>
			<br/><br/>
			<table class="table table-bordered  col-lg-10">
				<thead>
					<tr>
						<th class="center" width="10%">SN.</th>
						<th>Event</th>
						<th>Category</th>
						<th class="center" width="20%">Actions</th>
					</tr>
				</thead>
				
				<tbody>
					
					<?php 
					$i=1;
					foreach($items as $item){?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $item->title;?></td>
						<td><?php echo $item->category;?></td>
						<td class="center">
							<a class="update" href="<?php echo site_url('admin/event/update/'.$item->id);?>">
								<button class="btn btn-primary btn-mini"><i class="icon-cogs"></i></button>
							</a>
							<a class="delete" onclick="return confirm('Are you sure want to Delete this Record?')" 
								  href="<?php echo site_url('admin/event/delete/'.$item->id);?>">
								<button class="btn btn-danger btn-mini"><i class="icon-trash"></i></button>
							</a>
							
						</td>
					</tr>
					<?php $i++; }?>
					
				</tbody>
				
			</table>
			
			<div class="pagination pagination-mini"><ul><?php echo @$pagination; ?></ul></div>
			
		</div>
	</div>
</section>
