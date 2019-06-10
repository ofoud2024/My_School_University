<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "./../../common/classes/module.php";
    require_once __DIR__ . "./../../common/classes/moodle.php";
    require_once __DIR__ . "./../../common/classes/groupe.php";
    require_once __DIR__ . "./../../common/classes/moodleEtudiant.php";

    
    class ModeleMoodleAPI extends Database
    {
        public function modules_enseignant(){

            try{
                $enseignant = Enseignant::idEnseignantCourant();
                
                $result = Module::modulesEnseignant($enseignant);
                
                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }

        public function groupes_module($ref_module){
            try{
                $module     = new Module($ref_module);
                $semestre   = $module->getDetailsModule()['ref_semestre'];
                $requete    = "select distinct parent.id_groupe as id_semestre, parent.nom_groupe as nom_semestre, 
                                id_groupe_fils as id_fils, fils.nom_groupe as nom_groupe 
                                from sous_groupe inner join groupe as parent on(est_un_sous_groupe(id_groupe_parent, parent.id_groupe) or parent.id_groupe = id_groupe_parent)
                                inner join groupe as fils on(fils.id_groupe = id_groupe_fils) 
                                where parent.nom_groupe = :nom_groupe  order by fils.nom_groupe
                            ";
                $stmt = self::$db->prepare($requete);

                $stmt->bindValue(':nom_groupe', $semestre);

                $stmt->execute();
                
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                Response::sendHttpBodyAndExit($result);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur($e, UNKNOWN_MODULE_EXCEPTION_TITLE, UNKNOWN_MODULE_EXCEPTION_MESSAGE, array('ref'=>$ref_module), HTTP_BAD_REQUEST);
            }catch(PasEnseignantException $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE, array(), HTTP_BAD_REQUEST);
            }
        }

        public function depots_enseignant(){
            try{

                $depots = Moodle::liste_depots();

                $result = array_map(function($depot){
                    return array(
                        "id" => $depot["id_depot_exercice"],
                        "nom" => $depot["nom_support"],
                        "groupe" => $depot["nom_groupe"]
                    );
                },$depots);

                Response::sendHttpBodyAndExit($result);

            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE, array(), HTTP_BAD_REQUEST);
            }
        }

        public function depots_etudiants($id_depot){

            try{

                $depot = new Moodle($id_depot);

                $result = $depot->depots_etudiants();

                Response::sendHttpBodyAndExit($result);
                
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_YOUR_DEPOSIT_ERROR_TITLE, NOT_YOUR_DEPOSIT_ERROR_MESSAGE, array(), HTTP_BAD_REQUEST);
            }

        }

        public function telecharger_depot_etudiant($id_etudiant, $id_depot){
            try{
                $depot = new Moodle($id_depot);
                
                $depot_etudiant = $depot->getDepotEtudiant($id_etudiant);

                $fichier = new Fichier($depot_etudiant['lien_depot_etudiant']);

                $fichier->telecharger($depot_etudiant["pseudo_utilisateur"]."_".$depot_etudiant["nom_support"]);
    
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur($e, UNKNOWN_MODULE_EXCEPTION_TITLE, UNKNOWN_MODULE_EXCEPTION_MESSAGE, array('ref'=>$ref_module), HTTP_BAD_REQUEST);
            }catch(PasEnseignantException $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE, array(), HTTP_BAD_REQUEST);
            }
        }

        public function telecharger_support_cours($id_support){
            try{
                $support = MoodleEtudiant::fichier_support($id_support);
                
                $fichier = new Fichier($support['lien_fichier_support']);

                MoodleEtudiant::incrementer_nombre_visualisation($id_support);

                $fichier->telecharger($support['nom_support']);

                
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }

        public function modifierNote($id_etudiant, $id_depot, $note, $commentaire){
            try{
                $depot = new Moodle($id_depot);
                $depot->modifierNote($id_etudiant, $note , $commentaire);
                Response::sendHttpBodyAndExit(true);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur($e, UNKNOWN_MODULE_EXCEPTION_TITLE, UNKNOWN_MODULE_EXCEPTION_MESSAGE, array('ref'=>$ref_module), HTTP_BAD_REQUEST);
            }catch(PasEnseignantException $e){
                ErrorHandlerAPI::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE, array(), HTTP_BAD_REQUEST);
            }
        }
    }
    
?>