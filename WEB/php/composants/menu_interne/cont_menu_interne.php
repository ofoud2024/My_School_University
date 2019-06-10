<?php
    require_once __DIR__ . "/../../verify.php";

    include_once __DIR__ . '/vue_menu_interne.php';

    class ContMenuInterne
    {
        private $view;

        public function __construct()
        {
            $this->view = new VueMenuInterne();
        }

        public function afficherMenu()
        {
            $this->view->afficherMenu();
        }
    }
?>

