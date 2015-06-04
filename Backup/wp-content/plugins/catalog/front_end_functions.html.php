<style>
.spider_catalog_style {
  display: block;
  box-sizing:border-box !important;
  -moz-box-sizing: border-box !important;
  -webkit-box-sizing: border-box !important;
}
.spidercataloginput {
  box-sizing:border-box !important;
  -moz-box-sizing: border-box !important;
  -webkit-box-sizing: border-box !important;
}
.spider_catalog_style table, tr, td {
  margin: 0 !important;
  border: none !important;
  padding: 0 !important;  
}
.spider_catalog_style img {
  box-shadow: none;
}
</style>
<?php





function front_end_catalog_list($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{
    ob_start();
    $frontpage_id = get_option('page_for_posts');
    global $ident;
    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $urll = site_url();


    if ($params['enable_rating']):
        ?>
        <style type="text/css">
            .star-rating {
                background: url(<?php  echo plugins_url('',__FILE__).'/Front_images/star' . $params['list_rating_star'] . '.png';
?>) top left repeat-x !important;
            }

            .star-rating li a:hover {
                background: url(<?php  echo plugins_url('',__FILE__). '/Front_images/star' . $params['list_rating_star'] . '.png';
?>) left bottom !important;
            }

            .star-rating li.current-rating {
                background: url(<?php  echo plugins_url('',__FILE__).'/Front_images/star' . $params['list_rating_star'] . '.png';
?>) left center !important;
            }

            .star-rating1 {
                background: url(<?php  echo plugins_url('',__FILE__).'/Front_images/star' . $params['list_rating_star'] . '.png';
?>) top left repeat-x !important;
            }

            .star-rating1 li.current-rating {
                background: url(<?php  echo plugins_url('',__FILE__). '/Front_images/star' . $params['list_rating_star'] . '.png';
?>) left center !important;
            }
        </style>
    <?php
    endif;

    html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "list_");
    
$frontpage_id = get_option('page_for_posts');
if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
    echo '<script>
  document.getElementById("cat_form_page_nav1").style.display = "none";
  </script>';
}



$prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "list_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }


    if ($params7['show_prod'] == 1) {
        if (count($rows)) {
            echo '<table border="0" cellspacing="0" cellpadding="0" id="productCartFull" style="border-width:' . $params['list_border_width'] . 'px;border-color:' . $params['list_border_color'] . ';border-style:' . $params['list_border_style'] . ';' . (($params['list_text_color'] != '') ? ('color:' . $params['list_text_color'] . ';') : '') . (($params['list_background_color'] != '') ? ('background-color:' . $params['list_background_color'] . ';') : '') . '">'
                . '<tr>';

            $parameters_exist = 0;
            foreach ($rows as $row) {
                if ($row->param != "")
                    $parameters_exist++;
            }

            echo '<TD style="font-size:12pt;border-right:solid 1px '.$params['list_list_border_color'].' !important;color:'.$params['list_page_up_names_text_color'].';background-color:'.$params['list_page_up_names_background_color'].' !important;">'.__('Product', 'sp_catalog').'</TD>';

            echo '<TD style="font-size:12pt;border-right:solid 1px '.$params['list_list_border_color'].' !important;color:'.$params['list_page_up_names_text_color'].';background-color:'.$params['list_page_up_names_background_color'].' !important;">'.__('Name', 'sp_catalog');


            if ($params['enable_rating'])
                echo ' / ' . __('Rating', 'sp_catalog');

            echo '</TD>';

            if($parameters_exist and $params['list_show_parameters'])
                echo '<TD style="font-size:12pt;border-right:solid 1px '.$params['list_list_border_color'].' !important;color:'.$params['list_page_up_names_text_color'].';padding:13px;background-color:'.$params['list_page_up_names_background_color'].' !important;">'.__('Parameters', 'sp_catalog').'</TD>';

            if($params['list_show_description'])
                echo '<TD style="font-size:12pt;border-right:solid 1px '.$params['list_list_border_color'].' !important;color:'.$params['list_page_up_names_text_color'].';background-color:'.$params['list_page_up_names_background_color'].' !important;">'.__('Description', 'sp_catalog').'</TD>';

            if($params['price'] or $params['market_price'])
                echo '<TD style="font-size:12pt;color:'.$params['list_page_up_names_text_color'].';background-color:'.$params['list_page_up_names_background_color'].' !important;">'.__('Price', 'sp_catalog').'</TD>';

            echo '</tr>';
        }

        foreach ($rows as $key=>$row) {
            $imgurl = explode(";;;", $row->image_url);
            $image_and_atach = explode('******', $imgurl[0]);
            $image = $image_and_atach[0];
            if (isset($image_and_atach[1]))
                $atach = $image_and_atach[1];
            else
                $atach = NULL;
            if ($atach) {
                $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                $attach_url = $array_with_sizes[0];
            } else {
                $attach_url = $image;
            }

            if($key%2==0){

                echo'<tr style="background-color:'.$params['list_page_background_color_1'].';' . (($params['text_size_list'] != '') ? ('font-size:' . $params['text_size_list'] . 'px;') : '').'">';
            }
            if($key%2!=0){

                echo'<tr style="background-color:'.$params['list_page_background_color_2'].';' . (($params['text_size_list'] != '') ? ('font-size:' . $params['text_size_list'] . 'px;') : '').'">';
            }
            if($key==0){

                echo'<tr style="background-color:'.$params['list_page_background_color_1'].';' . (($params['text_size_list'] != '') ? ('font-size:' . $params['text_size_list'] . 'px;') : '').'">';
            }

            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';
            if ($row->image_url == "" or $row->image_url == "******0")
                $image = plugins_url("Front_images/noimage.jpg", __FILE__);

              echo '<td style="vertical-align: middle;padding:0;width:' . ($params['list_list_picture_width']). 'px;height:' . ($params['list_list_picture_height']). 'px;border-right: solid 1px '.$params['list_list_border_color'].' !important;border-width:'.$params['list_border_width'].'px;border-color:'.$params['list_border_color'].';border-style:'.$params['list_border_style'].';border-top:none; border-left:none; "><a href="' . $image . '" target="_blank"><img style=" margin:10px; max-width:' . $params['list_list_picture_width'] . 'px; max-height:' . $params['list_list_picture_height'] . 'px;" src="' . $image . '" /></a></td>';

            echo '<td style="vertical-align: middle;text-align: left !important;border-right: solid 1px '.$params['list_list_border_color'].' !important;border-width:'.$params['list_border_width'].'px;border-color:'.$params['list_border_color'].';border-style:'.$params['list_border_style'].';border-top:none; border-left:none;">';

            $categories_id=explode(',',$row->category_id);
            if ($params['list_show_category'])
            {
                echo '<br><div style="margin-left: 0px;margin-right: 10px;margin-bottom: 10px;margin-top: 10px;color:' . $params['list_page_text_color_1'] . ';"><b style="float:left;">' . __('Category:', 'sp_catalog') . '</b>&nbsp<div style="color:' . $params['list_page_text_color_2'] . ';display:inline-block;" id="cat_' . $row->id . '">';

                foreach($categories as $key=>$categ)
                {
                    if(in_array($key,$categories_id))
                        echo $categ.'<br>';

                }


                echo	'</div></div>';
            }
            echo '<a href="' . $link . '" style="color:' . $params['list_page_text_color_2'] . ';">' . esc_html(stripslashes($row->name)) . '</a>';


            if ($params['enable_rating']) {
                $id = $row->id;

                $rating = $ratings[$id] * 25;

                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:50px; padding:10px;'>
			<ul class='star-rating'>	
				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>
				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"	title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  title='" . $title . "' class='two-stars'>2</a></li>	
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"	title='" . $title . "' class='five-stars'>5</a></li>
			</ul>
			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating:', 'sp_catalog') . '&nbsp;' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');

                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:50px; padding:10px;'>
			<ul class='star-rating1'>	
			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>
			<li><a  title='" . $title . "' class='one-star'>1</a></li>
			<li><a  title='" . $title . "' class='two-stars'>2</a></li>
			<li><a title='" . $title . "' class='three-stars'>3</a></li>
			<li><a title='" . $title . "' class='four-stars'>4</a></li>
			<li><a title='" . $title . "' class='five-stars'>5</a></li>
			</ul>
			</div>";

                }

            }
            echo '</td>';

            if ($parameters_exist and $params['list_show_parameters']) {
                echo '<td style="vertical-align: middle;border-right: solid 1px '.$params['list_list_border_color'].' !important;padding:0px;border-width:'.$params['list_border_width'].'px;border-color:'.$params['list_border_color'].';border-style:'.$params['list_border_style'].';border-top:none; border-left:none;"><table border="0" cellspacing="0" cellpadding="0" width="100%"' . (($params['text_size_list'] != '') ? ('style="font-size:' . $params['text_size_list'] . 'px"') : '').'>';

                $par_data = explode("par_", $row->param);

                for ($j = 0; $j < count($par_data); $j++)
                    if ($par_data[$j] != '') {
                        $par1_data = explode("@@:@@", $par_data[$j]);
                        $par_values = explode("	", $par1_data[1]);

                        $countOfPar = 0;

                        for ($k = 0; $k < count($par_values); $k++)
                            if ($par_values[$k] != "")
                                $countOfPar++;

                        if ($countOfPar != 0)
                        {
                            echo '<tr style="text-align:left !important; border-bottom: 1px solid '.$params['list_list_border_color'].' !important;"><td style="width:40%; border-bottom: 1px solid '.$params['list_list_border_color'].' !important;text-align: left !important;line-height: 1em;color:' . $params['list_page_text_color_1'] . '"><b>' . $par1_data[0] . ':</b></td>';


                            echo '<td style=" border-bottom: 1px solid '.$params['list_list_border_color'].' !important;text-align: left !important; width:' . $params['parameters_select_box_width'] . 'px;color:' . $params['list_page_text_color_2'] . '"><ul class="spidercatalogparamslist">';

                            for ($k = 0; $k < count($par_values); $k++)
                                if ($par_values[$k] != "")
                                    echo '<li>' . stripslashes($par_values[$k]) . '</li>';

                            echo '</ul></td></tr>';

                        }
                    }
                echo '</table></td>';
            }

            if($params['list_show_description'])
            {
                $description = explode('<!--more-->', stripslashes($row->description));
                echo '<td style="vertical-align: middle;border-right: solid 1px '.$params['list_list_border_color'].' !important;border-width:'.$params['list_border_width'].'px;border-color:'.$params['list_border_color'].';border-style:'.$params['list_border_style'].';border-top:none; border-left:none;padding:10px">
   <div id="prodDescription">' . $description[0] . ' </div>
   <div id="prodMore"><a href="' . $link . '" style="' . (($params['list_hyperlink_color'] != '') ? ('color:' . $params['list_hyperlink_color'] . ';') : '') . '">' . __('More', 'sp_catalog') . '</a></div>
   </td>';
            }

            if($params['price'] or $params['market_price'])
            {
                echo '<td style="vertical-align: middle;border-width:'.$params['list_border_width'].'px;border-color:'.$params['list_border_color'].';border-style:'.$params['list_border_style'].';border-top:none; border-left:none;">';

                if ($params['price'] and $row->cost != 0 and $row->cost != '')
                    echo '<div id="prodCost" style="font-size:' . $params['price_size_list'] . 'px;color:' . $params['list_cell_price_color'] . ';">' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . '&nbsp;' . $row->cost . '&nbsp;' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</div>';

                if ($params['market_price'] and $row->market_cost != 0 and $row->market_cost != '')
                    echo '<div id="prodCost" style="color:' . $params['list_cell_market_price_color'] . ';font-size:' . ($params['market_price_size_list']) . 'px;">' . __('Market Price:', 'sp_catalog') . ' <span style=" text-decoration:line-through;color:' . $params['list_cell_market_price_color'] . ';"> ' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . '&nbsp;' . $row->market_cost . '&nbsp;' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</span></div>';

                echo '</td>';
            }

            echo '</tr>';

        }

        if (count($rows))
            echo '</table>';
        ?>

        <script>
            function submit_catal(page_link) {
                if (document.getElementById('cat_form_page_nav1')) {
                    document.getElementById('cat_form_page_nav1').setAttribute('action', page_link);
                    document.getElementById('cat_form_page_nav1').submit();
                }
                else {
                    window.location.href = page_link;
                }
            }

        </script>


        <div id="spidercatalognavigation" style="text-align:center;">
        <?php
        $url = '';
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;

        if ($prod_count > $prod_in_page and $prod_in_page > 0) {
            $r = ceil($prod_count / $prod_in_page);

            $navstyle = 'font-size:12px !important;' . (($params['list_text_color'] != '') ? ('color:' . $params['list_text_color'] . ' !important;') : '');

            $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

            if ($page_num > 5) {
                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                echo "
&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

            }


            if ($page_num > 1) {
                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

            }


            for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                if ($i <= $r and $i >= 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                    if ($i == $page_num)
                        echo "<span style='font-weight:bold;color:#000000'>&nbsp;$i&nbsp;</span>";

                    else
                        echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                }

            }


            if ($page_num < $r) {
                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

            }

            if (($r - $page_num) > 4) {
                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

            }
        }


        ?>
        </div><?php } ?></div>

    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>
    <?php


    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;

}

