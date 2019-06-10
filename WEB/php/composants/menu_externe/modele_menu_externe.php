<?php
	if(!defined('CONST_INCLUDE')){
		die("Accès interdit");
	}


	class ModeleMenuExterne{

		public function getMenuList(){
			return array("Accueil", "Formation", "Etudiants", "Inscription", "Contact");
		}

	}
?>

