<?php echo $this->session->flashdata('message'); ?>

<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading;?> </h3>
			<div class="box">
			
				<div class="span12">
				<?php echo $addlink; ?>
			<br/><br/>
					<?php echo $table; ?>
				</div>
			</div>
			
	<div class="pagination"><?php echo $pagination; ?></div>
			
</section>
