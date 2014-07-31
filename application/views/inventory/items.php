<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript" src="<?php echo base_url();?>templates/front/js/ckeditor/ckeditor.js"></script>

<?php echo '<script>var tier1='.$tier->tier1.';</script>'?>

<?php echo '<script>var tier2='.$tier->tier2.';</script>'?>

<?php echo '<script>var tier3='.$tier->tier3.';</script>'?>

<?php echo '<script>var tier4='.$tier->tier4.';</script>'?>

<?php echo '<script>var itemupdateurl="'.site_url('inventory/updateitem').'";</script>'?>

<?php echo '<script>var itemcodeupdateurl="'.site_url('inventory/updateitemcode').'";</script>'?>

<?php echo '<script>var itemnameupdateurl="'.site_url('inventory/updateitemname').'";</script>'?>

<?php echo '<script>var partnumupdateurl="'.site_url('inventory/updatepartnum').'";</script>'?>

<?php echo '<script>var manufacturerupdateurl="'.site_url('inventory/updatemanufacturer').'";</script>'?>

<?php echo '<script>var itempriceupdateurl="'.site_url('inventory/updateitemprice').'";</script>'?>

<?php echo '<script>var minqtyupdateurl="'.site_url('inventory/updateminqty').'";</script>'?>

<?php echo '<script>var qtyavailableupdateurl="'.site_url('inventory/updateqtyavailable').'";</script>'?>

<?php echo '<script>var iteminstockupdateurl="'.site_url('inventory/updateiteminstock').'";</script>'?>

<?php echo '<script>var itemsurl="'.site_url('inventory/itemsjson').'";</script>'?>

