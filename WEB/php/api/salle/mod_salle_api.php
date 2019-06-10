<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_salle_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModSalleAPI extends ModAPIGenerique
    {
        public function __construct(){
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres("action");
            $modele = new ModeleSalleAPI();

            switch($action){
                case 'liste_salles':
                    $modele->liste_salles();
                break;

                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);
                break;
            }
        }
    }
