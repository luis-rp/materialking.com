
	<style type="text/css">
		.box { padding-bottom: 0; }
		.box > p { margin-bottom: 20px; }

		#popovers li, #tooltips li {
			display: block;
			float: left;
			list-style: none;
			margin-right: 20px;
		}
		
		#lastpbar div.bar
		{
		 max-width:100%;
		}

		.adminflare > div { margin-bottom: 20px; }
	</style>

<script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/bootstrap-slider.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>
<script src="http://code.highcharts.com/modules/data.js"></script>
<script src="http://code.highcharts.com/modules/drilldown.js"></script>
<link href="<?php echo base_url(); ?>templates/admin/css/slider.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css">
<?php echo '<script type="text/javascript">var updateprogressurl = "'.site_url('admin/costcode/updateprogress/').'";</script>';?>
<?php echo '<script type="text/javascript">var getchildcostcodeurl = "'.site_url('admin/costcode/getchildcostcode/').'";</script>';?>

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
				   location.reload();   
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

	 tour6= new Tour({
		  steps: [
		  {
		    element: "#step1",
		    title: "Step 1",
		    content: "Welcome to the on-page tour for Cost Code managment"
		  },


		]
		});

		$("#activatetour").click(function(e){
			  e.preventDefault();
				$("#tourcontrols").remove();
				tour6.restart();
				// Initialize the tour
				tour6.init();
				start();
			});
		$('#canceltour').live('click',endTour);
});
function setprogress(id)
{

}
function start(){

	// Start the tour
		tour6.start();
	 }
function endTour(){

	 $("#tourcontrols").remove();
	 tour6.end();
		}
		
function getchildcostcode(obj,parentid,costcodeid)
{	
	if($("#isexpand_"+costcodeid).text() == 'Expand')
	{
		$.ajax({
		      type:"post",
		      data: 'parent='+parentid,
		      url: getchildcostcodeurl
		    }).done(function(data)
		    {
			   //$('#costcode_'+costcodeid).append(data);
			   
			   $('#costcode_'+costcodeid).eq(0).after(data);
			   $('.slider1').slider({value:0});
			   $(".slider1").on('slideStop', function(slideEvt) {
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
							   location.reload();   
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
		    
		    
		 $("#isexpand_"+costcodeid).text('Collapse');  
	}
	else
	{
		//if($("#isexpand_"+costcodeid).text() == 'Collapse')
		$(".clscostcode_"+parentid).hide();
		$("#isexpand_"+costcodeid).text('Expand');  
	}
		
	
}
</script>
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>

<section class="row-fluid">

	<h3 class="box-header" style="display:inline" ><span id="step1"><?php echo $heading; ?></span>  <?php echo $addlink;?>
                	<a href="<?php echo site_url('admin/costcode/costcodeexport').'/'.@$_POST['projectfilter']; ?>" class="btn btn-green">Export</a>&nbsp;<a href="<?php echo site_url('admin/costcode/costcodepdf').'/'.@$_POST['projectfilter']; ?>" class="btn btn-green">View PDF</a></h3>
	<div class="box">
	  <div class="span12">

	  <?php echo $this->session->flashdata('message'); ?>

		<div style="margin-bottom:20px;">
                <div>
                	
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
								<option value="viewall">View All</option>
								<?php foreach($projects as $p){?>
						      	<option value="<?php echo $p->id;?>" <?php if($p->id==@$_POST['projectfilter']){echo 'SELECTED';}?>>
						      		<?php echo $p->title;?>
						      	</option>
						      	<?php }?>
							</select>
						</form>
					</div>
                </div>
            <?php if($items){?>
            <table id="datatable" class="table table-bordered datagrid">
              <tr>
              	<th width="18%">Code</th>
              	<th width="8%">Image</th>
              	<th width="7%">Budget</th>
              	<th width="7%">$ Spent</th>
              	<th width="25%">Budget % Allocated</th>
              	<th width="22%">Task Progress % Complete</th>
              	<th width="5%">Status</th>
              	<th width="8%">Actions</th>
              </tr>
              <?php foreach($items as $item)
              {
              	$imgName = '';
              	 if (isset($item->costcode_image) && $item->costcode_image != '' && file_exists('./uploads/costcodeimages/' . $item->costcode_image)) 
				 { 
				 	 $imgName = site_url('uploads/costcodeimages/'.$item->costcode_image); 
				 } 
				
              	?>
              <input type="hidden" id="budget<?php echo $item->id;?>" value="<?php echo $item->budgetper;?>"/>
              <tr id='costcode_<?php echo $item->id;?>' class="clscostcode_<?php echo $item->parent;?>">
              	<td><span class='cost-code'><?php echo $item->code?></span></td>
              	<td><?php if($imgName != '') { ?> <img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;" src='<?php echo $imgName;?>'> <?php } ?></td>
              	<td><?php echo $item->cost?></td>
              	<td><span class='total-spent'><?php $shipping = 0; if(isset($item->shipping)) $shipping = $item->shipping; echo "$ ".round( ($item->totalspent + $item->totalspent*($taxrate->taxrate/100) + $shipping),2 ); ?></span></td>
              	<td id="lastpbar"><?php echo $item->budget;?></td>
              	
              
              	<td id="progress<?php echo $item->id;?>">
              	 	<?php //if($item->estimate!=1) { ?>
              	<span class='task-progress' style='display: none;'>            	
              	<?php  echo $item->manualprogress; ?>
              	</span>
              	
              	<span class='turnoff-estcost' style='display: none;'>            	
              	<?php  if(@$item->estimate ==1) { echo "yes"; } else { echo "no"; } ?>
              	</span>
              	
              	<?php //} ?>
              	<?php if(@$item->estimate!=1) { echo $item->manualprogressbar; } else { echo ""; }?></td>
              	<td id="status<?php echo $item->id;?>"><?php echo $item->status?></td>
              	<td><?php echo $item->actions?></td>
              </tr>
              <?php }?>
            </table>
            <?php }?>
               <?php if($items){?>
            <div id="container-highchart" class="span4" style="min-width: 200px ;height: 500px; margin: 0 auto; width:100%"></div><?php } ?>
		   <script type="text/javascript">
		   $(function () {
			   var spent = new Array;
               var prog = new Array;
               var turnoff = new Array;
               var cc = new Array;
               var ser = new Array;

               $(".total-spent").each(function(index){ spent.push( parseFloat($( this ).text().slice(1) ));});
               $(".task-progress").each(function(index){ prog.push(parseInt($( this ).text()) );});
               $(".turnoff-estcost").each(function(index){ turnoff.push($( this ).text().trim());});
               $(".cost-code").each(function(index){ cc.push($( this ).text() );});
          	  for(var index=0;index<prog.length;index++){
              	  if(prog[index]==0)
              		prog[index] = parseFloat(spent[index] * 100 );
              	  else
					prog[index] = parseFloat(parseFloat((spent[index] * 100 ) / prog[index]).toFixed(1));
				
				 if(turnoff[index] == "yes" ){ 	
					prog[index] = 0;					
				 }	
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
            <div class="alert alert-error"><a data-dismiss="alert" class="close" href="#">X</a><div class="msgBox"> No Costcodes Found.</div></div>
           
            <?php }?>
           </div>

      </div>
    </div>


</section>
