<?php
	$combocompanies = array();
	$messagecompanies = array();
        $recsum =0;
        $qntsum =0;
	foreach($awarded->items as $q)
	{
		$recsum = $recsum + $q->received;
                $qntsum = $qntsum + $q->quantity;
		if($q->received < $q->quantity)
		{
			if(isset($combocompanies[$q->company]))
			{
				$combocompanies[$q->company]['value'][] = $q->id; 
			}
			else
			{
				$combocompanies[$q->company] = array();
				$combocompanies[$q->company]['value'] = array($q->id);
				$combocompanies[$q->company]['id'] = $q->company;
				$combocompanies[$q->company]['label'] = $q->companyname;
			}
		}
	
		if(isset($messagecompanies[$q->company]))
		{
			$messagecompanies[$q->company]['value'][] = $q->id; 
		}
		else
		{
			$messagecompanies[$q->company] = array();
			$messagecompanies[$q->company]['value'] = array($q->id);
			$messagecompanies[$q->company]['id'] = $q->company;
			$messagecompanies[$q->company]['label'] = $q->companyname;
		}
	}

        $per = number_format(($recsum/$qntsum)*100,2);
        $per .='%';
        //$per = '80%';
	//print_r($combocompanies);die;
?>
<?php echo '<script type="text/javascript">var senderrorurl = "'.site_url('admin/message/senderror/'.$quote->id).'";</script>';?>
	
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<link href="<?php echo base_url(); ?>templates/admin/css/progressbar.css" media="all" rel="stylesheet" type="text/css" >

<script>
$(document).ready(function(){
	$(".datefield").datepicker();
        <?php if($per=='0.00%') { ?>
            $("#timelineid").attr("class","bar madras");
            $("#timelineid").css("width",'100%');
                <?php }else{ ?>
        $("#timelineid").css("width",'<?php echo $per;?>');
        <?php } ?>
});

function defaultinvoicenum(qid)
{
	if(confirm('Do you want to make this invoice # default for this session?'))
	{
		$("#makedefaultinvoicenum").val('1');
		$(".invoicenum").val($("#invoicenum"+qid).val());
	}
}

function defaultreceiveddate(qid)
{
	if(confirm('Do you want to make this date default for this session?'))
	{
		$("#makedefaultreceiveddate").val('1');
		$(".receiveddate").val($("#receiveddate"+qid).val());
	}
}

function selectbycompany()
{
	var ids = $("#combocompany").val();
	if(ids == '')
	{
		return false;
	}
	$('.select-for-complete').prop('checked',false);
	ids = ids.split(',');
	for(var i=0; i<ids.length; i++)
	{
		var id = ids[i];
		$('#select'+id).prop('checked',true);
	}
	completeselected();
}

function completeselected()
{
	var selected = new Array();
	$('.receivedqty').val('');
	$('.select-for-complete').each(function() 
	{    
	    if($(this).is(':checked'))
	    {
	    	selected.push($(this).val());
	    	var selectid = $(this).attr('id');
	    	var dueid = selectid.replace('select','due');
	    	var dueamount = $("#"+dueid).html();
	    	
	    	var receivedid = selectid.replace('select','received');

	    	$("#"+receivedid).val(dueamount);
	    }
	});
	if(selected.length>0)
	{
		$('#completemodel').modal();
	}
}

function errorselected()
{
	var selected = new Array();
	$('.select-for-error').each(function() 
	{    
	    if($(this).val() != '')
	    {
	    	selected.push($(this).val());
	    }
	});
	if(selected.length>0)
	{
		var errors = selected.join(',');
		
		$.ajax({
		      type:"post",
		      data: "errors="+errors,
		      url: senderrorurl
		    }).done(function(data){
			   window.location = window.location;
		    });
	}
}

function showInvoice(invoicenum)
{
	$("#invoicenum").val(invoicenum);
	$("#invoiceform").submit();
}
</script>

<section class="row-fluid">
	<h3 class="box-header"><span class="badge badge-warning"><?php echo $quote->potype=='Direct'?'Direct':'Via Quote';?></span> <?php echo @$heading; ?></h3>
	<?php //var_dump($awarded); exit; ?>
	<div class="box">
		<div class="span12">
		   <a class="btn btn-green" href="<?php echo site_url('admin/purchaseuser/quotes/');?>">&lt;&lt; Back</a>
		   <a class="btn btn-green" href="<?php echo site_url('admin/purchaseuser/messages/'.$quote->id);?>">Messages</a>
		   <br/> <br/>
		   <?php echo $this->session->flashdata('message'); ?>
		   <?php echo @$message; ?>
		 
	
		      <div class="control-group">
			    <div class="controls">
			       <span class="label label-pink"><?php echo $awarded->status;?></span>
			    	<strong>
			    	PO #:<?php echo $quote->ponum; ?>
				    &nbsp; &nbsp; 
				    Submitted:  <?php echo date('m/d/Y', strtotime($awarded->awardedon));?>
			       </strong>
			       <br/>
			    </div>
		      </div>
			  
			  <hr/>
			  <br/>
                          
<div class="barBg">
	<div class="bar carrot" id ="timelineid" >
            <div class="barFill" ><div align="center" style="overflow: hidden;color:black;" class="myLink"><b><?php echo $per;?> Received</b></div></div>
	</div>              
