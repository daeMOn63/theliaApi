<?php

require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Client.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Pays.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../exception/TheliaApiException.class.php');
require_once(realpath(dirname(__FILE__)) . '/../TheliaApiTools.class.php');

require_once(realpath(dirname(__FILE__)) . "/AbstractTheliaSubActions.class.php");


class TheliaRubriqueSubActions extends AbstractTheliaSubActions {

	public function getSubActions() {
		return array(
			"create_rubrique" => array($this, "createRubrique"),
			"list_rubrique" => array($this, "listRubriques"),
		);
	}

	public function createRubrique() {
        TheliaApiTools::displayResult(array('status' => 'ok','rubrique' =>$rubrique));
	}

	public function listRubriques() {

	}
}

