<?php //print_r(@$_POST);die;?>


<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">

<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/fg.menu.css" type="text/css">
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/site/assets/js/fg.menu.js"></script>

<style type="text/css">
	
	#menuLog { font-size:1.0em; margin:10px 20px 20px; }
	.hidden { position:absolute; top:0; left:-9999px; width:1px; height:1px; overflow:hidden; }
	
	.fg-button { clear:left; margin:0 4px 40px 0px; padding: .4em 1em; text-decoration:none !important; cursor:pointer; position: relative; text-align: center; zoom: 1; }
	.fg-button .ui-icon { position: absolute; top: 50%; margin-top: -8px; left: 50%; margin-left: -8px; }
	a.fg-button { float:left;  }
	button.fg-button { width:auto; overflow:visible; } /* removes extra button width in IE */
	
	.fg-button-icon-left { padding-left: 2.1em; }
	.fg-button-icon-right { padding-right: 2.1em; }
	.fg-button-icon-left .ui-icon { right: auto; left: .2em; margin-left: 0; }
	.fg-button-icon-right .ui-icon { left: auto; right: .2em; margin-left: 0; }
	.fg-button-icon-solo { display:block; width:8px; text-indent: -9999px; }	 /* solo icon buttons must have block properties for the text-indent to work */	
	
	.fg-button.ui-state-loading .ui-icon { background: url(spinner_bar.gif) no-repeat 0 0; }
</style>
<?php echo '<script>var costcodeurl = "' . site_url('site/getcostcodes') . '";</script>' ?>
<?php echo '<script>var quoteurl = "' . site_url('site/getquotes') . '";</script>' ?>

<script>
    function getquotecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: quoteurl,
            data: d
        }).done(function(data) {
            $("#additempo").html(data);
        	//document.getElementById("additempo").innerHTML = data;
        });
        	
    }

    function getcostcodecombo()
    {
    	var pid = $("#additemproject").val();
    	d = "pid="+pid;
    	$.ajax({
            type: "post",
            url: costcodeurl,
            data: d
        }).done(function(data) {
            $("#additemcostcode").html(data);
        });
    }

</script>
	
<script>
    $(document).ready(function() {
        InitChosen();
        $("#daterequested").datepicker();
    });


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

</script>
    
<script type="text/javascript">    
    $(function(){
    	// BUTTONS
    	$('.fg-button').hover(
    		function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
    		function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
    	);
    	
    	// MENUS    	
		$('#hierarchy').menu({
			content: $('#hierarchy').next().html(),
			crumbDefaultText: ' '
		});
		
		$('#hierarchybreadcrumb').menu({
			content: $('#hierarchybreadcrumb').next().html(),
			backLink: false
		});
    });
</script>
<?php echo '<script>var rfqurl = "' . site_url('site/additemtoquote') . '";</script>' ?>
<script>
	function addtopo(itemid)
	{
		$("#addform").trigger("reset");
		$("#additemid").val(itemid);
		//$('#additemproject').attr('selectedIndex',0);
		//$('#additemproject option:first-child').attr("selected", "selected");
		//document.getElementById('additemproject').value=2;
		$('#additemqty').val('');
		$("#additempo").html('<select name="quote" required></select>');
		$('#additemcostcode').html('<select name="costcode" required></select>');
		getquotecombo();
		getcostcodecombo()
		$("#addtoquotemodal").modal();
	}
	
	function addtopo1(quote)
	{
		//var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;

		var string = '<h3>RFQ created for the item.</h3><div><a target="_blank" href="<?php echo site_url("admin/quote/update/"); ?>/'+quote+'">Click here to view the Quote</a><br/><br/><br/><button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>';
		$("#modalhtm").html(string);
		$("#addtoquotemodal1").modal();

	}
	
	function rfqformsubmit()
	{
		var d = $("#addtoquoteform").serialize();
		var quote = $('[name="quote"]').val();	
        
        $.ajax({
            type: "post",
            url: rfqurl,
            data: d
        }).done(function(data) {
            if (data == 'Success')
            {
               addtopo1(quote);
            }
            else
            {
                alert(data);
            }
            $("#addtoquotemodal").modal('hide');
        });
        return false;
	}
</script>

<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script>
	function getlatlong()
	{
		var address = $("#inputLocation").val();
		//alert(address);
		if(address)
		{
    		var geocoder = new google.maps.Geocoder();
            geocoder.geocode({ 'address': address }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    $("#latitude").val(latitude);
                    $("#longitude").val(longitude);
                    //alert("Latitude: " + latitude + "\nLongitude: " + longitude);
                } else {
                    //alert("Request failed.")
                }
            });
		}
		$("#searchform").submit();
        return true;
	}