function front_end_catalog_cells($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{
    ob_start();
    global $ident;
    $frontpage_id = get_option('page_for_posts');

    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $prod_iterator = 0;
if ($params['enable_rating']):
    ?>
    <style type="text/css">

        .star-rating {
            background: url(<?php   echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells1_rating_star'] . '.png';?>) top left repeat-x !important;
        }

        .star-rating li a:hover {
            background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells1_rating_star'] . '.png'; ?>) left bottom !important;
        }

        .star-rating li.current-rating {
            background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells1_rating_star'] . '.png';?>) left center !important;
        }

        .star-rating1 {
            background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells1_rating_star'] . '.png';?>) top left repeat-x !important;
        }

        .star-rating1 li.current-rating {
            background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells1_rating_star'] . '.png';?>) left center !important;
        }

    </style>

<?php
endif;
html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells1_");


    global $post;
    $page_id = $post->ID;


    $frontpage_id = get_option('page_for_posts');
    if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
        echo '<script>
      document.getElementById("cat_form_page_nav1").style.display = "none";
      </script>';
    }

    $prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells1_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }
    if(count($rows))
        echo '<table class="spider_catalog_style" cellpadding="0" cellspacing="0" id="productMainTable" style="width:'. ($params['cells1_count_of_product_in_the_row']*$params['product_cell_width']+$params['cells1_count_of_product_in_the_row']*4).'px"><tr>';
    if ($params7['show_prod'] == 1) {
        $urll = site_url();
        $perm = get_permalink();


        foreach ($rows as $row) {
            if (($prod_iterator % $params['cells1_count_of_product_in_the_row']) === 0 and $prod_iterator > 0)
                echo "</tr><tr>";


            $prod_iterator++;
            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';


            $imgurl = explode(";;;", $row->image_url);
            $array=explode(" ",esc_html(stripslashes($row->name)));
            $array2=str_replace("$array[0]","",esc_html(stripslashes($row->name)));

            echo '<td><div id="productMain" style="overflow:hidden;margin:10px;' . (($params['cells1_text_color'] != '') ? ('color:' . $params['cells1_text_color'] . ';') : '') . (($params['cell_small_image_backround_color'] != '') ? ('background-color:' . $params['cell_small_image_backround_color'] . ';') : '') . ' width:' . $params['product_cell_width'] . 'px;height:' . ($params['product_cell_height'] - 20) . 'px;">

<table style="height:100%;border-width:' . $params['cells1_border_width'] . 'px;border-color:' . $params['cells1_border_color'] . ';border-style:' . $params['cells1_border_style'] . ';"><tr><td>

<div id="prodTitle" style="margin: 0 !important;padding:5px;' . (($params['cells1_title_color'] != '') ? ('color:' . $params['cells1_title_color'] . ';') : '') . (($params['cells1_title_background_color'] != '') ? ('background-color:' . $params['cells1_title_background_color'] . ';') : '') . '"><table style="margin: 0 !important; width:100%;"><tr style="height: 72px;line-height:1.5;"><td><font style="word-break: break-word;font-size:'.$params['cell_big_title_size'].'px;">' . $array[0] . '</font><p style="word-break: break-word;line-height: 1;font-size:' . $params['title_size_small'] . 'px;">'.$array2.'</p></td><td style="background-color:' . $params['cell_price_background_color'] . ';">';

            if ($params['price'] and $row->cost != 0 and $row->cost != '')
                echo '<div id="prodCost" style="min-width: 108px;text-align: center;font-size:' . $params['price_size_small'] . 'px;color:' . $params['cells1_price_color'] . ';">' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</div>';

            if ($params['market_price'] and $row->market_cost != 0 and $row->market_cost != '')
                echo '<div id="prodCost" style="text-align: center;font-size:' . ($params['market_price_size_small']) . 'px;"><span style="color:' . $params['cells1_market_price_color'] . ';">' . __('Market Price:', 'sp_catalog') . ' </span><br><span style=" text-decoration:line-through;color:' . $params['cells1_market_price_color'] . ';"> ' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->market_cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</span></div>';
           
            echo '</tr></tr></table></div></td></tr><tr><td><table id="prodMiddle" border="0" cellspacing="0" cellpadding="0" style="margin: 0 !important;"><tr><td>';


            if ($params['enable_rating']) {
                $id = $row->id;


                $rating = $ratings[$id] * 25;


                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;float: right;'>

			<ul class='star-rating'>	

				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   	title='" . $title . "' class='two-stars'>2</a></li>	
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  	 title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    	title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating', 'sp_catalog') . ' ' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;float: right;'>

			<ul class='star-rating1'>	

			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

			<li><a  title='" . $title . "' class='one-star'>1</a></li>

			<li><a  title='" . $title . "' class='two-stars'>2</a></li>

			<li><a title='" . $title . "' class='three-stars'>3</a></li>

			<li><a title='" . $title . "' class='four-stars'>4</a></li>

			<li><a title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                }

            }

            echo '</td></tr></table></td></tr><tr style="height:100%;"><td style="vertical-align:top;height:100%;vertical-align:top;"><table cellpadding="0"  style="height:100%;"><tr>';

            if (!($row->image_url != "" and $row->image_url != "******0")) {
                $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);

                echo '<td style="vertical-align: middle;background-image:url('.plugins_url("Front_images/cellpicback.png", __FILE__).');
                     background-size: '.($params['cells1_small_picture_width']+ 115).'px '.($params['cells1_small_picture_height']+ 440).'px;background-position:center;padding:20px;
                    background-repeat: no-repeat; width:'.($params['cells1_small_picture_width']+ 115).'px; height:'.($params['cells1_small_picture_height']+ 40).'px;"><center><img style="width:'.$params['cells1_small_picture_width'].'px; height:'.$params['cells1_small_picture_height'].'px;" src="'.$imgurl[0].'" /></center>
                    </td>';
            } else {
                $image_and_atach = explode('******', $imgurl[0]);
                $image = $image_and_atach[0];
                if (isset($image_and_atach[1]))
                    $atach = $image_and_atach[1];
                else
                    $atach = '';
                if ($atach) {
                    $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $image;
                }
                echo '<td style="vertical-align: middle; text-align:center;background-image:url(' . plugins_url("Front_images/cellpicback.png", __FILE__) . ');background-size: '.($params['cells1_small_picture_width']+ 115).'px '.($params['cells1_small_picture_height']+ 40).'px;background-position:center;padding:20px;background-repeat: no-repeat; width:'.($params['cells1_small_picture_width']+ 115).'px; height:'.($params['cells1_small_picture_height']+ 40).'px;"><a href="' . $image . '" target="_blank"><img style="max-width:'.$params['cells1_small_picture_width'].'px; max-height:'.$params['cells1_small_picture_height'].'px;" src="' . $attach_url . '" /></a></td>';
            }
            $small_images_str='';
            $small_images_count=0;

            foreach($imgurl as $key=>$img)
            {
                if($img!=='')
                {
                    $img = explode('******', $img);
                    $small_images_str.='<a style="vertical-align: middle !important;" href="'.$img[0].'" target="_blank"><img style="vertical-align: middle !important;max-height:77px;max-width:77px;" src="'.$img[0].'"  /></a>';
                    $small_images_count++;
                }
            }
            echo '</tr>';
            if($small_images_count>1) {
                echo '<tr><td style="background-color:'.$params['cell_small_image_backround_color'].';height: ' . ($small_images_count > 1 ? "80" : "0") . 'px; vertical-align: middle !important;text-align:center;">';
                echo $small_images_str;
                echo '</td></tr>';
            }
            echo'<tr style="height:100%;"><td colspan="2" style="background:'.$params['cell_small_image_backround_color'].';position: relative;padding-left: 3px !important;padding-right: 13px !important;vertical-align:top;">';

            $categories_id=explode(',',$row->category_id);

            echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\" style=\"height:100%;width:100%;margin-left:6px !important\">";
            echo '<tr style="background-size: '.$params['product_cell_width'].'px 12px;background-repeat: no-repeat;background-position:center;
		            background-image:url(' . plugins_url("Front_images/cellerkar.png", __FILE__) . '); vertical-align:middle; background-color: transparent !important; height: 10px;"><td colspan="2"/></tr>';

            if ($row->category_id > 0 and $params['cell_show_category']) {
                echo '<tr style="border-bottom:solid 1px #d8d7d7;' . (($params['cells1_params_background_color1'] != '') ? ('background-color:' . $params['cells1_params_background_color1'] . ';') : '') . ' vertical-align:middle;"><td style="width:40%;color:'.$params['cell_params_text_color'].';padding-left:20px;font-size:'.$params['cell_parameters_left_size'].'px;" ><b>' . __('Category:', 'sp_catalog') . '</b></td><td style="color:'.$params['cell_params_text_color'].'"><span id="cat_' . $row->id . '">';                
                foreach($categories as $key=>$categ)
                {
                    if(in_array($key,$categories_id))
                        echo $categ.'<br>';

                }
                echo '</span></td></tr>';
            }
            else
                echo '<span id="cat_' . $row->id . '"></span>';


            $par_data = explode("par_", $row->param);

            if ($params['cell_show_parameters']) {

                for ($j = 0; $j < count($par_data); $j++)
                    if ($par_data[$j] != '') {
                        $par1_data = explode("@@:@@", $par_data[$j]);


                        $par_values = explode("	", $par1_data[1]);


                        $countOfPar = 0;

                        for ($k = 0; $k < count($par_values); $k++)
                            if ($par_values[$k] != "")
                                $countOfPar++;

                        $bgcolor = (($j % 2) ? (($params['cells1_params_background_color2'] != '') ? ('background-color:' . $params['cells1_params_background_color2'] . ';') : '') : (($params['cells1_params_background_color1'] != '') ? ('background-color:' . $params['cells1_params_background_color1'] . ';') : ''));

                        if ($countOfPar != 0) {
                            echo '<tr style="' . $bgcolor . 'text-align:left;border-bottom:solid 1px #d8d7d7;"><td style="color:'.$params['cell_params_text_color'].';padding-left:20px;font-size:'.$params['cell_parameters_left_size'].'px;"><b>' . stripslashes($par1_data[0]) . ':</b></td>';

                            echo '<td style="' . (($params['text_size_list'] != '') ? ('font-size:' . $params['text_size_list'] . 'px;') : '') . 'width:' . $params['parameters_select_box_width'] . 'px;color:'.$params['cell_params_text_color'].';"><ul class="spidercatalogparamslist">';

                            for ($k = 0; $k < count($par_values); $k++)
                                if ($par_values[$k] != "")
                                    echo '<li>' . stripslashes($par_values[$k]) . '</li>';

                            echo '</ul></td></tr>';
                        }

                    }

            }


            $description = explode('<!--more-->', stripslashes($row->description));
            if (!isset($bgcolor)) {
              $bgcolor = (isset($params['params_background_color2']) && $params['params_background_color2'] != '') ? ('background-color:' . $params['params_background_color2'] . ';') : '';
            }
            
            echo '<tr style="' . $bgcolor . 'text-align:left;border-bottom:solid 1px #d8d7d7;"><td style="color:'.$params['cell_params_text_color'].';padding-left:20px;font-size:'.$params['cell_parameters_left_size'].'px;" colspan="2">' . $description[0] . '</td></tr><tr style="height:100%;"><td colspan="2" style="vertical-align: bottom;height:100%;"><a href="' . $link . '" style="color:'.$params['cell_more_font_color'].';font-size:'.$params['cell_more_font_size'].'px;"><div style="float: right;bottom:5px;position: relative;"><span style="background-color:'.$params['cell_more_background_color'].';padding: 4px;padding-left: 11px;padding-right: 11px;">' . __('More', 'sp_catalog') . '</span></div></a></td></tr>';

            echo '</table></td></tr>';            
            echo '</table></td></tr>
</table>
</td>

';
        }
    }

    if (count($rows))
        echo '</tr></table>';
    ?>
    <script>
        function submit_catal(page_link) {
            if (document.getElementById('cat_form_page_nav')) {
                document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                document.getElementById('cat_form_page_nav').submit();
            }
            else {
                window.location.href = page_link;
            }
        }
    </script>

    <div id="spidercatalognavigation" style="text-align:center;">

        <?php





        $url = "";
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;


        if ($params7['show_prod'] == 1) {

            if ($prod_count > $prod_in_page and $prod_in_page > 0) {
                $r = ceil($prod_count / $prod_in_page);


                $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');


                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

                if ($page_num > 5) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                    echo "

&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

                }


                if ($page_num > 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }


                for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                    if ($i <= $r and $i >= 1) {
                        $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                        if ($i == $page_num)
                            echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . ($params['text_size_small'] + 4) . 'px !important;') : 'font-size:16px !important;') . "' >&nbsp;$i&nbsp;</span>";

                        else
                            echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                    }

                }


                if ($page_num < $r) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }

                if (($r - $page_num) > 4) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                    echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

                }

            }
        }
        ?></div>
    </div>
    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>

    <?php
    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}



















