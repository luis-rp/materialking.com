<?php echo '<script>var addmasterdefaulturl="'.site_url('admin/itemcode/addmasterdefault').'";</script>'?>
<?php echo '<script>var getmasterdefaultsurl="'.site_url('admin/itemcode/getmasterdefaults').'";</script>'?>
<?php echo '<script>var deletedefaultitemurl="'.site_url('admin/itemcode/deletedefaultitem').'";</script>'?>
<?php echo '<script>var updatemasterdefaulturl="'.site_url('admin/itemcode/updatemasterdefault').'";</script>'?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#content').wysihtml5();

    
	$('#datatable').dataTable( {
		"bPaginate":   false,
		"bInfo":   false,
		"aoColumns": [
		        		{ "bSortable": false },
		        		null,
		        		null,
			]
		} );
});
//-->
</script>

<style>
.dataTables_filter
{
	margin-right:30px;
}
</style>

<script src="<?php echo base_url(); ?>templates/admin/js/jquery.form.js" type="text/javascript"></script>

<script type="text/javascript">

	$( window ).load(function() { fetchmasterdefaults(); });

    $(document).ready(function() {        
        $('#itemcodedate').datepicker();
        $('#notes').autosize();
        $('#wiki').wysihtml5();
        $('#listinfo').wysihtml5();
        //$('#description').wysihtml5();
        //$('#details').wysihtml5();
        $('#get_info').click(function() {
            var serviceurl = '<?php echo base_url() ?>admin/itemcode/get_amazon_details/';
            var url = $('#external_url').val();
            //alert(serviceurl);
            $.ajax({
                type: "post",
                url: serviceurl,
                dataType : 'json',
                data: "url=" + url,
                success: function(json){
                    //alert(json.details);
                    //json = JSON.parse(json);
                    $('#description').html(json.description.replace(/\<br[\/]*\>/g, "\n"));
                    var details = json.details.replace(/\<br[\/]*\>/g, "\n");
                    //alert(details);
                    $('#details').html(details);
                    //$('#itemname').val(json.title);
                    //$('#ea').val(json.amazon_price);
                    alert('Details fetched from amazon')
                }
            });
            return false;
        });
        
        /*$('.noxls').css('display','none');		
		$('.forxls').css('display','none');*/

       // $('#tagsInput').tagsinput();
       
       fetchmasterdefaults();
       
    });

    function showhistory(companyid, itemcode, companyname)
    {
        var serviceurl = '<?php echo base_url() ?>admin/itemcode/gethistory/';
        //alert(serviceurl);
        var d = "companyid=" + companyid + "&itemcode=" + encodeURIComponent(itemcode);
        //alert(d);
        $.ajax({
            type: "post",
            url: serviceurl,
            data: d
        }).done(function(data) {
            $("#pricehistory").html(data);
            $("#historycompanyname").html(companyname);
            $("#historymodal").modal();
        });
    }
    function searchprice(keyword)
    {
        var serviceurl = '<?php echo base_url() ?>admin/itemcode/amazon';
        //alert(serviceurl);
        $("#searchmodal").modal();
        $.ajax({
            type: "post",
            url: serviceurl,
            data: "keyword=" + keyword
        }).done(function(data) {
            $("#minpricesearch").html(data);
        });
    }

    function openamazon(keyword)
    {
        keyword = encodeURIComponent(keyword);
        var url = 'http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords=' + keyword + '&rh=i%3Aaps%2Ck%3A1%22+x+3%2F4%22+copper+reducer';

        window.open(url, 'amazonlookup', 'width=1200,height=800,menubar=no,scrollbars=yes');
    }
    
    
    function addmasterdefaultoptions()
    {    	
    	var minqtydefault = $("#minqtydefault").val();
    	var itemnamedefault = $("#itemnamedefault").val();
    	var pricedefault = $("#pricedefault").val();
    	var pricedefault = Number(pricedefault);    	
    	var partnodefault = $("#partnodefault").val();
		var manufacturerdefault = $("#manufacturerdefault").val();
    	var itemiddefault = $("#itemiddefault").val();
    	var itemcodedefault = $("#itemcodedefault").val();
    	
    	var data = "itemiddefault="+itemiddefault+"&partnodefault="+partnodefault+"&manufacturerdefault="+manufacturerdefault+"&pricedefault="+pricedefault+"&itemnamedefault="+itemnamedefault+"&minqtydefault="+minqtydefault+"&itemcodedefault="+itemcodedefault;
           	
    	$.ajax({
    		type:"post",
    		data: data,
    		url: addmasterdefaulturl
    	}).done(function(data){
    		if(data){
				
    			fetchmasterdefaults();
    		}
    	});

    	/*$("#addiscount").html('<table><tr><td>Qty</td><td>Price</td><td>&nbsp</td></td><tr><td><input type="text" name = "discqty" id="discqty"></td><td><input type="text" name = "discprice" id="discprice"></td><td><input type="button" value = "Add" onclick="addqtydiscount();"><input type="hidden" name="qtyitemid" id="qtyitemid" value="'+itemid+'" </td></tr><table>');*/
    }
    
    
    function updatedefaultoption(id){
    	
    	var minqtydefault = $("#minqtydefault"+id).val();
    	var itemnamedefault = $("#itemnamedefault"+id).val();
    	var pricedefault = $("#pricedefault"+id).val();
    	var pricedefault = Number(pricedefault);    	
    	var partnodefault = $("#partnodefault"+id).val();
		var manufacturerdefault = $("#manufacturerdefault"+id).val();    	
		var itemcodedefault = $("#itemcodedefault"+id).val();  
    	
    	var data = "id="+id+"&partnodefault="+partnodefault+"&manufacturerdefault="+manufacturerdefault+"&pricedefault="+pricedefault+"&itemnamedefault="+itemnamedefault+"&minqtydefault="+minqtydefault+"&itemcodedefault="+itemcodedefault;
           	
    	$.ajax({
    		type:"post",
    		data: data,
    		url: updatemasterdefaulturl
    	}).done(function(data){
    		if(data){				
    			if(data==1)
    			$("#htmldefaultitemmessage").html("Master Default Option Updated successfully!");
    			else
    			$("#htmldefaultitemmessage").html("*Error in Updating Master Default Option!");    			
    			fetchmasterdefaults();
    		}
    	});
    	
    }
    
    
    function fetchmasterdefaults(){
    	var itemiddefault = $("#itemiddefault").val();    	
    	var data = "itemiddefault="+itemiddefault;
           	
    	$.ajax({
    		type:"post",
    		data: data,
    		dataType : 'json',
    		url: getmasterdefaultsurl
    	}).done(function(data){
    		if(data){
    		
			$(".defaulttab tbody").html('');	
    		$.each(data,function(id,defaultitem){
    		
    			var newhtml = '<tr id="label'+defaultitem.id+'">';
    			newhtml +='<td class="v-align-middle">'+defaultitem.title+'</td>';
    			newhtml +='<td class="v-align-middle">'+defaultitem.itemcode+'</td>';
    			newhtml +='<td class="v-align-middle">'+defaultitem.partnum+'</td>';
    			newhtml +='<td class="v-align-middle">'+defaultitem.itemname+'</td>';
    			newhtml +='<td class="v-align-middle">'+defaultitem.price+'</td>';
    			newhtml +='<td class="v-align-middle">'+defaultitem.minqty+'</td>';
    			newhtml +='<td class="v-align-middle"><a href="javascript:void(0);" onclick="editdefaultoption('+defaultitem.id+','+defaultitem.manufacturer+')"><span class="icon-edit"></span></a>&nbsp;&nbsp;<a href="#"><img onclick="deldefaultoption('+defaultitem.id+')" src="<?php echo base_url();?>templates/front/assets/img/icon/delete.ico" /></a></td>';
    			newhtml +='</tr>';    	
    			
    			var newhtml2 = '<tr id="text'+defaultitem.id+'" style="display:none;">';
    			newhtml2 +='<td class="v-align-middle">';
    			newhtml2 +='<select style="width: 155px;font-size:12px;" class="form-control" id="manufacturerdefault'+defaultitem.id+'">';
    			newhtml2 +='<option value="">Select Manufacturer</option><?php foreach($manufacturers as $mf){?><option value="<?php echo $mf->id;?>"><?php echo $mf->title?></option><?php }?></select></td>';    			    			
    			//newhtml2 +='<td class="v-align-middle">'+defaultitem.title+'</td>';
    			
    			newhtml2 +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Itemcode" value="'+defaultitem.itemcode+'" id="itemcodedefault'+defaultitem.id+'" name="itemcodedefault'+defaultitem.id+'" /></td>';
    			
    			//newhtml2 +='<td class="v-align-middle">'+defaultitem.partnum+'</td>';
    			newhtml2 +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Part#" value="'+defaultitem.partnum+'" id="partnodefault'+defaultitem.id+'"/></td>';
    			//newhtml2 +='<td class="v-align-middle">'+defaultitem.itemname+'</td>';
    			newhtml2 +='<td class="v-align-middle"><input name="itemnamedefault'+defaultitem.id+'" value="'+defaultitem.itemname+'" id="itemnamedefault'+defaultitem.id+'"></td>';
    			//newhtml2 +='<td class="v-align-middle">'+defaultitem.price+'</td>';
    			newhtml2 +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Price" id="pricedefault'+defaultitem.id+'" name="pricedefault'+defaultitem.id+'" value="'+defaultitem.price+'" /></td>';
    			//newhtml2 +='<td class="v-align-middle">'+defaultitem.minqty+'</td>';
    			newhtml2 +='<td class="v-align-middle"><input type="text"  style="width: 100px;" placeholder="Min Qty" id="minqtydefault'+defaultitem.id+'"  value="'+defaultitem.minqty+'" /></td>';    		
    			newhtml2 +='<td class="v-align-middle"><input type="button" onclick="updatedefaultoption('+defaultitem.id+');" value="Update" name="updatemasterdefault" id="updatemasterdefault"/></td>';
    			newhtml2 +='</tr>'; 
    					
    			$(".defaulttab tbody").append(newhtml);
    			$(".defaulttab tbody").append(newhtml2);
    		});	
    			
    			
    			var html ='<tr>';
    			html +='<td class="v-align-middle">';
    			html +='<select style="width: 155px;font-size:12px;" class="form-control" id="manufacturerdefault">';
    			html +='<option value="">Select Manufacturer</option><?php foreach($manufacturers as $mf){?><option value="<?php echo $mf->id;?>"><?php echo $mf->title?></option><?php }?></select></td>';
    			html +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Itemcode" id="itemcodedefault" name="itemcodedefault" value="<?php echo $this->validation->itemcode; ?>" /></td>';
    			html +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Part#" id="partnodefault"/></td>';
    			html +='<td class="v-align-middle"><input name="itemnamedefault"  id="itemnamedefault"></td>';
    			html +='<td class="v-align-middle"><input type="text" style="width: 60px;" placeholder="Price" id="pricedefault" name="pricedefault"/></td>';
    			html +='<td class="v-align-middle"><input type="text"  style="width: 100px;" placeholder="Min Qty" id="minqtydefault" /></td></tr>';    			
    			html +='<tr><td><input type="button" onclick="addmasterdefaultoptions();" value="Add Another" name="addmasterdefault" id="addmasterdefault"/></td><td colspan="4"></td></tr>';
    			$(".defaulttab tbody").append(html);
    		
    		}
    	});
    	
    }
    
    function deldefaultoption(id){
    	
    	$.ajax({
    		type:"post",
    		data: "id="+id,
    		url: deletedefaultitemurl,
    		sync:false
    	}).done(function(data){
    		if(data){
    			if(data=="success")
    			$("#htmldefaultitemmessage").html("Master Default Option deleted successfully!");
    			else
    			$("#htmldefaultitemmessage").html("*Error in deleting Master Default Option!");
    			fetchmasterdefaults();
    		}
    	});
    	
    }
    
    
    function editdefaultoption(id,manufacturer){
    	
    	$('#label'+id).css('display','none');
    	$('#text'+id).css('display','table-row');    	
    	$('#manufacturerdefault'+id).val(manufacturer);
    }
    
    var upload_number = 2;
	function addFileInput() {
	 	var d = document.createElement("div");
	 	var abc= document.createElement("div");
	 	var file = document.createElement("input");
	 	var file1 = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file1.setAttribute("type", "text");
	 	file.setAttribute("name", "UploadFile[]");
	 	file1.setAttribute("name", "filename[]");
	 	d.appendChild(file);
	 	abc.appendChild(file1);
	 	document.getElementById("moreUploads").appendChild(d);
	 	document.getElementById("moreUploads").appendChild(abc);
	 	upload_number++;
	}
	
