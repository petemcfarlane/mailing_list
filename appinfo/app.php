<?php
OCP\App::registerAdmin( 'mailing_list', 'settings' );
OC::$CLASSPATH['OC_mailing_list'] = 'apps/mailing_list/lib/app.php';
OC::$CLASSPATH['VCard'] = 'apps/mailing_list/lib/app.php';
OC::$CLASSPATH['VCardProperty'] = 'apps/mailing_list/lib/app.php';

OCP\App::addNavigationEntry( array( 
	'id' => 'mailing_list',
	'order' => 74,
	'href' => OCP\Util::linkTo( 'mailing_list', 'index.php' ),
	'icon' => OCP\Util::imagePath( 'mailing_list', 'mailing_list.svg' ),
	'name' => 'Mailing List'
));
