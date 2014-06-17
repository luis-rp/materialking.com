<script type="text/javascript">
<!--
    function changepage(pagenum)
    {
        if (pagenum >= 0 && pagenum < <?php echo $totalpages; ?>)
        {
            $("#pagenum").val(pagenum);
            $("#pagingform").submit();
        }
    }
//-->
</script>
<?php
$minpagerange = $currentpage - 4;
if ($minpagerange <= 0)
    $minpagerange = 1;
$maxpagerange = $minpagerange + 9;
?>
<form id="pagingform" action="<?php echo site_url($submiturl); ?>" method="<?php echo $submitmethod; ?>">
    <?php while (list($k, $v) = each($pagingfields)) { ?>
        <input type="hidden" id="<?php echo $k; ?>" name="<?php echo $k; ?>" value="<?php echo $v; ?>"/>
    <?php } ?>
</form>

<ul class="pagination">
    <li <?php if ($currentpage == 1 || $totalcount == 0) { ?> class="disabled" style="display:none;"<?php } ?>
                                                              onclick="changepage('0')">
        <a href="javascript: void(0)" class="style20">First</a>
    </li>
    <li <?php if ($currentpage == 1 || $totalcount == 0) { ?> class="disabled" style="display:none;"<?php } ?>
                                                              onclick="changepage('<?php echo $currentpage - 2; ?>')">
        <a href="javascript: void(0)" class="style20">Prev</a>
    </li>
    <?php for ($i = 1; $i <= $totalpages; $i++) { ?>
        <?php if ($i >= $minpagerange && $i <= $maxpagerange) { ?>
            <li class="<?php
            if ($currentpage == $i) {
                echo 'active';
            }
            ?>" 
                onclick="changepage('<?php echo $i - 1; ?>')">
                <a href="javascript: void(0)" class="style26 style18 " style="color: #333333;">
            <?php echo $i; ?>
                </a>
            </li>
    <?php } ?>
<?php } ?>

    <li <?php if ($currentpage == $totalpages || $totalcount == 0) { ?> class="disabled" style="display:none;<?php } ?>
                                                                        onclick="changepage('<?php echo $currentpage; ?>')">
                                                                        <a href="javascript: void(0)" class="navbar style18">Next</a>
    </li>

    <li <?php if ($currentpage == $totalpages || $totalcount == 0) { ?> class="disabled" style="display:none;<?php } ?>
                                                                        onclick="changepage('<?php echo $totalpages - 1; ?>')">
                                                                        <a href="javascript: void(0)" class="navbar style18">Last</a>
    </li>
</ul>
