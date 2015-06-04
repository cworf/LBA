<?php

if (function_exists('current_user_can'))
    if (!current_user_can('manage_options')) {
        die('Access Denied');
    }
if (!function_exists('current_user_can')) {
    die('Access Denied');
}
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////      Html functions for products            ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////


function html_showProducts($rows, $pageNav, $sort, $cat_row)
{


    global $wpdb;
    ?>
    <script language="javascript">
        function ordering(name, as_or_desc) {
            document.getElementById('asc_or_desc').value = as_or_desc;
            document.getElementById('order_by').value = name;
            document.getElementById('admin_form').submit();
        }
        function saveorder() {
            document.getElementById('saveorder').value = "save";
            document.getElementById('admin_form').submit();

        }
        function listItemTask(this_id, replace_id) {
            document.getElementById('oreder_move').value = this_id + "," + replace_id;
            document.getElementById('admin_form').submit();
        }
        function doNothing() {
            var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
            if (keyCode == 13) {


                if (!e) var e = window.event;

                e.cancelBubble = true;
                e.returnValue = false;

                if (e.stopPropagation) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            }
        }
    </script>




    <form method="post" onkeypress="doNothing()" action="admin.php?page=Products_Spider_Catalog" id="admin_form"
          name="admin_form" enctype="multipart/form-data">
        <table cellspacing="10" width="100%">
            <tr>
                <td width="100%" style="font-size:14px; font-weight:bold"><a
                        href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                        style="color:blue; text-decoration:none;">User Manual</a><br/>
                    This section allows you to create Products.<a
                        href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                        style="color:blue; text-decoration:none;">More...</a></td>
              <td colspan="7" align="right" style="font-size:16px;">
                <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
                  <img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
                  Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
              </td>
            </tr>
            <tr>
                <td style="width:80px">
                    <?php echo "<h2>" . 'Products' . "</h2>"; ?>
                </td>
                <td style="width:90px; text-align:right;"><p class="submit" style="padding:0px; text-align:left"><input
                            type="button" value="Add a Product" name="custom_parametrs"
                            onclick="window.location.href='admin.php?page=Products_Spider_Catalog&task=add_prad'"/></p>
                </td>
                <td  style="width:90px; text-align:right;">
                  <p class="submit" style="padding:0px; text-align:left">
                    <input type="button" value="Publish" name="custom_parametrs" onclick="document.getElementById('admin_form').action='admin.php?page=Products_Spider_Catalog&task=publish'; document.getElementById('admin_form').submit();" />
                  </p>
                </td>
                <td  style="width:90px; text-align:right;">
                  <p class="submit" style="padding:0px; text-align:left">
                    <input type="button" value="Unpublish" name="custom_parametrs" onclick="document.getElementById('admin_form').action='admin.php?page=Products_Spider_Catalog&task=unpublish'; document.getElementById('admin_form').submit();" />
                  </p>
                </td>
                <td  style="width:90px; text-align:right;">
                  <p class="submit" style="padding:0px; text-align:left">
                    <input type="button" value="Delete" name="custom_parametrs" onclick="if (confirm('Do you want to delete selected items?')) {
                                                                 document.getElementById('admin_form').action='admin.php?page=Products_Spider_Catalog&task=delete'; document.getElementById('admin_form').submit();
                                                               } else {
                                                                 return false;
                                                               }" />
                  </p>
                </td>
                <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px">

                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;">
                    <input type="button" style="margin-bottom:9px" value="Export as CSV" onclick="window.location='<?php echo admin_url('admin-ajax.php?action=catalogexportcsv'); ?>'" />
					&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                    <!--<form action="" method="post" enctype="multipart/form-data">-->
                        Import CSV:
                        <span style="position:relative;height:25px;width:auto;cursor:pointer;">
                            <input type="button" style="cursor:pointer;" value="Choose file"/>
                            <input style="width:82%;height:90%;position:absolute;top:-5px;left:0px;opacity:0;cursor:pointer;display:block;" type="file" id="csv_file" value="brrr" name="filename" size="20" />
                        </span>
                        <input type="hidden" name="uploaded" id="uploaded" value="chok" />
                        <input type="button" value="Upload" onclick="if (document.getElementById('csv_file').value=='') {
                                                                        if (document.getElementById('uploaded')) {
                                                                          document.getElementById('uploaded').value = 'chok';
                                                                        }
                                                                        alert('choose CSV');
                                                                        return false;
                                                                      }
                                                                      else {
                                                                        if (document.getElementById('uploaded')) {
                                                                          document.getElementById('uploaded').value = 'ok';
                                                                        }
                                                                        document.getElementById('admin_form').submit();
                                                                      }" />
                    <!--</form>-->
                </td><td></td>
            </tr>
        </table>
        <?php
        $serch_value = '';
        if (isset($_POST['serch_or_not'])) {
            if ($_POST['serch_or_not'] == "search") {
                $serch_value = esc_html(stripslashes($_POST['search_events_by_title']));
            } else {
                $serch_value = "";
            }
        }
        $serch_fields = '<div class="alignleft actions" style="width:1825x;">
    	<label for="search_events_by_title" style="font-size:14px">Filter: </label>
        <input type="text" name="search_events_by_title" value="' . $serch_value . '" id="search_events_by_title" onchange="clear_serch_texts()">
    </div>
	<div class="alignleft actions">
   		<input type="button" value="Search" onclick="document.getElementById(\'page_number\').value=\'1\'; document.getElementById(\'serch_or_not\').value=\'search\';
		 document.getElementById(\'admin_form\').submit();" class="button-secondary action">
		 <input type="button" value="Reset" onclick="window.location.href=\'admin.php?page=Products_Spider_Catalog\'" class="button-secondary action">
    </div>';
        $serch_fields .= '<select style=" text-align:left;float:right;" name="cat_search" id="cat_search" class="inputbox" onchange="this.form.submit();">
	<option value="0"';
        if (!isset($_POST['cat_search']))
            $serch_fields .= 'selected="selected"';
        $serch_fields .= '>- Select a Category -</option>';
        foreach ($cat_row as $catt) {

            $serch_fields .= '<option value="' . $catt->id . '"';
            if ((isset($_POST['cat_search']) && $_POST['cat_search'] == $catt->id) || (isset($_GET['categoryid']) && $_GET["categoryid"] == $catt->id))
              $serch_fields .= 'selected="selected"';
            $serch_fields .= '>' . esc_html(stripslashes($catt->name)) . '</option>';

        }

        $serch_fields .= '</select>';
        print_html_nav($pageNav['total'], $pageNav['limit'], $serch_fields);

        ?>
        <table class="wp-list-table widefat fixed pages" style="width:95%">
            <thead>
            <TR>
                <th scope="col" id="id"
                    class="<?php if ($sort["sortid_by"] == "id") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style="width:30px"><a
                        href="javascript:ordering('id',<?php if ($sort["sortid_by"] == "id") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>ID</span><span
                            class="sorting-indicator"></span></a></th>
                <th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;" /></th>
                <th scope="col" id="name"
                    class="<?php if ($sort["sortid_by"] == "name") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('name',<?php if ($sort["sortid_by"] == "name") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Name</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="cost"
                    class="<?php if ($sort["sortid_by"] == "cost") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('cost',<?php if ($sort["sortid_by"] == "cost") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Price</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="Category"
                    class="<?php if ($sort["sortid_by"] == "Category") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('Category',<?php if ($sort["sortid_by"] == "Category") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Category</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="ordering"
                    class="<?php if ($sort["sortid_by"] == "ordering") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style="width:95px"><a style="display:inline"
                                          href="javascript:ordering('ordering',<?php if ($sort["sortid_by"] == "ordering") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Order</span><span
                            class="sorting-indicator"></span></a>

                    <div><a style="display:inline" href="javascript:saveorder(1, 'saveorder')" title="Save Order"><img
                                onclick="saveorder(1, 'saveorder')"
                                src="<?php echo plugins_url("images/filesave.png", __FILE__) ?>" alt="Save Order"></a>
                    </div>
                </th>
                <th scope="col" id="published"
                    class="<?php if ($sort["sortid_by"] == "published") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style="width:100px"><a
                        href="javascript:ordering('published',<?php if ($sort["sortid_by"] == "published") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Published</span><span
                            class="sorting-indicator"></span></a></th>
                <th style="width:80px">Edit</th>
                <th style="width:80px">Delete</th>
            </TR>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($rows); $i++) {
                if (isset($rows[$i - 1]->id)) {
                    $move_up = '<span><a href="#reorder" onclick="return listItemTask(\'' . $rows[$i]->id . '\',\'' . $rows[$i - 1]->id . '\')" title="Move Up">   <img src="' . plugins_url('images/uparrow.png', __FILE__) . '" width="16" height="16" border="0" alt="Move Up"></a></span>';
                } else {
                    $move_up = "";
                }
                if (isset($rows[$i + 1]->id))
                    $move_down = '<span><a href="#reorder" onclick="return listItemTask(\'' . $rows[$i]->id . '\',\'' . $rows[$i + 1]->id . '\')" title="Move Down">  <img src="' . plugins_url('images/downarrow.png', __FILE__) . '" width="16" height="16" border="0" alt="Move Down"></a></span>';
                else
                    $move_down = "";

                ?>
                <tr>
                    <td><?php echo $rows[$i]->id; ?></td>
                    <td class="table_small_col check-column"><input id="check_<?php echo $rows[$i]->id; ?>" name="check_<?php echo $rows[$i]->id; ?>" type="checkbox" /></td>
                    <td>
                        <a href="admin.php?page=Products_Spider_Catalog&task=edit_prad&id=<?php echo $rows[$i]->id ?>"><?php echo esc_html(stripslashes($rows[$i]->name)); ?></a>
                    </td>
                    <td><?php echo $rows[$i]->cost; ?></td>
                    <td><?php echo $rows[$i]->category; ?></td>
                    <td><?php echo $move_up . $move_down; ?><input type="text" name="order_<?php echo $rows[$i]->id; ?>"
                                                                   style="width:40px"
                                                                   value="<?php echo $rows[$i]->ordering; ?>"/></td>
                    <td>
                        <a href="admin.php?page=Products_Spider_Catalog&task=unpublish_prad&id=<?php echo $rows[$i]->id ?>"<?php if (!$rows[$i]->published) { ?> style="color:#C00;" <?php } ?> ><?php if ($rows[$i]->published) echo "Yes"; else echo "No"; ?></a>
                    </td>
                    <td><a href="admin.php?page=Products_Spider_Catalog&task=edit_prad&id=<?php echo $rows[$i]->id ?>">Edit</a>
                    </td>
                    <td>
                        <a href="admin.php?page=Products_Spider_Catalog&task=remove_prod&id=<?php echo $rows[$i]->id ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <input type="hidden" name="oreder_move" id="oreder_move" value=""/>
        <input type="hidden" name="asc_or_desc" id="asc_or_desc"
               value="<?php if (isset($_POST['asc_or_desc'])) echo $_POST['asc_or_desc']; ?>"/>
        <input type="hidden" name="order_by" id="order_by"
               value="<?php if (isset($_POST['order_by'])) echo $_POST['order_by']; ?>"/>
        <input type="hidden" name="saveorder" id="saveorder" value=""/>

        <?php
        ?>


    </form>
<?php


}


