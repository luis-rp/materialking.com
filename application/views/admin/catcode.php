<?php if (isset($jsfile)) include $this->config->config['base_dir'] . 'templates/admin/gridfeed/' . $jsfile; ?>
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

            <?php echo $message; ?>
            <?php echo $this->session->flashdata('message'); ?>
            <form class="form-horizontal" method="post" action="<?php echo $action; ?>"> 

                <br/>
                <a class="btn btn-green" href="<?php echo site_url('admin/catcode'); ?>">&lt;&lt; Back</a>
                <br/>
                <div  style="width:48%; float:left;">
                    <input type="hidden" name="id" value="<?php echo $this->validation->id; ?>"/>
                    <br/>

                    <div class="control-group">
                        <label class="control-label">Category</label>
                        <div class="controls">
                            <input type="text" id="catname" name="catname" class="span10" value="<?php echo $this->validation->catname; ?>">
                            <?php echo $this->validation->catname_error; ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Parent Category</label>
                        <div class="controls">
                            <select id="parent_id" name="parent_id">
                            	<option value="0">Main Category</option>
                            	<?php echo $parentoptions;?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">&nbsp;</label>
                        <div class="controls">
                            <input name="add" type="submit" class="btn btn-primary" value="<?php echo $heading; ?>"/>
                        </div>
                    </div>

                </div>
            </form>
        </div>

    </div>
</section>
