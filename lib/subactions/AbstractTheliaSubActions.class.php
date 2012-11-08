<?php

require_once(realpath(dirname(__FILE__)) . '/../../TheliaApi.class.php');

abstract class AbstractTheliaSubActions {

	protected $api = null;

	public function __construct(TheliaApi $api) {
		$this->api = $api;
	}

	/**
	 * Must return an assoc array with key as subaction name and value as callback array.
	 * See TheliaUserSubActions.class.php for implementation exemple.
	 */
	public abstract function getSubActions();


	protected function checkAuthenticationOrThrow() {
	    TheliaApiException::throwApiExceptionFaultUnless(
	        $this->api->checkAccess('clients',0,1),
	        TheliaApiException::ERROR,
	        TheliaApiException::E_unavailable);
	}



}
