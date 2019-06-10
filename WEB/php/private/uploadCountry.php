<?php
    define("CONST_INCLUDE", true);

    require_once "../common/Database.php";
    Database::initConnexion();

    class CountryUploader extends Database
    {
        private const insertQuery = "insert into pays values (:code_pays, :nom_pays)";
        public function insert()
        {
            $filename = __DIR__ . "/resource/sql-pays.csv";
            $separator = ",";
            $stmt = self::$db->prepare(self::insertQuery);
        
            $file = fopen($filename, "r");
        
            if (false !== $file) {
                fgets($file);
                $stmt->bindParam(':code_pays', $code_pays);
                $stmt->bindParam(':nom_pays', $nom);

                while (false !== ($line = fgets($file))) {
                    $parts = explode($separator, utf8_encode($line));
                    
                    $code_pays = str_replace('"', "", $parts[3]);
                    
                    $nom = str_replace('"', "", $parts[4]);

                    try {
                        $stmt->execute();
                    } catch (PDOException $e) {
                        echo "</br>**************************************************************************************************************************************************************************************</br>";
                        echo "Echouée pour :</br>code_pays \t=\t " . $code_pays . "</br>nom \t=\t ". $nom . "</br>raison \t=>\t " . $e->getMessage();
                        echo "</br>**************************************************************************************************************************************************************************************</br>";
                    }
                }
            }
        }
    }

    $uploader = new CountryUploader();
    $uploader->insert();
