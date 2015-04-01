<script type="text/javascript">
$.noConflict();
 </script>

<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript">

$(document).ready(function(){
	$('.date').datepicker();
});

function editData(val)
{
	$("#subscribersID").val(val);
	$(".field1_"+val).css('display','none');
	$(".field_"+val).css('display','block');
	$(".fieldCls_"+val).removeAttr('disabled');
	$("#btnSave_"+val).css('display','');
}

function deleteData(val)
{
	$("#subscribersID").val(val);
	$("#frmlistsubscribers").attr('action','<?php echo site_url('company/deletesubscribersdata');?>');
	$("#frmlistsubscribers").submit();
}
</script>

    <div class="content">  
    	 <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
		 
			<h3>Mailing List</h3>		
		</div>
	
	   <div id="container">
	   		        
		<?php 
		    	if(@$subscribers)
		    	{
		    ?>
<div class="row">
	<form id="frmlistsubscribers" method="post" action="<?php echo site_url('company/savelistsubscribers');?>">
    	<input type="hidden" id="subscribersID" name="subscribersID" value=""/>
   
    <div class="col-md-12">
        <div class="grid simple ">
            <div class="grid-title no-border">
                <h4>&nbsp;</h4>
            </div>
            
            <div class="grid-body no-border">
            
                    <table id="datatable" class="table no-more-tables general">
                    <thead>
                       <tr>
  	             			<th style="width:10%">Entry Id</th>
                   			<th style="width:60%">Data</th>
                   			<th style="width:30%">Actions</th>
                         </tr>
					</thead>	
					<tbody>                                    
	              <?php
	             // echo '<pre>',print_r($subscribers);
	              
			    	foreach($subscribers as $key=>$sub)
			    	{
			    		?>
                        		<tr>
                        			<td class="v-align-middle"><?php echo $key;?> </td>
                        			<td>
                        			<?php foreach($sub as $fields){ 
                        		
                        				?>
                        				<p class="field1_<?php echo $key;?>"><?php echo $fields["name"];?>  :  <?php echo $fields["value"];?></p>
                        				<p class="field_<?php echo $key;?>" style="display:none;">
                        				<input type="text" class="fieldCls_<?php echo $key;?>" disabled name="fieldName[<?php echo $key;?>][<?php echo $fields['id'];?>]" value="<?php echo $fields["name"];?>">	: 
                        				<input type="text" class="fieldCls_<?php echo $key;?>" disabled name="fieldValue[<?php echo $key;?>][<?php echo $fields['id'];?>]" value="<?php echo $fields["value"];?>"></p>
                        			<?php }?>
                        			</td>
                        			<td> 
                        			<span class="icon-2x">
                        				<input type="button" name="btnedit" id="btnedit" onclick="editData(<?php echo $key;?>);" value="Edit" class="btn btn-primary"> 
                        			</span> &nbsp;&nbsp; 
                        			<span class="icon-2x">
                        				<input type="button" name="btnDelete" id="btnDelete" onclick="deleteData(<?php echo $key;?>);" value="Delete" class="btn btn-danger">
                        			</span> &nbsp;&nbsp; 
                        			<span class="icon-2x">
                        				<input type="submit" name="btnSave" id="btnSave_<?php echo $key;?>" value="Save" style="display:none;" class="btn btn-primary">
                        			</span>
                        			</td>
                        		</tr>
                        		
             <?php } ?>
                        </tbody>
                    </table>
            </div>
        </div>
    </div>
        </div>
        
        <?php } else {?>
				<span style="display: block;position:absolute;z-index:9999;margin-top:10px; margin-left:30px;" class="label label-important">No Subscribers.</span>
        <?php }?>
	 </form>
</div>
</div> 