</script>
<script>
    $(document).ready(function() {
        InitChosen();

        $('#search_form').submit(function() {
            $('#search_type').val('suppliers');
            $('#search_form').attr('action', "<?php echo base_url('site/search'); ?>");
        });

    });


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

    function fetchOrder()
    {
        get_by = $("#inputSortBy").val();
        orderdir = $("#inputOrder").val();
        if(get_by=="all"){
            get_by = "";
        }
        $("#get_by").val(get_by);
        $("#filterorderdir").val(orderdir);
        $("#searchform").submit();
        return true;
    }
</script>


<?php //print_r($userquotes);?>
<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                	<div class="breadcrumb-pms"><ul class="breadcrumb"><?php echo $breadcrumb;?></ul></div>
                    <h1 class="page-header"><?php echo $page_titile;?></h1>

                    <div class="properties-rows">
                        <div class="row">
                            <?php if ($norecords) { ?>
                                <div class="alert alert-error" style="margin-left:30px;">
                                    <button data-dismiss="alert" class="close" type="button">X</button>
                                    <strong> <?php echo $norecords; ?></strong> <a href="<?php echo site_url('site/items'); ?>">View All Listing</a>
                                </div>
                            <?php } ?>

                            <?php
                            $i = 3;
                            foreach ($items as $item) {
                                $i++;
                                $item->url = urlencode($item->url);
                                ?>
                                <div class="property span9">
                                    <div class="row">
                                        <div class="image span3">
                                            <div class="content">
                                                <?php if ($item->item_img) { ?>
                                                    <img style="max-height: 120px; padding: 20px;" height="120" width="120" src="<?php echo site_url('uploads/item/' . $item->item_img) ?>" alt="">
                                                <?php } else { ?>
                                                    <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                                <?php } ?>

                                            </div>
                                        </div>

                                        <div class="body span6">
                                            <div class="title-price row">
                                                <div class="title span4">
                                                    <h2 style="word-wrap:break-word;"><a href="<?php echo site_url('site/item/' . $item->url); ?>"><?php echo $item->itemcode; ?></a></h2>
                                                    <p>
                                                        <?php echo $item->notes; ?>
                                                    </p>
                                                    <div class="area">
                                                        <span class="key"><strong>Item Name:</strong></span>
                                                        <span class="value"> <?php echo $item->itemname; ?></span>

                                                        <span class="key"><strong>Unit:</strong></span>
                                                        <span class="value"><?php echo $item->unit; ?></span>

                                                    </div>
                                                    <?php /* if($item->articles){?>
                                                    <br/>
                                                    <div class="area">
                                                    	<?php foreach($item->articles as $article){?>
                                                    		<a href="<?php echo site_url('site/article/'.$article->url);?>"><?php echo $article->title?></a><br/>
                                                    	<?php }?>
                                                    </div>
                                                    <?php } */?>
                                                    <?php if ($this->session->userdata('site_loggedin')){?>
                                            		<a class="btn btn-primary" style="margin-left:30px;" href="javascript:void(0)" onclick="addtopo(<?php echo $item->id; ?>)">
                                                        <i class="icon icon-plus"></i> <br/>Add to RFQ
                                                    </a>
                                                <?php }else{?>
                                               		<a class="btn btn-primary" style="margin-left:30px;" href="javascript:void(0)" onclick="$('#createmodal').modal();">
                                                        <i class="icon icon-plus"></i> <br/>Add to RFQ
                                                    </a>
                                                <?php } ?>
                                                </div>
                                                <?php if($item->minprice && $item->maxprice){?>
                                                <div class="price">
                                                	<?php  if($item->offercount>0) echo $item->offercount." Offers<br>"; ?>
                                                	$<?php echo $item->minprice; ?> - $<?php echo $item->maxprice; ?> 
                                                </div>
                                                <?php }?>
                                                <?php if($item->hasdeal){?>
                                                <div class="price2">
                                                <img src="<?php echo base_url(); ?>templates/site/assets/img/specialoffer.png" alt="" width="55" height="55">
                                                </div>
                                                <?php }?>
                                                

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php } ?>
                        </div>
                    </div>

                    <div class="pagination pagination-centered">
                        <?php $this->view('site/paging'); ?>
                    </div>
                </div>

                <div class="sidebar span3">
                	
                                    
                    <h2>Item Filter</h2>
                    
                    <div>
                        
                        <form id="categorysearchform" name="categorysearchform" method="post" action="<?php echo base_url('site/items');?>">
                            <input type="hidden" name="keyword" value="<?php echo isset($keyword)?$keyword:"";?>"/>
                            <input type="hidden" id="breadcrumb" name="breadcrumb"/>
                            <input type="hidden" id="formcategory" name="category" value="<?php echo isset($_POST['category'])?$_POST['category']:"";?>"/>
                            
                            <div class="location control-group">
                            	<?php $this->load->view('site/catmenu.php');?>
                            </div>
                        </form>
                       
                    </div>
                    
                    <div style="clear:both;"></div>
                     <div class="breadcrumb-pms" style="width:200px;" ><ul class="" style="margin-left: -8px;"><?php if(isset($breadcrumb2) && $breadcrumb2!="") echo $breadcrumb2;?></ul></div>
                   
                </div>

            </div>
        </div>
    </div>
