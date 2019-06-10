<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . '/vue_menu_moodle.php';
    require_once __DIR__ . "/modele_menu_moodle.php";

    if (!defined('CONST_INCLUDE')) {
        die("Accès interdit");
    }

    class ContMenuMoodle
    {
        private $view;
        private $modele;

        public function __construct()
        {
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : "";
            $module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : "";
            $type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : "";

            $this->modele = new ModeleMenuMoodle($this);
            $this->view = new VueMenuMoodle($module, $action, $type);
        }

        public function afficherMenu()
        {
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : "";
            
            $details = $this->modele->detailsUtilisateurCourant();
            
            $droits = $this->modele->getDroitsUtilisateurCourant();

            $this->view->afficherMenu($action, $details, $droits);
        }

    }
?>

