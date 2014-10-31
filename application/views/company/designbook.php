       
        <!-- <script src="<?php echo base_url(); ?>templates/admin/js/jquery.js" type="text/javascript"></script>              
        <script src="<?php echo base_url(); ?>templates/admin/js/bootstrap.min.js" type="text/javascript"></script>          

		<link rel="stylesheet" href="<?php echo base_url(); ?>templates/site/assets/css/ui.all.css" type="text/css" id="color-variant-default">-->
		<!-- <link href="<?php echo base_url(); ?>templates/admin/css/jquery-ui.css" media="all" rel="stylesheet" type="text/css" id="bootstrap-css"> -->		
		<!-- <script type="text/javascript" src="<?php echo base_url();?>templates/admin/js/jquery-ui.js"></script> -->
		<script src="<?php echo base_url();?>templates/admin/js/jquery.ui.autocomplete.html.js"></script>

<style>

#parent
{
	font-size:17px;
	color:white;
	text-align:center;
	font-weight:bold;
}
.supplier_new1 .price {
padding:5px 0px;
}

 .ui-tooltip {
	padding: 8px;
	font-size:19px !important;
	font-weight:bold !important;
	position: absolute;
	z-index: 9999;
	max-width: 300px;
	-webkit-box-shadow: 0 0 5px #aaa;
	box-shadow: 0 0 5px #aaa;
	color:#06A7EA !important;
}

 
 
 #logo
{
	width: 505px;
	margin: 0 auto;
	text-align: center;
}
#pgtitle
{
	margin: 0px 0px 20px;
	font-size: 18pt;
}
#container2
{
	display: block;
	width: 850px;
	height: 300px;
	margin: 0 auto;
}
#imgtag
{
	position: relative;
	min-width: 300px;
	min-height: 300px;
	float: none;
	border: 3px solid #FFF;
	cursor: crosshair;
	text-align: center;
}
.tagview
{
	border: 1px solid #F10303;
	width: 50px;
	height: 50px;
	position: absolute;
/*display:none;*/	
	color: #FFFFFF;
	text-align: center;
}
.square
{
	display: block;
	height: 79px;
	opacity: 1.0;
}
.person
{
	background: #282828;
	border-top: 1px solid #F10303;
}
#tagit
{
	position: absolute;
	top: 0;
	left: 0;
	width: 288px;
	border: 1px solid #D7C7C7;
}
#tagit .box
{
	border: 1px solid #F10303;
	width: 10px;
	height: 10px;
	float: left;
}
#tagit .name
{
	float: left;
	background-color: #C5C5C5;
	width: 280px;
	height: 180px;
	padding: 5px;
	font-size: 10pt;
}
#tagit DIV.text
{
	margin-bottom: 5px;
}
#tagit INPUT[type=text]
{
	margin-bottom: 5px;
}
#tagit #tagname
{
	width: 110px;
}
#taglist
{
	width: 30px;
	min-height: 200px;
	height: auto !important;
	height: 20px;
	float: left;
	padding: 10px;
	margin-left: 20px;
	color: #000;
}
#taglist OL
{
	padding: 0 20px;
	float: left;
	cursor: pointer;
}
#taglist OL A
{
}
#taglist OL A:hover
{
	text-decoration: underline;
}
.tagtitle
{
	font-size: 14px;
	text-align: center;
	width: 100%;
	float: left;
}
 
.tp_circle {
    background: none repeat scroll 0 0 #ACC70A;
    border: 2px solid rgba(255, 255, 255, 0.75);
    border-radius: 50% 50% 50% 50%;
    box-shadow: 0 0 10px #000000;
    color: #FFFFFF;    
    height: 16px;
    line-height: 13px;
    padding-top: 4px;
    /*position: absolute;
    text-align: center;*/
    width: 20px;
    z-index: 2;
}

.ui-autocomplete {
  z-index:2147483647;
}

</style>

		
		

<script type="text/javascript" charset="utf-8">
var upload_number = 2;
	function addFileInput() {
	 	var d = document.createElement("div");
	 	var file = document.createElement("input");
	 	file.setAttribute("type", "file");
	 	file.setAttribute("name", "UploadFile[]");
	 	d.appendChild(file);
	 	document.getElementById("moreUploads").appendChild(d);
	 	upload_number++;
	}