function html_editProduct($row, $lists, $votes, $params, $rows1, $cat_row, $parent_cat)
{

    ?>
    <script type="text/javascript">
        function submitbutton(pressbutton) {
            if (!document.getElementById('name').value) {
                alert('Name is required.');
                return;
            }
            else if (!document.getElementById('categ_search').value) {
              alert('Category is required.');
              return;
            }
            else
                referesh_created_tags();
            document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
            document.getElementById('adminForm').submit();
        }
        function change_select() {
            option_length = document.getElementById('cat_search_select').options.length;
            values_inline = '';

            for (i = 0; i < option_length; i++) {
                if (document.getElementById('cat_search_select').options[i].selected)
                    values_inline = values_inline + document.getElementById('cat_search_select').options[i].value + ',';
            }

            document.getElementById('categ_search').value = values_inline;
        }
    </script>
    <table width="95%">
        <tbody>
        <tr>
            <td width="100%" style="font-size:14px; font-weight:bold"><a
                    href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                    style="color:blue; text-decoration:none;">User Manual</a><br/>
                This section allows you to create Products.<a
                    href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                    style="color:blue; text-decoration:none;">More...</a></td>
            <td colspan="7" align="right" style="font-size:16px;">
                <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
                  <img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
                  Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
              </td>
        </tr>
        <tr>
            <td width="100%"><h2>Product - <?php echo esc_html(stripslashes($row->name)) ?></h2></td>
            <td align="right"><input type="button" onclick="submitbutton('edit_rating')" value=" Edit Ratings "
                                     class="button-primary action"></td>
            <td align="right"><input type="button" onclick="submitbutton('edit_reviews')" value=" Edit Reviews "
                                     class="button-primary action"></td>
            <td align="right"><input type="button" onclick="submitbutton('save')" value="Save"
                                     class="button-secondary action"></td>
            <td align="right"><input type="button" onclick="submitbutton('apply')" value="Apply"
                                     class="button-secondary action"></td>
            <td align="right"><input type="button"
                                     onclick="window.location.href='admin.php?page=Products_Spider_Catalog'"
                                     value="Cancel" class="button-secondary action"></td>
        </tr>
        </tbody>
    </table>
    <br/><br/>
    <form action="admin.php?page=Products_Spider_Catalog&id=<?php echo $row->id ?>" method="post" name="adminForm"
          id="adminForm">
    <fieldset class="adminform">
    <table class="admintable">
    <tr>
        <td width="100" align="right" class="key">
            Name:
        </td>
        <td>
            <input class="text_area" type="text" name="name"
                   id="name" size="50" maxlength="250"
                   value="<?php echo esc_html(stripslashes($row->name)); ?>"/>
        </td>
    </tr>
    <tr>
        <td align="right" style="color:#F00" class="key">Category:</td>
        <td>
            <?php
            $cat_select = '<select style=" text-align:left; width:285px;" multiple name="cat_search_select" id="cat_search_select" class="inputbox" onchange="change_select();">';
            foreach ($cat_row as $catt) {

                $cat_select .= '<option value="' . $catt->id . '"';
                if (strrpos(',' . $row->category_id, ',' . $catt->id . ',') !== false) {
                    $cat_select .= ' selected="selected"';
                }

                $cat_select .= '>' . esc_html(stripslashes($catt->name)) . '</option>';
            }
            $cat_select .= '<input type="hidden" id="categ_search" name="categ_search" value=' . $row->category_id . ' />';
            echo $cat_select;
            ?>
            <input type="button" onclick="submitbutton('apply')" value="Apply Categories" class="button-secondary action">
        </td>
    </tr>


        <tr style="<?php $params['price'] ? "" : "display:none" ?>">
            <td width="100" align="right" class="key">
                Price:
            </td>
            <td>
                <input class="cost" type="text" name="cost" id="cost" size="50" maxlength="250"
                       value="<?php echo $row->cost; ?>"/>
            </td>
        </tr>

        <tr style="<?php $params['market_price'] ? "" : "display:none" ?>">
            <td width="100" align="right" class="key">
                Market Price:
            </td>
            <td>
                <input class="market_cost" type="text" name="market_cost" id="market_cost" size="50" maxlength="250"
                       value="<?php echo $row->market_cost; ?>"/>

            </td>
        </tr>

    <?php

    $count_ord = 0;
    $images_with_id = explode(";;;", $row->image_url);
    $counnt_image = count($images_with_id);
    for ($i = 0; $i < $counnt_image; $i++) {
        $ffff = explode('******', $images_with_id[$i]);
        $images[$i] = $ffff[0];
    }
    $count_ord = count($images);
    ?>
    <tr>
        <td width="100" align="right" class="key">
            Images
        </td>
        <td>
            <script>
                var count_of_elements =<?php
if(!$images[0] && $count_ord==1)
{
	echo 1;
}
else{
echo $count_ord+1; 
}

 ?>;
                function add_upload(id_upload) {

                    if (id_upload >= count_of_elements) {
                        count_of_elements++;
                        var upload_button_texnod = document.createTextNode("Select")


                        var div_element = document.createElement('div');
                        div_element.setAttribute('id', "upload_div_" + count_of_elements);


                        var a_element = document.createElement('a');
                        a_element.setAttribute("class", "button lu_upload_button");
                        a_element.setAttribute("id", "upload_href_" + count_of_elements);
                        a_element.setAttribute("onclick", "narek('" + count_of_elements + "')");
                        a_element.appendChild(upload_button_texnod);


                        var inpElement = document.createElement('input');
                        inpElement.setAttribute('type', 'text');
                        inpElement.setAttribute('style', 'width:200px;');
                        inpElement.setAttribute('id', 'image_no_' + count_of_elements);
                        inpElement.setAttribute('value', '');
                        inpElement.setAttribute('onchange', 'add_upload(' + count_of_elements + ')');
                        inpElement.setAttribute('class', 'text_input');

                        var btnElement = document.createElement('input');
                        btnElement.setAttribute('type', 'button');
                        btnElement.setAttribute('value', 'X');
                        btnElement.setAttribute('onclick', "remov_upload(" + count_of_elements + ")");
                        btnElement.setAttribute('title', "Delete");


                        div_element.appendChild(inpElement);
                        div_element.appendChild(a_element);
                        div_element.appendChild(btnElement);
                        div_element.appendChild(document.createElement('br'));

                        document.getElementById("img__uploads").appendChild(div_element);


                        document.getElementById('image_no_' + count_of_elements).focus();
                    }
                    create_images_tags();


                }


                function remov_upload(id_upload) {
                    if (document.getElementById('img__uploads').getElementsByTagName('div').length != 1 && id_upload != count_of_elements) {
                        var div = document.getElementById("upload_div_" + id_upload);
                        div.parentNode.removeChild(div);
                        create_images_tags();
                    }


                }


                var formfield = null;
                var upload_id_x_for = null;
                window.original_send_to_editor = window.send_to_editor;
                window.send_to_editor = function (html) {
                    if (formfield) {
                        var fileurl = jQuery('img', html).attr('src');
                        if (!fileurl) {
                            var if_url_not_set;
                            if_url_not_set = "<a>" + html + "</a>"
                            fileurl = jQuery('img', if_url_not_set).attr('src');
                        }
                        formfield.val(fileurl);
                        add_upload(upload_id_x_for);
                        tb_remove();
                    } else {
                        window.original_send_to_editor(html);
                    }
                    formfield = null;

                };


                function narek(upload_id_x) {
                    upload_id_x_for = upload_id_x;
                    formfield = jQuery("#upload_href_" + upload_id_x).parent().find(".text_input");
                    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                    jQuery('#TB_overlay,#TB_closeWindowButton').bind("click", function () {
                        formfield = null;
                        upload_id_x_for = null;
                    });

                    return false;


                }

                function referesh_created_tags() {
                    var lists = "";
                    document.getElementById('uploadded_images_list').value = '';
                    for (i = 0; i < count_of_elements; i++) {
                        if (document.getElementById('image_no_' + i)) {
                            if (document.getElementById('uploadded_images_list').value)
                                document.getElementById('uploadded_images_list').value = document.getElementById('uploadded_images_list').value + ";;;" + document.getElementById('image_no_' + i).value;
                            else
                                document.getElementById('uploadded_images_list').value = document.getElementById('image_no_' + i).value;
                        }
                    }
                }


                function create_images_tags() {
                    document.getElementById("added_images").innerHTML = "";
                    for (i = 0; i < count_of_elements; i++) {

                        if (document.getElementById('image_no_' + i)) {
                            var a_for_image = document.createElement('a');
                            a_for_image.setAttribute('href', document.getElementById('image_no_' + i).value);
                            a_for_image.setAttribute('target', "_blank");
                            var img_for_image = document.createElement('img');
                            img_for_image.setAttribute('src', document.getElementById('image_no_' + i).value);
                            img_for_image.setAttribute('style', 'max-height: 50px; max-width: 50px; margin: 8px');
                            a_for_image.appendChild(img_for_image);
                            document.getElementById('added_images').appendChild(a_for_image);
                        }

                    }
                }

            </script>

            <table>
                <tr>
                    <td id="img__uploads"><input type="hidden" name="uploadded_images_list" id="uploadded_images_list"
                                                 value="<?php echo stripslashes($row->image_url); ?>">
                        <?php


                        $i = 0;
                        if ($images[0] || $count_ord = !1)
                            for ($i = 0; $i < $count_ord; $i++) {
                                ?>
                                <div id="upload_div_<?php echo $i + 1; ?>">
                                    <input type="text" id="image_no_<?php echo $i + 1; ?>"
                                           value="<?php echo stripslashes($images[$i]) ?>"
                                           onchange="add_upload('<?php echo $i + 1; ?>');" class="text_input"
                                           style="width:200px;"><a id="upload_href_<?php echo $i + 1; ?>"
                                                                   class="button lu_upload_button"
                                                                   onclick="narek('<?php echo $i + 1; ?>')">Select</a><input
                                        type="button" value="X" title="Delete"
                                        onclick="remov_upload('<?php echo $i + 1; ?>')"/><br>
                                </div>

                            <?php
                            }
                        ?>

                        <div id="upload_div_<?php echo $i + 1; ?>">
                            <input type="text" id="image_no_<?php echo $i + 1; ?>"
                                   value="<?php if (isset($images[$i])) echo stripslashes($images[$i]) ?>"
                                   onchange="add_upload('<?php echo $i + 1; ?>');" class="text_input"
                                   style="width:200px;"><a id="upload_href_<?php echo $i + 1; ?>"
                                                           class="button lu_upload_button"
                                                           onclick="narek('<?php echo $i + 1; ?>')">Select</a><input
                                type="button" value="X" title="Delete" onclick="remov_upload('<?php echo $i + 1; ?>')"/><br>
                        </div>
                    </td>
                <tr>
                    <td id="added_images">


                        <?php
                        if ($images[0] || !$count_ord == 1) {
                            for ($i = 0; $i < $count_ord; $i++) {
                                ?>
                                <a href="<?php echo stripslashes($images[$i]); ?>" target="_blank"><img
                                        src="<?php echo stripslashes($images[$i]); ?>"
                                        style="max-height: 50px; max-width: 50px; margin: 8px"></a>
                            <?php
                            }
                        } ?>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width:500px;">

        </td>
    </tr>


    <!---------------------------------------------------------------------------------------->


    <?php
    $par = "";
    for ($i = 0, $n = count($rows1); $i < $n; $i++) {
      $row1 = & $rows1[$i];
      $par .= $row1->param;
    }
    if ($par) {        
        $par = explode("	", $par);
        $par = array_unique($par);
        for ($k = 0; $k < count($par); $k++) {
          if (isset($par[$k]) and $par[$k] != '') {
            echo "<tr><td width=\"100\" align=\"right\" class=\"key\">" . stripslashes(str_replace("\r\n", "", htmlspecialchars($par[$k]))) . "</td>";
            ?>
            <td>
              <div id='<?php echo "par_" . stripslashes(str_replace("\r\n", "", htmlspecialchars($par[$k]))); ?>'>
                <?php
                  $param_string = "";
                  $par_data = explode("par_", $row->param);
                  for ($j = 0; $j < count($par_data); $j++) {
                      $par1_data = explode("@@:@@", $par_data[$j]);
                      if ($par1_data[0] == $par[$k])
                          $param_string = $par1_data[1];
                  }
                  $par_values = explode("	", $param_string);
                  $t = 0;
                ?>
              <script type="text/javascript">parameters0['<?php echo "par_".str_replace("\r\n","",htmlspecialchars($par[$k])); ?>'] = new Array(<?php
              for($j=0;$j<count($par_values);$j++) 
                { 
                  if($par_values[$j]!="") {
                    echo "'".str_replace("\r\n","",htmlspecialchars($par_values[$j]))."',";
                  }
                }?>'');
              </script>
              <?php
              for ($j = 0; $j < count($par_values); $j++) {
                  if ($par_values[$j] != "") {
                    echo '<input type="text" style="width:200px;" id="inp_par_' . str_replace("\r\n", "", htmlspecialchars($par[$k])) . "_" . $j . '" value="' . str_replace("\r\n", "", htmlspecialchars($par_values[$j])) . '" onChange="Add(\'par_' . str_replace("\r\n", "", addslashes(htmlspecialchars($par[$k]))) . '\')" /><input type="button" value="X" onClick="Remove(' . $j . ',\'' . "par_" . str_replace("\r\n", "", addslashes(htmlspecialchars($par[$k]))) . '\');" /><br />';
                  } 
                  else {
                    if ($t == 0) {
                      echo '<input type="text" style="width:200px;" id="inp_par_' . str_replace("\r\n", "", htmlspecialchars($par[$k])) . "_" . $j . '" value="" 	onChange="Add(\'par_' . str_replace("\r\n", "", addslashes(htmlspecialchars($par[$k]))) . '\')" /><input type="button" value="X" onClick="Remove(' . $j . ',\'' . "par_" . str_replace("\r\n", "", addslashes(htmlspecialchars($par[$k]))) . '\');" /><br />';
                      $t++;
                    }
                  }

              }?>
          </div>
          <input type="hidden" name="param1" id="hid_<?php echo "par_" . str_replace("\r\n", "", htmlspecialchars($par[$k])); ?>"/></td></tr>
            <?php
          }
        }
    }
    ?>

    <tr>
        <td width="100" align="right" class="key">
            Description:<input type="hidden" value="<?php echo $row->param; ?>" name="param" id="all_par_hid"/>

            <script type="text/javascript">
                loadHids();
            </script>

        </td>
        <td>
            <div id="main_editor">
                <?php if (version_compare(get_bloginfo('version'), '3.3') < 0) { ?>
                    <div style=" width:600px; text-align:left" id="poststuff">
                        <div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>"
                             class="postarea"><?php the_editor(stripslashes($row->description), "content", "title"); ?>
                        </div>
                    </div>
                <?php
                } else {
                    wp_editor(stripslashes($row->description), "content");
                } ?>
            </div>
        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Order:
        </td>
        <td>
            <select name="ordering">
                <?php
                $ord_elem = $lists;
                $count_ord = count($ord_elem);
                for ($i = 0; $i < $count_ord; $i++) {
                    ?>
                    <option
                        value="<?php echo $ord_elem[$i]->ordering ?>"<?php if ($ord_elem[$i]->ordering == $row->ordering) echo 'selected="selected"'; ?> > <?php echo $ord_elem[$i]->ordering . " ";
                        echo esc_html(stripslashes($ord_elem[$i]->name)); ?></option>

                <?php
                }
                ?>
                <option
                    value="<?php echo $ord_elem[$i - 1]->ordering + 1; ?>"><?php echo $ord_elem[$i - 1]->ordering + 1; ?>
                    Last
                </option>
            </select>

        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Show in Parent:
        </td>
        <td>
            <?php
            $check0 = "";
            $check1 = "";
            if ($parent_cat == 1)
                $check1 = ' checked="checked" ';
            else
                $check0 = ' checked="checked" ';
            ?>

            <input type="radio" name="par_cat" id="par_cat0" value="0" <?php echo $check0; ?> />
            <label for="par_cat0">No</label>
            <input type="radio" name="par_cat" id="par_cat1" value="1" <?php echo $check1; ?>  />
            <label for="par_cat1">Yes</label>


        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Published:
        </td>
        <td>
            <input type="radio" name="published" id="published0"
                   value="0" <?php if ($row->published == 0) echo 'checked="checked"'; ?> class="inputbox">
            <label for="published0">No</label>
            <input type="radio" name="published" id="published1"
                   value="1" <?php if ($row->published == 1) echo 'checked="checked"'; ?> class="inputbox">
            <label for="published1">Yes</label>
        </td>
    </tr>


    </table>

    </fieldset>

    </form>
<?php


}


