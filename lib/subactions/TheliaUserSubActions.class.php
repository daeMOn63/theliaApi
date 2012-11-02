<?php

require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Client.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Pays.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../exception/TheliaApiException.class.php');
require_once(realpath(dirname(__FILE__)) . '/../TheliaApiTools.class.php');

require_once(realpath(dirname(__FILE__)) . "/AbstractTheliaSubActions.class.php");


class TheliaUserSubActions extends AbstractTheliaSubActions {

	public function getSubActions() {
		return array(
			"create_account" => array($this, "createAccount"),
			"list_customer" => array($this, "listCustomer"),
			"list_countries" => array($this, "listCountries"),
		);
	}


    /**
     * @param int $raison customer's gender : 1 => Mme, 2 => Mlle, 3 => M - REQUIRED
     * @param string $entreprise customer's name enterprise - OPTIONAL
     * @param string $siret - OPTIONAL
     * @param string $intracom - OPTIONAL
     * @param string $nom   customer's lastname - REQUIRED
     * @param string $prenom customer's firstname - REQUIRED
     * @param string $adresse1 - REQUIRED
     * @param string $adresse2 - OPTIONAL
     * @param string $adresse3 - OPTIONAL
     * @param string $cpostal customer's zip code - REQUIRED
     * @param string $ville customer's city - REQUIRED
     * @param string $telfixe customer's phone - OPTIONAL IF $obligeTelFixe=0
     * @param string $telport customer's cellphone - OPTIONAL IF $obligeTelPort = 0
     * @param string $email - REQUIRED
     * @param string $motdepasse customer's password - OPTIONAL. IF EMPTY THE PASSWORD IS GENERATED
     * @param int $parrain id customer's sponsor - OPTIONAL
     * @param int $type - OPTIONAL
     * @param int $pourcentage - OPTIONAL
     * @param int $lang customer's language - DEFAULT TO 1 => FRENCH. 2 => ENGLISH
     * @param int $duplicateEmail - ALLOW RECORD WITH ALREADY EXISTING EMAIL. DEFAULT TO 0 (NONE)
     * @param int $obligeTelFixe - 1 => PHONE IS REQUIRED, 0 => NONE
     * @param int $obligeTelPort - 1 => CELLPHONE IS REQUIRED, 0 => NONE
     *
     * Needs acces_clients write access
     *
     *
     * @throws TheliaApiException
     */
    public function createAccount()
    {
        TheliaApiException::throwApiExceptionFaultUnless(
                $this->api->checkAccess('clients',0,1),
                TheliaApiException::ERROR,
                TheliaApiException::E_unavailable);



        extract(TheliaApiTools::extractParam(array(
            'raison' => array('type' => 'int', 'required' => true),
            'entreprise' => 'optional',
            'siret' => 'optional',
            'intracom' => 'optional',
            'nom',
            'prenom',
            'adresse1',
            'adresse2' => 'optional',
            'adresse3' => 'optional',
            'cpostal',
            'ville',
            'pays',
            'telfixe' => 'optional',
            'telport' => 'optional',
            'email',
            'motdepasse' => 'optional',
            'parrain' => array('type' => 'int','default' => 0),
            'type' => array('type' => 'int', 'default' => 0),
            'pourcentage' => array('type' => 'int', 'default' => 0),
            'lang' => array('type' => 'int', 'default' => 1),
            'duplicateEmail' => array('type' => 'int', 'default' => 0),
            'obligeTelFixe' => array('type' => 'int', 'default' => 0),
            'obligeTelPort' => array('type' => 'int', 'default' => 0)
        ),
        TheliaApiException::E_createAccount));

        ActionsModules::instance()->appel_module("avantclient");

        $errorCode = 0;
        $paysSearch = new Pays();
        TheliaApiException::throwApiExceptionFaultUnless(
                $paysSearch->getVars('SELECT * FROM '.$paysSearch->table.' WHERE isocode=\''.$pays.'\''),
                TheliaApiException::WARNING,
                TheliaApiException::E_createAccount,
                TheliaApiException::E_country,
                TheliaApiException::E_wrong);

        TheliaApiException::throwApiExceptionFaultIf(
                ($obligeTelFixe && empty($telfixe)),
                TheliaApiException::ERROR,
                TheliaApiException::E_createAccount,
                TheliaApiException::E_phone,
                TheliaApiException::E_missing);

        TheliaApiException::throwApiExceptionFaultIf(
                ($obligeTelPort && empty($telport)),
                TheliaApiException::ERROR,
                TheliaApiException::E_createAccount,
                TheliaApiException::E_cellphone,
                TheliaApiException::E_missing);

        $client = new Client();
        TheliaApiException::throwApiExceptionFaultIf(
                ($client->existe($email) && !$duplicateEmail),
                TheliaApiException::ERROR,
                TheliaApiException::E_createAccount,
                TheliaApiException::E_account,
                TheliaApiException::E_exists
        );

        if(empty($motdepasse)){
            $motdepasse = genpass(8);
        }

        $tempPassword = $motdepasse;

        $client->nom = $nom;
        $client->prenom = $prenom;
        $client->adresse1 = $adresse1;
        $client->adresse2 = $adresse2;
        $client->adresse3 = $adresse3;
        $client->cpostal = $cpostal;
        $client->ville = $ville;
        $client->pays = $paysSearch->id;
        $client->telfixe = $telfixe;
        $client->telport = $telport;
        $client->email = $email;
        $client->motdepasse = $motdepasse;
        $client->raison = $raison;
        $client->entreprise = $entreprise;
        $client->siret = $siret;
        $client->intracom = $intracom;
        $client->parrain = $parrain;
        $client->type = $type;
        $client->pourcentage = $pourcentage;
        $client->lang = $lang;
        $client->datecrea = date('Y-m-d H:i:s');
        $client->crypter();
        $client->id = $client->add();
        $client->ref = date("ymdHi") . genid($client->id, 6);
        $client->maj();

        ActionsModules::instance()->appel_module("apresclient", $client);

        $client->motdepasse = $tempPassword;
        unset($client->bddvars);
        unset($client->link);
        unset($client->table);

        TheliaApiTools::displayResult(array('status' => 'ok','client' =>$client));
    }


