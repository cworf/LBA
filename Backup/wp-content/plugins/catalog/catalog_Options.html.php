<?php

if (function_exists('current_user_can'))
    if (!current_user_can('manage_options')) {
        die('Access Denied');
    }
if (!function_exists('current_user_can')) {
    die('Access Denied');
}


function html_showGlobal($param_values, $op_type)
{


    ?>
    <div class="updated" style="font-size: 14px; color:red !important"><p><strong>Global Options is disabled in free version. If you need this functionality, you need to buy the commercial version.</strong></p></div>
    <table width="70%">
        <tbody>
         <tr>   
<td width="100%" style="font-size:14px; font-weight:bold"><a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-4/spider-catalog-wordpress-guide-step-4-1.html" target="_blank" style="color:blue; text-decoration:none;">User Manual</a><br />
This section allows you to configure the global options. <a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-4/spider-catalog-wordpress-guide-step-4-1.html" target="_blank" style="color:blue; text-decoration:none;">More...</a><br />
If you want to customize to the global options of your website,than you need to buy a license</td>   
<td colspan="7" align="right" style="font-size:16px;">
  <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
<img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
</a>
  </td>
        </tr>
        <tr>
            <td width="100%"><h2>Spider Catalog - Global Options</h2></td>
            <td align="right"></td>
        </tr>
        </tbody>
    </table>
    <br />
<img src="<?php echo plugins_url("images/WPCatalog-global.png",__FILE__) ?>" >

<?php
}


function      html_showStyles($param_values, $op_type)
{


    ?>
<div class="updated" style="font-size: 14px; color:red !important"><p><strong>Styles and Colors is disabled in free version. If you need this functionality, you need to buy the commercial version.</strong></p></div>
    <table width="70%">
        <tbody>
         <tr>   
<td width="100%" style="font-size:14px; font-weight:bold"><a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-4/spider-catalog-wordpress-guide-step-4-1.html" target="_blank" style="color:blue; text-decoration:none;">User Manual</a><br />
This section allows you to configure the Styles and Colors. <a href="http://web-dorado.com/spider-catalog-wordpress-guide-step-4/spider-catalog-wordpress-guide-step-4-1.html" target="_blank" style="color:blue; text-decoration:none;">More...</a><br />
If you want to customize to the styles and colors of your website,than you need to buy a license</td>   
<td colspan="7" align="right" style="font-size:16px;">
  <a href="http://web-dorado.com/files/fromSpiderCatalog.php" target="_blank" style="color:red; text-decoration:none;">
<img src="<?php echo plugins_url("images/header.png",__FILE__) ?>" border="0" alt="http://web-dorado.com/files/fromSpiderCatalog.php" width="215"><br>
Get the full version&nbsp;&nbsp;&nbsp;&nbsp;
</a>
  </td>
        </tr>
        <tr>
            <td width="100%"><h2>Spider Catalog - Styles and Colors</h2></td>
            <td align="right"></td>
        </tr>
        </tbody>
    </table>

    <br/>

    <form action="admin.php?page=Options_Catalog_styles" method="post" id="adminForm" name="adminForm">
    <style>
      .theme_type {
        background-color: #F4F4F4;
        border: 1px solid #8F8D8D;
        border-radius: 8px 8px 8px 8px;
        cursor: pointer;
        display: inline-block;
        font-size: 12px;
        height: 23px;
        padding-top: 6px;
        margin: 3px;
        text-align: center;
        vertical-align: middle;
        width: 150px;
    }
    </style>
    <?php $path_site = plugins_url("Front_images", __FILE__); ?>
    <table width="70%" style="min-width:550px; " class="paramlist admintable" cellspacing="10">
      <tr>
        <td>
          <div>
            <!--<div id="div_1" class="theme_type" onclick="change_type(1)"> Global Styles and Colors</div>-->
            <div id="div_2" class="theme_type" onclick="change_type(2)"> Cells 1 Page Options</div>
            <div id="div_3" class="theme_type" onclick="change_type(3)"> List Page Options</div>
            <div id="div_5" class="theme_type" onclick="change_type(5)"> Cells 2 Page Options</div>
            <div id="div_6" class="theme_type" onclick="change_type(6)"> Wide Cells Page Options</div>
            <div id="div_7" class="theme_type" onclick="change_type(7)"> Thumbnails Page Options</div>
            <div id="div_8" class="theme_type" onclick="change_type(8)"> Cells 3 Page Options</div>
            <div id="div_4" class="theme_type" onclick="change_type(4)"> Product Page Options</div>
            <input type="hidden" id="type" name="type" value="<?php echo isset($_POST['type']) ? $_POST['type'] : '1'; ?>"/>
          </div>
          <script>
            function change_type(type) {
                type = (type == '' ? 3 : type);
                document.getElementById('type').value = type;
                for ($i = 2; $i < 9; $i++) {
                    if ($i == type) {
                        document.getElementById('fieldset_' + $i).style.display = '';
                        document.getElementById('div_' + $i).style.background = '#C5C5C5';
                    }
                    else {
                        document.getElementById('fieldset_' + $i).style.display = 'none';
                        document.getElementById('div_' + $i).style.background = '#F4F4F4';
                    }
                }
            }
            window.onload = function() { change_type(<?php echo isset($_POST['type']) ? $_POST['type'] : '2'; ?>); };            
          </script>
        </td>
      </tr>
      <tr>
        <td valign="top">
          <fieldset style="border:2px groove; display: none;" id="fieldset_2">
            <legend>Cells 1 Page Options</legend>            
            <img src="<?php echo plugins_url("images/WPCatalogCells1.png",__FILE__) ?>" >
          </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_3">
          <legend>List Page Options</legend>
          <img src="<?php echo plugins_url("images/WPCatalogList.png",__FILE__) ?>" >
        </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_4"> 
          <img src="<?php echo plugins_url("images/WPCatalogSingleProduct.png",__FILE__) ?>" >          
        </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_5">
            <legend>Cells 2 Page Options</legend>
            <img src="<?php echo plugins_url("images/WPCatalogCells2.png",__FILE__) ?>" >          
        </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_6">
            <legend>Wide Cells Page Options</legend>
            <img src="<?php echo plugins_url("images/WPCatalogWideCells.png",__FILE__) ?>" >          
        </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_7">
            <legend>Thumbnails Page Options</legend>
            <img src="<?php echo plugins_url("images/WPCatalogThumbnails.png",__FILE__) ?>" >          
        </fieldset>

        <fieldset style="border:2px groove; display: none;" id="fieldset_8">
            <legend>Cells 3 Page Options</legend>
            <img src="<?php echo plugins_url("images/WPCatalogCells3.png",__FILE__) ?>" >          
        </fieldset>

        <input type="hidden" name="option" value=""/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="controller" value="options"/>
        <input type="hidden" name="op_type" value="styles"/>
        <input type="hidden" name="boxchecked" value="0"/>
        </td>
      </tr>
    </table>

    </form>

<?php
}
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  
	  