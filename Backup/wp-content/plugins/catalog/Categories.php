	<?php	
	if(function_exists('current_user_can'))
	if(!current_user_can('manage_options')) {
	die('Access Denied');
}	
if(!function_exists('current_user_can')){
	die('Access Denied');
}








 //////////////////////////////////////////////////////                                             /////////////////////////////////////////////////////// 
 //////////////////////////////////////////////////////         functions for categories            ///////////////////////////////////////////////////////
 //////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
 //////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////






/////////////////// show categories





function showCategory() 
  {
	  
	  
	  
	  
	  
  global $wpdb;
  
  if(isset($_POST['search_events_by_title']))
$_POST['search_events_by_title']=esc_html(stripslashes($_POST['search_events_by_title']));
if(isset($_POST['asc_or_desc']))
$_POST['asc_or_desc']=esc_js($_POST['asc_or_desc']);
if(isset($_POST['order_by']))
$_POST['order_by']=esc_js($_POST['order_by']);
  $where='';
  	$sort["custom_style"] ="manage-column column-autor sortable desc";
	$sort["default_style"]="manage-column column-autor sortable desc";
	$sort["sortid_by"]='id';
	$sort["1_or_2"]=1;
	$order='';
	
	if(isset($_POST['page_number']))
	{
			
			if($_POST['asc_or_desc'])
			{
				$sort["sortid_by"]=$_POST['order_by'];
				if($_POST['asc_or_desc']==1)
				{
					$sort["custom_style"]="manage-column column-title sorted asc";
					$sort["1_or_2"]="2";
					$order="ORDER BY ".$sort["sortid_by"]." ASC";
				}
				else
				{
					$sort["custom_style"]="manage-column column-title sorted desc";
					$sort["1_or_2"]="1";
					$order="ORDER BY ".$sort["sortid_by"]." DESC";
				}
			}
	if($_POST['page_number'])
		{
			$limit=($_POST['page_number']-1)*20; 
		}
		else
		{
			$limit=0;
		}
	}
	else
		{
			$limit=0;
		}
	if(isset($_POST['search_events_by_title'])){
		$search_tag=esc_html(stripslashes($_POST['search_events_by_title']));
		}
		
		else
		{
		$search_tag="";
		}
		
		
	 if(isset($_GET["catid"])){
	    $cat_id=$_GET["catid"];	
		}
       else
	   {
       if(isset($_POST['cat_search'])){
		$cat_id=$_POST['cat_search'];
		}else{
		
		$cat_id=0;}
       }

     
 if ( $search_tag ) {
		$where= " WHERE name LIKE '%".$search_tag."%' ";
	}
if($where){
	  if($cat_id){
	  $where.=" AND parent=" .$cat_id;
	  }
	
	}
	else{
	if($cat_id){
	  $where.=" WHERE parent=" .$cat_id;
	  }
	
	}
	
	
	
	
if(isset($_POST['saveorder']))
	{
		
		
		
		
		$different_par_ids=$wpdb->get_results("SELECT DISTINCT parent FROM ".$wpdb->prefix."spidercatalog_product_categories");
		foreach($different_par_ids as $different_par_id){
		if($_POST['saveorder']=="save")
		{
			
			$popoxvac_orderner=array();
			$aranc_popoxutineri_orderner=array();
			$all_products_oreder=$wpdb->get_results("SELECT `id`,`ordering` FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE  parent=".$different_par_id->parent);
			
			foreach($all_products_oreder as $products_oreder)
			{
				if(isset($_POST['order_'.$products_oreder->id]))
				{
					if($_POST['order_'.$products_oreder->id]==$products_oreder->ordering)
					$aranc_popoxutineri_orderner[$products_oreder->id]=$products_oreder->ordering;
					else
					$popoxvac_orderner[$products_oreder->id]=$_POST['order_'.$products_oreder->id];
				}
				else
				{
					$aranc_popoxutineri_orderner[$products_oreder->id]=$products_oreder->ordering;
				}
			}
			$count_of_ordered_products=count($all_products_oreder);
			$count_popoxvac=count($popoxvac_orderner);
			$count_anpopox=count($aranc_popoxutineri_orderner);
			if($count_popoxvac)
			{
			for($order_for_ordering=1;$order_for_ordering<=$count_of_ordered_products;$order_for_ordering++){
				$min_popoxvac_value=10000000;
				$min_popoxvac_id=0;
				$min_anpopox_value=10000000;
				$min_anpopox_id=0;
				foreach($popoxvac_orderner as $key=>$popoxvac_order)	{
					if($min_popoxvac_value>$popoxvac_order){
						$min_popoxvac_value=$popoxvac_order;
						$min_popoxvac_id=$key;
					}
				}

				foreach($aranc_popoxutineri_orderner as $key=>$aranc_popoxutineri_order)	{
					if($min_anpopox_value>$aranc_popoxutineri_order){
						$min_anpopox_value=$aranc_popoxutineri_order;
						$min_anpopox_id=$key;
					}
				}
				
				if($min_anpopox_value>$min_popoxvac_value)
				{
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
					'ordering'    =>$order_for_ordering,
					  ), 
					  array('id'=>$min_popoxvac_id),
					  array(  '%d' )
					  );
					  $popoxvac_orderner[$min_popoxvac_id]=1000000000000;
					  
				}
				if($min_anpopox_value==$min_popoxvac_value)
				{
					if($min_popoxvac_value<=$order_for_ordering){
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
					'ordering'    =>$order_for_ordering,
					  ), 
					  array('id'=>$min_popoxvac_id),
					  array(  '%d' )
					  );
					$popoxvac_orderner[$min_popoxvac_id]=1000000000000;
					}
					else
					{
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
					'ordering'    =>$order_for_ordering,
					  ), 
					  array('id'=>$min_anpopox_id),
					  array(  '%d' )
					  );
					  $aranc_popoxutineri_orderner[$min_anpopox_id]=1000000000000;
					}
					  
					  
				}
	
				if($min_anpopox_value<$min_popoxvac_value)
				{
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
					'ordering'    =>$order_for_ordering,
					  ), 
					  array('id'=>$min_anpopox_id),
					  array(  '%d' )
					  );
					  $aranc_popoxutineri_orderner[$min_anpopox_id]=1000000000000;
				}

				
			}
			}
			
		}
		}
		
	}
	
	

	if(isset($_POST["oreder_move"]) && $_POST["oreder_move"]!='')
	{
		$ids=explode(",",$_POST["oreder_move"]);
		$this_order=$wpdb->get_var("SELECT ordering FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=".$ids[0]);
		$next_order=$wpdb->get_var("SELECT ordering FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=".$ids[1]);	
		$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
		'ordering'    =>$next_order,
          ), 
          array('id'=>$ids[0]),
		array(  '%d' )
			  );
		$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
		'ordering'    =>$this_order,
          ), 
          array('id'=>$ids[1]),
		array(  '%d' )
			  );
			  
			  		
	}
	
	 $cat_row_query="SELECT id,name FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE parent=0";
	$cat_row=$wpdb->get_results($cat_row_query);
	
	// get the total number of records
	$query = "SELECT COUNT(*) FROM ".$wpdb->prefix."spidercatalog_product_categories". $where;
	
	$total = $wpdb->get_var($query);
	$pageNav['total'] =$total;
	$pageNav['limit'] =	 $limit/20+1;
	
	if($cat_id){
	$query ="SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."spidercatalog_product_categories  AS a LEFT JOIN ".$wpdb->prefix."spidercatalog_product_categories AS b ON a.id = b.parent LEFT JOIN (SELECT  ".$wpdb->prefix."spidercatalog_product_categories.ordering as ordering,".$wpdb->prefix."spidercatalog_product_categories.id AS id, COUNT( ".$wpdb->prefix."spidercatalog_products.category_id ) AS prod_count
FROM ".$wpdb->prefix."spidercatalog_products, ".$wpdb->prefix."spidercatalog_product_categories
WHERE ".$wpdb->prefix."spidercatalog_products.category_id = ".$wpdb->prefix."spidercatalog_product_categories.id
GROUP BY ".$wpdb->prefix."spidercatalog_products.category_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."spidercatalog_product_categories.name AS par_name,".$wpdb->prefix."spidercatalog_product_categories.id FROM ".$wpdb->prefix."spidercatalog_product_categories) AS g
 ON a.parent=g.id WHERE a.parent=$cat_id AND a.name LIKE '%".$search_tag."%' group by a.id ". $order ." "." LIMIT ".$limit.",20" ; 

	 }
	 else{
	 $query ="SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."spidercatalog_product_categories  AS a LEFT JOIN ".$wpdb->prefix."spidercatalog_product_categories AS b ON a.id = b.parent LEFT JOIN (SELECT  ".$wpdb->prefix."spidercatalog_product_categories.ordering as ordering,".$wpdb->prefix."spidercatalog_product_categories.id AS id, COUNT( ".$wpdb->prefix."spidercatalog_products.category_id ) AS prod_count
FROM ".$wpdb->prefix."spidercatalog_products, ".$wpdb->prefix."spidercatalog_product_categories
WHERE ".$wpdb->prefix."spidercatalog_products.category_id = ".$wpdb->prefix."spidercatalog_product_categories.id
GROUP BY ".$wpdb->prefix."spidercatalog_products.category_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."spidercatalog_product_categories.name AS par_name,".$wpdb->prefix."spidercatalog_product_categories.id FROM ".$wpdb->prefix."spidercatalog_product_categories) AS g
 ON a.parent=g.id WHERE a.name LIKE '%".$search_tag."%' AND a.parent=0 group by a.id ". $order ." "." LIMIT ".$limit.",20" ; 
}

$rows = $wpdb->get_results($query);
 global $glob_ordering_in_cat;
if(isset($sort["sortid_by"]))
{
	if($sort["sortid_by"]=='ordering'){
	if($_POST['asc_or_desc']==1){
		$glob_ordering_in_cat=" ORDER BY ordering ASC";
	}
	else{
		$glob_ordering_in_cat=" ORDER BY ordering DESC";
	}
	}
}
$rows=open_cat_in_tree($rows);
	$query ="SELECT  ".$wpdb->prefix."spidercatalog_product_categories.ordering,".$wpdb->prefix."spidercatalog_product_categories.id, COUNT( ".$wpdb->prefix."spidercatalog_products.category_id ) AS prod_count
FROM ".$wpdb->prefix."spidercatalog_products, ".$wpdb->prefix."spidercatalog_product_categories
WHERE ".$wpdb->prefix."spidercatalog_products.category_id = ".$wpdb->prefix."spidercatalog_product_categories.id
GROUP BY ".$wpdb->prefix."spidercatalog_products.category_id " ;
	$prod_rows = $wpdb->get_results($query);
	
	
foreach($rows as $row)
{
	foreach($prod_rows as $row_1)
	{
		if ($row->id == $row_1->id)
		{
			$row->ordering = $row_1->ordering;
		$row->prod_count = $row_1->prod_count;
	}
		}
	
	}
	
	$cat_row=open_cat_in_tree($cat_row);
		html_showcategories( $rows, $pageNav,$sort,$cat_row);
  }








//////////////////////        edit or add categories

function open_cat_in_tree($catt,$tree_problem='',$hihiih=1){

global $wpdb;
global $glob_ordering_in_cat;
static $trr_cat=array();
if(!isset($search_tag))
$search_tag='';
if($hihiih)
$trr_cat=array();
foreach($catt as $local_cat){
	$local_cat->name=$tree_problem.$local_cat->name;
	array_push($trr_cat,$local_cat);
	$new_cat_query=	"SELECT  a.* ,  COUNT(b.id) AS count, g.par_name AS par_name FROM ".$wpdb->prefix."spidercatalog_product_categories  AS a LEFT JOIN ".$wpdb->prefix."spidercatalog_product_categories AS b ON a.id = b.parent LEFT JOIN (SELECT  ".$wpdb->prefix."spidercatalog_product_categories.ordering as ordering,".$wpdb->prefix."spidercatalog_product_categories.id AS id, COUNT( ".$wpdb->prefix."spidercatalog_products.category_id ) AS prod_count
FROM ".$wpdb->prefix."spidercatalog_products, ".$wpdb->prefix."spidercatalog_product_categories
WHERE ".$wpdb->prefix."spidercatalog_products.category_id = ".$wpdb->prefix."spidercatalog_product_categories.id
GROUP BY ".$wpdb->prefix."spidercatalog_products.category_id) AS c ON c.id = a.id LEFT JOIN
(SELECT ".$wpdb->prefix."spidercatalog_product_categories.name AS par_name,".$wpdb->prefix."spidercatalog_product_categories.id FROM ".$wpdb->prefix."spidercatalog_product_categories) AS g
 ON a.parent=g.id WHERE a.name LIKE '%".$search_tag."%' AND a.parent=".$local_cat->id." group by a.id  ".$glob_ordering_in_cat; 
 $new_cat=$wpdb->get_results($new_cat_query);
 open_cat_in_tree($new_cat,$tree_problem. "— ",0);
}
return $trr_cat;

}


