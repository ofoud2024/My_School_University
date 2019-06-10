<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_moodle_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModMoodleAPI extends ModAPIGenerique
    {
        public function __construct(){

            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres('action');
            $modele = new ModeleMoodleAPI();

            switch($action){ 

                case 'modules_enseignant': 
                    $modele->modules_enseignant();
                break;

                case 'groupes_module':
                    $module = isset($_GET['module']) ? htmlspecialchars($_GET['module']) : $this->pasAssezDeParametres('module'); 
                    $modele->groupes_module($module);
                break;

                case 'depots_enseignant':
                    $modele->depots_enseignant();
                break;

                case 'depots_etudiants':
                    $id_depot = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('identifiant du dépôt'); 
                    $modele->depots_etudiants($id_depot);
                break;

                case 'telecharger_depot':
                    $id_etudiant = isset($_GET['etudiant']) ? htmlspecialchars($_GET['etudiant']) : $this->pasAssezDeParametres('identifiant de l\'étudiant');
                    $id_depot = isset($_GET['depot']) ? htmlspecialchars($_GET['depot']) : $this->pasAssezDeParametres('identifiant du dépôt');
    
                    $modele->telecharger_depot_etudiant($id_etudiant, $id_depot);
                break;

                case 'telecharger_cours':
                    $id_cours = isset($_GET['id_cours']) ? htmlspecialchars($_GET['id_cours']) : $this->pasAssezDeParametres('identifiant du support');

                    $modele->telecharger_support_cours($id_cours);
                break;

                case 'modifier_note':
                    $id_etudiant = isset($_GET['etudiant']) ? htmlspecialchars($_GET['etudiant']) : $this->pasAssezDeParametres('identifiant de l\'étudiant');;
                    $id_depot = isset($_GET['depot']) ? htmlspecialchars($_GET['depot']) : $this->pasAssezDeParametres('identifiant du dépôt');;
                    $note = isset($_GET['note']) ? htmlspecialchars($_GET['note']) : $this->pasAssezDeParametres('note du dépôt');;
                    $commentaire = isset($_GET['commentaire']) ? htmlspecialchars($_GET['commentaire']) : "";
                    $modele->modifierNote($id_etudiant, $id_depot, $note, $commentaire);

                break;

                    
                
                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);
                break;
            }
        }
    }

?>