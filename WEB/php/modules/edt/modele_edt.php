<?php
    require_once __DIR__ . "/../../common/classes/user/utilisateur.php";
    require_once __DIR__ . "/../../common/classes/absence.php";

    class ModeleEdt {
        private $cont;

        public function __construct($cont){
            $this->cont = $cont;
        }

        public function semestreEtudiant(){
            $utilisateur_courant = Utilisateur::getUtilisateurCourant();
            
            if($utilisateur_courant){
                return $utilisateur_courant->informations_simples()['semestre'];
            }else{
                header('Location: index.php?module=connexion&action=afficherConnexion');
                exit(0);
            }

        }

        public function getStatus(){
            $num_etudiant = Etudiant::numEtudiantCourant();
            if($num_etudiant === false){
                try{
                    return "enseignant";
                }catch(PasEnseignantException $e){
                    return "";
                }
            }else{
                return "etudiant";
            }
        }

        public function absencesEtudiant(){
            try{
                $absences = array(
                    "liste_absences"=>Absence::absencesEtudiant(),
                    "somme_absences"=>Absence::sommeAbsencesEtudiant()
                );
                return $absences;
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEtudiantException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_STUDENT_ERROR_TITLE,
                    NOT_STUDENT_ERROR_MESSAGE
                );
            }
        }

        public function etudiantsSeance(){
            try{
                $liste_etudiants = Absence::etudiantsSeance();
                return $liste_etudiants;
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }
        

        public function modifier_absences($etudiants_absents){
            try{
                Absence::appliquerAbsences($etudiants_absents);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }

        }
    
    }
?>