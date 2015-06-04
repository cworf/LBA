	<?php	
	if(function_exists('current_user_can'))
	if(!current_user_can('manage_options')) {
	die('Access Denied');
}	
if(!function_exists('current_user_can')){
	die('Access Denied');
}
 //////////////////////////////////////////////////////                                             /////////////////////////////////////////////////////// 
 //////////////////////////////////////////////////////      Html functions for categories          ///////////////////////////////////////////////////////
 //////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
 //////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////



















function html_showcategories( $rows,  $pageNav,$sort,$cat_row){
	global $wpdb;
	?>
    <script language="javascript">
	function ordering(name,as_or_desc)
	{
		document.getElementById('asc_or_desc').value=as_or_desc;		
		document.getElementById('order_by').value=name;
		document.getElementById('admin_form').submit();
	}
	function saveorder()
	{
		document.getElementById('saveorder').value="save";
		document.getElementById('admin_form').submit();
		
	}
	function listItemTask(this_id,replace_id)
	{
		document.getElementById('oreder_move').value=this_id+","+replace_id;
		document.getElementById('admin_form').submit();
	}
				 	function doNothing() {  
var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
    if( keyCode == 13 ) {


        if(!e) var e = window.event;

        e.cancelBubble = true;
        e.returnValue = false;

        if (e.stopPropagation) {
                e.stopPropagation();
                e.preventDefault();
        }
}
}
	</script>
    <form method="post"  onkeypress="doNothing()" action="admin.php?page=Categories_Spider_Catalog" id="admin_form" name="admin_form">
<table cellspacing="10" width="100%">
                  <tr>   
<td width="100%" style="font-size:14px; font-weight:bold"><a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">User Manual</a><br />
This section allows you to create categories of products. <a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">More...</a></td> 
<td colspan="7" align="right" style="font-size:16px;">
  <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
<img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
</a>
  </td>  
        </tr>
    <tr>
      <td style="width:80px">
        <?php echo "<h2>".'Categories'. "</h2>"; ?>
      </td>
      <td  style="width:90px; text-align:right;">
        <p class="submit" style="padding:0px; text-align:left">
          <input type="button" value="Add a Category" name="custom_parametrs" onclick="window.location.href='admin.php?page=Categories_Spider_Catalog&task=add_cat'" />
        </p>
      </td>
      <td  style="width:90px; text-align:right;">
        <p class="submit" style="padding:0px; text-align:left">
          <input type="button" value="Publish" name="custom_parametrs" onclick="document.getElementById('admin_form').action='admin.php?page=Categories_Spider_Catalog&task=publish'; document.getElementById('admin_form').submit();" />
        </p>
      </td>
      <td  style="width:90px; text-align:right;">
        <p class="submit" style="padding:0px; text-align:left">
          <input type="button" value="Unpublish" name="custom_parametrs" onclick="document.getElementById('admin_form').action='admin.php?page=Categories_Spider_Catalog&task=unpublish'; document.getElementById('admin_form').submit();" />
        </p>
      </td>
      <td  style="width:90px; text-align:right;">
        <p class="submit" style="padding:0px; text-align:left">
          <input type="button" value="Delete" name="custom_parametrs" onclick="if (confirm('Do you want to delete selected items?')) {
                                                       document.getElementById('admin_form').action='admin.php?page=Categories_Spider_Catalog&task=delete'; document.getElementById('admin_form').submit();
                                                     } else {
                                                       return false;
                                                     }" />
        </p>
      </td>
      <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px"></td>
    </tr>
    </table>
    <?php
	$serch_value='';
	if(isset($_POST['serch_or_not'])) {if($_POST['serch_or_not']=="search"){ $serch_value=esc_html(stripslashes($_POST['search_events_by_title'])); }else{$serch_value="";}} 
	$serch_fields='<div class="alignleft actions" style="width:185px;">
    	<label for="search_events_by_title" style="font-size:14px">Filter: </label>
        <input type="text" name="search_events_by_title" value="'.$serch_value.'" id="search_events_by_title" onchange="clear_serch_texts()">
    </div>
	<div class="alignleft actions">
   		<input type="button" value="Search" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
		 document.getElementById(\'admin_form\').submit();" class="button-secondary action">
		 <input type="button" value="Reset" onclick="window.location.href=\'admin.php?page=Categories_Spider_Catalog\'" class="button-secondary action">
    </div>';
	
	$serch_fields.='<select style=" text-align:left;float:right;" name="cat_search" id="cat_search" class="inputbox" onchange="this.form.submit();">
	<option value="0"';
	if(!isset($_POST['cat_search']))
    $serch_fields.='selected="selected"';
	$serch_fields.='>- Select Parent -</option>';

	foreach($cat_row as $cat_id)
	{
		
		$serch_fields.='<option value="'.$cat_id->id.'"';
		if(isset($_POST['cat_search']) || (isset($_GET["catid"]) && $_GET["catid"])) {
      if((isset($_POST['cat_search']) && $_POST['cat_search'] == $cat_id->id) || (isset($_GET["catid"]) && $_GET["catid"]==$cat_id->id)) {
        $serch_fields.='selected="selected"';		
      }
    }
		$serch_fields.='>'.esc_html(stripslashes($cat_id->name)).'</option>';
		
	}
	
	$serch_fields.='</select>';
	 print_html_nav($pageNav['total'],$pageNav['limit'],$serch_fields);
	?>
  <table class="wp-list-table widefat fixed pages" style="width:95%">
 <thead>
 <TR>
   <th scope="col" id="id" class="<?php if($sort["sortid_by"]=="id") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width:30px" ><a href="javascript:ordering('id',<?php if($sort["sortid_by"]=="id") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>ID</span><span class="sorting-indicator"></span></a></th>
   <th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;" /></th>
 <th scope="col" id="name" class="<?php if($sort["sortid_by"]=="name") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width:85px" ><a href="javascript:ordering('name',<?php if($sort["sortid_by"]=="name") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Name</span><span class="sorting-indicator"></span></a></th>
<th scope="col" id="description" class="<?php if($sort["sortid_by"]=="description") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="" ><a href="javascript:ordering('description',<?php if($sort["sortid_by"]=="description") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Description</span><span class="sorting-indicator"></span></a></th>
<th scope="col" id="count" class="<?php if($sort["sortid_by"]=="count") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width: 102px;
" ><a href="javascript:ordering('count',<?php if($sort["sortid_by"]=="count") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Subcategories</span><span class="sorting-indicator"></span></a></th>

<th scope="col" id="par_name" class="<?php if($sort["sortid_by"]=="par_name") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width: 76px;" ><a href="javascript:ordering('par_name',<?php if($sort["sortid_by"]=="par_name") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Parent</span><span class="sorting-indicator"></span></a></th>

<th scope="col" id="prod_count" class="<?php if($sort["sortid_by"]=="prod_count") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width: 75px;" ><a href="javascript:ordering('prod_count',<?php if($sort["sortid_by"]=="prod_count") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Products</span><span class="sorting-indicator"></span></a></th>

 <th scope="col" id="ordering" class="<?php if($sort["sortid_by"]=="ordering") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width:91px" ><a style="display:inline" href="javascript:ordering('ordering',<?php if($sort["sortid_by"]=="ordering") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Order</span><span class="sorting-indicator"></span></a><div><a style="display:inline" href="javascript:saveorder(1, 'saveorder')" title="Save Order"><img onclick="saveorder(1, 'saveorder')" src="<?php echo plugins_url("images/filesave.png",__FILE__) ?>" alt="Save Order"></a></div></th>
  <th scope="col" id="published"  class="<?php if($sort["sortid_by"]=="published") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>" style="width:70px" ><a href="javascript:ordering('published',<?php if($sort["sortid_by"]=="published") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Published</span><span class="sorting-indicator"></span></a></th>
 <th style="width:33px">Edit</th>
 <th style="width:40px">Delete</th>
 </TR>
 </thead>
 <tbody>
 <?php 
  for($i=0; $i<count($rows);$i++){
	  $ka0=0;
	  $ka1=0;
	  if(isset($rows[$i-1]->id))
		  {
			  if($rows[$i]->parent==$rows[$i-1]->parent){
			  $x1=$rows[$i]->id;
			  $x2=$rows[$i-1]->id;
			  $ka0=1;
			  }
			  else
			  {
				  $jj=2;
				  while(isset($rows[$i-$jj]))
				  {
					  if($rows[$i]->parent==$rows[$i-$jj]->parent)
					  {
						  $ka0=1;
						  $x1=$rows[$i]->id;
						  $x2=$rows[$i-$jj]->id;
						   break;
					  }
 					$jj++;
				  }
			  }
			  if($ka0){
		  $move_up='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''.$x2.'\')" title="Move Up">   <img src="'.plugins_url('images/uparrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Up"></a></span>';
			  }
			  else
			  $move_up="";
		  }
	  else
	  	{
			$move_up="";
	  	}
		if(isset($rows[$i+1]->id)){
			
			if($rows[$i]->parent==$rows[$i+1]->parent){
			  $x1=$rows[$i]->id;
			  $x2=$rows[$i+1]->id;
			  $ka1=1;
			  }
			  else
			  {
				  $jj=2;
				  while(isset($rows[$i+$jj]))
				  {
					  if($rows[$i]->parent==$rows[$i+$jj]->parent)
					  {
						  $ka1=1;
						  $x1=$rows[$i]->id;
						  $x2=$rows[$i+$jj]->id;
						  break;
					  }
 					$jj++;
				  }
			  }
			
			if($ka1)
  		$move_down='<span><a href="#reorder" onclick="return listItemTask(\''.$x1.'\',\''. $x2.'\')" title="Move Down">  <img src="'.plugins_url('images/downarrow.png',__FILE__).'" width="16" height="16" border="0" alt="Move Down"></a></span>';
		else
		$move_down="";	
		}
  		else
		{
  		$move_down="";	
		}
		$uncat=$rows[$i]->par_name;
		if(isset($rows[$i]->prod_count))
		$pr_count=$rows[$i]->prod_count;
		else
		$pr_count=0;


  ?>
 <tr>
         <td><?php echo $rows[$i]->id; ?></td>
         <td class="table_small_col check-column"><input id="check_<?php echo $rows[$i]->id; ?>" name="check_<?php echo $rows[$i]->id; ?>" type="checkbox" /></td>
         <td><a  href="admin.php?page=Categories_Spider_Catalog&task=edit_cat&id=<?php echo $rows[$i]->id?>"><?php echo esc_html(stripslashes($rows[$i]->name)); ?></a></td>
         <td><?php echo $rows[$i]->description; ?></td>
		 <td><a href="admin.php?page=Categories_Spider_Catalog&catid=<?php echo $rows[$i]->id; ?>" alt="Subcategories">(<?php echo $rows[$i]->count; ?>)</a></td>
		 <td><?php if(!($uncat)){echo 'Uncategory';} else{ echo $rows[$i]->par_name;}?></td>		 
		 <td><a href="admin.php?page=Products_Spider_Catalog&categoryid=<?php echo $rows[$i]->id; ?>" alt="">(<?php if(!($pr_count)){echo '0';} else{ echo $rows[$i]->prod_count;} ?>)</a></td>
         <td ><?php echo  $move_up.$move_down; ?><input type="text" name="order_<?php echo $rows[$i]->id; ?>" style="width:40px" value="<?php echo $rows[$i]->ordering; ?>" /></td>
         <td><a  href="admin.php?page=Categories_Spider_Catalog&task=unpublish_cat&id=<?php echo $rows[$i]->id?>"<?php if(!$rows[$i]->published){ ?> style="color:#C00;" <?php }?> ><?php if($rows[$i]->published)echo "Yes"; else echo "No"; ?></a></td>
         <td ><a  href="admin.php?page=Categories_Spider_Catalog&task=edit_cat&id=<?php echo $rows[$i]->id?>">Edit</a></td>
         <td><a  href="admin.php?page=Categories_Spider_Catalog&task=remove_cat&id=<?php echo $rows[$i]->id?>">Delete</a></td>
		
  </tr> 
 <?php } ?>
 </tbody>
 </table>
 <input type="hidden" name="oreder_move" id="oreder_move" value="" />
 <input type="hidden" name="asc_or_desc" id="asc_or_desc" value="<?php if(isset($_POST['asc_or_desc'])) echo $_POST['asc_or_desc'];?>"  />
 <input type="hidden" name="order_by" id="order_by" value="<?php if(isset($_POST['order_by'])) echo $_POST['order_by'];?>"  />
 <input type="hidden" name="saveorder" id="saveorder" value="" />

 <?php
?>
    
    
   
 </form>
    <?php

}

























/////////////////////////////////////////////// 







function Html_editCategory($ord_elem, $count_ord,$images,$row,$cat_row)

{
	
	
		
	
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if(!document.getElementById('name').value){
	alert("Name is required.");
	return;
	
	}
	referesh_created_tags();
	document.getElementById("adminForm").action=document.getElementById("adminForm").action+"&task="+pressbutton;
	document.getElementById("adminForm").submit();
	
}
function change_select()
{
		submitbutton('apply'); 
	
}
</script>




<table width="95%">
  <tbody>
                    <tr>   
<td width="100%" style="font-size:14px; font-weight:bold"><a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">User Manual</a><br />
This section allows you to create categories of products. <a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">More...</a></td>   
<td colspan="7" align="right" style="font-size:16px;">
  <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
<img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
</a>
  </td>
        </tr>
  <tr>
  <td width="100%"><h2>Category - <?php echo esc_html(stripslashes($row->name)) ?></h2></td>
  <td align="right"><input type="button" onclick="submitbutton('save')" value="Save" class="button-secondary action"> </td>  
  <td align="right"><input type="button" onclick="submitbutton('apply')" value="Apply" class="button-secondary action"> </td> 
  <td align="right"><input type="button" onclick="window.location.href='admin.php?page=Categories_Spider_Catalog'" value="Cancel" class="button-secondary action"> </td> 
  </tr>
  </tbody></table>
  <br />
  <br />
<form action="admin.php?page=Categories_Spider_Catalog&id=<?php echo $row->id; ?>" method="post" name="adminForm" id="adminForm">

<table class="admintable">
<tr>
<td width="100" align="right" class="key">
Name:
</td>
<td>
<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="<?php echo esc_html(stripslashes($row->name));?>" />
</td>
</tr>



<tr>
<td align="right" class="key">Parent Category:</td>
<td>
<?php
	$cat_select='<select style=" text-align:left;" name="parent" id="parent" class="inputbox"  onchange="change_select();" >
	<option value="0"';
	if(!isset($row->parent))
    $cat_select.='selected="selected"';
	$cat_select.='>Main Category</option>';
	
	foreach($cat_row as $catt)
	{
		if($row->id!=$catt->id){
		$cat_select.='<option value="'.$catt->id.'"';
		if($row->parent==$catt->id)
		$cat_select.='selected="selected"';
		
		$cat_select.='>'.esc_html(stripslashes($catt->name)).'</option>';
		}
	}
	echo $cat_select;
?>
</td>
</tr>



<tr>
<td width="100" align="right" >
Images:
</td>
<td>


<script>
var count_of_elements=<?php 

if(!$images[0] && $count_ord==1)
{
	echo 1;
}
else{
echo $count_ord+1; 
}
?>;
function add_upload(id_upload){
	
					if(id_upload>=count_of_elements)	
					{		
					count_of_elements++;
				var upload_button_texnod=document.createTextNode("Select")
	
	
				var div_element=document.createElement('div');
				div_element.setAttribute('id',"upload_div_"+count_of_elements);
				
				
				var a_element=document.createElement('a');
				a_element.setAttribute("class","button lu_upload_button");
				a_element.setAttribute("id","upload_href_"+count_of_elements);
				a_element.setAttribute("onclick","narek('"+count_of_elements+"')");
				a_element.appendChild(upload_button_texnod);
				
				
				

				var inpElement = document.createElement('input');
				inpElement.setAttribute('type','text');
				inpElement.setAttribute('style','width:200px;');
				inpElement.setAttribute('id','image_no_'+count_of_elements);
				inpElement.setAttribute('value','');
				inpElement.setAttribute('onchange','add_upload('+count_of_elements+')');
				inpElement.setAttribute('class','text_input');
				
				var btnElement = document.createElement('input');
				btnElement.setAttribute('type','button');
				btnElement.setAttribute('value','X');
				btnElement.setAttribute('onclick',"remov_upload("+count_of_elements+")");
				btnElement.setAttribute('title',"Delete");
					
					
					
					
				div_element.appendChild(inpElement);
				div_element.appendChild(a_element);
				div_element.appendChild(btnElement);
				div_element.appendChild(document.createElement('br'));
					
				document.getElementById("img__uploads").appendChild(div_element);
	

	
				document.getElementById('image_no_'+count_of_elements).focus();

					}
					create_images_tags();
	
								
}


function remov_upload(id_upload){
	if(document.getElementById('img__uploads').getElementsByTagName('div').length!=1 && id_upload!=count_of_elements)
	{
	var div = document.getElementById("upload_div_" + id_upload);
	div.parentNode.removeChild(div);
	create_images_tags();
	}
	
	
}
	
	
   var formfield=null;
   var upload_id_x_for=null;
	 window.original_send_to_editor = window.send_to_editor;
   	 window.send_to_editor = function(html){
        if (formfield) {
            var fileurl = jQuery('img',html).attr('src');
			if(!fileurl){
			var if_url_not_set;
			if_url_not_set="<a>"+html+"</a>"
			fileurl=jQuery('img',if_url_not_set).attr('src');
			}
            formfield.val(fileurl);
			add_upload(upload_id_x_for);
            tb_remove();
        } else {
            window.original_send_to_editor(html);
        }
        formfield=null;
		
    };
    
			

function narek(upload_id_x)
{
		upload_id_x_for=upload_id_x;
        formfield = jQuery("#upload_href_"+upload_id_x).parent().find(".text_input");
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        jQuery('#TB_overlay,#TB_closeWindowButton').bind("click",function(){formfield=null; upload_id_x_for=null;});
		
        return false;

	
}

function referesh_created_tags()
{
	var lists="";
	document.getElementById('uploadded_images_list').value='';
	for(i=0;i<count_of_elements;i++)
	{
		if(document.getElementById('image_no_'+i)){
		if(document.getElementById('uploadded_images_list').value)
		document.getElementById('uploadded_images_list').value=document.getElementById('uploadded_images_list').value+";;;"+document.getElementById('image_no_'+i).value;
		else
		document.getElementById('uploadded_images_list').value=document.getElementById('image_no_'+i).value;
		}
	}
}



function create_images_tags()
{
	document.getElementById("added_images").innerHTML="";
	for(i=0;i<count_of_elements;i++)
	{
		
		if(document.getElementById('image_no_'+i)){					
			var a_for_image = document.createElement('a');
			a_for_image.setAttribute('href',document.getElementById('image_no_'+i).value);
			a_for_image.setAttribute('target',"_blank");
			var img_for_image=document.createElement('img');
			img_for_image.setAttribute('src',document.getElementById('image_no_'+i).value);
			img_for_image.setAttribute('style','max-height: 50px; max-width: 50px; margin: 8px');
			a_for_image.appendChild(img_for_image);
			document.getElementById('added_images').appendChild(a_for_image);
		}
	
	}
}

</script>
<table>
<tr>
<td id="img__uploads">
<?php 
$images_with_id=$images;
$counnt_image=count($images_with_id);
for($i=0;$i<$counnt_image;$i++){
	$ffff=explode('******',$images_with_id[$i]);
	$images[$i]=$ffff[0];
}
$count_ord=count($images);

if($images[0] || $count_ord!=1)
for($i=0;$i<$count_ord;$i++){ ?>
<div id="upload_div_<?php echo $i+1; ?>">
<input type="text"  id="image_no_<?php echo $i+1; ?>" value="<?php echo stripslashes($images[$i]) ?>" onchange="add_upload('<?php echo $i+1; ?>');" class="text_input" style="width:200px;"><a id="upload_href_<?php echo $i+1; ?>" class="button lu_upload_button" onclick="narek('<?php echo $i+1; ?>')" >Select</a><input type="button" value="X" title="Delete" onclick="remov_upload('<?php echo $i+1; ?>')" /><br>
</div>

<?php }

if(!isset($images[1]))
$images[1]='';
?>

<div id="upload_div_<?php echo $i+1; ?>">
<input type="text"  id="image_no_<?php echo $i+1; ?>" value="<?php if(isset($images[$i])) echo stripslashes($images[$i]) ?>" onchange="add_upload('<?php echo $i+1; ?>');" class="text_input" style="width:200px;"><a id="upload_href_<?php echo $i+1; ?>" class="button lu_upload_button" onclick="narek('<?php echo $i+1; ?>')">Select</a><input type="button" value="X" title="Delete" onclick="remov_upload('<?php echo $i+1; ?>')" /><br>
</div>
</td>
<tr>
<td id="added_images">
<?php if($images[0] || $count_ord!=1){ ?>
<?php for($i=0;$i<$count_ord;$i++){ ?>
<a href="<?php echo $images[$i]; ?>" target="_blank"><img src="<?php echo stripslashes($images[$i]); ?>" style="max-height: 50px; max-width: 50px; margin: 8px"></a>
<?php } }?>
</td>
</tr>

</table>

<input type="hidden" name="uploadded_images_list" id="uploadded_images_list" value="<?php stripslashes($row->category_image_url); ?>" />
<td>

</tr>
<tr><td colspan="2" style="width:500px;">
</td></tr>





<tr>
<td width="100" align="right" class="key">
Description:
</td>
<td>

<div id="main_editor"><div  style=" width:600px; text-align:left" id="poststuff">
<?php if(version_compare(get_bloginfo('version'),3.3)<0) {?>
<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea"><?php the_editor(stripslashes($row->description),"content","title" ); ?>
</div>
<?php }else{?>
<?php 
wp_editor(stripslashes($row->description),"content");
}
?>
</div>
</div>

</td>
</tr>
<tr>
<tr>
<td width="100px" align="right" class="key">
Parameters:
</td>
<td>


<script type="text/javascript">

parameters0['sel1']=new Array(<?php
if(isset($row->param) && $row->param!='')
$par=explode("	",$row->param);
else
$par[0]='';
for($k=0;$k<count($par);$k++)
{
if($par[$k]!='')
echo "'".stripslashes(addslashes(str_replace('
','',htmlspecialchars($par[$k]))))."',";
}

?>'');
</script>

<div id="sel1">
<?php

$k=0;
while($k<1000)
{
if(isset($par[$k]) and $par[$k]!=''){
echo '<input type="text" style="width:200px;" id="inp_sel1_'.$k.'" value="'.stripslashes(htmlspecialchars($par[$k])).'" 

onChange="Add(\'sel1\')" /><input type="button" value="X" 

onClick="Remove('.$k.',\'sel1\');" /><br />';
$k++;
}
else{
echo '<input type="text" style="width:200px;" id="inp_sel1_'.$k.'" value="" onChange="Add(\'sel1\')" /><input 

type="button" value="X" onClick="Remove('.$k.',\'sel1\');" /><br 

/>';
$k=1000;
}
}
?>
</div><input type="hidden" name="param" id="hid_sel1" value="<?php echo stripslashes($row->param);?>" />
</td>
</tr>

<tr>
<td width="100" align="right" class="key">
Order:
</td>
<td>
<select name="ordering" >
<?php
$count_ord=count($ord_elem);
for($i=0;$i<$count_ord;$i++)
{
?>
<option value="<?php echo $ord_elem[$i]->ordering  ?>"<?php if($ord_elem[$i]->ordering==$row->ordering) echo 'selected="selected"'; ?> > <?php echo  $ord_elem[$i]->ordering." "; echo esc_html(stripslashes($ord_elem[$i]->name)); ?></option>

<?php 
}
?>
<option value="<?php echo  $ord_elem[$i-1]->ordering+1; ?>" if()><?php echo  $ord_elem[$i-1]->ordering+1; ?> Last</option>
</select>


</td>
</tr>
<tr>
<td width="100" align="right" class="key">Published:</td>
<td>
	<input type="radio" name="published" id="published0" value="0" <?php if($row->published==0) echo 'checked="checked"'; ?> class="inputbox">
	<label for="published0">No</label>
	<input type="radio" name="published" id="published1" value="1" <?php if($row->published==1) echo 'checked="checked"'; ?> class="inputbox">
	<label for="published1">Yes</label>
</td>
<?php

?>
</td>
</tr>
</table>
<input type="hidden" name="task" value="" />
</form>
<?php






	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
    
   





function html_add_category($ord_elem, $cat_row)
{
	
	
	
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	if(!document.getElementById('name').value){
	alert("Name is required.");
	return;
	
	}
	referesh_created_tags();
	document.getElementById("adminForm").action=document.getElementById("adminForm").action+"&task="+pressbutton;
	document.getElementById("adminForm").submit();
	
}
function change_select()
{
		submitbutton('apply'); 
	
}
</script>




<table width="95%">
  <tbody>
                    <tr>   
<td width="100%" style="font-size:14px; font-weight:bold"><a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">User Manual</a><br />
This section allows you to create categories of products. <a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-2.html" target="_blank" style="color:blue; text-decoration:none;">More...</a></td>  
<td colspan="7" align="right" style="font-size:16px;">
  <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
<img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
</a>
  </td> 
        </tr>
  <tr>
  <td width="100%"><h2>Add Category</h2></td>
  <td align="right"><input type="button" onclick="submitbutton('save')" value="Save" class="button-secondary action"> </td>  
  <td align="right"><input type="button" onclick="submitbutton('apply')" value="Apply" class="button-secondary action"> </td> 
  <td align="right"><input type="button" onclick="window.location.href='admin.php?page=Categories_Spider_Catalog'" value="Cancel" class="button-secondary action"> </td> 
  </tr>
  </tbody></table>
  <br />
  <br />
<form action="admin.php?page=Categories_Spider_Catalog" method="post" name="adminForm" id="adminForm">

<table class="admintable">
<tr>
<td width="100" align="right" class="key">
Name:
</td>
<td>
<input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value="" />
</td>
</tr>



<tr>
<td align="right" class="key">Parent Category:</td>
<td>
<?php
	$cat_select='<select style=" text-align:left;" name="parent" id="parent" class="inputbox" onchange="change_select()">
	<option value="0"';
	if(!isset($row->parent))
    $cat_select.='selected="selected"';
	$cat_select.='>Main Category</option>';
	foreach($cat_row as $catt)
	{
		
		$cat_select.='<option value="'.$catt->id.'"';
	
		
		$cat_select.='>'.esc_html(stripslashes($catt->name)).'</option>';
		
	}
	echo $cat_select;
?>
</td>
</tr>



<tr>
<td width="100" align="right" >
Images:
</td>
<td>


<script>
var count_of_elements=1;
function add_upload(id_upload){
	
					if(id_upload>=count_of_elements)	
					{		
					count_of_elements++;
				var upload_button_texnod=document.createTextNode("Select")
	
	
				var div_element=document.createElement('div');
				div_element.setAttribute('id',"upload_div_"+count_of_elements);
				
				
				var a_element=document.createElement('a');
				a_element.setAttribute("class","button lu_upload_button");
				a_element.setAttribute("id","upload_href_"+count_of_elements);
				a_element.setAttribute("onclick","narek('"+count_of_elements+"')");
				a_element.appendChild(upload_button_texnod);
				
				
				

				var inpElement = document.createElement('input');
				inpElement.setAttribute('type','text');
				inpElement.setAttribute('style','width:200px;');
				inpElement.setAttribute('id','image_no_'+count_of_elements);
				inpElement.setAttribute('value','');
				inpElement.setAttribute('onchange','add_upload('+count_of_elements+')');
				inpElement.setAttribute('class','text_input');
				
				var btnElement = document.createElement('input');
				btnElement.setAttribute('type','button');
				btnElement.setAttribute('value','X');
				btnElement.setAttribute('onclick',"remov_upload("+count_of_elements+")");
				btnElement.setAttribute('title',"Delete");
					
					
					
					
				div_element.appendChild(inpElement);
				div_element.appendChild(a_element);
				div_element.appendChild(btnElement);
				div_element.appendChild(document.createElement('br'));
					
				document.getElementById("img__uploads").appendChild(div_element);
	

	
				document.getElementById('image_no_'+count_of_elements).focus();

					}
					create_images_tags();
	
								
}


function remov_upload(id_upload){
	
	if(document.getElementById('img__uploads').getElementsByTagName('div').length!=1 && id_upload!=count_of_elements)
	{
	var div = document.getElementById("upload_div_" + id_upload);
	div.parentNode.removeChild(div);
		create_images_tags();
	}

	
}
	
	
   var formfield=null;
   var upload_id_x_for=null;
	 window.original_send_to_editor = window.send_to_editor;
   	 window.send_to_editor = function(html){
        if (formfield) {
            var fileurl = jQuery('img',html).attr('src');
			if(!fileurl){
			var if_url_not_set;
			if_url_not_set="<a>"+html+"</a>"
			fileurl=jQuery('img',if_url_not_set).attr('src');
			}
            formfield.val(fileurl);
			add_upload(upload_id_x_for);
            tb_remove();
        } else {
            window.original_send_to_editor(html);
        }
        formfield=null;
		
    };
    
			

function narek(upload_id_x)
{
		upload_id_x_for=upload_id_x;
        formfield = jQuery("#upload_href_"+upload_id_x).parent().find(".text_input");
        tb_show('', 'media-upload.php?type=image&TB_iframe=true');
        jQuery('#TB_overlay,#TB_closeWindowButton').bind("click",function(){formfield=null; upload_id_x_for=null;});
		
        return false;

	
}

function referesh_created_tags()
{
	var lists="";
	document.getElementById('uploadded_images_list').value='';
	for(i=0;i<count_of_elements;i++)
	{
		if(document.getElementById('image_no_'+i)){
		if(document.getElementById('uploadded_images_list').value)
		document.getElementById('uploadded_images_list').value=document.getElementById('uploadded_images_list').value+";;;"+document.getElementById('image_no_'+i).value;
		else
		document.getElementById('uploadded_images_list').value=document.getElementById('image_no_'+i).value;
		}
	}
}



function create_images_tags()
{
	document.getElementById("added_images").innerHTML="";
	for(i=0;i<count_of_elements;i++)
	{
		
		if(document.getElementById('image_no_'+i)){					
			var a_for_image = document.createElement('a');
			a_for_image.setAttribute('href',document.getElementById('image_no_'+i).value);
			a_for_image.setAttribute('target',"_blank");
			var img_for_image=document.createElement('img');
			img_for_image.setAttribute('src',document.getElementById('image_no_'+i).value);
			img_for_image.setAttribute('style','max-height: 50px; max-width: 50px; margin: 8px');
			a_for_image.appendChild(img_for_image);
			document.getElementById('added_images').appendChild(a_for_image);
		}
	
	}
}

</script>
<table>
<tr>
<td id="img__uploads">
<div id="upload_div_1">
<input type="text"  id="image_no_1" onchange="add_upload('1');" class="text_input" style="width:200px;"><a id="upload_href_1" class="button lu_upload_button" onclick="narek('1')">Select</a><input type="button" value="X" title="Delete" onclick="remov_upload('1')" /><br>
</div>
</td>
<tr>
<td id="added_images">

</td>
</tr>

</table>

<input type="hidden" name="uploadded_images_list" id="uploadded_images_list" value="" />
<td>

</tr>
<tr><td colspan="2" style="width:500px;">

</td></tr>






<tr>
<td width="100" align="right" class="key">
Description:
</td>
<td>

<div id="main_editor">

<?php if(version_compare(get_bloginfo('version'),'3.3')<0){?>
<div  style=" width:600px; text-align:left" id="poststuff">
<div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>" class="postarea"><?php the_editor

("","content","title" ); ?>
</div>
</div>
<?php }else{
	wp_editor("","content");
	}?>
</div>

</td>
</tr>



<tr>
<tr>
<td width="100" align="right" class="key">
Parameters:</td>
<td>
<?php
?>

<script type="text/javascript">

parameters0['sel1']=new Array(<?php
if(isset($row->param) && $row->param!='')
$par=explode("	",$row->param);
else
$par[0]='';
for($k=0;$k<count($par);$k++)
{
if(isset($par[$k]) and $par[$k]!='')
echo "'".addslashes(htmlspecialchars($par[$k]))."',";
}

?>'');

</script>

<div id="sel1">
<?php
$k=0;
while($k<1000)
{
if(isset($par[$k]) and $par[$k]!=''){
echo '<input type="text" style="width:200px;" id="inp_sel1_'.$k.'" value="'.htmlspecialchars($par[$k]).'" 

onChange="Add(\'sel1\')" /><input type="button" value="X" 

onClick="Remove('.$k.',\'sel1\');" /><br />';
$k++;
}
else{
echo '<input type="text" style="width:200px;" id="inp_sel1_'.$k.'" value="" onChange="Add(\'sel1\')" /><input 

type="button" value="X" onClick="Remove('.$k.',\'sel1\');" /><br 

/>';
$k=1000;
}
}
?>
</div><input type="hidden" name="param" id="hid_sel1" value="" />
</td>
</tr>

<tr>
<td width="100" align="right" class="key">
Order:
</td>
<td>
<select name="ordering" >
<?php
$count_ord=count($ord_elem);
for($i=0;$i<$count_ord;$i++)
{
?>
<option value="<?php echo $ord_elem[$i]->ordering  ?>"> <?php echo  $ord_elem[$i]->ordering." "; echo esc_html(stripslashes($ord_elem[$i]->name)); ?></option>

<?php 
}
?>
<option value="<?php if(isset($ord_elem[$i-1])) echo  $ord_elem[$i-1]->ordering+1; ?>"><?php if(isset($ord_elem[$i-1])) echo  $ord_elem[$i-1]->ordering+1; ?> Last</option>
</select>


</td>
</tr>
<tr>
<td width="100" align="right" class="key">Published:</td>
<td>
	<input type="radio" name="published" id="published0" value="0" class="inputbox">
	<label for="published0">No</label>
	<input type="radio" name="published" id="published1" value="1" checked="checked" class="inputbox">
	<label for="published1">Yes</label>
</td>
<?php

?>
</td>
</tr>
</table>

</form>
<?php








	
	
	
	
}












?>