function html_addProduct($lists, $params, $rows1, $cat_row)
{


    ?>
    <script type="text/javascript">
        function submitbutton(pressbutton) {
            if (!document.getElementById('name').value) {
                alert('Name is required.');
                return;
            }
            else if (!document.getElementById('categ_search').value) {
              alert('Category is required.');
              return;
            }
            else
                referesh_created_tags();
            document.getElementById('adminForm').action = document.getElementById('adminForm').action + "&task=" + pressbutton;
            document.getElementById('adminForm').submit();
        }
        function change_select() {
            option_length = document.getElementById('cat_search_select').options.length;
            values_inline = '';

            for (i = 0; i < option_length; i++) {
                if (document.getElementById('cat_search_select').options[i].selected)
                    values_inline = values_inline + document.getElementById('cat_search_select').options[i].value + ',';
            }

            document.getElementById('categ_search').value = values_inline;
        }
    </script>
    <table width="95%">
        <tbody>
        <tr>
        <tr>
            <td width="100%" style="font-size:14px; font-weight:bold"><a
                    href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                    style="color:blue; text-decoration:none;">User Manual</a><br/>
                This section allows you to create Products.<a
                    href="http://web-dorado.com/spider-catalog-wordpress-guide-step-3.html" target="_blank"
                    style="color:blue; text-decoration:none;">More...</a></td>
                    <td colspan="7" align="right" style="font-size:16px;">
                <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
                  <img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
                  Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
              </td>
        </tr>
        <td width="100%"><h2>Add Product</h2></td>
        <td align="right"><input type="button" onclick="submitbutton('save')" value="Save"
                                 class="button-secondary action"></td>
        <td align="right"><input type="button" onclick="submitbutton('apply')" value="Apply"
                                 class="button-secondary action"></td>
        <td align="right"><input type="button" onclick="window.location.href='admin.php?page=Products_Spider_Catalog'"
                                 value="Cancel" class="button-secondary action"></td>
        </tr>
        </tbody>
    </table>
    <br/><br/>
    <form action="admin.php?page=Products_Spider_Catalog" method="post" name="adminForm" id="adminForm">
    <fieldset class="adminform">
    <table class="admintable">
    <tr>
        <td width="100" align="right" class="key">
            Name:
        </td>
        <td>
            <input class="text_area" type="text" name="name" id="name" size="50" maxlength="250" value=""/>
        </td>
    </tr>
    <tr>
        <td align="right" style="color:#F00" class="key">Category:</td>
        <td>
            <?php
            $cat_select = '<select style=" text-align:left; width:285px;" multiple name="cat_search_select" id="cat_search_select" class="inputbox" onchange="change_select();">';
            foreach ($cat_row as $catt) {

                $cat_select .= '<option value="' . $catt->id . '"';

                $cat_select .= '>' . esc_html(stripslashes($catt->name)) . '</option>';
            }
            $cat_select .= '<input type="hidden" id="categ_search" name="categ_search" value="" />';
            echo $cat_select;
            ?>
            <input type="button" onclick="submitbutton('apply')" value="Apply Categories" class="button-secondary action">
        </td>
    </tr>

        <tr style="<?php $params['price'] ? "" : "display:none" ?>">
            <td width="100" align="right" class="key">
                Price:
            </td>
            <td>
                <input class="cost" type="text" name="cost" id="cost" size="50" maxlength="250"
                       value=""/>
            </td>
        </tr>

        <tr style="<?php $params['market_price'] ? "" : "display:none" ?>">
            <td width="100" align="right" class="key">
                Market Price:
            </td>
            <td>
                <input class="market_cost" type="text" name="market_cost" id="market_cost" size="50" maxlength="250"
                       value=""/>

            </td>
        </tr>

    <tr>
        <td width="100" align="right" class="key">
            Images
        </td>
        <td>
            <script>
                var count_of_elements = 1;
                function add_upload(id_upload) {

                    if (id_upload >= count_of_elements) {
                        count_of_elements++;
                        var upload_button_texnod = document.createTextNode("Select")


                        var div_element = document.createElement('div');
                        div_element.setAttribute('id', "upload_div_" + count_of_elements);


                        var a_element = document.createElement('a');
                        a_element.setAttribute("class", "button lu_upload_button");
                        a_element.setAttribute("id", "upload_href_" + count_of_elements);
                        a_element.setAttribute("onclick", "narek('" + count_of_elements + "')");
                        a_element.appendChild(upload_button_texnod);


                        var inpElement = document.createElement('input');
                        inpElement.setAttribute('type', 'text');
                        inpElement.setAttribute('style', 'width:200px;');
                        inpElement.setAttribute('id', 'image_no_' + count_of_elements);
                        inpElement.setAttribute('value', '');
                        inpElement.setAttribute('onchange', 'add_upload(' + count_of_elements + ')');
                        inpElement.setAttribute('class', 'text_input');

                        var btnElement = document.createElement('input');
                        btnElement.setAttribute('type', 'button');
                        btnElement.setAttribute('value', 'X');
                        btnElement.setAttribute('onclick', "remov_upload(" + count_of_elements + ")");
                        btnElement.setAttribute('title', "Delete");


                        div_element.appendChild(inpElement);
                        div_element.appendChild(a_element);
                        div_element.appendChild(btnElement);
                        div_element.appendChild(document.createElement('br'));

                        document.getElementById("img__uploads").appendChild(div_element);


                        document.getElementById('image_no_' + count_of_elements).focus();


                    }
                    create_images_tags();


                }


                function remov_upload(id_upload) {
                    if (document.getElementById('img__uploads').getElementsByTagName('div').length != 1 && id_upload != count_of_elements) {
                        var div = document.getElementById("upload_div_" + id_upload);
                        div.parentNode.removeChild(div);
                        create_images_tags();
                    }


                }


                var formfield = null;
                var upload_id_x_for = null;
                window.original_send_to_editor = window.send_to_editor;
                window.send_to_editor = function (html) {
                    if (formfield) {
                        var fileurl = jQuery('img', html).attr('src');
                        if (!fileurl) {
                            var if_url_not_set;
                            if_url_not_set = "<a>" + html + "</a>"
                            fileurl = jQuery('img', if_url_not_set).attr('src');
                        }
                        formfield.val(fileurl);
                        add_upload(upload_id_x_for);
                        tb_remove();
                    } else {
                        window.original_send_to_editor(html);
                    }
                    formfield = null;

                };


                function narek(upload_id_x) {
                    upload_id_x_for = upload_id_x;
                    formfield = jQuery("#upload_href_" + upload_id_x).parent().find(".text_input");
                    tb_show('', 'media-upload.php?type=image&TB_iframe=true');
                    jQuery('#TB_overlay,#TB_closeWindowButton').bind("click", function () {
                        formfield = null;
                        upload_id_x_for = null;
                    });

                    return false;


                }

                function referesh_created_tags() {
                    var lists = "";
                    document.getElementById('uploadded_images_list').value = '';
                    for (i = 0; i < count_of_elements; i++) {
                        if (document.getElementById('image_no_' + i)) {
                            if (document.getElementById('uploadded_images_list').value)
                                document.getElementById('uploadded_images_list').value = document.getElementById('uploadded_images_list').value + ";;;" + document.getElementById('image_no_' + i).value;
                            else
                                document.getElementById('uploadded_images_list').value = document.getElementById('image_no_' + i).value;
                        }
                    }
                }


                function create_images_tags() {
                    document.getElementById("added_images").innerHTML = "";
                    for (i = 0; i < count_of_elements; i++) {

                        if (document.getElementById('image_no_' + i)) {
                            var a_for_image = document.createElement('a');
                            a_for_image.setAttribute('href', document.getElementById('image_no_' + i).value);
                            a_for_image.setAttribute('target', "_blank");
                            var img_for_image = document.createElement('img');
                            img_for_image.setAttribute('src', document.getElementById('image_no_' + i).value);
                            img_for_image.setAttribute('style', 'max-height: 50px; max-width: 50px; margin: 8px');
                            a_for_image.appendChild(img_for_image);
                            document.getElementById('added_images').appendChild(a_for_image);
                        }

                    }
                }

            </script>

            <table>
                <tr>
                    <td id="img__uploads"><input type="hidden" name="uploadded_images_list" id="uploadded_images_list"
                                                 value="">

                        <div id="upload_div_1">
                            <input type="text" id="image_no_1" value="" onchange="add_upload('1');" class="text_input"
                                   style="width:200px;"><a id="upload_href_1" class="button lu_upload_button"
                                                           onclick="narek('1')">Select</a><input type="button" value="X"
                                                                                                 title="Delete"
                                                                                                 onclick="remov_upload('1')"/><br>
                        </div>
                    </td>
                <tr>
                    <td id="added_images">
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="width:500px;">
        </td>
    </tr>


    <!---------------------------------------------------------------------------------------->


    <tr>
        <td width="100" align="right" class="key">
            Description:<input type="hidden" value="" name="param" id="all_par_hid"/>

            <script type="text/javascript">
                loadHids();
            </script>

        </td>
        <td>
            <div id="main_editor">
                <?php if (version_compare(get_bloginfo('version'), '3.3') < 0) { ?>
                    <div style=" width:600px; text-align:left" id="poststuff">
                        <div id="<?php echo user_can_richedit() ? 'postdivrich' : 'postdiv'; ?>"
                             class="postarea"><?php the_editor("", "content", "title"); ?>
                        </div>
                    </div>
                <?php
                } else {
                    wp_editor("", "content");

                } ?>
            </div>
        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Order:
        </td>
        <td>
            <select name="ordering">
                <?php
                $ord_elem = $lists;
                $count_ord = count($ord_elem);
                for ($i = 0; $i < $count_ord; $i++) {
                    ?>
                    <option value="<?php echo $ord_elem[$i]->ordering ?>"> <?php echo $ord_elem[$i]->ordering . " ";
                        echo esc_html(stripslashes($ord_elem[$i]->name)); ?></option>

                <?php
                }
                ?>
                <option
                    value="<?php echo $ord_elem[$i - 1]->ordering + 1; ?>"><?php echo $ord_elem[$i - 1]->ordering + 1; ?>
                    Last
                </option>
            </select>

        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Show in parents:
        </td>
        <td>


            <input type="radio" name="par_cat" id="par_cat0" value="0" checked="checked"/>
            <label for="par_cat0">No</label>
            <input type="radio" name="par_cat" id="par_cat1" value="1"/>
            <label for="par_cat1">Yes</label>


        </td>
    </tr>
    <tr>
        <td width="100" align="right" class="key">
            Published:
        </td>
        <td>
            <input type="radio" value="0" name="published" id="published0" class="inputbox">
            <label for="published0">No</label>
            <input type="radio" value="1" name="published" id="published1" checked="checked" class="inputbox">
            <label for="published1">Yes</label>
        </td>
    </tr>


    </table>

    </fieldset>
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="option" value=""/>


    <input type="hidden" name="controller" value="products"/>

    <input type="hidden" name="task"
           value=""/>
    </form>
<?php


}


