<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jRating.jquery.css" type="text/css" />
<?php if(isset($jsfile)) include $this->config->config['base_dir'].'templates/admin/gridfeed/'.$jsfile;?>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<?php echo '<script type="text/javascript">var permissionurl = "'.site_url('admin/purchaseuser/quotepermissions/').'";</script>';?>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jRating.jquery.js"></script>

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
	
<script type="text/javascript">

$(document).ready(function(){
	$('.datefield').datepicker();	
});
</script>

<script>
	function duplicate(id)
	{
		$("#duplicateid").val(id);
		$("#duplicatemodal").modal();
	}

	function viewitems(quoteid)
	{
		var serviceurl = '<?php echo base_url()?>admin/quote/getitemsajax/';
		//alert(serviceurl);
		$.ajax({
		      type:"post",
		      url: serviceurl,
		      data: "quote="+quoteid
		    }).done(function(data){
		        $("#quoteitems").html(data);
		        $("#itemsmodal").modal();
		    });
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
			$("#quoteitems").css({display: "none"});
			$("#quoteitemdetails").html(data);
			$("#quoteitemdetails").css({display: "block"});
			$("#quoteitemdetailsm").css({display: "block"});
			$("#quoteitemdetailsm").removeClass("hide");
			//$("#quoteitemdetailsm").modal();
		});
	}
	
	function viewcontractitems(quoteid)
	{
		var serviceurl = '<?php echo base_url()?>admin/quote/getcontractitemsajax/';		
		//alert(serviceurl);
		$.ajax({
		      type:"post",
		      url: serviceurl,
		      data: "quote="+quoteid
		    }).done(function(data){
		        $("#quotecontractitems").html(data);
		        $("#contractitemsmodal").modal();
		    });
	}
	
	function closepop(){
		$("#quoteitemdetails").html('');
		$("#quoteitemdetails").css({display: "none"});
		$("#quoteitemdetailsm").css({display: "none"});
		$("#quoteitems").css({display: "block"});
	}

	function checkpo()
	{
		if($("#ponum").val() == '')
		{
			alert('Please enter PO#');
			return false;
		}
		else
		{
			var url = '<?php echo base_url()?>admin/quote/checkpo/'+$("#ponum").val()+'/<?php echo $this->session->userdata('managedprojectdetails')->id;?>';
			
			$.ajax({
			     type: "GET",
				 url: url
				}).done(function(data) 
				{
					if(data=='Allow')
						$("#duplicateform").submit();
					else
						alert('PO# already exists');
				});
			return false;
		}
	}
</script>	

<script>
function quotepermission(quote, ponum)
{
	$('#permissionmodal').modal();
	$('#permissionwrapper').html('Loading...');
	$('#permissionponum').html(ponum);
	$.ajax({
	      type:"post",
	      data: "quote="+quote,
	      url: permissionurl
	    }).done(function(data){
		    $('#permissionwrapper').html(data);
	});
	
}


$(document).ready(function(){
	//alert("fdf");
	setTimeout(function() {
		$('.fixedrating').jRating({
			length:5,
			bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
			nbRates : 3,
			isDisabled:true,
			sendRequest: true,
			canRateAgain : true,
			decimalLength:1,
			onClick : function(element,rate) {
				alert('New Rating Saved');
			},
			onError : function(){
				alert('Error : please retry');
			},
			onSuccess : function(response){
				
			}
		});
	},500);
	
	$(document).on("click", ".table th", function () {
setTimeout(function() {
		$('.fixedrating').jRating({
			length:5,
			bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
			nbRates : 3,
			isDisabled:true,
			sendRequest: true,
			canRateAgain : true,
			decimalLength:1,
			onClick : function(element,rate) {
				alert('New Rating Saved');
			},
			onError : function(){
				alert('Error : please retry');
			},
			onSuccess : function(response){
				
			}
		});
	},500);
});   
	
});


