<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_module_api.php";
    require_once __DIR__ . "/../../common/classes/module.php";

    class ModeleModuleAPI
    {
        public function __construct(){
        }

        public function liste_modules(){
            try{
                $liste_modules = Module::listeModules();
                Response::sendHttpBodyAndExit($liste_modules);
            }catch(PDOException $e){
                ErrorHandlerAPI::afficherErreur($e);
            }
        }
    }