<?php echo '<script>var serviceurl = "'.base_url().'admin/itemcode/showeditform/";</script>';?>
<?php echo '<script>var addserviceandlabor="'.site_url('admin/itemcode/addnewserviceandlabor').'";</script>'?>
<?php echo '<script>var insertserviceandlabor="'.site_url('admin/itemcode/insertserviceandlabor').'";</script>'?>
<?php echo '<script>var removeserviceitem="'.site_url('admin/itemcode/delserviceitem').'";</script>'?>
<?php echo '<script>var updtserviceandlabor="'.site_url('admin/itemcode/updateserviceandlabor').'";</script>'?>

<script type="text/javascript">
		
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
    		if(data)
    		{    			
    			$(".alert-success").css({display: "block"});
    			$("#htmlqtymessage").html("service item details deleted successfully!");    				
    			addserviceandlabortest();
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
</script>

<section class="row-fluid">
 <a  href="javascript: void(0)" onclick="addserviceandlabortest()" class="btn btn-green"> Add service & labor items </a></h3>
</section>

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