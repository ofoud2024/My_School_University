<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . '/Date.php';
    class Database
    {
        protected static $db;
        
        /*
            Initialise la connexion
        */
       public static function initConnexion()
        {
            try {
                self::$db = new PDO("pgsql:host=localhost;port=5432;dbname=etablissement_ofoudane", "etablissement_ofoudane", "etablissement");
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                header("Location: index.php?module=error&title=Problème Serveur&message=".DATABASE_ERROR_MESSAGE);
            }
        }


        /*
            - Renvoie l'instance de la base de données
        */
        public static function getDB()
        {
            return self::$db;
        }
        


        /*
            -Renvoie la période maximale dans la base de données.
            -Si aucune date n'est présente, alors la période actuelle est insérée.
        */

        public static function getDBYear()
        {
            $stmt = self::$db->prepare("select max(date_debut) as date_debut, max(date_fin) as date_fin from periode_semestre ");
            
            $stmt->execute();
 
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
 
            $current_interval = Date::getPeriodeCourante();
            
            if (false === $result || $current_interval['debut'] !== $result['date_debut']) {
                $stmtInsert = self::$db->prepare("insert into periode_semestre values (:debut, :fin)");
                $stmtInsert->execute($current_interval);
            }

            return $current_interval['debut'] . " => " . $current_interval['fin'];
        }

        /*
            - Récupère l'astuce contenue dans le message d'érreur d'une exception PDOException
            - @param PDOException exception : L'éxception que l'on veut traiter.
            - @return String Hint: L'astuce contenu dans le message d'érreur
        */

        public static function getPDOHint($exception)
        {
            $message = $exception->getMessage();
            $message_lines = explode("\n", $message);

            $exception_hint = trim(str_replace('HINT:  ', '', $message_lines[1]));
            
            return $exception_hint;
        }
    
    }
