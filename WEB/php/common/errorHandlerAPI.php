<?php
    require_once __DIR__ . "/errorHandler.php";
    /*
        -Gestionnaire des exceptions pour les APIS
        -Au lieu de rediréger l'utilisateur, on lui envoie une réponse 
        contenant le code d'erreur et le message de l'éxception
    */
    
    class ErrorHandlerAPI extends ErrorHandler{
  
        /*
            - Gère une éxecption, puis redirige l'utilisateur vers la page d'erreur
            - @param Exception e: L'exception à logger
            - @param String titreErreur: Le titre de l'erreur
            - @param String message: Le message d'erreur à afficher
            - @param Json remplacements: L'objet contenant les associations clé=>valeur. 
                Les occurrences de {{clé}} dans le message d'erreur seront remplacés par leur valeur.  
            - @param Integer http_error_code: Le code d'erreur à envoyer
        */
        public static function afficherErreur($e, $titreErreur = DEFAULT_API_ERROR_TITLE, $messageErreur = DEFAULT_API_ERROR_MESSAGE, $remplacements = array(), $http_error_code = INTERNAL_SERVER_ERROR ){
            $message = $messageErreur;
            
            if(is_array($remplacements)){
                foreach($remplacements as $key=>$value){
                    $pattern = "{{{" . $key . "}}}";
                    $message = preg_replace($pattern, $value, $message);
                }
            }

            self::logException($e, $titreErreur);

            Response::send_error($http_error_code, $message);

        }

    }
?>