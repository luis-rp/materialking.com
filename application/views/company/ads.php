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
			<h3>Dashboard </h3>	<a class="btn btn-primary btn-xs btn-mini" href="<?php echo base_url("company/addAd");?>">Add Ad</a>	
		</div>
		
	   <div id="container">
	    
		<div class="row">
			 <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                            </div>
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <th style="width:20%">Title</th>
                                                <th style="width:30%">Desc</th>
                                                <th style="width:30%">Price</th>
                                                
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
							              <?php
									    	$i = 0;
									    	foreach($ads as $ad)
									    	{
									    		$i++;
									      ?>
                                            <tr>
                                                <td><a href="<?php echo base_url("classified/ad/".$ad->id);?>"><?php echo $ad->title;?></a></td>
                                                <td><?php echo $ad->description;?></td>
                                                <td><?php echo $ad->price;?></td>
                                                
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    <br/>
                            </div>
                        </div>
			
		</div>
		
	</div>
</div>
		