function front_end_catalog_cells2($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{

    ob_start();
    global $ident;
    $frontpage_id = get_option('page_for_posts');

    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $prod_iterator = 0;
    if ($params['enable_rating']):
        ?>
        <style type="text/css">

            .star-rating {
                background: url(<?php   echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells2_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating li a:hover {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells2_rating_star'] . '.png'; ?>) left bottom !important;
            }

            .star-rating li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells2_rating_star'] . '.png';?>) left center !important;
            }

            .star-rating1 {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells2_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating1 li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells2_rating_star'] . '.png';?>) left center !important;
            }

        </style>
        <style type="text/css">
        #productMainDiv .prodTitle
        {
            -webkit-border-bottom-left-radius: 22px;
            -moz-border-radius-bottomleft: 22px;
            border-bottom-left-radius: 22px;
        }
        
        #spanman p {
            margin: 0;
        }
    </style>
    <?php
    endif;
    html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells2_");
    global $post;
    $page_id = $post->ID;


    $frontpage_id = get_option('page_for_posts');
    if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
        echo '<script>
      document.getElementById("cat_form_page_nav1").style.display = "none";
      </script>';
    }

    $prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells2_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }
    if(count($rows))
        echo '<table cellpadding="0" cellspacing="0" id="productMainTable" style="width:'. ($params['cells2_count_of_product_in_the_row']*$params['new_cell_all_width']+$params['cells2_count_of_product_in_the_row']*20).'px"><tr>';
    if ($params7['show_prod'] == 1) {
        $urll = site_url();
        $perm = get_permalink();


        foreach ($rows as $row) {
            if (($prod_iterator % $params['cells2_count_of_product_in_the_row']) === 0 and $prod_iterator > 0)
                echo "</tr><tr>";


            $prod_iterator++;
            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';


            $imgurl = explode(";;;", $row->image_url);
            $array=explode(" ",esc_html(stripslashes($row->name)));
            $array2=str_replace("$array[0]","",esc_html(stripslashes($row->name)));
            
            $prz = $params['price'] and $params['market_price'];
            if ($prz) {
              $prz = $row->cost != 0 and $row->cost != '' and $row->market_cost != 0 and $row->market_cost != '';
            }

            echo '<td><div id="productMainDiv" class="spider_catalog_style" style="padding:10px;' . ($prz ? 'background-image:url('.plugins_url("Front_images/tuxt.png", __FILE__).');' : '') . 'background-repeat:no-repeat;background-position: 100% 0;background-size: 150px 140px;' . (($params['cells2_text_color'] != '') ? ('color:' . $params['cells2_text_color'] . ';') : '') .  ' width:' . $params['new_cell_all_width'] . 'px; height:' . $params['new_cell_all_height'] . 'px;background-color:' . $params['cells2_cell_background_color'] . '">
                <div style="height:' . ($params['new_cell_all_height'] - 20) . 'px;">

            <table style="height:100%; width: inherit;"><tr><td>

            <div id="prodTitle" class="prodTitle" style="overflow:hidden;margin-bottom:4px;padding: 0 0 0 5px;height:100px;background-image:url('.plugins_url("Front_images/seriy.png", __FILE__).');background-size:'.($params['new_cell_all_width'] - ($prz ? 145 : 0)).'px 100px;background-repeat: no-repeat;"><table style="color:' . $params['cell_new_title_color'].'; width: inherit;"><tr><td style="word-break: break-word;width:'.($params['new_cell_all_width']-145).'px;"><font style="font-size: ' . $params['cell_new_big_title_size'] . 'px;">' . $array[0] . '</font><span style="font-size:' . $params['cell_new_title_size'] . 'px"><br>'.$array2.'&nbsp;&nbsp;&nbsp;</span></td><td style="width: 100px;">';

            if ($params['price'] and $row->cost != 0 and $row->cost != '')
                echo '<div id="prodCost" style="text-align: right;font-size:' . $params['cell_new_price_size'] . 'px;color:' . $params['cells2_price_color'] . ';">' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</div>';
            if ($params['market_price'] and $row->market_cost != 0 and $row->market_cost != '')
                echo '<div id="prodCost" style="text-align: right;font-size:' . ($params['cell_new_market_price_size']) . 'px;"><span style="color:' . $params['cells2_market_price_color'] . ';">' . __('Market Price:', 'sp_catalog') . ' </span><span style=" text-decoration:line-through;color:' . $params['cells2_market_price_color'] . ';"><br> ' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->market_cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</span></div>';

            echo '</td></tr></table></div></td></tr><tr><td>';


            if ($params['enable_rating']) {
                $id = $row->id;


                $rating = $ratings[$id] * 25;


                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;padding-left:7px;'>

			<ul class='star-rating'>

				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   	title='" . $title . "' class='two-stars'>2</a></li>
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  	 title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    	title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating', 'sp_catalog') . ' ' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;padding-left:7px;'>

			<ul class='star-rating1'>

			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

			<li><a  title='" . $title . "' class='one-star'>1</a></li>

			<li><a  title='" . $title . "' class='two-stars'>2</a></li>

			<li><a title='" . $title . "' class='three-stars'>3</a></li>

			<li><a title='" . $title . "' class='four-stars'>4</a></li>

			<li><a title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                }

            }

            echo '</td></tr><tr style="height:100%;"><td style="vertical-align:top;"><table style="margin: 0;height:100%; width: 100%;"><tr style="height:'.($params['cell_new_picture_height'] + 20).'px;">';

            if (!($row->image_url != "" and $row->image_url != "******0")) {
                $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);

                echo '<td style="border:none !important;vertical-align: middle !important;padding-right:15px;height:'.$params['cell_new_picture_height'].'px;width:'.$params['cell_new_picture_width'].'px;"><img  style="max-width:'.$params['cell_new_picture_width'].'px;max-height:'.$params['cell_new_picture_height'].'px;border:solid 1px #e5e5e5;" src="'.$imgurl[0].'" /></td>';
            } else {
                $image_and_atach = explode('******', $imgurl[0]);
                $image = $image_and_atach[0];
                if (isset($image_and_atach[1]))
                    $atach = $image_and_atach[1];
                else
                    $atach = '';
                if ($atach) {
                    $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $image;
                }
                echo '<td style="vertical-align: middle !important;padding-right:15px;height:'.$params['cell_new_picture_height'].'px;width:'.$params['cell_new_picture_width'].'px;"><a href="' . $image . '" target="_blank"><img  style="border:none !important;max-width:'.$params['cell_new_picture_width'].'px;max-height:'.$params['cell_new_picture_height'].'px;border:solid 1px #e5e5e5;" src="' . $attach_url . '" /></a></td>';
            }
            echo'<td style="text-align:justify;vertical-align: middle;"><div style="max-width:' . ($params['new_cell_all_width'] - $params['cell_new_picture_width'] - 10) . 'px;">';
            $small_images_str='';
            $small_images_count=0;
            foreach($imgurl as $key=>$img)
            {
                if($img!=='')
                {
                    $img = explode('******', $img);
                    $small_images_str.='<a href="'.$img[0].'" target="_blank"><img  style="border:none !important;max-width:' . (($params['new_cell_all_width'] - $params['cell_new_picture_width']) / 3) . 'px;max-height:' . ($params['cell_new_picture_width'] / 3) . 'border:solid 1px #e5e5e5;margin:2px;" src="' . $img[0] . '"/></a>';

                    $small_images_count++;
                }
            }

            if($small_images_count>1) {
                echo $small_images_str;
            }
            else {
                echo '&nbsp;';
            }
            echo '</div></td></tr>';
            echo'<tr style="height:100%;"><td colspan="2">';


            $categories_id=explode(',',$row->category_id);

            echo '<table style="margin:0; width: 100%; height:100%;">';

            if ($row->category_id > 0 and $params['cell_show_category']) {
                echo '<tr style="font-size:'.$params['cell_new_text_size'].'px;color:'.$params['cell_new_text_color'].';background-color:'.$params['cell_new_text_back_color_1'].'; vertical-align:middle;"><td style="padding-left:12px !important;padding-right:12px;padding-top:12px; width:40%;"><b>' . __('Category:', 'sp_catalog') . '</b></td><td style="padding-left:12px;padding-right:12px;padding-top:12px;"><span id="cat_' . $row->id . '">';
                foreach($categories as $key=>$categ)
                {
                    if(in_array($key,$categories_id))
                        echo $categ.'<br>';

                }
                echo '</span></td></tr>';
            }
            else
                echo '<span id="cat_' . $row->id . '"></span>';


            $par_data = explode("par_", $row->param);

            if ($params['cell_show_parameters']) {

                for ($j = 0; $j < count($par_data); $j++)
                    if ($par_data[$j] != '') {
                        $par1_data = explode("@@:@@", $par_data[$j]);


                        $par_values = explode("	", $par1_data[1]);


                        $countOfPar = 0;

                        for ($k = 0; $k < count($par_values); $k++)
                            if ($par_values[$k] != "")
                                $countOfPar++;

                        $bgcolor = (($j % 2) ? (($params['cell_new_text_back_color_2'] != '') ? ('background-color:' . $params['cell_new_text_back_color_2'] . ';') : '') : (($params['cell_new_text_back_color_1'] != '') ? ('background-color:' . $params['cell_new_text_back_color_1'] . ';') : ''));

                        if ($countOfPar != 0) {
                            echo '<tr style="font-size:'.$params['cell_new_text_size'].'px;' . $bgcolor . 'text-align:left"><td style=" width:40%;color:'.$params['cell_new_text_color'].';padding-left:12px !important;padding-right:12px; !important"><b>' . $par1_data[0] . ':</b></td>';

                            echo '<td style="color:'.$params['cell_new_text_color'].';width:' . $params['parameters_select_box_width'] . 'px;"><ul class="spidercatalogparamslist">';

                            for ($k = 0; $k < count($par_values); $k++)
                                if ($par_values[$k] != "")
                                    echo '<li>' . stripslashes($par_values[$k]) . '</li>';

                            echo '</ul></td></tr>';
                        }

                    }

            }


            $description = explode('<!--more-->', stripslashes($row->description));

            echo '<tr style="height:100%;"><td colspan="2"><div style="height: 100%;color:'.$params['cell_new_text_color'].';font-size:'.$params['cell_new_text_size'].'px;padding-left:12px;padding-right:12px;background-color:' . $params['cell_new_text_back_color_2'] . ';"  id="spanman">' . $description[0] . '</div></td></tr><tr><td colspan="2"><a href="' . $link . '" style="position:relative;bottom: 0;display:block;color:' . $params['cell_new_more_font_color'] . ';text-align:center;text-decoration:none;min-height:20px; background-color:' . $params['cell_new_more_background_color'] . ';font-size:' . $params['new_cell_more_font_size'] . 'px !important;">' . __('More', 'sp_catalog') . '</a></td></tr>';
            echo '</table></td></tr></table></td></tr></table></div></div></td>';                     
        }
		if (count($rows))
		  echo '</tr></table>';   
    }
    
    ?>
    <script>
        function submit_catal(page_link) {
            if (document.getElementById('cat_form_page_nav')) {
                document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                document.getElementById('cat_form_page_nav').submit();
            }
            else {
                window.location.href = page_link;
            }
        }
    </script>

    <div id="spidercatalognavigation" style="text-align:center;">

        <?php





        $url = "";
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;


        if ($params7['show_prod'] == 1) {

            if ($prod_count > $prod_in_page and $prod_in_page > 0) {
                $r = ceil($prod_count / $prod_in_page);


                $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');


                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

                if ($page_num > 5) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                    echo "

&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

                }


                if ($page_num > 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }


                for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                    if ($i <= $r and $i >= 1) {
                        $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                        if ($i == $page_num)
                            echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . ($params['text_size_small'] + 4) . 'px !important;') : 'font-size:16px !important;') . "' >&nbsp;$i&nbsp;</span>";

                        else
                            echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                    }

                }


                if ($page_num < $r) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }

                if (($r - $page_num) > 4) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                    echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

                }

            }
        }
        ?></div>
</div>
    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>

    <?php
    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

