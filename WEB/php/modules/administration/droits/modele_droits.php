<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/Database.php";
    require_once __DIR__ . "/../../../common/classes/droits.php";

    class ModeleDroits extends Database
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function getListeDroits()
        {
            try{
                return Droits::getListeDroits();
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"afficher la liste des droits")
                );
            }
        }

        public function ajouterDroits(
            $nom_droits,
            $creation_utilisateurs,
            $creation_modules,
            $creation_cours,
            $creation_groupes,
            $modification_absences,
            $modification_droits,
            $modification_heures_travail,
            $statistiques
            ) {
            try{
                Droits::ajouterDroits(
                    $nom_droits,
                    $creation_utilisateurs,
                    $creation_modules,
                    $creation_cours,
                    $creation_groupes,
                    $modification_absences,
                    $modification_droits,
                    $modification_heures_travail,
                    $statistiques
                );
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>"ce droit")
                );

            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"créer un droit")
                );
            }
        }

        public function modifierDroits(
            $nom_droits,
            $creation_utilisateurs,
            $creation_modules,
            $creation_cours,
            $creation_groupes,
            $modification_absences,
            $modification_droits,
            $modification_heures_travail,
            $statistiques
            ) {
            try{
                $droit = new Droits($nom_droits);
                $droit->modifierDroits(
                    $creation_utilisateurs,
                    $creation_modules,
                    $creation_cours,
                    $creation_groupes,
                    $modification_absences,
                    $modification_droits,
                    $modification_heures_travail,
                    $statistiques
                );
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"ce droit", 'id'=>$nom_droits)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le droit', 'id'=>$nom_droits)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modification du droit")
                );
            }
        }

        public function supprimerDroits($nom_droits)
        {
            try{
                $droit = new Droits($nom_droits);
                $droit->supprimerDroits($nom_droits);
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce droit", 'id'=>$nom_droits)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le droit', 'id'=>$nom_droits) 
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Supprimer un droit")
                );
            }
        }




        public function getDroit($nom_droits)
        {
            try {
                $droit =  new Droits($nom_droits);
                $droit->getListeGroupes();
                $droit->getListeUtilisateurs();
                $droit->getData();
                return $droit;
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le droit', 'id'=>$nom_droits)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Accéder aux détails d'un droit")
                );
            }
        }
    }
