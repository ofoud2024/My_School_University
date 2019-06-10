<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/classes/module.php";

    class ModeleModule 
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }


        public function liste_modules(){
            try{
                return Module::listeModules();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }

        public function ajouter_module($ref, $nom, $coef, $heures_cm, $heures_td, $heures_tp, $couleur, $semestre,$abreviation){
            try{
                Module::ajouterModule($ref, $nom, $coef, $heures_cm , $heures_td, $heures_tp, $couleur, $semestre, $abreviation);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'ce module')
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Création du module")
                );
            }
        }

        public function getModule($id_module){
            try{
                $module = new Module($id_module);
                $module->getDetailsModule();
                $module->getEnseignantsModule();
                return $module;
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$id_module)
                );
            }
        }

        public function modifier_module($ref, $nom, $coef, $heures_cm, $heures_td, $heures_tp, $couleur, $abreviation){
            try{
                $module = new Module($ref);
                $module->modifierModule($nom, $coef, $heures_cm , $heures_td, $heures_tp, $couleur, $abreviation);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"ce module", 'id'=>$ref)
                );       
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref)
                );
            }
            catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Modification du module")
                );
            }
        }

        public function retirer_enseignant($ref_module, $id_enseignant){
            try{
                $module = new Module($ref_module);
                $module->retirerEnseignant($id_enseignant);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce module", 'id'=>$ref_module)
                );
                
            } catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref_module)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Suppression d'un enseignant du module")
                );
            }
        }

        public function ajouter_enseignant($ref_module, $id_enseignant, $est_responsable = false){
            try{
                $module = new Module($ref_module);
                $module->ajouterEnseignant($id_enseignant, $est_responsable);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'cet enseignant à ce module')
                );
            } catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref_module)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Ajouter un enseignant au groupe")
                );
            }
        }

        public function supprimer_module($ref_module){
            try{
                $module = new Module($ref_module);
                $module->supprimerModule();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce module", 'id'=>$ref_module)
                );
                
            } catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le module', 'id'=>$ref_module)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Supprimer le module")
                );
            }
        }

    }
