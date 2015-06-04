<?php

return array(
        'favico' => array(
                'dir' => '/images/favicon.ico'
        ),
        'option_saved_text' => 'Options successfully saved',
        'tabs' => array(
                array(
                        'title'=>'General Options',
                        'icon'=>1,
                        'boxes' => array(
                                'Logo' => array(
                                        'icon'=>'customization',
                                        'size'=>'2_3',
                                        'columns'=>true,
                                        'description'=>'Here you upload a image as logo or you can write it as text and select the logo color, size, font.',
                                        'input_fields' => array(
                                                'Logo As Image'=>array(
                                                        'size'=>'half',
                                                        'id'=>'logo_image',
                                                        'type'=>'image_upload',
                                                        'note'=>'Here you can insert your link to a image logo or upload a new logo image.'
                                                ),
                                                'Logo As Text'=>array(
                                                        'size'=>'half_last',
                                                        'id'=>'logo_text',
                                                        'type'=>'text',
                                                        'note' => "Type the logo text here, then select a color, set a size and font",
                                                        'color_changer'=>true,
                                                        'font_changer'=>true,
                                                        'font_size_changer'=>array(1,1000, 'px'),
                                                        'font_preview'=>array(true, true)
                                                )
                                        )
                                ),
                                'Favicon' => array(
                                        'icon'=>'customization',
                                        'size'=>'1_3_last',
                                        'input_fields' => array(
                                                array(
                                                        'id'=>'favicon',
                                                        'type'=>'image_upload',
                                                        'note'=>'Here you can upload the favicon icon.'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Site Background',
                        'icon'=>4,
                        'boxes' => array(
                                'Background Customization'=>array(
                                        'icon'=>'background',
                                        'columns'=>true,
                                        'input_fields' => array(
                                                'Background Color'=>array(
                                                        'size'=>'3',
                                                        'id'=>'bg_color',
                                                        'type'=>'colorpicker'
                                                ),
                                                'Background Image' => array(
                                                        'size'=>'3',
                                                        'id'=>'bg_image',
                                                        'type'=>'image_upload'
                                                ),
                                                'Position' => array(
                                                        'size'=>'3_last',
                                                        'id' => 'bg_image_position',
                                                        'type' => 'radio',
                                                        'values' => array('Left','Center','Right')
                                                ),
                                                'Repeat' => array(
                                                        'size'=>'3_last',
                                                        'id' => 'bg_image_repeat',
                                                        'type' => 'radio',
                                                        'values' => array('bitch'=>'No Repeat','Tile','Tile Horizontally','Tile Vertically')
                                                ),
                                                'Attachment' => array(
                                                        'size'=>'3_last',
                                                        'id' => 'bg_image_attachment',
                                                        'type' => 'radio',
                                                        'values' => array('Scroll','Fixed')
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Social Icons',
                        'icon'=>2,
                        'boxes'=>array(
                                'Social Platforms'=>array(
                                        'icon'=>'social',
                                        'description'=>"Set the URL for social platforms.<br/>Only the platforms that have and URL set will be shown.<br/>The URL should start with http:// or https://.",
                                        'size'=>'1_3',
                                        'columns'=>true,
                                        'input_fields'=>array(
                                                array(
                                                        'id'=>'social_platforms',
                                                        'size'=>'half',
                                                        'type'=>'social_platforms',
                                                        'platforms'=>array('facebook','twitter','google','linkedin','dribbble','pinterest','instagram')
                                                )
                                        )
                                ),
                                'Custom Icons'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Add custom social icons and URLs.<br/>These icons will be appended to the list of default icons.<br/>The image should be of the format like other images.<br/>For an example please look in the /img/ folder (for ex. the image forInstagram is social-instagram.png).",
                                        'size'=>'half_last',
                                        'repeater' => 'Add new icon',
                                        'input_fields' =>array(
                                                'Image'=>array(
                                                        'id'=>'custom_social_image',
                                                        'type'=>'image_upload',
                                                        'note'=>'Set the custom social icon image.'
                                                ),
                                                'URL'=>array(
                                                        'type'=>'text',
                                                        'id'=>'custom_social_url',
                                                        'placeholder'=>'Enter the full URL',
                                                        'note'=>'Set the full URL for the custom social icon. The URL should start with http:// or https://.'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Twitter Settings',
                        'icon'  => 3,
                        'boxes' => array(
                                'Twitter API keys'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Used by the Twitter Widget",
                                        'size'=>'1_3_last',
                                        'columns'=>false,
                                        'input_fields' =>array(
                                                'Consumer Key' => array(
                                                        'id'    => 'twitter_consumerkey',
                                                        'type'  => 'text',
                                                        'size' => '1'
                                                ),
                                                'Consumer Secret' => array(
                                                        'id'    => 'twitter_consumersecret',
                                                        'type'  => 'text',
                                                        'size' => '1',
                                                ),
                                                'Access Token' => array(
                                                        'id'    => 'twitter_accesstoken',
                                                        'type'  => 'text',
                                                        'size' => '1',
                                                ),
                                                'Access Toekn Secret' => array(
                                                        'id'    => 'twitter_accesstokensecret',
                                                        'type'  => 'text',
                                                        'size' => '1',
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Additional Options',
                        'icon'  => 6,
                        'boxes' => array(
                                'Custom CSS' => array(
                                        'icon'=>'css',
                                        'size'=>'2_3',
                                        'description'=>'Here you can write your personal CSS for customizing the classes you choose to modify.',
                                        'input_fields' => array(
                                                array(
                                                        'id'=>'custom_css',
                                                        'type'=>'textarea'
                                                )
                                        )
                                ),
                                'Append to footer' => array(
                                        'icon'=>'track',
                                        'size'=>'2_3_last',
                                        'description' => 'You can paste here the Google Analytics code for example.',
                                        'input_fields'=>array(
                                                array(
                                                        'type'=>'textarea',
                                                        'id'=>'append_to_footer'
                                                )
                                        )
                                ),
                                'Excerpt length' => array(
                                        'icon'=>'track',
                                        'size'=>'2_3_last',
                                        'description' => 'Set a custom excerpt length. Leave empty for default value. Default value is 55.',
                                        'input_fields'=>array(
                                                array(
                                                        'type'=>'text',
                                                        'id'=>'excerpt_length'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Contact Info',
                        'icon'  => 5,
                        'boxes' => array(
                                'Contact info'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Provide contact information. This information will appear in contact template. For more informations read documentation.",
                                        'size'=>'2_3',
                                        'columns'=>true,
                                        'input_fields' =>array(
                                                'Map iframe' => array(
                                                        'id'    => 'contact_map',
                                                        'type'  => 'map',
                                                        'note' => 'Here you can insert iframe with your location. For more information you can find in theme\'s documentation' ,
                                                        'size' => 'half',
                                                        'icons' => array('google-marker.gif','home.png','home_1.png','home_2.png','administration.png','office-building.png')
                                                ),
                                                'Map height' => array(
                                                        'id'    => 'map_height',
                                                        'type'  => 'text',
                                                        'note' => 'Specify a height in pixels for the contact map (default is 700)',
                                                        'size' => 'half',
                                                        'placeholder' => 'Map height'
                                                ),
                                                'Contact form' => array(
                                                        'id'    => 'email_contact',
                                                        'type'  => 'text',
                                                        'note' => 'Provide an email used to recive messages from Contact Form',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Contact Form Email'
                                                ),
                                                array(
                                                        'id'    => 'email_prefix',
                                                        'type'  => 'text',
                                                        'note' => 'Provide a prefix for subjects of the messages received from Contact Form',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Subject Prefix'
                                                )
                                        )
                                )

                        )
                ),
                array(
                        'title' => 'Subscription',
                        'icon'  => 7,
                        'boxes' => array(
                                'Subscribers'=>array(
                                        'icon' => 'social',
                                        'description'=>'First 20 subscribers are listed here. To get the full list export files using buttons below:',
                                        'size'=>'1',
                                        'input_fields' => array(
                                                array(
                                                        'type'=>'subscription',
                                                        'id'=>'subscription_list'
                                                )
                                        )
                                )
                        )
                ),
        ),
        'styles' => array( array('wp-color-picker'),'style','select2' ),
        'scripts' => array( array( 'jquery', 'jquery-ui-core','jquery-ui-datepicker','wp-color-picker' ), 'select2.min','jquery.cookie','tt_options', 'admin_js' )
);