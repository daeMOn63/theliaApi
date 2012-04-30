<?php
include_once(realpath(dirname(__FILE__)) . '/../../../../../fonctions/authplugins.php');
autorisation('theliaApi');

include_once(realpath(dirname(__FILE__)) . '/../../TheliaApi.class.php');
include_once(realpath(dirname(__FILE__)) . '/../../lib/TheliaApiAuth.class.php');

$autorisation = lireParam('autorisation','int');
$apiAuth = lireParam('apiAuth','int');
$mode = lireParam('mode','string');
$valeur = lireParam('valeur','int');

$theliaApiAuth = new TheliaApiAuth();

if(!$theliaApiAuth->charger($autorisation, $apiAuth))
{
    $theliaApiAuth->autorisation_id = $autorisation;
    $theliaApiAuth->thelia_api_id = $apiAuth;
    $theliaApiAuth->read = 0;
    $theliaApiAuth->write = 0;
}

switch($mode)
{
    case 'read':
        $theliaApiAuth->read = $valeur;
        break;
    case 'write':
        $theliaApiAuth->write = $valeur;
        break;
}
if(!$theliaApiAuth->id)
{
    $theliaApiAuth->add();
}
else
{
    $theliaApiAuth->maj();
}

?>