//////////////////////////////////////////////////////                                            ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////      revive and reting			            ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////
//////////////////////////////////////////////////////                                             ///////////////////////////////////////////////////////


function html_spider_cat_prod_rev($rows, $pageNav, $sort, $id)
{


    global $wpdb;
    $title = $wpdb->get_var("SELECT `name` FROM " . $wpdb->prefix . "spidercatalog_products WHERE id=" . $id)
    ?>
    <script language="javascript">
        function ordering(name, as_or_desc) {
            document.getElementById('asc_or_desc').value = as_or_desc;
            document.getElementById('order_by').value = name;
            document.getElementById('admin_form').action = document.getElementById('admin_form').action + '&task=edit_reviews';
            document.getElementById('admin_form').submit();
        }
        function doNothing() {
            var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
            if (keyCode == 13) {


                if (!e) var e = window.event;

                e.cancelBubble = true;
                e.returnValue = false;

                if (e.stopPropagation) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            }
        }
        function delete_reviews() {

            document.getElementById('admin_form').action = document.getElementById('admin_form').action + '&task=delete_reviews'
            document.getElementById('admin_form').submit();

        }
    </script>
    <form method="post" action="admin.php?page=Products_Spider_Catalog&id=<?php echo $id; ?>" onkeypress="doNothing()"
          id="admin_form" name="admin_form">
        <table cellspacing="10" width="100%">
            <tr>
            </tr>
            <tr>
                <td style="width:100%">
                    <?php echo "<h2>" . 'Review - ' . $title . "</h2>"; ?>
                </td>
                <td style="width:90px; text-align:right;"><p class="submit" style="padding:0px; text-align:left"><input
                            type="button" value="Cancel" name="custom_parametrs"
                            onclick="window.location.href='admin.php?page=Products_Spider_Catalog&task=edit_prad&id=<?php echo $id ?>'"/>
                    </p></td>
                <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px">

                </td>
            </tr>
        </table>
        <?php
        print_html_nav($pageNav['total'], $pageNav['limit']);

        ?>
        <table class="wp-list-table widefat" style="width:95%">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox"></th>
                <th scope="col" id="remote_ip"
                    class="<?php if ($sort["sortid_by"] == "remote_ip") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style="width:110px"><a
                        href="javascript:ordering('remote_ip',<?php if ($sort["sortid_by"] == "id") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Remote Ip</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="name"
                    class="<?php if ($sort["sortid_by"] == "name") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('name',<?php if ($sort["sortid_by"] == "name") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Name</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="content"
                    class="<?php if ($sort["sortid_by"] == "content") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('content',<?php if ($sort["sortid_by"] == "content") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Message</span><span
                            class="sorting-indicator"></span></a></th>
                <th style="width:80px"><a href="javascript:delete_reviews()">Delete</a></th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < count($rows); $i++) { ?>
                <tr>
                    <th scope="row" class="check-column"><input type="checkbox" name="post[]"
                                                                value="<?php echo $rows[$i]->id; ?>"></th>
                    <td><?php echo $rows[$i]->remote_ip; ?></td>
                    <td><?php echo esc_html(stripslashes($rows[$i]->name)); ?></td>
                    <td><?php echo esc_html(stripslashes($rows[$i]->content)); ?></td>
                    <td>
                        <a href="admin.php?page=Products_Spider_Catalog&task=delete_review&id=<?php echo $id; ?>&del_id=<?php echo $rows[$i]->id ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <input type="hidden" name="asc_or_desc" id="asc_or_desc"
               value="<?php if (isset($_POST['asc_or_desc'])) echo $_POST['asc_or_desc']; ?>"/>
        <input type="hidden" name="order_by" id="order_by"
               value="<?php if (isset($_POST['order_by'])) echo $_POST['order_by']; ?>"/>

        <?php
        ?>


    </form>
<?php


}


