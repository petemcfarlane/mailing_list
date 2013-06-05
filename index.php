<?php
// Check if we are a user
OCP\User::checkLoggedIn();
OCP\JSON::checkAppEnabled('mailing_list');

OCP\Util::addScript('mailing_list','mailing_list');
OC_Util::addScript( 'core', 'multiselect' );
OCP\Util::addStyle('mailing_list','mailing_list');

OCP\App::setActiveNavigationEntry( 'mailing_list' );

if (isset($_POST['new_member']) && $_POST['new_member'] === 'Add Member') {
	$request['member_name'] = $_POST['member_name'];
	$request['member_email'] = $_POST['member_email'];
	if (isset($_POST['member_lists'])) {
		foreach ($_POST['member_lists'] as $member_list) {
			$request['member_lists'] .= $member_list.',';
		}
	}	
	$addMember = OC_mailing_list::addMemberFromRequest($request);
}

$tmpl = new OCP\Template( 'mailing_list', 'mailing_list', 'user' );
$tmpl->printPage();
