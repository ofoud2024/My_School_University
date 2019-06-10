<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "./../../common/classes/semestre.php";

    class ModeleSemestreAPI extends Database
    {
        public function getListeSemestre(){
            try{
                $result = Semestre::liste_semestres();
                Response::sendHttpBodyAndExit(array_map(function($el){
                    return array(
                        "nom"=>$el['nom_semestre'],
                        "ref"=>$el['ref_semestre']
                    );
                },$result));
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }

        public function groupesSemestre($id_semestre){
            try{
                $semestre = new Semestre($id_semestre);
                $result = $semestre->groupes_semestre();
                Response::sendHttpBodyAndExit($result);                
            }catch(Exception $e){
                ErrorHandlerAPI::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandlerAPI::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le semestre', 'id'=>$id_semestre), 
                    HTTP_BAD_REQUEST
                );

            }
        }

    }
