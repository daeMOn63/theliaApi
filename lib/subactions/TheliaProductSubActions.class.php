<?php

require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Produit.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../../../../classes/Produitdesc.class.php');
require_once(realpath(dirname(__FILE__)) . '/../../exception/TheliaApiException.class.php');
require_once(realpath(dirname(__FILE__)) . '/../TheliaApiTools.class.php');

require_once(realpath(dirname(__FILE__)) . "/AbstractTheliaSubActions.class.php");

class TheliaProductSubActions extends AbstractTheliaSubActions {

	protected $productParams = array(
        'ref',
        'prix' 			=> array('type' => 'float', 'required' => false),
        'ecotaxe' 		=> array('type' => 'float', 'required' => false),
        'promo' 		=> array('type' => 'boolean', 'default' => false),
        'prix2' 		=> array('type' => 'float', 'required' => false),
        'nouveaute' 	=> array('type' => 'boolean', 'default' => false),
        'stock' 		=> array('type' => 'int', 'required' => false),
        'ligne' 		=> array('type' => 'boolean', 'required' => false),
        'poids' 		=> array('type' => 'float', 'required' => false),
        'tva' 			=> array('type' => 'float', 'required' => false),
        'classement' 	=> array('type' => 'int', 'required' => false),
    );

	protected $productDescParams = array(
        'ref',
        'titre'			=> 'optional',
        'chapo' 		=> 'optional',
        'description' 	=> 'optional',
        'postscriptum'	=> 'optional',
        'lang' 			=> array('type' => 'int', 'default' => 1)
    );

	protected $productImgParams = array(
		"ref"			=> 'optional',
		'titre'			=> 'optional',
        'chapo' 		=> 'optional',
        'description' 	=> 'optional',
        'lang' 			=> array('type' => 'int', 'default' => 1)
	);

	protected $productImgUpdateParams = array(
		'image_id',
		'titre'			=> 'optional',
        'chapo' 		=> 'optional',
        'description' 	=> 'optional',
        'lang' 			=> array('type' => 'int', 'default' => 1)
	);

	public function getSubActions() {
		return array(
			"create_product" 			=> array($this, "createProduct"),
			"update_product" 			=> array($this, "updateProduct"),
			"update_product_desc" 		=> array($this, "updateProductDesc"),
			"add_product_picture"	 	=> array($this, "addProductPicture"),
			"update_product_picture" 	=> array($this, "updateProductPicture"),
		);
	}

	public function createProduct() {

		$this->checkAuthenticationOrThrow();

        // switch on required fields on creation
        $this->productParams["prix"]["required"] = true;
        $this->productParams["stock"]["required"] = true;
        $this->productParams["ligne"]["required"] = true;

      	$params = TheliaApiTools::extractParam($this->productParams, TheliaApiException::E_productSubActions);

        $product = new Produit();
		TheliaApiException::throwApiExceptionFaultIf(
			$product->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$params["ref"].'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_parameter,
            TheliaApiException::E_exists
        );

		foreach($params as $name => $value) {
			$product->$name = $value;
		}

		$product->datemodif = date('Y-m-d H:i:s');
		$product->id = $product->add();

        unset($product->bddvars);
        unset($product->link);
        unset($product->table);

        TheliaApiTools::displayResult(array('status' => 'ok','produit' =>$product));
	}

	public function updateProduct() {
		$this->checkAuthenticationOrThrow();

        $params = TheliaApiTools::extractParam($this->productParams, TheliaApiException::E_productSubActions);

        // retreive product from ref or throw if not found
        $product = new Produit();
		TheliaApiException::throwApiExceptionFaultUnless(
			$product->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$params["ref"].'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_product,
            TheliaApiException::E_notFound
        );

		foreach($params as $name => $value) {
			$product->$name = $value;
		}
		$product->maj();

        unset($product->bddvars);
        unset($product->link);
        unset($product->table);

        TheliaApiTools::displayResult(array('status' => 'ok','produit' =>$product));
	}

