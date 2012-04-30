<?php

class TheliaApiException extends Exception{
    
       /**
      * Error contant definition
      *
      * (1) X : 1 = error / 2 = warning
      * (2) XX: function
      * (3) X : 1 = parameter
      * (4) XX: involve parameter 
      * (5) XX: message (in english)
      */
    
    const ERROR                                     = 0x10000000;
    const WARNING                                   = 0x20000000;
    
    const E_createAccount                           = 0x00100000;
    
    const E_parameter                               = 0x00010000;
    const E_country                                 = 0x00020000;
    const E_phone                                   = 0x00030000;
    const E_cellphone                               = 0x00040000;
    const E_account                                 = 0x00050000;
    
    const E_missing                                 = 0x00000001;
    const E_wrong                                   = 0x00000002;
    const E_exists                                  = 0x00000003;
    const E_unavailable                             = 0x00000004;
    
    
    static private $errorMessage = array(
        10110001 => 'Missing mandatory Parameter',
        20120002 => 'Impossible to retrieve country',
        10130001 => 'Missing phone number',
        10140001 => 'Missing cell phone number',
        10150003 => 'Account already exists',
        10000004 => 'unavailable Resource'
    );
    
    /**
     *
     * return the mesage corresponding with the error code
     * 
     * @param string $code
     * @return string 
     */
    public static function getCustomMessage($code = NULL, $complement = NULL)
    {
        $message = '';
        if(isset(self::$errorMessage[$code])){
            $message .= self::$errorMessage[$code];
        }
        else{
            return '';
        }
        
        if(!empty($complement))
        {
            $message .= ' '.$complement;
        }
        
        return $message;
    }
    
    /**
     * throw a TheliaApiException with unlimit arguments
     * 
     * @param $errorCode1 integer the error codes to add (binay or)
     * @param $errorCode2 integer 
     * @param $errorCode.. integer 
     * @param $errorCodeN integer 
     * 
     * @throws TheliaApiException 
     */
    public static function throwApiExceptionFault()
    {
        $errorCode |= 0;
        $args = func_get_args();

        if(is_array($args[0]))
        {
            $args = $args[0];
        }
        
        foreach($args as $arg)
        {
            $errorCode |= $arg;
        }
        
        if($errorCode != 0)
        {
            $errorCode = strtoupper(dechex($errorCode));
            throw new TheliaApiException(self::$errorMessage[$errorCode],$errorCode);
        }
    }
    
    /**
     *  throw a TheliaApiException if condition is not realised.
     * Condition is the first argument. After it's the arguments for the Exception 
     * 
     * @param $condition mixed the condition that musn't match to throw soap exception
     * @param $errorCode1 integer the error codes to add (binay or)
     * @param $errorCode2 integer 
     * @param $errorCode.. integer 
     * @param $errorCodeN integer 
     * 
     * @throws TheliaApiException 
     */
    public static function throwApiExceptionFaultUnless()
    {
        $args = func_get_args();
        $condition = array_shift($args);
                
        if(!$condition){
            self::throwApiExceptionFault($args);
        }
    }
    
    /**
     *  throw a TheliaApiException if condition is realised.
     * Condition is the first argument. After it's the arguments for the Exception 
     *      
     * @param $condition mixed the condition that must match to throw soap exception
     * @param $errorCode1 integer the error codes to add (binay or)
     * @param $errorCode2 integer 
     * @param $errorCode.. integer 
     * @param $errorCodeN integer 
     * 
     * @throws TheliaApiException 
     */
    public static function throwApiExceptionFaultIf()
    {
        $args = func_get_args();
        $condition = array_shift($args);
        
        if($condition){
             self::throwApiExceptionFault($args);
        }
    }
}