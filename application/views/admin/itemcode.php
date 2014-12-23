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
    	
    	var data = "itemiddefault="+itemiddefault+"&partnodefault="+partnodefault+"&manufacturerdefault="+manufacturerdefault+"&pricedefault="+pricedefault+"&itemnamedefault="+itemnamedefault+"&minqtydefault="+minqtydefault;
           	
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
    	
    	var data = "id="+id+"&partnodefault="+partnodefault+"&manufacturerdefault="+manufacturerdefault+"&pricedefault="+pricedefault+"&itemnamedefault="+itemnamedefault+"&minqtydefault="+minqtydefault;
           	
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
	
	
	function showuploadfields(){
		$('.noxls').css('display','none');		
		$('.forxls').css('display','block');		
	}
	
	function showindividualfields(){
		$('.noxls').css('display','block');		
		$('.forxls').css('display','none');		
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
                <br>
                <a style="cursor:pointer" onclick="showuploadfields();">Upload items using excel sheet</a> / <a style="cursor:pointer" onclick="showindividualfields();">Add Individual items</a>         
                
                <form id="massitemaddform" class="form-horizontal" method="post" action="<?php echo site_url('admin/itemcode/add_itemcode_xls'); ?>" enctype="multipart/form-data">
                
                
                    <div class="control-group forxls">
                        <label class="control-label">Category<br>(Press Cntl to select multiple categories)</label>
                        <div class="controls">
                            <select style="width:400px;" multiple id="categories[]" name="categories[]">
                            	<?php foreach($categories as $cat){?>
                            	<option title="<?php echo htmlentities($cat->catname);?>" value="<?php echo $cat->id;?>" <?php if(isset($this->validation->category[0])) { if(in_array($cat->id,$this->validation->category)){echo 'selected';} } ?>><?php echo htmlentities($cat->catname);?></option>
                            	<?php  } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group forxls">
                        <label class="control-label">Featured Supplier</label>
                        <div class="controls">
                            <select id="featuredsuppliers" name="featuredsuppliers">
                            	<?php foreach($companies as $sup){?>
                            	<option value="<?php echo $sup->id;?>" <?php if($this->validation->featuredsupplier==$sup->id){echo 'selected';}?>><?php echo $sup->title;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                
                
                 <div class="control-group forxls">
                        <label class="control-label">Upload Excel</label>
                        <div class="controls">                        	
                            <input type="file" name="massexcelfile" size="20"  />                            
                        </div>
                </div>
                
                <div class="control-group forxls">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <input name="massadd" type="submit" class="btn btn-primary" value="Update Itemcode List"/>
                            </div>
                </div>
                
                	
                </form>
                
                <form id="itemaddform" class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
                <div  style="width:48%; float:left;">
                    <input type="hidden" name="id" value="<?php echo $this->validation->id; ?>"/>
                    <br/>

                    <div class="control-group noxls">
                        <label class="control-label">Category<br>(Press Cntl to select multiple categories)</label>
                        <div class="controls">
                            <select style="width:400px;" multiple id="category[]" name="category[]">
                            	<?php foreach($categories as $cat){?>
                            	<option title="<?php echo htmlentities($cat->catname);?>" value="<?php echo $cat->id;?>" <?php if(isset($this->validation->category[0])) { if(in_array($cat->id,$this->validation->category)){echo 'selected';} } ?>><?php echo htmlentities($cat->catname);?></option>
                            	<?php  } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">Featured Supplier</label>
                        <div class="controls">
                            <select id="featuredsupplier" name="featuredsupplier">
                            	<?php foreach($companies as $sup){?>
                            	<option value="<?php echo $sup->id;?>" <?php if($this->validation->featuredsupplier==$sup->id){echo 'selected';}?>><?php echo $sup->title;?></option>
                            	<?php }?>
                            </select>
                        </div>
                    </div>
                    
                    
                    
                    
					<?php if(0){?>
                    <div class="control-group noxls">
                        <label class="control-label">In Store?</label>
                        <div class="controls">
                            <input type="checkbox" name="instore" value="1" <?php if($this->validation->instore){ echo 'CHECKED';}?>/>
                        </div>
                    </div>
                    <?php }?>
                    
                    <div class="control-group noxls">
                        <label class="control-label">Item Code</label>
                        <div class="controls">
                            <input type="text" id="itemcode" name="itemcode" class="span10" value="<?php echo $this->validation->itemcode; ?>">
                            <?php //echo $this->validation->itemcode_error; ?>
                        </div>
                    </div>
                    
                    <div class="control-group noxls">
                        <label class="control-label">URL</label>
                        <div class="controls">
                            <input type="text" id="url" name="url" class="span10" value="<?php echo $this->validation->url; ?>" required
                             onkeyup="this.value=this.value.replace(/[^0-9a-zA-Z-]/g,'');">
                             <br/>(only number, alphabet and dash allowed)
                           <?php if($this->validation->url!="") { ?><a target="_blank" href="<?php echo base_url(); ?>site/item/<?php echo $this->validation->url; ?>">view item</a><?php } ?>
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">Item Name</label>
                        <div class="controls">
                            <input type="text" id="itemname" name="itemname" class="span10" value="<?php echo htmlentities($this->validation->itemname); ?>">
                            <?php echo $this->validation->itemname_error; ?>
                        </div>
                    </div>
                    
                    <div class="control-group noxls">
                        <label class="control-label">Item Description</label>
                        <div class="controls">
                            <textarea class="span10" id="description" name="description"><?php echo htmlentities($this->validation->description); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="control-group noxls">
                        <label class="control-label">Item Details</label>
                        <div class="controls">
                        	<textarea class="span10" id="details" name="details"><?php echo htmlentities($this->validation->details); ?></textarea> 
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">Unit</label>
                        <div class="controls">
                            <input type="text" id="unit" name="unit" class="span10" value="<?php echo $this->validation->unit; ?>">
                            <?php echo $this->validation->unit_error; ?>
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">
                            E A
                            <?php
                            if ($this->validation->targetprice) {
                                echo '(Target Price: $' . round($this->validation->targetprice, 2) . ')';
                            }
                            ?>
                        </label>
                        <div class="controls noxls">
                            <input type="text" id="ea" name="ea" class="span10" value="<?php echo $this->validation->ea; ?>">
                            <?php echo $this->validation->ea_error; ?>
                        </div>
                    </div>
                    
                    
                      <div class="control-group noxls">
                        <label class="control-label">Special Increment</label>
                        <div class="controls">
                            <input type="number" class="span10" id="increment" name="increment"  min="1" max="100" 
                            value="<?php echo $this->validation->increment?$this->validation->increment:'1'; ?>">
                            <?php echo $this->validation->increment_error; ?>
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">Notes</label>
                        <div class="controls">
                            <textarea class="span10" id="notes" name="notes"><?php echo $this->validation->notes; ?></textarea>
                            <?php echo $this->validation->notes_error; ?>
                        </div>
                    </div>

                    <div class="control-group noxls">
                        <label class="control-label">Keyword</label>
                        <div class="controls">
                            <input type="text" id="keyword" name="keyword" class="span10" value="<?php echo $this->validation->keyword; ?>">
                            <?php echo $this->validation->keyword_error; ?>
                        </div>
                    </div>
                    <div class="control-group noxls">
                        <label class="control-label">Amazon URL</label>
                        <div class="controls">
                            <input value="<?php echo @$this->validation->external_url; ?>" type="text" name="external_url" id="external_url" class="span10"  placeholder="Enter amazon.com URL for that item" >
                            <?php if ($this->session->userdata('usertype_id') == 1) { ?>
                            <input name="get_info" id="get_info" class="btn btn-primary" value="get_info"/>
                            <?php }?>
                        </div>
                    </div>
                    <div class="control-group noxls">
                        <label class="control-label">Length (in)</label>
                        <div class="controls">
                            <input value="<?php echo $this->validation->length; ?>" type="text" name="length" id="length" class="span10"   >
                           <?php echo @$this->validation->error_length; ?>
                        </div>
                    </div>
                    <div class="control-group noxls">
                        <label class="control-label">Width (in)</label>
                        <div class="controls">
                            <input value="<?php echo $this->validation->width; ?>" type="text" name="width" id="width" class="span10"   >
                            <?php echo @$this->validation->error_width; ?>
                        </div>
                    </div>
                    <div class="control-group noxls">
                        <label class="control-label">Height (in)</label>
                        <div class="controls">
                            <input value="<?php echo $this->validation->height; ?>" type="text" name="height" id="height" class="span10"   >
                         <?php echo @$this->validation->error_height; ?>
                        </div>
                    </div>
                    <div class="control-group noxls">
                        <label class="control-label">Weight (oz)</label>
                        <div class="controls">
                            <input value="<?php echo $this->validation->weight; ?>" type="text" name="weight" id="weight" class="span10"  >
                           <?php echo @$this->validation->error_weight; ?>
                        </div>
                    </div> 
                                        
                    <div class="control-group noxls">
                        <label class="control-label">File</label>
                        <div class="controls">
                        	<?php if ($this->session->userdata('usertype_id') == 1) { ?>
                            <input type="file" name="userfile" size="20"  />
                            <?php }?>
                            <a href="<?php echo site_url('uploads/item') . '/' . @$this->validation->item_img; ?>" target="_blank"> 
                             <?php echo @$this->validation->item_img; ?>
                            </a> 
                            <label>Alternate Text for Image</label><input type="text" name="item_img_alt_text" id="item_img_alt_text" value="<?php if(isset($this->validation->item_img_alt_text)) echo $this->validation->item_img_alt_text; else echo "";?>" class="span10">
                        </div>
                    </div>
                    
                    
                     <div class="control-group noxls">
						<label class="control-label">Add Files</label>
						 <div class="controls">
							<?php if ($this->session->userdata('usertype_id') == 1) { ?>
						    <input type="file" name="UploadFile[]" id="UploadFile" onchange="document.getElementById('moreUploadsLink').style.display = 'block';" />
						    <?php }?>
						    
						      <?php //echo "<pre>"; print_r(@$this->validation->files); die; ?>
                             <?php if(@$this->validation->files)
                                       { 
                                       	$files=explode(',',@$this->validation->files);
                                       	$filecount=count($files); 
                                       	   if(@$this->validation->filename)
                                       	   {
                                       	     $filename=explode(',',@$this->validation->filename);
                                       	     $filenamecount=count($filename); 	
                                       	   }
                                       	    if($filecount==$filenamecount) 
                                                 {
                                                   for ($x=0; $x<$filecount; $x++)
                                                     {                                        
                                                       if(file_exists("./uploads/item/".$files[$x])) {  ?>                                   
                                    <a href="<?php echo site_url('uploads/item') . '/' . $files[$x]; ?>" target="_blank"> <?php echo $filename[$x]; ?></a>  
                                              <?php    } } } }?>                          
						    
					         <label>File Name</label>
						    <input type="text" name="filename[]" id="filename" class="span10" value="<?php if(isset($this->validation->filename)) echo $this->validation->filename; else echo "";?>">
						    
							<div id="moreUploads">												
							</div>
							<div id="moreUploadsLink" style="display:none;">
							
							<a href="javascript:addFileInput();">Add another File</a>
							
							
							</div>
						</div>
					</div>
                                        
                    <div class="control-group noxls">
                        <label class="control-label">Wiki</label>
                        <div class="controls">
                            <textarea class="span10" rows="10" id="wiki" name="wiki" ><?php echo @$this->validation->wiki; ?></textarea>
                        </div>
                    </div>
                    
                     <div class="control-group noxls">
                       <label class="control-label"> Zoom Image </label> 
                        <div class="controls"> 
                        <input type="checkbox" name="zoom" id="zoom" <?php echo @$this->validation->zoom?'checked="CHECKED"':''?>" />                          
                           <?php echo @$this->validation->error_zoom; ?>
                        </div>
                    	</div> 
                    
                    <div class="control-group noxls">
                        <label class="control-label">List Info</label>
                        <div class="controls">
                            <textarea class="span10" rows="10" id="listinfo" name="listinfo" ><?php echo @$this->validation->listinfo; ?></textarea>
                        </div>
                    </div>
					    <div class="control-group noxls">
                        <label class="control-label">Tags</label>
                        <div class="controls">
                            <input type="text" class="span10"  id="tags" name="tags" value="<?php echo @$this->validation->tags; ?>" data-role="tagsinput">
                        </div>
                    </div>
                   
                    
                     <div class="control-group noxls">
                        <label class="control-label">Price Check Setting<br/>Set Item Search URL</label>
                        <div class="controls"><span>http://supplyspy.net/</span>
                            <input type="text" class="span10"  id="searchquery" name="searchquery" value="<?php echo @$this->validation->searchquery; ?>">
                        </div>
                    </div>
                    
                    
                    <?php if ($this->session->userdata('usertype_id') == 1) { ?>
                        <div class="control-group noxls">
                            <label class="control-label">&nbsp;</label>
                            <div class="controls">
                                <input name="add" type="submit" class="btn btn-primary" value="Update Itemcode List"/>
                            </div>
                        </div>
                    <?php } ?>

                </div>
                </form>
                <div class="span6">
                    
                <?php if ($this->validation->id) { ?>
                            <table class="table table-bordered span12">
                                <tr>
                                    <?php
                                    if ($this->validation->lastquoted) {
                                        echo '<td>Last quoted: ' . $this->validation->lastquoted . '</td>';
                                    }
                                    ?>
                                    <?php
                                    if (@$itempricetrend) {
                                        echo '<td>Price Trend: ' . $itempricetrend . '</td>';
                                    }
                                    ?>
                                    <?php
                                    if (@$minprices) {
                                        if ($poitems) {
                                            ?>
                                            <td>
                                                <a class="btn btn-green" href="<?php echo site_url('admin/itemcode/poitems/' . $this->validation->id); ?>">View PO Items</a>
                                            </td>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tr>
                                </table>
                                
                            <?php
                            $seconds = time() - strtotime($this->validation->lastquoted);
                            $days = $seconds / (3600 * 24);
                            if ($days > 30)
                                echo "<b><font color='red'>Item has not been requoted within 30 days.</font></b>";
                            ?>
                        
                        <h3 class="box-header"><i class="icon-ok"></i>Company Prices for <?php echo $this->validation->itemcode; ?></h3>
                        <?php if ($this->validation->keyword) { ?>
                            <a class="btn btn-primary" onclick="searchprice('<?php echo $this->validation->keyword; ?>')">Amazon Lookup</a>
                            <a class="btn btn-primary" onclick="openamazon('<?php echo $this->validation->keyword; ?>')">Search</a>
                            <br/><br/>
                        <?php } ?>

                        <?php if (@$minprices) { ?>
                            <table class="table table-bordered span12">
                                <tr>
                                    <th>Company Name</th>
                                    <th>Date</th>
                                    <th>Purchase Price</th>
                                    <th>Substitute</th>
                                    <th>History</th>
                                </tr>
                                <?php
                                //print_r($minprices);
                                foreach ($minprices as $m) {
                                    ?>
                                    <tr>
                                        <td><?php echo $m->companyname; ?></td>
                                        <td><?php echo $m->quoteon; ?></td>
                                        <td>
                                            <div class="input-prepend input-append span6">
                                                <span class="add-on">$</span>
                                                <input type="text" class="span12" id="price<?php echo $m->company; ?>" name="price<?php echo $m->company; ?>" value="<?php echo $m->price; ?>" required/>
                                            </div>
                                        </td>
                                        <td><?php echo $m->substitute ? 'Substitute [' . $m->itemname . ']' : '-' ?></td>
                                        <td>
                                            <a href="javascript: void(0);" onclick="showhistory('<?php echo $m->company ?>', '<?php echo $m->itemcode ?>', '<?php echo $m->companyname ?>')"><i class="icon icon-search"></i></a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </table>
                        <?php } ?>
                        
                        <h3>Articles:</h3>
                        <a class="btn btn-primary" href="<?php echo site_url('admin/itemcode/addarticle/'.$this->validation->id);?>">Add New Article</a>
						<?php if(@$articles){?>
							<br/><br/>
                            <table class="table table-bordered span12">
                                <tr>
                                    <th>Title</th>
                                    <th>Posted</th>
                                    <th>Actions</th>
                                </tr>
								<?php foreach($articles as $article){?>
                                <tr>
                                    <td width="85%"><?php echo $article->title;?></td>
                                    <td width="10%"><?php echo $article->postedon;?></td>
                                    <td width="5%">
                                        <a href="<?php echo site_url('admin/itemcode/editarticle/'.$article->id.'/'.$this->validation->id);?>"><i class="icon icon-edit"></i></a>
                                        <a href="<?php echo site_url('admin/itemcode/deletearticle/'.$article->id.'/'.$this->validation->id);?>" onclick="return confirm('Are you sure to delete this record?');"><i class="icon icon-trash"></i></a>
                                    </td>
                                </tr>
								<?php }?>
							</table>
						<?php }?>
                        
                        <h3>Images:</h3>
                        <form method="post" class="form form-inline" action="<?php echo site_url('admin/itemcode/saveimage/'.$this->validation->id);?>" enctype="multipart/form-data">
                        	<input type="file" name="filename"/><input type="submit" value="Add" class="btn btn-primary"/>
                        </form>
                        <h3>Videos:</h3>
                        <form method="post" class="form form-inline" action="<?php echo site_url('admin/itemcode/savevideoid/'.$this->validation->id);?>" enctype="multipart/form-data">
                        	Video ID: <input type="text" name="videoid"/>&nbsp;&nbsp;&nbsp;
                        	<input type="submit" value="Add" class="btn btn-primary"/>
                        </form>
                        
						<?php if(@$images){?>
							<br/><br/>
                            <table class="table table-bordered span12">
                                <tr>
                                    <th>Image</th>
                                    <th>Delete</th>
                                </tr>
								<?php foreach($images as $image){?>
                                <tr>
                                    <td width="95%"><img width="50" src="<?php echo site_url('uploads/item/thumbs/'.$image->filename);?>"/></td>
                                    <td>
                                        <a href="<?php echo site_url('admin/itemcode/deleteimage/'.$image->id.'/'.$this->validation->id);?>" onclick="return confirm('Are you sure to delete this image?');"><i class="icon icon-trash"></i></a>
                                    </td>
                                </tr>
								<?php }?>
							</table>
						<?php }?>
			
						<?php if(@$videos){?>
							<br/><br/>
                            <table class="table table-bordered span12">
                                <tr>
                                    <th>Video ID</th>
                                    <th>Delete</th>
                                </tr>
								<?php foreach($videos as $video){?>
                                <tr>
                                    <td width="95%"><?php if(isset($video->filename)) echo $video->filename;?></td>
                                    <td>
                                        <a href="<?php echo site_url('admin/itemcode/deleteimage/'.$video->id.'/'.$this->validation->id);?>" onclick="return confirm('Are you sure to delete this video id?');"><i class="icon icon-trash"></i></a>
                                    </td>
                                </tr>
								<?php }?>
							</table>
						<?php }?>
						
            			<h3>Related Items:</h3>
            			<form method="post" action="<?php echo site_url('admin/itemcode/saverelateditem/'.$this->validation->id);?>">
                        	
                			<div style="height: 300px; max-height: 300px; overflow-x: hidden;overflow-y: auto;">
                				<table id="datatable" class="table table-bordered" width="95%">
                					<thead>
                                    <tr>
                                    	<th>Sel</th>
                                        <th>Item code</th>
                                        <th>Item name</th>
                                    </tr>
                                    </thead>
                                    <?php foreach($items as $item)if($this->validation->id != $item->id){?>
                                    <tr>
                                    	<td>
                                    		<input type="checkbox" name="item[]" value="<?php echo $item->id;?>" 
                                    		<?php if(in_array($item->id, $relateditems)){echo 'CHECKED';}?>/>
                                    	</td>
                                        <td><?php echo $item->itemcode;?></td>
                                        <td><?php echo $item->itemname;?></td>
                                    </tr>
                                    <?php }?>
                				</table>
                			</div>
                			<br/>
                			<input type="submit" value="Save" class="btn btn-primary"/>
            			</form>
						
                <?php } ?>
                
                
                
                					<div id="htmldefaultitemmessage"></div>
                                    <div class="grid-body no-border">
                                    <table id="datatable" class="table no-more-tables general defaulttab">
                                        <thead>
                                            <tr>                                                                                        
                                                <th style="width:10%"><font color="#fff">Manufacturer</font></th>
                                                <th style="width:10%"><font color="#fff">Part#</font></th>
                                                <th style="width:15%">Item Name</th>        
                                                <th style="width:10%"><font color="#fff">List Price</font></th>
                                                <th style="width:15%"><font color="#fff">Min. Qty.</font></th>         
                                                <th style="width:5%">&nbsp;</th>            
                                            </tr>
                                        </thead>

                                        <tbody>
							              
							               <tr>                                           
                                                <td class="v-align-middle">
                                                	<select style="width: 155px;font-size:12px;" class="form-control" id="manufacturerdefault">
                                                		<option value="">Select Manufacturer</option>
                                                		<?php foreach($manufacturers as $mf){?>
                                                			<option value="<?php echo $mf->id;?>"><?php echo $mf->title?></option>
                                                		<?php }?>
                                                	</select>
                                                </td>
                                                
                                                
                                                <td class="v-align-middle">
                                                	<input type="text" style="width: 60px;" placeholder="Part#" id="partnodefault"/>
                                                </td>

                                                <td class="v-align-middle"><input name="itemnamedefault"  id="itemnamedefault"></td>                                                                                                
                                                <td class="v-align-middle">
                                                	<input type="text" style="width: 60px;" placeholder="Price" id="pricedefault" name="pricedefault"/>                                    </td>

                                                 <td class="v-align-middle">
                                                	<input type="text"  style="width: 100px;" placeholder="Min Qty" id="minqtydefault" />
                                        </td></tr>				                                                         
                                        <tr><td><input type="button" onclick="addmasterdefaultoptions();" value="Add Another" name="addmasterdefault" id="addmasterdefault"/></td><td colspan="4"></td></tr>
                                        </tbody>
                                    </table>
                                   <input type="hidden" name="itemiddefault" id="itemiddefault" value="<?php echo @$defaultitemid;?>" />
                            </div>
                
                
						
                </div>


            </div>
    </div>
	<!--<div class="control-group">
                    <label class="control-label">Attachment</label>
                    <div class="controls">
                        <form action="<?php //echo base_url();    ?>admin/itemcode/fileupload" id="uploadfrm" name="uploadfrm" enctype="multipart/form-data" method="post">
      <input type="file" id="filesel" name="filesel"  >
      <input type="submit" name="btnupload" class="btn btn-primary" value="Upload" />

        </form>
    </div>
    </div>-->
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
