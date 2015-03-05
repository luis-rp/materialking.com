<script>
	 function filtermanufacturer(id)
	 {
	    $("#formmanufacturer").val(id);
	    document.forms['manufacturersearchform'].submit();
	    //setTimeout("doPost()", 10);
	    return false;
	 }

	 /*function doPost()
	 {
	    document.forms['categorysearchform'].submit();
	 }*/
</script>
<!--<a tabindex="0" href="#news-items-2" class="fg-button fg-button-icon-right ui-widget ui-state-default ui-corner-all" id="hierarchybreadcrumb">
	<span class="ui-icon ui-icon-triangle-1-s"></span><?php if(isset($catname) && $catname!="") echo $catname; else echo "Select Category"; ?>
</a>-->
<!--<div id="news-items-2" class="hidden">-->
    <?php  echo @$manufacturermenu;?>
<!--</div>-->