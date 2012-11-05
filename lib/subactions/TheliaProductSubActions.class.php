<?php

require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Produit.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Produitdesc.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../exception/TheliaApiException.class.php');
require_once(realpath(dirname(__FILE__)) . '/../TheliaApiTools.class.php');

require_once(realpath(dirname(__FILE__)) . "/AbstractTheliaSubActions.class.php");


class TheliaProductSubActions extends AbstractTheliaSubActions {

	public function getSubActions() {
		return array(
			"create_product" => array($this, "createProduct"),
			"add_product_desc" => array($this, "addProductDesc"),
			"list_products" => array($this, "listProducts"),
			"delete_product" => array($this, "deleteProduct")
		);
	}

	public function createProduct() {
        TheliaApiException::throwApiExceptionFaultUnless(
                $this->api->checkAccess('clients',0,1),
                TheliaApiException::ERROR,
                TheliaApiException::E_unavailable);

        extract(TheliaApiTools::extractParam(array(
            'ref',
            'prix' => array('type' => 'float', 'required' => true),
            'ecotaxe' => array('type' => 'float', 'required' => false),
            'promo' => array('type' => 'boolean', 'default' => false),
            'prix2' => array('type' => 'float', 'required' => false),
            'nouveaute' => array('type' => 'boolean', 'default' => false),
            'stock' => array('type' => 'int', 'required' => true),
            'ligne' => array('type' => 'boolean', 'required' => true),
            'poids' => array('type' => 'float', 'required' => false),
            'tva' => array('type' => 'float', 'required' => false),
            'classement' => array('type' => 'int', 'required' => false),
        ),
        TheliaApiException::E_createProduct));


        $product = new Produit();
		TheliaApiException::throwApiExceptionFaultIf(
			$productDesc->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$ref.'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_createProduct,
            TheliaApiException::E_parameter,
            TheliaApiException::E_exists
        );

		$product->ref = $ref;
		$product->datemodif = date('Y-m-d H:i:s');
		$product->prix = $prix;
		$product->ecotaxe = $ecotaxe;
		$product->promo = $promo;
		$product->prix2 = $prix2;
		$product->nouveaute = $nouveaute;
		$product->stock = $stock;
		$product->ligne = $ligne;
		$product->poids = $poids;
		$product->tva = $tva;
		$product->classement = $classement;
		$product->id = $product->add();


        unset($product->bddvars);
        unset($product->link);
        unset($product->table);

        TheliaApiTools::displayResult(array('status' => 'ok','produit' =>$product));
	}

	public function addProductDesc() {
		TheliaApiException::throwApiExceptionFaultUnless(
            $this->api->checkAccess('clients',0,1),
            TheliaApiException::ERROR,
            TheliaApiException::E_unavailable);

        extract(TheliaApiTools::extractParam(array(
            'ref',
            'titre',
            'chapo' => 'optional',
            'description' => 'optional',
            'postscriptum' => 'optional',
            'lang' => array('type' => 'int', 'default' => 1),
        ),
        TheliaApiException::E_createProductDesc));

        // Retreive product
		$product = new Produit();
		TheliaApiException::throwApiExceptionFaultUnless(
			$product->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$ref.'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_createProductDesc,
            TheliaApiException::E_product,
            TheliaApiException::E_notFound
        );

		// Check that produitdesc does not exists yet for this lang
		$productDesc = new Produitdesc();
		TheliaApiException::throwApiExceptionFaultIf(
			$productDesc->getVars('SELECT * FROM '.$productDesc->table.' WHERE produit=\''.$product->id.'\' AND lang=\''. $lang.'\''),
			TheliaApiException::ERROR,
			TheliaApiException::E_createProductDesc,
			TheliaApiException::E_productdesc,
			TheliaApiException::E_exists
		);

		$productDesc->produit = $product->id;
		$productDesc->titre = $titre;
		$productDesc->chapo = $chapo;
		$productDesc->description = $description;
		$productDesc->postscriptum = $postscriptum;
		$productDesc->lang = $lang;
		$productDesc->id = $productDesc->add();

        unset($productDesc->bddvars);
        unset($productDesc->link);
        unset($productDesc->table);

        TheliaApiTools::displayResult(array('status' => 'ok','produitdesc' =>$productDesc));
	}


	public function listProducts() {

	}

	public function deleteProduct() {

	}
}