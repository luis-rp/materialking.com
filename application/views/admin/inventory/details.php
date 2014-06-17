<script src="<?php echo base_url(); ?>templates/admin/js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#itemcodedate').datepicker();
	$('#notes').autosize();
          getsubcat($('#catid').val(),'<?php echo $item->subcategory;?>');
//         $('#filesel').live('change', function()
//                {
//
//                    $("#uploadfrm").submit();
//                });
});

//function uploaded(data){
//    alert(data);
//}
function showhistory(companyid, itemid, companyname)
{
	var serviceurl = '<?php echo base_url()?>admin/inventory/gethistory/';
	//alert(serviceurl);
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: "companyid="+companyid+"&itemid="+itemid
	    }).done(function(data){
	        $("#pricehistory").html(data);
	        $("#historycompanyname").html(companyname);
	        $("#historymodal").modal();
	    });
}
function searchprice(keyword)
{
	var serviceurl = '<?php echo base_url()?>admin/itemcode/amazon';
	//alert(serviceurl);
    $("#searchmodal").modal();
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: "keyword="+keyword
	    }).done(function(data){
	        $("#minpricesearch").html(data);
	    });
}

function openamazon(keyword)
{
	keyword = encodeURIComponent(keyword);
	var url = 'http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords='+keyword+'&rh=i%3Aaps%2Ck%3A1%22+x+3%2F4%22+copper+reducer';
	
	window.open(url,'amazonlookup','width=1200,height=800,menubar=no,scrollbars=yes');
}

function getsubcat(catid,subid){
    var serviceurl = '<?php echo base_url()?>admin/itemcode/getsubcat';
	//alert(serviceurl);
	$.ajax({
	      type:"post",
	      url: serviceurl,
	      data: "catid="+catid+"&subid="+subid
	    }).done(function(data){
               
	        $("#subcatid").html(data);
	        
	    });
}

//function f1(){
//$('#filesel').click();
//}
//-->
</script>


<section class="row-fluid">
	<h3 class="box-header">Item Details - <?php echo $item->companyitem->itemcode;?></h3>
	<div class="box">
        <div class="span12">
	
		  <?php echo @$message; ?>
   		  <?php echo $this->session->flashdata('message'); ?>
		   <a class="btn btn-green" href="<?php echo site_url('admin/inventory');?>">&lt;&lt; Back</a>
		   <br/><br/>
		   <div  style="width:48%; float:left;">
		    <?php 
		    	if(@$item->poitems)
		    	{
		    ?>
		    	<h4>Purchased On:</h4>
		    	<table class="table table-bordered">
		    		<tr>
		    			<th>PO#</th>
		    			<th>Company</th>
		    			<th>Price</th>
		    			<th>Quantity</th>
		    			<th>Date</th>
		    		</tr>
		    <?php 
			    	foreach($item->poitems as $poitem)
			    	{
			?>
					<tr>
						<td><?php echo $poitem->ponum;?></td>
						<td><?php echo $poitem->companyname;?></td>
						<td>$ <?php echo $poitem->ea;?></td>
						<td><?php echo $poitem->quantity;?></td>
						<td><?php echo $poitem->awardedon;?></td>
					</tr>
			<?php
			    	}
		    		
		    ?>
		    	</table>
		    <?php 
		    	}
		    ?>
		    
		  </div>
		  <div class="span5">
		  <strong>
		  <table class="table table-bordered span12">
			  <tr>
				  <?php if($item->lastquoted){echo '<td>Last quoted: '.$item->lastquoted.'</td>';}?>
				  <?php if(@$itempricetrend){echo '<td>Price Trend: '.$itempricetrend.'</td>';}?>
		  <?php 
		   if(@$item->minprices)
		   {
		  ?>
			  </tr>
		  </table>
		  <?php } ?>
		  <?php 
		  	$seconds = time() - strtotime($item->lastquoted);
		  	$days = $seconds/(3600 * 24);
		  	if($days > 30)
		  	echo "<b><font color='red'>Item has not been requoted within 30 days.</font></b>";
		  ?>
		  </strong>
		  <h3 class="box-header"><i class="icon-ok"></i>Company Prices for <?php echo $item->companyitem->itemcode;?></h3>
		  		<?php if($item->keyword){?>
		  		<a class="btn btn-primary" onclick="searchprice('<?php echo $item->keyword;?>')">Amazon Lookup</a>
		  		<a class="btn btn-primary" onclick="openamazon('<?php echo $item->keyword;?>')">Search</a>
		  		<br/><br/>
		  		<?php }?>
				<table class="table table-bordered">
					<tr>
						<th>Company Name</th>
						<th>Date</th>
						<th>Purchase Price</th>
						<th>Substitute</th>
						<th>History</th>
					</tr>
			 	<?php //print_r($item->minprices);die;
				  	foreach($item->minprices as $m)
				  	{
			  	?>
					<tr>
						<td><?php echo $m->companyname;?></td>
						<td><?php echo $m->quoteon;?></td>
						<td>
							<div class="input-prepend input-append span6">
								$ <?php echo $m->price;?>
			    			</div>
						</td>
						<td><?php echo $m->substitute?'Substitute ['.$m->itemname.']':'-'?></td>
						<td>
							<a href="javascript: void(0);" onclick="showhistory('<?php echo $m->company?>','<?php echo $m->itemid?>','<?php echo $m->companyname?>')"><i class="icon icon-search"></i></a>
						</td>
					</tr>
				<?php } ?>
				</table>
			
		</div>
			
    
    	</div>
    </div>
</section>

        <div id="historymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h4>Price History - <span id="historycompanyname"></span></h4>
        	</div>
        	<div class="modal-body" id="pricehistory">
        	</div>
            
        </div>

        <div id="searchmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h4>Amazon Price Lookup</h4>
        	</div>
        	<div class="modal-body" id="minpricesearch">Loading prices...</div>
            
        </div>
