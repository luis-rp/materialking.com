

<link href='<?php echo base_url(); ?>templates/admin/css/fullcalendar.css' rel='stylesheet' />
<script src='<?php echo base_url(); ?>templates/admin/js/jquery-ui.custom.min.js'></script>
<script src='<?php echo base_url(); ?>templates/admin/js/fullcalendar.js'></script>
<script>

	$(document).ready(function() {
	
		$('#calendar').fullCalendar({
			editable: false,
			events: "<?php echo base_url(); ?>event/jsonlist",
			
			eventDrop: function(event, delta) {
				//alert(event.title + ' was moved ' + delta + ' days\n' + '(should probably update your database)');
			},
			
			loading: function(bool) {
				if (bool) $('#loading').show();
				else $('#loading').hide();
			}
			
		});
		
	});

</script>

<style>
	#loading {
		position: absolute;
		top: 5px;
		right: 5px;
		}

	#calendar {
		width: 100%;
		}

</style>

<div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Events &nbsp; &nbsp;<a href="<?php echo site_url('event/add');?>" class="btn btn-green">Add Event</a></h3>		
		</div>

<section class="row-fluid" id="eventbox">
	
	<?php if ($this->session->userdata('usertype_id') == 2) {?>
	<span>

	</span>
	<br/><br/>
	<?php }?>
	<div class="box">
    	<div class="span12">
    	
    		<div id='loading' style='display:none'>Loading...</div>
    		<div id='calendar'></div>
    
    	</div>
    </div>
</section>
</div>