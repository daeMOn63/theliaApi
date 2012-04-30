<?php
include_once(realpath(dirname(__FILE__)) . '/../../../fonctions/authplugins.php');
autorisation('theliaApi');

$view = lireParam('view','string');

switch($view)
{
    case 'change_rules':
        include_once(realpath(dirname(__FILE__)) . '/admin/changeRule.php');
        break;
    case 'ajax_rule':
        include_once(realpath(dirname(__FILE__)) . '/admin/ajax/changeRule.php');
        break;
    default : 
        include_once(realpath(dirname(__FILE__)) . '/admin/default.php');
        break;
}