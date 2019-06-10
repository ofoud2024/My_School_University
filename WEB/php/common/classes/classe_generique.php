<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../Database.php";
    require_once __DIR__ . "/../exceptions/ElementIntrouvable.php";

    /*
        - Cette classe permet de valider les classes se trouvant dans common
        - Son constructeur doit être appelé lors d'instanciation d'une des classes de common
        - Cette classe vérifie l'éxistance d'un élement dans la base de donnée
    */
    class ClasseGenerique extends Database{

        /*
            - Vérifie l'éxistance d'un élement dans la base de données.
            - @Throws ElementIntrouvable: Si l'élément n'éxiste pas  
        */
    
        public function __construct($requete, $params, $message = "Élément introuvable"){
            $stmt = self::$db->prepare($requete);
            $exists = false;
            
            if(is_array($params)){
                $stmt->execute($params);
                $exists = $stmt->fetch(PDO::FETCH_NUM) != null;
                $stmt->closeCursor();
            }

            if(!$exists){
                throw new ElementIntrouvable($message);
            }
        }
    }

?>