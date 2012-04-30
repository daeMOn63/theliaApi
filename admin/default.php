<?php
include_once(realpath(dirname(__FILE__)) . '/../../../../fonctions/authplugins.php');
autorisation('theliaApi');

include_once(realpath(dirname(__FILE__)) . '/../TheliaApi.class.php');

$action = lireParam('action', 'string');

function modifier($id,$firstname,$lastname,$login,$password1,$password2)
{
    $theliaApi = new TheliaApi();
    
    if($theliaApi->charger_id($id))
    {
        $theliaApi->firstname = $firstname;
        $theliaApi->lastname = $lastname;
        $theliaApi->login = $login;
        
        if(!empty($password1) && $password1 == $password2)
        {
            $theliaApi->setPassword($password1);
        }
        
        $theliaApi->maj();
    }
}

function ajouter($firstname,$lastname,$login, $password1,$password2)
{
    if($password1 != $password2 || empty($password1))
    {
        return false;
    }
    
    $theliaApi = new TheliaApi();
    $theliaApi->firstname = $firstname;
    $theliaApi->lastname = $lastname;
    $theliaApi->login = $login;
    $theliaApi->password = $password1;
    $theliaApi->add();
}

if(!empty($action))
{
    switch($action)
    {
        case 'modifier':
            modifier(lireParam('id','int'), lireParam('firstname','string'), lireParam('lastname','string'), lireParam('login','string'), lireParam('password1','string'), lireParam('password2','string'));
            break;
        case 'ajouter':
            ajouter(lireParam('firstname','string'), lireParam('lastname','string'), lireParam('login','string'), lireParam('password1','string'), lireParam('password2', 'string'));
            break;
    }
}

?>
<script type="text/javascript">
	function valid(admin){
            document.getElementById('formadmin' + admin).submit();
	}

	function ajout(){

		if(document.getElementById('password1').value == document.getElementById('password2').value && document.getElementById('password1').value != "")
			document.getElementById('formadmin').submit();
		else{
			alert("Veuillez verifier votre mot de passe");
			return false;
		}
	}