</script>

<script type="text/javascript">


$(document).ready(function() {
		
	var counter = 0;
    var mouseX = 0;
    var mouseY = 0;
	var mouseXactual = 0;
	
	$("#imgtag img").click(function(e) { // make sure the image is clicked
  var imgtag = $(this).parent(); // get the div to append the tagging list
  mouseX = ( e.pageX - $(imgtag).offset().left ); // x and y axis
  mouseY = ( e.pageY - $(imgtag).offset().top );
  mouseXactual = ( e.pageX - $(img).offset().left );
  $( '#tagit' ).remove( ); // remove any tagit div first
  //insert an input box with save and cancel operations.
  $( imgtag ).append( '<div id="tagit"><!-- <div class="box"></div>--><div class="name"><table><tr><td colspan="2">Tag Name</td></tr><tr><td  colspan="2"><input type="text" name="txtname" id="tagname" style="width:200px;" /></td></tr><tr><td  colspan="2">Tag Description</td></tr><tr><td  colspan="2"><textarea name="tagdesc" id="tagdesc"></textarea></td></tr><tr><td><input type="button" name="btnsave" value="Save" id="btnsave" /></td><td><input type="button" name="btncancel" value="Cancel" id="btncancel" /></td></tr></table></div></div>' );
  $( '#tagit' ).css({ top:mouseY, left:mouseX });
   
  $('#tagname').focus();    
});


$( document ).on( 'click',  '#tagit #btnsave', function(){
    name = $('#tagname').val();
    description = $('#tagdesc').val();
var img = $('#imgtag').find( 'img' );
var id = $( img ).attr( 'id' );
  $.ajax({
    type: "POST",
    url: "<?php echo site_url('site/savetag');?>",
    data: "pic_id=" + id + "&name=" + name + "&description=" + description + "&pic_x=" + mouseX + "&pic_y=" + mouseY + "&type=insert" + "&pic_xactual=" + mouseXactual,
    cache: true,
    success: function(data){
    //$('#taghidid').val(data);
     var img = $('#imgtag').find( 'img' );
     var id = $( img ).attr( 'id' );
     //get tags if present
     viewtag( id );
      $('#tagit').fadeOut();
    }
  });
    
});


// Cancel the tag box.
    $( document ).on( 'click', '#tagit #btncancel', function() {
      $('#tagit').fadeOut();
    });
    
    
    // mouseover the taglist 
	$('#taglist').on( 'mouseover', 'li', function( ) {
      id = $(this).attr("id");
      $('#view_' + id+'_').css({ opacity: 1.0 });
    }).on( 'mouseout', 'li', function( ) {
        $('#view_' + id+'_').css({ opacity: 0.0 });
    });
	
	// mouseover the tagboxes that is already there but opacity is 0.
	$( '#tagbox' ).on( 'mouseover', '.tagview', function( ) {
		var pos = $( this ).position();
		 id = $(this).attr("id");	
		 // alert(id);	 
		//$(this).css({ opacity: 1.0 }); // div appears when opacity is set to 1.
		$('#' + id+'_').css({ opacity: 1.0 });
	}).on( 'mouseout', '.tagview', function( ) {
		//$(this).css({ opacity: 0.0 }); // hide the div by setting opacity to 0.
		$('#' + id+'_').css({ opacity: 0.0 });
	});

	
	// load the tags for the image when page loads.
    var img = $('#imgtag').find( 'img' );
	var id = $( img ).attr( 'id' );
	   
    // Remove tags.
    $( '#taglist' ).on('click', '.remove', function() {
      id = $(this).parent().attr("id");
      console.log($(this));
      // Remove the tag
	  $.ajax({
        type: "POST", 
        url: "<?php echo site_url('site/removetag');?>", 
        data: "id=" + id,
        success: function(data) {
			var img = $('#imgtag').find( 'img' );
			var id = $( img ).attr( 'id' );
			//get tags if present
			viewtag( id );
        }
      });
    });
     
    
    $.widget( "app.autocomplete", $.ui.autocomplete, {
        
        // Which class get's applied to matched text in the menu items.
        options: {
            highlightClass: "ui-state-highlight"
        },
        
        _renderItem: function( ul, item ) {

            // Replace the matched text with a custom span. This
            // span uses the class found in the "highlightClass" option.
            var re = new RegExp( "(" + this.term + ")", "gi" ),
                cls = this.options.highlightClass,
                template = "<span class='" + cls + "'>$1</span>",
                label = item.label.replace( re, template ),
                $li = $( "<li/>" ).appendTo( ul );
            
            // Create and return the custom menu item content.
            $( "<a/>" ).attr( "href", "#" )
                       .html( label )
                       .appendTo( $li );
            
            return $li;
            
        }
        
    });	
	
});


