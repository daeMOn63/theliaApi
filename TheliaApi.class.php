<?php
Autoload::getInstance()->addDirectories(array(__DIR__ . "/exception/", __DIR__ . "/lib/", __DIR__ . "/lib/subactions"));

class TheliaApi extends PluginsClassiques
{

    public $id;
    public $login;
    public $password;
    public $firstname;
    public $lastname;

    const TABLE = 'thelia_api';
    public $table = self::TABLE;

    public $bddvars = array('id','login','password','firstname','lastname');

    protected $autorisation = array();

    protected $subActions = array();

    private static $realm = 'TheliaApi';

    public function __construct($name = 'theliaApi')
    {
        parent::__construct($name);

        $this->registerSubActions(new TheliaUserSubActions($this));
        $this->registerSubActions(new TheliaRubriqueSubActions($this));
        $this->registerSubActions(new TheliaProductSubActions($this));
    }


    protected function registerSubActions(AbstractTheliaSubActions $subActions) {
        $actions = $subActions->getSubActions();
        foreach($actions as $action => $callback) {
            if(array_key_exists($action, $this->subActions)) {
                TheliaApiTools::displayError("registerSubActions", get_class($subActions). " redefines an existing subaction : '".$action."'");
            }else {
                $this->subActions[$action] = $callback;
            }
        }
    }

    /**
     * Create table for authentification if needed
     */
    public function init()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `thelia_api`(`id` INT AUTO_INCREMENT, `login` VARCHAR(255), `password` VARCHAR(255), `firstname` VARCHAR(255), `lastname` VARCHAR(255), PRIMARY KEY(`id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;';
        $this->query($sql);

        $sql = 'CREATE TABLE IF NOT EXISTS `autorisation_thelia_api`( `id` INT AUTO_INCREMENT, `thelia_api_id` INT, `autorisation_id` INT, `read` INT DEFAULT 0, `write` INT DEFAULT 0, PRIMARY KEY(`id`), INDEX `autorisation_thelia_api_thelia_api_id`(`thelia_api_id`), INDEX `autorisation_thelia_api_autorisation_id`(`autorisation_id`)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;';
        $this->query($sql);
    }

    /**
     *  check if php is running in cgi(-fcgi) mode
     *
     * @return boolean
     */
    public static function checkIsCgi()
    {
        $sapi = php_sapi_name();

        if (substr($sapi, 0, 3) == 'cgi') {
            return true;
        }
        return false;
    }

    public function add()
    {
        if(empty($this->id))
        {
            $this->password = sha1($this->password);
        }

        return parent::add();
    }

    /**
     * hash new password
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = sha1($password);
    }

    /**
     * put all thelia_api user authorisation in autorisation paramter
     */
    protected function _getAuth()
    {
        $query = 'SELECT `a`.`nom` as `nom`, `t`.`read` as `read`, `t`.`write` as `write` FROM `autorisation` `a` LEFT JOIN `autorisation_thelia_api` `t` ON `a`.`id` = `t`.`autorisation_id` WHERE `t`.`thelia_api_id`='.$this->id;
        $results = $this->query_liste($query);
        if(!empty($results))
        {
            foreach($results as $result)
            {
                $this->autorisation[$result->nom] = array('read' => $result->read, 'write' => $result->write);
            }

        }

    }

    /**
     *
     * @param string $type type of access needed (clients, commandes, etc)
     * @param type $read if the resource need read access
     * @param type $write if the resource need write access
     *
     * @return boolean
     */
    public function checkAccess($type, $read = 0, $write = 0)
    {

        if(!isset($this->autorisation['acces_'.$type]))
        {
            return false;
        }

        if($this->autorisation['acces_'.$type]['read'] < $read)
        {
            return false;
        }

        if($this->autorisation['acces_'.$type]['write'] < $write)
        {
            return false;
        }

        return true;
    }

    /**
     *
     * try to load a thelia_api user
     *
     * @param string $login
     * @param string $password
     * @return boolean
     */
    public function charger($login, $password)
    {
        $query = sprintf("select ".$this->getListVarsSql()." from $this->table where login='%s' and password='%s'", $this->escape_string($login), sha1($password));

        if($this->getVars($query))
        {
            $this->_getAuth();
            return true;
        }

        return false;
    }


    private function _connexion()
    {
        if(self::checkIsCgi()){
            list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':' , base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
        }

       if(!$this->charger($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'])){
            header('WWW-Authenticate: Basic realm='.self::$realm);
            header('HTTP/1.0 401 Unauthorized');
            echo 'Resource unavailable';
            exit;
        }
    }

    public function action()
    {
        $action = lireParam('action','string');
        $subaction = lireParam('subaction','string');

        if($action == 'api'){
            $this->_connexion();
            try{
                if(array_key_exists($subaction, $this->subActions)) {
                    call_user_func($this->subActions[$subaction]);
                }else{
                    ActionsModules::instance()->appel_module("api",$subaction);
                }
            }
            catch(TheliaApiException $e)
            {
                TheliaApiTools::displayError($subaction, $e->getMessage(), $e->getCode());
            }
            catch(InvalidArgumentException $e){
                TheliaApiTools::displayError($subaction, $e->getMessage(), $e->getCode());
            }
        }
    }

}