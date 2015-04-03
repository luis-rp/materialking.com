<html>
<head>
 <link href="<?php echo base_url(); ?>templates/admin/css/bootstrap.min.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/adminflare.min.css" media="all" rel="stylesheet" type="text/css" id="adminflare-css">
<script src="<?php echo base_url(); ?>templates/admin/js/jquery.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>templates/admin/js/adminflare.min.js" type="text/javascript"></script>

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
		
		var data = "itemid="+itemid+"&quantity="+adjustedqty;        
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
<section class="row-fluid" style="margin-top:-100px;">
    <h3 class="box-header" style="display:inline;width:50%;align:center;text-align:center;" ><span id="step1"><?php echo 'Inventory Adjustment'; ?></span>   
    &nbsp;&nbsp;   
    </h3>
    <div class="box" style="width:50%;align:center;" >
        <div class="span12">
			<div id="msg"></div>
            <?php // echo $this->session->flashdata('message'); ?>

			<?php if($items){ ?>		
            <table style="align:center;text-align:center;" class="table-bordered">
			    	
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
			    	<td><img style="max-height: 120px; padding: 0px;width:150px; height:150px;" src="<?php echo $imgName;?>"></td>
			    	</tr>
            
            		<tr>
			    		<td width="20%"><strong>Itemcode</strong></td>
			    	</tr>	
			    	
			    	<tr>
			    	<td><?php echo $item->itemcode; ?></td>
			    	</tr>
			    	
			    	<tr>
			    	<td width="20%"><strong>Itemname</strong></td>			    		
			    	</tr>	
			    	
			    	<tr>	
			    		<td><?php echo $item->itemname; ?></td>
			    	</tr>
			    	
			    	<tr>
			    	<td width="10%"><strong>Qty On Hand</strong></td>
			    	</tr>	
			    	
			    	<tr>	
			    		<td><?php echo $item->qtyonhand; ?></td>
			    	</tr>
			    	
			    	<tr>
			    		<td width="10%"><strong>Qty On PO</strong></td>
			    	</tr>
			    	
			    	<tr>
			    		<td><?php echo $item->qtyonpo; ?></span></td>
			    	</tr>
			    	
			    	<tr>	
			    		<td width="20%"><strong>Adjust Qty On Hand</strong></td>
			    	</tr>	
			    	
			    	<tr>
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