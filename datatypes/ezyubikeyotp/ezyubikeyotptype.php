<?php
 
// Define the name of datatype string
define( "EZ_DATATYPESTRING_YUBIKEY_OTP", "ezyubikeyotp" );
 
class eZYubiKeyOTPType extends eZDataType
{
   /*!
    Construction of the class, note that the second parameter in eZDataType
    is the actual name showed in the datatype dropdown list.
   */
   function eZYubiKeyOTPType()
   {
       $this->eZDataType( EZ_DATATYPESTRING_YUBIKEY_OTP, "YubiKey OTP" );
   }

   /*!
    Validates the input and returns true if the input was valid for this
    datatype. Here you could add special rules for validating email.
    Parameter $http holds the class object eZHttpTool which has functions to
    fetch and check http input. Parameter $base holds the base name of http
    variable, in this case the base name will be 'ContentObjectAttribute'.
    Parameter $objectAttribute holds the attribue object.
   */
   function validateObjectAttributeHTTPInput( $http, $base,
                                              $objectAttribute )
   {

       if ( $http->hasPostVariable( $base . '_data_text_' .
                                    $objectAttribute->attribute( 'id' ) ) )
       {
           $YubiKey =& $http->postVariable( $base . '_data_text_' .
                                          $objectAttribute->attribute( 'id' )
                                        );
           $classAttribute =& $objectAttribute->contentClassAttribute();
           if ( $classAttribute->attribute( "is_required" ) == true )
           {
               if ( $YubiKey == "" )
               {
                   $objectAttribute->setValidationError(
                                       ezi18n( 'content/datatypes',
                                               'A valid YubiKey OTP is required.',
                                               'eZYubiKeyOTPType' ) );
                   return eZInputValidator::STATE_INVALID;
               }
           }
           if ( $YubiKey != "" )
           {
		$YubiKeyIsValid = false;

	        // Validate OTP
        	if(!empty($YubiKey)) {
                	// Generate a new id+key from https://api.yubico.com/get-api-key/
                	$yubi = new Auth_Yubico('3826', 'cMidarDsxfKD2WafoWEyRCQbQrk=');
                	$auth = $yubi->verify($YubiKey);
                	if (PEAR::isError($auth)) {
                        	eZLog::write("<p>YubiKey (datatype) Authentication failed: " . $auth->getMessage() . "<p>Debug output from server: " . $yubi->getLastResponse());
                	} else {
				eZLog::write("YubiKey OTP valid (datatype)");
                        	$YubiKeyIsValid = true;
                	} 
        	}

               if ( ! $YubiKeyIsValid )
               {
                   $objectAttribute->setValidationError(
                                             ezi18n( 'content/datatypes',
                                                     'The YubiKey OTP is not valid.',
                                                     'eZYubiKeyOTPType' ) );
                   return eZInputValidator::STATE_INVALID;
               }
           }
       }
       return  eZInputValidator::STATE_ACCEPTED;
   }
 
  /*!
    Fetches the http post var string input and stores it in the data instance.
    A YubiKey OTP could be easily stored as variable characters, we use data_text filed in
    database to save it. In the template, the textfile name of YubiKey OTP input is
    something like 'ContentObjectAttribute_data_text_idOfTheAttribute', therefore we
    fetch the http variable
    '$base . "_data_text_" . $objectAttribute->attribute( "id" )'
    Again, parameters $base holds the base name of http variable and is
    'ContentObjectAttribute' in this example.
   */
   function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }
 
   /*!
    Store the content. Since the content has been stored in function
    fetchObjectAttributeHTTPInput(), this function is with empty code.
   */
   function storeObjectAttribute( $objectAttribute )
   {
   }
 
   /*!
    Returns the content.
   */
   function objectAttributeContent( $objectAttribute )
   {
       return $objectAttribute->attribute( "data_text" );
   }
 
   /*!
    Returns the meta data used for storing search indices.
   */
   function metaData( $objectAttribute )
   {
       return $objectAttribute->attribute( "data_text" );
   }
 
   /*!
    Returns the text.
   */
   function title( $objectAttribute, $name = null )
   {
       return $objectAttribute->attribute( "data_text" );
   }
}
 
eZDataType::register( EZ_DATATYPESTRING_YUBIKEY_OTP, "ezyubikeyotptype" );
 
?>
