

<section class="row-fluid">
<?php echo $this->session->flashdata("message"); ?>
  <h3 class="box-header"><i class="icon-th-list"></i>&nbsp;&nbsp;Manage Purchaser Company</h3>
    <div class="box">
     <div class="container">
       <table class="table table-hover table-bordered" >
         <tr><th style="text-align:center;">ID</th><th style="text-align:center;">Purchaser Company</th>
         <th style="text-align:center;">Email</th><th style="text-align:center;">Action</th></tr>
           <?php

           //echo "<pre>"; print_r($pc); die;
           if(isset($pc) && count($pc) > 0){

           foreach ($pc as $item){ ?>
         <tr>
            <td style="text-align:center;"><?php echo $item->purchasingadmin; ?></td>
            <td style="text-align:center;"><?php echo $item->companyname; ?></td>
            <td style="text-align:center;"><?php echo $item->email; ?></td>
            <td style="text-align:center;"><a href="<?php echo site_url('admin/manage_network/delete/'.$item->purchasingadmin);?>" style="text-decoration: none;">
                <button type="button" class="btn btn-danger btn-sm" onclick="javascript:return confirm('Do You Really Want to Delete This Company From Network?');">Delete &nbsp;
                       <span class="glyphicon glyphicon-trash"></span></button></a>
             </td>

         </tr>
          <?php } } else { echo "No records found for Purchaser Company";}?>
      </table>
    </div>
  </div>
</section>

