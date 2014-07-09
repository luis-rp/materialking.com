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
	function rfqformsubmit()
	{
		var d = $("#addtoquoteform").serialize();

        
        $.ajax({
            type: "post",
            url: rfqurl,
            data: d
        }).done(function(data) {
            if (data == 'Success')
            {
                alert('RFQ created for the item.')
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
                                                    <h2><a href="<?php echo site_url('site/item/' . $item->url); ?>"><?php echo $item->itemcode; ?></a></h2>
                                                    <p>
                                                        <?php echo $item->notes; ?>
                                                    </p>
                                                    <div class="area">
                                                        <span class="key"><strong>Item Name:</strong></span>
                                                        <span class="value"> <?php echo $item->itemname; ?></span>

                                                        <span class="key"><strong>Unit:</strong></span>
                                                        <span class="value"><?php echo $item->unit; ?></span>

                                                    </div>
                                                    <?php if($item->articles){?>
                                                    <br/>
                                                    <div class="area">
                                                    	<?php foreach($item->articles as $article){?>
                                                    		<a href="<?php echo site_url('site/article/'.$article->url);?>"><?php echo $article->title?></a><br/>
                                                    	<?php }?>
                                                    </div>
                                                    <?php }?>
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
                                                <?php if ($this->session->userdata('site_loggedin')){?>
                                            		<a class="btn btn-primary" href="javascript:void(0)" onclick="addtopo(<?php echo $item->id; ?>)">
                                                        <i class="icon icon-plus"></i> <br/>Add to RFQ
                                                    </a>
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