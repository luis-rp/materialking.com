<?php if (isset($jsfile)) include $this->config->config['base_dir'] . 'templates/admin/gridfeed/' . $jsfile; ?>
<?php echo '<script>var serviceurl = "'.base_url().'admin/itemcode/showeditform/";</script>';?>
<?php echo '<script>var addserviceandlabor="'.site_url('admin/itemcode/addnewserviceandlabor').'";</script>'?>
<?php echo '<script>var insertserviceandlabor="'.site_url('admin/itemcode/insertserviceandlabor').'";</script>'?>
<?php echo '<script>var removeserviceitem="'.site_url('admin/itemcode/delserviceitem').'";</script>'?>
<?php echo '<script>var updtserviceandlabor="'.site_url('admin/itemcode/updateserviceandlabor').'";</script>'?>
<?php echo '<script>var addqtytoinventoryurl="'.site_url('admin/itemcode/addqtytoinventory').'";</script>'?>
<script>
	function updateitem(id)
	{
		var d = "itemid="+id;
        $.ajax({
            type: "post",
            url: serviceurl,
            data: d
        }).done(function(data) {
            $("#editbody").html(data);
            $("#editmodal").modal();
        });
	}
</script>
	 <script type="text/javascript">
	 $(document).ready(function(){
 tour8 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Item Code Managment"
	  },


	]
	});

	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour8.restart();
			// Initialize the tour
			tour8.init();
			start();
		});

	$('#btndel').click(function(e){
		if(confirm('Are You Sure?')){
				var checkd = $('.del_group:checked')	;
				var itemdToSend = new Array();
				for( i = 0; i < checkd.length; i++){
					itemdToSend[i] = checkd[i].value;
					}
				//console.log(itemdToSend);
				$.ajax({
					url:"/admin/itemcode/delete_multiple",
					data:{items:itemdToSend},
					type:'POST'
					});
				}

		 });

		$('#canceltour').click(function(e){
			endTour();
			});

	 });


	 function start(){

			// Start the tour
				tour8.start();
			 }
	 function endTour(){

		 $("#tourcontrols").remove();
		 tour8.end();
			}
			
	 function addserviceandlabortest()
	 {
	 	$("#qtypricelist").modal();
    	
    	var data = "";
    	$("#qtypriceplacer").html("");
    	$.ajax({
    		type:"post",
    		data: "",
    		url: addserviceandlabor
    	}).done(function(data){
    		if(data){			
    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    		}
    	});

    	$("#addiscount").html('<br><table width="100%" align="center"><tr><td>Name</td><td>Price</td><td>Tax</td><td>&nbsp</td><tr><td><input type="text" name = "servicename" id="servicename"></td><td><input type="text" name = "serviceprice" id="serviceprice"></td><td><input type="text" name = "servicetax" id="servicetax"></td><td><input type="button" value = "Add" onclick="addservice();"></tr><table>');
	 }
	 
	 function addservice(){

    	var data = "name="+$("#servicename").val()+"&serviceprice="+$("#serviceprice").val()+"&servicetax="+$("#servicetax").val();

    	$.ajax({
    		type:"post",
    		data: data,
    		url: insertserviceandlabor
    	}).done(function(data){
    		if(data){

    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    			$("#htmlqtymessage").html("Service items added successfully!");
    			$(".alert-success").css({display: "block"});
    			$("#servicename").val('');
    			$("#serviceprice").val('');
    			$("#servicetax").val('');
    			
    		}
    	});

    }
    
    function delserviceitem(id){

    	$.ajax({
    		type:"post",
    		data: "id="+id,
    		url: removeserviceitem,
    		sync:false
    	}).done(function(data){
    		if(data){
    			if(data=="success")
    			$("#htmlqtymessage").html("service item details deleted successfully!");
    			else
    			$("#htmlqtymessage").html("*Error in deleting Quantity-Price details!");
    		}
    	});
    }
    
    function editserviceitem(id)
    {
    	$("#isEdit"+id).css("display","");
    	$("#isdefault"+id).css("display","none");
    }
    
    function saveserviceitem(id)
    {
    	
    	var data = "id="+id+"&name="+$("#servicename"+id).val()+"&serviceprice="+$("#serviceprice"+id).val()+"&servicetax="+$("#servicetax"+id).val();

    	$.ajax({
    		type:"post",
    		data: data,
    		url: updtserviceandlabor
    	}).done(function(data){
    		if(data){

    			$("#qtypriceplacer").html("");
    			$("#qtypriceplacer").html(data);
    			$("#htmlqtymessage").html("Service items added successfully!");
    			$(".alert-success").css({display: "block"});
    			addserviceandlabortest();
    		}
    	});
    	
    }
    
    
    function addtoinventory(id){
		
		$('#invitemid').val(id);
		$('#addinventorymodal').modal();		
	}
	
	
	function saveitemtoinventory(){
		
		if($('#invquantity').val()<=0)
			alert("Please enter value greater than zero");
		else{
			var data = "itemid="+$('#invitemid').val()+"&quantity="+$('#invquantity').val();
			$.ajax({
    		type:"post",
    		data: data,
    		url: addqtytoinventoryurl
    	}).done(function(data){
    		if(data){
    			
    			alert(data);
    			
    		}
    	});
			
		}
	}
	
	
	function viewitems2(itemid)
	{
		var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;
		//alert(quoteid);
		$("#quoteitemdetails").html('loading ...');

		$.ajax({
			type:"post",
			url: serviceurl,
		}).done(function(data){			
			$("#quoteitemdetails").html(data);
			$("#quoteitemdetails").css({display: "block"});
			$("#quoteitemdetailsm").css({display: "block"});
			$("#quoteitemdetailsm").removeClass("hide");
			//$("#quoteitemdetailsm").modal();
		});
	}
	
	
	function closepop(){
		$("#quoteitemdetails").html('');
		$("#quoteitemdetails").css({display: "none"});
		$("#quoteitemdetailsm").css({display: "none"});		
	}
	
    function addtofavorites(itemid)
    {
    	var itemcodeurl = '<?php echo base_url()?>admin/itemcode/addtofavorites/'+ itemid;
		
		$.ajax({
			type:"post",
			url: itemcodeurl,
		}).done(function(data){			
			alert('Item added in Favorites list.');
			location.reload();
		});
    }
    
    function removefromfavorites(itemid)
    {
    	var itemcodeurl = '<?php echo base_url()?>admin/itemcode/removefromfavorites/'+ itemid;
		
		$.ajax({
			type:"post",
			url: itemcodeurl,
		}).done(function(data){			
			alert('Item removed from Favorites list.');
			location.reload();
		});
    }
    
    function setViewFavorite(val)
    {
    	$("#isfavorite").val(val);
    	$("#frmItemcode").submit();
    }
 </script>
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
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>

