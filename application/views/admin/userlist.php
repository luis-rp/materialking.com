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


	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour1.restart();
			// Initialize the tour
			tour1.init();
			start();
		});

	$('#canceltour').live('click',endTour);

 });
 function start(){

	// Start the tour
		tour1.start();
	 }
 function endTour(){

	 $("#tourcontrols").remove();
	 tour1.end();
		}
 </script>
<?php echo $this->session->flashdata('message'); ?>
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>
<section class="row-fluid">
	<h3 class="box-header" style="display:inline;" id="step1">
	    <?php echo $heading;?>		<?php echo $addlink; ?>
	</h3>
		<div class="box">

			<div class="span15">
	
			<br/><br/>
				<?php echo $table; ?>
			</div>
		</div>
	<div class="pagination"><?php echo $pagination; ?></div>

</section>