function front_end_catalog_cells3($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{

    ob_start();
    global $ident;
    $frontpage_id = get_option('page_for_posts');

    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $prod_iterator = 0;
    if ($params['enable_rating']):
        ?>
        <style type="text/css">

            .star-rating {
                background: url(<?php   echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells3_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating li a:hover {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells3_rating_star'] . '.png'; ?>) left bottom !important;
            }

            .star-rating li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells3_rating_star'] . '.png';?>) left center !important;
            }

            .star-rating1 {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells3_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating1 li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['cells3_rating_star'] . '.png';?>) left center !important;
            }

        </style>
        <style>
          #spanman p {
              margin: 0;
          }

          #productMainDiv
          {
              -webkit-border-radius: 4px;
              -moz-border-radius: 4px;
              border-radius: 4px;
          }
      </style>
    <?php
    endif;
    html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells3_");

    global $post;
    $page_id = $post->ID;


    $frontpage_id = get_option('page_for_posts');
    if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
        echo '<script>
      document.getElementById("cat_form_page_nav1").style.display = "none";
      </script>';
    }

    $prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "cells3_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }
    if(count($rows))
        echo '<table cellpadding="0" cellspacing="0" id="productMainTable" style="width:'. ($params['cells3_count_of_product_in_the_row']*$params['all_cell_all_width']+$params['cells3_count_of_product_in_the_row']*20).'px"><tr>';
    if ($params7['show_prod'] == 1) {
        $urll = site_url();
        $perm = get_permalink();


        foreach ($rows as $row) {
            if (($prod_iterator % $params['cells3_count_of_product_in_the_row']) === 0 and $prod_iterator > 0)
                echo "</tr><tr>";


            $prod_iterator++;
            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';


            $imgurl = explode(";;;", $row->image_url);
            $array=explode(" ",esc_html(stripslashes($row->name)));
            $array2=str_replace("$array[0]","",esc_html(stripslashes($row->name)));

            echo '<td><div id="productMainDiv" class="spider_catalog_style" style="overflow: hidden; position: relative;border-width:' . $params['cells3_border_width'] . 'px;border-color:' . $params['cells3_border_color'] . ';border-style:' . $params['cells3_border_style'] . ';' . (($params['cells3_text_color'] != '') ? ('color:' . $params['cells3_text_color'] . ';') : '') . (($params['cells3_background_color'] != '') ? ('background-color:' . $params['cells3_background_color'] . ';') : '') . ' width:' . $params['all_cell_all_width'] . 'px; height:' . $params['all_cell_all_height'] . 'px;background-color:' . $params['cells3_cell_background_color'] . '">

            <div style="height:inherit;">';

            echo '<table style="margin:0;height:inherit;"><tr>';

            if (!($row->image_url != "" and $row->image_url != "******0")) {
                $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);

                echo '<td style="height:' . ($params['all_cell_picture_height']) .'px;width:' . ($params['all_cell_picture_width']) .'px;"><center><img style="border:none;max-height:'.$params['all_cell_picture_height'].'px;max-width:'.$params['all_cell_picture_width'].'px;" src="'.$imgurl[0].'" /></center></td>';
            } else {
                $image_and_atach = explode('******', $imgurl[0]);
                $image = $image_and_atach[0];
                if (isset($image_and_atach[1]))
                    $atach = $image_and_atach[1];
                else
                    $atach = '';
                if ($atach) {
                    $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $image;
                }
                echo '<td style="height:' . ($params['all_cell_picture_height']) .'px;width:' . ($params['all_cell_picture_width']) .'px;"><center><a href="' . $image . '" target="_blank"><img style="border:none;max-height:'.$params['all_cell_picture_height'].'px;max-width:'.$params['all_cell_picture_width'].'px;" src="' . $attach_url . '" /></a></center></td>';
            }
            echo '</tr><tr style="vertical-align:top;"><td style="word-break: break-word;font-size:' . $params['all_cell_title_size']. 'px !important;"><a style="color:' . $params['all_cell_title_color']. ';" href="'. $link . ' "><b>'.esc_html(stripslashes($row->name)).'</b></a>';

            if ($params['enable_rating']) {
                $id = $row->id;
                $rating = $ratings[$id] * 25;
                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating'>

				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   	title='" . $title . "' class='two-stars'>2</a></li>
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  	 title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    	title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating', 'sp_catalog') . ' ' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating1'>

			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

			<li><a  title='" . $title . "' class='one-star'>1</a></li>

			<li><a  title='" . $title . "' class='two-stars'>2</a></li>

			<li><a title='" . $title . "' class='three-stars'>3</a></li>

			<li><a title='" . $title . "' class='four-stars'>4</a></li>

			<li><a title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                }
            }

            $description = explode('<!--more-->', stripslashes($row->description));
            echo '<span id="spanman" style="color:'.$params['all_cell_text_color'].';font-size:'.$params['all_cell_text_size'].'px !important;">'.$description[0].'</span></tr></table>';

            if ($params['price'] and $row->cost != 0 and $row->cost != '')
                echo '<div style="position: absolute;top: ' . ($params['all_cell_all_height'] - 35) . 'px;right: 5px;width:120px;background-image:url(' . plugins_url("Front_images/kapcal.png", __FILE__) . ');background-repeat: no-repeat;background-size:176px 32px;color:'.$params['all_cell_price_text_color'].';font-size:'.$params['all_cell_price_size'].'px !important;"><center>' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</center></div>';

            echo'</div></td>';
        }
    }

    if (count($rows))
        echo '</tr></table>';
    ?>
    <script>
        function submit_catal(page_link) {
            if (document.getElementById('cat_form_page_nav')) {
                document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                document.getElementById('cat_form_page_nav').submit();
            }
            else {
                window.location.href = page_link;
            }
        }
    </script>

    <div id="spidercatalognavigation" style="text-align:center;">

        <?php





        $url = "";
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;


        if ($params7['show_prod'] == 1) {

            if ($prod_count > $prod_in_page and $prod_in_page > 0) {
                $r = ceil($prod_count / $prod_in_page);


                $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');


                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

                if ($page_num > 5) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                    echo "

&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

                }


                if ($page_num > 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }


                for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                    if ($i <= $r and $i >= 1) {
                        $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                        if ($i == $page_num)
                            echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . ($params['text_size_small'] + 4) . 'px !important;') : 'font-size:16px !important;') . "' >&nbsp;$i&nbsp;</span>";

                        else
                            echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                    }

                }


                if ($page_num < $r) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }

                if (($r - $page_num) > 4) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                    echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

                }

            }
        }
        ?></div>
</div>
    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>

    <?php
    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}


function front_end_catalog_thumb($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{

    ob_start();
    global $ident;
    $frontpage_id = get_option('page_for_posts');

    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $prod_iterator = 0;
    if ($params['enable_rating']):
        ?>
        <style type="text/css">

            .star-rating {
                background: url(<?php   echo plugins_url('',__FILE__).'/Front_images/star' . $params['thumb_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating li a:hover {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['thumb_rating_star'] . '.png'; ?>) left bottom !important;
            }

            .star-rating li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['thumb_rating_star'] . '.png';?>) left center !important;
            }

            .star-rating1 {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['thumb_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating1 li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['thumb_rating_star'] . '.png';?>) left center !important;
            }

        </style>
        <style>
            #spanman p {
                margin: 5px;
                line-height: 1em;
            }

            #productMainDiv
            {
                -webkit-border-radius: 4px;
                -moz-border-radius: 4px;
                border-radius: 4px;
            }
        </style>
    <?php
    endif;
    html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "thumb_");
    global $post;
    $page_id = $post->ID;


    $frontpage_id = get_option('page_for_posts');
    if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
        echo '<script>
      document.getElementById("cat_form_page_nav1").style.display = "none";
      </script>';
    }

    $prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "thumb_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }
    if(count($rows))
        echo '<table cellpadding="0" cellspacing="0" id="productMainTable" style="width:'. ($params['thumb_count_of_product_in_the_row']*$params['product_cell_width']+$params['thumb_count_of_product_in_the_row']*20).'px"><tr>';
    if ($params7['show_prod'] == 1) {
        $urll = site_url();
        $perm = get_permalink();


        foreach ($rows as $row) {
            if (($prod_iterator % $params['thumb_count_of_product_in_the_row']) === 0 and $prod_iterator > 0)
                echo "</tr><tr>";


            $prod_iterator++;
            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';


            $imgurl = explode(";;;", $row->image_url);
            $array=explode(" ",esc_html(stripslashes($row->name)));
            $array2=str_replace("$array[0]","",esc_html(stripslashes($row->name)));

            echo '<td style="padding-bottom: 10px !important;"><div id="productMainDivLittle" class="spider_catalog_style" style="overflow:hidden;padding-right: 0px !important;border-width:' . $params['thumb_border_width'] . 'px;border-color:' . $params['thumb_border_color'] . ';border-style:' . $params['thumb_border_style'] . ';' . (($params['thumb_text_color'] != '') ? ('color:' . $params['thumb_text_color'] . ';') : '') . (($params['thumb_cell_background_color'] != '') ? ('background-color:' . $params['thumb_cell_background_color'] . ';') : '') . ' width:' . $params['cell_tumble_all_width'] . 'px; height:' . $params['cell_tumble_all_height'] . 'px;">

            <div><table style="margin: 4px !important;"><tr>';

            if (!($row->image_url != "" and $row->image_url != "******0")) {
                $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);

                echo '<td style="width: '.($params['cell_tumble_picture_width']+22).'px;height: '.($params['cell_tumble_picture_height']+22).'px;vertical-align: top !important;"><div style="display: table-cell;text-align:center;vertical-align:middle;width: '.($params['cell_tumble_picture_width']+22).'px;height: '.($params['cell_tumble_picture_height']+22).'px;background-image:url(' . plugins_url("Front_images/fons.png", __FILE__) . ');background-repeat: no-repeat;background-size:100% 100%;"><img style="border: none;height:'.$params['cell_tumble_picture_height'].'px;width:'.$params['cell_tumble_picture_width'].'px;" src="'.$imgurl[0].'" /></div></td>';
            } else {
                $image_and_atach = explode('******', $imgurl[0]);
                $image = $image_and_atach[0];
                if (isset($image_and_atach[1]))
                    $atach = $image_and_atach[1];
                else
                    $atach = '';
                if ($atach) {
                    $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $image;
                }
                echo '<td style="width: '.($params['cell_tumble_picture_width']+22).'px;height: '.($params['cell_tumble_picture_height']+22).'px;vertical-align: top !important;"><div style="display: table-cell;text-align:center;vertical-align:middle;width: '.($params['cell_tumble_picture_width']+22).'px;height: '.($params['cell_tumble_picture_height']+22).'px;background-image:url(' . plugins_url("Front_images/fons.png", __FILE__) . ');background-repeat: no-repeat;background-size:100% 100%;"><a href="' . $image . '" target="_blank"><img style="border: none;max-height:'.$params['cell_tumble_picture_height'].'px;max-width:'.$params['cell_tumble_picture_width'].'px;" src="' . $attach_url . '" /></a></div></td>';
            }
            echo '<td style="vertical-align: top;font-size:'.$params['cell_tumble_title_size'].'px;"><a style="word-break: break-word;color:'.$params['cell_tumble_title_font_color'].' !important;" href="'. $link . '"><b>'.esc_html(stripslashes($row->name)).'</b></a>';

            if ($params['enable_rating']) {
                $id = $row->id;
                $rating = $ratings[$id] * 25;
                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating'>

				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   	title='" . $title . "' class='two-stars'>2</a></li>
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  	 title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    	title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating', 'sp_catalog') . ' ' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating1'>

			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

			<li><a  title='" . $title . "' class='one-star'>1</a></li>

			<li><a  title='" . $title . "' class='two-stars'>2</a></li>

			<li><a title='" . $title . "' class='three-stars'>3</a></li>

			<li><a title='" . $title . "' class='four-stars'>4</a></li>

			<li><a title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                }
            }

            if ($params['price'] and $row->cost != 0 and $row->cost != '')
                echo '<div style="background-image:url(' . plugins_url("Front_images/kaperk.png", __FILE__) . ');background-repeat: no-repeat;background-size:'.($params['cell_tumble_all_width']-138).'px 32px;padding-left:10px;color:'.$params['cell_tumble_price_text_color'].';font-size:'.$params['cell_tumble_price_size'].'px;">' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</div>';

            $description = explode('<!--more-->', stripslashes($row->description));
            echo	'<span id="spanman" style="font-size:'.$params['cell_tumble_text_size'].'px;color:'.$params['cell_tumble_text_color'].';">'.$description[0].'</span>';

            echo'</td></tr></table></div></td>';
        }
    }

    if (count($rows))
        echo '</tr></table>';
    ?>
    <script>
        function submit_catal(page_link) {
            if (document.getElementById('cat_form_page_nav')) {
                document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                document.getElementById('cat_form_page_nav').submit();
            }
            else {
                window.location.href = page_link;
            }
        }
    </script>

    <div id="spidercatalognavigation" style="text-align:center;">

        <?php





        $url = "";
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;


        if ($params7['show_prod'] == 1) {

            if ($prod_count > $prod_in_page and $prod_in_page > 0) {
                $r = ceil($prod_count / $prod_in_page);


                $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');


                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

                if ($page_num > 5) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                    echo "

&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

                }


                if ($page_num > 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }


                for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                    if ($i <= $r and $i >= 1) {
                        $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                        if ($i == $page_num)
                            echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . ($params['text_size_small'] + 4) . 'px !important;') : 'font-size:16px !important;') . "' >&nbsp;$i&nbsp;</span>";

                        else
                            echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                    }

                }


                if ($page_num < $r) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }

                if (($r - $page_num) > 4) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                    echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

                }

            }
        }
        ?></div>
</div>
    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>

    <?php
    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}





