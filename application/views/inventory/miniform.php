<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>

<script type="text/javascript">
CKEDITOR.replace( 'companynotes', {
	toolbar: [
		{ name: 'document', items: [ 'Source', '-', 'NewPage', 'Preview', '-', 'Templates' ] },
		[ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ],
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Link', 'Unlink', 'Anchor', 'Styles','Format','Font','FontSize', 'Image','Table','HorizontalRule'] }
	]
});
</script>

    	<form method="post" action="<?php echo site_url('inventory/saveinventory');?>" enctype="multipart/form-data">
    		<input type="hidden" id="edititemid" name="itemid" value="<?php echo $item->id;?>"/>
          
              <h4 class="semi-bold" id="myModalLabel">
              Edit Item: <?php echo $item->itemname;?>
              </h4>
    		<table class="table table-bordered">
    			<tr>
    				<td>Item Description</td>
    				<td><textarea id="companynotes" name="companynotes" class="form-control ckeditor" style="width: 400px;height: 200px;"><?php echo @$item->companynotes;?></textarea></td>
    			</tr>
    			<tr>
    				<td>Picture</td>
    				<td>
    				<input type="file" name="image"/>
    				<?php if(@$item->image){?>
    					<a href="<?php echo site_url('uploads/item/'.$item->image);?>" target="_blank">View</a>
    				<?php }?>
    				<br/>*If no picture uploaded, default main store image will be shown.
    				</td>
    			</tr>
    			<tr>
    				<td>Attachment</td>
    				<td>
    				<input type="file" name="filename"/>
    				<?php if(@$item->filename){?>
    					<a href="<?php echo site_url('uploads/item/'.$item->filename);?>" target="_blank">View</a>
    				<?php }?>
    				<br/>*Add an attachment related to this item.
    				</td>
    			</tr>
    			<tr>
    				<td></td>
    				<td>
    					<input type="submit" value="Save" class="btn btn-primary">
    				</td>
    			</tr>
    		</table>
    	</form>