<?php if($this->session->userdata('managedprojectdetails')){?>

	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/app.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/sparkline/jquery.sparkline.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.tooltip.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.resize.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.time.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/flot/jquery.flot.pie.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/plugins/easy-pie-chart/jquery.easy-pie-chart.min.js"></script>
	
<?php }?>

<script type="text/javascript">
            $(document).ready(function() {                
               
                $(".pid").change(function(event){                   
                	
                        $.ajax("<?php echo base_url()?>admin/costcode/get_cc_by_project",{data:{projectfilter:$(this).val()},dataType:"json",type:"POST",success:function(data,textStatus){
                                                $(".ccid").empty();
                                   for(var i=0;i<data.length;i++){

                                                $(".ccid").append('<option value="'+data[i].id+'">'+data[i].code+'</option>');
                                           }
                                   $(".cost-code").show();
                                   }
                               });
                               
                               
                         $.ajax("<?php echo base_url()?>admin/inventorymanagement/get_quotes_by_project",{data:{projectfilter:$(this).val(),itemid:$('#hiddenitemid').val()},dataType:"json",type:"POST",success:function(data,textStatus){
                                                $(".qid").empty();
                                   for(var i=0;i<data.length;i++){

                                                $(".qid").append('<option value="'+data[i].id+'">'+data[i].ponum+'</option>');
                                           }
                                   $(".quote").show();
                                   }
                               });      
                        
                    });
            });
</script>
<section class="row-fluid">
			<h3 class="box-header">Reorder Item</h3>
	<div class="box">
		<div class="span12">
			<?php // echo @$message; ?>
			 <?php echo @$this->session->flashdata('message'); ?>
			<div class="well">				
				<form class="form-horizontal" action="<?php echo base_url()?>admin/inventorymanagement/additemtoquote" method="post" >
					<div class="control-group">
						<label for="inputEmail" class="control-label">
						<strong>Select Project to assign order to</strong>
						</label>
						<div class="controls">
							<select class="pid" name="pid">
								<option value="0">Select</option>
								<?php foreach($projects as $p){?>
								<option value="<?php echo $p->id;?>"><?php echo $p->title?></option>
								<?php }?>
							</select>
						</div>
					</div>
					
					<div class="control-group cost-code">
						<label for="inputEmail" class="control-label">
						<strong>Select Cost Code</strong>
						</label>
						<div class="controls">
							<select name="ccid" class="ccid" >							
							</select>
						</div>
					</div>			
					
					<div class="control-group quote">
						<label for="inputquote" class="control-label">
						<strong>Select Quote</strong>
						</label>
						<div class="controls">
							<select name="qid" class="qid" >							
							</select>
						</div>
					</div>						
								
				<input type="hidden" id="hiddenitemid" name="hiddenitemid" value="<?php echo @$itemid;?>">	
				<input type="hidden" id="hiddenreorderqty" name="hiddenreorderqty" value="<?php echo @$reorderqty;?>">
				<input type="submit" value="Add Item to Quote">
				</form>
			</div>		
		</div>
	
		
	</div>
</section>