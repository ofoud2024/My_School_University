<?php
    define("CONST_INCLUDE", true);

    require_once "../common/Database.php";
    Database::initConnexion();

    class CityUploader extends Database
    {
        private const insertQuery = "insert into ville values (:code_postal, :nom_ville)";
        public function insert()
        {
            $filename = __DIR__ . "/resource/laposte_hexasmal.csv";
            $separator = ";";
            $stmt = self::$db->prepare(self::insertQuery);
        
            $file = fopen($filename, "r");
        
            if (false !== $file) {
                fgets($file);
                $stmt->bindParam(':code_postal', $code_postal);
                $stmt->bindParam(':nom_ville', $nom);

                while (false !== ($line = fgets($file))) {
                    $parts = explode($separator, utf8_decode($line));
                    
                    $code_postal = $parts[2];
                    $nom = $parts[1];

                    try {
                        $stmt->execute();
                    } catch (PDOException $e) {
                        echo "</br>**************************************************************************************************************************************************************************************</br>";
                        echo "Echouée pour :</br>code_postal \t=\t " . $code_postal . "</br>nom \t=\t ". $nom . "</br>raison \t=>\t " . $e->getMessage();
                        echo "</br>**************************************************************************************************************************************************************************************</br>";
                    }
                }
            }
        }
    }

    $uploader = new CityUploader();
    $uploader->insert();
