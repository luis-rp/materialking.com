<?php if(isset($jsfile)) include $this->config->config['base_dir'].'templates/admin/gridfeed/'.$jsfile;?>

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
	<?php if($jsfile=="costcodeitemjs.php") {?>
	 <script type="text/javascript">
	 $(document).ready(function(){
 tour7 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Items For Costode"
	  },


	]
	});

	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour7.restart();
			// Initialize the tour
			tour7.init();
			start7();
		});

	<?php if(!@$items2) { ?>	
    	$("#MyGrid").css("display","none");		
    <?php } ?>	
	 });
		$('#canceltour').live('click',endTour7);
	 function start7(){

			// Start the tour
				tour7.start();
			 }
	 function endTour7(){

		 $("#tourcontrols").remove();
		 tour7.end();
			}
			
	
 </script>
	<?php }?>
	<?php if($jsfile=="itemcodeitemjs.php") {?>
	 <script type="text/javascript">
	 $(document).ready(function(){
 tour9 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for PO Items For Item"
	  },


	]
	});

	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour9.restart();
			// Initialize the tour
			tour9.init();
			start9();
		});

	<?php if(!@$items) { ?>	
    	$("#MyGrid").css("display","none");		
    <?php } ?>	
	 });
		$('#canceltour').live('click',endTour9);
	 function start9(){

			// Start the tour
				tour9.start();
			 }
	 function endTour9(){

		 $("#tourcontrols").remove();
		 tour9.end();
			}

 </script>
	<?php }?>
	
	<script type="text/javascript">
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
			//$("#quoteitems").css({display: "none"});
			$("#quoteitemdetails").html(data);
			$("#quoteitemdetails").css({display: "block"});
			$("#quoteitemdetailsm").css({display: "block"});
			$("#quoteitemdetailsm").removeClass("hide");
			//$("#quoteitemdetailsm").modal();
		});
	}
	
	function closepop()
	{
		$("#quoteitemdetails").html('');
		$("#quoteitemdetails").css({display: "none"});
		$("#quoteitemdetailsm").css({display: "none"});
		$("#quoteitems").css({display: "block"});
	}
	</script>
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>
<section class="row-fluid">
	<h3 class="box-header" style="display:inline;" ><span id="step1"><?php echo $heading; ?></span>    <?php echo $addlink;?> </h3><?php echo @$poitemimage;?>
	<div class="box">
	  <div class="span12">

	   <?php echo $this->session->flashdata('message'); ?>

	    <div class="datagrid-example">
		<div style="height:600px;width:100%;margin-bottom:20px;">
            <table id="MyGrid" class="table table-bordered datagrid">
             <thead>
              <tr>
                <th>
                <div>
             
                </div>
                </th>
               </tr>
              </thead>
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
                        <?php if(0){?>
                        <button type="button" class="btn" data-toggle="dropdown"><i class="caret"></i></button>
                        <?php }?>
                        <ul class="dropdown-menu"></ul>
                        </div>
                        <span>of <span class="grid-pages"></span></span>
                        <button type="button" class="btn grid-nextpage"><i class="icon-chevron-right"></i></button>
                    </div>
                </div>
                </th>
               </tr>
              </tfoot>
            </table>
            <?php if($jsfile=="costcodeitemjs.php" && !@$items2) echo '<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Record Exist.</div></div>'; ?>
            <?php if($jsfile=="itemcodeitemjs.php" && !@$items) echo '<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Record Exist.</div></div>'; ?>
           </div>
         </div>

         <div>
          <?php
         if(isset($items2)){
         ?>
         <script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>

		<span>Filter chart: </span>
		
		<div class="select chart-year" data-resize="auto">
			Year:&nbsp;&nbsp;&nbsp;<button type="button" data-toggle="dropdown" class="btn dropdown-toggle chart-year-trigger">
			<span class="dropdown-label"></span>
			<span class="caret"></span>
			</button>
			<ul id="chartYear" class="dropdown-menu" style="top:auto" >
				<li class="chartYearItem" data-value="1" ><a href="#"><?php echo date("Y"); ?></a></li>
				<li class="chartYearItem" data-value="2" ><a href="#"><?php echo date("Y")-1; ?></a></li>
				<li class="chartYearItem" data-value="3" ><a href="#"><?php echo date("Y")-2; ?></a></li>
				<li class="chartYearItem" data-value="4" ><a href="#"><?php echo date("Y")-3; ?></a></li>
				<li class="chartYearItem" data-value="5" ><a href="#"><?php echo date("Y")-4; ?></a></li>
				<li class="chartYearItem" data-value="6" ><a href="#"><?php echo date("Y")-5; ?></a></li>
				<li class="chartYearItem" data-value="7" ><a href="#"><?php echo date("Y")-6; ?></a></li>
				<li class="chartYearItem" data-value="8" ><a href="#"><?php echo date("Y")-7; ?></a></li>
				<li class="chartYearItem" data-value="9" ><a href="#"><?php echo date("Y")-8; ?></a></li>
				<li class="chartYearItem" data-value="10" ><a href="#"><?php echo date("Y")-9; ?></a></li>				
			</ul>
		</div>
		<br>
		<div class="select chart-month" data-resize="auto">
			Month:<button type="button" data-toggle="dropdown" class="btn dropdown-toggle chart-month-trigger">
			<span class="dropdown-label"></span>
			<span class="caret"></span>
			</button>
			<ul id="chartMonth" class="dropdown-menu" style="top:auto" >
				<li class="chartMonthItem" data-value="1" <?=(date("m")==1?'data-selected="true"':'')?>><a href="#">January</a></li>
				<li class="chartMonthItem" data-value="2" <?=(date("m")==2?'data-selected="true"':'')?>><a href="#">February</a></li>
				<li class="chartMonthItem" data-value="3" <?=(date("m")==3?'data-selected="true"':'')?>><a href="#">March</a></li>
				<li class="chartMonthItem" data-value="4" <?=(date("m")==4?'data-selected="true"':'')?>><a href="#">April</a></li>
				<li class="chartMonthItem" data-value="5" <?=(date("m")==5?'data-selected="true"':'')?>><a href="#">May</a></li>
				<li class="chartMonthItem" data-value="6" <?=(date("m")==6?'data-selected="true"':'')?>><a href="#">June</a></li>
				<li class="chartMonthItem" data-value="7" <?=(date("m")==7?'data-selected="true"':'')?>><a href="#">July</a></li>
				<li class="chartMonthItem" data-value="8" <?=(date("m")==8?'data-selected="true"':'')?>><a href="#">August</a></li>
				<li class="chartMonthItem" data-value="9" <?=(date("m")==9?'data-selected="true"':'')?>><a href="#">September</a></li>
				<li class="chartMonthItem" data-value="10" <?=(date("m")==10?'data-selected="true"':'')?>><a href="#">October</a></li>
				<li class="chartMonthItem" data-value="11" <?=(date("m")==11?'data-selected="true"':'')?>><a href="#">November</a></li>
				<li class="chartMonthItem" data-value="12" <?=(date("m")==12?'data-selected="true"':'')?>><a href="#">December</a></li>
			</ul>
		</div>

        <div id="container-highchart" class="span4" style="min-width: 200px ;height: 500px; margin: 0 auto; width:60%"></div>

         <script type="text/javascript">
         $(function () {

			var month  = new Date().getMonth() + 1;
			//var month  = 4;
			Highcharts.theme = {
				tooltip: {

				  fontSize: '15px',
				   width: '750px',
				  style: {
					 fontSize: '15px',
					  width: '750px'
				  }
			   }
			};
			Highcharts.setOptions(Highcharts.theme);
             var seriesData = new Array();
             var dataData = new Array();
			 var items = new Array();


               <?php $j=0; foreach($items2 as $item){ ?>
					var dateItem =new Date("<?php echo $item->daterequested;?>");

					if(!items['<?=$j?>'])
					{
						var costItem = "<?php echo $item->totalprice; ?>";
						costItem = parseFloat(costItem.slice(1));
						items['<?=$j?>'] = {name:"<?php echo $item->ponum;?>",x:Date.UTC(dateItem.getFullYear(),dateItem.getMonth(),dateItem.getDate()),y:costItem, date: dateItem, datereq:'<?=$item->daterequested?>' };
					}
					else
					{

						var costItem = "<?php echo $item->totalprice; ?>";
						costItem = parseFloat(costItem.slice(1));
						items['<?=$j?>'].y += costItem;
						items['<?=$j?>'].name += ", <?php echo $item->ponum;?>";
						items['<?=$j?>'].datereq += '<?=$item->daterequested?>';
					}

			<?php $j++; } ?>


			function daysInMonth(month,year) {
				return new Date(year, month, 0).getDate();
			}

			function changeMonth()
			{
				month = ''+month+'';
				var today = new Date();
				
				if($('.chart-year').find('.dropdown-label').text()=="" || $('.chart-year').find('.dropdown-label').text()=="undefined")
					var year  = new Date().getFullYear();
				else
					var year = $('.chart-year').find('.dropdown-label').text();
					
				var tomorrow = new Date(today.getTime() + (24 * 60 * 60 * 1000));

				var firstdate = new Date(year, month-1,1);

				var utcdate;


				dataData = new Array();
				seriesData = new Array();
				datearr1 = new Array();

				dataname = new Array();
				datax = new Array();
				datay = new Array();
				datadate = new Array();

				items.sort(function(a,b) {
					if(a.date > b.date)
						return 1;
					else if (a.date == b.date)
						return 0;
					else
						return -1;
				});

				var k=0;
				for(var i in items)
				{
					if(items[i].date.getMonth() + 1 == month)
					{   datearr1[k] = items[i].datereq;
						console.log(items[i].datereq);
						//dataData.push({ name: items[i].name, x: items[i].x, y: items[i].y, date: items[i].date});
						dataname[k] = items[i].name;
						datax[k] = items[i].x;
						datay[k] = items[i].y;
						datadate[k] = items[i].date;
						k++;
					}
				}

				var jdt;
				var monthdt = "";
				var r = 0;
				for(var j=1;j<=daysInMonth(month,year);j++){
					if(month<10)
					monthdt = '0'+month;
					else
					monthdt = month;
					if(j<10){
						jdt = '0'+j;
					}
					else
					jdt = j;
					datefinal = ''+monthdt+"/"+jdt+"/"+year+'';

					if(datearr1.indexOf(datefinal)<= -1){
						utcdate = Date.UTC(year,month-1,j);
						dataData.push({ name: '', x: utcdate, y: 0, date: 'Date {'+firstdate+'}'});

					}else{
						dataData.push({ name: dataname[r], x: datax[r], y: datay[r], date: datadate[r]});
						r++;
					}
					firstdate = new Date(firstdate.getTime() + (24 * 60 * 60 * 1000));
					jdt = "";
					monthdt = "";
				}
				console.log(dataData);
				/*dataData.sort(function(a,b) {
					if(a.date > b.date)
						return 1;
					else if (a.date == b.date)
						return 0;
					else
						return -1;
				});*/

				seriesData.push({"name":"PO#","data":dataData, marker: {
					fillColor: 'white',
					lineWidth: 2,
					lineColor: Highcharts.getOptions().colors[0]
				}});
				 $('#container-highchart').highcharts({
					 chart: {
					 	type: 'line'
					 },
					 title: {
						 text: 'Monthly Overview'
					 },
					 subtitle: {
						 text: ''
					 },
					 xAxis: {
						 type: 'datetime',
						 //min:Date.UTC(2014, 5, 1),
        				 //max:Date.UTC(2014, 6, 1),
						 dateTimeLabelFormats: { // don't display the dummy year
							 month: '%e. %b',
							 year: '%b'
						 },
						 title: {
							 text: 'Date'
						 }
					 },
					 yAxis: {
						 title: {
							 text: 'Amount ($)'
						 },
						 min: 0
					 },
					 tooltip: {
				   //D      headerFormat: '<b>{series.data}</b><br>',
						 pointFormat: '{point.x:%e. %b}: ${point.y:.2f}'
					 },

					 series: seriesData
				 });

				 //$('.chart-month').find('.dropdown-label').text($('.chartMonthItem[data-value='+month+']').text());
			}

			changeMonth();


			$('.chartMonthItem').click(function () {
				  month = $(this).attr('data-value');
				  changeMonth();
			});
         });
         </script>
         <?php
         }
         ?>
				<?php 
				if(isset($jsfile) && ($jsfile=="costcodeitemjs.php")) {
					
				 ?>

                <table id="datatable" class="table table-bordered">
                    <thead>
                    	<tr><td colspan="8"><?php if(isset($bottomheading)){?><h3 class="box-header"><?php echo $bottomheading; ?></h3><?php } ?></td></tr>
                    <?php if( isset($orders) && count($orders) > 0) { ?>	
                        <tr>
                            <th style="width:5%">S.No.</th>
                            <th style="width:15%">Order#</th>
                            <th style="width:20%">Ordered On</th>
                            <th style="width:20%">Order Total</th>
                            <th style="width:20%">Project</th>
                            <th style="width:10%">Type</th>
                            <th style="width:10%">Txn ID</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
		              <?php
				    	$i = 0;
				    	foreach($orders as $order)
				    	{
				    		log_message('debug',var_export($order,true));
				    		$i++;
 							//$orderdetails=mysql_fetch_assoc(mysql_query(""));
							
							$sql = "SELECT sum(od.price*od.quantity) as totalprice  
							FROM ".$this->db->dbprefix('orderdetails')." od where od.orderid=".$order->id."";
							 $orderdetails = $this->db->query($sql)->result();
 				      ?>
                        <tr>
                            <td><?php echo $order->sno;?></td>
                            <td><?php echo $order->ordernumber;?></td>
                            <td><?php echo $order->purchasedate;?></td>
                            <td>$<?php echo number_format($orderdetails[0]->totalprice + $order->shipping + ($orderdetails[0]->totalprice * $order->taxpercent/100),2)
;?></td>
                            <td><?php if(isset($order->prjName)) echo $order->prjName;?></td>
                            <td><?php echo $order->type;?></td>
                            <td><?php echo ($order->txnid)?$order->txnid:$order->paymentnote;?></td>
                            <td>
                            	<a href="<?php echo site_url('admin/order/details/'.$order->id);?>">
                            		<span class="icon icon-search"></span>
                            	</a>
                            	<a href="<?php echo site_url('admin/order/add_to_project/'.$order->id);?>">
                            		<span class="icon icon-list-ul"></span>
                            	</a>
                            </td>
                        </tr>
                      <?php } ?>
                    </tbody>
                </table>
            	<?php }else{ ?>
            	<table id="datatable" class="table">
            	<tr><td><div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Store Orders Exist Associated to Cost Code.</div></div></td></tr>
            	</table>
            	<?php }
            	}
            	?>
            	
            	
            	<?php
				if(isset($jsfile) && ($jsfile=="itemcodeitemjs.php")){
				?>

                <table id="datatable" class="table table-bordered">
                    <thead>
                    	<tr><td colspan="8"><?php if(isset($bottomheading)){?><h3 class="box-header"><?php echo $bottomheading; ?></h3><?php } ?></td></tr>
                    	<?php if( isset($orders) && count($orders) > 0) { ?>
                        <tr>
                            <th style="width:5%">S.No.</th>
                            <th style="width:15%">Order#</th>
                            <th style="width:20%">Ordered On</th>
                            <th style="width:20%">Order Total</th>
                            <th style="width:20%">Qty</th>
                            <th style="width:20%">Project</th>
                            <th style="width:10%">Type</th>
                            <th style="width:10%">Txn ID</th>
                            <th style="width:10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>


		              <?php
				    	$i = 0;

				    	foreach($orders as $order)
				    	{
				    		log_message('debug',var_export($order,true));

				    		$i++;
							$sql = "SELECT sum(od.price*od.quantity) as totalprice, sum(shipping) as shipping
							FROM ".$this->db->dbprefix('orderdetails')." od where od.orderid=".$order->orderid." group by od.orderid";
							$orderdetails = $this->db->query($sql)->result();
 				       ?>
                        <tr>
                            <td><?php echo $order->sno;?></td>
                            <td><?php echo $order->ordernumber;?></td>
                            <td><?php echo $order->purchasedate;?></td>
                            <?php /*
                            $taxpercent = $order->taxpercent;
                            $tot=$order->price*$order->quantity;
                    	    $tax = $tot * $taxpercent/100;
                    	    $totalwithtax = $tax+$order->shipping+$tot;*/
                      ?>
                            <td>$<?php echo number_format($orderdetails[0]->totalprice + $orderdetails[0]->shipping + ($orderdetails[0]->totalprice * $order->taxpercent/100),2)
;?></td>
							<td><?php echo $order->quantity;?></td>
                             <!-- <td>$<?php echo number_format($totalwithtax,2);?></td> -->
                            <td><?php if(isset($order->prjName)) echo $order->prjName;?></td>
                            <td><?php echo $order->type;?></td>
                            <td><?php echo $order->txnid;?></td>
                            <td>
                            	<a href="<?php echo site_url('admin/order/details/'.$order->orderid);?>">
                            		<span class="icon icon-search"></span>
                            	</a>
                            	<a href="<?php echo site_url('admin/order/add_to_project/'.$order->orderid);?>">
                            		<span class="icon icon-list-ul"></span>
                            	</a>
                            </td>
                        </tr>
                      <?php }//die; ?>
                    </tbody>
                </table>
            	<?php }else{ ?>
            	<table id="datatable" class="table">
            	<div class="alert"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox">No Store Orders.</div></div>
            	</table>
            	<?php }
            	}
            	?>
            	
            	
            </div>
      </div>
    </div>
    <div id="itemsmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
            	<h3>Items<span id="minpriceitemcode"></span></h3>
        	</div>
        	<div class="modal-body" id="quoteitems">
        	
        	</div>
            
    </div>
    <div id="quoteitemdetailsm" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">
        	
            <div class="modal-header">
        		<input style="float:right;margin-top:2px;" type="button" id="cls" name="cls" class="btn btn-green" value="close" onclick="closepop();" />
        		
        	</div>
        	<div class="modal-body" id="quoteitemdetails">
        	</div>
            
    </div>
</section>