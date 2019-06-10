<?php
    require_once __DIR__ . "/../verify.php";

    class ModeleGroupeAPI 
    {
        private $id_groupe;

        public function __construct($id_groupe)
        {
            $this->id_groupe = $id_groupe;
        }

        public function getUtilisateurs()
        {

            try {
                $groupe = new Groupe($this->id_groupe);
                Response::sendHttpBodyAndExit($groupe->utilisateursPossibles());
            } catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            } catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    array('action'=>'utilisateurs du groupe'), 
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$this->id_groupe), 
                    HTTP_BAD_REQUEST
                );
            }
        }


        //Renvoie la liste des groupes commencant par debut
        //et qui ne sont pas parent du groupe défini par son id
        public function getSousGroupes()
        {
            try {
                $groupe = new Groupe($this->id_groupe);
                $resultat = $groupe->groupesPossibles();
                Response::sendHttpBodyAndExit($resultat);
            } catch (PDOException $e) {
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    array('action'=>'accéder aux groupe'), 
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$this->id_groupe), 
                    HTTP_BAD_REQUEST
                );
            }
        }


        public function tousLesSousGroupes(){
            try{
                $groupe = new Groupe($this->id_groupe);
                
                $result = $groupe->tousLesSousGroupes();
    
                Response::sendHttpBodyAndExit($result);
    
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandlerAPI::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    array('action'=>'accéder au groupe'), 
                    HTTP_FORBIDDEN
                );
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$this->id_groupe), 
                    HTTP_BAD_REQUEST
                );
            }

        }


        public static function listeGroupes(){
            try{
                $result = Groupe::getListeGroupes();
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }

    }
