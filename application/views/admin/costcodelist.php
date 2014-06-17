
	<style type="text/css">
		.box { padding-bottom: 0; }
		.box > p { margin-bottom: 20px; }

		#popovers li, #tooltips li {
			display: block;
			float: left;
			list-style: none;
			margin-right: 20px;
		}
		
		.adminflare > div { margin-bottom: 20px; }
	</style>
	

<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/bootstrap-slider.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/slider.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<?php echo '<script type="text/javascript">var updateprogressurl = "'.site_url('admin/costcode/updateprogress/').'";</script>';?>

<script>
$(document).ready(function(){
	$('.slider').slider({value:0});
	$(".slider").on('slideStop', function(slideEvt) {
		if(confirm('Do you want to change the value?'))
		{
			id=this.id;
			id=id.replace('progress','');
			v=slideEvt.value;
			d = "id="+id+"&manualprogress="+v;
			//alert(d);
			$.ajax({
			      type:"post",
			      data: d,
			      url: updateprogressurl
			    }).done(function(data){
				   $("#progresslabel"+id).html(v+'%');
				   var b = $("#budget"+id).val().replace('%','');
				   $("#progress"+id +" .tooltip-inner").text(v);
				   
				   if(b<=v)
					   $("#status"+id).html("<img src='<?php echo site_url('templates/admin/images/ok.gif');?>'/>");
				   else
					   $("#status"+id).html("<img src='<?php echo site_url('templates/admin/images/bad.png');?>'/>");
			    });
		}
		else
		{
			var v = $("#progresslabel"+id).html().replace('%','');
			//$("#progress"+id).attr('data-slider-value',v);
			$("#progress"+id).val(v);
			$("#progress"+id +" .tooltip-inner").text(v);
		}
		return false;
	});
		
});
function setprogress(id)
{
	
}
</script>


<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    
		<div style="margin-bottom:20px;">
                <div>
                	<?php echo $addlink;?>
                	<br/><br/>
	                <div class="datagrid-header-right">
						<form class="form-inline" action="<?php echo site_url('admin/costcode');?>" method="post">
							Filter by parent: 
							<select name="parentfilter" onchange="this.form.submit()">
								<option value="">View All</option>
								<?php echo $parentcombooptions;?>
							</select>
							Filter by Project: 
							<select name="projectfilter" onchange="this.form.submit()">
								<option value="">View All</option>
								<?php foreach($projects as $p){?>
						      	<option value="<?php echo $p->id;?>" <?php if($p->id==@$_POST['projectfilter']){echo 'SELECTED';}?>>
						      		<?php echo $p->title;?>
						      	</option>
						      	<?php }?>
							</select>
						</form>
					</div>
                </div>
            
            <table id="datatable" class="table table-bordered datagrid">
              <tr>
              	<th width="20%">Code</th>
              	<th width="7%">Budget</th>
              	<th width="9%">$ Spent</th>
              	<th width="25%">Budget % Allocated</th>
              	<th>Task Progress % Complete</th>
              	<th>Status</th>
              	<th width="10%">Actions</th>
              </tr>
              <?php foreach($items as $item){?>
              <input type="hidden" id="budget<?php echo $item->id;?>" value="<?php echo $item->budgetper;?>"/>
              <tr>
              	<td><?php echo $item->code?></td>
              	<td><?php echo $item->cost?></td>
              	<td><?php echo $item->totalspent?></td>
              	<td id=""><?php echo $item->budget?></td>
              	<td id="progress<?php echo $item->id;?>"><?php echo $item->manualprogressbar?></td>
              	<td id="status<?php echo $item->id;?>"><?php echo $item->status?></td>
              	<td><?php echo $item->actions?></td>
              </tr>
              <?php }?>
            </table>
            <?php if(!$items){?>
            No Costcodes Found.
            <?php }?>
           </div>

      </div>
    </div>
</section>