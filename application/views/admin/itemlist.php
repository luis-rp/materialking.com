<?php if (isset($jsfile)) include $this->config->config['base_dir'] . 'templates/admin/gridfeed/' . $jsfile; ?>
<?php echo '<script>var serviceurl = "'.base_url().'admin/itemcode/showeditform/";</script>';?>

<script>
	function updateitem(id)
	{
		var d = "itemid="+id;
        $.ajax({
            type: "post",
            url: serviceurl,
            data: d
        }).done(function(data) {
            $("#editbody").html(data);
            $("#editmodal").modal();
        });
	}
</script>
	 <script type="text/javascript">
	 $(document).ready(function(){
 tour8 = new Tour({
	  steps: [
	  {
	    element: "#step1",
	    title: "Step 1",
	    content: "Welcome to the on-page tour for Item Code Managment"
	  },


	]
	});

	$("#activatetour").click(function(e){
		  e.preventDefault();
			$("#tourcontrols").remove();
			tour8.restart();
			// Initialize the tour
			tour8.init();
			start();
		});

	$('#btndel').click(function(e){
		if(confirm('Are You Sure?')){
				var checkd = $('.del_group:checked')	;
				var itemdToSend = new Array();
				for( i = 0; i < checkd.length; i++){
					itemdToSend[i] = checkd[i].value;
					}
				//console.log(itemdToSend);
				$.ajax({
					url:"/admin/itemcode/delete_multiple",
					data:{items:itemdToSend},
					type:'POST'
					});
				}

		 });

		$('#canceltour').click(function(e){
			endTour();
			});

	 });


	 function start(){

			// Start the tour
				tour8.start();
			 }
	 function endTour(){

		 $("#tourcontrols").remove();
		 tour8.end();
			}
 </script>
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
 <?php if(isset($settingtour) && $settingtour==1) { ?>
<div id="tourcontrols" class="tourcontrols" style="right: 30px;">
<p>First time here?</p>
<span class="button" id="activatetour">Start the tour</span>
<span class="closeX" id="canceltour"></span></div><?php } ?>

<section class="row-fluid">
    <h3 class="box-header" style="display:inline;" id="step1"><?php echo $heading; ?>   <a href="<?php echo base_url("admin/itemcode/export");?>" class="btn btn-green">Export all items</a>  &nbsp;&nbsp; <a href="<?php echo base_url("admin/itemcode/itempdf");?>" class="btn btn-green">View PDF</a></h3>
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
                            <?php echo $addlink;
                            echo '&nbsp;' . $addcatlink;
                            echo '&nbsp;' . $addsubcatlink; ?>
                          
                            <?php if($this->session->userdata('usertype_id') != 2) { ?> <button type="button" class="btn btn-green " id="btndel">Delete Selected Items</button> <?php } ?>
                            <div class="datagrid-header-right">

                            		<table style="border:0px !important;float:left;"><form method="post" action="<?php echo site_url('admin/itemcode');?>">
                            		<tr><td  style="border:0px !important;">Category:</td>
                            		<td  style="border:0px !important;"> <select id="searchcategory" name="searchcategory" style="width: 120px;">
                                        <option value=''>All Categories</option>
                                        <?php
                                        foreach ($categories as $cat) { ?>
                                            <option value="<?php echo $cat->id ?>"
                                            <?php
                                            if (@$_POST['searchcategory'] == $cat->id) {
                                                echo 'SELECTED';
                                            }
                                            ?>
                                                    >
                                            <?php echo $cat->catname ?>
                                            </option>
                                        <?php } ?>
                                    </select></td>
                                    <td  style="border:0px !important;"> <button type="search" class="btn"><i class="icon-search"></i></button>
                                     </td>
                                    <td  style="border:0px !important;"></td>
                                    </tr>
                            	</form>
                            		</table>



                            	<table style="border:0px !important;float:left;"><tr><td  style="border:0px !important;">Item:</td>
                            	<td  style="border:0px !important;"><?php if(1){?>
                                <div class="input-append search datagrid-search" style="margin-top:0px !important;">
                                    <input type="text" class="input-medium" placeholder="Search" value="<?php echo @$_POST['searchitemname'];?>" style="height:22px !important;">
                                    <button class="btn"><i class="icon-search"></i></button>
                                </div>
                                <?php }?></td>

                            	</tr></table>


                            </div>
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
                                    <?php if (0) { ?>
                                        <button type="button" class="btn" data-toggle="dropdown"><i class="caret"></i></button>
<?php } ?>
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
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($this->session->userdata('usertype_id') == 2){?>
<div id="editmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        <h4>Edit Item Specification</h4>
    </div>
    <div class="modal-body" id="editbody">
    </div>

</div>
<?php }?>