function editCategory($id)
  {
	  
	  global $wpdb;


	   $query=$wpdb->prepare("SELECT * FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=%d",$id);
	   $row=$wpdb->get_row($query);
	   if(!isset($row->category_image_url))
	   return 'id not found';
       $images=explode(";;;",$row->category_image_url);
	   $par=explode('	',$row->param);
	   $count_ord=count($images);
	   $cat_row=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id!=" .$id." and parent=0");
       $cat_row=open_cat_in_tree($cat_row);
	   	  $query="SELECT name,ordering FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE parent=".$row->parent."  ORDER BY `ordering` ";
	   $ord_elem=$wpdb->get_results($query);
    Html_editCategory($ord_elem, $count_ord, $images, $row, $cat_row);
  }
  
  
  
  
  
  
  
  
function add_category()
{
	global $wpdb;
	
	
	$query="SELECT name,ordering FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE parent=0 ORDER BY `ordering`";
	$ord_elem=$wpdb->get_results($query); ///////ordering elements list
	$cat_row=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."spidercatalog_product_categories where parent=0");
	$cat_row=open_cat_in_tree($cat_row);
	
	
	
	html_add_category($ord_elem, $cat_row);
	
	
	
}

function save_cat()
{
	
	 global $wpdb;
	 if(isset($_POST["ordering"])){	 
	 	$rows=$wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'spidercatalog_product_categories WHERE ordering>='.$_POST["ordering"].'   AND parent='.$_POST['parent'].' ORDER BY `ordering` ASC ');
	 }
	 else{
		 		echo "<h1>Error</h1>";
		return false;
	 }
	 
	$count_of_rows=count($rows);
	$ordering_values=array();
	$ordering_ids=array();
	for($i=0;$i<$count_of_rows;$i++)
	{		
	
		$ordering_ids[$i]=$rows[$i]->id;
		$ordering_values[$i]=$i+1+$_POST["ordering"];
	}
	for($i=0;$i<$count_of_rows;$i++){
				$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', 
			  array('ordering'    =>$ordering_values[$i]), 
              array('id'=>$ordering_ids[$i]),
			  array(  '%d' )
			  );
			
			 
	}
	   
	   
	
	 	 $images=explode(';;;',$_POST['uploadded_images_list']);
	     $kk=count($images);
    for($i=0;$i<$kk;$i++){
		 $image_with_id=get_attachment_id_from_src($images[$i]);
		 $images[$i]=$image_with_id;
	 }
	 $new_images=implode(';;;',$images);
	 $script_cat = preg_replace('#<script(.*?)>(.*?)</script>#is', '', stripslashes($_POST["content"]));
	 $save_or_no= $wpdb->insert($wpdb->prefix.'spidercatalog_product_categories', array(
		'id'	=> NULL,
		'name'   				 => esc_js($_POST["name"]),
		'parent'                 => $_POST["parent"],
        'category_image_url'     => esc_js($new_images),
        'description'			 => $script_cat,
        'param'  				 =>esc_js($_POST["param"]),		
        'ordering' 				 => $_POST["ordering"],
		'published'				 =>$_POST["published"],
                ),
				array(
				'%d',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',	
				'%d',
						
				)
                );
			$id=$wpdb->get_var("SELECT MAX( id ) FROM ".$wpdb->prefix."spidercatalog_product_categories");
			
			$query=$wpdb->prepare("SELECT parent FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=%d",$id);
	        $fff=$wpdb->get_var($query);
	        $query=$wpdb->prepare("SELECT param FROM ".$wpdb->prefix."spidercatalog_product_categories where id=%d",$fff);
		    $rows1 =$wpdb->get_row($query);	
    	
		if(isset($_POST["parent"]) && $_POST["parent"]!=0){
       foreach($rows1 as $par_cat){
        $wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
		'param'    =>$par_cat,
          ), 
          array('id'=>$id),
		  array(  '%s' )
			  );
		}
		}
				$cat_row=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id!=%d ",$id));
				
					if(!$save_or_no)
	{
		?>
	<div class="updated"><p><strong><?php _e('Error. Please install plugin again'); ?></strong></p></div>
	<?php
		return false;
	}
	

	
	
	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php
	
    return true;
	
	
}




