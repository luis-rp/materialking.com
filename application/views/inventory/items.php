<?php //echo '<pre>'; print_r($items);die;?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>
<?php echo '<script>var itemupdateurl="'.site_url('admin/inventory/updateitem').'";</script>'?>
<script type="text/javascript" charset="utf-8">
	$(document).ready( function() {
    	$('#datatable').dataTable( {
    		"aaSorting": [],
    		"sPaginationType": "full_numbers",
    		"aoColumns": [
            		null,
            		null,
            		null,
            		{ "bSortable": false},
            		{ "bSortable": false},
            		{ "bSortable": false},
            		{ "bSortable": false},
            		{ "bSortable": false}
    		
    			]
    		} );
   	 $('.dataTables_length').hide();
	});
    function updateItemcode(itemid,itemcode)
    {
        var data = "itemid="+itemid+"&itemcode="+itemcode;
        
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateItemname(itemid,itemname)
    {
        var data = "itemid="+itemid+"&itemname="+itemname;
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateItemprice(itemid,itemprice)
    {
        var data = "itemid="+itemid+"&ea="+itemprice;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateIteminstock(itemid,instock)
    {
    	instock = instock==true?1:0;
        var data = "itemid="+itemid+"&instock="+instock;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
</script>
<section class="row-fluid">
	<h3 class="box-header">Inventory</h3>
	<div class="box">
	<div class="span12">
	
	<?php echo $this->session->flashdata('message'); ?>

   	<?php if($items){?>
   	<table id="datatable" class="table no-more-tables general">
		<thead>
			<tr>
				<th style="width:20%">Item Code</th>
				<th style="width:25%">Item Name</th>
				<th style="width:10%">Price</th>
				<th style="width:15%"><font color="#0AA699">Item Code</font></th>
				<th style="width:15%"><font color="#0AA699">Item Name</font></th>
				<th style="width:10%"><font color="#0AA699">Price</font></th>
				<th style="width:10%"><font color="#0AA699">Stock</font></th>
                <th style="width:5%"><font color="#0AA699">Details</font></th>
			</tr>
		</thead>
		
		<tbody>
		  <?php
			$i = 0;
			foreach($items as $item)
			{
				$i++;
		  ?>
			<tr>
				<td class="v-align-middle"><?php echo $item->itemcode;?></td>
				<td class="v-align-middle"><?php echo $item->itemname;?></td>
				<td class="v-align-middle">$<?php echo $item->ea;?></td>
			  
				<td class="v-align-middle">
					<input type="text"
					value="<?php echo @$item->companyitem->itemcode?>"
					onchange="updateItemcode('<?php echo $item->id?>',this.value);"/>
				</td>
				<td class="v-align-middle">
					<input type="text"
					value="<?php echo @$item->companyitem->itemname?>"
					onchange="updateItemname('<?php echo $item->id?>',this.value);"/>
				</td>
				<td class="v-align-middle">
					<input type="text" size="10"
					value="<?php echo @$item->companyitem->ea?>"
					onchange="updateItemprice('<?php echo $item->id?>',this.value);"/>
				</td>
				<td class="v-align-middle">
					<input type="checkbox"
					<?php echo @$item->companyitem->instock?'checked="CHECKED"':''?>"
					onchange="updateIteminstock('<?php echo $item->id?>',this.checked);"/>
				</td>
                <td class="v-align-middle">
                <?php if(@$item->companyitem){?>
                	<a href="<?php echo site_url('admin/inventory/details/'.$item->id);?>">
                		<i class="icon icon-search"></i>
                	</a>
                <?php }?>
                </td>
			</tr>
		  <?php } ?>
		</tbody>
	</table>
   	<?php }?>
    
    </div>
    </div>
</section>
