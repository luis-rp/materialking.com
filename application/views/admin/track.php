<?php echo '<script type="text/javascript">var createbillurl="'.site_url('admin/quote/createbill').'";</script>'?>
<?php echo '<script type="text/javascript">var getcustomerdataurl = "' . site_url('admin/quote/getcustomerdata') . '";</script>'; ?>
<?php echo '<script type="text/javascript">var previewbillurl ="'.site_url('admin/quote/previewbill').'";</script>'?>
<?php echo '<script>var insertserviceandlabor="'.site_url('admin/itemcode/insertserviceandlabor').'";</script>'?>
<script type="text/javascript">
$(document).ready(function(){
	$('.dis_td').attr('disabled','disabled');
});
 </script>
<?php
$combocompanies = array();
$messagecompanies = array();
$recsum = 0;
$qntsum = 0;
foreach ($awarded->items as $q) {
    $recsum = $recsum + $q->received;
    $qntsum = $qntsum + $q->quantity;
    if ($q->received < $q->quantity) {
        if (isset($combocompanies[$q->company])) {
            $combocompanies[$q->company]['value'][] = $q->id;
        } else {
            $combocompanies[$q->company] = array();
            $combocompanies[$q->company]['value'] = array($q->id);
            $combocompanies[$q->company]['id'] = $q->company;
            $combocompanies[$q->company]['label'] = $q->companyname;
        }
    }
    if (isset($messagecompanies[$q->company])) {
        $messagecompanies[$q->company]['value'][] = $q->id;
    } else {
        $messagecompanies[$q->company] = array();
        $messagecompanies[$q->company]['value'] = array($q->id);
        $messagecompanies[$q->company]['id'] = $q->company;
        $messagecompanies[$q->company]['label'] = $q->companyname;
    }
}
if ($qntsum) {
    $per = number_format(($recsum / $qntsum) * 100, 2);
}else{
    $per = 0;
}
$per .='%';
//$per = '80%';
//print_r($combocompanies);die;
?>
<?php echo '<script type="text/javascript">var senderrorurl = "' . site_url('admin/message/senderror/' . $quote->id) . '";</script>'; ?>
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jquery-ui.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/jRating.jquery.css" type="text/css" />
<script type="text/javascript" src="<?php echo base_url(); ?>templates/admin/js/jRating.jquery.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/progressbar.css" media="all" rel="stylesheet" type="text/css" >
<link rel="stylesheet" href="<?php echo base_url(); ?>templates/admin/css/flipclock.css" media="all" type="text/css">
<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
-->
<script src="<?php echo base_url(); ?>templates/admin/js/flipclock.js"></script>
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>-->
    <script src="<?php echo base_url(); ?>templates/admin/js/jquery.percentageloader-0.1.js"></script>
<script type="text/javascript">
	var clock;
		$(document).ready(function() {
			var currentDate = new Date("08 29, 2014");
			var pastDate  = new Date("08 19, 2014");
			var diff = currentDate.getTime() / 1000 - pastDate.getTime() / 1000;
				clock = $('.clock').FlipClock(diff, {
					clockFace: 'DailyCounter'
				});
				<?php $greaterseconds = ""; $seconds="";  foreach ($awarded->items as $q) {
                    	 if(($q->quantity - $q->received) >0)
                    	$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($awarded->awardedon);
                    	elseif ($q->quantity=="")
						$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d H:i:s'));
                    	else {
                    		$greaterreceived = "";
                    		foreach ($awarded->invoices as $invoice) {
                    			foreach ($invoice->items as $item) {
                    				if($item->awarditem==$q->id){
                    					$receiveddate =$item->receiveddate;
                    					if($greaterreceived!=""){
                    						if(strtotime($greaterreceived)<strtotime($receiveddate))
                    						$greaterreceived = $receiveddate;
                    					}else
                    					$greaterreceived = $receiveddate;
                    				}
                    			}
                    		}
                    		//echo "g=".$greaterreceived."-G";
                    		$seconds = strtotime($greaterreceived) - strtotime($awarded->awardedon);
                    	}
                    	if($greaterseconds!=""){
                    		if($greaterseconds<$seconds)
                    		$greaterseconds = $seconds;
                    	}else
                    	$greaterseconds = $seconds;
						}
						$days    = floor($greaterseconds / 86400);
                    	$hours   = floor(($greaterseconds - ($days * 86400)) / 3600);
                    	$minutes = floor(($greaterseconds - ($days * 86400) - ($hours * 3600))/60);
                    	$seconds = floor(($greaterseconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));
                    	//echo $days." d"." ".$hours." h".$minutes." m"; ?>
				clock.setTime(<?php echo $greaterseconds;?>);
				clock.stop();
				//setTimeout(function() { clock.stop(function() { }) }, 1000);
			});
			//setTimeout(function() { clock.stop(function() { }) }, 1000);
</script>
<script>
    $(document).ready(function() {
    	$("#feedbackformwrapper").hide();
    	$(".datefield").datepicker();
    	$("#customerduedate").datepicker();
    	$('.basic').jRating({
			length:5,
			bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
			nbRates : 1000,
			sendRequest: false,
			canRateAgain : true,
			decimalLength:1,
		    onClick : function(element,rate) {
	         $("#feedbackrating").val(rate);
	        },
			onError : function(){
				alert('Error : please retry');
			}
		});
    	$('.fixedrating').jRating({
			length:5,
			bigStarsPath : '<?php echo site_url('templates/admin/css/icons/stars.png');?>',
			nbRates : 0,
			isDisabled:true,
			sendRequest: false,
			canRateAgain : false,
			decimalLength:1,
			 onClick : function(element,rate) {
		        },
			onError : function(){
				alert('Error : please retry');
			}
		});
        <?php if ($per == '0.00%') { ?>
        //$("#timelineid").attr("class", "bar madras");
        $("#timelineid").attr("class", "bar");
        $("#timelineid").css("width", '100%');
        <?php } else { ?>
        $("#timelineid").css("width", '<?php echo $per; ?>');
        <?php } ?>
    });
    function defaultinvoicenum(qid,cnt)
    {
    	if(cnt > 1)
    	{
	        if (confirm('Do you want to make this invoice # default for this session?'))
	        {
	            $("#makedefaultinvoicenum").val('1');
	            $(".invoicenum").val($("#invoicenum" + qid).val());
	        }
    	}
    }
    function defaultreceiveddate(qid,cnt)
    {
    	if(cnt > 1)
    	{
	        if (confirm('Do you want to make this date default for this session?'))
	        {
	            $("#makedefaultreceiveddate").val('1');
	            $(".receiveddate").val($("#receiveddate" + qid).val());
	        }
    	}
    }
    function selectbycompany()
    {
        var ids = $("#combocompany").val();
        if (ids == '')
        {
            return false;
        }
        $('.select-for-complete').prop('checked', false);
        ids = ids.split(',');
        for (var i = 0; i < ids.length; i++)
        {
            var id = ids[i];
            $('#select' + id).prop('checked', true);
        }
        completeselected();
    }
    function completeselected()
    {
        var selected = new Array();
        $('.receivedqty').val('');
        $('.select-for-complete').each(function()
        {
            if ($(this).is(':checked'))
            {
                selected.push($(this).val());
                var selectid = $(this).attr('id');
                var dueid = selectid.replace('select', 'due');
                var dueamount = $("#" + dueid).html();
                var receivedid = selectid.replace('select', 'received');
                $("#" + receivedid).val(dueamount);
            }
        });
        if (selected.length > 0)
        {
            $('#completemodel').modal();
        }
    }
    function errorselected()
    {
        var selected = new Array();
        var quantities = new Array();
        var invoicenums = new Array();
        var dates = new Array();
        $('.select-for-error').each(function()
        {
            if ($(this).val() != '')
            {
                id = $(this).attr('id');
                id = id.replace('error','');
                selected.push($(this).val());
                quantities.push($('#received'+id).val());
                invoicenums.push($('#invoicenum'+id).val());
                dates.push($('#receiveddate'+id).val());
            }
        });
        if (selected.length > 0)
        {
            var errors = selected.join(',');
			var d = "errors=" + errors+"&quantities="+quantities.join(',')+"&invoicenums="+invoicenums.join(',')+"&dates="+dates.join(',')+"&comments="+$('#comments').val();
            $.ajax({
                type: "post",
                data: d,
                url: senderrorurl
            }).done(function(data) { //alert(data);
               window.location = window.location;
            });
        }
    }
    function showErrorModal()
	{
		$('#commentmodal').modal();
		$('#commentwrapper').html('Loading...');
	}
	function getCommentdata()
	{
		var comments = $("#commentdata").val();
		$("#comments").val(comments);
		errorselected();
	}
    function showInvoice(invoicenum,invoicequote)
    {
        $("#invoicenum").val(invoicenum);
        $("#invoicequote").val(invoicequote);	
        $("#invoiceform").submit();
    }
    
    function showBill(invoiceid,invoicequote){
    	
    	$("#billid").val(invoiceid);
        $("#billquote").val(invoicequote);	
        $("#billform").submit();
    }   
    