<section class="row-fluid">
    <h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo $heading; ?></span>   <a href="<?php echo base_url("admin/itemcode/export");?>" class="btn btn-green">Export all items</a>  &nbsp;&nbsp; <a href="<?php echo base_url("admin/itemcode/itempdf");?>" class="btn btn-green">View PDF</a>
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
                            <?php echo $addlink;
                            echo '&nbsp;' . $addcatlink;
                            echo '&nbsp;' . $addsubcatlink;
                           
                             ?>
                            
                          <input type="button" class="btn btn-green" name="btnviewfavorite" id="btnviewfavorite" value="View Favorites" onclick="setViewFavorite(1);"> 
                            <?php if($this->session->userdata('usertype_id') != 2) { ?> <button type="button" class="btn btn-green " id="btndel">Delete Selected Items</button> <?php } ?>
                            <div class="datagrid-header-right">

                            		<table style="border:0px !important;float:left;">
                            		 <form id="frmItemcode" method="post" action="<?php echo site_url('admin/itemcode');?>">
                            		<input type="hidden" name="isfavorite" id="isfavorite" value="0">
                            		<tr><td  style="border:0px !important;">Category:</td>
                            		<td style="border:0px !important;"> 
                            		<select id="searchcategory" name="searchcategory" style="width: 200px;">
                                        <option value=''>All Categories</option>
                                        <?php
                                         echo $categories;
                                        /*foreach ($categories as $cat) { ?>
                                            <option value="<?php echo $cat->id ?>"
                                            <?php
                                            if (@$_POST['searchcategory'] == $cat->id) {
                                                echo 'SELECTED';
                                            }
                                            ?>
                                                    >
                                            <?php echo $cat->catname ?>
                                            </option>
                                        <?php }*/ ?>
                                    </select></td>
                                    <td  style="border:0px !important;"> <button type="search" class="btn"><i class="icon-search"></i></button>
                                     </td>
                                    <td  style="border:0px !important;"></td>
                                    </tr>
                            	
                            		</table>



                            	<table style="border:0px !important;float:left;"><tr><td  style="border:0px !important;">Item:</td>
                            	<td  style="border:0px !important;"><?php //if(1){?>
                                <div class="input-append search datagrid-search" style="margin-top:0px !important;">
                                    <!--<input type="text" class="input-medium" placeholder="Search" value="<?php echo @$_POST['searchitemname'];?>" style="height:22px !important;">
                                    <button class="btn"><i class="icon-search"></i></button>-->
                                    <input type="text" name="searchQuery" id="searchQuery" value="<?php if(isset($searchQuery) && $searchQuery != '') echo $searchQuery; else echo '';?>"> &nbsp;&nbsp;
                                    <input type="submit" name="btnSearch" id="btnSearch" value="    "  class="icon-search" style="margin-left:10px;">
                                </div>
                                <?php //}?></td>

                            	</tr></table>

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
                            <span style="text-align:right;">
	                        <form method="post" action="<?php echo site_url('admin/itemcode');?>">   
	                        <input type="hidden" name="loadoffset" value = "<?php echo $offset;?>">                    
	                        <input type="submit" name="btnloadnewitems" id="btnloadnewitems" value="Load next 100 Items" style="margin-left:10px;">
	                        </form>
	                        </span>
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

