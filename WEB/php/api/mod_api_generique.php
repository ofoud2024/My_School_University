<?php
    require_once __DIR__ . "/verify.php";

    class ModAPIGenerique{
        public function __construct(){}

        /*
            Exception levée, lorsqu'un des paramètres est manquants:
            @param parameter: Le nom du paramètre manquant
        */
        public function pasAssezDeParametres($parameter){
            ErrorHandlerAPI::afficherErreur(
                new ParametresInsuffisantsException(),
                NOT_ENOUGH_PARAM_TITLE,
                NOT_ENOUGH_PARAM_MESSAGE,
                array("parametre" => $parameter),
                HTTP_BAD_REQUEST
            );
        }
    }
    
?>