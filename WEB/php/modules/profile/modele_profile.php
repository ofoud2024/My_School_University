<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/Database.php";
    require_once __DIR__ . "/../../common/classes/user/utilisateur.php";

    class ModeleProfile extends Database
    {
        private $cont;
        private $id_utilisateur;

        public function __construct($cont)
        {
            $this->cont = $cont;
            $this->id_utilisateur = Utilisateur::idUtilisateurCourant();
        }

        public function details_utilisateur(){
            try{
                $utilisateur = new Utilisateur($this->id_utilisateur);
                return $utilisateur->informations_utilisateur();
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$id_utilisateur)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder au profile de cet utilisateur")
                );
            }

        }

        public function changerMdp($mot_de_passe){
            try{
                $utilisateur = new Utilisateur($this->id_utilisateur);

                if(strlen($mot_de_passe) < 8){
                    ErrorHandler::afficherErreur(
                        new ParametresIncorrectes("Mot de passe < 8"),
                        PASSWORD_LENGTH_ERROR_TITLE, 
                        PASSWORD_LENGTH_ERROR_MESSAGE
                    );
                }
    
                return $utilisateur->modifierMDPUtilisateur($mot_de_passe);
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$id_utilisateur)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"changer le mot de passe de cet utilisateur")
                );
            }

        }

    }
