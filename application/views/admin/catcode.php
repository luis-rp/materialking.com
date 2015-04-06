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
            <form class="form-horizontal" method="post" action="<?php echo $action; ?>" enctype="multipart/form-data">
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
                                <?php echo $parentoptions; ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Category Banner</label>
                        <div class="controls">
                            <input type="file" name="banner_image" id="banner_image"/>
                            <?php
                                if(isset($banner_image) && $banner_image != "" && file_exists("./uploads/category-banners/".$banner_image))
                                {
                             ?>
                                    <img src="<?php echo site_url('uploads/category-banners/thumbs/'.$banner_image);?>" alt=""/>
                                    <input type="hidden" readonly="readonly" name="previous_image" id="previous_image" value="<?php echo $banner_image; ?>" />
                                    &nbsp;&nbsp;
                                    <a href="<?php echo site_url('admin/catcode/removeimage/'.$this->validation->id);?>">Remove Image</a>
                            <?php
                                }   
                            ?>
                        </div>
                    </div>

                     <div class="control-group">
                        <label class="control-label">Title</label>
                        <div class="controls">
                            <input type="text" id="catTitle" name="catTitle" class="span10" value="<?php if(isset($this->validation->title)) echo $this->validation->title; ?>">
                        </div>
                    </div>
                    
                    
                    <div class="control-group">
                        <label class="control-label">Category URL</label>
                        <div class="controls">
                             <input type="text" id="categoryurl" name="categoryurl" class="span10" onkeyup="this.value=this.value.replace(/[^0-9a-zA-Z-]/g,'');" value="<?php if(isset($this->validation->categoryurl)) echo $this->validation->categoryurl; ?>">
                        </div>
                    </div> 
                    
                    
                    <div class="control-group">
                        <label class="control-label">Text</label>
                        <div class="controls">
                             <input type="text" id="catText" name="catText" class="span10" value="<?php if(isset($this->validation->text)) echo $this->validation->text; ?>">
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
