<?php 
return array(
    'error_class'=>'error',			//class added to container on error

	'required_class' => 'required_error',	//class added to the required input with no value at submission
	'required_msg' => 'Please fill required fields',	//message displayed when at least one required filed is not filled
	'invalid_email_class' => 'invalid_email',	//class added to the email input with an invalid email value at submission
	'invalid_email_msg'=>"Invalid Email",	//Invalid email message
	'input_timeout' => true,	//if true will remove input classes within the time in ms set for 'result_timeout' config below

	'success_class'=>'success',		//class addded to container on successful subscription
	'animation_done_class'=>'animation_done',
	'result_timeout' => 3000,	//time in ms to remove result container html and added classes (error_class or success_class)
	
	'result_container_selector'=>'',//Jquery type selector for the result container
	'date_format' => "F j, Y, g:i a",
	'date_headline' => 'Date',
	'no_data_posted' => 'No data received',
	'error_open_create_files_msg' => '', //leave blank to get the php error msg
	'success_msg' => 'Succesfully Subscribed',
	'error_writing_msg' => "Couldn't write to file",
	'error_mailchimp' => ""	//leave blank to get the mailchimp api error msg
);