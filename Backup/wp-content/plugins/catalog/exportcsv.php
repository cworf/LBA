<?php
/**
 * @package Spider Contacts
 * @author Web-Dorado
 * @copyright (C) 2012 Web-Dorado. All rights reserved.
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/

// This php file generates CSV therefore direct access must be allowed

function echo_catalog_csv( $fields )
{
    $pattern = "/par_([a-zA-Z0-9\-]*)@@:@@(([a-zA-Z0-9\-(),.\+_]|\x20)*)/";
    $separator = '';
    foreach ( $fields as $field )
    {
        // if ( substr_count($field, '@@:@@'))
        // {
            // $string = '';
            // $par_data=explode("par_",$field);

            // for($j=0;$j<count($par_data);$j++)
                // if($par_data[$j]!='')
                // {
                    // $par1_data=explode("@@:@@",$par_data[$j]);
                    // $par_values=explode("	",$par1_data[1]);
                    // $countOfPar=0;
                    // for($k=0;$k<count($par_values);$k++)
                        // if($par_values[$k]!="")
                            // $countOfPar++;
                    // if($countOfPar!=0)
                    // {
                        // $string .= $par1_data[0].':';
                        // for ($k = 0; $k < count($par_values); $k++)
                            // if ($par_values[$k] != "")
                                // $string.= $par_values[$k].', ' ;
                        // $string .= "\n";
                    // }
                // }
            // $field = $string;
        // }
        $field1 = $field;

        // if ( preg_match_all(  $pattern ,$field, $res))
        // { 	$field1 ='';
            // for ($i=0;$i<count($res[0]);$i++)
            // {
                // $field1.=$res[1][$i].':'.$res[2][$i].' ';
            // }
        // }
        if ( preg_match( '/\\r|\\n|,|"/', $field1 ) )
        {
            $field1 = '"' . str_replace( '"', '""', $field1 ) . '"';
        }
        $field1 = str_replace( "'", "\'", $field1);
        echo $separator . $field1;
        $separator = ',';
    }
    echo "\r\n";
}

function export_catalog_csv() {
	global $wpdb;
	$filename = 'export_'.date("d-m-y") . '.csv';

	//JRequest::setVar('format','raw');
	@ob_end_clean();
	@clearstatcache();
	header('Content-Description: File Transfer');
	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment;filename='.$filename.'' );

	$query = "SELECT " . $wpdb->prefix . "spidercatalog_products.name AS 'Name'," . $wpdb->prefix . "spidercatalog_products.category_id AS 'Category id'," . $wpdb->prefix . "spidercatalog_products.description AS 'Description', " . $wpdb->prefix . "spidercatalog_products.image_url AS 'Picture Url'," . $wpdb->prefix . "spidercatalog_products.cost AS 'Cost'," . $wpdb->prefix . "spidercatalog_products.market_cost AS 'Market Cost'," . $wpdb->prefix . "spidercatalog_products.param AS 'Parameters'," . $wpdb->prefix . "spidercatalog_products.ordering AS 'Ordering'," . $wpdb->prefix . "spidercatalog_products.published AS 'Published'," . $wpdb->prefix . "spidercatalog_products.published_in_parent AS 'Show in Parent',categories.name AS 'Category' FROM " . $wpdb->prefix . "spidercatalog_products LEFT JOIN " . $wpdb->prefix . "spidercatalog_product_categories  AS categories ON  " . $wpdb->prefix . "spidercatalog_products.category_id=categories.id";
	$result = $wpdb->get_results($query);
	if (!$result)
	{
		echo "Unexpected error occured";
	}

	//
	// output header row 
	//

	echo_catalog_csv( $wpdb->get_col_info("name") );

	//
	// output data rows (if atleast one row exists)
	//

	foreach ($result as $row1)
	{
		echo_catalog_csv( $row1 );
	}
	die();
}
  
 
  