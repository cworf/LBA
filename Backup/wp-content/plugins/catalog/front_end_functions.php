<?php


function catal_secure_for_scripts($key)
{
    if (!isset($_POST[$key])) {
        return '';
    }
    $_POST[$key] = htmlspecialchars(stripslashes($_POST[$key]));
    $_POST[$key] = str_ireplace("script", "blocked", $_POST[$key]);
    $_POST[$key] = mysql_escape_string($_POST[$key]);
    return $_POST[$key];
}


function front_end_single_product($id)
{
    global $wpdb;
    global $ident;
    if (!is_numeric($id))
        return 'insert numeric `id`';
    $params = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "spidercatalog_params");
    $new_param = array();
    foreach ($params as $param) {
        $new_param[$param->name] = $param->value;
    }
    $params = $new_param;

    $product_id = $id;
    if (isset($_GET['rev_page_' . $ident]) && $_GET['rev_page_' . $ident])
        $rev_page = esc_html($_GET['rev_page_' . $ident]);
    else
        $rev_page = 1;


    $query = "SELECT " . $wpdb->prefix . "spidercatalog_products.*, " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on  " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id where
	" . $wpdb->prefix . "spidercatalog_products.id='" . $product_id . "' and " . $wpdb->prefix . "spidercatalog_products.published = '1' ";
    $rows = $wpdb->get_results($query);
    if (!$rows) {
      return "Product doesn't exist";
    }

    foreach ($rows as $row) {
        $category_id = $row->category_id;
    }

    $query = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE id = %d ", $category_id);

    $row1 = $wpdb->get_row($query);
    $category_name = $row1->name;
    $full_name = catal_secure_for_scripts('full_name_' . $ident . '');
    $message_text = catal_secure_for_scripts('message_text_' . $ident . '');


    @session_start();
    if (isset($_POST['code_' . $ident]))
        $code = esc_html($_POST['code_' . $ident]);
    else
        $code = '';


    if ($code != '' and $full_name != '' and $code == $_SESSION['captcha_code']) {


        $save_or_no = $wpdb->insert($wpdb->prefix . 'spidercatalog_product_reviews', array(
                'id' => NULL,
                'name' => $full_name,
                'content' => $message_text,
                'product_id' => $product_id,
                'remote_ip' => $_SERVER['REMOTE_ADDR'],
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',

            )
        );

        if (!$save_or_no) {
            echo "<script> alert('lav cheq pahum dzez');
								window.history.go(-1); </script>\n";
            exit();
        }
    }


    $reviews_perpage = $params['reviews_perpage'];
    $query = $wpdb->prepare("SELECT name,content FROM " . $wpdb->prefix . "spidercatalog_product_reviews where product_id='$product_id' order by id desc  limit %d,%d ", (($rev_page - 1) * $reviews_perpage), $reviews_perpage);

    $reviews_rows = $wpdb->get_results($query);


    $query_count = $wpdb->prepare("SELECT count(" . $wpdb->prefix . "spidercatalog_product_reviews.id) as reviews_count FROM " . $wpdb->prefix . "spidercatalog_product_reviews  WHERE product_id=%d", $product_id);

    $row = $wpdb->get_row($query_count);
    //print_r($row);
    $reviews_count = $row->reviews_count;


    $query = $wpdb->prepare("SELECT AVG(vote_value) as rating FROM " . $wpdb->prefix . "spidercatalog_product_votes  WHERE product_id = %d", $product_id);


    $row1 = $wpdb->get_var($query);

    $rating = substr($row1, 0, 3);

    $query = $wpdb->prepare("SELECT vote_value FROM " . $wpdb->prefix . "spidercatalog_product_votes  WHERE product_id = %d and remote_ip='" . $_SERVER['REMOTE_ADDR'] . "' ", $product_id);
    $voted = count($wpdb->get_col($query));
  
    return html_front_end_single_product($rows, $reviews_rows, $params, $category_name, $rev_page, $reviews_count, $rating, $voted, $product_id, $ident);

}


//////////////////////////////////////////////////////////////////////										//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////										//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////     		 Front End Catalog			//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////										//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////										//////////////////////////////////////////////////////////////////////

