<?php

class TheliaApiTools{
    
    /**
     * display errors as json
     * 
     * @param string $method
     * @param string $message
     * @param string $code 
     */
    public static function displayError($method,$message,$code = null)
    {
        $result = array(
            'status' => 'ko',
            'error' => $method,
            'errorMessage' => $message,
            'errorCode' => $code
        );
        self::displayResult($result);
    }
    
    /**
     * display result as json
     * 
     * @param mixed $result 
     */
    public static function displayResult($result){
        header('Content-type: application/json');
        echo json_encode($result);
        exit;
    } 
    
    
    
    /**
     *
     * @param array $params parameters to retrieve in GET/POST 
     * @param string $errorContext the api exception context that (see ApiException::E_* contants). 
     * @return array
     * @throws InvalidArgumentException 
     */
    public static function extractParam($params,$errorContext = null)
    {
        
        if(null === $errorContext ){
            throw new InvalidArgumentException('[API] cannot find constant for \'ApiException::E_*\'. Pass the error context argument to method');
        }
        
        $out = array();
        $defaultItemsParams = array('required' => true);
        $shortcutItemParams = array(
            'optional' => array('required' => false)
        );
        $errorCode = 0;
        $faultActor = array();
        $faultDetails = array();
        foreach($params as $name => $param){
            if(is_integer($name)){
                $name = $param;
                $param = $defaultItemsParams;
            }
            
            if(is_string($param) && array_key_exists($param, $shortcutItemParams)){
                $param = $shortcutItemParams[$param];
            }
            
            if(!is_array($param)){
                $param = array('default' => $param, 'type' => 'string');
            }
            
            if(!isset($param['type'])){
                $param['type'] = 'string';
            }
            
            $value = lireParam($name,$param['type']);

            if(empty($value)){
                if(isset($param['default'])){
                    $value = $param['default'];
                }
                else if($param['required'] === true){
                    $faultActor[] = $name;
                    $faultDetails[] = sprintf('"%s" parameter is missing', $name);
                    continue;
                }
            }
            
            $out[$name] = $value;
        }
        
        if(!empty($faultActor)){
            $errorCode |= TheliaApiException::ERROR;
            $errorCode |= $errorContext;
            $errorCode |= TheliaApiException::E_parameter;
            $errorCode |= TheliaApiException::E_missing;
            $errorCode = strtoupper(dechex($errorCode));
            $complement = sprintf('[%s]', implode(', ', $faultActor));
            throw new InvalidArgumentException(TheliaApiException::getCustomMessage($errorCode, $complement),$errorCode);
        }
        
        return $out;
    }
}
