<?php 
OCP\JSON::callCheck();
if (isset($_POST['member_id']) && isset($_POST['checked'])) {
	$request['member_id'] 	= $_POST['member_id'];
	$request['checked'] 	= $_POST['checked'];
	$return = OC_mailing_list::toggleMemberList($request);
	print json_encode($return);
}