	public function updateProductDesc() {

		$this->checkAuthenticationOrThrow();

        $params = TheliaApiTools::extractParam($this->productDescParams, TheliaApiException::E_productSubActions);

        // Retreive product
		$product = new Produit();
		TheliaApiException::throwApiExceptionFaultUnless(
			$product->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$params["ref"].'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_product,
            TheliaApiException::E_notFound
        );
		unset($params["ref"]);

		// Check that produitdesc does not exists yet for this lang
		$productDesc = new Produitdesc();
		$productDesc->getVars('SELECT * FROM '.$productDesc->table.' WHERE produit=\''.$product->id.'\' AND lang=\''. $params["lang"].'\'');

		foreach($params as $name => $value) {
			$productDesc->$name = $value;
		}

		if(!isset($productDesc->id)) {
			$productDesc->produit = $product->id;
			$productDesc->id = $productDesc->add();
		}else{
			$productDesc->maj();
		}

        unset($productDesc->bddvars);
        unset($productDesc->link);
        unset($productDesc->table);

        TheliaApiTools::displayResult(array('status' => 'ok','produitdesc' =>$productDesc));
	}


	public function addProductPicture() {

		$this->checkAuthenticationOrThrow();

		$this->productImgParams["ref"] = array("required" => true);

        $params = TheliaApiTools::extractParam($this->productImgParams, TheliaApiException::E_productSubActions);

 		// Retreive product
		$product = new Produit();
		TheliaApiException::throwApiExceptionFaultUnless(
			$product->getVars('SELECT * FROM '.$product->table.' WHERE ref=\''.$params["ref"].'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_product,
            TheliaApiException::E_notFound
        );
		unset($params["ref"]);

		TheliaApiException::throwApiExceptionFaultUnless(
			isset($_FILES["img"]),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_parameter,
            TheliaApiException::E_missing
        );
		$filename = $_FILES["img"]["name"];
		$tmpPath = $_FILES["img"]["tmp_name"];

		$image = new Image();
		$image->produit = $product->id;
		$image->fichier = $filename;
		$image->id = $image->add();
		// TODO : add title / lang / desc to parameter and save ImageDesc.
		$imageDesc = new Imagedesc();
		$imageDesc->image = $image->id;
		foreach($params as $name => $value ) {
			$imageDesc->$name = $value;
		}
		$imageDesc->id = $imageDesc->add();

		// Copy image to product images folder
		TheliaApiException::throwApiExceptionFaultUnless(
			move_uploaded_file($tmpPath, $this->getProductImagesFolderPath().'/'.$filename),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_image,
            TheliaApiException::E_io
        );

        unset($imageDesc->bddvars);
        unset($imageDesc->link);
        unset($imageDesc->table);

        TheliaApiTools::displayResult(array('status' => 'ok','imageDesc' =>$imageDesc));
	}

	public function updateProductPicture() {

		$this->checkAuthenticationOrThrow();

        $params = TheliaApiTools::extractParam($this->productImgUpdateParams, TheliaApiException::E_productSubActions);


        // Retreive picture
        $image = new Image();
        TheliaApiException::throwApiExceptionFaultUnless(
			$image->getVars('SELECT * FROM '.$image->table.' WHERE id=\''.$params["image_id"].'\''),
            TheliaApiException::ERROR,
            TheliaApiException::E_productSubActions,
            TheliaApiException::E_image,
            TheliaApiException::E_notFound
        );
        unset($params['image_id']);

		$imageDesc = new Imagedesc();
		$imageDesc->getVars('SELECT * FROM '.$imageDesc->table.' WHERE image=\''.$image->id.'\' AND lang=\''.$params["lang"].'\'');

		foreach($params as $name => $value) {
			$imageDesc->$name = $value;
		}

		if($imageDesc->id) {
			$imageDesc->maj();
		}else{
			$imageDesc->image = $image->id;
			$imageDesc->id = $imageDesc->add();
		}

        unset($imageDesc->bddvars);
        unset($imageDesc->link);
        unset($imageDesc->table);

        TheliaApiTools::displayResult(array('status' => 'ok','imageDesc' =>$imageDesc));
	}

	// Redefined here since ImageAdmin class does not really seems "usable" outside its original scope...
	protected function getProductImagesFolderPath() {
		return __DIR__."/../../../../gfx/photos/produit";
	}
}