function showimagewithtag(imgid,imagsrc){

	$("#imgmodaltag").modal();
	var img = $('#imgtag').find( 'img' );
	$( img ).attr( 'id' ,imgid);
	$( img ).attr( 'src' , imagsrc);
	//alert(imgid);
	viewtag(imgid);
}


function viewtag(pic_id)
{
	$('#pictureid').val(pic_id);
	// get the tag list with action remove and tag boxes and place it on the image.
	$.post( "<?php echo site_url('site/taglist');?>" ,  "pic_id=" + pic_id, function( data ) {
		$('#taglist').html(data.lists);
		$('#tagbox').html(data.boxes);
	}, "json");
	
	$. ajax ({
		type: "POST",
		dataType: 'json',
		data: {"pic_id" : pic_id},
		url: "<?php echo site_url('site/populatetags')?>",
		success: function (data) {
			if(data){

				$('#tagcombo').empty();
				$('#tagcombo').append( new Option("Choose","") );
				$.each(data, function( index, value ) {

					$('#tagcombo').append( new Option(value.name,value.id) );

				});

			}
		},
		error: function(x,y,z){
			alert('An error has occurred:\n' + x + '\n' + y + '\n' + z);
		}
	});

}


$(function() {

 	//autocomplete
    $(".itemcode").autocomplete({
        source: "<?php echo base_url(); ?>admin/quote/finditemcode?term=c",
        minLength: 1,
        html: true
    });
});


    function fetchItem(codeid)
    {
    	var itemcode = document.getElementById(codeid).value;
    	
    	if($('#taghidid').val()=="")
    		alert("Please select any tag first");
    		
    	if(itemcode =="")
    		alert("Please select any itemcode first");
    			
    	if(itemcode!="" && $('#taghidid').val()!=""){
    		var url = '<?php echo base_url()?>/company/getitembycode';
    		$('#itemcodedetails').html('');
    		$.ajax({
    			type:"post",
    			data: "code="+encodeURIComponent(itemcode),
    			url: url
    		}).done(function(data){
    			var obj = $.parseJSON(data);
    			console.log(obj);
    			if(obj.itemname !== undefined)
    			{
    				$('#itemcodedetails').css('display:block');
    				$('#itemcodedetails').html('<table style="width:100px;"><tr><td>Itemcode:'+obj.itemcode+'</td></tr><tr><td>Itemname:'+obj.itemname+'</td></tr><tr><td>Price:'+obj.ea+'</td></tr><tr><td><a target="blank" href="<?php echo base_url()?>site/item/'+obj.url+'">ViewItem</a></td></tr><tr><td><input type="button" value="Save" onclick="saveitemtotag('+obj.id+');" /></td></tr></table>');    				
    			}
    		});
    	}
    }   

    function saveitemtotag(itemid){
    	
    	$.ajax({
    		type: "POST",
    		url: "<?php echo site_url('site/saveitemtotag');?>",
    		data: "itemid=" + itemid + "&id=" + $('#taghidid').val(),
    		cache: true,
    		success: function(data){
    			
    			$('#tagit').fadeOut();
    			$('#itemcodedetails').fadeOut();
    			viewtag($('#pictureid').val());
    		}
    	});
    	
    }
    
    function sethiddentag(tagobj){
    	
    	 var tagid = $(tagobj).val();
    	 $('#taghidid').val(tagid);
    }
    
</script>

