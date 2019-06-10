<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/Database.php";
    require_once __DIR__ . "/../../../common/classes/groupe.php";
    require_once __DIR__ . "/../../../common/classes/droits.php";

    class ModeleGroupe extends Database
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function getListeGroupes()
        {
            try{    
                return Groupe::getListeGroupes();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }

        public function getListeDroits()
        {
            try{
                $liste_droits = array();

                foreach (Droits::getListeDroits() as $droits) {
                    array_push($liste_droits, $droits['nom_droits']);
                }
    
                return $liste_droits;    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un groupe")
                );            
            }
        }

        public function ajouterGroupe($nom, $droits)
        {
            try{
                Groupe::ajouterGroupe($nom, $droits);
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'ce groupe')
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un groupe")
                );            
            }
        }

        public function detailsGroupe($id_groupe)
        {
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->detailsGroupe();
                $groupe->utilisateursGroupe();
                $groupe->sousGroupes();
                return $groupe;    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder aux détails du groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_groupe)
                );                
            }
        }


        public function retirerUtilisateur($id_utilisateur, $id_groupe)
        {
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->retirerUtilisateur($id_utilisateur);    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"cet utilisateur du groupe", 'id'=>$id_groupe)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"retirer l'utilisateur du groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_utilisateur)
                );                
            }
        }

        public function retirerSousGroupe($sous_groupe, $id_groupe)
        {
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->retirerSousGroupe($sous_groupe);    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce sous groupe", 'id'=>$sous_groupe)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"retirer le sous groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_groupe)
                );                
            }
        }

        public function ajouterSousGroupe($sous_groupe, $id_groupe)
        {
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->ajouterSousGroupe($sous_groupe);    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'ce groupe')
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un sous groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_groupe)
                );                
            }
        }

        public function ajouterUtilisateur($id_utilisateur, $id_groupe)
        {
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->ajouterUtilisateur($id_utilisateur);    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'cet utilisateur au groupe')
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un utilisateur au groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_groupe)
                );                
            }
        }



        public function supprimerGroupe($id_groupe){
            try{
                $groupe = new Groupe($id_groupe);
                $groupe->supprimer();    
            }catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce groupe", 'id'=>$id_groupe)
                );
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"supprimer le groupe")
                );            
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le groupe', 'id'=>$id_groupe)
                );                
            }
        }
    }
