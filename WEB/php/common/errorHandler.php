<?php
    class ErrorHandler{
        private static $normalLogFile = __DIR__ . "./../log/normal/"; 
        private static $securityLogFile = __DIR__ . "./../log/security/";
        
        /*
            - Gère une éxecption, puis redirige l'utilisateur vers la page d'erreur
            - @param Exception e: L'exception à logger
            - @param String titreErreur: Le titre de l'érreur
            - @param String message: Le message d'erreur à afficher
            - @param Json remplacements: L'objet contenant les associations clé=>valeur. 
                Les occurrences de {{clé}} dans le message d'érreur seront remplacés par leur valeur.  
        */
        public static function afficherErreur($e, $titreErreur = DEFAULT_ERROR_TITLE, $messageErreur = DEFAULT_ERROR_MESSAGE, $remplacements = array() ){

            $message = $messageErreur;
            
            if(is_array($remplacements)){
                foreach($remplacements as $key=>$value){
                    $pattern = "{{{" . $key . "}}}";
                    $message = preg_replace($pattern, $value, $message);
                }
            }

            $new_url = "index.php?module=error&title=".$titreErreur."&message=".$message;

            self::logException($e, $titreErreur);

            self::redirect($new_url);

            header('Location: ' . $new_url);
 
            exit(0);
        }


        /*
            Redirige l'utilisateur vers la page d'erreur
        */

        private static function redirect($url){
            if($_SERVER['REQUEST_METHOD'] === 'GET'){
                array_shift($_SESSION['historique']);
            }
            header('Location: ' . $new_url);
        }

        /*
            Log l'exception dans le fichier du log correspondant
        */

        private static function log($file, $exception, $titre){
            $id_utilisateur = Utilisateur::getUtilisateurCourant() ? Utilisateur::getUtilisateurCourant()->getIdUtilisateur() : "déconnecté" ;
            
            if($file){
                fputs($file, "\n\n");
                fputs($file, "*************************************************\n");
                fputs($file, "Utilisateur   :   " . $id_utilisateur . "\n");
                fputs($file, "Titre         :   " . $titre . "\n");
                fputs($file, "Date          :   " . date('d-m-Y G:i:s') . "\n");
                fputs($file, "Nom Exception :   " . get_class($exception) . "\n");
                fputs($file, "Message       :   " . $exception->getMessage() . "\n");
                fputs($file, "File          :   " . $exception->getFile() . "\n");
                fputs($file, "Line          :   " . $exception->getLine() . "\n");
                fputs($file, "*************************************************\n");
                fputs($file, "\n\n");
            }

            fclose($file);        
        }



        /*
            Le nom du fichier de log normal 
        */

        private static function getNormalLogFile(){
            $date = date('y_m_d');
            $handle = fopen(self::$normalLogFile . $date, "a");
            return $handle;
        }


        /*
            Le nom du fichier de log pour la sécurité
        */

        private static function getSecurityLogFile(){
            $date = date('y_m_d');
            $handle = fopen(self::$securityLogFile . $date, "a");
            return $handle;
        }


        /*
            Log l'éxception dans le fichier de log normal, 
            puis dans le fichier de log de sécurité: Si l'excéption est relative aux droits
        */

        protected static function logException($e, $titreErreur){
            if(get_class($e) === 'NonAutoriseException'){
                self::log(self::getSecurityLogFile(), $e, $titreErreur);
            }

            self::log(self::getNormalLogFile(), $e, $titreErreur);
        }

    }
?>