function previewshipment(quoteid){
	
	if($('#hrefa_'+quoteid).text() == "Preview Shipment"){
		$('#hrefa_'+quoteid).text('Close Preview');
		$(jq("ship"+quoteid)).css('display','table');
		$('.box-header').css('display','block');
		$('#imageholder_'+quoteid).attr('src','<?php echo site_url('templates/admin/css/icons/minus.png')?>');	
	}else{
		$('#hrefa_'+quoteid).text('Preview Shipment');
		$(jq("ship"+quoteid)).css('display','none');
		$('.box-header').css('display','none');
		$('#imageholder_'+quoteid).attr('src','<?php echo site_url('templates/admin/css/icons/plus.gif')?>');	
	}
}

function jq( myid ) {
 
    return "#" + myid.replace( /(:|\.|\[|\])/g, "\\$1" );
 
}

</script>	
	
<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    
	    <div class="datagrid-example">
		<div style="width:100%;margin-bottom:20px;">
            <table id="MyGrid" class="table table-bordered datagrid" style="border-style: none !important;">
             <thead>
              <tr>
                <!-- <td>
               <div>-->
               
             <!--   <form class="form-inline" style="padding-top: 10px; padding-bottom:2px;" action="<?php echo site_url('admin/quote/index/'.$pid)?>" method="post">
                	
                	 <?php echo $addlink;?><br/></br>
                	 
                	Type: <select name="potype" style="width: 70px;">
                		<option value="All" <?php if(@$_POST['potype']=='All'){echo 'SELECTED';}?>>All</option>
                		<option value="Bid" <?php if(@$_POST['potype']=='Bid'){echo 'SELECTED';}?>>Bid</option>
                		<option value="Direct" <?php if(@$_POST['potype']=='Direct'){echo 'SELECTED';}?>>Direct</option>
                	</select>
                	&nbsp;&nbsp;
                	Date From: <input type="text" name="searchdatefrom" value="<?php echo @$_POST['searchdatefrom']?>" class="datefield" style="width: 70px;"/>
                	&nbsp;&nbsp;
                	Date To: <input type="text" name="searchdateto" value="<?php echo @$_POST['searchdateto']?>" class="datefield" style="width: 70px;"/>
                	&nbsp;&nbsp;
                	
                	Status: <select name="postatus" style="width: 170px;">
                		<option value="">All</option>
                		<option value="AWARDED - COMPLETE" <?php if(@$_POST['postatus']=='AWARDED - COMPLETE'){echo 'SELECTED';}?>>AWARDED - COMPLETE</option>
                		<option value="AWARDED - INCOMPLETE" <?php if(@$_POST['postatus']=='AWARDED - INCOMPLETE'){echo 'SELECTED';}?>>AWARDED - INCOMPLETE</option>
                		<option value="PENDING AWARD" <?php if(@$_POST['postatus']=='PENDING AWARD'){echo 'SELECTED';}?>>PENDING AWARD</option>
                		<option value="NO BIDS" <?php if(@$_POST['postatus']=='NO BIDS'){echo 'SELECTED';}?>>NO BIDS</option>
                		<option value="NO INVITATIONS" <?php if(@$_POST['postatus']=='NO INVITATIONS'){echo 'SELECTED';}?>>NO INVITATIONS</option>
                	</select>
                	&nbsp;&nbsp;
                	Name: <input type="text" style="width: 120px;" name="searchponum" value="<?php echo @$_POST['searchponum']?>"/>
                	&nbsp;&nbsp;
                    Company:
				    <select id="searchcompany" name="searchcompany" style="width: 150px;">
					 <option value=''>All Companies</option>
					  <?php if(count($companies)>0) { foreach($companies as $company){?>
						<option value="<?php echo $company->id?>"
							<?php if(@$_POST['searchcompany']==$company->id){echo 'SELECTED';}?>
							>
							<?php if(isset($company->title) && $company->title!="") echo $company->title?>
						</option>
					 <?php } }?>
				    </select>                	
                	&nbsp;&nbsp;
                	
                	<input type="submit" value="Filter" class="btn btn-primary"/>
                </form>-->
                
              <!--  </div>
                </td>-->
               </tr>
              </thead>
              <?php if ($counts) {?>
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
                       
                        <ul class="dropdown-menu"></ul>
                        </div>
                        <span>of <span class="grid-pages"></span></span>
                        <button type="button" class="btn grid-nextpage"><i class="icon-chevron-right"></i></button>
                    </div>
                </div>
                </th>
               </tr>
              </tfoot>
              <?php } ?>
            </table>
            
 <?php if (!$counts) { ?>
             <div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Shipments Pending Acknowledgement</div></div>              <?php } ?>
           </div>
                       
           </div>
           
            <?php
            if (@$shipmentarray)
            {
            ?>
            <h3 class="box-header" style="display:none;">Shipments:
            <?php /* if(count($shipmentarray)>0){?>
           <button class="btn btn-primary"  title="Please go to tracking page to accept shipments">Accept All</button>
           <?php } */ ?></h3>
		   <?php foreach($shipmentarray as $shipments){ if(@$shipments[0]->quote){ ?>	
           <table id="ship<?php echo $shipments[0]->quote;?>" class="table table-bordered" style="display:none;">
           	<tr>
           		<th>Item</th>
           		<th>Date Sent</th>
           		<th>Quantity</th>
           		<th>Reference #</th>
           		
           	</tr>
           	<?php foreach($shipments as $s){?>
           	<tr>
           		<td><?php echo $s->itemname;?></td>
           		<td id="shipdate<?php echo $s->id;?>"><?php echo date("m/d/Y h:i A", strtotime($s->shipdate));?></td>
           		<td id="acceptqty<?php echo $s->id;?>"><?php echo $s->quantity;?></td>
           		<td id="acceptinvoicenum<?php echo $s->id;?>"><?php echo $s->invoicenum;?></td>
           	</tr>
           	<?php }?>
           	<tr><td colspan="5" style="text-align:center";><a href="<?php echo base_url()?>admin/quote/track/<?php echo $shipments[0]->quote;?>"><span class="label label-pink">Track</span></a></td></tr>
           </table>
            <?php
            } }
            }
            ?>
           
           <?php if(!$costcodedata) {?> <a href="<?php echo site_url('admin/costcode/add');?>" target="_blank">
            No Costcodes Exist - Please Click Here to Create a Cost Code for "<?php echo $this->session->userdata('managedprojectdetails')->title; ?>"</a><?php } ?>
         </div>
      </div>
    </div>
