<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/classes/salle.php";

    class ModeleSalle 
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function liste_salles()
        {
            try {
                return Salle::liste_salles();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }
        }


        public function ajouter_salle($nom_salle, $nb_pc, $nb_places, $contient_projecteur)
        {
            try {
                Salle::ajouter_salle($nom_salle, $nb_pc, $nb_places, $contient_projecteur);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'cette salle')
                );                
            } catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter une salle")
                );            
            }
        }


        public function getSalle($nom_salle)
        {
            try {
                $salle = new Salle($nom_salle);
                $salle->detailsSalle();
                return $salle;
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'la salle', 'id'=>$nom_salle)
                );
            }
        }

        public function modifier_salle($nom_salle, $nb_pc, $nb_places, $contient_projecteur)
        {
            try {
                $salle = new Salle($nom_salle);
                $salle->modifierSalle($nb_pc, $nb_places, $contient_projecteur);
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"cette salle", 'id'=>$nom_salle)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'la salle', 'id'=>$nom_salle)
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modifier une salle")
                );            
            }
        }

        public function supprimer_salle($nom_salle)
        {
            try {
                $salle = new Salle($nom_salle);
                $salle->supprimerSalle();
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"cette salle", 'id'=>$nom_salle)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'la salle', 'id'=>$nom_salle)
                );                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"supprimer une salle")
                );            
            }
        }
        
    }
