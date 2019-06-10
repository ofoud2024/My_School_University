<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/classes/semestre.php";
    require_once __DIR__ . "/../../../common/Database.php";

    class ModeleSemestre
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function liste_semestres()
        {
            try {
                return Semestre::liste_semestres();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }
        }


        public function ajouter_semestre($ref, $nom, $points_ets, $periode)
        {
            try {
                Semestre::ajouter_semestre($ref, $nom, $points_ets, $periode);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Ajouter un semestre")
                );            
            }catch(ParametresIncorrectes $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INVALID_SEMESTER_PERIOD_TTILE, 
                    INVALID_SEMESTER_PERIOD_MESSAGE
                );                
            }
        }


        public function getSemestre($ref)
        {
            try {
                $semestre = new Semestre($ref);

                $semestre->detailsSemestre();
                $semestre->anneesSemestre();
                $semestre->etudiantsSemestre();
            
                return $semestre;
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Accéder aux détails du semestre")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le semestre', 'id'=>$ref)
                );                
            }
        }


        public function modifier_semestre($ref, $nom, $pts_ets)
        {
            try {
                $semestre = new Semestre($ref);
                $semestre->modifierSemestre($nom, $pts_ets);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"ce semestre", 'id'=>$ref)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Modifier le semestre")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le semestre', 'id'=>$ref)
                );                
            }
        }

        public function retirer_etudiant($ref, $num_etudiant)
        {
            try {
                $semestre = new Semestre($ref);
                $semestre->retirerEtudiant($num_etudiant);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"cet étudiant du semestre", 'id'=>$ref)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Retirer un étudiant du semestre")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le semestre', 'id'=>$ref)
                );                
            }
        }


        public function supprimer_semestre($ref)
        {
            try {
                $semestre = new Semestre($ref);
                $semestre->supprimerSemestre();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce semestre", 'id'=>$ref)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"supprimer le semestre")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le semestre', 'id'=>$ref)
                );                
            }
        }


        public function annee_courante()
        {
            try{
                return Database::getDBYear();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }

    }