///////////////////////////////////////////////////////////////////////////////////////////////


function html_spider_cat_prod_rating($rows, $pageNav, $sort, $id)
{


    global $wpdb;
    $title = $wpdb->get_var($wpdb->prepare("SELECT `name` FROM " . $wpdb->prefix . "spidercatalog_products WHERE id=%d", $id))
    ?>
    <script language="javascript">
        function ordering(name, as_or_desc) {
            document.getElementById('asc_or_desc').value = as_or_desc;
            document.getElementById('order_by').value = name;
            document.getElementById('admin_form').action = document.getElementById('admin_form').action + '&task=edit_rating';
            document.getElementById('admin_form').submit();
        }
        function doNothing() {
            var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
            if (keyCode == 13) {


                if (!e) var e = window.event;

                e.cancelBubble = true;
                e.returnValue = false;

                if (e.stopPropagation) {
                    e.stopPropagation();
                    e.preventDefault();
                }
            }
        }
        function delete_rating() {

            document.getElementById('admin_form').action = document.getElementById('admin_form').action + '&task=delete_ratings'
            document.getElementById('admin_form').submit();

        }
        function submit_button(presbutton) {
            document.getElementById('admin_form').action = document.getElementById('admin_form').action + '&task=' + presbutton;
            document.getElementById('admin_form').submit();


        }
    </script>
    <form method="post" action="admin.php?page=Products_Spider_Catalog&id=<?php echo $id; ?>" onkeypress="doNothing()"
          id="admin_form" name="admin_form">
        <table cellspacing="10" width="100%">
            <tr></tr>
            <tr>
                <td style="width:100%">
                    <?php echo "<h2>" . 'Review - ' . $title . "</h2>"; ?>
                </td>
                <td style="width:90px; text-align:right;"><p class="submit" style="padding:0px; text-align:left"><input
                            type="button" value="Save" name="custom_parametrs"
                            onclick="submit_button('s_p_save_rating')"/></p></td>
                <td style="width:90px; text-align:right;"><p class="submit" style="padding:0px; text-align:left"><input
                            type="button" value="Apply" name="custom_parametrs"
                            onclick="submit_button('s_p_apply_rating')"/></p></td>
                <td style="width:90px; text-align:right;"><p class="submit" style="padding:0px; text-align:left"><input
                            type="button" value="Cancel" name="custom_parametrs"
                            onclick="window.location.href='admin.php?page=Products_Spider_Catalog&task=edit_prad&id=<?php echo $id ?>'"/>
                    </p></td>
                <td style="text-align:right;font-size:16px;padding:20px; padding-right:50px">

                </td>
            </tr>
        </table>
        <?php
        print_html_nav($pageNav['total'], $pageNav['limit']);

        ?>
        <table class="wp-list-table widefat" style="width:95%">
            <thead>
            <tr>
                <th scope="col" id="cb" class="manage-column column-cb check-column"><input type="checkbox"></th>
                <th scope="col" id="remote_ip"
                    class="<?php if ($sort["sortid_by"] == "remote_ip") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style="width:110px"><a
                        href="javascript:ordering('remote_ip',<?php if ($sort["sortid_by"] == "remote_ip") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Remote Ip</span><span
                            class="sorting-indicator"></span></a></th>
                <th scope="col" id="vote_value"
                    class="<?php if ($sort["sortid_by"] == "vote_value") echo $sort["custom_style"]; else echo $sort["default_style"]; ?>"
                    style=""><a
                        href="javascript:ordering('vote_value',<?php if ($sort["sortid_by"] == "vote_value") echo $sort["1_or_2"]; else echo "1"; ?>)"><span>Vote Value</span><span
                            class="sorting-indicator"></span></a></th>
                <th style="width:80px"><a href="javascript:delete_rating()">Delete</a></th>
            </tr>
            </thead>
            <tbody>
            <?php for ($i = 0; $i < count($rows); $i++) { ?>
                <tr>
                    <th scope="row" class="check-column"><input type="checkbox" name="post[]"
                                                                value="<?php echo $rows[$i]->id; ?>"></th>
                    <td><?php echo $rows[$i]->remote_ip; ?></td>
                    <td><select name="vote_<?php echo $rows[$i]->id; ?>" id="vote_<?php echo $rows[$i]->id; ?>"
                                class="inputbox">
                            <option <?php if ($rows[$i]->vote_value == 1) echo 'selected="selected"'; ?>  value="1">1
                            </option>
                            <option <?php if ($rows[$i]->vote_value == 2) echo 'selected="selected"'; ?> value="2">2
                            </option>
                            <option <?php if ($rows[$i]->vote_value == 3) echo 'selected="selected"'; ?> value="3">3
                            </option>
                            <option <?php if ($rows[$i]->vote_value == 4) echo 'selected="selected"'; ?> value="4">4
                            </option>
                            <option <?php if ($rows[$i]->vote_value == 5) echo 'selected="selected"'; ?> value="5">5
                            </option>
                        </select></td>
                    <td>
                        <a href="admin.php?page=Products_Spider_Catalog&task=delete_rating&id=<?php echo $id; ?>&del_id=<?php echo $rows[$i]->id ?>">Delete</a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <input type="hidden" name="asc_or_desc" id="asc_or_desc"
               value="<?php if (isset($_POST['asc_or_desc'])) echo $_POST['asc_or_desc']; ?>"/>
        <input type="hidden" name="order_by" id="order_by"
               value="<?php if (isset($_POST['order_by'])) echo $_POST['order_by']; ?>"/>

        <?php
        ?>


    </form>
<?php


}
 
 
 
 
 
 
 
 
 
 
 
 

 