<?php if ($this->session->userdata('usertype_id') == 2){?>
<div id="editmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        <h4>Edit Item Specification</h4>
    </div>
    <div class="modal-body" id="editbody">
    </div>

</div>
<?php }?>

<div id="qtypricelist" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>
          <div style="display: none;" class="alert alert-success fade in"><button type="button" class="close close-sm" data-dismiss="alert"><i class="icon-remove"></i></button><div id="htmlqtymessage"></div></div>
          <h4 class="semi-bold" id="myModalLabel">
          <!--Price Details:
          <span id="qtyitemcode"></span>
          (<span id="qtyitemname"></span>)-->
          <br/> Service & Labor Items:
          </h4>
          <br>
        </div>
        <div class="modal-body">
          <div class="row form-row">
            <div class="col-md-8">
             <!-- List Price:-->
            </div>
            <div class="col-md-4">
              <span id="qtylistprice"></span>
            </div>
          </div>
          <div id="qtypriceplacer" style="overflow-y:auto;height:150px;"></div>

          <div id="addiscount" class="row form-row"></div>

        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  
  
  
  
  
  <div id="addinventorymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        <h4>Add Quantity to Inventory</h4>
    </div>
    <div class="modal-body">
    <input type="text" name="invquantity" id="invquantity"/>
    <input type="hidden" name="invitemid" id="invitemid"/>
    <input type="button" value="save" onclick="saveitemtoinventory();"/>
    </div>

</div>




<div id="quoteitemdetailsm" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<input style="float:right;margin-top:2px;" type="button" id="cls" name="cls" class="btn btn-green" value="close" onclick="closepop();" />
        		
        	</div>
        	<div class="modal-body" id="quoteitemdetails">
        	</div>
            
        </div>