function open_cat_in_tree($catt, $tree_problem = '', $hihiih = 1)
{

    global $wpdb;
    $search_tag = '';
    static $trr_cat = array();
    if ($hihiih)
        $trr_cat = array();
    foreach ($catt as $local_cat) {
        $local_cat->name = $tree_problem . $local_cat->name;
        array_push($trr_cat, $local_cat);
        $new_cat_query = "SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM " . $wpdb->prefix . "spidercatalog_product_categories  AS a LEFT JOIN " . $wpdb->prefix . "spidercatalog_product_categories AS b ON a.id = b.parent LEFT JOIN (SELECT  " . $wpdb->prefix . "spidercatalog_product_categories.ordering as ordering," . $wpdb->prefix . "spidercatalog_product_categories.id AS id, COUNT( " . $wpdb->prefix . "spidercatalog_products.category_id ) AS prod_count
FROM " . $wpdb->prefix . "spidercatalog_products, " . $wpdb->prefix . "spidercatalog_product_categories
WHERE " . $wpdb->prefix . "spidercatalog_products.category_id = " . $wpdb->prefix . "spidercatalog_product_categories.id
GROUP BY " . $wpdb->prefix . "spidercatalog_products.category_id) AS c ON c.id = a.id LEFT JOIN
(SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name AS par_name," . $wpdb->prefix . "spidercatalog_product_categories.id FROM " . $wpdb->prefix . "spidercatalog_product_categories) AS g
 ON a.parent=g.id WHERE a.name LIKE '%" . esc_html($search_tag) . "%' AND a.parent=" . $local_cat->id . " group by a.id";
        $new_cat = $wpdb->get_results($new_cat_query);
        open_cat_in_tree($new_cat, $tree_problem . "— ", 0);
    }
    return $trr_cat;

}


function get_cat_childs_ids($cat_id = 0)
{
    global $wpdb;
    $cat_ids = '';
    if (!$cat_id)
        return $cat_ids;
    else {
        $loc_ids = $wpdb->get_col("SELECT id FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE parent=" . $cat_id);

        $count_cat = count($loc_ids);
        if ($count_cat) {
            for ($i = 0; $i < $count_cat; $i++) {
                if ($cat_ids)
                    $cat_ids = $cat_ids . ',' . $loc_ids[$i] . ',' . get_cat_childs_ids($loc_ids[$i]);
                else
                    $cat_ids = $loc_ids[$i] . ',' . get_cat_childs_ids($loc_ids[$i]);
            }
            return str_replace(',,', ',', $cat_ids);
        } else
            return '';

    }
}


function remov_last_storaket($str)
{
    if (isset($str[strlen($str) - 1]))
        $last = $str[strlen($str) - 1];
    else
        $last = '';
    if ($last == ',') {
        $str = substr_replace($str, "", -1);
    }
    if (!$str)
        $str = '0';
    return $str;
}


