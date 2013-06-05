<?php

OCP\User::checkAdminUser();
OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('mailing_list');

OCP\Util::addScript( "mailing_list", "admin" );
OCP\Util::addStyle('mailing_list','mailing_list');

if (isset($_POST['add_mailing_list_name']) && $_POST['add_mailing_list_name'] !== '' ) {
	OC_mailing_list::addMailingList($_POST['add_mailing_list_name']);
}

$tmpl = new OCP\Template( 'mailing_list', 'settings');

return $tmpl->fetchPage();