<?php mysql_connect('localhost','member_add','dNhSSBUc3yYQaTCe');

mysql_select_db('owncloud');

if ( isset($_POST['name']) && isset($_POST['email']) ) {
	$exists = mysql_query("SELECT * FROM oc_mailing_list WHERE member_email = '$_POST[email]' ");
	if ( mysql_num_rows($exists) > 0 ) {
		$query = ("UPDATE oc_mailing_list SET member_name = '$_POST[name]' WHERE member_email = '$_POST[email]' ");
	} else {
		$query = ("INSERT INTO oc_mailing_list (member_name, member_email, member_mailing_lists, member_since, ip_address) VALUES ('$_POST[name]', '$_POST[email]', 'General,', '" . date("Y-m-d") . "', '$_POST[ip_address]') ");
	}

	if ( mysql_query($query) ) {
		print json_encode( array('successs' => $_POST ) );
	} else {
		print json_encode( array('error' => mysql_error() ) );
	}

}?>
