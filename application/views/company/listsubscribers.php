
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript">

$(document).ready(function(){
	$('.date').datepicker();
	

	
});

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
				<form id="invoiceform" method="post" action="<?php echo site_url('quote/invoice');?>">
                	<input type="hidden" id="invoicenum" name="invoicenum"/>
                </form>
                    <div class="col-md-12">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            
                            <div class="grid-body no-border">
                            
                                    <table id="datatable" class="table no-more-tables general">
                                    <thead>
                                       <tr>
                  	             			<th style="width:20%">Entry Id</th>
                                   			<th>Data</th>
                                   			<th>&nbsp;</th>
                                         </tr>
									</thead>	
									<tbody>                                    
							              <?php
							              
									    	foreach($subscribers as $key=>$sub)
									    	{
									    		?>
									      			
                                                		<tr>
                                                			<td class="v-align-middle"><?php echo $key;?> </td>
                                                			<td>
                                                			<?php foreach($sub as $fields){ 
                                                		
                                                				?>
                                                				<p><?php echo $fields["name"];?>  :  <?php echo $fields["value"];?></p>
                                                			<?php }?>
                                                			</td>
                                                			<td> <span class="icon-2x icon-edit"><input type="button" name="btnedit" id="btnedit" onclick="editData();"> </span> &nbsp;&nbsp; <span class="icon-2x icon-trash"><input type="button" name="btnDelete" id="btnDelete" onclick="deleteData();"></span></td>
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
			
		</div>
	  </div> 