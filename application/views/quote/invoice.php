<script type="text/javascript">

    function PrintElem(elem)
    {
        PopupPrint($(elem).html());
    }

    function PopupPrint(data)
    {
        var mywindow = window.open('', 'my div', 'height=100,width=100,left=100,top=100');
        mywindow.document.write('<html><head><title>my div</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write(data);
        mywindow.document.write('</body></html>');
        mywindow.print();
        mywindow.close();

        return true;
    }
</script>

<div class="content">
    	<?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			<h3>&nbsp;</h3>
		</div>

	   <div id="container">
		<div class="row">
                    <div class="col-md-11">
                        <div class="grid simple ">
                            <div class="grid-title no-border">
                                <h4>&nbsp;</h4>

                            </div>
                            <div id="invoicewrapper" class="grid-body no-border">

			                  <table width="100%" >
			                  <tr>
			                  <td>
			                  	<h2>Invoice#: <?php echo $invoice->invoicenum;?></h2>
			                  </td>
			                  <td>
			                  	Payment: <?php echo $invoice->paymentstatus;?>
			                  	<?php if($invoice->paymentstatus=='Unpaid'  || $invoice->status =='Error'){?>
			                  	<form action="<?php echo site_url('quote/requestpayment');?>" method="post">
			                  		<input type="hidden" name="invoicenum" value="<?php echo $invoice->invoicenum;?>"/>
			                  		<input type="submit" value="Request Payment">
			                  	</form>
			                  	<?php }elseif($invoice->paymentstatus=='Paid'){?>
			                  	Payment type: <?php echo $invoice->paymenttype;?>
			                  	Ref#: <?php echo $invoice->refnum;?>
			                  	<?php }?>
			                  	Verification: <?php echo $invoice->status;?>
			                  	<?php if($invoice->status=='Pending' && $invoice->paymentstatus=='Paid'){?>
			                  	<form action="<?php echo site_url('quote/invoicestatus');?>" method="post">
			                  		<input type="hidden" name="invoicenum" value="<?php echo $invoice->invoicenum;?>"/>
			                  		<input type="hidden" name="invoicequote" value="<?php echo $invoice->quote;?>"/>
			                  		<input type="submit" name="status" value="Verified">
			                  		<input type="submit" name="status" value="Error">
			                  	</form>
			                  	<?php }?>
			                  	<?php if($invoice->datedue){?>
			                  	<br/>Date Due: <?php  echo date("m/d/Y", strtotime( $invoice->datedue)); ?>
			                  	<?php if($invoice->paymentstatus=='Paid'){?>
			                  	<br/>Paid: <?php  echo date("m/d/Y", strtotime( $invoice->paymentdate)); ?>
			                  	<?php }?>
			                  	<br/>Status:
			                  	<?php if($invoice->paymentstatus=='Paid'){?>
			                  		<?php echo ($invoice->datedue == $invoice->paymentdate?'Paid on time':($invoice->datedue > $invoice->paymentdate?'Paid Early':'Paid Late'))
			                  				.' on '. date("m/d/Y", strtotime( $invoice->paymentdate));?>
			                  	<?php }else{?>
			                  	    <?php echo $invoice->datedue > date('Y-m-d')?'Upcoming':'Overdue';?>
			                  	<?php }?>

			                  	<?php }?>
			                  </td>

			              	  <td valign="top">
				              	 <div class="pull-right">
					              <h2><strong>INVOICE</strong></h2>
					            </div>

			              	  </td>
			                  </tr>
			                  </table>

			                  <br/>
							    <br/>

							    <table width="100%">
							    <tr>
							    <td width="50%" valign="top">

				                <table width="90%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
							      <tr>
							        <th colspan="3" valign="top" bgcolor="#000033" ><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>
						          </tr>
							      <tr>
							        <td width="33%" valign="top">PO#</td>
							        <td width="7%" valign="top">&nbsp;</td>
							        <td width="60%" valign="top"><?php echo $quote->ponum?></td>
							      </tr>
							      <tr>
							        <td valign="top">Subject</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php echo $quote->subject;?></td>
							      </tr>
							      <tr>
							        <td valign="top">PO# Date</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php echo $quote->podate;?></td>
							      </tr>
							    </table>
							    <br/>
							    <table width="90%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
							      <tr>
							        <th bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></th>
							      </tr>
							      <tr>
							        <td><?php echo nl2br($awarded->shipto);?></td>
							      </tr>
							    </table>
							    </td>
							    <td width="50%" align="right">
							     <table cellspacing="0" width="90%" cellpadding="4" style="border:1px solid #000;">
							      <tr>
							        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></th>
							      </tr>
							     <tr>
							        <td width="33%" valign="top">Contact</td>
							        <td width="7%" valign="top">&nbsp;</td>
							        <td width="60%" valign="top"><?php if(isset($purchasingadmin->fullname)) echo $purchasingadmin->fullname; else echo '';?></td>
							      </tr>
							      <tr>
							        <td valign="top">Company</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php if(isset($purchasingadmin->companyname)) echo $purchasingadmin->companyname; else echo '';?></td>
							      </tr>
							      <tr>
							        <td valign="top">Address</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php if(isset($purchasingadmin->address)) echo $purchasingadmin->address; else echo '';?></td>
							      </tr>
							      <tr>
							        <td valign="top">Phone</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php if(isset($purchasingadmin->phone)) echo $purchasingadmin->phone; else echo '';?></td>
							      </tr>
							      <tr>
							        <td valign="top">Fax</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php if(isset($purchasingadmin->fax)) echo $purchasingadmin->fax; else echo '';?></td>
							      </tr>
							    </table>
							    <br/>
							     <table width="90%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
							      <tr>
							        <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
							        </tr>
							      <tr>
							        <td width="33%" valign="top">Project Title</td>
							        <td width="7%" valign="top">&nbsp;</td>
							        <td width="60%" valign="top"><?php echo $project->title;?></td>
							      </tr>
							      <tr>
							        <td valign="top">Address</td>
							        <td valign="top">&nbsp;</td>
							        <td valign="top"><?php echo nl2br($project->address);?></td>
							      </tr>
							    </table>

							    </td>
							    </tr>
							    </table>
							   <br/>
							   <table><tr><td style="text-align:center;"><?php echo ($invoice->invoice_type == "fullpaid" || $invoice->invoice_type == "alreadypay")?"* Pre-Paid Invoice":"";?></td></tr></table>		
							   <br/>

							<table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
							  <thead>
							 <tr>
							    <th bgcolor="#000033"><font color="#FFFFFF">Item No</font></th>
							    <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
							    <th bgcolor="#000033" width="150"><font color="#FFFFFF">Date Requested</font></th>
							    <th bgcolor="#000033" width="150"><font color="#FFFFFF">Date Received</font></th>
							    <th bgcolor="#000033" width="75"><font color="#FFFFFF">Quantity</font></th>
							    <th bgcolor="#000033" width="45"><font color="#FFFFFF">Unit</font></th>
							    <th bgcolor="#000033" width="120" align="right"><font color="#FFFFFF">Unit Price</font></th>
							    <th bgcolor="#000033" width="150" align="right"><font color="#FFFFFF">Total Price</font></th>
							  </tr>
							  </thead>
							  <?php
							  	$totalprice = 0;
							  	$i = 0;
							  	
							  	foreach($invoice->items as $invoiceitem)
								{
									if ($invoiceitem->item_img && file_exists('./uploads/item/' . $invoiceitem->item_img)) 
									 { 
									 	 $imgName = site_url('uploads/item/'.$invoiceitem->item_img); 
									 } 
									 else 
									 { 
									 	 $imgName = site_url('uploads/item/big.png'); 
                                     }
									$invoiceitem = (array)$invoiceitem;
									$totalprice += $invoiceitem['ea'] * (($invoiceitem['invoice_type'] != "fullpaid")?(($invoiceitem['invoice_type'] == "alreadypay")?0:$invoiceitem['quantity']):$invoiceitem['aiquantity']);
									$quantity = ($invoiceitem['invoice_type'] != "fullpaid")?(($invoiceitem['invoice_type'] == "alreadypay")?0:$invoiceitem['quantity']):$invoiceitem['aiquantity'];
									$olddate=strtotime($invoiceitem['receiveddate']); $newdate = date('m/d/Y', $olddate);
									echo '<tr nobr="true">
									    <td><img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='.$imgName.'></td>
									    <td>'.htmlentities($invoiceitem['itemname']).'</td>
									    <td>'.$invoiceitem['daterequested'].'</td>
									    <td>'.$newdate.'</td>
									    <td>'.(($invoiceitem['invoice_type'] != "fullpaid")?$invoiceitem['quantity']:$invoiceitem['aiquantity']).'</td>
									    <td>'.$invoiceitem['unit'].'</td>
									    <td align="right">$ '.$invoiceitem['ea'].'</td>
									    <td align="right">$ '.$invoiceitem['ea'] * $quantity.'</td>
									  </tr>
									  ';
								}
								$taxtotal = $totalprice * $config['taxpercent'] / 100;
								$grandtotal = $totalprice + $taxtotal;
								echo '<tr>
								    <td colspan="6" rowspan="3">

			                  		<div style="width:70%">
			                  		<br/>
			                  		<h4 class="semi-bold">Terms and Conditions</h4>
			                    <p>'.$company->invoicenote.'</p>
			                    <h5 class="text-right semi-bold">Thank you for your business</h5>
			                  		</div>
			                  		</td>
								    <td align="right">Subtotal</td>
								    <td align="right">$ '. number_format($totalprice,2).'</td>
								  </tr>
								  <tr>

								    <td align="right">Tax</td>
								    <td align="right">$ '. number_format($taxtotal,2).'</td>
								  </tr>
								  <tr>
								    <td align="right"><strong>Total</strong></td>
								    <td align="right"><strong>$ '.number_format($grandtotal,2).'</strong></td>
								  </tr>
								';                
							  ?>
				    	</table>
								 <?php 
							        if($invoice->sharewithsupplier == 1 && @$invoice->attachmentname)
							        	{ ?>
							        	<a href="<?php echo site_url('uploads/invoiceattachments/'.$invoice->attachmentname);?>" target="_blank">View Attached File</a>
							     <?php   } ?>
                            </div>
                        </div>
                    </div>
			
				     <div class="col-md-1">
				        <div class="invoice-button-action-set">
				          <p>
				            <button type="button" class="btn btn-primary" onclick="PrintElem(invoicewrapper)"><i class="fa fa-print"></i></button>
				          </p>
				        </div>
				      </div>

                </div>
      </div>

		</div>