<?php

require_once(realpath(dirname(__FILE__)) . '/../../../../classes/Baseobj.class.php');

class TheliaApiAuth extends Baseobj
{

    public $id;
    public $thelia_api_id;
    public $autorisation_id;
    public $read;
    public $write;

    public $bddvars = array('id','thelia_api_id','autorisation_id','read','write');

    const TABLE = 'autorisation_thelia_api';
    public $table = self::TABLE;

    public function charger($autorisation,$api)
    {
        return $this->getVars('select * from '.$this->table.' where thelia_api_id='.$api.' and autorisation_id='.$autorisation);
    }

}
