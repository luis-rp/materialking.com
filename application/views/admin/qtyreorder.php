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
<?php echo '<script>var addpoquoteurl = "' . site_url('site/addpoquote') . '";</script>' ?>
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
                                                if(data.length<=0)
                                                $('#addpoid').css('display','block');
                                                else
                                                $('#addpoid').css('display','none');
                                                
                                   for(var i=0;i<data.length;i++){

                                                $(".qid").append('<option value="'+data[i].id+'">'+data[i].ponum+'</option>');
                                           }
                                   $(".quote").show();
                                   }
                               });      
                        
                    });
                    
                    $('#deliverydate').datepicker();
					$('#podate').datepicker();
					$('#duedate').datepicker();
                    
            });
            

    function addpo(){

    	var pid=$('.pid').val();
    	if(pid){
    		
    		$('#Addpomodal').modal();
    	}
    }        


       function savepo(){
    	
    	var pid=$('.pid').val();
    	var ponum = $("#ponum").val();
    	if(ponum=="")
    		alert("Please Enter PO");
    	
    	var podate = $("#podate").val();
		var duedate = $('#duedate').val();
		var deliverydate = $('#deliverydate').val();
		
		var d = "pid="+pid+"&ponum="+ponum+"&podate="+podate+"&duedate="+duedate+"&deliverydate="+deliverydate;
		
		$.ajax({
			type: "post",
			url: addpoquoteurl,
			dataType: 'json',
			data: d
		}).done(function(data) {			
			if(data=="Duplicate PO#"){
				alert(data);
			}else{		
					var option = new Option(data.ponum, data.poid);
					$('[name="qid"]').append($(option));
					$('[name="qid"]').val(data.poid);			
					$('#Addpomodal').modal('hide');
			}
		});
    } 
       
            
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
				
				<span style="display:none;" id="addpoid" ><a href="javascript:void(0)" onclick="addpo()">Add PO</a></span>
				<br><br>
				<input type="submit" value="Add Item to Quote">
				</form>
			</div>		
		</div>
	
		
	</div>
</section>



 <div id="Addpomodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

            <div class="modal-header">
        	<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            <h3>Please Add Your P.O. Now</h3>
        	</div>
        	<div class="modal-body">
        	
        	<div class="control-group">
			    <div class="controlss">PO # &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; 
                  <input type="text" id="ponum" name="ponum" style="width: 20%" class="input small" >		</div>
		    </div>
		    <br><br>		    
		    <div class="control-group">
			    <div class="controlss">
			      Delivery or Pick-Up Date: &nbsp; &nbsp;
			      <input type="text" id="deliverydate" name="deliverydate" class="input small span2" 
			      	data-date-format="mm/dd/yyyy">			      
			       &nbsp; &nbsp; <br><br>
			      PO Date: &nbsp; &nbsp; 
			      <input type="text" id="podate" name="podate" class="input small span2"
			      	data-date-format="mm/dd/yyyy">
			      	&nbsp; &nbsp; &nbsp; &nbsp; <br><br>
			     Bid Due Date: &nbsp; &nbsp; 
			      <input type="text" id="duedate" name="duedate" class="input small span2"
			      data-date-format="mm/dd/yyyy">
			      <input name="add" type="button" class="btn btn-primary" value="Save" onclick="savepo();"/>
			    </div>			   
		    </div>
        	
        	</div>

   </div>  
        
