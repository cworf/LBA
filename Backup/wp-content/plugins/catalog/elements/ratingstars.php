<?php /** * @package Spider Catalog * @author Web-Dorado * @copyright (C) 2012 Web-Dorado. All rights reserved. * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html **/defined('_JEXEC') or die('Restricted access');

class JElementRatingstars extends JElement

{

function fetchElement($name, $value, &$node, $control_name)

{

        ob_start();
		
        $img=JUri::root()."components/com_spidershop/images/";

            ?>

   
<table cellpadding="0" cellspacing="10" border="0" ><tr>
<td>
<img src="<?php echo $img;?>star1.png" alt="star1" /><br />
<input name="<?php echo $control_name."[".$name."]";?>" type="radio" class="inputbox" id="<?php echo  $control_name.$name ?>" value="1" size="10" <?php if($value==1) echo 'checked="checked"'; ?> />
</td><td>
<img src="<?php echo $img;?>star2.png" alt="star2" /><br />
<input name="<?php echo $control_name."[".$name."]";?>" type="radio" class="inputbox" id="<?php echo  $control_name.$name ?>" value="2" size="10" <?php if($value==2) echo 'checked="checked"'; ?> />
</td><td>
<img src="<?php echo $img;?>star3.png" alt="star3" /><br />
<input name="<?php echo $control_name."[".$name."]";?>" type="radio" class="inputbox" id="<?php echo  $control_name.$name ?>" value="3" size="10" <?php if($value==3) echo 'checked="checked"'; ?> />
</td><td>
<img src="<?php echo $img;?>star4.png" alt="star4" /><br />
<input name="<?php echo $control_name."[".$name."]";?>" type="radio" class="inputbox" id="<?php echo  $control_name.$name ?>" value="4" size="10" <?php if($value==4) echo 'checked="checked"'; ?> />
</td><td>
<img src="<?php echo $img;?>star5.png" alt="star5" /><br />
<input name="<?php echo $control_name."[".$name."]";?>" type="radio" class="inputbox" id="<?php echo  $control_name.$name ?>" value="5" size="10" <?php if($value==5) echo 'checked="checked"'; ?> />
</td></tr>
</table>

        <?php



        $content=ob_get_contents();



        ob_end_clean();

        return $content;



    }

	}

	

	?>