<?php if (isset($jsfile)) include $this->config->config['base_dir'] . 'templates/admin/gridfeed/' . $jsfile; ?>

<?php echo '<script>var updateminstockurl="'.site_url('admin/inventorymanagement/updateminstock').'";</script>'?>
<?php echo '<script>var updatemaxstockurl="'.site_url('admin/inventorymanagement/updatemaxstock').'";</script>'?>
<?php echo '<script>var updatereorderqtyurl="'.site_url('admin/inventorymanagement/updatereorderqty').'";</script>'?>
<?php echo '<script>var updateadjustedqtyurl="'.site_url('admin/inventorymanagement/updateadjustedqty').'";</script>'?>
<script type="text/javascript">

function reduceval(itemid){
	var value = parseInt($('#adjustqty'+itemid).val());	
	if(value > 0){
		value = value - 1;
		$('#save'+itemid).css('display','block');
	}	
	$('#adjustqty'+itemid).val(value);	
}
                 
function updateadjustedqty(itemid,oldqtyinhand){

	adjustedqty = oldqtyinhand - $('#adjustqty'+itemid).val();	
	if(confirm("Do you really want to reduce the quantity on hand by "+adjustedqty+"?")){
		
		var data = "itemid="+itemid+"&adjustedqty="+adjustedqty;        
        $.ajax({
		      type:"post",
		      data: data,
		      url: updateadjustedqtyurl
		    }).done(function(data){
			   $('#qtyonhand'+itemid).val($('#adjustqty'+itemid).val());	
			   alert('Quantity In Hand Value Modified ');
		    });		
		
	}else{
		$('#adjustqty'+itemid).val(oldqtyinhand);	
	}
	
	$('#save'+itemid).css('display','none');
}


function updateminstock(itemid,val)
    {    	
        var data = "itemid="+itemid+"&minstock="+val;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: updateminstockurl
		    }).done(function(data){
				//alert(data);
		    });
    }


function updatemaxstock(itemid,val)
    {    	
        var data = "itemid="+itemid+"&maxstock="+val;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: updatemaxstockurl
		    }).done(function(data){
				//alert(data);
		    });
    }


function updatereorderqty(itemid,qty)
    {    	
        var data = "itemid="+itemid+"&reorderqty="+qty;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: updatereorderqtyurl
		    }).done(function(data){
				//alert(data);
		    });
    }
        
    
</script>
<html>
<body>
<section class="row-fluid">
    <h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo $heading; ?></span>   
    &nbsp;&nbsp;
    <!--<a  href="javascript: void(0)" onclick="addserviceandlabortest()" class="btn btn-green"> Add service & labor items </a>-->
    </h3>
    <div class="box">
        <div class="span12">

            <?php echo $this->session->flashdata('message'); ?>

            <div class="datagrid-example">
                <div style="height:600px;width:100%;margin-bottom:20px;">
                    <table id="MyGrid" class="table table-bordered datagrid">
                        <thead>
                            <tr>
                                <th>
                        <div>
                           
                            <div class="datagrid-header-right">

								</form>
                            </div>
                        </div>
                        </th>
                        </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>
                        <div class="datagrid-footer-left" style="display:none;">
                            <div class="grid-controls">
                                <span>
                                    <span class="grid-start"></span> -
                                    <span class="grid-end"></span> of
                                    <span class="grid-count"></span>
                                </span>
                                <div class="select grid-pagesize" data-resize="auto">
                                    <button type="button" data-toggle="dropdown" class="btn dropdown-toggle">
                                        <span class="dropdown-label"></span>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li data-value="5"><a href="#">5</a></li>
                                        <li data-value="10" data-selected="true"><a href="#">10</a></li>
                                        <li data-value="20"><a href="#">20</a></li>
                                        <li data-value="50"><a href="#">50</a></li>
                                        <li data-value="100"><a href="#">100</a></li>
                                    </ul>
                                </div>
                                <span>Per Page</span>
                            </div>
                        </div>
                        <div class="datagrid-footer-right" style="display:none;">
                            <div class="grid-pager">
                                <button type="button" class="btn grid-prevpage"><i class="icon-chevron-left"></i></button>
                                <span>Page</span>

                                <div class="input-append dropdown combobox">
                                    <input class="span1" type="text">
                                    <?php if (0) { ?>
                                        <button type="button" class="btn" data-toggle="dropdown"><i class="caret"></i></button>
<?php } ?>
                                    <ul class="dropdown-menu"></ul>
                                </div>
                                <span>of <span class="grid-pages"></span></span>
                                <button type="button" class="btn grid-nextpage"><i class="icon-chevron-right"></i></button>
                            </div>
                           <!-- <span style="text-align:right;">
	                        <form method="post" action="<?php echo site_url('admin/inventorymanagement');?>">   
	                        <input type="hidden" name="loadoffset" value = "<?php echo $offset;?>">                    
	                        <input type="submit" name="btnloadnewitems" id="btnloadnewitems" value="Load next 100 Items" style="margin-left:10px;">
	                        </form>
	                        </span>-->
                        </div>
                        </th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>