function front_end_catalog_wcells($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident)
{

    ob_start();
    global $ident;
    $frontpage_id = get_option('page_for_posts');

    $pos = strrpos(get_permalink(), "?");
    $permalink_for_sp_cat = "";
    if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink($frontpage_id);
        } else {
            $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
        }
    } else if (is_home()) {
        $pos1 = strrpos(site_url() . '/index.php', "?");
        if ($pos1) {
            $permalink_for_sp_cat = site_url() . '/index.php';
        } else {
            $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
        }
    } else {
        if ($pos) {
            $permalink_for_sp_cat = get_permalink();
        } else {
            $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
        }
    }
    $prod_iterator = 0;
    if ($params['enable_rating']):
        ?>
        <style type="text/css">

            .star-rating {
                background: url(<?php   echo plugins_url('',__FILE__).'/Front_images/star' . $params['wcells_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating li a:hover {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['wcells_rating_star'] . '.png'; ?>) left bottom !important;
            }

            .star-rating li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['wcells_rating_star'] . '.png';?>) left center !important;
            }

            .star-rating1 {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['wcells_rating_star'] . '.png';?>) top left repeat-x !important;
            }

            .star-rating1 li.current-rating {
                background: url(<?php    echo plugins_url('',__FILE__).'/Front_images/star' . $params['wcells_rating_star'] . '.png';?>) left center !important;
            }

        </style>
        <style>
            #spanman p {
                margin: 0;
            }
        </style>
    <?php
    endif;
    html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "wcells_");

    global $post;
    $page_id = $post->ID;


    $frontpage_id = get_option('page_for_posts');
    if ((!$params["choose_category"] and ($params1['categories'] > 0)) or !$params["search_by_name"]) {
        echo '<script>
      document.getElementById("cat_form_page_nav1").style.display = "none";
      </script>';
    }

    $prod_name = html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, "wcells_");
    
    
    
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {
        if ($cat_id == 'ALL_CAT')
            $cat_id = 0;
        $subcat_id = $cat_id;
    }
    if(count($rows))
        echo '<table border="0" cellspacing="0" cellpadding="0" id="productCartFullcube" style="padding-bottom: 30px;border-width:'.$params['wcells_border_width'].'px;border-color:'.$params['wcells_border_color'].';border-style:'.$params['wcells_border_style'].';border-bottom:none; border-right:none;'.(($params['wcells_text_color']!='')?('color:'.$params['wcells_text_color'].';'):'').(($params['wcells_background_color']!='')?('background-color:'.$params['wcells_background_color'].';'):'').'">';
    if ($params7['show_prod'] == 1) {
        $urll = site_url();
        $perm = get_permalink();


        foreach ($rows as $row) {
            $link = $permalink_for_sp_cat . '&ident=' . $ident . '&product_id=' . $row->id . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $page_num . '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name . '&back=1';


            $imgurl = explode(";;;", $row->image_url);
            $array=explode(" ",esc_html(stripslashes($row->name)));
            $array2=str_replace("$array[0]","",esc_html(stripslashes($row->name)));
            echo'<tr style="border-bottom:1px solid white !important;background-color:' . $params['wcells_cell_background_color'] . '">';

            if (!($row->image_url != "" and $row->image_url != "******0")) {
                $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);
                echo '<td style="text-align:center;vertical-align:middle;padding: 10px !important;border-width:'.$params['wcells_border_width'].'px;border-color:'.$params['wcells_border_color'].';border-style:'.$params['wcells_border_style'].';border-top:none; border-left:none;"><img  style="width:'.$params['single_cell_picture_width'].'px;height:'.$params['single_cell_picture_height'].'px;border: #CCC solid 1px;" src="'.$imgurl[0].'" /></td>';
            } else {
                $image_and_atach = explode('******', $imgurl[0]);
                $image = $image_and_atach[0];
                if (isset($image_and_atach[1]))
                    $atach = $image_and_atach[1];
                else
                    $atach = '';
                if ($atach) {
                    $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $image;
                }
                echo '<td style="text-align:center;vertical-align:middle;padding: 10px !important;border-width:'.$params['wcells_border_width'].'px;border-color:'.$params['wcells_border_color'].';border-style:'.$params['wcells_border_style'].';border-top:none; border-left:none;"><a href="' . $image . '" target="_blank"><img  style="max-width:'.$params['single_cell_picture_width'].'px;max-height:'.$params['single_cell_picture_height'].'px;border: #CCC solid 1px;" src="' . $attach_url . '" /></a></td>';                
            }

            echo '<td style="vertical-align:top;padding: 0px 4px 2px 4px;border-width:'.$params['wcells_border_width'].'px;border-color:'.$params['wcells_border_color'].';border-style:'.$params['wcells_border_style'].';border-top:none; border-left:none;"><a href="' . $link . '" style="word-break: break-word;' . (($params['wcells_hyperlink_color'] != '') ? ('color:' . $params['wcells_hyperlink_color'] . ';') : '') . 'font-size:'.$params['single_cell_title_size'].'px !important;color:'.$params['single_cell_title_color'].';"><b>' . esc_html(stripslashes($row->name)) . '</b></a>';
            if ($params['price'] and $row->cost != 0 and $row->cost != '') {
                echo '<div style="color:'.$params['wcells_price_color'].';float:right;font-size:'.$params['wcells_price_size'].'px;"><strong>' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</strong></div>';
            }

            if ($params['enable_rating']) {
                $id = $row->id;


                $rating = $ratings[$id] * 25;


                if ($voted[$id] == 0) {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = $ratings[$id];


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating'>

				<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

				<li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
				<li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"   	title='" . $title . "' class='two-stars'>2</a></li>
				<li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"  	 title='" . $title . "' class='three-stars'>3</a></li>
				<li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"    	title='" . $title . "' class='four-stars'>4</a></li>
				<li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                } else {
                    if ($ratings[$id] == 0)
                        $title = __('Not rated Yet.', 'sp_catalog');

                    else
                        $title = __('Rating', 'sp_catalog') . ' ' . $ratings[$id] . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');


                    echo "<div id='voting" . $row->id . "_" . $cels_or_list . "_" . $ident . "' style='height:30px;'>

			<ul class='star-rating1'>

			<li class='current-rating' id='current-rating' style=\"width:" . $rating . "px\"></li>

			<li><a  title='" . $title . "' class='one-star'>1</a></li>

			<li><a  title='" . $title . "' class='two-stars'>2</a></li>

			<li><a title='" . $title . "' class='three-stars'>3</a></li>

			<li><a title='" . $title . "' class='four-stars'>4</a></li>

			<li><a title='" . $title . "' class='five-stars'>5</a></li>

			</ul>

			</div>";

                }

            }

            $categories_id=explode(',',$row->category_id);
            if ($row->category_id > 0 and $params['cell_show_category']) {
                echo '<div style="padding-left:7px;background-color:'.$params['single_cell_background_color_1'].';font-size:'.$params['single_cell_font_1_size'].'px !important;"><span style="color:'.$params['single_cell_text_color_2'].' !important;" id="cat_' . $row->id . '">' . __('Category:', 'sp_catalog') . ':';

                foreach($categories as $key=>$categ)
                {
                    if(in_array($key,$categories_id))
                        echo $categ.'<br>';

                }
                echo '</span></div>';
            }
            else
                echo '<span id="cat_' . $row->id . '"></span>';
            echo '<table>';


            if($params['list_show_parameters'])
            {
                $par_data = explode("par_", $row->param);

                for ($j = 0; $j < count($par_data); $j++)
                    if ($par_data[$j] != '')
                    {
                        $par1_data = explode("@@:@@", $par_data[$j]);
                        $par_values = explode("	", $par1_data[1]);

                        $countOfPar = 0;

                        for ($k = 0; $k < count($par_values); $k++)
                            if ($par_values[$k] != "")
                                $countOfPar++;
                        if($j%2!=0){

                            if ($countOfPar != 0)
                            {
                                echo '<tr class="spidercatalogparamslist" >';

                                echo '<td style="background-color:'.$params['single_cell_background_color_2'].';" ><span style="color:'.$params['single_cell_text_color_1'].';font-size:'.$params['single_cell_font_2_size'].'px !important;"><b>' . $par1_data[0] . ':&nbsp;&nbsp;&nbsp;</b></span>';

                                for ($k = 0; $k < count($par_values); $k++)
                                    if ($par_values[$k] != "")
                                        echo '<span style="color:'.$params['single_cell_text_color_2'].' !important;font-size:'.$params['single_cell_font_1_size'].'px !important;">' . $par_values[$k] . '</span>';
                                echo '</td>';
                            }
                        }

                        if($j%2==0){

                            if ($countOfPar != 0)
                            {
                                echo '';

                                echo '<td style="background-color:'.$params['single_cell_background_color_2'].';"><span style="color:'.$params['single_cell_text_color_1'].';font-size:'.$params['single_cell_font_2_size'].'px !important;"><b>' . $par1_data[0] . ':&nbsp;&nbsp;&nbsp;</b></span>';

                                for ($k = 0; $k < count($par_values); $k++)
                                    if ($par_values[$k] != "")
                                        echo '<span style="color:'.$params['single_cell_text_color_2'].' !important;font-size:'.$params['single_cell_font_1_size'].'px !important;">' . $par_values[$k] . '</span>';
                                echo '</td></tr>';
                            }
                        }

                    }
            }
            echo '</table>';

            $description = explode('<!--more-->', stripslashes($row->description));
            echo '<div id="spanman" style="color:'.$params['single_cell_text_color_2'].' !important;padding-left:7px;background-color:'.$params['single_cell_background_color_1'].';font-size:'.$params['single_cell_font_1_size'].'px !important;" id="prodDescription">' . $description[0] . ' </div>';
            echo '<div  style="background-color:'.$params['wcells_more_background_color'].';width:70px;float:right;margin-bottom: 3px;;padding-left: 16px;position:relative;"><a style="color:'.$params['wcells_more_font_color'].';font-size:12pt;" href="'.$link.'">' . __('More', 'sp_catalog') . '</a></div>';

            echo '</td>';

            echo '</tr>';
        }
    }

    if (count($rows))
        echo '</table>';
    ?>
    <script>
        function submit_catal(page_link) {
            if (document.getElementById('cat_form_page_nav')) {
                document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                document.getElementById('cat_form_page_nav').submit();
            }
            else {
                window.location.href = page_link;
            }
        }
    </script>

    <div id="spidercatalognavigation" style="text-align:center;">

        <?php





        $url = "";
        if ($cat_id != 0)
            $url = '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $subcat_id;

        if ($prod_name != "")
            $url .= '&prod_name_' . $cels_or_list . '_' . $ident . '=' . $prod_name;


        if ($params7['show_prod'] == 1) {

            if ($prod_count > $prod_in_page and $prod_in_page > 0) {
                $r = ceil($prod_count / $prod_in_page);


                $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');


                $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '= ';

                if ($page_num > 5) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=1';

                    echo "

&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('First', 'sp_catalog') . "</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";

                }


                if ($page_num > 1) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num - 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Prev', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }


                for ($i = $page_num - 4; $i < ($page_num + 5); $i++) {
                    if ($i <= $r and $i >= 1) {
                        $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $i;

                        if ($i == $page_num)
                            echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . ($params['text_size_small'] + 4) . 'px !important;') : 'font-size:16px !important;') . "' >&nbsp;$i&nbsp;</span>";

                        else
                            echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";

                    }

                }


                if ($page_num < $r) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . ($page_num + 1);

                    echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Next', 'sp_catalog') . "</a>&nbsp;&nbsp;";

                }

                if (($r - $page_num) > 4) {
                    $link = $permalink_for_sp_cat . $url . '&page_num_' . $cels_or_list . '_' . $ident . '=' . $r;

                    echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">" . __('Last', 'sp_catalog') . "</a>";

                }

            }
        }
        ?></div>
</div>
    <script type="text/javascript">
        var SpiderCatOFOnLoad = window.onload;
        window.onload = SpiderCatAddToOnload;
    </script>

    <?php
    $ident++;
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}




