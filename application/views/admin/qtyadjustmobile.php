<html>
<head>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>

<?php if (isset($jsfile)) include $this->config->config['base_dir'] . 'templates/admin/gridfeed/' . $jsfile; ?>

<?php echo '<script>var updateadjustedqtyurl="'.site_url('admin/inventorymanagement/updateadjustedqty').'";</script>'?>
<script type="text/javascript">

function reduceval(itemid){
	var value = parseInt($('#adjustqty'+itemid).val());	
	if(value > 0){
		value = value - 1;		
	}	
	$('#adjustqty'+itemid).val(value);	
}
                 
function updateadjustedqty(itemid,ea){
	$('#msg').html('');
	adjustedqty = $('#qtyonhand'+itemid).val() - $('#adjustqty'+itemid).val();	
	if(confirm("Do you really want to reduce the quantity on hand by "+adjustedqty+"?")){
		
		var data = "itemid="+itemid+"&adjustedqty="+adjustedqty;        
        $.ajax({
		      type:"post",
		      data: data,
		      url: updateadjustedqtyurl
		    }).done(function(data){
			   $('#qtyonhand'+itemid).val($('#adjustqty'+itemid).val());	
			   $('#valueonhand'+itemid).val($('#adjustqty'+itemid).val()*ea);
			   $('#msg').html('<div class="alert alert-success fade in" ><button event="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button>Quantity In Hand Value Modified</div>');
		    });		
		
	}else{
		$('#adjustqty'+itemid).val($('#qtyonhand'+itemid).val());	
	}	
	
}

</script>
</head>

<body>
<section class="row-fluid">
    <h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo 'Quantity Adjustment'; ?></span>   
    &nbsp;&nbsp;   
    </h3>
    <div class="box">
        <div class="span12">
			<div id="msg"></div>
            <?php // echo $this->session->flashdata('message'); ?>

			<?php if($items){ ?>		
            <table class="table table-bordered">
			    	<tr>
			    		<th width="20%">Itemcode</th>
			    		<th width="20%">Itemname</th>
			    		<th width="10%">Item Image</th>
			    		<th width="10%">Qty On Hand</th>
			    		<th width="10%">Qty On PO</th>
			    		<th width="20%">Adjust Qty On Hand</th>
			    	</tr>
			    	<?php foreach ($items as $item){ 
			    	
			    		if (@$item->item_img && file_exists('./uploads/item/' . @$item->item_img))
			    		{
			    			$imgName = site_url('uploads/item/'.$item->item_img);
			    		}
			    		else
			    		{
			    			$imgName = site_url('uploads/item/big.png');
			    		}			    		
			    	?>
			    	<tr>
			    		<td><?php echo $item->itemcode; ?></td>
			    		<td><?php echo $item->itemname; ?></td>
			    		<td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src="<?php echo $imgName;?>"></td>
			    		<td><?php echo $item->qtyonhand; ?></td>
			    		<td><?php echo $item->qtyonpo; ?></span></td>
			    		<td><?php echo $item->manage; ?></span></td>
			    	</tr>
			    	<?php } ?>
	    	</table>
            <?php }else echo $message; ?>
        </div>
    </div>
</section>
</body>
</html>