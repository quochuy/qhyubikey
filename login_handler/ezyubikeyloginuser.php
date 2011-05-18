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
	$YubiKey = $password;
	eZLog::write("Login handler, YubiKey: {$YubiKey}");

	$user = eZUser::fetchByName( $login );
	$userContentObject = eZContentObject :: fetch( $user->attribute('contentobject_id') );
        $userDatamap = $userContentObject->dataMap();
        $userRecordedYubiKeyOTP = $userDatamap['yubikey']->attribute('data_text');
	$userRecordedYubiKeyOTP_2 = $userDatamap['yubikey_backup']->attribute('data_text');
	$userUseOTP4MultiFactor = $userDatamap['multifactor']->attribute('data_int');

        $YubiKeyPrefix = substr($YubiKey, 0, 12);
	$recordedYubiKeyPrefix = substr($userRecordedYubiKeyOTP, 0, 12);
	$recordedYubiKeyPrefix_2 = substr($userRecordedYubiKeyOTP_2, 0, 12); 
	
	eZLog::write("Primary Key: {$userRecordedYubiKeyOTP}");
	eZLog::write("Secondary Key: {$userRecordedYubiKeyOTP_2}");
	
	switch(true) {
		// if the use's set to use OTP as multifactor
		case ($userUseOTP4MultiFactor == 1):
			eZLog::write("Multifactor: {$userUseOTP4MultiFactor}");
			// if no key was submitted then don't allow login
			if(empty($YubiKey)) $user = self::REQUIRE_MULTIFACTOR;
			// else return false to continue with the next login handler
			else $user = false;
		break;

		// if there is an OTP recorded and not set to multifactor but no YubiKey submitted then don't allow login
		case (!empty($userRecordedYubiKeyOTP) && empty($YubiKey)):
			eZLog::write("OTP set, no multifactor, no YubiKey received");
			$user = self::REQUIRE_YUBIKEY_OTP;
		break;

		/*
        	Don't allow login in using YubiKey if
                	- if YubiKey is empty
                	- if no YubiKey OTP has been recorded in profile
        	*/
		case (empty($YubiKey)):
		case (empty($userRecordedYubiKeyOTP)):
			eZLog::write("Auth denied");
                        $user = false;
		break;

		/*
		If YubiKey OTP from profile has a different prefix as the YubiKey OTP submitted
		then test against secondary key's prefix, if fail then auth denied
		*/
		case (($YubiKeyPrefix != $recordedYubiKeyPrefix) && ($YubiKeyPrefix != $recordedYubiKeyPrefix_2)):
			eZLog::write("Auth denied");
			$user = false;
		break;

		default:
			eZLog::write("Looks OK!");
		break;
	}
 
	return $user;
    }

}

?>
