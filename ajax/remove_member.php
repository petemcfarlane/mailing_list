<?php
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('mailing_list');
OCP\JSON::callCheck();

if (isset($_POST['member_id'])) {
	$removeMember = OC_mailing_list::removeMemberFromRequest($_POST['member_id']);
	print json_encode($removeMember);
}