<div class="content">
     <?php echo $this->session->flashdata('message'); ?>
		<div class="page-title">
			<h3>Design Book</h3>
		</div>

	   <div id="container">
	   	<div class="row1">
	   	  <div class="grid simple ">
       		<div class="grid-body no-border">
              <div class="row">
              <form id="designform" name="designform" class="animated fadeIn" method="post" action="<?php echo site_url('company/designbook1');?>" enctype="multipart/form-data">
                 <div class="col-md-10 col-sm-10 col-xs-10">
				    <div class="form-group">
					   <label class="form-label">Add Images</label>
						  <input type="file" name="UploadFile[]" id="UploadFile" onchange="document.getElementById('moreUploadsLink').style.display = 'block';" />
							<div id="moreUploads"></div>
								<div id="moreUploadsLink" style="display:none;"><a href="javascript:addFileInput();">Add another Image</a></div>
					 </div>

              <?php if(isset($design) && count($design)>0) {?>
			    <table class="table no-more-tables general">
				  <thead>
                    <tr>
                     <th style="width:30%">Image</th>
                     <th style="width:30%">Name</th>
                     <th style="width:20%">Publish</th>
                     <th style="width:20%">Delete</th>
                     </tr>
                   </thead>
                   <tbody>
		           <?php  foreach($design as $items)  { ?>
				   <tr>
				     <td>
			           <?php $arr1=explode('.',$items->imagename); $ext=end($arr1);
				             if($ext=='gif' || $ext=='tif' || $ext=='jpg' || $ext=='jpeg' || $ext=='png' || $ext=='GIF' || $ext=='TIF' || $ext=='JPG' || $ext=='PNG') { ?>      <a onclick="showimagewithtag('<?php echo $items->id;?>','<?php echo site_url('uploads/designbook/'.$items->imagename);?>');">                    <img  src="<?php echo site_url('uploads/designbook/'.$items->imagename);?>" height="100px" width="100px" class="img-thumbnail" alt="<?php echo $items->name;?>"></a>
                            <?php } else { echo $items->name; } ?>
                     </td>
                     <td><input type="text" class="form-control" name="designname[<?php echo $items->id;?>]" id="designname[<?php echo $items->id;?>]" value="<?php echo $items->name;?>" required>
                      <input type="hidden" name="nameid[]" value="<?php echo $items->id;?>"/>
                     </td>
					 <td><input type="checkbox" id="file[<?php echo $items->id;?>]" name="file[<?php echo $items->id;?>]" <?php if(isset($items->publish) && $items->publish==1) {echo "checked='checked'";}?>/>
						  <input type="hidden" name="publishid[]" value="<?php echo $items->id;?>"/>
					 </td>
					 <td><a class="close"  href="<?php echo base_url("company/deletedesignfile/".$items->id);?>" onclick="return confirm('Are you really want to delete this Image?');">&times;</a></td>
					</tr>
				    <?php } ?>				    
				   <tbody>
			    </table>
               <?php } ?>
			    <div class="form-group">
				   <label class="form-label"></label>
				      <div class="controls">
				         <input type="submit" value="Save" class="btn btn-primary btn-cons general">
				      </div>
				</div>
				</div>
               </form>
              </div>
		    </div>
	      </div>
        </div>
     </div>
  </div>



  <div id="imgmodaltag" aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" class="modal fade" style="display: none;">
    <div class="modal-dialog" style="width:92%;">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <i class="icon-credit-card icon-7x"></i>                 
        </div>
        <div class="modal-body" style="width:30%;padding:12px;">
        <div id="container2">
        <div id="imgtag">  
  <img id="pic1"  />
  <input type="hidden" name="taghidid" id="taghidid"/>
  <input type="hidden" id="pictureid" name="pictureid"/> 
  <div id="tagbox">
  </div>
  <br/><br/>
  Select Tag:<SELECT  id="tagcombo" name="tagcombo" style='WIDTH:100px' onchange="sethiddentag(this);"><option value="">Choose </option></SELECT> &nbsp; &nbsp;  
  Select Item Code:<input type="text" id="itemcode" name="itemcode" class="itemcode" onblur="fetchItem('itemcode');" />
  <div id="itemcodedetails">
  </div>
</div>
<div id="taglist">
  </div>

</div>

        </div>        
        <div class="modal-footer">          
          <!-- <button data-dismiss="modal" class="btn btn-default" type="button">Close</button> -->
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>