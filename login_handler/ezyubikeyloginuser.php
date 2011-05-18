<?php

class eZYubiKeyLoginUser extends eZUser
{

    const REQUIRE_MULTIFACTOR='multifactor_required';
    const REQUIRE_YUBIKEY_OTP='otp_required';

    /*!
    \static
     Logs in the user if applied username and password is valid.
     \return The user object (eZContentObject) of the logged in user or \c false if it failed.
    */
    static function loginUser( $login, $password, $authenticationMatch = false )
    {
	$ini = eZINI::instance( 'qhyubikey.ini' );
	$debugOutput = $ini->variable( 'QHYubiKeySettings', 'DebugOutput' );
	$YubiKey = $password;
	if( $debugOutput ) eZLog::write("Login handler, YubiKey: {$YubiKey}");

	$user = eZUser::fetchByName( $login );
	$userContentObject = eZContentObject :: fetch( $user->attribute('contentobject_id') );
        $userDatamap = $userContentObject->dataMap();

	if( isset( $userDatamap['yubikeys'] )) {
		$matrix = new eZMatrix( '' );
		$matrix->decodeXML( $userDatamap['yubikeys']->attribute('data_text'));
		$userRecordedYubiKeyOTPArray = $matrix->Matrix['columns']['sequential'][1]['rows'];
	}

	$userUseOTP4MultiFactor = $userDatamap['multifactor']->attribute('data_int');

        $YubiKeyPrefix = substr($YubiKey, 0, 12);

	$recordedMatchedPrefixes = array();
	foreach( $userRecordedYubiKeyOTPArray as $key => $userRecordedYubiKeyOTP ) {
		if( $debugOutput ) eZLog::write( "Yubikey{$key}: {$userRecordedYubiKeyOTP}");
		$recordedYubiKeyPrefix = substr( $userRecordedYubiKeyOTP, 0, 12 );
		if( $YubiKeyPrefix == $recordedYubiKeyPrefix) {
			if( $debugOutput ) eZLog::write( "key {$key}'s prefix matches" );
			$recordedMatchedPrefixes[] = $key; 
		}
	}
	
	switch(true) {
		// if the use's set to use OTP as multifactor
		case ($userUseOTP4MultiFactor == 1):
			if( $debugOutput ) eZLog::write("Multifactor: {$userUseOTP4MultiFactor}");
			// if no key was submitted then don't allow login
			if(empty($YubiKey)) $user = self::REQUIRE_MULTIFACTOR;
			// else return false to continue with the next login handler
			else $user = false;
		break;

		// if there is an OTP recorded and not set to multifactor but no YubiKey submitted then don't allow login
		case (count($userRecordedYubiKeyOTPArray) && empty($YubiKey)):
			if( $debugOutput ) eZLog::write("OTP set, no multifactor, no YubiKey received");
			$user = self::REQUIRE_YUBIKEY_OTP;
		break;

		/*
        	Don't allow login in using YubiKey if
                	- if YubiKey is empty
			- if there are no matching recorded keys in the profile
                	- if no YubiKey OTP has been recorded in profile
        	*/
		case (empty($YubiKey)):
		case (empty($recordedMatchedPrefixes)):
		case (!count($userRecordedYubiKeyOTP)):
			if( $debugOutput ) eZLog::write("Auth denied");
                        $user = false;
		break;

		default:
			if( $debugOutput ) eZLog::write("Looks OK!");
		break;
	}
 
	return $user;
    }

}

?>
