<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_edt_api.php";
    require_once __DIR__ . "./../../common/classes/edt.php";

    class ModeleEdtAPI
    {
        public function __construct(){
        }

        public function liste_seances($date_debut, $date_fin){
            try{
                $result = Edt::liste_seances($date_debut, $date_fin);
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }



        public function ajouter_seance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle){
            try{
                $id_seance = Edt::ajouterSeance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle);
                Response::sendHttpBodyAndExit($id_seance);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e, EDT_INSERT_ERROR_TITLE, EDT_INSERT_ERROR_MESSAGE);
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_ENOUGH_ROLES_TITLE, NOT_ENOUGH_ROLES_TITLE, array('action'=>'création séance'), HTTP_FORBIDDEN);
            }
        }

        public function modifier_seance($id_seance, $date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle){
            try{    
                $seance = new Edt($id_seance);
                $id_seance = $seance->modifierSeance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle);
                Response::sendHttpBodyAndExit($id_seance);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    UPDATE_ERROR_TITLE, 
                    EDT_UPDATE_ERROR_MESSAGE
                );
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    array('action'=>'création séance'), 
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'la séance', 'id'=>$id_seance), 
                    HTTP_BAD_REQUEST
                );
            }
        }

        public function supprimer_seance($id_seance){
            try{    
                $seance = new Edt($id_seance);
                $seance->supprimerSeance();
                Response::sendHttpBodyAndExit($id_seance);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    DELETE_ERROR_MESSAGE, 
                    EDT_DELETE_ERROR_MESSAGE
                );
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    array('action'=>'création séance'), 
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'la séance', 'id'=>$id_seance), 
                    HTTP_BAD_REQUEST
                );
            }
        }


    }