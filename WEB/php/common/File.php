<?php
    require_once __DIR__ . "./../verify.php";
    require_once __DIR__ . "/errorHandlerAPI.php";
    class Fichier{

        private $path;

        public function __construct($path){
            $this->path = $path;
        }

        /*
            -Permet de télecharger un fichier s'il existe
        */
        public function telecharger($filename)
        {
            if (file_exists($this->path)) {
                if($this->getExtension() !== $this->getExtension($filename))
                    $filename = $filename . $this->getExtension();

                header('Content-Description: File Transfer');
                header('Content-Disposition: attachment; filename=' . $filename);
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($this->path));
                flush();
                readfile($this->path);
                exit(0);
            } else {
                ErrorHandlerAPI::afficherErreur(
                    new Exception(DOWNLOAD_ERROR_TITLE),
                    DOWNLOAD_ERROR_TITLE,
                    DOWNLOAD_ERROR_MESSAGE,
                    array(),
                    INTERNAL_SERVER_ERROR
                );
            }
        }


        /*
            - Supprime le fichier
        */
        public function supprimer()
        {
            if (file_exists($this->path)) {
                unlink($this->path);
            }
        }

        public function getExtension($filename = false)
        {
            $array = explode(".", $this->path);

            if($filename !== false){
                $array = explode(".", $filename);
            }

            $extension = end($array);
            return $extension;
        }
    



    }
?>