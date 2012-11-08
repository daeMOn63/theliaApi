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
     * Redefine lireParam method here since the original one from Thelia core return
     * an empty string if searched param does not exists or if it exists but with an empty value...
     * We need distinct values for those 2 cases to be able to update db objects fields to empty value
     * only when requested (and not when param is volontary ommited).
     */
    protected static function lireParam($param, $filtre="", $methode="", $purifier = 1){

        if($methode == "post")
            $tab = &$_POST;
        else if($methode == "get")
            $tab = &$_GET;
        else
            $tab = &$_REQUEST;

        if (isset($tab[$param]))
        {
            $param = $tab[$param];

            if(get_magic_quotes_gpc())
                $param = stripslashes($param);

            if(preg_match("/^([^\+]*)\+(.*)$/", $filtre, $resfiltre)){
                $filtre = $resfiltre[1];
                $complement = $resfiltre[2];
            }
                else $complement = "";

            return filtrevar($param, $filtre, $complement, $purifier);
        }
        else
        {
            return null;
        }
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

            $value = self::lireParam($name,$param['type']);
            if(!isset($value) || empty($value)){
                if(isset($param['default'])){
                    $value = $param['default'];
                }
                else if($param['required'] === true){
                    $faultActor[] = $name;
                    $faultDetails[] = sprintf('"%s" parameter is missing', $name);
                    continue;
                }
            }
            if(isset($value)) // do not return parameters that where not in query string
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
