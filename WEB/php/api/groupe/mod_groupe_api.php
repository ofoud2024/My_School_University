<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_groupe_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModGroupeAPI extends ModAPIGenerique
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres('action');
            
            if($action !== 'liste_groupes'){
                $id_groupe = isset($_GET['id']) && is_numeric(htmlspecialchars($_GET['id'])) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('identifiant du groupe');
                $modele = new ModeleGroupeAPI($id_groupe);    
            }

            switch ($action) {
                case 'utilisateurs':
                    $modele->getUtilisateurs();
                break;

                case 'sous_groupes':
                    $modele->getSousGroupes();
                break;

                case 'tous_sous_groupes':
                    $modele->tousLesSousGroupes();
                break;

                case 'liste_groupes':
                    ModeleGroupeAPI::listeGroupes();
                break;


                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);

            }
        }
    }
