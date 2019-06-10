<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/classes/etudiant.php";
    require_once __DIR__ . "/../../../common/Database.php";

    class ModeleEtudiant extends Database
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function liste_etudiants()
        {
            try {
                return Etudiant::liste_etudiants();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }
        }


        public function ajouter_etudiant($num, $id_utilisateur)
        {
            try {
                Etudiant::ajouter_etudiant($num, $id_utilisateur);
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'cet étudiant')
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un étudiant")
                );            
            }
        }


        public function modifier_semestre_etudiant($num, $semestre){
            try {
                $etudiant = new Etudiant($num);
                $etudiant->modifierSemestreEtudiant($semestre);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"le semestre de cet étudiant", 'id'=>$num)
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modifier le semestre de l'étudiant")
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'étudiant', 'id'=>$num)
                );
            }

        }


        public function modifier_moyenne_etudiant($num, $moyenne, $est_valide){
            try {
                $etudiant = new Etudiant($num);
                $etudiant->modifierMoyenneEtudiant($moyenne, $est_valide);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"la moyenne de cet étudiant", 'id'=>$num)
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modifier la moyenne de l'étudiant")
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'étudiant', 'id'=>$num)
                );
            }catch(ParametresIncorrectes $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INVALID_MARK_ERROR_TITLE,
                    INVALID_MARK_ERROR_MESSAGE,
                    array('note'=>$moyenne)
                );
            }

        }

        public function supprimer_etudiant($num)
        {
            try {
                $etudiant = new Etudiant($num);
                $etudiant->supprimerEtudiant();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"cet étudiant", 'id'=>$num)
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Supprimer l'étudiant")
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'étudiant', 'id'=>$num)
                );
            }
        }





        public function getEtudiant($num)
        {
            try {
                $etudiant = new Etudiant($num);
                $etudiant->detailsEtudiant();
                return $etudiant;
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"détails de l'étudiant")
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'étudiant', 'id'=>$num)
                );
            }
        }

        public function getPeriodeCourante(){
            try{
                return explode(" => ", self::getDBYear());
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }


        public function listeSemestres(){
            try{
                return Semestre::liste_semestres();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }
    }
