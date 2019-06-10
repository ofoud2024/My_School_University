<?php
    require_once __DIR__ . "/../verify.php";

    define('HTTP_BAD_REQUEST', 400);
    define('INTERNAL_SERVER_ERROR', 500);
    define('HTTP_FORBIDDEN', 403);
    DEFINE('HTTP_NOT_FOUND', 404);
    define('PARAM_ERROR_MESSAGE', 'Les paramètres envoyés sont incorrects ou incomplets');
    define('REQUEST_METHOD_ERROR_MESSAGE', "Le type de requête utilisé n'est pas pris en charge par cette page");
    define('REQUEST_TYPE_ERROR_MESSAGE', 'Veuillez indiquer le but de la requête');
    
    class Response
    {

        /*
            - Envoie une erreur au client et arrête tous les traitements en cours sur le serveur
            - @param Integer error_code: Le code d'erreur.
            - @param String message : Le message à envoyer.
            - @param PDO db : L'instance de connexion à la base de donnée.
            - @param Fichier file : Le fichier en cours de traitement. 
        */
        public static function send_error($error_Code, $message, PDO $db = null, $file = null) : void
        {
            if ($db && $db->inTransaction()) {
                $db->rollback();
            }

            if ($file) {
                $file->destroy();
            }

            http_response_code($error_Code);
            
            echo $message;

            exit(1);
        }

        /*
            - Envoie un réponse positive au client
            - @param body: La réponse en Json.
        */
        public static function sendHttpBodyAndExit($body = null)
        {
            http_response_code(200);
            echo json_encode($body);
            exit(0);
        }


    }
