<?php

function build_category_tree(&$categories, $parent_id, $cats, $sub = '') {

    foreach ($cats[$parent_id] as $cat) {
        $cat->id = $cat->id;
        $cat->catname = $sub . $cat->catname;
        $categories[] = $cat;
        if (isset($cats[$cat->id]) && sizeof($cats[$cat->id]) > 0) {
            $sub2 = str_replace('&rarr;&nbsp;', '&nbsp;', $sub);
            $sub2 .= '&nbsp;&nbsp;&nbsp;&rarr;&nbsp;';
            build_category_tree($categories, $cat->id, $cats, $sub2);
        }
    }
}

?>