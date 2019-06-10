<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/../../common/classes/salle.php";

    class ModeleSalleAPI
    {
        public function __construct(){
        }

        public function liste_salles(){
            try{
                $liste_salles = Salle::liste_salles();
                Response::sendHttpBodyAndExit($liste_salles);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }
    }