</script>
<script>
	function showfeedbackform(companyid, companyname)
	{
		$("#feedbackcompanyname").html(companyname);
		$("#feedbackcompany").val(companyid);
		$("#feedbackformwrapper").show();
	}
	function checkfeedback()
	{
		if(!$("#feedbackrating").val())
		{
			alert('Please give rating');
			return false;
		}
		return true;
	}
</script>
<?php echo '<script type="text/javascript">var accepturl = "' . site_url('admin/quote/acceptshipment') . '";</script>'; ?>
<?php echo '<script type="text/javascript">var accepallturl = "' . site_url('admin/quote/acceptall') . '";</script>'; ?>
<script>
function acceptshipment(ai,si)
{
    $.ajax({
        type: "post",
        data: "id=" + si,
        url: accepturl
    }).done(function(data) {
    	var quantity = $("#acceptqty"+si).html();
    	var invoicenum = $("#acceptinvoicenum"+si).html();
    	$("#received"+ai).val(quantity);
    	$("#invoicenum"+ai).val(invoicenum);
    	//$("#receiveddate"+ai).val('<?php echo date('m/d/Y');?>');
    	$("#trackform").submit();
    });
}
function acceptall()
{
    $.ajax({
        type: "post",
        data: "quote=" + '<?php echo $quote->id;?>',
        url: accepallturl
    }).done(function(data) {
		<?php if(count($shipments2)>1) { foreach($shipments2 as $s) // if($s->accepted == 0){?>
		$("#received"+<?php echo $s->awarditem;?>).val(<?php echo $s->quantity;?>);
		$("#invoicenum"+<?php echo $s->awarditem;?>).val('<?php echo $s->invoicenum;?>');
		//$("#receiveddate"+<?php echo $s->awarditem;?>).val('<?php echo date('m/d/Y');?>');
		<?php // }
		}else{
		foreach($shipments as $s) if($s->accepted == 0){?>
		$("#received"+<?php echo $s->awarditem;?>).val(<?php echo $s->quantity;?>);
		$("#invoicenum"+<?php echo $s->awarditem;?>).val('<?php echo $s->invoicenum;?>');
		//$("#receiveddate"+<?php echo $s->awarditem;?>).val('<?php echo date('m/d/Y');?>');
		<?php  } 
		}
		?>
		$("#trackform").submit();
    });
}
function addalltobill(quoteid,cnt,prevbillitems,billcnt,currentbillitems){
	
	$('#prevbillitemcount').val(billcnt);
	$('#prevbillawarditems').val(prevbillitems);	
	$('#billawarditems').val(currentbillitems);
	$('#billwindow').html('');	
	$('#billitemcount').val(billcnt);	
	$('#billwindow').html(cnt+' items on bill <br> Done adding Bill items? <br> <input type="button" value="Create Bill" onclick="createallbill('+quoteid+')" id="createallbillbtn" />');
	$('#billingtype').val('all');
	$('#rem'+quoteid).css('display','block');
}
function addtobill(quoteid, awardid,isprev){
		
	awarditemstr = $('#billawarditems').val();
	var res = awarditemstr.split(",");
	if($.inArray(awardid,res) ==-1){
	if($('#billingtype').val() == "all")
	$('#billwindow').html('');		
	
	$('#billitemcount').val(parseInt($('#billitemcount').val())+1); 
	if($('#billawarditems').val()=="")
	$('#billawarditems').val(awardid);
	else	
	$('#billawarditems').val($('#billawarditems').val()+","+awardid);
	$('#billwindow').html($('#billitemcount').val()+' items on bill <br> Done adding Bill items? <br> <input type="button" value="Create Bill" onclick="createallbill('+quoteid+')" id="createallbillbtn" />');
	$('#billingtype').val('single');
	
	
	if(isprev=="1"){
		if($('#prevbillitemcount').val()=="0")
			$('#prevbillawarditems').val(awardid);
		else
			$('#prevbillawarditems').val($('#prevbillawarditems').val()+","+awardid);
			
		$('#prevbillitemcount').val(parseInt($('#prevbillitemcount').val())+1)	
		
	}
	
	}else
	alert('item already exists');
		
	$('#rem'+awardid).css('display','block');
	
}
function createallbill(quoteid){
	
	if($('#prevbillitemcount').val()>0)
	$('#previtemmsg').html(($('#prevbillitemcount').val()+' item/s on Bill already exists on previous Bill'));
	$('#billmodal').modal();
	$('#customerquoteid').val(quoteid);	
}
function billformsubmit()
	{
		var quote = $('#customerquoteid').val();
		var d = $("#createbillform").serialize();
        $.ajax({
            type: "post",
            url: createbillurl,
            data: d
        }).done(function(data) {
            if (data)
            {                
                showbillpdf(data);
            }
            
            $("#billmodal").modal('hide');
            $('#billawarditems').val('');
			$('#billwindow').html('');	
			$('#billitemcount').val('0');	
			$('#billingtype').val('');
        });
        return false;
	}
	
	
function showbillpdf(data)
	{
		//var serviceurl = '<?php echo base_url()?>admin/itemcode/ajaxdetail/'+ itemid;
		var string = '<h3>Bill sent to Customer</h3><div><a target="blank" href="<?php echo base_url()?>uploads/pdf/bill_'+data+'.pdf" class="btn btn-primary btn-xs btn-mini">View PDF</a><br/>';	
		$("#modalhtm").html(string);	
		$("#billsuccessmodal").modal();
	}	
	
function setcustomerdata(id)
{
	$('#customername').val('');
    $('#customeraddress').val('');
    $('#customeremail').val('');
    
    if(id!=""){	
    $.ajax({
        type: "post",
        data: "id=" + id,
        sync:false,
        url: getcustomerdataurl
    }).done(function(data) {
    	var obj = $.parseJSON(data);
    	
    	$('#customername').val(obj.name);
    	$('#customeraddress').val(obj.address);
    	$('#customeremail').val(obj.email);
    	$('#customername').prop("readonly",true);
    	$('#customeraddress').prop("readonly",true);
    	$('#customeremail').prop("readonly",true);
    });
    }else{
    	$('#customername').val('');
   	 	$('#customeraddress').val('');
   	 	$('#customeremail').val('');
   	 	$('#customername').prop("readonly",false);
    	$('#customeraddress').prop("readonly",false);
    	$('#customeremail').prop("readonly",false);
    }
}
function previewbillitems(){
	
	var quote = $('#customerquoteid').val();
	var awarditemstr = $('#billawarditems').val();
	var res2 = awarditemstr.split(",");
	var prevawarditemstr = $('#prevbillawarditems').val();
	var prevres2 = prevawarditemstr.split(",");
	
	var itemstr = "";
	
	$.ajax({
            type: "post",
            url: previewbillurl,
            sync:false,
            data: "quote=" + quote,
        }).done(function(data) {
            if (data)
            {  
               itemstr += '<table id="previewtab">';	 
               var obj = $.parseJSON(data);         
               $.each(obj, function( index, value ) {
               			
				if($.inArray(value.id,res2) !=-1){	
					itemstr +='<tr id="'+value.id+'" ><td>'+value.itemname+'</td><td>'+value.quantity+'</td><td>'+value.ea+'</td>';
					if($.inArray(value.id,prevres2) !=-1){
						itemstr +='<td>*already on previous bill</td><td><a href="javascript:void(0)" onclick="removeitem('+value.id+','+quote+')">Remove</a></td></tr>';	
               		}else{
               			itemstr +='<td>&nbsp;</td><td>&nbsp;</td></tr>';
               		}
				}
               });
               itemstr += '</table>';	 
               $("#bpitems").html(itemstr);
               $("#billmodal").modal('hide');
               $("#billpreviewmodal").modal();
            }
            
            
            /*$('#billawarditems').val('');
			$('#billwindow').html('');	
			$('#billitemcount').val('0');	
			$('#billingtype').val('');*/
        });
	
}
function removeitem(id,quote){
	
	var awarditemstr = $('#billawarditems').val();
	var values = removeCsvVal(awarditemstr, id);
	if(values!="")
	$('#billawarditems').val(values);
	else
	$('#billawarditems').val('');
	
	$('#billitemcount').val(parseInt($('#billitemcount').val())-1);	
	$('#prevbillitemcount').val(parseInt($('#prevbillitemcount').val())-1);	
	$('#previtemmsg').html(parseInt($('#prevbillitemcount').val())+' item/s on Bill already exists on previous Bill');
	$('#billwindow').html($('#billitemcount').val()+' items on bill <br> Done adding Bill items? <br> <input type="button" value="Create Bill" onclick="createallbill('+quote+')" id="createallbillbtn" />');
	$("#"+id).css('display','none');
	$("#"+id).remove();
	$('#rem'+id).css('display','none');
}
function removeCsvVal(source,toRemove)      //source is a string of comma-seperated values,
{                                                    //toRemove is the CSV to remove all instances of
    var sourceArr = source.split(",");               //Split the CSV's by commas
    var toReturn  = "";                              //Declare the new string we're going to create
    for (var i = 0; i < sourceArr.length; i++)       //Check all of the elements in the array
    {
        if (sourceArr[i] != toRemove)                //If the item is not equal
            toReturn += sourceArr[i] + ",";          //add it to the return string
    }
    return toReturn.substr(0, toReturn.length - 1);  //remove trailing comma
}
function removeallitems(quote){
 	
	$('#billawarditems').val('');
	$('#billwindow').html('');	
	$('#billitemcount').val('0');	
	$('#billingtype').val('');	
	$('#prevbillitemcount').val('0');	
	$('#previtemmsg').html(parseInt($('#prevbillitemcount').val())+' item/s on Bill already exists on previous Bill');
	$('#rem'+quote).css('display','none');
	$("#previewtab").css('display','none');
	$("#previewtab").remove();
}

