<script type="text/javascript">
$.noConflict();
 </script>

<?php echo '<script>var readnotifyurl="'.site_url('dashboard/readnotification').'";</script>'?>

<script>
function readnotification(id)
{
	$.ajax({
	      type:"post",
	      url: readnotifyurl,
	      data: "id="+id
	    }).done(function(data){
	        //alert(data);
	    });
	return true;
}
</script>
    <div class="content">  
     <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Dashboard  <a class="btn btn-primary btn-xs btn-mini" href="<?php echo base_url("company/addAd");?>">Add Ad</a>	</h3>	
		</div>
		
	   <div id="container">
	    
		<div class="row1">
			 <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            <div class="grid-body no-border">
                                 <?php if(isset($ads) && count($ads) > 0) { ?>
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:40%">Title</th>
                                                <!--<th style="width:30%">Desc</th>-->
                                                <th style="width:30%">Price</th>
                                                <th style="width:15%">Edit</th>
                                                <th style="width:15%">Delete</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php $i = 0;
							             
									    	foreach($ads as $ad)
									    	{
									    		$i++;
									      ?>
                                            <tr>
                                                <td><a href="<?php echo base_url("site/ad/".$ad->id);?>"><?php echo $ad->title;?></a></td>
                                               <!-- <td><?php //echo $ad->description;?></td>-->
                                                <td><?php echo $ad->price.' '.$ad->priceunit ;?></td>
                                                <td><a href="<?php echo base_url("company/updatead/".$ad->id);?>">Edit</a></td>
                                                <td><a class="close" href="<?php echo base_url("company/deletead/".$ad->id);?>" onclick="return confirm('Are you really want to delete this field?');">&times;</a></td>
                                            </tr>
                                          <?php }  ?>
                                        </tbody>
                                    </table>
                                    <?php } else { ?>
                         <br><div class="alert alert-error"><a data-dismiss="alert" class="close" href="#"></a><div class="msgBox">No Live Classified Ads</div></div>
                                    <?php }  ?>
                                    <br/>
                            </div>
                        </div>
			
		</div>
		
	</div>
</div>
		