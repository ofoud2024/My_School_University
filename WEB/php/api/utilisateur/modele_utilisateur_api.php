<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/../../common/classes/module.php";
    require_once __DIR__ . "/../../common/classes/etudiant.php";
    require_once __DIR__ . "./../../common/classes/user/utilisateur.php";

    class ModeleUtilisateurAPI extends Database
    {

        public function __construct()
        {
        }   


        public function getListeUtilisateurs(){
            try{
                $liste_utilisateurs = Utilisateur::getListeUtilisateurs();
                $result = array_map(function($utilisateur){
                    return array(
                        "pseudo"=>$utilisateur['pseudo'],
                        "nom"=>$utilisateur['nom'],
                        "prenom"=>$utilisateur['prenom'],
                        "id"=>$utilisateur['id']
                    );
                },$liste_utilisateurs);

                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
            
        }

        public function getPseudoPersonnels()
        {

                    
            try {
                $result = Personnel::personnelsPossible();
                Response::sendHttpBodyAndExit($result);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            } catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des utilisateurs"),
                    HTTP_FORBIDDEN
                );                        
            }
        }


        public function getPseudoEtudiants(){
            try{
                $result = Etudiant::utilisateursPossible();
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des utilisateurs"),
                    HTTP_FORBIDDEN
                );                        
            }
        }

        public function getVille()
        {
            $requete = "select nom_ville, code_postal_ville from ville ";

            $stmt = self::$db->prepare($requete);
            
            try {
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();
    
                Response::sendHttpBodyAndExit($result);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }
        }


        public function getPays()
        {
            $requete = "select code_pays, nom_pays  from pays ";

            $stmt = self::$db->prepare($requete);
                    
            try {
                $stmt->execute();

                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();

                Response::sendHttpBodyAndExit($result);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }
        }


        public function getEnseignantsPossibles($ref_module){
            try{
                $module = new Module($ref_module);
                $result = $module->getEnseignantsAAjouter();
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref_module), 
                    HTTP_BAD_REQUEST
                );
            }
        } 


        public function getEnseignantsModule($ref_module){
            try{
                $module = new Module($ref_module);
                $result = $module->getEnseignantsModule();
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref_module), 
                    HTTP_BAD_REQUEST
                );
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des utilisateurs")
                );                        
            }
        } 

        public function detailsUtilisateurCourant(){
            try{
                $utilisateur = Utilisateur::getUtilisateurCourant();
                if($utilisateur){
                    Response::sendHttpBodyAndExit($utilisateur->informations_simples());
                }else{
                    ErrorHandlerAPI::afficherErreur(
                        new Exception("USER NOT FOUND"),
                        NOT_CONNECTED_EXCEPTION_TITLE,
                        NOT_CONNECTED_EXCEPTION_MESSAGE
                    );    
                }
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }

        }


    }
