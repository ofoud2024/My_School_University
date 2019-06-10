<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_utilisateur_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModUtilisateurAPI extends ModAPIGenerique
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres('action');
            
            $modele = new ModeleUtilisateurAPI();

            switch ($action) {
                case 'liste_utilisateurs':
                    $modele->getListeUtilisateurs();
                break;

                case 'pseudo_personnel':
                    $modele->getPseudoPersonnels();
                break;
                
                case 'pseudo_etudiant':
                    $modele->getPseudoEtudiants();
                break;


                case 'ville':
                    $modele->getVille();
                break;

                case 'pays':
                    $modele->getPays();
                break;

                case 'enseignants_possibles':
                    $ref_module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : die('pas de module');
                    $modele->getEnseignantsPossibles($ref_module);
                break;

                case 'enseignants_module':
                    $ref_module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : die('pas de module');
                    $modele->getEnseignantsModule($ref_module);
                break;

                case 'details_utilisateur_courant':
                    $modele->detailsUtilisateurCourant();
                break;



                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);

            }
        }
    }
