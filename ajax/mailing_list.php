<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('mailing_list');
OCP\JSON::callCheck();

if ( isset ($_POST['mailing_list_remove_id'] ) ) 
	{
		print json_encode( OC_mailing_list::removeMailingList($_POST['mailing_list_remove_id']), true );
	} 
else 
	{		
		foreach ($_POST as $mailing_list_id => $mailing_list_name) {
			$data[] = array('mailing_list_id'=>substr($mailing_list_id, 16 ), 'mailing_list_name'=>$mailing_list_name);
		}
		
		print json_encode( OC_mailing_list::updateMailingListName($data), true );
	}