<?php
	include_once __DIR__ . '/modele_menu_externe.php';
	include_once __DIR__ . '/vue_menu_externe.php';

	if(!defined('CONST_INCLUDE')){
		die("Accès interdit");
	}

	class ContMenuExterne{

		private $modele;
		private $view;

		public function __construct(){
			$this->modele = new ModeleMenuExterne();
			$this->view = new VueMenuExterne();	
		}

		public function afficherMenu($active){
			$liste = $this->modele->getMenuList();
			$this->view->afficherMenu($liste, $active);
		}

	}
?>

