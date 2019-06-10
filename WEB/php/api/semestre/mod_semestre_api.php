<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_semestre_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModSemestreAPI extends ModAPIGenerique
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres('action');

            $modele = new ModeleSemestreAPI();

            switch($action) {
                case 'liste_semestres':
                    $modele->getListeSemestre();
                break; 

                case 'groupes_semestre':
                    $id_semestre = isset($_GET['semestre']) ? htmlspecialchars($_GET['semestre']) : $this->pasAssezDeParametres('référence du semestre');
                    $modele->groupesSemestre($id_semestre);
                break;

                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);
                break;
            }
        }
    }