function change_cat( $id ){
  global $wpdb;
  
  $published=$wpdb->get_var($wpdb->prepare("SELECT published FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE `id`=%d",$id) );
  if($published)
   $published=0;
  else
   $published=1;
  $savedd=$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
			'published'    =>$published,
              ), 
              array('id'=>$id),
			  array(  '%d' )
			  );
	
	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php
	
    return true;
}

function publish_all($published) {
  global $wpdb;  
  $ids = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'spidercatalog_product_categories');
  foreach ($ids as $id) {
    if (isset($_POST['check_' . $id])) {
      $wpdb->update($wpdb->prefix . 'spidercatalog_product_categories', array('published' => $published), array('id' => $id));
    }
  }	
	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php	
  return true;
}

function delete_all() {
  global $wpdb;
  $ids = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'spidercatalog_product_categories');
  foreach ($ids as $id) {
    if (isset($_POST['check_' . $id])) {
      removeCategory($id);
    }
  }
  return true;
}

function removeCategory($id)
{
	
	
	global $wpdb;
	 $sql_remov_tag=$wpdb->prepare("DELETE FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=%d",$id);
 if(!$wpdb->query($sql_remov_tag))
 {
	  ?>
	  <div id="message" class="error"><p>Category Not Deleted</p></div>
      <?php
	 
 }
 else{
 ?>
 <div class="updated"><p><strong><?php _e('Item Deleted.' ); ?></strong></p></div>
 <?php
 }
    $row=$wpdb->get_results($wpdb->prepare( 'UPDATE '.$wpdb->prefix.'spidercatalog_product_categories SET parent="0"   WHERE parent=%d',$id));
	$rows=$wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'spidercatalog_product_categories  ORDER BY `ordering` ASC ');
	
	$count_of_rows=count($rows);
	$ordering_values=array();
	$ordering_ids=array();
	for($i=0;$i<$count_of_rows;$i++)
	{		
	
		$ordering_ids[$i]=$rows[$i]->id;
		if(isset($_POST["ordering"]))
		$ordering_values[$i]=$i+1+$_POST["ordering"];
		else
		$ordering_values[$i]=$i+1;
	}

		for($i=0;$i<$count_of_rows;$i++)
	{	
			$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', 
			  array('ordering'      =>$ordering_values[$i]), 
              array('id'			=>$ordering_ids[$i]),
			  array('%s'),
			  array( '%s' )
			  );
	}
		

  

}