function setServicelaboritemsFlag()
{
	alert("Service/Labor items added to bill");
	$("#servicelaboritemsflag").val(1);
}

function showserviceitemblock()
{
	$("#serviceitemblock").css('display','');
}

function addservice()
{
	var data = "name="+$("#servicename").val()+"&serviceprice="+$("#serviceprice").val()+"&servicetax="+$("#servicetax").val();

	$.ajax({
		type:"post",
		data: data,
		url: insertserviceandlabor
	}).done(function(data){
		if(data){

			/*$("#qtypriceplacer").html("");
			$("#qtypriceplacer").html(data);*/
			alert("Service items added successfully!");
			location.reload();
			//$(".alert-success").css({display: "block"});
			$("#servicename").val('');
			$("#serviceprice").val('');
			$("#servicetax").val('');
			
		}
	});

}
</script>
<section class="row-fluid">
    <h3 class="box-header"><span class="badge badge-warning"><?php echo $quote->potype == 'Direct' ? 'Direct' : 'Via Quote'; ?></span> <?php echo @$heading; ?></h3>
<?php //var_dump($awarded); exit;  ?>
    <div class="box">
        <div class="span12">
            <a class="btn btn-green" href="javascript:void(0)" onclick="history.back();">&lt;&lt; Back</a>
            &nbsp;&nbsp;<a href="<?php echo site_url('admin/quote/trackexport').'/'.$adquoteid; ?>" class="btn btn-green">Export</a>&nbsp;&nbsp;<a href="<?php echo site_url('admin/quote/trackpdf').'/'.$adquoteid; ?>" class="btn btn-green">View PDF</a>
            <br/> <br/>
            <?php echo $this->session->flashdata('message'); ?>
            <?php echo @$message; ?>
            <div class="control-group">
                <div class="controls">
                        <?php if ($awarded->status == 'incomplete') { ?>
                        <div class="pull-right">
                            Mark completed for All items of:
                            <select id="combocompany" onchange="selectbycompany();">
                                <option value=''>Select Company</option>
                                    <?php foreach ($combocompanies as $combocompany) { ?>
                                    <option value="<?php echo implode(',', $combocompany['value']); ?>"><?php echo $combocompany['label'] ?></option>
                                    <?php } ?>
                            </select>
                        </div>
                        <?php } ?>
                    <span class="label label-pink"><?php echo $awarded->status; ?></span>
                    <strong>
                        PO #:<?php echo $quote->ponum; ?>
                        &nbsp; &nbsp;
                        Submitted:  <?php echo date('m/d/Y', strtotime($awarded->awardedon)); ?>
                    </strong><div class="clock"></div>
                    <div style="clear:both;"></div>
                    <?php /* $greaterseconds = ""; $seconds="";  foreach ($awarded->items as $q) {
                    	 if(($q->quantity - $q->received) >0)
                    	$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($awarded->awardedon);
                    	else {
                    		$greaterreceived = "";
                    		foreach ($awarded->invoices as $invoice) {
                    			foreach ($invoice->items as $item) {
                    				if($item->awarditem==$q->id){
                    					$receiveddate = $item->receiveddate;
                    					if($greaterreceived!=""){
                    						if(strtotime($greaterreceived)<strtotime($receiveddate))
                    						$greaterreceived = $receiveddate;
                    					}else
                    					$greaterreceived = $receiveddate;
                    				}
                    			}
                    		}
                    		//echo "g=".$greaterreceived."-G";
                    		$seconds = strtotime($greaterreceived) - strtotime($awarded->awardedon);
                    	}
                    	if($greaterseconds!=""){
                    		if($greaterseconds<$seconds)
                    		$greaterseconds = $seconds;
                    	}else
                    	$greaterseconds = $seconds;
						}
						$days    = floor($greaterseconds / 86400);
                    	$hours   = floor(($greaterseconds - ($days * 86400)) / 3600);
                    	$minutes = floor(($greaterseconds - ($days * 86400) - ($hours * 3600))/60);
                    	$seconds = floor(($greaterseconds - ($days * 86400) - ($hours * 3600) - ($minutes*60))); */ ?>
                    	<!-- <span style="margin-left:300px;">&nbsp;&nbsp;&nbsp;&nbsp;<strong><?php //  echo $days." d"." ".$hours." h".$minutes." m"; ?></strong></span> -->
                    <?php if (0) { ?>
                        &nbsp;  &nbsp;
                        <form action="<?php echo site_url('admin/quote/changestatus/' . $quote->id); ?>" method="post" class="form-horizontal">
                            <select name="status" onchange="this.form.submit()">
                                <option value="Pending" <?php if ($quote->status == 'Pending') {
                        echo 'SELECTED';
                    } ?>>Pending</option>
                                <option value="Verified" <?php if ($quote->status == 'Verified') {
                        echo 'SELECTED';
                    } ?>>Verified</option>
                                <option value="Error" <?php if ($quote->status == 'Error') {
                        echo 'SELECTED';
                    } ?>>Error</option>
                            </select>
                        </form>
                    <?php } ?>
                </div>
            </div>
            <div class="barBg">
                <div class="bar carrot" id ="timelineid" >
                    <div class="barFill" ><div align="center" style="overflow: hidden;color:black;" class="myLink"><b><?php echo $per; ?> Received</b></div></div>
                </div>
            </div>
            <br/>
            <div class="control-group" style="overflow-x:auto;">
                <table class="table table-bordered" style="table-layout:fixed;word-wrap:break-word;width:150%;">
                    <tr>
                        <th width="10%">Company</th>
                        <th width="10%">Item Code</th>
                        <th width="10%">Item Name</th>
                        <th width="13%">Item Progress</th>
                        <th width="7%">Qty.</th>
                        <th width="5%">Unit</th>
                        <th width="10%">Price EA</th>
                        <th width="10%">Total Price</th>
                        <th width="10%">Date Requested</th>
                        <th width="10%">Cost Code</th>
                        <th width="10%">Notes</th>
                        <th width="5%">Still Due</th>
                        <th width="20%">History</th>
                        <?php if ($awarded->status == 'incomplete') { ?>
                        <th width="8%">Received Qty.</th>
                        <th width="10%">Invoice #</th>
                        <th width="16%">Date Received</th>
                        <th width="7%">Complete<br/><input type="checkbox" id="selectall" onclick="$('.select-for-complete').prop('checked', this.checked);"></th>
                        <th width="11%">Error</th>
                        <?php } ?>
                    </tr>
                        <?php if ($awarded->status == 'incomplete') { ?>
                        <form id="trackform" class="form-horizontal" method="post" action="<?php echo base_url(); ?>admin/quote/savetrack/<?php echo $quote->id; ?>">
                            <input type="hidden" id="makedefaultinvoicenum" name="makedefaultinvoicenum"/>
                            <input type="hidden" id="makedefaultreceiveddate" name="makedefaultreceiveddate"/>
                        <?php } ?>
					    <?php
						$counter_kk = 1;
						$billcnt = 0;
						$alltotal = 0; $cnt = count($awarded->items); 
						$prevbillitems = "";
						$currentbillitems = "";
						$itemdatearr = array();
						foreach ($awarded->items as $q) {
						
						$itemdatearr[$q->itemid] = $q->daterequested;		
							
						if($currentbillitems=="")						
						$currentbillitems = $q->id;
						else 
						$currentbillitems .= ",".$q->id;
						
						$counter_kk++;
						?>
					    <?php $alltotal+=$q->totalprice; ?>
                            <tr>
                                <td><?php echo @$q->companydetails->title; ?></td>
                                <td><?php echo $q->itemcode; ?></td>
                                <td><?php echo $q->itemname; ?></td>
                                <td class="dis_td"><div id="topLoader<?php echo $counter_kk;?>">
      <?php
      //$q->quantity;//100%
      if(@$q->quantity)
    	$new_pr_value = (($q->received * 100) / $q->quantity)/100;
      else 
      $new_pr_value = 0;	
   //   $new_pr_value = ($q->received/100) *10; ?>
       <script>
        $(function() {
          var $topLoader = $("#topLoader<?php echo $counter_kk; ?>").percentageLoader({width: 80, height: 80, controllable : false, progress : <?php echo $new_pr_value; ?>, onProgressUpdate : function(val) {
              $topLoader.setValue(Math.round(<?php echo $q->received;?>));
            }});
          var topLoaderRunning = false;
          $("#animateButton").click(function() {
            if (topLoaderRunning) {
              return;
            }
            topLoaderRunning = true;
            $topLoader.setProgress(0);
            $topLoader.setValue('0kb');
            var kb = 0;
            var totalKb = 999;
            var animateFunc = function() {
              kb += 17;
              $topLoader.setProgress(kb / totalKb);
              $topLoader.setValue(kb.toString() + 'kb');
              if (kb < totalKb) {
                setTimeout(animateFunc, 25);
              } else {
                topLoaderRunning = false;
              }
            }
            setTimeout(animateFunc, 25);
          });
        });
      </script>
    </div></td>
                                <td>
                                <?php echo $q->quantity; ?>
                                <?php if($q->received != '0.00' && $q->received != ''){?>
                                <br/><i class="icon icon-ok btn-green"> <?php echo $q->received;?></i>
                                <?php }?>
                                </td>
                                <td><?php echo $q->unit; ?></td>
                                <td>$ <?php echo $q->ea; ?></td>
                                <td>$ <?php echo $q->totalprice; ?>
                                <br> <span style="color:red;"><a href="javascript:void(0)" onclick="addtobill('<?php echo @$quote->id;?>','<?php echo $q->id; ?>','<?php if (array_key_exists($q->company, $billitemdata) && isset($billitemdata[$q->company][$q->award]) ){ if(in_array(@$q->itemid,$billitemdata[$q->company][$q->award])){ echo "1"; }else echo "0"; } else echo "0"; ?>')" >+ Add to bill</a></span><br><?php if (array_key_exists($q->company, $billitemdata) && isset($billitemdata[$q->company][$q->award]) ){ if(in_array(@$q->itemid,$billitemdata[$q->company][$q->award])){ echo "Already Billed"; $billcnt++; if($prevbillitems=="") $prevbillitems = $q->id; else $prevbillitems .= ",".$q->id;  } } ?>
                                 <span style="display:none;" id="<?php echo 'rem'.$q->id;?>"><a href="javascript:void(0)" onclick="removeitem('<?php echo $q->id;?>','<?php echo $quote->id;?>')">- Remove</a></span>
                                </td>
                                <td><?php echo $q->daterequested;?><br/>
                                
                                <?php $greaterreceived = ""; 
									foreach ($awarded->invoices as $invoice) {
										//$i=0;$myarr[$i]=array();
										foreach ($invoice->items as $item) {
											
											if($item->awarditem==$q->id){
												$receiveddate = $item->receiveddate;
												if($greaterreceived!=""){
													if(strtotime($greaterreceived)<strtotime($receiveddate))
													$greaterreceived = $receiveddate;
												}else
												$greaterreceived = $receiveddate;
												
										    }
										    //$myarr[$i]['itemidi']=$item->itemid;
										    //$i++;
									   } 
									   												
									} 
                                   
                               echo (date('Y-m-d H:i:s', strtotime( $q->daterequested."23:59:59")) < $greaterreceived)? "*Late": "";?>&nbsp; <!-- <a href="<?php // echo site_url('admin/quote/sendautolateemail') . '/' . $quote->id; ?>">Email</a> --> <br> <?php if (@$q->shipreceiveddate) echo "Received &nbsp;".number_format($q->received)."&nbsp;on &nbsp;".date("m/d/Y",strtotime($q->shipreceiveddate)); ?>  
 <?php   echo (date('Y-m-d H:i:s', strtotime( $q->daterequested."23:59:59")) < date('Y-m-d H:i:s'))?"*Item Past Due":""; ?></td>
                                <td><?php echo $q->costcode; ?></td>
                                <td><?php echo $q->notes; ?></td>
                                <td><span id="due<?php echo $q->id; ?>"><?php echo $q->quantity - $q->received; ?></span>
                               <br><?php if($q->quantity - $q->received!=0){                              
                                echo (date('Y-m-d H:i:s', strtotime( $q->daterequested."23:59:59")) < date('Y-m-d H:i:s'))?"PAST DUE":""; } ?></td>
                                <td><?php if($q->etalog){?><a href="javascript:void(0)" onclick="$('#etalogmodal<?php echo $q->id?>').modal();">
							    				<i class="icon"></i><p style="padding-left:36px;">View</p>
							    			</a>
						<?php } $seconds = "";
								if(($q->quantity - $q->received) >0)
								$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime($awarded->awardedon);
								elseif ($q->quantity=="")
								$seconds = strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d H:i:s'));
								else {
									$greaterreceived = "";
									foreach ($awarded->invoices as $invoice) {
										foreach ($invoice->items as $item) {
											if($item->awarditem==$q->id){
												$receiveddate = $item->receiveddate;
												if($greaterreceived!=""){
													if(strtotime($greaterreceived)<strtotime($receiveddate))
													$greaterreceived = $receiveddate;
												}else
												$greaterreceived = $receiveddate;
										    }
									   }
									}
									//echo "g=".$greaterreceived."-G";
									$seconds = strtotime($greaterreceived) - strtotime($awarded->awardedon);
								}
	                            $days    = floor($seconds / 86400);
								$hours   = floor(($seconds - ($days * 86400)) / 3600);
								$minutes = floor(($seconds - ($days * 86400) - ($hours * 3600))/60);
								$seconds = floor(($seconds - ($days * 86400) - ($hours * 3600) - ($minutes*60)));?>
								<strong> <?php //echo $days." d"." ".$hours." h".$minutes." m";  ?></strong>
					<div style="height:40px;width:160px;">
							<strong><p style="font-size:15px;">&nbsp;&nbsp;D&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;H&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp;&nbsp;M</p></strong>
                    	<div style="height:28px;width:25px;background-color:#000000;border-radius:5px;float:left;box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);">
                    		<p style="text-align:center;font-weight:bold;font-size:15px;color:#FFFFFF;font-family:'Helvetica Neue', Helvetica, sans-serif; padding-top:4px;"><?php echo $days ?></p>
                    	</div>
                    	<p style="font-size:25px;font-weight:bold;float:left;margin-left:4px;">:</p>
                    	<div style="height:28px;width:25px;background-color:#000000;border-radius:5px;margin-left:4px;float:left;box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);">
                    		<p style="text-align:center;font-weight:bold;font-size:15px;color:#FFFFFF;font-family:'Helvetica Neue', Helvetica, sans-serif; padding-top:4px;"><?php echo $hours ?></p>
                   		</div>
                   		<p style="font-size:25px;font-weight:bold;float:left;margin-left:4px;">:</p>
                    	<div style="height:28px;width:25px;background-color:#000000;border-radius:5px;margin-left:4px;float:left;box-shadow: 0 0 5px rgba(0, 0, 0, 0.5);">
                   			<p style="text-align:center;font-weight:bold;font-size:15px;color:#FFFFFF;font-family:'Helvetica Neue', Helvetica, sans-serif; padding-top:4px;"><?php echo $minutes ?></p>
                    	</div>
                    	<div style="clear:left;"></div>
                    </div>
                    			<?php if(@$q->pendingshipments){?>
                                <br/><?php echo $q->pendingshipments;?> Pending Acknowledgement
                                <?php }?>
								</td>
                                <?php if ($awarded->status == 'incomplete') { ?>
                                    <td><input type="text" <?php if ($q->quantity - $q->received == 0) echo 'readonly'; ?> class="span6 receivedqty"
                                    	name="received<?php echo $q->id; ?>" id="received<?php echo $q->id; ?>" value="" onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"/>
                                    	<input type="hidden" name="comments" id="comments" value=""/>                                    	
                                    </td>
                                    <td>
                                    <?php $isupfrontinvoice = 0; 
                                               if($awarded->invoices) { foreach($awarded->invoices as $i){
												foreach($i->items as $items){
                                               	if($items->invoice_type == "fullpaid" && $items->awarditem==$q->id){
                                               	echo '<input type="hidden" name="invoicetype'.$q->id.'" id="invoicetype'.$q->id.'" value="'.$items->invoice_type.'" >';
                                               	echo '<input type="hidden" name="paymentdate'.$q->id.'" id="paymentdate'.$q->id.'" value="'.$items->paymentdate.'" >';
                                               	echo '<input type="hidden" name="refnum'.$q->id.'" id="refnum'.$q->id.'" value="'.$items->refnum.'" >';
                                               	echo '<input type="hidden" name="datedue'.$q->id.'" id="datedue'.$q->id.'" value="'.$items->datedue.'" >';
                                               	$isupfrontinvoice = 1;
												}
												}

                                    } }?>
                                        <input type="text" id="invoicenum<?php echo $q->id; ?>" name="invoicenum<?php echo $q->id; ?>"
                                               <?php if ($q->quantity - $q->received == 0) echo 'readonly class="span10"';
                                               else echo 'class="span10 invoicenum" onchange="defaultinvoicenum(\''.$q->id.'\',\''.$cnt.'\');"'; ?>
                                              value="<?php // if(@$isupfrontinvoice==1) echo "paid-in-full-already".$awarded->id; ?>" <?php // if(@$isupfrontinvoice==1) echo "readonly"; ?>
                                               onchange="defaultinvoicenum('<?php echo $q->id; ?>');"/>
                                    </td>
                                    <td>
                                        <input type="text" id="receiveddate<?php echo $q->id; ?>" name="receiveddate<?php echo $q->id; ?>"
                                               <?php if ($q->quantity - $q->received == 0) echo 'readonly class="span10" ';
                                               else echo ' class="span10 datefield receiveddate" onchange="defaultreceiveddate(\''.$q->id.'\',\''.$cnt.'\');"'; ?>
                                               value="<?php if(@$q->datereceived) echo date("m/d/Y",strtotime($q->datereceived)); //if ($this->session->userdata('defaultreceiveddate')) echo $this->session->userdata('defaultreceiveddate'); ?>"
                                               data-date-format="mm/dd/yyyy"/>
                                    </td>
                                    <td>
                                        <?php if ($q->quantity > $q->received) { ?>
                                            <input type="checkbox" id="select<?php echo $q->id ?>" value="<?php echo $q->id ?>" class="select-for-complete" />
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <select id="error<?php echo $q->id ?>" class="select-for-error" style="font-size:11px; width:65px;">
                                            <option value=''>No Error</option>
                                            <option value='<?php echo $q->id ?>-Wrong Item Sent'>Wrong Item Sent</option>
                                            <option value='<?php echo $q->id ?>-Quantity Discrepancy'>Quantity Discrepancy</option>
                                            <option value='<?php echo $q->id ?>-Revise PO Qty'>Revise PO Qty</option>
                                            <option value='<?php echo $q->id ?>-Revise Received Qty'>Revise Received Qty</option>
                                        </select>
                                    </td>
                            <?php } ?>
                            </tr>
                        <?php  }  ?>
                        <?php if ($awarded->status == 'incomplete') { ?>
                            <tr>
                                <td colspan="<?php echo $awarded->status == 'incomplete' ? 11 : 7//14:10; ?>" style="text-align:right"></td>
                                <td><input type="submit" value="Update" class="btn btn-primary btn-small"/></td>
                                <td colspan="3">&nbsp;</td>
                                <td><input type="button" class="btn btn-primary btn-small" onclick="completeselected();" value="Complete"></td>
                                <td><input type="button" class="btn btn-primary btn-small" onclick="showErrorModal();" value="Error"></td>
                            </tr>
                        </form>
                    <?php } ?>
                    <?php
                    $taxtotal = $alltotal * $config['taxpercent'] / 100;
                    $grandtotal = $alltotal + $taxtotal;
                    ?>
                    <tr>
                        <td colspan="6" style="text-align:right">Subtotal: </td>
                        <td colspan="<?php echo $awarded->status == 'incomplete' ? 10 : 5; ?>">$ <?php echo round($alltotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:right">Tax: </td>
                        <td colspan="<?php echo $awarded->status == 'incomplete' ? 10 : 5; ?>">$ <?php echo round($taxtotal, 2); ?></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align:right">Total: </td>
                        <td colspan="<?php echo $awarded->status == 'incomplete' ? 10 : 5; ?>">
                        <table><tr><td>$ <?php echo round($grandtotal, 2); ?> &nbsp; <br> <span style="color:red;"><a href="javascript:void(0)" onclick="addalltobill('<?php echo @$quote->id;?>','<?php echo @$cnt;?>','<?php echo htmlspecialchars(addslashes($prevbillitems));?>','<?php echo @$billcnt;?>','<?php  echo htmlspecialchars(addslashes($currentbillitems)); ?>')" >+ Add all items to bill</a></span> <br> <?php if(@$billcnt == $cnt) { echo "All items already Billed"; } ?>   <span style="display:none;" id="<?php echo 'rem'.$quote->id;?>"><a href="javascript:void(0)" onclick="removeallitems('<?php echo $quote->id;?>')">- Remove All</a></span> </td>
                        <td>
                        <span style="align:right;" id="billwindow"></span>
                        </td></tr></table>
                        
                        </td>
                    </tr>
                </table>
            </div>
            <?php
            if (@$shipments)
            {
            ?>
            <h3 class="box-header">Shipments:  <?php
               /* $canacceptall = false;
                $shipitemids = array();
                foreach($shipments as $cs)
                {
                    if($cs->accepted == 0)
                    {
                        $canacceptall = true;
                    }
                }
                foreach($shipments as $cs)
                {
                    if(isset($shipitemids[$cs->awarditem]))
                    {
                        $canacceptall = false;
                        break;
                    }
                    $shipitemids[$cs->awarditem] = 1;
                }*/
            ?>
            <?php if(count($shipments2)>0){?>
           <button class="btn btn-primary" onclick="acceptall()">Accept All</button>
           <?php }?></h3>
           <table class="table table-bordered" >
           	<tr>
           		<th>Item</th>
           		<th>Date Sent</th>
           		<th>Quantity</th>
           		<th>Reference #</th>
           		<th>Action</th>
           	</tr>
           	<?php foreach($shipments as $s){ ?>
           	<tr>
           		 <td><?php if($s->itemname=="") {echo $s->iii;} else { echo $s->itemname;}?></td>
           		<td id="shipdate<?php echo $s->id;?>"><?php echo date("m/d/Y h:i A", strtotime($s->shipdate));?></td>
           		<td id="acceptqty<?php echo $s->id;?>"><?php echo $s->quantity;?></td>
           		<td id="acceptinvoicenum<?php echo $s->id;?>"><?php echo $s->invoicenum;?></td>
           		<td>
           			<?php if($s->accepted == 0){?>
           			<input type="button" value="Accept" onclick="acceptshipment('<?php echo $s->awarditem;?>','<?php echo $s->id;?>')"/>
           			<?php }?>
           		</td>
           	</tr>
           	<?php } ?>
           </table>
            <?php
            }
            ?>
            <?php
            if (@$messages)
                foreach ($messages as $c)
                    if (@$c['messages']) {
                        ?>
                         <h3 class="box-header">Messages for <?php echo $c['companydetails']->title ?> regarding PO# <?php echo $quote->ponum; ?>:</h3>
                        <table class="table table-bordered" >
                            <tr>
                                <th>From</th>
                                <th>To</th>
                                <th>Message</th>
                                <th>Date/Time</th>
                                <th>&nbsp;</th>
                            </tr>
                            <?php
                            foreach ($c['messages'] as $msg) {
                            ?>
                            <tr>
                                <td><?php echo $msg->from; ?></td>
                                <td><?php echo $msg->to; ?></td>
                                <td><?php echo $msg->message; ?></td>
                                <td><?php echo date("m/d/Y h:i A", strtotime($msg->senton)); ?></td>
                                <td>
                                    <?php if ($msg->user_attachment != '') { ?>
                                        <a href="<?php echo site_url('uploads/messages') . '/' . $msg->user_attachment; ?>" target="_blank" title="View Attachment"><?php echo 'View Attachment'; ?></a>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <?php
                        }
                        ?>
                <div class="well" style="float:left">
                    <form class="form-horizontal" method="post" action="<?php echo site_url('admin/message/sendmessage/' . $quote->id . '/track') ?>" onsubmit="this.to.value = this.company.options[this.company.selectedIndex].innerHTML"  enctype="multipart/form-data">
                        <input type="hidden" name="quote" value="<?php echo $quote->id; ?>"/>
                        <input type="hidden" name="from" value="<?php echo $this->session->userdata('fullname') ?> (Admin)"/>
                        <input type="hidden" name="to" value=""/>
                        <input type="hidden" name="ponum" value="<?php echo $quote->ponum; ?>"/>
                        <div class="control-group">
                            <label class="control-label" for="company">Send Message To:</label>
                            <div class="controls">
                                <select name="company" required>
                                    <?php foreach ($messagecompanies as $combocompany) { ?>
                                        <option value="<?php echo $combocompany['id']; ?>"><?php echo $combocompany['label'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="message">Message</label>
                            <div class="controls">
                                <textarea name="message" class="span8" rows="5" required></textarea>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="userfile">Attachment</label>
                            <div class="controls">
                                <input type="file" name="userfile" size="13" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="control-label" for="">&nbsp;</label>
                            <div class="controls">
                                <input type="submit" value="Send" class="btn btn-primary"/>
                            </div>
                        </div>
                    </form>
                </div>
                <hr/>
        		<?php if($shippingdocs){?>
        		   <h3 class="box-header">Shipping Documents</h3>
        		<table class="table table-bordered col-md-4">
        			<tr>
        				<th>Company</th>
        				<th>Date</th>
        				<th>Reference#</th>
        				<th>View</th>
        			</tr>
        			<?php foreach($shippingdocs as $sd){?>
        			<tr>
        				<td><?php echo $sd->companyname;?></td>
        				<td><?php echo date("m/d/Y", strtotime($sd->uploadon));  ?></td>
                                        <td><?php echo $sd->invoicenum;?></td>
        				<td><a href="<?php echo site_url('uploads/shippingdoc/'.$sd->filename);?>" target="_blank">View</a></td>
        			</tr>
        			<?php }?>
        		</table>
        		<?php }?>
        		<hr/>
                <?php if ($awarded->invoices) { ?>
                <div class="control-group">
                    <div class="controls">
                         <h3 class="box-header">
                            Existing Invoices
                        </h3>
                        <br/>
                        <table class="table table-bordered">
                            <tr>
                                <th>Invoice #</th>
                                <th>Total Cost</th>
                                <!-- <th>Tax</th> -->
                                <th>Payment</th>
                                <th>DUE DATE</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                                <?php
								$f_total=0;
                                $p_total=0;
                                $u_total=0;
                                foreach ($awarded->invoices as $invoice) {?>
                                <tr>
                                    <td><?php echo $invoice->invoicenum; ?></td>
                                    <td><?php echo "$ ".number_format($invoice->totalprice+$invoice->totalprice * $config['taxpercent'] / 100, 2); ?></td>
                                    <!-- <td><?php // echo number_format($invoice->totalprice * $config['taxpercent'] / 100, 2);?></td> -->
                                    <td><?php echo $invoice->paymentstatus; ?></td>
                                    <td><?php if($invoice->datedue){echo date("m/d/Y", strtotime($invoice->datedue));}else{ echo "No Date Set";}
                                    if($invoice->invoicenum == "paid-in-full-already".$awarded->id)	
                                                               echo "<br> Pre-Paid";?>
                                    </td>
                                    <td><?php echo $invoice->status; ?></td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="showInvoice('<?php echo $invoice->invoicenum; ?>',<?php echo $awarded->quote; ?>);">
                                            <span class="icon-2x icon-search"></span>
                                        </a>
                                    </td>
                                </tr>
                                 <?php
                                $f1_total=$invoice->totalprice+number_format($invoice->totalprice * $config['taxpercent'] / 100, 2);
                                $f_total +=$f1_total;
                                if($invoice->paymentstatus=='Paid')
                                {
                                   $p1_total=$invoice->totalprice+number_format($invoice->totalprice * $config['taxpercent'] / 100, 2);
                                   $p_total +=$p1_total;
                                }
                                 if($invoice->paymentstatus=='Unpaid')
                                {
                                	 $u1_total=$invoice->totalprice+number_format($invoice->totalprice * $config['taxpercent'] / 100, 2);
                                     $u_total +=$u1_total;
                                }
                                } ?>
                               <tr><td style="text-align:right;">Total:</td><td><?php echo "$ ".number_format($f_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".number_format($p_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td style="text-align:right;">Total Unpaid:</td><td><?php echo "$ ".number_format($u_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                        </table>
                        <form id="invoiceform" method="post" action="<?php echo site_url('admin/quote/invoice'); ?>">
                            <input type="hidden" id="invoicenum" name="invoicenum"/>
                            <input type="hidden" id="invoicequote" name="invoicequote" />
                        </form>
                    </div>
                </div>
            <?php } ?>
            
            
            
               <?php if ($bills) { ?>
                <div class="control-group">
                    <div class="controls">
                         <h3 class="box-header">
                            Existing Bills
                        </h3>
                        <br/>
                        <table class="table table-bordered">
                            <tr>
                                <th>Bill Name #</th>
                                <th>Total Cost</th>                               
                                <th>Payment</th>
                                <th>DUE DATE</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                                <?php
								$f_total=0;
                                $p_total=0;
                                $u_total=0;
                             //  echo '<prE>',print_r($bills);die; 
                                foreach ($bills as $invoice) 
                                {
                                	$markupTotal = $invoice->total + ($invoice->total * $invoice->markuptotalpercent/100);	
                                	$totalTax =  ($invoice->total * $config['taxpercent'] / 100);
                                	$finalTotal = $markupTotal+$totalTax+$invoice->serviceItems;                                	
                                ?>
                                <tr>
                                    <td><?php echo $invoice->billname; ?></td>
                                    <td><?php echo "$ ".number_format($finalTotal,2); ?></td>
                                    <td><?php echo $invoice->paymentstatus; ?></td>
                                    <td><?php if($invoice->customerduedate){echo date("m/d/Y", strtotime($invoice->customerduedate));}else{ echo "No Date Set";} ?></td>
                                    <td><?php echo $invoice->status; ?></td>
                                    <td>
                                        <a href="javascript:void(0);" onclick="showBill('<?php  echo $invoice->id; ?>','<?php echo $invoice->quote; ?>');">
                                            <span class="icon-2x icon-search"></span>
                                        </a>
                                    </td>
                                </tr>
                                 <?php
                                $f_total +=$finalTotal;
                                
                                   $p1_total=$invoice->amountpaid;
                                   $p_total +=$p1_total;
                                
                                	 $u1_total=number_format(($finalTotal - $invoice->amountpaid),2);
                                     $u_total +=$u1_total;
                                } ?>
                               <tr><td style="text-align:right;">Total:</td><td><?php echo "$ ".number_format($f_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td style="text-align:right;">Total Paid:</td><td><?php echo "$ ".number_format($p_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                                <tr><td style="text-align:right;">Total Unpaid:</td><td><?php echo "$ ".number_format($u_total ,2);?></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
                        </table>
                        <form id="billform" method="post" action="<?php echo site_url('admin/quote/bill'); ?>">
                            <input type="hidden" id="billid" name="billid"/>
                            <input type="hidden" id="billquote" name="billquote" />
                        </form>
                    </div>
                </div>
            <?php } ?>
            
            
            <?php
            if ($recsum == $qntsum) {
                $class = "complete";
                $closed = "active complete";
            } else {
                $class = "active late";
                $closed = "future";
            }
            ?>
            <section id="progress">
       <!--     <ul class="breadcrumb1">
  <li><a href="#">Created Sep <span><?php echo date('M d,Y', strtotime($quote->podate)); ?></span></a></li>
  <li><a href="#">Issued Date Sep <span><?php echo date('M d,Y', strtotime($awarded->awardedon)); ?></span></a></li>
  <li><a href="#">Delivery Expected Date Sep <span><?php echo date('M d,Y', strtotime(@$awarded->items[0]->daterequested)); ?></span></a></li>
  <li><a href="#" class="current"><?php echo $per; ?> Received </a></li>
  <li><a href="#">Closed</a></li>
</ul>-->
                <ul>
                	<li class="<?php echo (!$quote->podate) ? "active" : "complete"; ?>">
                		Created <span><?php echo date('M d,Y', strtotime($quote->podate)); ?></span>
                	</li>
                	<li class="<?php echo (!$awarded->awardedon) ? "active" : "complete"; ?>">
                		Issued Date <span><?php echo date('M d,Y', strtotime($awarded->awardedon)); ?></span>
                	</li>
                	<?php if(@$awarded->items[0]->daterequested){?>
                	<li class="<?php echo (!@$awarded->items[0]->daterequested) ? "active" : "complete"; ?>">
                	Delivery Expected Date <span><?php echo date('M d,Y', strtotime(@$awarded->items[0]->daterequested)); ?></span>
                	</li>
                	<?php }?>
                	<li class="<?php echo $class; ?>"><?php echo $per; ?> Received <span>&nbsp;</span></li>
                	<li class="<?php echo $closed; ?>">Closed</li>
                </ul>
            </section>
            <div>
                   <h3 class="box-header">Time Line</h3>
                <div style="float:left; width:100%">
                    <table width="100%">
                        <tr><td style="border-right:2px black solid;" width="10%">
                                    <?php echo date('m/d/Y', strtotime($awarded->awardedon)); ?>&nbsp;</td><td width="90%">&nbsp;&nbsp;&nbsp;PO #<?php echo $quote->ponum; ?> Submitted</td>
                        </tr>
                        <tr>
                            <td style="border-right:2px black solid;" width="10%">&nbsp;</td>
                            <td >  <?php // echo $q->daterequested."-><pre>"; print_r($awarded->invoices); //die;?>
                                    <?php //$i=0; 
                                    foreach ($awarded->invoices as $invoice) {?>
                                    <div class="label label-pink"><?php echo '#' . $invoice->invoicenum; ?></div>
                                    <div class="clear"></div>
                                    <table width="100%">
                                            <?php  foreach ($invoice->items as $item) { ?>
                                            <tr><td  style="border-right:2px #dff0d8 solid;" width="15%">
                                                <span><?php if(@$item->receiveddate) echo date('m/d/Y', strtotime($item->receiveddate)); ?></span>&nbsp;</td><td> &nbsp;&nbsp;&nbsp;<span>													<?php echo '<b>' . $item->itemname . '</b> - ' . $item->quantity . ' Received'; 
                                                               	if(@$itemdatearr[$item->itemid]) echo (date('Y-m-d H:i:s', strtotime( $itemdatearr[$item->itemid]."23:59:59")) < $item->receiveddate)? "&nbsp;- &nbsp;*Late": "";
                                                               	
                                                               if($invoice->invoicenum == "paid-in-full-already".$awarded->id)	
                                                               echo "&nbsp;&nbsp;(PO #".@$quote->ponum."&nbsp;Paid In Full, All Invoices are Pre-Paid)";
                                            ?></span></td>                                            
                                            </tr>
                                            <?php  }  ?>
                                    </table>
                                    <?php //$i++; 
                                    } ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <br/><br/>
            <?php if ($awarded->status == 'complete') {?>
            <div>
            	<h3>Feedbacks</h3>
            	<table class="table">
            		<tr>
            			<th>Company</th>
            			<th>Rating</th>
            			<th>Feedback</th>
            			<th></th>
            		</tr><?php if(@$item->receiveddate) echo date('m/d/Y', strtotime($item->receiveddate)); ?>
                	<?php
                	    foreach($messagecompanies as $combocompany)
                	    {
                	        if(isset($feedbacks[$combocompany['id']]))
                	            $rating = '<div class="fixedrating" data-average="'.$feedbacks[$combocompany['id']]->rating.'" data-id="1"></div>';
                	        else
                	            $rating = '';
                	?>
            		<tr><?php $companylabel = is_string($combocompany['label'])?$combocompany['label']:''; ?>
            			<td><?php echo $companylabel;?></td>
            			<td><?php echo $rating;?></td>
            			<td><?php echo isset($feedbacks[$combocompany['id']]) ? $feedbacks[$combocompany['id']]->feedback : '';?></td>
            			<th>
            				<?php if(!isset($feedbacks[$combocompany['id']])){?>
            					<a href="javascript:void(0)" onclick="showfeedbackform('<?php echo $combocompany['id'];?>','<?php echo $companylabel;?>')"><i class="icon icon-edit"></i></a>
            				<?php }?>
            			</th>
            		</tr>
                	<?php
                	    }
                	?>
            	</table>
            	<div id="feedbackformwrapper">
            		<form method="post" action="<?php echo site_url('admin/quote/savefeedback');?>" onsubmit="return checkfeedback();">
            			<input type="hidden" name="quote" value="<?php echo $quote->id;?>">
            			<input type="hidden" id="feedbackcompany" name="company">
            			<input type="hidden" id="feedbackrating" name="rating">
            			<h4>Give Feedback to: <span id="feedbackcompanyname"></span></h4>
            			<table>
            				<tr>
            					<td>Rating:</td>
            					<td><div class="basic" data-id="1"></div></td>
            				</tr>
            				<tr>
            					<td>Feedback:</td>
            					<td><textarea name="feedback" rows="5" style="width: 300px;"></textarea></td>
            				</tr>
            				<tr>
            					<td> </td>
            					<td><input type="submit" value="Save" class="btn btn-primary"/></td>
            				</tr>
            			</table>
            		</form>
            	</div>
            </div>
            <?php }?>
             <div>
                <?php
                if(!empty($errorLog))
                {
                    ?>
                     <hr>
                         <h3 class="box-header">Error Log</h3>
                       <table  class='table table-bordered'>
                            <tbody>
                                <tr>
                                     <th>company</th>
                                     <th>Error</th>
                                     <th>Item</th>
                                     <th>Qty</th>
                                     <th>Invoice#</th>
                                     <th>Date</th>
                                 </tr>
                        <?php
                         foreach($errorLog as $error)
                         { ?>
                                <tr>
                                    <td><?php echo $error->title;?></td>
                                    <td><?php echo $error->error;?></td>
                                    <td><?php echo $error->itemcode;?></td>
                                    <td><?php echo $error->quantity;?></td>
                                    <td><?php echo $error->invoicenum;?></td>
                                    <td><?php echo (isset($error->date) && $error->date!="" && $error->date!="0000-00-00" && $error->date!="1969-12-31")?date("m/d/Y",  strtotime($error->date)):"";?></td>
                                </tr>
                        <?php
                        }?>
                       </tbody>
                 </table>
                 <?php    }
                  ?>
        </div>
    </div>
    </div>
</section>
<div id="completemodel" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <input type="hidden" id="selecteditemids" name="itemids">
    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        <h3>Mark selected items as COMPLETED.</h3>
    </div>
    <div class="modal-body">
        <table>
            <tr>
                <td><strong>Invoice #:</strong></td>
                <td><input type="text" class="span4" id="invoicenum" name="invoicenum" required onchange="$('.invoicenum').val(this.value)"/></td>
            </tr>
            <tr>
                <td><strong>Received on:</strong></td>
                <td><input type="text" name="receiveddate" id="receiveddate" class="span2 datefield receiveddate" data-date-format="mm/dd/yyyy" required onchange="$('.receiveddate').val(this.value)"></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td><input type="button" value="Submit" class="btn btn-primary" onclick="$('#trackform').submit()"/></td>
            </tr>
        </table>
    </div>
</div>
<div id="commentmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
    	<h3>Comment: <span id="permissionponum"></span></h3>
	</div>
	<div class="modal-body" id="commmentwrapper">
		<textarea style="width:516px;" rows="5" id="commentdata" name="commentdata"></textarea>
		<input type="button" value="Save" class="btn btn-primary" onclick="getCommentdata()"/>
	</div>
</div>
    <?php // echo "<pre>",print_r($backtrack['quote']); die;
    if(isset($awarded->items) && count($awarded->items)>0) { foreach($awarded->items as $q) { //if($q->etalog) {?>
  <div id="etalogmodal<?php echo $q->id?>" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none; min-width: 700px;">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
         <table style="border:0px !important;" class="no-border">
         <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
         <tr><td style="border:0px;"><h3>ETA Update History</td></h3> <td style="border:0px;"><b>PO#: </b><?php if(isset($quote->ponum)) echo $quote->ponum; ?></td> <td style="border:0px;">Order Qty <?php if(isset($q->quantity)) echo number_format($q->quantity,0); ?></td></tr>
          <tr><td style="border:0px;"><b>Item Code:</b> <?php if(isset($q->itemcode)) echo $q->itemcode; ?></td> <td style="border:0px;"><b>Item Name: </b><?php if(isset($q->itemname)) echo $q->itemname ; ?></td> <td style="border:0px;"><b>Received Qty: </b><?php if(isset($q->received)) echo number_format($q->received,0) ; ?></td></tr>
          <tr><td style="border:0px;">&nbsp;</td> <td style="border:0px;"><b>Company: </b><?php if(isset($q->companyname)) echo $q->companyname; ?> </td> <td style="border:0px;"><b>Due Qty: </b><?php if(isset($q->quantity) && isset($q->received) ) { echo number_format(($q->quantity - $q->received),0); } ?><br><?php if(@$q->pendingshipments){?> <br/><?php echo $q->pendingshipments;?> Pending Acknowledgement <?php }?></td></tr><table>
        </div>
        <div class="modal-body">
          <table class="table table-bordered">
          	<tr>
          		<th>Date</th>
          		<th>Notes</th>
          		<th>Updated</th>
          	</tr>
          	<?php $i=0; foreach($q->etalog as $l){?>
          	<tr>
          		<td><?php if ($i==0) echo "changed from ".$q->quotedaterequested->daterequested." to ".$l->daterequested; else echo "changed from ".$olddate." to ".$l->daterequested; ?></td>
          		<td><?php echo $l->notes;?></td>
          		<td><?php echo date("m/d/Y", strtotime($l->updated));?></td>
          	</tr>
          	<?php $i++; $olddate = $l->daterequested; }?>
          </table>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
<?php //}
} }?>
<div id="billmodal" class="modal hide" style="width:500px;height:500px;"  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
    <div class="modal-header">
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
    	<h3>Bill <span id="billspan"></span>&nbsp;<span style="text-align:center;"><a href="javascript:void(0)" onclick="previewbillitems();">Edit/Preview Bill</a></span></h3>
    	<span style="text-align:center;" id="previtemmsg"></span>
	</div>
	<div class="modal-body" id="billwrapper">
		<form id="createbillform" action="<?php echo site_url('site/additemtoquote'); ?>" method="post" return false;">
		<input type="hidden" name="servicelaboritemsflag" id="servicelaboritemsflag" value="0">
		<table>
	<!--	<tr style="cursor:pointer;"><td colspan="2"><a onclick="setServicelaboritemsFlag()"> Add Service/Labor Items to bill </a>  </td> </tr>-->
		<tr style="cursor:pointer;"><td width="45%">Select Service/Labor Items to bill </a>  </td> <td><a onclick="showserviceitemblock();"> Create New Item </a> </td> </tr>
		
		<?php
			if(isset($servicebillitems) && count($servicebillitems)>0)
			{?>
				<tr> 
				<td colspan="3"><table width="80%"><th width="40%">Name</th><th width="10%">Price</th><th width="10%">Tax</th><th width="10%">Qty</th><th width="10%">&nbsp;</th> </td>
				</tr>	
		<?php	foreach ($servicebillitems as $key=>$v)
				{ ?>
					<tr>
						<td><?php echo $v->name; ?> <input type="hidden" name="servicelaboritemname[<?php echo $v->id; ?>]" value="<?php echo $v->name; ?>" > </td>
						<td><?php echo $v->price; ?> <input type="hidden" name="servicelaboritemprice[<?php echo $v->id; ?>]" value="<?php echo $v->price; ?>" > </td>
						<td><?php echo $v->tax; ?> <input type="hidden" name="servicelaboritemtax[<?php echo $v->id; ?>]" value="<?php echo $v->tax; ?>" > </td>
						<td><input type="text" name="servicelaboritemqty[<?php echo $v->id; ?>]" value="" > </td>
						<td><input type="checkbox" name="servicelaboritem[<?php echo $v->id; ?>]" value="<?php echo $v->id; ?>"> </td> 
					</tr>	 
		<?php 		} ?>
				
		</table>
			 </td>
			 </tr>
	<?php 		} else { 
					?> 
					<tr>
					<td colspan="2"> 
					<table width="100%" align="center"><tr><th width="40%">Name</th><th width="10%">Price</th><th width="10%">Tax</th><th width="10%">&nbsp;</th><tr><td><input type="text" name = "servicename" id="servicename"></td><td><input type="text" name = "serviceprice" id="serviceprice"></td><td><input type="text" name = "servicetax" id="servicetax"></td><td><input type="button" value = "Add" onclick="addservice();"></tr>
					<table>
					</td> </tr>
			<?php	} ?>
		
		<tr id="serviceitemblock" style="display:none;">
			<td><input type="text" name = "servicename" id="servicename"></td>
			<td><input type="text" name = "serviceprice" id="serviceprice"></td>
			<td><input type="text" name = "servicetax" id="servicetax"></td>
			<td><input type="button" value = "Add" onclick="addservice();">
		</tr>	
		<tr><td>&nbsp;&nbsp;</td></tr>	
		<tr><td>Bill #Name:</td><td><input type="text" name="billname" id="billname" style="width: 250px;"/></td></tr>
		<?php if($customerdata){ ?>
		<tr><td>Select Customer:</td><td><select id="customerid" name="customerid" onchange="setcustomerdata(this.value);">
		<option value="">Select</option>
		<?php foreach($customerdata as $cust){ ?>
		<option value="<?php echo $cust->id;?>"><?php echo $cust->name;?></option>	
		<?php } ?>
		</select>
		</td></tr>
		<?php } ?>
		<tr><td>Customer name:</td><td><input type="text" name="customername" id="customername" style="width: 250px;"/></td></tr>
		<tr><td>Customer e-mail:</td><td><input type="text" name="customeremail" id="customeremail" style="width: 250px;" /> </td></tr>
		<tr><td>Customer Address:</td><td><input type="text" name="customeraddress" id="customeraddress" style="width: 250px;" /> </td></tr>
		<tr><td>Due Date:</td><td><input type="text" name="customerduedate" id="customerduedate" style="width: 150px;" /> </td></tr>
		<tr><td>Bill Note:</td><td><input type="text" name="customerbillnote" id="customerbillnote" style="width: 250px;" /></td></tr>
		<tr><td>Include logo:</td><td><input type="radio" name="customerlogo" id="customerlogoyes" value="yes" /> Yes <input type="radio" name="customerlogo" id="customerlogono" value="no" checked="checked" /> No </td></tr>
		<!--<tr><td>Payable via:</td><td><select id="customerpaymenttype" name="customerpaymenttype" style="width: 150px;" >
        							<option value="cash">Cash</option>
        							<option value="cheque">Cheque</option>
        							<option value="paypal">Paypal</option>
        							</select></td></tr>
		<tr><td>Paypal e-mail:</td><td><input type="text" name="customerpaypalemail" id="customerpaypalemail" style="width: 250px;" /> </td></tr>-->
		<tr><td>Mark up total %:</td><td><input type="text" name="markuptotalpercent" id="markuptotalpercent" style="width: 75px;" /> </td></tr>
		<!-- <tr><td>Mark up each item %:</td><td><input type="text" name="markupitempercent" id="markupitempercent" style="width: 75px;" /></td></tr> -->
		<tr><td>Payable To:</td><td><input type="text" name="customerpayableto" id="customerpayableto" value="<?php echo @$this->session->userdata('companyname');?>" style="width: 250px;" /></td></tr> 		
		<tr><td colspan="2" style="text-align:center;"><input type="button" value="Save Bill" class="btn btn-primary" onclick="billformsubmit();"/></td>
		<input type="hidden" name="customerquoteid" id="customerquoteid"/>
		<input type="hidden" name="billingtype" id="billingtype"/>
		<input type="hidden" name="billitemcount" id="billitemcount" value="0" />
		<input type="hidden" name="billawarditems" id="billawarditems"/>
		<input type="hidden" name="prevbillitemcount" id="prevbillitemcount" value="0" />
		<input type="hidden" name="prevbillawarditems" id="prevbillawarditems"/>
		</tr> 
		</table>
		</form>
	</div>
</div>
		<div class="modal hide fade" id="billsuccessmodal">
		<div class="modal-header">
		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
    	</div>
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
        
       
        
        <div class="modal hide fade" id="billpreviewmodal">
		<div class="modal-header">
		<button aria-hidden="true" data-dismiss="modal"  onclick="$('#billmodal').modal('show')" class="close" type="button">x</button>
    	</div>
          <div class="modal-body">
          <span id="bpitems"></span>          
          </div>