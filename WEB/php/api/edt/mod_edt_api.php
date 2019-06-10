<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_edt_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModEdtAPI extends ModAPIGenerique
    {
        public function __construct(){
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action']))         : $this->pasAssezDeParametres('action');
            $modele = new ModeleEdtAPI();

            switch($action){
                
                case 'liste_seances':
                    $date_debut = isset($_GET['date_debut']) ? htmlspecialchars($_GET['date_debut']) : $this->pasAssezDeParametres('date de début de la séance');
                    $date_fin = isset($_GET['date_fin'])     ? htmlspecialchars($_GET['date_fin'])   : $this->pasAssezDeParametres('date de fin de la séance');

                    $modele->liste_seances($date_debut, $date_fin);        
                break;

                case 'ajouter_seance': case 'modifier_seance': 
                    $id_seance      = isset($_GET['id_seance'])             ? htmlspecialchars($_GET['id_seance'])              : null;
                    $date_seance    = isset($_POST['date_seance'])          ? htmlspecialchars($_POST['date_seance'])           : $this->pasAssezDeParametres('jour séance'); 
                    $heure_depart   = isset($_POST['heure_depart_seance'])  ? htmlspecialchars($_POST['heure_depart_seance'])   : $this->pasAssezDeParametres('heure départ');
                    $duree          = isset($_POST['duree_seance'])         ? htmlspecialchars($_POST['duree_seance'])          : $this->pasAssezDeParametres('durée de séance');
                    $groupe         = isset($_POST['id_groupe'])            ? htmlspecialchars($_POST['id_groupe'])             : $this->pasAssezDeParametres('le groupe de la séance');
                    $enseignant     = isset($_POST['id_enseignant'])        ? htmlspecialchars($_POST['id_enseignant'])         : $this->pasAssezDeParametres('identifiant de l\'enseignant');
                    $module         = isset($_POST['ref_module'])           ? htmlspecialchars($_POST['ref_module'])            : $this->pasAssezDeParametres('Module enseigné dans la séance');
                    $salle          = isset($_POST['nom_salle'])            ? htmlspecialchars($_POST['nom_salle'])             : $this->pasAssezDeParametres('Salle');
                    
                    if($id_seance === null){
                        $modele->ajouter_seance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle);
                    }else{
                        $modele->modifier_seance($id_seance, $date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle);
                    }
                    
                break;
                
                case 'supprimer_seance':
                    $id_seance = isset($_GET['id_seance']) ? htmlspecialchars($_GET['id_seance'])              : null;
                    $modele->supprimer_seance($id_seance);    
                break;

                default:
                    Response::send_error(HTTP_BAD_REQUEST, INVALID_ACTION_ERROR_MESSAGE);
                break;
            }

        }
    }