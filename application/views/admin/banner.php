

<section class="row-fluid">
<?php echo $this->session->flashdata("message"); ?>
  <h3 class="box-header"><i class="icon-picture"></i>Banner Settings</h3>
    <div class="box">
      <div class="container">
        <form action="<?php echo base_url();?>admin/banner/do_upload" method="post" name="banner" id="banner" enctype="multipart/form-data"  role="form">
         <div class="form-group">
		    <label class="form-label"><strong>Add Banner</strong></label>
				 <div class="controls">
				   <input type="file"  name="banner" id="banner"/>
				  </div>
		  </div>
          <input type="submit" value="upload" class="btn btn-success btn-sm"/>
        </form>
     </div>

<div class="container">
      <div class="table-responsive" id="table_div">
       <table class="table table-hover table-bordered" >
         <tr><th style="text-align:center;">ID</th><th style="text-align:center;">Banner</th><th style="text-align:center;">Action</th><th>Banner URL</th></tr>
           <?php foreach ($banner as $item){ ?>
         <tr>
            <td style="text-align:center;"><?php echo $item->id; ?></td>

            <td style="text-align:center;"><img src="<?php echo base_url();?>uploads/banners/<?php echo $item->banner;?>"
                      alt="<?php echo $item->banner;?>"height="200" width="200" class="img-responsive img-thumbnail"></td>

            <td style="text-align:center;"><a href="<?php echo site_url();?>admin/banner/del?id=<?php echo $item->id;?>" style="text-decoration: none;">
                <button type="button" class="btn btn-danger btn-sm" onclick="javascript:confirm('Do You Really Want to Delete This Banner?');">Delete &nbsp;
                       <span class="glyphicon glyphicon-trash"></span></button></a>
             </td>
             <td>
             <form action="<?php echo base_url()."admin/banner/seturl/".$item->id; ?>" method="post" name="banner" id="banner"  role="form">
             <input type="text" name="bannerurl" id="bannerurl" value="<?php isset($item->bannerurl)?$item->bannerurl:''?><?php echo $item->bannerurl;?>"
                       placeholder="Enter URL">
            <input type="submit" value="Set URL" class="btn btn-primary btn-sm"/>
             </form>
             </td>
         </tr>
          <?php } ?>
      </table>
    </div>
</div>
</div>

</section>

