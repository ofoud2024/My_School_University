<?php

require_once __DIR__ . "./../verify.php";

class ContGenerique
{
    public function __construct()
    {
    }

    /*
        Exception levée, lorsqu'un des paramètres est manquants:
        @param parameter: Le nom du paramètre manquant
    */
    public function pasAssezDeParametres($parameter){
        ErrorHandler::afficherErreur(
            new ParametresInsuffisantsException(),
            NOT_ENOUGH_PARAM_TITLE,
            NOT_ENOUGH_PARAM_MESSAGE,
            array("parametre" => $parameter) 
        );
    }


    /*
        -Génère un token pour la validation des formulaires
    */
    public function genererToken(){
        $token = Token::createToken();
        
        if(false === $token){

            ErrorHandler::afficherErreur(
                new Exception(TOKEN_CREATION_ERROR_MESSAGE),
                TOKEN_CREATION_ERROR_TITLE, 
                TOKEN_CREATION_ERROR_MESSAGE
            );        
            
        }
        return $token;
    }

    /*
        -Valide un token
        -Déclenche une erreur si le token n'est pas valide
    */

    public function validerToken(){
        $token = "";

        if(isset($_GET['token'])){
            $token = $_GET['token'];
        }else if(isset($_POST['token'])){
            $token = $_POST['token'];
        }

        if(!Token::validateToken($token)){
            $this->pasAssezDeParametres("Token");
        }
    }
}
