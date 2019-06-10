<?php
    require_once __DIR__ . "/../../verify.php";

    class ModeleMenuMoodle {

        private $cont;

        public function __construct($cont){
            $this->cont = $cont;
        }

        public function detailsUtilisateurCourant(){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            $resultat = array();

            if($utilisateur){
                try{
                    $details = $utilisateur->informations_utilisateur();
                    $resultat['est_etudiant'] = Etudiant::numEtudiantCourant() !== false;
                    $resultat['est_enseignant'] = false   ;
                    $resultat['nom'] = $details['nom_utilisateur'];
                    $resultat['prenom'] = $details['prenom_utilisateur'];
                    try{
                        Enseignant::idEnseignantCourant();
                        $resultat['est_enseignant'] = true;
                    }catch(PasEnseignantException $e){}        
                }catch(PDOException $e){
                    ErrorHandler::afficherErreur($e);
                }
            }else{
                ErrorHandler::afficherErreur(
                    new NonConnecterException(),
                    NOT_CONNECTED_EXCEPTION_TITLE,
                    NOT_CONNECTED_EXCEPTION_MESSAGE
                );
            }

            return $resultat;

        }


        public function getDroitsUtilisateurCourant(){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            try{
                return $utilisateur->droitsUtilisateur();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Accéder aux détails d'un autre utilisateur")
                );
            }
        }

    }
?>