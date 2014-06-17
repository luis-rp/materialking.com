
    	<form method="post" action="<?php echo site_url('admin/itemcode/saveinventory');?>" enctype="multipart/form-data">
    		<input type="hidden" id="edititemid" name="itemid" value="<?php echo $item->id;?>"/>
    		<table class="table table-bordered">
    			<tr>
    				<td>Item Name</td>
    				<td><?php echo @$item->itemname;?></td>
    			</tr>
    			<tr>
    				<td>Item Notes</td>
    				<td><textarea id="edititemnotes" name="companynotes" style="width: 300px;"><?php echo @$item->companynotes;?></textarea></td>
    			</tr>
    			<tr>
    				<td>Attachment</td>
    				<td>
    				<input type="file" name="filename"/>
    				<?php if(@$item->filename){?>
    					<a href="<?php echo site_url('uploads/item/'.$item->filename);?>" target="_blank">View</a>
    				<?php }?>
    				</td>
    			</tr>
    			<tr>
    				<td></td>
    				<td><input type="submit" value="Save"></td>
    			</tr>
    		</table>
    	</form>