<?php
    require_once __DIR__ . "/mod_profile.php";
    require_once __DIR__ . "/cont_profile.php";

    class ModProfile
    {
        public function __construct()
        {
            $cont = new ContProfile();
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : '';
            
            switch($action){
                case 'changer_mot_de_passe':
                    $cont->changer_mdp();
                break;
                default:
                    $cont->afficherProfile();
            }
        }
    }
