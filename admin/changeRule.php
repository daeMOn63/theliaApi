<?php

include_once(realpath(dirname(__FILE__)) . '/../../../../fonctions/authplugins.php');
autorisation('theliaApi');

include_once(realpath(dirname(__FILE__)) . '/../TheliaApi.class.php');
include_once(realpath(dirname(__FILE__)) . '/../lib/TheliaApiAuth.class.php');
include_once(realpath(dirname(__FILE__)) . '/../../../../classes/Autorisation.class.php');
include_once(realpath(dirname(__FILE__)) . '/../../../../classes/Autorisationdesc.class.php');

if(!isset($lang)) $lang=$_SESSION["util"]->lang;
$id = lireParam('id', $id);
?>

<script type="text/javascript">
	function changer_droits(autorisation, mode, valeur){
		if(valeur != "")
			valeur = 1;
		else
			valeur = 0;

		$.ajax({type:'GET', url:'module.php?nom=theliaApi&view=ajax_rule', data:'autorisation='+autorisation+'&apiAuth=<?php echo $id; ?>' + '&mode=' + mode + '&valeur=' + valeur})
	}

</script>

<div id="contenu_int">
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="modules.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="module.php?nom=theliaApi" class="lien04"><?php echo trad('gestion_api', 'admin'); ?></a></p>

   <div class="entete_liste_config">
	<div class="titre"><?php echo trad('Droits_generaux', 'admin'); ?></div>
</div>
   <ul class="Nav_bloc_description">
		<li style="height:25px; width:258px;"><?php echo trad('Autorisation', 'admin'); ?></li>
		<li style="height:25px; width:100px; border-left:1px solid #96A8B5;"><?php echo trad('lecture', 'admin'); ?></li>
		<li style="height:25px; width:55px; border-left:1px solid #96A8B5;"><?php echo trad('ecriture', 'admin'); ?></li>
</ul>
   <div class="bordure_bottom">
 	<?php

	$autorisation = new Autorisation();

 	$query = "select * from $autorisation->table";
  	$resul = mysql_query($query, $autorisation->link);
  	$i=0;
  	while($row = mysql_fetch_object($resul)){
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;

			$autorisationdesc = new Autorisationdesc();
			$autorisationdesc->charger($row->id, $lang);

			$theliaApiAuth = new TheliaApiAuth();
			$theliaApiAuth->charger($row->id,$id);
 	 ?>
		<ul class="<?php echo $fond; ?>">
			<li style="width:250px;"><?php echo $autorisationdesc->titre; ?></li>
			<li style="width:93px; border-left:1px solid #96A8B5;"><input type="checkbox" onchange="changer_droits(<?php echo $row->id; ?>, 'read', this.checked)" <?php if($theliaApiAuth->read == 1) echo 'checked="checked"'; ?>></li>
			<li style="width:47px; border-left:1px solid #96A8B5;"><input type="checkbox" onchange="changer_droits(<?php echo $row->id; ?>, 'write', this.checked)" <?php if($theliaApiAuth->write == 1) echo 'checked="checked"'; ?> /></li>
		</ul>
	 <?php } ?>

	<br />
   </div>
   
</div>