function html_front_end_single_product($rows, $reviews_rows, $params, $category_name, $rev_page, $reviews_count, $rating, $voted, $product_id, $ident)
{
  ob_start();
  global $ident;
  ?>
  <div id="spider_catalog_div" style="background-color:<?php echo $params['single_background_color']; ?>; border-width:<?php echo $params['single_border_width']; ?>px; border-color:<?php echo $params['single_border_color']; ?>; border-style:<?php echo $params['single_border_style']; ?>; color:<?php echo $params['single_text_color']; ?>; font-size:<?php echo $params['text_size_big']; ?>px;">
  <?php if ($params['enable_rating']): ?>
    <style type="text/css">
      .star-rating {
          background: url(<?php echo plugins_url("Front_images",__FILE__).'/star'.$params['single_rating_star'].'.png'; ?>) top left repeat-x !important;
          margin-top: 0px;
      }

      .star-rating li a:hover {
          background: url(<?php echo plugins_url("Front_images",__FILE__).'/star'.$params['single_rating_star'].'.png'; ?>) left bottom !important;
      }

      .star-rating li.current-rating {
          background: url(<?php echo plugins_url("Front_images",__FILE__).'/star'.$params['single_rating_star'].'.png'; ?>) left center !important;
      }

      .star-rating1 {
          background: url(<?php echo plugins_url("Front_images",__FILE__).'/star'.$params['single_rating_star'].'.png'; ?>) top left repeat-x !important;
          margin-top: 0px;
      }

      .star-rating1 li.current-rating {
          background: url(<?php echo plugins_url("Front_images",__FILE__).'/star'.$params['single_rating_star'].'.png'; ?>) left center !important;
      }
    </style>
  <?php
  endif;
  
  $posss = strrpos(get_permalink(), "?");
  $frontpage_id = get_option('page_for_posts');
  $rest = explode("&", $_SERVER['QUERY_STRING']);
  $nnn = count($rest);

  if ((isset($_GET['ident']) && $_GET['ident'] == $ident) || !(isset($_GET['product_id']) && $_GET['product_id'])){
    foreach ($rows as $key => $row) {
      if (isset($_GET['product_id']) && $_GET['product_id'] == $row->id) {
        if ($posss) {
          if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
            $page_link1 = get_permalink($frontpage_id);
            $ff = '&' . $rest[$nnn - 4] . '&' . $rest[$nnn - 3] . '&' . $rest[$nnn - 2];
          } 
          else if (is_home()) {
            $page_link1 = site_url() . '/index.php';
            if (isset($rest[$nnn - 4]) && isset($rest[$nnn - 3]) && isset ($rest[$nnn - 2])) {
              $ff = '&' . $rest[$nnn - 4] . '?' . $rest[$nnn - 3] . '&' . $rest[$nnn - 2];
            }
            else {
              $ff = '?';
            }
          } 
          else {
            $page_link1 = get_permalink();
            $ff = '&' . $rest[$nnn - 4] . '&' . $rest[$nnn - 3] . '&' . $rest[$nnn - 2];
          }
        } 
        else {
          if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
              $page_link1 = get_permalink($frontpage_id);
              $ff = '?' . $rest[$nnn - 4] . '&' . $rest[$nnn - 3] . '&' . $rest[3];
          } 
          else if (is_home()) {
            $page_link1 = site_url() . '/index.php';
            $ff = '?' . $rest[$nnn - 4] . '&' . $rest[$nnn - 3] . '&' . $rest[$nnn - 2];
          } 
          else {
            $page_link1 = get_permalink();
            $ff = '?' . $rest[$nnn - 4] . '&' . $rest[$nnn - 3] . '&' . $rest[$nnn - 2];
          }
        }
      echo '<div id="back_to_spidercatalog_button" style="padding-bottom:5px;"><a href="' . $page_link1 . $ff . '" >' . __('Back to Catalog', 'sp_catalog') . '</a></div>';
    }
    $array = explode(" ", esc_html(stripslashes($row->name)));
    $array2 = str_replace("$array[0]", "", esc_html(stripslashes($row->name)));
    ?>
    <table id="prodMiddle" class="spider_catalog_style" style="border:inherit !important" cellspacing="0" cellpadding="0">
      <tbody>
        <tr>
          <td colspan="2">
          <?php
            $prz = $params['price'] and $params['market_price'];
            if ($prz) {
              $prz = $row->cost != 0 and $row->cost != '' and $row->market_cost != 0 and $row->market_cost != '';
            }
            echo 
            '<div id="prodTitle" style="' . (($params['single_title_color'] != '') ? ('color:' . $params['single_title_color'] . ';') : '') . (($params['single_title_background_color'] != '') ? ('background-color:' . $params['single_title_background_color'] . ';') : '') . 'padding:0px;">
              <table width="100%">
                <tr>
                  <td  style="padding:0px 0px 0px 10px !important;line-height:1em; font-size:' . $params['title_size_big'] . 'px;">
                    <font size="7" style="margin-left: 10px !important;font-size: ' . $params['product_big_title_size'] . 'px;">' . $array[0] . '</font><br/>
                    <span style="margin-left: 10px !important;line-height: 2;">' . $array2 . ' </span>
                  </td>
                  <td style="border: 5px solid transparent !important;padding-right:40px !important; text-align:right; ' . ($prz ? 'background:' . $params['product_price_background_color'] : '') . ';width:180px;">';

                  if ($params['price'] and $row->cost != 0 and $row->cost != '') {
                    echo '<div id="prodCost" style="font-size:' . $params['price_size_big'] . 'px; color:' . $params['single_price_color'] . ';">' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</div>';
                  }
                  if ($params['market_price'] and $row->market_cost != 0 and $row->market_cost != '') {
                    echo '<div id="prodCost" style="font-size:' . ($params['market_price_size_big']) . 'px;">
                            <span style="color:' . $params['single_market_price_color'] . ';">' . __('Market Price:', 'sp_catalog') . ' </span><br>
                            <span style=" text-decoration:line-through;color:' . $params['single_market_price_color'] . ';"> ' . (($params['currency_symbol_position'] == 0) ? ($params['currency_symbol']) : '') . ' ' . $row->market_cost . ' ' . (($params['currency_symbol_position'] == 1) ? $params['currency_symbol'] : '') . '</span></div>';
                  }
                  if ($params['enable_rating']) {
                    echo '</td></tr><tr>
                    <td style="padding-right:10px;" colspan="2">
                      <div style="overflow:hidden; vertical-align:top; height:25px;">
                        <div id="voting' . $row->id . '" class="rating_stars" style="width:130px; margin-left:15px !important;">';
                        if ($voted == 0) {
                          if ($rating == 0)
                            $title = __('Not rated Yet.', 'sp_catalog');
                          else
                            $title = $rating;
                          echo "
                            <ul class='star-rating'>
                              <li class='current-rating' id='current-rating' style=\"width:" . ($rating * 25) . "px\"></li>
                              <li><a href=\"#\" onclick=\"vote(1," . $row->id . ",'voting" . $row->id . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='one-star'>1</a></li>
                              <li><a href=\"#\" onclick=\"vote(2," . $row->id . ",'voting" . $row->id . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"     title='" . $title . "' class='two-stars'>2</a></li>
                              <li><a href=\"#\" onclick=\"vote(3," . $row->id . ",'voting" . $row->id . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"      title='" . $title . "' class='three-stars'>3</a></li>
                              <li><a href=\"#\" onclick=\"vote(4," . $row->id . ",'voting" . $row->id . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"     title='" . $title . "' class='four-stars'>4</a></li>
                              <li><a href=\"#\" onclick=\"vote(5," . $row->id . ",'voting" . $row->id . "','" . __('Rated.', 'sp_catalog') . "','" . admin_url('admin-ajax.php?action=catalogstarerate') . "'); return false;\"		title='" . $title . "' class='five-stars'>5</a></li>
                            </ul>";
                        } 
                        else {
                          if ($rating == 0)
                              $title = __('Not rated Yet.', 'sp_catalog');
                          else
                              $title = __('Rating', 'sp_catalog') . ' ' . $rating . '&nbsp;&nbsp;&nbsp;&nbsp;&#013;' . __('You have already rated this product.', 'sp_catalog');
                          echo "
                            <ul class='star-rating1'>
                              <li class='current-rating' id='current-rating' style=\"width:" . ($rating * 25) . "px\"></li>
                              <li><a  title='" . $title . "' class='one-star'>1</a></li>
                              <li><a  title='" . $title . "' class='two-stars'>2</a></li>
                              <li><a title='" . $title . "' class='three-stars'>3</a></li>
                              <li><a title='" . $title . "' class='four-stars'>4</a></li>
                              <li><a title='" . $title . "' class='five-stars'>5</a></li>
                            </ul>";
                        }
                        echo '</div></div></td>';
                  }

                  echo '</tr></table></div>
              </td>
          </tr>
          <tr>
            <td style="vertical-align:top;">
              <table id="spider_catalog_image_table">';
                $imgurl = explode(";;;", $row->image_url);
                if (!($row->image_url != "" and $row->image_url != "******0")) {
                    $imgurl[0] = plugins_url("Front_images/noimage.jpg", __FILE__);
                    echo '<tr>
                                <td colspan="2" id="prod_main_picture_container">
                                  <div style="width:' . ($params['large_picture_width']  * 1.2) . 'px;height:' . ($params['large_picture_height']  * 1.2) . 'px;">
                                    <div style="width:' . ($params['large_picture_width']  * 1.2) . 'px;height:' . ($params['large_picture_height']  * 1.2) . 'px;
                                        background-image:url(' . plugins_url("Front_images/prodimgb.png", __FILE__) . ');
                                        background-repeat: no-repeat;
                                        background-size:100% 100%;text-align: center;display: table-cell; vertical-align: middle;">
                                            <a href="' . $imgurl[0][0] . '" target="_blank" id="prod_main_picture_a_' . $ident . '"
                                                style="text-decoration:none; ">
                                                        <img id="prod_main_picture_' . $ident . '"
                                                            style="box-shadow: none;position:static;margin:20px;max-width:' . ($params['large_picture_width']) . 'px;max-height:' . ($params['large_picture_height']) . 'px;" src="' . $imgurl[0] . '">
                                                        </img>
                                            </a>
                                    </div>
                                  </div>
                                </td>
                            </tr>';
                } else {
                    $imgurl[0] = explode('******', $imgurl[0]);
                    echo '<tr>
                            <td colspan="2" id="prod_main_picture_container">
                              <div style="width:' . ($params['large_picture_width'] * 1.2) . 'px;height:' . ($params['large_picture_height'] * 1.2) . 'px;">
                                <div style="width:' . ($params['large_picture_width']  * 1.2) . 'px;height:' . ($params['large_picture_height'] * 1.2) . 'px;
                                    background-image:url(' . plugins_url("Front_images/prodimgb.png", __FILE__) . ');
                                    background-repeat: no-repeat;
                                    background-size:100% 100%;text-align: center;display: table-cell; vertical-align: middle;">
                                        <a href="' . $imgurl[0][0] . '" target="_blank" id="prod_main_picture_a_' . $ident . '"
                                            style="text-decoration:none; ">
                                                    <img id="prod_main_picture_' . $ident . '"
                                                        style="box-shadow: none;position:static;margin:20px;max-width:' . ($params['large_picture_width']) . 'px;max-height:' . ($params['large_picture_height']) . 'px;" src="' . $imgurl[0][0] . '">
                                                    </img>
                                        </a>
                                </div>
                              </div>
                            </td>
                          </tr>';
                  }
                  echo '
                    <tr><td style="text-align:center;">';

                    $small_images_str = '';
                    $small_images_count = 0;
                    $imgurl = explode(";;;", $row->image_url);
                    foreach ($imgurl as $img) {

                        if ($img !== '******0') {

                            $image_with_atach_id = explode('******', $img);
                            if (isset($image_with_atach_id[1]) && $image_with_atach_id[1]) {
                                $array_with_sizes = wp_get_attachment_image_src($image_with_atach_id[1], 'thumbnail');
                                $attach_url = $array_with_sizes[0];
                            } else {
                                $attach_url = $image_with_atach_id[0];
                            }
                            $img = $image_with_atach_id[0];
                            $small_images_str .= '<a href="' . $img . '" target="_blank"><img style="max-height:80px" src="' . $attach_url . '" vspace="0" hspace="0" onMouseOver="prod_change_picture(\'' . $img . '\',' . $ident . ',this,' . $params['large_picture_width'] . ',' . $params['large_picture_height'] . ');" /></a>
                ';
                            $small_images_count++;
                        }
                    }
                    if ($small_images_count > 1)
                        echo $small_images_str;
                    else
                        echo '&nbsp;';

                    echo '</td></tr>
                </table></td>
                    <td align="right" style="vertical-align: top;">';
                      echo '<table style="width:99%;">';

                      $param_chan_color = 0;
                      if ($category_name != "") {
                          echo '<tr style="border-bottom:solid 2px #e5e5e5;' . (($params['single_params_background_color1'] != '') ? ('background-color:' . $params['single_params_background_color1'] . ';') : '') . ' vertical-align:middle;"><td style="width:40%;padding:4px !important;"><b>' . __('Category:', 'sp_catalog') . '</b></td><td style="' . (($params['single_params_color'] != '') ? ('color:' . $params['single_params_color'] . ';') : '') . '"><span id="cat_' . $row->id . '">' . $category_name . '</span></td></tr>';
                          $param_chan_color++;
                      } else
                          echo '<span id="cat_' . $row->id . '"></span>';

                        $par_data = explode("par_", $row->param);

                        for ($j = 0; $j < count($par_data); $j++)
                            if ($par_data[$j] != '') {

                                $par1_data = explode("@@:@@", $par_data[$j]);

                                $par_values = explode("	", $par1_data[1]);

                                $countOfPar = 0;
                                for ($k = 0; $k < count($par_values); $k++)
                                    if ($par_values[$k] != "")
                                        $countOfPar++;
                                $bgcolor = (($j % 2) ? (($params['single_params_background_color2'] != '') ? ('background-color:' . $params['single_params_background_color2'] . ';') : '') : (($params['single_params_background_color1'] != '') ? ('background-color:' . $params['single_params_background_color1'] . ';') : ''));


                                if ($countOfPar != 0) {

                                    $param_chan_color++;

                                    echo '<tr style="border-bottom:solid 2px #e5e5e5;' . $bgcolor . 'text-align:left !important;"><td style="padding:4px !important;"><b>' . stripslashes($par1_data[0]) . ':</b></td>';


                                    echo '<td style="text-align:left !important;' . $bgcolor . (($params['single_params_color'] != '') ? ('color:' . $params['single_params_color'] . ';') : '') . '"><ul class="spidercatalogparamslist">';

                                    for ($k = 0; $k < count($par_values); $k++)
                                        if ($par_values[$k] != "")
                                            echo '<li>' . stripslashes($par_values[$k]) . '</li>';

                                    echo '</ul></td></tr>';

                                }
                            }

                        echo '<tr style="text-align:left;vertical-align:middle;"><td colspan="2"><div style="line-height: 1;padding:4px !important;' . (isset($bgcolor) ? $bgcolor : "") . '">' . $row->description . '</div></td></tr></table>';

                        echo '</td></tr><tr><td colspan="2">';


                        if ($params['enable_review'])
                        {
                        echo '<center><div style="margin-top: 10px;background-color:' . $params['product_back_add_your_review_here'] . ';width:240px;"><a name="rev" style="color:' . $params['product_color_add_your_review_here'] . ';text-decoration:inherit;font-size:150%">' . __('Add your review here', 'sp_catalog') . '</a></div></center>';

                        $pos = strpos($_SERVER['QUERY_STRING'], 'rev_page_' . $ident) - 1;
                        $reviews_perpage = $params['reviews_perpage'];
                        if ($pos > 0)
                            $url = substr($_SERVER['QUERY_STRING'], 0, $pos);
                        else
                            $url = $_SERVER['QUERY_STRING'];


                        $poss = strrpos(get_permalink(), "?");
                        $part_of_url = "";
                        if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
                            if ($poss) {
                                $part_of_url = get_permalink($frontpage_id);
                            } else {
                                $part_of_url = get_permalink($frontpage_id) . "?";
                            }
                        } else if (is_home()) {
                            $pos1 = strrpos(site_url() . '/index.php', "?");
                            if ($pos1) {
                                $part_of_url = site_url() . '/index.php';
                            } else {
                                $part_of_url = site_url() . '/index.php' . "?";
                            }
                        } else {
                            if ($poss) {
                                $part_of_url = get_permalink();
                            } else {
                                $part_of_url = get_permalink() . "?";
                            }
                        }

                        echo '
                    <div style="box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;width:99%;margin:3px; padding:10px !important;' . (($params['review_background_color'] != '') ? ('background-color:' . $params['review_background_color'] . ';') : '') . '">

                    <form  action="' . $part_of_url . '' . $url . '#rev"  name="review_' . $ident . '" method="post" >

                            <div style="margin: 0;"><input type="text" name="full_name_' . $ident . '" id="full_name_' . $ident . '" style="box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;width:100%; margin:0px;" value="' . __('Name', 'sp_catalog') . '"
                           onfocus="(this.value == \'' . __('Name', 'sp_catalog') . '\') && (this.value = \'\')"
                           onblur="(this.value == \'\') && (this.value = \'' . __('Name', 'sp_catalog') . '\')" /></div>
                            <div style="margin: 4px 0 4px 0;"><textarea rows="4" 
                            name="message_text_' . $ident . '" id="message_text_' . $ident . '" style="box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;width:100%; margin:0px;" onfocus="(this.innerHTML  == \'' . __('Message', 'sp_catalog') . '\') && (this.innerHTML  = \'\')"
                            onblur="(this.innerHTML  == \'\') && (this.innerHTML  = \'' . __('Message', 'sp_catalog') . '\')">' . __('Message', 'sp_catalog') . '</textarea></div>

                      <input type="hidden" name="product_id" value="' . $row->id . '" />
                      
                      
                      <input type="hidden" name="review_' . $ident . '" value="1" />';

                        ?>

                        <table cellpadding="0" id="spider_catalog_captcha_table" cellspacing="10" border="0" valign="middle" width="100%">
                            <tr>
                                <td>
                                    <?php echo __('Please enter the code:', 'sp_catalog') ?>
                                </td>
                                <td style="width:120px !important;">
                                    <span id="wd_captcha"><img style="width:80px"
                                                               src="<?php echo admin_url('admin-ajax.php?action=spidercatalogwdcaptchae') ?>"
                                                               id="wd_captcha_img_<?php echo $ident; ?>" height="24" width="80"/></span><a
                                        href="javascript:refreshCaptcha(<?php echo $ident; ?>);" style="text-decoration:none">&nbsp;<img
                                            src="<?php echo plugins_url("", __FILE__) ?>/Front_images/refresh.png" border="0"
                                            style="border:none"/></a>&nbsp;</td>
                                <td><input type="text" name="code_<?php echo $ident; ?>" id="review_capcode_<?php echo $ident; ?>"
                                           size="6"/><span style="display:none;" id="caphid_<?php echo $ident; ?>"></span>
                                </td>
                                <td style="text-align:right !important;" align="right">
                                  <input type="button" class="spidercatalogbutton" style="<?php echo 'background-color:' . $params['product_back_add_your_review_here'] . '; color:' . $params['product_color_add_your_review_here'] ?>; width:inherit;margin-right:10px;" value="<?php echo __('Send', 'sp_catalog') ?>" onclick='prod_id=<?php echo $ident; ?>; submit_reveiw("<?php echo __('The Name field is required.', 'sp_catalog'); ?>","<?php echo __('The Message field is required.', 'sp_catalog'); ?>","<?php echo __('Sorry, the code you entered was invalid.', 'sp_catalog'); ?>");'/>
                                </td>
                            </tr>
                        </table>

                        </form>
                        </div>

                    <?php


                        if (isset($_POST['code_' . $ident]))
                            $code = $_POST['code_' . $ident];
                        else
                            $code = '';
                        if (isset($_POST['review_' . $ident]))
                            $review = $_POST['review_' . $ident];
                        else
                            $review = '';


                        if ($review)
                            if ($code != '' and $code == $_SESSION['captcha_code']) {
                                echo '<br /><center style="font-weight:bold">' . __('The review has been added successfully.', 'sp_catalog') . '</center>';
                            } else {
                                echo '<br /><center style="font-weight:bold">' . __('Sorry, the code you entered was invalid.', 'sp_catalog') . '</center>';
                            }

                        $pos = strrpos(get_permalink(), "?");

                        $permalink_for_sp_cat = "";
                        if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
                            if ($pos) {
                                $permalink_for_sp_cat = get_permalink($frontpage_id);
                            } else {
                                $permalink_for_sp_cat = get_permalink($frontpage_id) . "?s_p_c_t=1342";
                            }
                        } else if (is_home()) {
                            $pos1 = strrpos(site_url() . '/index.php', "?");
                            if ($pos1) {
                                $permalink_for_sp_cat = site_url() . '/index.php';
                            } else {
                                $permalink_for_sp_cat = site_url() . '/index.php' . "?s_p_c_t=1342";
                            }
                        } else {
                            if ($pos) {
                                $permalink_for_sp_cat = get_permalink();
                            } else {
                                $permalink_for_sp_cat = get_permalink() . "?s_p_c_t=1342";
                            }
                        }
                        foreach ($reviews_rows as $reviews_row) {
                            echo '<br />
                      <div style="box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;margin-left:3px;padding:3px;width: 99%;' . (($params['review_author_background_color'] != '') ? ('background-color:' . $params['review_author_background_color'] . ';') : '') . '">' . __('Author:', 'sp_catalog') . ' <b>' . esc_html(stripslashes($reviews_row->name)) . '</b></div>

                       <div style="box-sizing: border-box;-moz-box-sizing: border-box;-webkit-box-sizing: border-box;margin-left:3px;width: 99%;' . (($params['review_text_background_color'] != '') ? ('background-color:' . $params['review_text_background_color'] . ';') : '') . (($params['review_color'] != '') ? ('color:' . $params['review_color'] . ';') : '') . ' padding:8px;">' . str_replace('
                    ', '<br>', esc_html(stripslashes($reviews_row->content))) . '</div>
                       ';
                        }


                    if ($reviews_count > $reviews_perpage)
                    {
                        ?>
                        <div id="spidercatalognavigation" style="text-align:center;">
                            <?php
                            $r = ceil($reviews_count / $reviews_perpage);
                            $navstyle = (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . (($params['text_color'] != '') ? ('color:' . $params['text_color'] . ' !important;') : '');
                            ?>
                            <script>
                                function submit_catal(page_link) {
                                    if (document.getElementById('cat_form_page_nav')) {
                                        document.getElementById('cat_form_page_nav').setAttribute('action', page_link);
                                        document.getElementById('cat_form_page_nav').submit();
                                    }
                                    else {
                                        window.location.href = page_link;
                                    }
                                }

                            </script>
                            <?php


                            $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '= ');
                            if ($rev_page > 5) {
                                $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '=1#rev');
                                echo "
                    &nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">first</a>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;...&nbsp";
                            }

                            if ($rev_page > 1) {
                                $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '=' . ($rev_page - 1) . '#rev');
                                echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">prev</a>&nbsp;&nbsp;";
                            }

                            for ($i = $rev_page - 4; $i < ($rev_page + 5); $i++) {
                                if ($i <= $r and $i >= 1) {
                                    $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '=' . $i . '#rev');
                                    if ($i == $rev_page)
                                        echo "<span style='font-weight:bold !important; color:#000000 !important; " . (($params['text_size_small'] != '') ? ('font-size:' . $params['text_size_small'] . 'px !important;') : 'font-size:12px !important;') . "'>&nbsp;$i&nbsp;</span>";
                                    else
                                        echo "<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">&nbsp;$i&nbsp;</a>";
                                }
                            }


                            if ($rev_page < $r) {
                                $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '=' . ($rev_page + 1) . '#rev');
                                echo "&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">next</a>&nbsp;&nbsp;";
                            }
                            if (($r - $rev_page) > 4) {
                                $link = ($permalink_for_sp_cat . $url . '&rev_page_' . $ident . '=' . $r . '#rev');
                                echo "&nbsp;...&nbsp;&nbsp;&nbsp;<a href=\"javascript:submit_catal('{$link}')\" style=\"$navstyle\">last</a>";
                            }

                            echo '</div>';
                            }
                            }
                            echo '</div>';
    }
  }
  ?>
  </td></tr></table></div><br/>
  <script type="text/javascript">
    var SpiderCatOFOnLoad = window.onload;
    window.onload = SpiderCatAddToOnload;
  </script>
  <?php
  $ident++;
  $content = ob_get_contents();
  ob_end_clean();
  return $content;
}