<script type="text/javascript" charset="utf-8">
	$(document).ready( function() {
	});
    function updateItemcode(itemid,itemcode)
    {
        var data = "itemid="+itemid+"&itemcode="+itemcode;
        
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemcodeupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateItemname(itemid,itemname)
    {
        var data = "itemid="+itemid+"&itemname="+encodeURIComponent(itemname);
        
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemnameupdateurl
		    }).done(function(data){
		    	//alert(data);
		    });
    }
    function updatePartnum(itemid,partnum)
    {
        var data = "itemid="+itemid+"&partnum="+partnum;
        $.ajax({
		      type:"post",
		      data: data,
		      url: partnumupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateManufacturer(itemid,manufacturer)
    {
        var data = "itemid="+itemid+"&manufacturer="+manufacturer;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: manufacturerupdateurl
		    }).done(function(data){
		    	//alert(data);
		    });
    }
    function updateItemprice(itemid,itemprice)
    {
        var data = "itemid="+itemid+"&ea="+itemprice;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itempriceupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateMinqty(itemid,minqty)
    {
        var data = "itemid="+itemid+"&minqty="+minqty;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: minqtyupdateurl
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
		      url: iteminstockupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    
       
    function updateqtyavailable(itemid,qtyavailable)
    {
        var data = "itemid="+itemid+"&qtyavailable="+qtyavailable;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: qtyavailableupdateurl
		    }).done(function(data){
				
		    });
    }
    function updateItemisfeature(itemid,isfeature)
    {
    	isfeature = isfeature==true?1:0;
        var data = "itemid="+itemid+"&isfeature="+isfeature;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    function updateIteminstore(itemid,instore)
    {
    	instore = instore==true?1:0;
        var data = "itemid="+itemid+"&instore="+instore;
        //alert(data);
        $.ajax({
		      type:"post",
		      data: data,
		      url: itemupdateurl
		    }).done(function(data){
				//alert(data);
		    });
    }
    function viewPricelist(itemcode,itemname,price)
    {
    	$("#pricelist").modal();
    	$("#pricelistitemcode").html(itemcode);
    	$("#pricelistitemname").html(itemname);
    	price = Number(price);
    	$("#pricelistdefault").html(price.toFixed(2));
    	$("#pricelisttier1").html(Number(price + (tier1 * price/100)).toFixed(2));
    	$("#pricelisttier2").html(Number(price + (tier2 * price/100)).toFixed(2));
    	$("#pricelisttier3").html(Number(price + (tier3 * price/100)).toFixed(2));
    	$("#pricelisttier4").html(Number(price + (tier4 * price/100)).toFixed(2));
    }
</script>
<?php echo '<script>var formurl = "'.site_url('inventory/showeditform').'";</script>';?>
<?php echo '<script>var dealurl = "'.site_url('inventory/showdealform').'";</script>';?>
<script>
function updateitem(id)
{	
	var d = "itemid="+id;
	formurl = formurl+"/"+id;
    /*$.ajax({
        type: "post",
        url: formurl,
        data: d
    }).done(function(data) {
        $("#editbody").html(data);
        $("#editmodal").modal();
    });*/
    window.open(formurl,null,
"height=700,width=700,status=yes,toolbar=no,menubar=no,location=no");
}
function updatedeal(id)
{
	var d = "itemid="+id;
	dealurl = dealurl+"/"+id;
    /*$.ajax({
        type: "post",
        url: dealurl,
        data: d
    }).done(function(data) {
        $("#editbody").html(data);
        $("#editmodal").modal();
    });*/
    
    window.open(dealurl,null,
"height=700,width=700,status=yes,toolbar=no,menubar=no,location=no");

}
</script>

<style>
.awarded-to-me td
{
	color: green;
}
.not-awarded-to-me td
{
	color: red;
}
</style> 
<?php //print_r($items);die;?>
    <div class="content">  
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">	
			<h3>Inventory </h3>
			<div class="pull-right">
				<a href="<?php echo site_url("store/items/".$this->session->userdata('company')->username);?>" target="_blank">
				Go to my store
				</a>
			</div>
		</div>		
	   <div id="container">
		<div class="row">
                    <div class="col-md-12">

                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>
                               
                                <form action="<?php echo site_url('inventory');?>" method="post">
                                <table>
                                
                                <tr>
                                <td width="20">&nbsp;</td>
                                	<td>ITEM CODE/Name: </td>
                                	<td>
                                	<input type="text" name="searchitem" value="<?php echo @$_POST['searchitem'];?>"/>
                                	</td>
                                	<td>
                                	Category:
                                	</td>
                                	<td>
                                	<select id="category" name="category" class="form-control" style="width:250px">
                                		<option value="">All</option>
                                    	<?php foreach($categories as $cat){?>
                                    	<option value="<?php echo $cat->id;?>" <?php if(@$_POST['category']==$cat->id){echo 'selected';}?>><?php echo $cat->catname;?></option>
                                    	<?php }?>
                                    </select>
                                    </td>
                                    <td>
                                	Brand:
                                	</td>
                                	<td>
                                	<select id="manufacturer" name="manufacturer" class="form-control" style="width:150px">
                                		<option value="">All</option>
                                    	<?php foreach($manufacturers as $mf){?>
                                    	<option value="<?php echo $mf->id;?>" <?php if(@$_POST['manufacturer']==$mf->id){echo 'selected';}?>><?php echo $mf->title;?></option>
                                    	<?php }?>
                                    </select>
                                    </td><td>
                                    &nbsp;<input type="checkbox" name="serachmyitem" id="serachmyitem" <?php if(@$_POST['serachmyitem']!=""){echo 'checked';}?> >&nbsp;My Iitems only
                                    </td>
                                    <td width="5">&nbsp;</td>
                                    <td>
                                	<input type="submit" value="Search" class="btn btn-primary"/>
                                	<?php if(@$_POST['searchitem'] || @$_POST['category'] || @$_POST['manufacturer']){?>
                                	<a href="<?php echo site_url('inventory');?>">
                                		<input type="button" value="Clear" class="btn btn-primary"/>
                                	</a>
                                	<?php }?>
                                	</td>
                                	</tr>
                                	</table>
                                </form>
                            </div>
                		    <?php 
                		    	if($items)
                		    	{
                		    ?>
                            <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general">
                                        <thead>
                                            <tr>
                                                <!-- <th style="width:10%">Item Code</th>  -->
                                                <th style="width:15%">Item Name</th>
                                                <th style="width:15%"><font color="#0AA699">Code/Name</font></th>
                                                <th style="width:10%"><font color="#0AA699">Manufacturer</font></th>
                                                <th style="width:10%"><font color="#0AA699">Part#</font></th>
                                                <th style="width:10%"><font color="#0AA699">List Price</font></th>
                                                <th style="width:10%"><font color="#0AA699">Min. Qty.</font></th>
                                                <th style="width:5%"><font color="#0AA699">Stock</font></th>
                                                <th style="width:5%"><font color="#0AA699">Store/<br/>Featured</font></th>
                                                <th style="width:5%"><font color="#0AA699">Action</font></th>
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
                                                <!-- <td class="v-align-middle"><?php echo $item->itemcode;?></td> -->
                                                <td class="v-align-middle"><?php echo $item->itemname;?></td>
                                              
                                                <td class="v-align-middle">
                                                	<input type="text" placeholder="Itemcode"
                                                	value="<?php echo @$item->companyitem->itemcode?>"
                                                	onchange="updateItemcode('<?php echo $item->id?>',this.value);"/>
                                                	
                                                	<input type="text" style="margin-top:5px" placeholder="Itemname"
                                                	value="<?php echo @$item->companyitem->itemname?>"
                                                	onchange="updateItemname('<?php echo $item->id?>',this.value);"/>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<select onchange="updateManufacturer('<?php echo $item->id?>',this.value);" style="width: 155px;font-size:12px;" class="form-control">
                                                		<option value="">Select Manufacturer</option>
                                                		<?php foreach($manufacturers as $mf){?>
                                                			<option value="<?php echo $mf->id;?>" <?php if($mf->id == @$item->companyitem->manufacturer){echo 'SELECTED';}?>><?php echo $mf->title?></option>
                                                		<?php }?>
                                                	</select>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="text" style="width: 80px;" placeholder="Part#"
                                                	value="<?php echo @$item->companyitem->partnum?>"
                                                	onchange="updatePartnum('<?php echo $item->id?>',this.value);"/>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="text" style="width: 80px;" placeholder="Price"
                                                	value="<?php echo @$item->companyitem->ea?>"
                                                	onchange="updateItemprice('<?php echo $item->id?>',this.value);"/>
                                                	<?php if(@$item->companyitem){?>
                                                	<a href="javascript: void(0)" onclick="viewPricelist('<?php echo htmlentities(@$item->companyitem->itemcode)?>','<?php echo htmlentities(@$item->companyitem->itemname)?>','<?php echo @$item->companyitem->ea?>');">
                                                		<i class="fa fa-search"></i>
                                                	</a>
                                                	<?php }?>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="text"  style="width: 60px;" placeholder="Min Qty"
                                                	value="<?php echo @$item->companyitem->minqty?>"
                                                	onchange="updateMinqty('<?php echo $item->id?>',this.value);"/>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="checkbox"
                                                	<?php echo @$item->companyitem->instock?'checked="CHECKED"':''?>"
                                                	onchange="updateIteminstock('<?php echo $item->id?>',this.checked);"/>
                                                	                                         	
                                                	
                                                	<input type="text"  style="width: 60px;" placeholder="Stock"
                                                	value="<?php echo @$item->companyitem->qtyavailable?>"
                                                	onchange="updateqtyavailable('<?php echo $item->id?>',this.value);"/>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<input type="checkbox"
                                                	<?php echo @$item->companyitem->instore?'checked="CHECKED"':''?>"
                                                	onchange="updateIteminstore('<?php echo $item->id?>',this.checked);"/>
                                                	
                                                	<input type="checkbox"
                                                	<?php echo @$item->companyitem->isfeature?'checked="CHECKED"':''?>"
                                                	onchange="updateItemisfeature('<?php echo $item->id?>',this.checked);"/>
                                                </td>
                                                
                                                <td class="v-align-middle">
                                                	<a href="javascript:void(0);" onclick="updateitem('<?php echo $item->id;?>')">My Store</a>
                                                	<br/>
                                                	<a href="javascript:void(0);" onclick="updatedeal('<?php echo $item->id;?>')">Deal Setup</a>
                                                </td>
                                                
                                            </tr>
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                    <br/>
                                    <div class="pagination pagination-centered">
                                        <?php $this->view('inventory/paging'); ?>
                                    </div>
                            </div>
                
                <?php } else { ?>
                        </div>
                    </div>
                </div>
                
                    <div class="errordiv">
      				<div class="alert alert-info">
	                  <button data-dismiss="alert" class="close"></button>
	                  <div class="msgBox">
	                  No Items Detected on System.
	                  </div>
	                 </div>
     	 		   </div>
                <?php }?>
			
		</div>
  </div>
  
  
  <div id="pricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <h4 class="semi-bold" id="myModalLabel">
          Price Details:
          <span id="pricelistitemcode"></span>
          (<span id="pricelistitemname"></span>)
          </h4>
          <br>
        </div>
        <div class="modal-body">
          <div class="row form-row">
            <div class="col-md-8">
              List Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelistdefault"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier1 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier1"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier2 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier2"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier3 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier3"></span>
            </div>
          </div>
          <div class="row form-row">
            <div class="col-md-8">
              Tier4 Price: 
            </div>
            <div class="col-md-4">
              <span id="pricelisttier4"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  <div id="editmodal" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none; min-width: 700px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        </div>
        <div class="modal-body" id="editbody">
          
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
	  