</div>
			  <br/>
			  <div class="control-group">
				    <table class="table table-bordered">
				    	<tr>
				    		<th>Company</th>
				    		<th>Item Code</th>
				    		<th>Item Name</th>
				    		<th>Qty.</th>
				    		<th>Unit</th>
				    		<th>Price EA</th>
				    		<th>Total Price</th>
				    		<th>Date Requested</th>
				    		<th>Cost Code</th>
				    		<th>Notes</th>
				    		<th>Received Qty.</th>
				    		<th>Still Due</th>
				    	</tr>
				    	<?php $alltotal=0; foreach($awarded->items as $q){?><?php $alltotal+=$q->totalprice;?>
				    	<tr>
				    		<td><?php echo @$q->companydetails->title;?></td>
				    		<td><?php echo $q->itemcode;?></td>
				    		<td><?php echo $q->itemname;?></td>
                            <td>
                            <?php echo $q->quantity; ?>
                            <?php if($q->received != '0.00' && $q->received != ''){?>
                            <br/><i class="icon icon-ok btn-green"> <?php echo $q->received;?></i>
                            <?php }?>
                            </td>
				    		<td><?php echo $q->unit;?></td>
				    		<td>$ <?php echo $q->ea;?></td>
				    		<td>$ <?php echo $q->totalprice;?></td>
				    		<td><?php echo $q->daterequested;?></td>
				    		<td><?php echo $q->costcode;?></td>
				    		<td><?php echo $q->notes;?></td>
				    		<td><?php echo $q->received;?></td>
				    		<td><?php echo $q->quantity - $q->received;?></td>
				    	</tr>
				    	<?php }?>
				    	<?php 
							$taxtotal = $alltotal * $config['taxpercent'] / 100;
							$grandtotal = $alltotal + $taxtotal;
				    	?>
				    	<tr>
				    		<td colspan="6" style="text-align:right">Subtotal: </td>
				    		<td colspan="<?php echo $awarded->status=='incomplete'?10:5;?>">$ <?php echo round($alltotal,2);?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="6" style="text-align:right">Tax: </td>
				    		<td colspan="<?php echo $awarded->status=='incomplete'?10:5;?>">$ <?php echo round($taxtotal,2);?></td>
				    	</tr>
				    	<tr>
				    		<td colspan="6" style="text-align:right">Total: </td>
				    		<td colspan="<?php echo $awarded->status=='incomplete'?10:5;?>">$ <?php echo round($grandtotal,2);?></td>
				    	</tr>
				    </table>
			    </div>
				



            <?php
            if (@$messages)
                foreach ($messages as $c)
                    if (@$c['messages']) {
            ?>
                Messages for <?php echo $c['companydetails']->title ?> regarding PO# <?php echo $quote->ponum; ?>:
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
                            <td><?php echo $msg->senton; ?></td>
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
		    
		    <hr/>


            <div class="well">
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
			    
	    					<?php
                               if($recsum==$qntsum){ $class="complete"; $closed ="active complete"; }else{ $class="active late"; $closed ="future"; }
                            ?>
                            <section id="progress">
                              <ul><li class="<?php echo (!$quote->podate)?"active":"complete";?>">Created <span><?php echo date('M d,Y', strtotime($quote->podate));?></span></li><li class="<?php echo (!$awarded->awardedon)?"active":"complete";?>">Issued Date <span><?php echo date('M d,Y', strtotime($awarded->awardedon));?></span></li><li class="<?php echo (!$awarded->items[0]->daterequested)?"active":"complete";?>">Delivery Expected Date <span><?php echo date('M d,Y', strtotime($awarded->items[0]->daterequested)); ?></span></li><li class="<?php echo $class;?>"><?php echo $per; ?> Received <span>&nbsp;</span></li><li class="<?php echo $closed; ?>">Closed</li></ul>
                            </section>
                             <div>
                                <h3>Time Line</h3>
                                <div>
                                    <table width="100%">
                                        <tr><td style="border-right:2px black solid;" width="10%">
                               <?php echo date('m/d/Y', strtotime($awarded->awardedon));?>&nbsp;</td><td width="90%">&nbsp;&nbsp;&nbsp;PO #<?php echo $quote->ponum; ?> Submitted</td>
                               </tr>
                               <tr>
                                   <td style="border-right:2px black solid;" width="10%">&nbsp;</td>
                                   <td >
                               <?php foreach($awarded->invoices as $invoice){?>
                                    <div class="label label-pink"><?php echo '#'.$invoice->invoicenum; ?></div>
                                    <div class="clear"></div>
                                    <table width="100%">
                                    <?php
                                            foreach($invoice->items as $item) { 
                                        ?>
                                           <tr><td  style="border-right:2px #dff0d8 solid;" width="15%">
                                    <span><?php echo date('m/d/Y', strtotime($item->receiveddate)); ?></span>&nbsp;</td><td> &nbsp;&nbsp;&nbsp;<span><?php echo '<b>'.$item->itemname.'</b> - '.$item->quantity.' Received'; ?></span></td>
                                           </tr>
                                    <?php } ?></table><?php  }?>
                                    </td>
                               </tr>
                                    </table>
                                </div>
                            </div>
                </div>
    </div>
</section>