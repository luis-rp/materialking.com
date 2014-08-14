
<section class="row-fluid">
    <h3 class="box-header"><?php echo @$heading; ?> - <?php echo ($this->session->userdata('managedprojectdetails')) ? $this->session->userdata('managedprojectdetails')->title : "no project title" ?></h3>
    <div class="box">
        <div class="span12">
            <a class="btn btn-green" href="javascript:void(0)" onclick="history.back();">&lt;&lt; Back</a>
            <br/>
            <div align="center">
                <h4>
                    INVOICE #: <?php echo $invoice->invoicenum; ?> 
                    STATUS: <font color=#FF0000""> <?php echo $invoice->status; ?></font>
                    PAYMENT: <font color=#FF0000""> <?php echo $invoice->paymentstatus; ?></font>
                    <?php if($invoice->datedue){?>
                    DUE DATE: <font color=#FF0000""> <?php  echo date("m/d/Y", strtotime( $invoice->datedue)); ?></font>
                    <br/>
                    <font color=#FF0000""> <?php if($invoice->status == "Verified" && $invoice->paymentstatus == "Paid" && isset($invoice->items[0]->paymentdate)) {  
                    // echo "<pre>",print_r($invoice);
                    	if(strtotime($invoice->items[0]->paymentdate) < strtotime($invoice->datedue))
                    	echo "PAID EARLY";
                    	elseif (strtotime($invoice->items[0]->paymentdate) == strtotime($invoice->datedue))
                    	echo "PAID ON TIME";
                    	elseif (strtotime($invoice->items[0]->paymentdate) > strtotime($invoice->datedue))
                    	echo "PAID LATE";
                    } else { echo $invoice->datedue >= date('Y-m-d')?'Upcoming':'Overdue'; } ?></font>
                	<?php }?>
                </h4>
            </div>
            
            <div class="newbox">
            <table width="100%" cellspacing="2" cellpadding="2">
                <tr>
                    <td width="33%" align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Project Information</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">Project Title</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $project->title; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Address</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $project->address; ?></td>
                            </tr>
                        </table>
                    </td>
                    <td width="10" align="left" valign="top">&nbsp;</td>
                    <td width="65%" align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>Purchase Order Information</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">PO#</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $quote->ponum ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Subject</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $quote->subject; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">PO# Date</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $quote->podate; ?></td>
                            </tr>
                        </table></td>
                </tr>
                <tr>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">&nbsp;</td>
                </tr>
                <tr>
                    <td align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th colspan="3" valign="top" bgcolor="#000033"><font color="#FFFFFF"><strong>From</strong></font></th>
                            </tr>
                            <tr>
                                <td width="33%" valign="top">Contact</td>
                                <td width="7%" valign="top">&nbsp;</td>
                                <td width="60%" valign="top"><?php echo $purchasingadmin->fullname; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Company</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->companyname; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Address</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo nl2br($purchasingadmin->address); ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Phone</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->phone; ?></td>
                            </tr>
                            <tr>
                                <td valign="top">Fax</td>
                                <td valign="top">&nbsp;</td>
                                <td valign="top"><?php echo $purchasingadmin->fax; ?></td>
                            </tr>
                        </table></td>
                    <td align="left" valign="top">&nbsp;</td>
                    <td align="left" valign="top">
                        <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                            <tr>
                                <th bgcolor="#000033"><font color="#FFFFFF"><strong>Ship to</strong></font></th>
                            </tr>
                            <tr>
                                <td><?php echo nl2br($awarded->shipto); ?></td>
                            </tr>
                        </table></td>
                </tr>

            </table>

            <table width="100%" cellspacing="0" cellpadding="4">
                <tr>
                    <td>Items:</td>
                </tr>
            </table>

            <br/>

            <table width="100%" cellspacing="0" cellpadding="4" style="border:1px solid #000;">
                <thead>
                    <tr>
                        <th bgcolor="#000033"><font color="#FFFFFF">Item No.</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Description</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Company</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Date Requested</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Date Received</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Quantity</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Unit</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Unit Price</font></th>
                        <th bgcolor="#000033"><font color="#FFFFFF">Total Price</font></th>
                    </tr>
                </thead>
                <?php
                $totalprice = 0;
                $i = 0;
                foreach ($invoice->items as $invoiceitem) {
                    $invoiceitem = (array) $invoiceitem;
                    $totalprice += $invoiceitem['ea'] * $invoiceitem['quantity'];
                    echo '<tr nobr="true">
						    <td style="border: 1px solid #000000;">' . ++$i . '</td>
						    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['itemname']) . '</td>
						    <td style="border: 1px solid #000000;">' . htmlentities($invoiceitem['companyname']) . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['daterequested'] . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['receiveddate'] . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['quantity'] . '</td>
						    <td style="border: 1px solid #000000;">' . $invoiceitem['unit'] . '</td>
						    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] . '</td>
						    <td align="right" style="border: 1px solid #000000;">$ ' . $invoiceitem['ea'] * $invoiceitem['quantity'] . '</td>
						  </tr>
						  ';
                }
                $taxtotal = $totalprice * $config['taxpercent'] / 100;
                $grandtotal = $totalprice + $taxtotal;
                echo '<tr>
					    <td colspan="7" rowspan="3">
                      		<div style="width:70%">
                          		<br/>
                          		<h4 class="semi-bold">Terms and Conditions</h4>
                                <p>'.$company->invoicenote.'</p>
                                <h5 class="text-right semi-bold">Thank you for your business</h5>
                      		</div>
                  		</td>
					    <td align="right">Subtotal</td>
					    <td align="right">$ ' . number_format($totalprice, 2) . '</td>
					  </tr>
					  <tr>
					    <td align="right">Tax</td>
					    <td align="right">$ ' . number_format($taxtotal, 2) . '</td>
					  </tr>
					  <tr>
					    <td align="right">Total</td>
					    <td align="right">$ ' . number_format($grandtotal, 2) . '</td>
					  </tr>
					';
                ?>
            </table>
            </div>
        </div>
    </div>
</section>