function showPublishedProducts_1($cat_id = 1, $show_cat_det = 1, $cels_or_list = '', $show_sub = 1, $show_sub_prod = 1, $show_prod = 1)
{
    global $ident;
    global $wpdb;
    if ($cat_id == 'ALL_CAT')
        $cat_id = 0;
    $params7['show_sub'] = $show_sub;
    $params7['show_sub_prod'] = $show_sub_prod;
    $params7['show_prod'] = $show_prod;
    if (!isset($params7['show_sub'])) {
        $params7['show_sub'] = 1;
    }
    if (!isset($params7['show_sub_prod'])) {
        $params7['show_sub_prod'] = 2;
    }
    if (!isset($params7['show_prod'])) {
        $params7['show_prod'] = 1;
    }

    $params = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "spidercatalog_params");
    $new_param = array();
    foreach ($params as $param) {
        $new_param[$param->name] = $param->value;
    }
    $params = $new_param;

    switch ($cels_or_list) {
        case 'list':
            $cels_or_list = 1;
            $prod_in_page = $params['list_count_of_products_in_the_page'];
            break;
        case 'cells2':
            $cels_or_list = 2;
            $prod_in_page = $params['cells2_count_of_product_in_the_row'] * $params['cells2_count_of_rows_in_the_page'];
            break;
        case 'wcells':
            $cels_or_list = 3;
            $prod_in_page = $params['wcells_count_of_products_in_the_page'];
            break;
        case 'thumb':
            $cels_or_list = 4;
            $prod_in_page = $params['thumb_count_of_product_in_the_row'] * $params['thumb_count_of_rows_in_the_page'];
            break;
        case 'cells3':
            $cels_or_list = 5;
            $prod_in_page = $params['cells3_count_of_product_in_the_row'] * $params['cells3_count_of_rows_in_the_page'];
            break;
        case '':
            $cels_or_list = 0;
            $prod_in_page = $params['cells1_count_of_product_in_the_row'] * $params['cells1_count_of_rows_in_the_page'];
            break;
    }

    $params1['show_category_details'] = $show_cat_det;
    $params1['categories'] = $cat_id;
    if (isset($_GET['page_num_' . $cels_or_list . '_' . $ident . '']))
        $page_num = $_GET['page_num_' . $cels_or_list . '_' . $ident . ''];
    else
        $page_num = 1;
    if (isset($_POST['cat_id_' . $cels_or_list . '_' . $ident . ''])) {
        if ($_POST['cat_id_' . $cels_or_list . '_' . $ident . ''] != 0) {
            $cat_id = (int)$_POST['cat_id_' . $cels_or_list . '_' . $ident . ''];
        } else {
            $cat_id = 0;
        }
    } else if (isset($_GET['cat_id_' . $cels_or_list . '_' . $ident . ''])) {
        if ($_GET['cat_id_' . $cels_or_list . '_' . $ident . ''] != 0) {
            $cat_id = (int)$_GET['cat_id_' . $cels_or_list . '_' . $ident . ''];
        } else {
            $cat_id = 0;
        }
    }  
    if (isset ($_POST['subcat_id_' . $cels_or_list . '_' . $ident . '']) && $_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''] != "") {
        $subcat_id = (int)$_POST['subcat_id_' . $cels_or_list . '_' . $ident . ''];
    } else {

        $subcat_id = $cat_id;
    }

    $categ_query = "SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE parent=" . $subcat_id . " AND `published`=1 ORDER BY `ordering` ASC  ";

    $child_ids = $wpdb->get_results($categ_query);

    if (isset($_POST['prod_name_' . $cels_or_list . '_' . $ident . ''])) {
        if ($_POST['prod_name_' . $cels_or_list . '_' . $ident . ''] != '' && $_POST['prod_name_' . $cels_or_list . '_' . $ident . ''] != __('Search...', 'sp_catalog'))
            $prod_name = esc_html(stripslashes($_POST['prod_name_' . $cels_or_list . '_' . $ident . '']));
        else
            $prod_name = '';
    } else if (isset($_GET['prod_name_' . $cels_or_list . '_' . $ident . '']) && $_GET['prod_name_' . $cels_or_list . '_' . $ident . ''] != '' && $_GET['prod_name_' . $cels_or_list . '_' . $ident . ''] != __('Search...', 'sp_catalog'))
        $prod_name = esc_html(stripslashes($_GET['prod_name_' . $cels_or_list . '_' . $ident . '']));
    else 
        $prod_name = '';
    /*if ($cat_id > 0) {
      $query_count = $wpdb->prepare("SELECT count(" . $wpdb->prefix . "spidercatalog_products.id) as prod_count FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1'  and " . $wpdb->prefix . "spidercatalog_products.category_id= %d", $subcat_id);
      
      if ($params7['show_sub_prod'] == 2) {
          $query = $wpdb->prepare("SELECT " . $wpdb->prefix . "spidercatalog_products.*, " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1'  and (" . $wpdb->prefix . "spidercatalog_products.category_id=%d  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($subcat_id)) . ") AND " . $wpdb->prefix . "spidercatalog_products.published_in_parent = '1')) ", $subcat_id);
      } else if ($params7['show_sub_prod'] == 1) {
          $query = $wpdb->prepare("SELECT " . $wpdb->prefix . "spidercatalog_products.*, " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1'  and (" . $wpdb->prefix . "spidercatalog_products.category_id=%d  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($subcat_id)) . "))) ", $subcat_id);
      } else {
          $query = $wpdb->prepare("SELECT " . $wpdb->prefix . "spidercatalog_products.*, " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1'  and " . $wpdb->prefix . "spidercatalog_products.category_id=%d", $subcat_id);
      }
      
      $cat_query = $wpdb->prepare("SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE published = '1' and id= %d", $subcat_id);
    } 
    else {
      $query_count = "SELECT count(" . $wpdb->prefix . "spidercatalog_products.id) as prod_count FROM " . $wpdb->prefix . "spidercatalog_products  WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1' ";

      $query = "SELECT  " . $wpdb->prefix . "spidercatalog_products.*, " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_products left join " . $wpdb->prefix . "spidercatalog_product_categories on " . $wpdb->prefix . "spidercatalog_products.category_id=" . $wpdb->prefix . "spidercatalog_product_categories.id WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1' ";

      $cat_query = "SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE published = '1' ";

      if ($cat_id != 0) {
          if (is_numeric($cat_id) and $params7['show_sub_prod'] == 2) {
              $query_count .= " and (" . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($cat_id)) . ") AND " . $wpdb->prefix . "spidercatalog_products.published_in_parent = '1'))";
              $query .= " and (" . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($cat_id)) . ") AND " . $wpdb->prefix . "spidercatalog_products.published_in_parent = '1'))";
          } 
          else if ($params7['show_sub_prod'] == 1) {
              $query_count .= " and (" . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($cat_id)) . ")))";
              $query .= " and (" . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'  OR ( " . $wpdb->prefix . "spidercatalog_products.category_id IN (" . remov_last_storaket(get_cat_childs_ids($cat_id)) . ")))";
          } 
          else {
              $query_count .= " and " . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'";
              $query .= " and " . $wpdb->prefix . "spidercatalog_products.category_id='" . $cat_id . "'";
          }
      }
    }*/
    
    if ($cat_id > 0) {            
      $words = remov_last_storaket(get_cat_childs_ids($subcat_id));
      $words = explode(',', $words);
      foreach($words as $word){
          $sql[] = " (CONCAT(',', " . $wpdb->prefix . "spidercatalog_products.category_id) LIKE CONCAT('%,', '" . $word . "', ',%'))";
      }
      
      if ($params7['show_sub_prod'] == 2) {
          $query = "SELECT " . $wpdb->prefix . "spidercatalog_products.* FROM " . $wpdb->prefix . "spidercatalog_products WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1' and ((CONCAT(',', " . $wpdb->prefix . "spidercatalog_products.category_id)LIKE CONCAT('%,', '" . $subcat_id . "', ',%')) OR ((" . implode(" OR ", $sql) . ") AND " . $wpdb->prefix . "spidercatalog_products.published_in_parent = '1')) ";
      } else if ($params7['show_sub_prod'] == 1) {
          $query = "SELECT " . $wpdb->prefix . "spidercatalog_products.* FROM " . $wpdb->prefix . "spidercatalog_products WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1' AND ((CONCAT(',', " . $wpdb->prefix . "spidercatalog_products.category_id)LIKE CONCAT('%,', '" . $subcat_id . "', ',%')) OR (" . implode(" OR ", $sql) . ")) ";
      } else {
          $query = "SELECT " . $wpdb->prefix . "spidercatalog_products.* FROM " . $wpdb->prefix . "spidercatalog_products WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1'  and CONCAT(',', " . $wpdb->prefix . "spidercatalog_products.category_id)LIKE CONCAT('%,', '" . $subcat_id . "', ',%')";
      }
      
      $cat_query = $wpdb->prepare("SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE published = '1' and id= %d", $subcat_id);
    } 
    else {      
      $query = "SELECT  " . $wpdb->prefix . "spidercatalog_products.* FROM " . $wpdb->prefix . "spidercatalog_products WHERE " . $wpdb->prefix . "spidercatalog_products.published = '1' ";

      $cat_query = "SELECT " . $wpdb->prefix . "spidercatalog_product_categories.name as cat_name," . $wpdb->prefix . "spidercatalog_product_categories.category_image_url as cat_image_url," . $wpdb->prefix . "spidercatalog_product_categories.description as cat_description FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE published = '1' ";
    }

    if ($prod_name != "") {
        //$query_count .= " and (" . $wpdb->prefix . "spidercatalog_products.name like %s or " . $wpdb->prefix . "spidercatalog_products.description like %s )  ";
        $prod_name = explode(' ', $prod_name);
        foreach ($prod_name as $pr_name) {
          $query .= " and (" . $wpdb->prefix . "spidercatalog_products.name like '%" . $pr_name . "%' or " . $wpdb->prefix . "spidercatalog_products.description like '%" . $pr_name . "%'  or " . $wpdb->prefix . "spidercatalog_products.param like '%" . $pr_name . "%')  ";
        }
    }

    /*if ($prod_name != "") {
        $query = $wpdb->prepare($query, "%" . $prod_name . "%", "%" . $prod_name . "%");
        //$query_count = $wpdb->prepare($query_count, "%" . $prod_name . "%", "%" . $prod_name . "%");
    }*/

    $rows = $wpdb->get_results($query);

    $prod_count = count($rows);
    $query .= " order by " . $wpdb->prefix . "spidercatalog_products.ordering limit " . (($page_num - 1) * $prod_in_page) . "," . $prod_in_page . "  ";
    $rows = $wpdb->get_results($query);
    $cat_rows = $wpdb->get_results($cat_query);
    $ratings = array();
    $voted = array();
    $categories = NULL;
    if ($params7['show_prod'] == 1) {
        foreach ($rows as $row) {
            $id = $row->id;
            $query = $wpdb->prepare("SELECT AVG(vote_value) as rating FROM " . $wpdb->prefix . "spidercatalog_product_votes  WHERE product_id = %d ", $id);

            $row1 = $wpdb->get_var($query);
            $ratings[$id] = substr($row1, 0, 3);
            $query = $wpdb->prepare("SELECT vote_value FROM " . $wpdb->prefix . "spidercatalog_product_votes  WHERE product_id = %d and remote_ip='" . $_SERVER['REMOTE_ADDR'] . "' ", $id);


            $num_rows = $wpdb->get_var($query);
            $voted[$id] = $num_rows;

            $query = "SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE CONCAT(',', '" . $row->category_id . "') LIKE CONCAT('%,', id, ',%')";
            $row2 = $wpdb->get_results($query);
            if ($row2)
              foreach($row2 as $rr)
                $categories[$rr->id] = $rr->name;
            else
                $categories[0] = '';
        }
    }
    $par = 0;
    $query = "SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE parent=0 AND `published`=1 ORDER BY `ordering` ASC  ";
    $category_list = $wpdb->get_results($query);
    $cat_query = "SELECT * FROM " . $wpdb->prefix . "spidercatalog_product_categories WHERE `published`=1 AND id=" . $subcat_id . "";
    $categor = $wpdb->get_results($cat_query);
    foreach ($categor as $chid) {
        $par = $chid->parent;
    }


    if ($params7['show_sub'] == 1) {
        $category_list = open_cat_in_tree($category_list);
    }
    
    $rows3=array();
    $count_of_cat=count($category_list);
    $ii = 0;
    for($k = 0; $k < $count_of_cat; $k++){
      if($category_list[$k] -> published){
        $rows3[$ii] = $category_list[$k];
        $ii++;
      }
    }
   $category_list= $rows3;

    switch ($cels_or_list) {
        case 1:
            return front_end_catalog_list($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
        case 2:
            return front_end_catalog_cells2($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
        case 3:
            return front_end_catalog_wcells($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
        case 4:
            return front_end_catalog_thumb($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
        case 5:
            return front_end_catalog_cells3($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
        case 0:
            return front_end_catalog_cells($rows, $params, $page_num, $prod_count, $prod_in_page, $ratings, $voted, $categories, $category_list, $params1, $cat_rows, $cat_id, $child_ids, $params7, $categor, $par, $cels_or_list, $ident);
    }
}


?>






