<div class="content">
  <div class="container">
  	<div class="row">
  	  <div class="col-md-12">
         <div class="grid simple ">
            <div class="grid-title no-border">
               <?php  if(isset($message)) echo $message; else echo ''; ?>
            <h4>Created Form </h4>
            </div>

                  <div class="grid-body no-border">
                   <div class="row">
            		<form  class="animated fadeIn" role="form" method="post" action="saveformdata">
                     <div class="col-md-6 col-sm-6 col-xs-6">
 					<?php  foreach($result as $key=>$fields) { $name_id=trim($fields->Label);  ?>
 					<div class="form-group">
    						<label for="label" class="form-label"><?php echo $fields->Label ?></label>
    							<div class="controls">
      					<?php if($fields->FieldType == 'text' || $fields->FieldType == 'email' || $fields->FieldType == 'password') {?>		<input type="<?php echo $fields->FieldType ?>" class="form-control" id="<?php echo $name_id; ?>" name="formfields[<?php echo $fields->Id ?>]" placeholder="<?php echo $fields->Label; ?>" required value="<?php echo $fields->Value;?>">
      					<?php  }  ?>

      					<?php if($fields->FieldType == 'dropdown') { $dropdownValues = explode(",",$fields->FieldValue); $k= array_search($fields->Value,$dropdownValues); ?> <select id="<?php echo $name_id; ?>" name="formfields[<?php echo $fields->Id ?>]"><?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?><option value="<?php echo $dropdownValues[$i];?>" <?php if($dropdownValues[$i]==$fields->Value) { echo " selected ";} else { echo " "; } ?>><?php echo $dropdownValues[$i];?></option> <?php  } } ?></select>

    							<?php   } ?>
						<?php if($fields->FieldType == 'radio') { $dropdownValues = explode(",",$fields->FieldValue); ?> <?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?><input type="radio" name="formfields[<?php echo $fields->Id ?>]" id="<?php echo $dropdownValues[$i];?>" value="<?php echo $dropdownValues[$i];?>" <?php if($fields->Value ==$dropdownValues[$i]) echo 'checked'; ?>><?php echo $dropdownValues[$i];?> <?php  } } ?>

 					    		<?php  } ?>
 					    <?php if($fields->FieldType == 'checkbox') { $dropdownValues = explode(",",$fields->FieldValue); ?> <?php if(count($dropdownValues) > 0) { for($i=0;$i<count($dropdownValues); $i++) { ?><input type="checkbox" name="formfields[<?php echo $fields->Id ?>]" id="<?php echo $name_id; ?>"  value="<?php echo $dropdownValues[$i];?>" <?php if($fields->Value ==$dropdownValues[$i]) echo 'checked'; ?>><?php echo $dropdownValues[$i];?><?php  } } ?>

 					    		<?php } ?>
 					    <?php if($fields->FieldType == 'textarea') { ?> <textarea id="<?php echo $name_id;?>" name="formfields[<?php echo $fields->Id ?>]"><?php echo $fields->Value;?></textarea>
 					    		<?php  } } ?>
 					    	</div>
 					    </div>

 					    <div class="form-group">
	                        <label class="form-label"></label>
	                        <div class="controls">
	                          <input type="submit" value="Save Field" class="btn btn-primary btn-cons general">
	                        </div>
	                    </div>
 						 </div>
					</form>
                 </div>
              </div>
          </div>
  		</div>
	</div>