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
	
	
<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
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
                <?php echo $addlink;?>
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
            <?php if(!@$items) echo "No Records Exist"; ?>
           </div>
         </div>
         
         <div>
          <?php 
         if(isset($items)){
         ?>
         <script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
		
		<span>Filter chart by month: </span>
		<div class="select chart-month" data-resize="auto">
			<button type="button" data-toggle="dropdown" class="btn dropdown-toggle chart-month-trigger">
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
				  style: {
					 fontSize: '15px'
				  }
			   }
			};
			Highcharts.setOptions(Highcharts.theme);
             var seriesData = new Array();
             var dataData = new Array();
			 var items = new Array();
			 
			 
                <?php foreach($items as $item){ ?>
					var dateItem =new Date("<?php echo $item->daterequested;?>");
					
					if(!items['<?=$item->daterequested?>'])
					{
						var costItem = "<?php echo $item->totalprice; ?>";
						costItem = parseFloat(costItem.slice(1)); 
						items['<?=$item->daterequested?>'] = {name:"<?php echo $item->ponum;?> = <?php echo $item->totalprice;?>",x:Date.UTC(dateItem.getFullYear(),dateItem.getMonth(),dateItem.getDate()),y:costItem, date: dateItem};
					}
					else
					{
						
						var costItem = "<?php echo $item->totalprice; ?>";
						costItem = parseFloat(costItem.slice(1)); 
						items['<?=$item->daterequested?>'].y += costItem;
						items['<?=$item->daterequested?>'].name += ", <?php echo $item->ponum;?> = <?php echo $item->totalprice;?>";
					}
					
			<?php } ?>
			
			
			function daysInMonth(month,year) {
				return new Date(year, month, 0).getDate();
			}
			
			function changeMonth()
			{	var today = new Date();
				var year  = new Date().getFullYear();
				
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
				
				var k=0;
				for(var i in items)
				{
					if(items[i].date.getMonth() + 1 == month)
					{   datearr1[k] = i;
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
					monthdt = 0+month;
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
				<?php if(isset($orders)) { ?>
				<?php if(isset($title_orders)){?><h3 class="box-header"><?php echo $title_orders; ?></h3><?php } ?>
                <table id="datatable" class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width:5%">S.No.</th>
                            <th style="width:15%">Order#</th>
                            <th style="width:20%">Ordered On</th>
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
				      ?>
                        <tr>
                            <td><?php echo $order->sno;?></td>
                            <td><?php echo $order->ordernumber;?></td>
                            <td><?php echo $order->purchasedate;?></td>
                            <td><?php if(isset($order->prjName)) echo $order->prjName;?></td>
                            <td><?php echo $order->type;?></td>
                            <td><?php echo $order->txnid;?></td>
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
            	<tr><td>No Purchase Orders.</td></tr>
            	</table>
            	<?php }?>
            </div>
      </div>
    </div>
</section>