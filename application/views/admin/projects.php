        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap-tour.min.js" type="text/javascript"></script>
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
              
              <?php if ($counts){?>
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
              <?php } ?>
            </table>
           </div>
         </div>
      </div>
    </div>
</section>