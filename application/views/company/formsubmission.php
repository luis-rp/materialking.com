
<div class="content">
  <div class="container">
  	<div class="row">
  	  <div class="col-md-12">
         <div class="grid simple ">
            <div class="grid-title no-border"><h4>Form Submission Data</h4></div>
               <div class="grid-body no-border">
                  <div class="row">
                  <?php if(isset($formresult))?>
                  <div class="pull-right">
                  <form id="searchform" name="searchform" class="form form-inline" method="post" action="<?php echo site_url('company/formsubmission');?>">
                   <div class="form-group">
				      <label class="form-label">Select Company</label>
				         <div class="controls">
				             <select name="companyname" id="companyname">
    				              <?php $name = ""; foreach($formresult as $k=>$value)  { if($name!=$value['fromid']) {?>
                                   <option value='<?php echo $value['fromid'];?>'><?php echo $value['companyname'];?></option>
                                    <?php  $name = $value['fromid']; }  }?>
				              </select>
				          </div>
				     </div>

				    <div class="form-group">
				      <label class="form-label"></label>
				         <div class="controls">
				            <input type="submit" value="Search" class="btn btn-primary btn-cons general">
				         </div>
				     </div>
				   </form>
                  </div>

                  <table class="table table-condensed">
                    <?php $name = ""; foreach ($formresult as $k=>$value)  {  ?>

                    	 <?php if($name!=$value['fromid']) {?> <tr><td><h3><?php echo $value['companyname']; ?></h3></td>
                    	 <tr><td>Message</td><td><?php echo $value['message'] ?></td></tr>
                  		 <tr><td>Account Number</td><td><?php echo $value['accountnumber'] ?></td></tr>
                    	 <?php } ?>
                         <tr><td><?php echo $value['Label'] ?></td><td><?php echo $value['formValue'] ?></td></tr>
                  		 <?php $name = $value['fromid']; } ?>
				   </table>

                </div>
              </div>
           </div>
  		 </div>
	  </div>
  </div>
</div>