</section>

        <div id="itemsmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Items<span id="minpriceitemcode"></span></h3>
        	</div>
        	<div class="modal-body" id="quoteitems">
        	
        	</div>
            
        </div>

        
        <div id="contractitemsmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Contract Items<span id="minpriceitemcode"></span></h3>
        	</div>
        	<div class="modal-body" id="quotecontractitems">
        	
        	</div>
            
        </div>
        
        
        <div id="quoteitemdetailsm" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<input style="float:right;margin-top:2px;" type="button" id="cls" name="cls" class="btn btn-green" value="close" onclick="closepop();" />
        		
        	</div>
        	<div class="modal-body" id="quoteitemdetails">
        	</div>
            
        </div>

      
        <div id="duplicatemodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	<form id="duplicateform" class="stylemoduleform" method="post" 
        	action="<?php echo site_url('admin/quote/duplicate');?>">
			<input type="hidden" id="duplicateid" name="id">
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Duplicate Quote/Purchase Order</h3>
        	</div>
        	<div class="modal-body">
	        	<table>
		        	<tr>
			        	<td><strong>PO #:</strong> <br/>
			        	</td>
			        	<td><input type="text" id="ponum" name="ponum"></td>
		        	</tr>
		        	<?php if(0){?>
		        	<tr>
			        	<td><strong>Type:</strong> <br/>
			        	</td>
			        	<td>
			        		<select name="potype">
			        			<option value="Bid">Bid</option>
			        			<option value="Direct">Direct</option>
			        		</select>
			        	</td>
		        	</tr>
	        	<?php }?>
	        	</table>
        	</div>
        	<div class="modal-footer">
        		<input type="button" class="btn btn-primary" value="Save" onclick="checkpo()"/>
        	</div>
            </form>
        </div>

        <div id="permissionmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>PO Permissions: <span id="permissionponum"></span></h3>
        	</div>
        	<div class="modal-body" id="permissionwrapper">
        		
        	</div>
        </div>