    /**
     *
     * return list of customer
     *
     * @param integer $limit => The number of results to return (default : 50)
     * @param integer $offset => Result to start at (default : 0)
     * @param string $nom => search on customer name - optional
     * @param string $ref => search on customer ref - optional
     * @param integer $id => search on customer id - optional
     * @param order => order results on column you want (default : nom ASC)
     *
     * Needs acces_clients read access
     *
     * @throws TheliaApiException
     *
     */
    public function listCustomer()
    {
        TheliaApiException::throwApiExceptionFaultUnless(
                $this->api->checkAccess('clients',1,0),
                TheliaApiException::ERROR,
                TheliaApiException::E_unavailable);

        extract(TheliaApiTools::extractParam(array(
            'limit' => array('type' => 'int', 'default' => 50),
            'offset' => array('type' => 'int', 'default' => 0),
            'name' => 'optional',
            'ref' => 'optional',
            'id' => array('type' => 'int', 'required' => false),
            'order' => array('type' => 'string', 'default' => 'nom')
        ),
        TheliaApiException::E_listCustomer));

        $query = 'SELECT id, ref, datecrea, raison, entreprise, siret, intracom, nom, prenom, telfixe, telport, email, adresse1, adresse2, adresse3, cpostal, ville, pays, parrain, type, pourcentage, lang FROM '.Client::TABLE;

        $search = ' WHERE 1';

        if(!empty($name))
        {
            $search .= ' AND nom LIKE \''.$name.'\'';
        }

        if(!empty($ref))
        {
            $search .= ' AND ref=\''.$ref.'\'';
        }

        if(!empty($id))
        {
            $search .= ' AND id='.$id;
        }

        $query .= $search;

        $query .= ' ORDER BY '.$order;

        $query .= ' LIMIT '.$offset.','.$limit;

        $results = $this->api->query_liste($query);

        TheliaApiTools::displayResult(array('status' => 'ok', 'clients' => $results));
    }

    /**
     * List all countries with isocode and alphacode
     *
     * Needs access_configuration read access
     *
     * @throws TheliaApiException
     *
     */
    public function listCountries()
    {
        TheliaApiException::throwApiExceptionFaultUnless(
                $this->api->checkAccess('configuration',1,0),
                TheliaApiException::ERROR,
                TheliaApiException::E_unavailable);

        $query = 'SELECT pd.titre, p.id, p.isocode, p.isoalpha2, p.isoalpha3 FROM '.Pays::TABLE.' p LEFT JOIN '.Paysdesc::TABLE.' pd ON p.id = pd.pays WHERE pd.lang=1';

        $countries = $this->api->query_liste($query);

        TheliaApiTools::displayResult(array("status" => "ok", "countries" => $countries));

    }
}
