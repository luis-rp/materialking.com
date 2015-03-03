<script type="text/javascript">
    <!--
    $(document).ready(function(){
        $('#itemcodedate').datepicker();
        $('#notes').autosize();
    });
    function showhistory(companyid, itemid, companyname,imgname)
    {
        var serviceurl = '<?php echo base_url() ?>admin/itemcode/gethistory/';
      
        $.ajax({
            type:"post",
            url: serviceurl,
            data: "companyid="+companyid+"&itemid="+encodeURIComponent(itemid)
        }).done(function(data){        
        	var arr = data.split('*#*#$');        	
            $("#pricehistory").html(arr[0]);
            $("#itemcode").html(arr[1]);
            $("#itemimage").html('<img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:right;margin-top:-3em;" src='+imgname+'>');
            $("#historycompanyname").html(companyname);
            $("#historymodal").modal();
        });
    }
    function searchprice(keyword)
    {
        var serviceurl = '<?php echo base_url() ?>admin/itemcode/amazon';
        //alert(serviceurl);
        $("#searchmodal").modal();
        $.ajax({
            type:"post",
            url: serviceurl,
            data: "keyword="+keyword
        }).done(function(data){
            $("#minpricesearch").html(data);
        });
    }

    function openamazon(keyword)
    {
        keyword = encodeURIComponent(keyword);
        var url = 'http://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords='+keyword+'&rh=i%3Aaps%2Ck%3A1%22+x+3%2F4%22+copper+reducer';
	
        window.open(url,'amazonlookup','width=1200,height=800,menubar=no,scrollbars=yes');
    }
    //-->
</script>


<section class="row-fluid">
  	<span>
    <h3 class="box-header" style="width:75%" ><?php echo $heading; ?> </h3>
    <img style="max-height: 120px; padding: 0px;width:80px; height:80px;float:left;margin-top:10px;" src='<?php echo $imgName; ?>'>
    </span>
    <div class="box">
        <form class="form-horizontal" method="post" action="<?php echo $action; ?>"> 
            <div class="span12">

                <?php echo $message; ?>
                <?php echo $this->session->flashdata('message'); ?>



                <strong>
                    <?php if ($this->validation->id) { ?>
                        <table class="table table-bordered span12">
                            <tr>
                                <?php if ($this->validation->lastquoted) {
                                    echo '<td>Last quoted: ' . $this->validation->lastquoted . '</td>';
                                } ?>
                                <?php if (@$itempricetrend) {
                                    echo '<td>Price Trend: ' . $itempricetrend . '</td>';
                                } ?>
    <?php
    if ($poitems) {
        ?>
                                    <td>
                                        <a class="btn btn-green" href="<?php echo site_url('admin/itemcode/poitems/' . $this->validation->id); ?>">View PO Items</a>
                                    </td>
        <?php
    }
    ?>
                            </tr>
                        </table>

                        <?php
                        //$seconds = time() - strtotime($this->validation->lastquoted);
                        //$days = $seconds / (3600 * 24);
                        //if ($days > 30)
                            //echo "<b><font color='red'>Item has not been requited within 30 days.</font></b>";
                        ?>
                    </strong>
                    <?php
                    if (@$minprices) {
                        ?>
                        <h3 class="box-header"><i class="icon-ok"></i>Company Prices for <?php echo @$item?@$item->itemcode:$this->validation->itemcode; ?></h3>
        <?php if ($this->validation->keyword) { ?>
                            <a class="btn btn-primary" onclick="searchprice('<?php echo $this->validation->keyword; ?>')">Amazon Lookup</a>
                            <a class="btn btn-primary" onclick="openamazon('<?php echo $this->validation->keyword; ?>')">Search</a>
                            <br/><br/>
        <?php } ?>
                        <table class="table table-bordered">
                            <tr>
                                <th>Company Name</th>
                                <th>Date</th>
                                <th>Purchase Price</th>
                                <th>Substitute</th>
                                <th>History</th>
                            </tr>
                            <?php
                            //print_r($minprices);
                            foreach ($minprices as $m) {
                                ?>
                                <tr>
                                    <td><?php echo $m->companyname; ?></td>
                                    <td><?php echo $m->quoteon; ?></td>
                                    <td>
                                        <div class="input-prepend input-append span6">
                                            <span class="add-on">$</span>
                                            <input type="text" class="span12" id="price<?php echo $m->company; ?>" name="price<?php echo $m->company; ?>" value="<?php echo $m->price; ?>" required/>
                                        </div>
                                    </td>
                                    <td><?php echo $m->substitute ? 'Substitute [' . $m->itemname . ']' : '-' ?></td>
                                    <td>
                                        <a href="javascript: void(0);" onclick="showhistory('<?php echo $m->company ?>','<?php echo $m->itemid ?>','<?php echo $m->companyname ?>','<?php echo $imgName; ?>')"><i class="icon icon-search"></i></a>
                                    </td>
                                </tr>
                        <?php } ?>
                        </table>
                    <?php } ?>

<?php } ?>



            </div>
        </form>
    </div>
</section>

<div id="historymodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <h4><span id='itemcode'></span></h4>
        <button aria-hidden="true" onclick="$('#historymodal').modal('hide')" class="close" type="button">x</button>
        <h4>Price History - <span id="historycompanyname"></span>
        <span id="itemimage"></span>
        </h4>
    </div>
    <div class="modal-body" id="pricehistory">
    </div>

</div>

<div id="searchmodal" class="modal hide "  tabindex="-1" role="dialog" aria-labelledby="	myModalLabel" aria-hidden="true">

    <div class="modal-header">
        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
        <h4>Amazon Price Lookup</h4>
    </div>
    <div class="modal-body" id="minpricesearch">Loading prices...</div>

</div>
