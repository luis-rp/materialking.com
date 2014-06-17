<script type="text/javascript" charset="utf-8">
$(document).ready(function(){
   $('#dealdate').datepicker();
});
</script>

<script type="text/javascript">
CKEDITOR.replace('dealnote', {
	toolbar: [
		{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Link', 'Unlink', 'Anchor','Font','FontSize', 'Image','Table','HorizontalRule'] }
	]
});
</script>

    	<form method="post" action="<?php echo site_url('inventory/savedeal');?>" enctype="multipart/form-data">
    		<input type="hidden" id="edititemid" name="itemid" value="<?php echo $item->itemid;?>"/>
          
              <h4 class="semi-bold" id="myModalLabel">
              Setup Deal for: <?php echo @$item->itemname;?>
              </h4>
              
    		<table class="table table-bordered">
    			<tr>
    			
    				<td colspan="4">
    				<table border="0" cellpadding="0" cellspacing="0">
    				<tr>
    				<td>Deal Price</td>
    				<td><input type="text" name="dealprice" value="<?php echo @$item->dealprice;?>"/></td>
    				<td>Date Ending</td>
    				<td><input style="width:100px" type="text" id="dealdate" name="dealdate" value="<?php if(@$item->dealdate) echo date("m/d/Y", strtotime($item->dealdate));?>" data-date-format="mm/dd/yyyy"/></td>
    				
    				</td>
    				</tr>
    				</table>
    			</tr>
    			
    			<tr>
    				<td colspan="4">
    				<table border="0" cellpadding="0" cellspacing="0">
    				<tr>
    				<td>Quantity Required</td>
    				<td><input style="width:60px" type="text" name="qtyreqd" value="<?php echo @$item->qtyreqd;?>"/>
    				</td>
    				<td>Quantity Available</td>
    				<td><input style="width:100px" type="text" name="qtyavailable" value="<?php echo @$item->qtyavailable;?>"/></td>
    				</td>
    				</tr>
    				</table>
    			</tr>
    			
    			
    			<tr>
    				<td colspan="2">
    				Deal Note:<br/>
    				<textarea name="dealnote" cols="40"><?php echo @$item->dealnote;?></textarea></td>
    			</tr>
    			
    			<tr>
    				<td>Image</td>
    				<td>
    					<input type="file" name="image"/>
    					<?php if(@$item->image){?>
    					<br/>
    					<img src="<?php echo site_url('uploads/item/'.$item->image);?>" width="50" height="50"/>
    					<?php }?>
    				</td>
    			</tr>
    			<tr>
    				<td>Attachment</td>
    				<td>
    					<input type="file" name="filename"/>
    					<?php if(@$item->filename){?>
    					<br/>
    					<a href="<?php echo site_url('uploads/item/'.$item->filename);?>" target="_blank">View</a>
    					<?php }?>
    				</td>
    			</tr>
    			<tr>
    				<td>Member Only</td>
    				<td><input type="checkbox" name="memberonly" value="1" <?php echo @$item->memberonly=='1'?'checked="CHECKED"':'';?>"/></td>
    			</tr>
    			<tr>
    				<td>Active?</td>
    				<td><input type="checkbox" name="dealactive" value="1" <?php echo @$item->dealactive=='1'?'checked="CHECKED"':'';?>"/></td>
    			</tr>
    			<tr>
    				<td></td>
    				<td><input type="submit" value="Save" class="btn btn-primary"></td>
    			</tr>
    		</table>
    	</form>