function apply_cat($id)
{
	
	
		 global $wpdb;
		 if(!is_numeric($id)){
			 echo 'insert numerc id';
		 	return '';
		 }
		 if(!(isset($_POST['parent']) && isset($_POST["ordering"]) && isset($_POST["name"]) && isset($_POST["content"]) && isset($_POST["param"]) && isset( $_POST["ordering"])))
		 {
			 echo 'not important values';
			 return '';
		 }
		 $cat_row=$wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id!=%d ",$_GET['id']));
		 $corent_ord=$wpdb->get_var($wpdb->prepare('SELECT `ordering` FROM '.$wpdb->prefix.'spidercatalog_product_categories WHERE id=%d AND parent=%d',$id,$_POST['parent']));
		 $max_ord=$wpdb->get_var('SELECT MAX(ordering) FROM '.$wpdb->prefix.'spidercatalog_product_categories');
		 if($corent_ord>$_POST["ordering"])
		 {
				$rows=$wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'spidercatalog_product_categories WHERE ordering>=%d AND id<>%d AND parent=%d ORDER BY `ordering` ASC ',$_POST["ordering"],$id,$_POST['parent']));
			 
			$count_of_rows=count($rows);
			$ordering_values=array();
			$ordering_ids=array();
			for($i=0;$i<$count_of_rows;$i++)
			{		
			
				$ordering_ids[$i]=$rows[$i]->id;
				$ordering_values[$i]=$i+1+$_POST["ordering"];
			}
			for($i=0;$i<$count_of_rows;$i++){
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', 
					  array('ordering'    =>$ordering_values[$i]), 
					  array('id'=>$ordering_ids[$i]),
					  array(  '%d' )
					  );
		
			}
		 }
		 if($corent_ord<$_POST["ordering"])
		 {
			 $rows=$wpdb->get_results($wpdb->prepare('SELECT * FROM '.$wpdb->prefix.'spidercatalog_product_categories WHERE ordering<=%d AND id<>%d AND parent=%d  ORDER BY `ordering` ASC ',$_POST["ordering"],$id,$_POST['parent']));
			 
			$count_of_rows=count($rows);
			$ordering_values=array();
			$ordering_ids=array();
			for($i=0;$i<$count_of_rows;$i++)
			{		
			
				$ordering_ids[$i]=$rows[$i]->id;
				$ordering_values[$i]=$i+1;
			}
			if($max_ord==$_POST["ordering"]-1)
			{
				$_POST["ordering"]--;
			}
			for($i=0;$i<$count_of_rows;$i++){
					$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', 
					  array('ordering'    =>$ordering_values[$i]), 
					  array('id'=>$ordering_ids[$i]),
					  array(  '%d' )
					  );
		
			}
		 }
		 
		 
     
            $query=$wpdb->prepare("SELECT parent FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=%d",$id);
	        $id_bef=$wpdb->get_var($query);
      
		 
	 	 $images=explode(';;;',$_POST['uploadded_images_list']);
	 $kk=count($images);
     for($i=0;$i<$kk;$i++){
		 $image_with_id=get_attachment_id_from_src($images[$i]);
		 $images[$i]=$image_with_id;
	 }
	 $new_images=implode(';;;',$images);
	$script_cat = preg_replace('#<script(.*?)>(.*?)</script>#is', '', stripslashes($_POST["content"]));
	$savedd=$wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
					'name'   				 => esc_js($_POST["name"]),
					'parent'   				 => $_POST["parent"],
					'category_image_url'     => esc_js($new_images),
					'description'			 => $script_cat,
					'param'  				 =>esc_js($_POST["param"]),
					'ordering' 				 => $_POST["ordering"],
					'published'				 =>$_POST["published"],
              ), 
              array('id'=>$id),
			  array( 
			    '%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d',	
				'%d', )
			  );
		if($_POST["parent"]!=0){
		    $query=$wpdb->prepare("SELECT parent FROM ".$wpdb->prefix."spidercatalog_product_categories WHERE id=%d",$id);
	        $fff=$wpdb->get_var($query);
	        $query=$wpdb->prepare("SELECT param FROM ".$wpdb->prefix."spidercatalog_product_categories where id=%d",$fff);
		    $rows1 =$wpdb->get_row($query);	
		    if(isset($_POST["parent"]) && $_POST["parent"]!=$id_bef){
	 
    	    foreach($rows1 as $par_cat){
             $wpdb->update($wpdb->prefix.'spidercatalog_product_categories', array(
		     'param'    =>$par_cat,
             ), 
             array('id'=>$id),
		     array(  '%s' )
			  );
		    }
		    }
		}

	?>
	<div class="updated"><p><strong><?php _e('Item Saved'); ?></strong></p></div>
	<?php
	
    return true;
	
}








?>