</script>
<div id="contenu_int">
   <p align="left"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a>  <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="module_liste.php" class="lien04"><?php echo trad('Modules', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /> <a href="module.php?nom=theliaApi" class="lien04"><?php echo trad('gestion_api', 'admin'); ?></a></p>

<!-- bloc dŽclinaisons / colonne gauche -->

<div class="entete_liste_config">
	<div class="titre"><?php echo trad('LISTE_API', 'admin'); ?></div>
	<div class="fonction_ajout"><a href="#" onclick="$('#ajout_admin').show()"><?php echo trad('AJOUTER_ADMINISTRATEUR', 'admin'); ?></a></div>
</div>
<ul class="Nav_bloc_description">
		<li style="height:25px; width:158px;"><?php echo trad('Nom', 'admin'); ?></li>
		<li style="height:25px; width:157px; border-left:1px solid #96A8B5;"><?php echo trad('Prenom', 'admin'); ?></li>
		<li style="height:25px; width:117px; border-left:1px solid #96A8B5;"><?php echo trad('Identifiant', 'admin'); ?></li>
		<li style="height:25px; width:137px; border-left:1px solid #96A8B5;"><?php echo trad('Mdp', 'admin'); ?></li>
		<li style="height:25px; width:137px; border-left:1px solid #96A8B5;"><?php echo trad('Confirmation', 'admin'); ?></li>
		<li style="height:25px; width:30px;">&nbsp;</li>
                
</ul>
<div class="bordure_bottom">
 	<?php

	$api = new TheliaApi();

 	$query = "select * from $api->table";
  	$resul = mysql_query($query, $api->link);
  	$i=0;
  	while($row = mysql_fetch_object($resul)){
			if(!($i%2)) $fond="ligne_claire_rub";
  			else $fond="ligne_fonce_rub";
  			$i++;
 	 ?>
    <form action="module.php?nom=theliaApi" id="formadmin<?php echo($row->id); ?>" method="post" onsubmit="valid('<?php echo $row->id; ?>');return false;">
		<ul class="<?php echo $fond; ?>">
			<li style="width:150px;"><input name="firstname" type="text" class="form" value="<?php echo  htmlspecialchars($row->lastname); ?>" style="width:150px;"  /></li>
			<li style="width:150px; border-left:1px solid #96A8B5;"><input name="lastname" type="text" class="form" value="<?php echo  htmlspecialchars($row->firstname); ?>" style="width:150px;"  /></li>
			<li style="width:110px; border-left:1px solid #96A8B5;"><input name="login" type="text" class="form" value="<?php echo  htmlspecialchars($row->login); ?>" style="width:110px;" /></li>
			<li style="width:130px; border-left:1px solid #96A8B5;"><input name="password1" id="password1<?php echo($row->id); ?>" type="password" value="" class="form" style="width:130px;" onclick="this.value='';" /></li>
			<li style="width:130px; border-left:1px solid #96A8B5;"><input name="password2" id="password2<?php echo($row->id); ?>" type="password" value="" class="form" style="width:130px;" onclick="this.value='';" /></li>
			<li style="width:50px; border-left:1px solid #96A8B5;"><a href="#" onclick="valid('<?php echo $row->id; ?>');return false;"><?php echo trad('modifier', 'admin'); ?></a></li>
                        <li style="width:150px; border-left:1px solid #96A8B5;"><a href="module.php?nom=theliaApi&view=change_rules&id=<?php echo $row->id; ?>" ><?php echo trad('modifier_droits', 'admin'); ?></a></li>
		</ul>
 	<input type="hidden" name="action" value="modifier" />
   	<input type="hidden" name="id" value="<?php echo($row->id); ?>" />
   	</form>
	 <?php } ?>
</div>

<div class="bordure_bottom" id="ajout_admin" style="display: none;" >

   

		<div class="entete_liste_config" style="margin-top:10px;">
			<div class="titre"><?php echo trad('AJOUT_ADMINISTRATEUR', 'admin'); ?></div>
		</div>
		<ul class="Nav_bloc_description">
			<li style="height:25px; width:158px;">Nom</li>
			<li style="height:25px; width:157px; border-left:1px solid #96A8B5;"><?php echo trad('Prenom', 'admin'); ?></li>
			<li style="height:25px; width:117px; border-left:1px solid #96A8B5;"><?php echo trad('Identifiant', 'admin'); ?></li>
			<li style="height:25px; width:137px; border-left:1px solid #96A8B5;"><?php echo trad('Mdp', 'admin'); ?></li>
			<li style="height:25px; width:137px; border-left:1px solid #96A8B5;"><?php echo trad('Confirmation', 'admin'); ?></li>
			<li style="height:25px; width:30px; border-left:1px solid #96A8B5;">&nbsp;</li>
		</ul>
                <form action="module.php?nom=theliaApi" id="formadmin" method="post" onsubmit="ajout();return false;">
                <input type="hidden" name="action" value="ajouter" />
		<ul class="ligne_claire_rub">
			<li style="width:150px;"><input name="firstname" type="text" class="form" style="width:150px;" /></li>
			<li style="width:150px; border-left:1px solid #96A8B5;"><input name="lastname" type="text" class="form" style="width:150px;" /></li>
			<li style="width:110px; border-left:1px solid #96A8B5;"><input name="login" type="text" class="form" style="width:110px;" /></li>
			<li style="width:130px; border-left:1px solid #96A8B5;"><input name="password1" id="password1" type="password" class="form" style="width:130px;" /></li>
			<li style="width:130px; border-left:1px solid #96A8B5;"><input name="password2" id="password2" type="password" class="form" style="width:130px;" onclick="this.value='';" /></li>
			<li style="width:30px; border-left:1px solid #96A8B5;"><a href="#" onclick="ajout();return false;"><?php echo trad('ajouter', 'admin'); ?></a></li>
		</ul>
                </form>
</div>


</div>