
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
	

<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/bootstrap-slider.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/slider.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<?php echo '<script type="text/javascript">var updateprogressurl = "'.site_url('admin/costcode/updateprogress/').'";</script>';?>

<script>
$(document).ready(function(){
	$('.slider').slider({value:0});
	$(".slider").on('slideStop', function(slideEvt) {
		if(confirm('Do you want to change the value?'))
		{
			id=this.id;
			id=id.replace('progress','');
			v=slideEvt.value;
			d = "id="+id+"&manualprogress="+v;
			//alert(d);
			$.ajax({
			      type:"post",
			      data: d,
			      url: updateprogressurl
			    }).done(function(data){
				   $("#progresslabel"+id).html(v+'%');
				   var b = $("#budget"+id).val().replace('%','');
				   $("#progress"+id +" .tooltip-inner").text(v);
				   
				   if(b<=v)
					   $("#status"+id).html("<img src='<?php echo site_url('templates/admin/images/ok.gif');?>'/>");
				   else
					   $("#status"+id).html("<img src='<?php echo site_url('templates/admin/images/bad.png');?>'/>");
			    });
		}
		else
		{
			var v = $("#progresslabel"+id).html().replace('%','');
			//$("#progress"+id).attr('data-slider-value',v);
			$("#progress"+id).val(v);
			$("#progress"+id +" .tooltip-inner").text(v);
		}
		return false;
	});
		
});
function setprogress(id)
{
	
}
</script>


<section class="row-fluid">
	<h3 class="box-header"><?php echo $heading; ?></h3>
	<div class="box">
	  <div class="span12">
	
	   <?php echo $this->session->flashdata('message'); ?>
	    
		<div style="margin-bottom:20px;">
                <div>
                	<?php echo $addlink;?>
                	<br/><br/>
	                <div class="datagrid-header-right">
						<form class="form-inline" action="<?php echo site_url('admin/costcode');?>" method="post">
							Filter by parent: 
							<select name="parentfilter" onchange="this.form.submit()">
								<option value="">View All</option>
								<?php echo $parentcombooptions;?>
							</select>
							Filter by Project: 
							<select name="projectfilter" onchange="this.form.submit()">
								<option value="">View All</option>
								<?php foreach($projects as $p){?>
						      	<option value="<?php echo $p->id;?>" <?php if($p->id==@$_POST['projectfilter']){echo 'SELECTED';}?>>
						      		<?php echo $p->title;?>
						      	</option>
						      	<?php }?>
							</select>
						</form>
					</div>
                </div>
            
            <table id="datatable" class="table table-bordered datagrid">
              <tr>
              	<th width="20%">Code</th>
              	<th width="7%">Budget</th>
              	<th width="9%">$ Spent</th>
              	<th width="25%">Budget % Allocated</th>
              	<th>Task Progress % Complete</th>
              	<th>Status</th>
              	<th width="10%">Actions</th>
              </tr>
              <?php foreach($items as $item){?>
              <input type="hidden" id="budget<?php echo $item->id;?>" value="<?php echo $item->budgetper;?>"/>
              <tr>
              	<td><span class='cost-code'><?php echo $item->code?></span></td>
              	<td><?php echo $item->cost?></td>
              	<td><span class='total-spent'><?php echo $item->totalspent?></span></td>
              	<td id=""><?php echo $item->budget?></td>
              	<td id="progress<?php echo $item->id;?>"><span class='task-progress' style='display: none;'><?php echo $item->manualprogress;?></span><?php echo $item->manualprogressbar?></td>
              	<td id="status<?php echo $item->id;?>"><?php echo $item->status?></td>
              	<td><?php echo $item->actions?></td>
              </tr>
              <?php }?>
            </table>
            <div id="container-highchart" class="span4" style="min-width: 200px ;height: 500px; margin: 0 auto; width:60%"></div>
		   <script type="text/javascript">
		   $(function () {
			   var spent = new Array;
               var prog = new Array;
               var cc = new Array;
               var ser = new Array;
               
               $(".total-spent").each(function(index){ spent.push( parseFloat($( this ).text().slice(1) ));});
               $(".task-progress").each(function(index){ prog.push(parseInt($( this ).text()) );});
               $(".cost-code").each(function(index){ cc.push($( this ).text() );});
          	  for(var index=0;index<prog.length;index++){
              	  if(spent[index]==0)
              		prog[index] = parseFloat((spent[index] * 100 ));
              	  else
					prog[index] = parseFloat(parseFloat((spent[index] * 100 ) / prog[index]).toFixed(1));
                }
              console.log(prog);
          	ser[0] ={"name":"Spent","data":spent};
     		ser[1] = {"name":"Estimated","data":prog};
		        $('#container-highchart').highcharts({
		            chart: {
		                type: 'bar'
		            },
		            title: {
		                text: 'Estimated Cost To Complete'
		            },
		            subtitle: {
		                text: ''
		            },
		            xAxis: {
		                categories: cc,
		                title: {
		                    text: null
		                }
		            },
		            yAxis: {
		                min: 0,
		                title: {
		                    text: '$',
		                    align: 'high'
		                },
		                labels: {
		                    overflow: 'justify'
		                }
		            },
		            tooltip: {
		                valueSuffix: ' $'
		            },
		            plotOptions: {
		                bar: {
		                    dataLabels: {
		                        enabled: true
		                    }
		                }
		            },
		            plotOptions: {
	                       series: {
	                           borderWidth: 0,
	                           dataLabels: {
	                               enabled: true,
	                               format: '$ {point.y:.1f}'
	                           }
	                       }
	                   },
		            legend: {
		                layout: 'vertical',
		                align: 'right',
		                verticalAlign: 'top',
		                x: -40,
		                y: 100,
		                floating: true,
		                borderWidth: 1,
		                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor || '#FFFFFF'),
		                shadow: true
		            },
		            credits: {
		                enabled: false
		            },
		            series: ser
		        });
		    });
		    
		   </script>
            <?php if(!$items){?>
            No Costcodes Found.
            <?php }?>
           </div>

      </div>
    </div>
</section>