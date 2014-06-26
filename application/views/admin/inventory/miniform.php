<script type="text/javascript">

$("#projectiddefault").click(function () {
        if ($("#projectiddefault").is(':checked')) {
            $(".projectgroup").prop("checked", false);
           
        } 
        
         if ($(".projectgroup").is(':checked')) {
        	alert("unchecked");
            $("#projectiddefault").prop("checked", false);
        }
    });
    
    
  $(".projectgroup").click(function () {
        if ($("#projectiddefault").is(':checked')) {
            $("#projectiddefault").prop("checked", false);
           
        }      
         
   });  

</script>
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
    			<?php
				$selectedprojectid = array();
    			if(@$item->projectid){
    					$selectedprojectid = explode(",",$item->projectid);
    			}else 
    					$selectedprojectid[] = '';
    			?>
    			<tr>
    				<td>Set note as <input type="checkbox" name="projectid[]" id="projectiddefault" value="-1" <?php if(in_array('-1',$selectedprojectid)) echo "checked='checked'";?> />&nbsp;default
    				<?php foreach($projectdata as $projectname){ ?>
    					&nbsp;&nbsp;<input type="checkbox" class="projectgroup" name="projectid[]" id="projectid[]" value="<?php echo $projectname->id?>" <?php if(in_array($projectname->id,$selectedprojectid)) echo "checked='checked'";?>  /> <?php echo $projectname->title; ?>
    				<?php }?>
    				</td>
    			</tr>
    			<tr>
    				<td></td>
    				<td><input type="submit" value="Save"></td>
    			</tr>
    		</table>
    	</form>