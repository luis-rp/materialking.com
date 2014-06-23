
<link rel="stylesheet" type="text/css" href="<?php echo base_url();?>templates/front/assets/plugins/data-tables/DT_bootstrap.css">
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/datatable.js"></script>
<script type="text/javascript" language="javascript" src="<?php echo base_url();?>templates/front/assets/plugins/data-tables/jquery.dataTables.js"></script>

<script type="text/javascript">
<!--
$(document).ready(function(){
	$('#content').wysihtml5();

    
	$('#datatable').dataTable( {
		"bPaginate":   false,
		"bInfo":   false,
		"aoColumns": [
		        		{ "bSortable": false },
		        		null,
		        		null,
			]
		} );
});
//-->
</script>

<section class="row-fluid">
	<h3 class="box-header">Item Article</h3>
	<div class="box">
    	<div class="span7">
    	
        	<?php echo @$message; ?>
        	<?php echo $this->session->flashdata('message'); ?>
           	<form class="form-horizontal" method="post" action="<?php echo site_url('admin/itemcode/savearticle/'.$itemid); ?>"> 
               <input type="hidden" name="id" value="<?php echo @$article->id;?>"/>
               <input type="hidden" name="itemid" value="<?php echo $itemid;?>"/>
                <br/>
                
                <div class="control-group">
                <label class="control-label">Title</label>
                <div class="controls">
                  <input type="text" id="title" name="title" class="span8" value="<?php echo @$article->title; ?>" required>
                </div>
                </div>
                
                <div class="control-group">
                <label class="control-label">URL</label>
                <div class="controls">
                  <input type="text" id="url" name="url" class="span8" value="<?php echo @$article->url; ?>" required
                  onkeyup="this.value=this.value.replace(/[^0-9a-zA-Z-]/g,'');">
                  <br/>(only number, alphabet and dash allowed)
                </div>
                </div>
                
                <div class="control-group">
                <label class="control-label">Link Title</label>
                <div class="controls">
                  <input type="text" id="linkhead" name="linkhead" class="span8" value="<?php echo @$article->linkhead; ?>">
                </div>
                </div>
                
                <div class="control-group">
                <label class="control-label">Items Title</label>
                <div class="controls">
                  <input type="text" id="itemhead" name="itemhead" class="span8" value="<?php echo @$article->itemhead; ?>">
                </div>
                </div>
            
                
                <div class="control-group">
                <label class="control-label">Content<br><br><a href="<?php echo site_url('admin/itemcode');?>" class="btn btn-green">Back To Items</a></label>
                <div class="controls">
                   <textarea id="content" class="span12" rows="15"  name="content" required><?php echo @$article->content; ?></textarea>
                </div>
                </div>
                
                <div class="control-group">
                <label class="control-label">&nbsp;</label>
                <div class="controls">
                 <input type="submit" class="btn btn-primary" value="Save"/>
                </div>
                </div>
            
          	</form>
        
        </div>
        <?php if(@$article->id){?>
        <div class="span4">
            <h3>Links:</h3>
            <form method="post" action="<?php echo site_url('admin/itemcode/savearticlelink/'.@$article->id);?>" enctype="multipart/form-data">
            	<table>
            		<tr>
            			<td>Title</td>
            			<td><input type="text" name="title" class="span12" required/></td>
            		</tr>
            		<tr>
            			<td>Link</td>
            			<td><input type="text" name="link" class="span12" required/></td>
            		</tr>
            		<tr>
            			<td>Image</td>
            			<td><input type="file" name="filename"/></td>
            		</tr>
            		<tr>
            			<td></td>
            			<td><input type="submit" value="Add" class="btn btn-primary"/></td>
            		</tr>
            	</table>
            	
            </form>
			<?php if(@$links){?>
				<br/><br/>
                <table class="table table-bordered span12">
                	
                    <tr>
                        <th>Link</th>
                        <th>Image</th>
                        <th>Delete</th>
                    </tr>
					<?php foreach($links as $link){?>
                    <tr>
                    	<td><a href="<?php echo $link->link?>" target="_blank"><?php echo $link->title;?></a></td>
                        <td width="95%"><img width="50" src="<?php echo site_url('uploads/item/thumbs/'.$link->filename);?>"/></td>
                        <td>
                            <a href="<?php echo site_url('admin/itemcode/deletearticlelink/'.$link->id.'/'.$article->id);?>" onclick="return confirm('Are you sure to delete this link?');"><i class="icon icon-trash"></i></a>
                        </td>
                    </tr>
					<?php }?>
				</table>
			<?php }?>
			
			<h3>Other Items:</h3>
			<form method="post" action="<?php echo site_url('admin/itemcode/savearticleitem/'.@$article->id);?>">
            	
    			<div style="height: 300px; max-height: 300px; overflow: scroll;">
    				<table id="datatable" class="table table-bordered span12">
    					<thead>
                        <tr>
                        	<th>Sel</th>
                            <th>Item code</th>
                            <th>Item name</th>
                        </tr>
                        </thead>
                        <?php foreach($items as $item)if($itemid != $item->id){?>
                        <tr>
                        	<td>
                        		<input type="checkbox" name="item[]" value="<?php echo $item->id;?>" 
                        		<?php if(in_array($item->id, $articleitems)){echo 'CHECKED';}?>/>
                        	</td>
                            <td><?php echo $item->itemcode;?></td>
                            <td><?php echo $item->itemname;?></td>
                        </tr>
                        <?php }?>
    				</table>
    			</div>
    			<input type="submit" value="Save" class="btn btn-primary"/>
			</form>
        </div>
        <?php }?>
        
    </div>
</section>
