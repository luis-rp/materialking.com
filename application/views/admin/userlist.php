 <script type="text/javascript">
 $(document).ready(function(){
 tour1 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for User Overview"
	  },
	 
   	  
	]
	});

	// Initialize the tour
	tour1.init();

	// Start the tour
	tour1.start();
 });
 </script>
<?php echo $this->session->flashdata('message'); ?>

<section class="row-fluid">
	<h3 class="box-header" style="display:inline;" id="step1">
	    <?php echo $heading;?> 
	</h3>
		<div class="box">
		
			<div class="span15">
			<?php echo $addlink; ?>
			<br/><br/>
				<?php echo $table; ?>
			</div>
		</div>
	<div class="pagination"><?php echo $pagination; ?></div>
			
</section>
