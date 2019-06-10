<?php

class FileUpload
{
    private $file;
    private $fileName;
    private $uploadDir;
    private $fullPath;
    private $uploaded;
    private $clientFileName;

    public static  $CSV_MIMES = array(
        'text/plain', 
        'text/csv',
        'application/octet-stream',
        'text/tsv'
    );


    /*
        -Crée une instance du fichier chargé depuis un formulaire
        -@param String filename: Le nom du fichier dans le formulaire.
        -@Throws FichierInexistant: Si le fichier n'éxiste pas
    */
    public function __construct($fileName)
    {
        if (isset($_FILES[$fileName])) {
            $this->file = $_FILES[$fileName];
            $this->clientFileName = htmlspecialchars($_FILES[$fileName]['name']);
            $this->fileName = $fileName;
            $this->uploadDir = __DIR__ . '/../upload/';
            $this->uploaded = false;
            $this->fullPath = null;
        } else {
            throw new FichierInexistant("Le fichier est inexistant");
        }
    }

    /*
        - Copie un fichier sur le disque
        - @return boolean uploaded: Vrai si le fichier a bien été copier sur le disque
    */

    public function copyFile()
    {
        if (!$this->uploaded) {
            $this->setCopyPath();
            $this->uploaded = move_uploaded_file($this->file['tmp_name'], $this->fullPath);
        }

        return $this->uploaded;
    }

    /*
        - Valide le fichier en vérifiant l'éxtension du fichier.
    */
    public function checkMimes($mimes)
    {
        return in_array($this->file['type'], $mimes);
    }


    /*
        - Fonction utilisé pour éviter le conflit au cas où deux utilisateur upload deux fichier ayant le même nom
    */
    private function setCopyPath()
    {
        $file_exists = true;

        while ($file_exists) {
            $this->fullPath = $this->uploadDir . '/' . $this->fileName . '_' . random_int(0, 9000000000000) . '.' . $this->getExtension();
            $file_exists = file_exists($this->fullPath);
        }

        return $this->fullPath;
    }

    /*
        - Récupère l'éxtension du fichier chargé
    */
    public function getExtension()
    {
        $array = explode(".", $this->file['name']);
        $extension = end($array);
        return $extension;
    }

    /*
        - Récupère le contenu d'un fichier csv, et le transforme en un Tableau.
        - @param String separator: Le séprateur des colonnes dans le fichier.
        - @return String[][] : Le contenu du fichier csv. 
    */
    public function fichierEnTableau($separator = ";")
    {
        $data = array();

        $handle = fopen($this->fullPath, 'r');
        $i = 0;

        while (($row = trim(fgets($handle)))) {
            $row = utf8_encode($row);

            $line = explode($separator, $row);

            array_push($data, $line);
        }

        return $data;
    }


    /*
        - Renvoie l'emplacement du fichier sur le disque
    */
    public function getFullPath(){
        return $this->fullPath;
    }


    public function getClientFileName(){
        return $this->clientFileName;
    }


}
