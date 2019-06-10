<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/personnel.php";
    
    class Enseignant extends Personnel
    {
        //Requête sql pour insérer un enseignant dans la base de données
        private static $insertEnseignantQuery = 'insert into enseignant values(default, ?)';

        //Requête de suppression d'un enseignant
        private static $deleteEnseignantQuery = "delete from enseignant where id_personnel = :id_personnel";

        private static $teacherIdQuery        = "select id_enseignant from enseignant natural join personnel where id_personnel = :id_personnel";


        public function __construct($id_personnel){
            parent::__construct($id_personnel);
        }

        /*
         - Ajoute un personnel dans la table enseignant
         - @param id_personnel : L'identifiant d'un personnel
         
         - @Throws PDOException.
         - @Throws NonAutoriseException: Si le droit de création utilisateur n'est pas accordé
        
        */
        public static function ajouterEnseignant($id_personnel)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');
            
            $stmt = self::$db->prepare(self::$insertEnseignantQuery);
            
            $stmt->bindValue(1, $id_personnel);

            $stmt->execute();
        }

        /*
         - Supprime un enseignant éxistant.
         - @param id_personnel: L'identifiant du personnel associé à cet enseignant.
         
         - @Throws NonAutoriseException: Si le droit de création utilisateur n'est pas accordé.
         - Attention: Aucune erreur ne sera déclenchée si l'enseignant à supprimer n'éxiste pas.
        */
        public static function supprimerEnseignant($id_personnel)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            $stmt = self::$db->prepare(self::$deleteEnseignantQuery);
                    
            $stmt->bindValue(':id_personnel', $id_personnel);

            $stmt->execute();
        }



        /*
            - Renvoie l'identifiant de l'enseignant connecter
            - @Throws PasEnseignantException : Si l'utilisateur connecté n'est pas un enseignant
        */

        public static function idEnseignantCourant(){
            $id_personnel_courant = Personnel::idPersonnelCourant();
            $id_enseignant_courant = false;

            if(false !== $id_personnel_courant){
                $stmt = self::$db->prepare(self::$teacherIdQuery);
                $stmt->bindValue(':id_personnel', $id_personnel_courant);
                try{
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if($result){
                        $id_enseignant_courant = $result['id_enseignant'];
                    }

                }catch(PDOException $e){}
            }

            if(false === $id_enseignant_courant){
                throw new PasEnseignantException();
            }

            return $id_enseignant_courant;
            
        }

    }
