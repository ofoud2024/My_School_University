<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../Database.php";
    require_once __DIR__ . "/classe_generique.php";

    class Droits extends ClasseGenerique
    {

        private static $checkRoleQuery = "select nom_droits from droits where nom_droits = ?";

        //Récupère la liste des droits
        private static $rolesListQuery  = 'select * from droits order by nom_droits';

        //Récupère les détails concernant un droit donnée
        private static $roleQuery       = 'select * from droits where nom_droits = :nom_droits';

        //Récupère les noms des groupes auxquelles est attribué un droit. 
        private static $groupQuery      = 'select nom_groupe from groupe where nom_droits = :nom_droits';

        //Récupère les utilisateurs auxquelles est attribué ce droit
        private static $userQuery       = 'select pseudo_utilisateur, nom_utilisateur, prenom_utilisateur from utilisateur where nom_droits = :nom_droits';

        //Insère un nouveau droit
        private static $insertRoleQuery = "insert into droits values(
            :nom_droits,
            :creation_utilisateurs,
            :creation_modules,
            :creation_cours,
            :creation_groupes,
            :modification_absences,
            :modification_droits,
            :modification_heures_travail,
            :statistiques
        )";

        //Met à jour un droit éxistant
        private static $updateRoleQuery = "update droits set
            droit_creation_utilisateurs         = :creation_utilisateurs,
            droits_creation_modules             = :creation_modules,
            droit_creation_cours                = :creation_cours,
            droit_creation_groupes              = :creation_groupes,
            droit_modification_absences         = :modification_absences     ,
            droit_modification_droits           = :modification_droits,
            droit_modification_heures_travail   = :modification_heures_travail,
            droit_visualisation_statistique     = :statistiques
        where nom_droits = :nom_droits";

        //Supprime un droit donnée
        private static $deleteRoleQuery = "delete from droits where nom_droits = :nom_droits";


        private $informations_droits;

        private $nom_droits;

        private $liste_groupes;
        
        private $liste_utilisateurs;

        /*
         - Instancie un droit identifié par son nom
         - @param nom_droits: Le nom du droit à instancié
         - @Throws PDOException : Si une erreur se produit au niveau de la base de données
         - @Throws ElementIntrouvable: Si le droit est inéxistant
        */
        public function __construct($nom_droits)
        {
            parent::__construct(self::$checkRoleQuery ,array($nom_droits));
            $this->nom_droits = $nom_droits;
        }

        /*
         - Renvoie les données relatives à ce droit
         - @return data : {
             nom_droits                         : string,
             droit_creation_utilisateurs        : boolean,
             droits_creation_modules            : boolean, 
             droit_creation_cours               : boolean,
             droit_creation_groupes             : boolean,
             droit_modification_absences        : boolean,
             droit_modification_droits          : boolean,
             droit_modification_heures_travail  : boolean,
             droit_visualisation_statistique    : boolean
         }
         - @Throws PDOException : Si l'éxecution de la requête a échoué
        */
        public function getData(){
        

            if (!$this->informations_droits) {
                $stmt = self::$db->prepare(self::$roleQuery);

                $stmt->bindValue(":nom_droits", $this->nom_droits);
                
                $stmt->execute();
    
                $this->informations_droits = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return $this->informations_droits;
        }



        /*
         - Renvoie la liste des groupes qui ont ce droit
         - @return data : {
            nom_groupe : string  
          }
         - @Throws PDOException : Si l'éxecution de la requête a échoué
         - @Throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de modification droits
        */

        public function getListeGroupes()
        {
            Utilisateur::possedeDroit('droit_modification_droits');

            //Si on a jamais recherché la liste des groupes
            if (!$this->liste_groupes) {
                $stmt = self::$db->prepare(self::$groupQuery);
                
                $stmt->bindValue(":nom_droits", $this->nom_droits);
                
                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();      
    
                $this->liste_groupes = $resultat;
            }

            return $this->liste_groupes;
        }

        /*
         - Renvoie la liste des utilisateurs qui ont ce droit
         - @return data : {
            nom_groupe : string  
          }
         - @Throws PDOException : Si l'éxecution de la requête a échoué
         - @Throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de modifications des droits
        */

        public function getListeUtilisateurs()
        {
            Utilisateur::possedeDroit('droit_modification_droits');

            //Si on a jamais recherché la liste des utilisateurs
            if (!$this->liste_utilisateurs) {
                $stmt = self::$db->prepare(self::$userQuery);
                
                $stmt->bindValue(":nom_droits", $this->nom_droits);
                
                $stmt->execute();
    
                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();      

                $this->liste_utilisateurs = $resultat;
            }

            return $this->liste_utilisateurs;
        }



        /*
         - Modifie ce droit
         
         - @param creation_utilisateur, ..., statistiques: Vrai si accordée, faux sinon 

         - @Throws PDOException.
         - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de modification des droits
        */

        public function modifierDroits(
            $creation_utilisateurs,
            $creation_modules,
            $creation_cours,
            $creation_groupes,
            $modification_absences,
            $modification_droits,
            $modification_heures_travail,
            $statistiques
        ) { 
            Utilisateur::possedeDroit('droit_modification_droits');

            $stmt = self::$db->prepare(self::$updateRoleQuery);

            $stmt->bindValue(":nom_droits", $this->nom_droits);
            $stmt->bindValue(":creation_utilisateurs", $creation_utilisateurs);
            $stmt->bindValue(":creation_modules", $creation_modules);
            $stmt->bindValue(":creation_cours", $creation_cours);
            $stmt->bindValue(":creation_groupes", $creation_groupes);
            $stmt->bindValue(":modification_absences", $modification_absences);
            $stmt->bindValue(":modification_droits", $modification_droits);
            $stmt->bindValue(":modification_heures_travail", $modification_heures_travail);
            $stmt->bindValue(":statistiques", $statistiques);

            $stmt->execute();
        }


        /*
         - Supprime ce droit
         
         - @Throws PDOException: Si ce droit est déjà attribué à un groupe ou un utilisateur
         - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de modification droits
        */

        public function supprimerDroits()
        {
            Utilisateur::possedeDroit('droit_modification_droits');

            $stmt = self::$db->prepare(self::$deleteRoleQuery);

            $stmt->bindValue(':nom_droits', $this->nom_droits);

            $stmt->execute();
        }






        /*
         - Renvoie la liste de tous les droits
         - @Throws PDOException.
         - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de création utilisateurs ou le droit de création des groupes
        */

        public static function getListeDroits()
        {
            try{
                Utilisateur::possedeDroit('droit_creation_utilisateurs');
            }catch(NonAutoriseException $e){
                Utilisateur::possedeDroit('droit_creation_groupes');
            }

            $stmt = self::$db->prepare(self::$rolesListQuery);
          
            $stmt->execute();
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();      

            return $resultat;
        }


        /*
         - Ajoute un droit dans la base de données
         - @param nom_droits : Le nom du droit à créer
         - @param creation_utilisateur, ..., statistiques: Vrai si accordée, faux sinon 

         - @Throws PDOException.
         - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de modifications des droits
        */

        public static function ajouterDroits(
            $nom_droits,
            $creation_utilisateurs,
            $creation_modules,
            $creation_cours,
            $creation_groupes,
            $modification_absences,
            $modification_droits,
            $modification_heures_travail,
            $statistiques
        ) {
            Utilisateur::possedeDroit('droit_modification_droits');

            $stmt = self::$db->prepare(self::$insertRoleQuery);

            $stmt->bindValue(":nom_droits", $nom_droits);
            $stmt->bindValue(":creation_utilisateurs", $creation_utilisateurs);
            $stmt->bindValue(":creation_modules", $creation_modules);
            $stmt->bindValue(":creation_cours", $creation_cours);
            $stmt->bindValue(":creation_groupes", $creation_groupes);
            $stmt->bindValue(":modification_absences", $modification_absences);
            $stmt->bindValue(":modification_droits", $modification_droits);
            $stmt->bindValue(":modification_heures_travail", $modification_heures_travail);
            $stmt->bindValue(":statistiques", $statistiques);

            $stmt->execute();
        }

    }