function html_search($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, $from) {
  if ('page' == get_option('show_on_front') && ('' != get_option('page_for_posts')) && is_home()) {
    if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''])) {
        if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) {
            if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != 0)
                $page_link = get_permalink($frontpage_id) . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
            else
                $page_link = get_permalink($frontpage_id);
        } else if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']) or isset($_POST['page_num_' . $cels_or_list . '_' . $ident . '']))
            $page_link = get_permalink($frontpage_id) . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $cat_id;
        else
            $page_link = get_permalink($frontpage_id);
    } else
        $page_link = get_permalink($frontpage_id);
  } else if (is_home()) {
      if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''])) {
          if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) {
              if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != 0)
                  $page_link = site_url() . '/index.php?cat_id_' . $cels_or_list . '_' . $ident . '=' . $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
              else
                  $page_link = site_url() . '/index.php';
          } else if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']) or isset($_POST['page_num_' . $cels_or_list . '_' . $ident . '']))
              $page_link = site_url() . '/index.php?cat_id_' . $cels_or_list . '_' . $ident . '=' . $cat_id;
          else
              $page_link = site_url() . '/index.php';
      } else
          $page_link = get_permalink();
  } else {
      if (strrpos(get_permalink(), '?')) {
          if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''])) {
              if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) {
                  if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != 0)
                      $page_link = get_permalink() . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
                  else
                      $page_link = get_permalink();
              } else if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']) or isset($_POST['page_num_' . $cels_or_list . '_' . $ident . '']))
                  $page_link = get_permalink() . '&cat_id_' . $cels_or_list . '_' . $ident . '=' . $cat_id;
              else
                  $page_link = get_permalink();
          } else
              $page_link = get_permalink();
      } else {
          if (isset($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''])) {
              if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) {
                  if ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != 0)
                      $page_link = get_permalink() . '?cat_id_' . $cels_or_list . '_' . $ident . '=' . $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
                  else
                      $page_link = get_permalink();
              } else if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']) or isset($_POST['page_num_' . $cels_or_list . '_' . $ident . '']))
                  $page_link = get_permalink() . '?cat_id_' . $cels_or_list . '_' . $ident . '=' . $cat_id;
              else
                  $page_link = get_permalink();
          } else
              $page_link = get_permalink();
      }
  }
  echo '<form action="' . $page_link . '" method="post" name="cat_form_' . $cels_or_list . '_' . $ident . '" id="cat_form_page_nav1"><input type="hidden" name="page_num_' . $cels_or_list . '_' . $ident . '"	value="1"><input type="hidden" name="subcat_id_' . $cels_or_list . '_' . $ident . '" id="subcat_id_' . $cels_or_list . '_' . $ident . '" value=""><div class="CatalogSearchBox" style="padding-top:10px;"><div class="spider_catalog_style" style="padding-bottom: 35px;">';
  if ($params["choose_category"])/* || $params1['categories'] > 0*/
  {
  ?>     
    <div style="margin-right: 90px;"><?php echo __('Choose Category', 'sp_catalog') ?></div>
    <div style="height: 30px;width: 197px !important;border: none;display: block;background-color: #ececec;position: relative;float: right;webkit-border-radius: 0px !important;-moz-border-radius: 0px !important;border-radius: 0px !important;">
    <?php
      echo '<select style="opacity:0;cursor:pointer;height: 30px;width: 197px !important;border: none;display: block;background-color: #ececec;position: absolute;z-index:10;top:0 !important;margin:0 !important;webkit-border-radius: 0px !important;-moz-border-radius: 0px !important;border-radius: 0px !important;" id="cat_id_' . $cels_or_list . '_' . $ident . '" name="cat_id_' . $cels_or_list . '_' . $ident . '" class="spidercataloginput" size="1" onChange="this.form.submit();">
        <option value="0">' . __('All', 'sp_catalog') . '</option> ';
        foreach ($category_list as $category) {
            if (isset($_POST['cat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['cat_id_' . $cels_or_list . '_' . $ident . '']) {
                if ($_POST['cat_id_' . $cels_or_list . '_' . $ident . ''] == $category->id) {
                    echo '<option value="' . $category->id . '"  selected="selected">' . esc_html(stripslashes($category->name)) . '</option>';
                } else
                    echo '<option value="' . $category->id . '" >' . esc_html(stripslashes($category->name)) . '</option>';
            } else if ($category->id == $cat_id)
                echo '<option value="' . $category->id . '"  selected="selected">' . esc_html(stripslashes($category->name)) . '</option>';
            else
                echo '<option value="' . $category->id . '" >' . esc_html(stripslashes($category->name)) . '</option>';
        }
        echo '</select>';
        foreach ($category_list as $category) {                      
          if ($category->id == $cat_id) {
            ?>
              <div style="text-align: left !important;top: 7px;left: 7px;border: none;display: block;position: relative;float: left"><?php echo esc_html(stripslashes($category->name)); ?></div>
            <?php
          }
        }
        if ($cat_id == '0') {
          ?>
            <div style="text-align: left !important;top: 7px;left: 7px;border: none;display: block;position: relative;float: left;"><?php echo __('All', 'sp_catalog'); ?></div>
          <?php
        }
        ?>
        <div style="background-color:<?php echo $params[$from . 'select_icon_color']; ?> ;position:relative;float: right;width:30px;height:30px;background-repeat: no-repeat;background-image: url('<?php echo plugins_url("Front_images/selectcat.png", __FILE__) ?>');background-size: 100% 100%; border: 0px;cursor: pointer;"></div>
      </div>
  <?php
  }
  if ($params["search_by_name"] && isset($params7['show_prod']) && $params7['show_prod'] == 1)
  {
    if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']))
        $prod_name = esc_html(stripslashes($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']));
    else if (isset($_GET['prod_name_' . $cels_or_list . '_' . $ident . '']))
        $prod_name = esc_html(stripslashes($_GET['prod_name_' . $cels_or_list . '_' . $ident . '']));
    else 
        $prod_name = '';
  ?>
    <div style="display: block;margin-top: 33px;clear: both;">
          <input id="<?php echo 'prod_name_' . $cels_or_list . '_' . $ident?>" name="<?php echo 'prod_name_' . $cels_or_list . '_' . $ident?>" style="color: inherit;vertical-align:top;-webkit-border-radius: 0px;-moz-border-radius: 0px;border-radius: 0px;text-align: left !important;height: 30px;width:137px !important;border: none;background-color:#ececec;position:relative;padding: 5px;box-sizing: border-box;" class="spidercataloginput" value="<?php echo ($prod_name ? $prod_name : __('Search...', 'sp_catalog')); ?>" onfocus="(this.value == '<?php echo __('Search...', 'sp_catalog')?>') && (this.value = '')" onblur="(this.value == '') && (this.value = '<?php echo __('Search...', 'sp_catalog')?>')"/>
          <input type="submit" value="" style="background-color:<?php echo $params[$from . 'search_icon_color']; ?>;background-image:url('<?php echo plugins_url("Front_images/search-icon.png", __FILE__) ?>') !important;width: 30px !important;height: 30px !important;background-size: 100% 100%; position:relative;background-repeat: no-repeat; border: 0px;cursor: pointer;border-radius: 0 !important; padding: 3px !important;margin:  -3px -3px 0px -3px;;">
          <input type="submit" value="" onClick="<?php echo 'cat_form_resett(this.form,' . $cels_or_list . ',' . $ident . ');'?>" style="background-color:<?php echo $params[$from . 'reset_icon_color']; ?>;border-radius: 0 !important;position:relative;width:30px;height:30px;background-repeat: no-repeat;background-image:url('<?php echo plugins_url("Front_images/search-reset.png", __FILE__) ?>') !important; background-size: 100% 100%; border: 0px;cursor: pointer; padding: 3px !important;">
    <?php
    echo '</div>';
  }
  echo "</div></div></form>";
  return isset($prod_name) ? $prod_name : "";
}

function html_categories($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident, $from) {
  ?>
    <script>
        function catt_idd_<?php echo $ident; ?>(id) {
            document.getElementById("subcat_id_<?php echo $cels_or_list."_".$ident; ?>").value = id;
            document.cat_form_<?php echo $cels_or_list."_".$ident; ?>.submit();
        }
    </script>
    <style>
      #productMainDiv a {
        color:<?php echo $params[$from . 'hyperlink_color']; ?>;
      }
    </style>
    <?php
    foreach ($categor as $chidd) {
        if ($par != 0 and $params1['show_category_details'] == 1 and ($cat_id != $chidd->id or (isset($_POST['cat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['cat_id_' . $cels_or_list . '_' . $ident . ''])  or (isset($_GET['cat_id_' . $cels_or_list . '_' . $ident . '']) && $_GET['cat_id_' . $cels_or_list . '_' . $ident . '']))) {
          echo '<a style="cursor:pointer;" onclick="catt_idd_' . $ident . '(' . $chidd->parent . ')" >' . __('Back to Catalog', 'sp_catalog') . '</a>';
        }
    }
    echo '<div id="productMainDiv" class="spider_catalog_style" style="width: 100%;display: inline-block;border-width:' . $params[$from . 'border_width'] . 'px;border-color:' . $params[$from . 'border_color'] . ';border-style:' . $params[$from . 'border_style'] . ';' . (($params[$from . 'text_color'] != '') ? ('color:' . $params[$from . 'text_color'] . ';') : '') . (($params[$from . 'background_color'] != '') ? ('background-color:' . $params[$from . 'background_color'] . ';') : '') . '">'; 
    if ($cat_id != 0 and $params1['show_category_details'] == 1) {
        echo '<div id="prodTitle" style="text-align: right;width:370px;' . (($params[$from . 'button_color'] != '') ? ('color:' . $params[$from . 'button_color'] . ';') : '') . (($params[$from . 'button_background_color'] != '') ? ('background-color:' . $params[$from . 'button_background_color'] . ';') : '') . 'padding:20px;font-size:' . $params[$from . 'category_title_size'] . 'px;">' . $cat_rows[0]->cat_name . '</div>';
        $imgurl = explode(";;;", $cat_rows[0]->cat_image_url);
        echo '<table id="category" border="0" cellspacing="10" cellpadding="10"><tr>';
        if ($cat_rows[0]->cat_image_url != "" and $cat_rows[0]->cat_image_url != "******0") {
          $url_for_image = explode('******', $imgurl[0]);
          echo '<td style="vertical-align:top;width:' . ($params[$from . 'category_picture_width'] + 10) . 'px;height:' . ($params[$from . 'category_picture_height'] + 10) . 'px;"><table cellpadding="0" cellspacing="5" border="0" style="margin:0px;"><tr><td colspan="2" id="prod_main_picture_container" style="vertical-align:top;"><div style="display:table-cell;text-align:center;vertical-align:middle;width:' . ($params[$from . 'category_picture_width'] + 10) . 'px;height:' . ($params[$from . 'category_picture_height'] + 10) . 'px;border: #CCCCCC solid 2px;padding:3px;background-color:white;"><a href="' . $url_for_image[0] . '" target="_blank" id="prod_main_picture_a_' . $ident . '" style="text-decoration:none;"><img id="prod_main_picture_' . $ident . '" style="max-width:' . ($params[$from . 'category_picture_width']) . 'px;max-height:' . ($params[$from . 'category_picture_height']) . 'px;" src="' . $url_for_image[0] . '" /></a></div></td></tr>';
          echo '<tr><td style="text-align:justify;">';
          $small_images_str = '';
          $small_images_count = 0;
          foreach ($imgurl as $img) {
            if ($img !== '******0') {
              $image_and_atach = explode('******', $img);
              $img = $image_and_atach[0];
              if (isset($image_and_atach[1]))
                  $atach = $image_and_atach[1];
              else
                  $atach = "";
              if ($atach) {
                  $array_with_sizes = wp_get_attachment_image_src($atach, 'thumbnail');
                  $attach_url = $array_with_sizes[0];
              } else {
                  $attach_url = $image_and_atach[0];
              }
              $small_images_str .= '<a href="' . $img . '" target="_blank"><img style="max-height:70px" src="' . $attach_url . '" vspace="0" hspace="0" onMouseOver="prod_change_picture(\'' . $img . '\',' . $ident . ',this,' . $params[$from . 'category_picture_width'] . ',' . $params[$from . 'category_picture_height'] . ');" /></a>
';
              $small_images_count++;
            }
          }
          if ($small_images_count > 1)
              echo $small_images_str;
          else
              echo '&nbsp;';
          echo '</td></tr></table></td>';
        }
        echo '<td style="vertical-align:top;word-break: break-word;line-height:1.5;">' . $cat_rows[0]->cat_description . '</td></tr></table>';
        if (count($child_ids) and $params7['show_sub'] == 1) {
          echo '<center><div id="prodTitle" style="width:190px;' . (($params[$from . 'button_color'] != '') ? ('color:' . $params[$from . 'button_color'] . ';') : '') . (($params[$from . 'button_background_color'] != '') ? ('background-color:' . $params[$from . 'button_background_color'] . ';') : '') . 'padding:10px;font-size:' . $params[$from . 'category_title_size'] . 'px;">' . __('Subcategories', 'sp_catalog') . '</div></center>';          
          echo '<table id="category" border="0" cellspacing="10" cellpadding="10"><tr style="background-color:' . $params[$from . 'categories_header_background_color'] . '; color:' . $params[$from . 'categories_header_color'] . '">';
          ?>
          <script>
            function change_subcat_<?php echo $ident; ?>(id) {
              document.getElementById("subcat_id_<?php echo $cels_or_list."_".$ident; ?>").value = id;
              document.cat_form_<?php echo $cels_or_list."_".$ident; ?>.submit();
            }
          </script>
          <?php
          echo '<td style="padding:10px;width:130px;border-right: solid 1px #FFFFFF !important;"><center>' . __('Image', 'sp_catalog') . '</center></td><td style="width:150px;border-right: solid 1px #FFFFFF !important;"><center>' . __('Name', 'sp_catalog') . '</center></td><td style="width:350px;"><center>' . __('Description', 'sp_catalog') . '</center></td></tr>';
          foreach ($child_ids as $key => $chid) {
            $imgurl = explode(";;;", $chid->category_image_url);
            if ($key % 2 == 0) {
                $backgurl = plugins_url("images/stverlist.png", __FILE__);
                $backgurl2 = '';
                $backcolor = $params[$from . 'categories_row_color1'];
            } else {
                $backgurl = '';
                $backgurl2 = plugins_url("images/stverlist.png", __FILE__);
                $backcolor = $params[$from . 'categories_row_color2'];
            }
            echo '<tr style="background-color:' . $backcolor . ';"><td style="vertical-align:middle;text-align:center;width:150px;  background-repeat: no-repeat;background-image: url(' . $backgurl . '); background-size: 100% 5%;" vertical-align: middle;">';
            if (!($chid->category_image_url != "" and $chid->category_image_url != "******0"))
                echo '<a style="cursor:pointer;" onclick="change_subcat_' . $ident . '(' . $chid->id . ')"><img style="max-width:' . $params[$from . 'list_picture_width'] . 'px; max-height:' . $params[$from . 'list_picture_height'] . 'px" src="' . plugins_url("Front_images/noimage.jpg", __FILE__) . '"  vspace="0" hspace="0"  /></a>';
            else {
                $askofen = explode('******', $imgurl[0]);
                if (isset($askofen[1]) && $askofen[1]) {
                    $array_with_sizes = wp_get_attachment_image_src($askofen[1], 'thumbnail');
                    $attach_url = $array_with_sizes[0];
                } else {
                    $attach_url = $askofen[0];
                }
                $imgurl[0] = $askofen[0];
                echo '<a href="' . $imgurl[0] . '" target="_blank" ><img src="' . $attach_url . '" vspace="0" hspace="0" style="max-width:' . $params[$from . 'list_picture_width'] . 'px; max-height:' . $params[$from . 'list_picture_height'] . 'px" /></a>';
            }
            echo '</td><td style="width:160px; vertical-align: middle;">';
            echo '<div>' . '<center><a style="cursor:pointer; ' . (($params[$from . 'hyperlink_color'] != '') ? ('color:' . $params[$from . 'hyperlink_color'] . ';') : '') . '; font-size:inherit;" onclick="change_subcat_' . $ident . '(' . $chid->id . ')">' . esc_html(stripslashes($chid->name)) . '</a></center></div><br />';
            echo '</td><td style="width:355px; background-repeat: no-repeat;background-image: url(' . $backgurl2 . '); background-size: 100% 5%;word-break: break-word;"><div>' . $chid->description . ' </div></td></tr>';
          }
          echo '</table>';
        }
    }
}

?>