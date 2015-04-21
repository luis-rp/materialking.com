
<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
<?php echo '<script>var getmanufacturersurl="' . site_url('site/getmanufacturers') . '";</script>' ?>
<script>
	function getlatlong()
	{
		var address = $("#inputLocation").val();
		if(address)
		{
    		var geocoder = new google.maps.Geocoder();
    		//alert(geocoder);
            geocoder.geocode({ 'address': address }, function (results, status) 
            {
                if (status == google.maps.GeocoderStatus.OK)
                {
                    var latitude = results[0].geometry.location.lat();
                    var longitude = results[0].geometry.location.lng();
                    $("#latitude").val(latitude);
                    $("#longitude").val(longitude);                   
                }
                 
            });
		}
		$("#searchform").submit();
        return true;
	}
</script>
<script>
    $(document).ready(function() {
        InitChosen();

        $('#search_form').submit(function() {
            $('#search_type').val('contractors');
            $('#search_form').attr('action', "<?php echo base_url('site/contractors'); ?>");
        });

    });


    function InitChosen() {
        $('select').chosen({
            disable_search_threshold: 10
        });
    }

    function fetchOrder()
    {
        get_by = $("#inputSortBy").val();
        orderdir = $("#inputOrder").val();
        if(get_by=="all"){
            get_by = "";
        }
        $("#get_by").val(get_by);
        $("#filterorderdir").val(orderdir);
        $("#searchform").submit();
        return true;
    }
    
    function changemanufacturer(industryid){
	if(industryid=="")
		industryid=0;
	$. ajax ({
					type: "POST",					
					data: {"industryid" : industryid},
					async: false,
					dataType: 'json',
					url: getmanufacturersurl,
					success: function (data) {
						if(data){
							
							$('#manuselect').html('');
							
							$("#manuselect").append("<select name='typem' id='typem'>" +"</select>");
        												
							$('#typem').append( new Option("All","") ); 	
							
							$.each(data, function( index, value ) {
								
								$('#typem').append( new Option(value.title,value.id) );

							});									
							
						}
					}
					/*error: function(x,y,z){
						alert('An error has occurred:\n' + x + '\n' + y + '\n' + z);
					}*/
				});
	
	}
    
</script>

<div id="content">
    <div class="container">
        <div id="main">
            <div class="row">
                <div class="span9">
                   <h3 class="titlebox" style="padding:0px 0px 0px 8px"><strong>Contractors List</strong></h3>
                    <div class="properties-rows">
                        <div class="row">

                            <?php if ($norecords) { ?>
                                <div class="alert alert-error" style="margin-left:30px;">
                                    <button data-dismiss="alert" class="close" type="button">X</button>
                                    <strong> <?php echo $norecords; ?></strong> <a href="<?php echo site_url('site/contractors'); ?>">View All Listings</a>
                                </div>
                            <?php } ?>

                            <?php
                            $i = 3;
                            foreach ($contractors as $contractor) {
                                $i++
                                ?>
                                <div class="property span9" style="width:auto;padding:0 0 10px 0px;">
                                    <div class="row">
                                        <div class="image1 span3">
                                            <div class="content">
                                                <?php if ($contractor->logo) { ?>
                                                    <img style="padding: 20px 0px 20px 10px; vertical-align: middle;" src="<?php echo site_url('uploads/logo/thumbs/' . $contractor->logo) ?>" alt="">
                                                <?php } else { ?>
                                                    <img src="<?php echo base_url(); ?>templates/site/assets/img/default/big.png" alt="">
                                                <?php } ?>

                                            </div>
                                        </div>

                                        <div class="body1 span6">
                                            <div class="title-price row">
                                                <div class="title1 span4" style="margin-left:25px;">
                                                    <h2><a href="<?php echo site_url('site/contractor/' . $contractor->username); ?>">
                                                    <?php echo $contractor->companyname; ?></a></h2>
                                                </div>
                                                <div class="price1" style="float:right; margin:-11px 0 0 7px; padding:0px 25px 0 0px;" >
                                                <?php if (isset($contractor->city) && isset($contractor->state)) { ?>
                                                
                                                     <?php echo $contractor->city.",&nbsp;".$contractor->state;
													?>
                                                
                                                <?php } ?>
                                               
                                                </div>
                                              </div>

                                            <div class="location"><?php echo $contractor->fullname; ?></div>
                                            <p><?php echo $contractor->shortdetail; ?></p>
                                            <div class="area">
	                                            <div class="btn btn-primary arrow-right">
	                                            	<a href="<?php echo site_url('site/contractor/' . $contractor->username); ?>">View Profile</a>
	                                            </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>

                    <div class="pagination pagination-centered">
                        <?php $this->view('site/paging'); ?>
                    </div>
                </div>

                <div class="sidebar span3">
                    <h2>Contractor Filter</h2>
                    <div class="property-filter widget">
                        <div class="content">
                            <form id="searchform" method="post" action="" onsubmit="return getlatlong()">

                            	<input type="hidden" id="latitude" name="lat"/>
                            	<input type="hidden" id="longitude" name="lng"/>
                                <input type="hidden" id="get_by" name="get_by" value="<?php echo isset($_POST['get_by'])? $_POST['get_by'] : "" ?>" />
                                <input type="hidden" id="filterorderdir" name="orderdir" value="<?php echo isset($_POST['orderdir']) ? $_POST['orderdir'] : "" ?>" />
                                <div class="location control-group">
                                    <label class="control-label" for="inputLocation">
                                        Location
                                    </label>
                                    <div class="controls">
                                        <input type="text" id="inputLocation" name="location" value="<?php echo ($this->input->post('location')) ? $this->input->post('location') : $my_location; ?>">
                                        <?php if (0) { ?>
                                            <select id="inputLocation" name="citystates">
                                                <?php foreach ($citystates as $cst) { ?>
                                                    <option value="<?php echo $cst->citystate; ?>" <?php
                                                    if ($cst->citystate == @$_POST['citystates']) {
                                                        echo 'selected="selected"';
                                                    }
                                                    ?>><?php echo $cst->citystate; ?></option>
                                                        <?php } ?>
                                            </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                
                                
                                <div class="type control-group">
                                        <label class="control-label" for="inputType">
                                            Category
                                        </label>
                                        <div id="manuselect" class="controls">
                                            <select id="typec" name="typec">
                                                <option value=''>All</option>
                                                <?php
                                                foreach ($contractcategory as $t)
                                                {  ?>
                                                <option value='<?php echo $t->id; ?>' <?php
                                                        if ($t->id == @$_POST['typec']) {
                                                            echo 'selected="selected"';
                                                        }
                                                        ?>><?php echo $t->catname; ?></option>
                                                            <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                
                                
                                    <?php if ($found_records) { ?>
                                    <div class="form-actions">
                                        <div class="notfound"><?php echo $found_records; ?></div>
                                    </div>
                                    <?php } ?>

                                <div class="form-actions">
                                    <input type="submit" value="Filter Now!" class="btn btn-primary btn-large">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
