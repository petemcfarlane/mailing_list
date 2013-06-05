<?php

OCP\JSON::checkLoggedIn();
OCP\JSON::checkAppEnabled('mailing_list');
OCP\JSON::callCheck();


function getNamesAndEmailsFromVcard ($file) {
	$lines = file($file);
	if (!$lines) exit("Can't read the vCard file: $file");
	$cards = array();
	$card = new VCard();
	while ($card->parse($lines)) {
		$cards[] = $card;
		$card = new VCard();
	}
	$members = array();
	foreach ($cards as $card_name => $card) {
		$properties = $card->getProperties('FN');
		if ($properties) {
			foreach($properties as $property) {
				$components = $property->getComponents();
				foreach ($components as $component) {
					if ($component) {
						$FN = stripcslashes($component);
					}
				}
				
			}
		}
		$properties = $card->getProperties('EMAIL');
		if ($properties) {
			foreach($properties as $property) {
				$components = $property->getComponents();
				foreach ($components as $component) {
					if ($component) {
						$EMAIL = stripcslashes($component);
					}
				}
				
			}
		}
		if ($FN && $EMAIL) $members[$FN] = $EMAIL;
	}
	return $members;
}

if (isset($_FILES['import_file'])) {
	if ( move_uploaded_file($_FILES['import_file']['tmp_name'], "/var/tmp/" . basename($_FILES['import_file']['name']) ) ) {
		$file = "/var/tmp/" . basename($_FILES['import_file']['name']);	
		$members = getNamesAndEmailsFromVcard($file);
		if (isset($_POST['member_lists'])) {
			$member_lists = '';
			foreach($_POST['member_lists'] as $list) {
				$member_lists = $member_lists . $list . ',';
			}
		}
		$request = array();
		$new_members = array();
		foreach($members as $member_name => $member_email) {
			unset($request);
			$request['member_name']  = $member_name;
			$request['member_email'] = $member_email;
			$request['member_lists'] = $member_lists;
			
			$new_members[] = OC_mailing_list::addMemberFromRequest($request);
		}
		unlink($file);
	}
}

print json_encode($new_members);