</script>


<section class="row-fluid">
    <h3 class="box-header"><?php echo $heading; ?></h3>
    <div class="box">
            <div class="span12">

                <?php echo @$message; ?>
                <?php echo @$this->session->flashdata('message'); ?>
                <a class="btn btn-green" href="<?php echo site_url('admin/itemcode'); ?>">&lt;&lt; Back</a>
                <br/>                      
                <form id="itemaddform" class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
                <div  style="width:48%; float:left;">
                    <input type="hidden" name="id" value="<?php echo @$this->validation->id; ?>"/>
                    <br/>                   
                    
                    <div class="control-group noxls">
                        <label class="control-label">Item Code</label>
                        <div class="controls">
                            <input type="text" id="itemcode" name="itemcode" class="span10" value="<?php echo @$this->validation->itemcode; ?>">
                            <?php //echo $this->validation->itemcode_error; ?>
                        </div>
                    </div>            

                    <div class="control-group noxls">
                        <label class="control-label">Item Name</label>
                        <div class="controls">
                            <input type="text" id="itemname" name="itemname" class="span10" value="<?php echo htmlentities(@$this->validation->itemname); ?>">
                            <?php echo @$this->validation->itemname_error; ?>
                        </div>
                    </div>         
                   

                    <div class="control-group noxls">
                        <label class="control-label">Unit</label>
                        <div class="controls">
                            <input type="text" id="unit" name="unit" class="span10" value="<?php echo @$this->validation->unit; ?>">
                            <?php echo @$this->validation->unit_error; ?>
                        </div>
                    </div>                                              

                    
                    <?php if ($this->session->userdata('usertype_id') == 2) { ?>
                        <div class="control-group noxls">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <input name="add" type="submit" class="btn btn-primary" value="Update Itemcode List"/>
                            </div>
                        </div>
                    <?php } ?>
                    
                </div>
                </form>

            </div>
    </div>