</div>

        <!-- Modal -->
        <div class="modal hide fade" id="addtoquotemodal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title nobottompadding" id="myModalLabel">Request for quote</h3>
                    </div>
                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" onsubmit="rfqformsubmit(); return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                            <h4>Select Project</h4>
                            <select id="additemproject" onchange="getquotecombo();getcostcodecombo();">
                                <option value="">Select</option>
                                <?php foreach($projects as $up){?>
                                	<option value="<?php echo $up->id?>"><?php echo $up->title;?></option>
                                <?php }?>
                            </select>
                            
                            <h4>Select PO</h4>
                            <span id="additempo">
                            <select name="quote" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>
                            
                            <a href="javascript:void(0)" target="_blank" onclick="var pid=$('#additemproject').val();if(pid){$(this).attr('href','<?php echo site_url('admin/quote/add/');?>/'+pid);$('#additemproject').val('');$('#addtoquotemodal').modal('hide');}else{return false;}">Add PO</a>
                            
                            <h4>Quantity</h4>
                            <input type="text" id="additemqty" name="quantity" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');" required/>
                            <h4>Costcode</h4>
                            <span id="additemcostcode">
                            <select name="costcode" required>
                                <?php if(0)foreach($userquotes as $uq){?>
                                	<option value="<?php echo $uq->id?>"><?php echo $uq->ponum;?></option>
                                <?php }?>
                            </select>
                            </span>
                            
                            <h4>Date Requested</h4>
                            <input type="text" id="daterequested" name="daterequested"/>
                            
                            <br/><br/>
                            <div>
                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="rfqformsubmit();">Add</button>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="modal hide fade" id="addtoquotemodal1">
            <div class="modal-dialog">
                <div class="modal-content">

                    <form id="addtoquoteform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" return false;">
                        <input type="hidden" id="additemid" name="itemid" value=""/>
                        <div class="modal-body">
                        <div id="modalhtm">
                        
                        </div>                              
                        </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        
        
        
        
        
        
         <?php if ($data2['norecords']=="") { ?>    
     <div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                   <?php if ($data2['norecords']!="") { ?> <h1 class="page-header">Suppliers List</h1><?php } ?>
                    <div class="properties-rows">
                        <div class="row">

                            <?php if ($data2['norecords']) { ?>
                                <div class="alert alert-error" style="margin-left:30px;">
                                    <button data-dismiss="alert" class="close" type="button">X</button>
                                    <strong> <?php echo $data2['norecords']; ?></strong> <a href="<?php echo site_url('site/items'); ?>">View All Listing</a>
                                </div>
                            <?php } ?>

                            <?php
                            $i = 3;
                            foreach ($data2['suppliers'] as $supplier) {
                                $i++
                                ?>
                                <div class="property span9" style="width:auto">
                                    <div class="row">
                                        <div class="image1 span3">
                                            <div class="content">
                                                <?php if ($supplier->logo) { ?>
                                                    <img style="padding: 20px; vertical-align: middle;" src="<?php echo site_url('uploads/logo/thumbs/' . $supplier->logo) ?>" alt="">
                                                <?php } else { ?>
                                                    <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                                <?php } ?>

                                            </div>
                                        </div>

                                        <div class="body1 span6">
                                            <div class="title-price row">
                                                <div class="title1 span4">
                                                    <h2><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>"><?php echo $supplier->title; ?></a></h2>
                                                </div>
                                                <?php if (isset($supplier->city) && isset($supplier->state)) { ?>
                                                <div class="price1">
                                                     <?php echo $supplier->city.",&nbsp;".$supplier->state;
													?>
                                                </div>
                                                <?php } ?>
                                              </div>

                                            <div class="location"><?php echo $supplier->contact; ?></div>
                                            <p><?php echo $supplier->shortdetail; ?></p>
                                            <div class="area">
                                            <?php if ($this->session->userdata('site_loggedin')){?>
                                                <?php echo $supplier->joinstatus; ?>
                                            <?php }else{?>
                                            <input type="button" value="Join" onclick="$('#createmodal').modal();" class="btn btn-primary arrow-right"/>
                                            <?php }?>&nbsp;<div class="btn btn-primary arrow-right"><a href="<?php echo site_url('site/supplier/' . $supplier->username); ?>">View Profile</a></div>&nbsp;<div class="btn btn-primary arrow-right"><a href="<?php echo site_url('store/items/' . $supplier->username); ?>">Go to Store</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>

                    <!-- <div class="pagination pagination-centered">
                        <?php // $this->view('site/paging'); ?>
                    </div> -->
                </div>

                <div class="sidebar span3">
                    <h2>Supplier Filter</h2>
                    <div class="property-filter widget">
                        <div class="content">
                            <form id="searchform" method="post" action="" onsubmit="return getlatlong()">
                            
                            	<input type="hidden" id="latitude" name="lat"/>
                            	<input type="hidden" id="longitude" name="lng"/>
                                <input type="hidden" id="get_by" name="get_by" value="<?php echo isset($_POST['get_by'])? $_POST['get_by'] : "" ?>" />
                                <input type="hidden" id="filterorderdir" name="orderdir" value="<?php echo isset($_POST['orderdir']) ? $_POST['orderdir'] : "" ?>" />
                                <div class="location control-group">
                                    <label class="control-label" for="inputLocation">
                                        Location
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="inputLocation" name="location" value="<?php echo ($this->input->post('location')) ? $this->input->post('location') : $data2['my_location']; ?>">
                                        <?php if (0) { ?>
                                            <select id="inputLocation" name="citystates">
                                                <?php foreach ($data2['citystates'] as $cst) { ?>
                                                    <option value="<?php echo $cst->citystate; ?>" <?php
                                                    if ($cst->citystate == @$_POST['citystates']) {
                                                        echo 'selected="selected"';
                                                    }
                                                    ?>><?php echo $cst->citystate; ?></option>
                                                        <?php } ?>
                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="type control-group">
                                    <label class="control-label" for="inputType">
                                        Industry
                                    </label>
                                    <div class="controls">
                                        <select id="typei" name="typei">
                                            <option></option>
                                            <?php
                                            foreach ($data2['types'] as $t)
                                                if ($t->category == 'Industry') {
                                                    ?>
                                                    <option value='<?php echo $t->id; ?>' <?php
                                                    if ($t->id == @$_POST['typei']) {
                                                        echo 'selected="selected"';
                                                    }
                                                    ?>><?php echo $t->title; ?></option>
    <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="type control-group">
                                    <label class="control-label" for="inputType">
                                        Manufacturer
                                    </label>
                                    <div class="controls">
                                        <select id="typem" name="typem">
                                            <option></option>
                                            <?php
                                            foreach ($data2['types'] as $t)
                                                if ($t->category == 'Manufacturer') {
                                                    ?>
                                                    <option value='<?php echo $t->id; ?>' <?php
                                                    if ($t->id == @$_POST['typem']) {
                                                        echo 'selected="selected"';
                                                    }
                                                    ?>><?php echo $t->title; ?></option>
                                           <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                    <?php if ($data2['found_records']) { ?>
                                    <div class="form-actions">
                                        <div class="notfound"><?php echo $data2['found_records']; ?></div>
                                    </div>
                                    <?php } ?>

                                <div class="form-actions">
                                    <input type="submit" value="Filter Now!" class="btn btn-primary btn-large">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
    <?php } ?>
    
    
    
     <div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
    <?php if(!empty($datatags)){?><div><h1 class="page-header">Tags:</h1><ul class="tags"><?php 
   foreach ($datatags as $tag){
    	$tag = trim($tag);?>
    <li><a class="tag" target="_blank" href="<?php echo site_url("site/tag/".str_replace('%2F', '/', urlencode(urlencode($tag))));?>"><?php echo $tag;?></a></li>
    <?php } ?></ul><?php } ?></div>
    </div></div></div></div></div>