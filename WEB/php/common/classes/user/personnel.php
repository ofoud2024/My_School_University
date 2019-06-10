<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../classe_generique.php";
    
    class Personnel extends ClasseGenerique
    {
        //Vérifie si un personnel est éxistant ou pas
        private static $personalCheckQuery      = "select id_personnel from personnel where id_personnel = ?";

        //Récupère l'identifiant du personnel associé à un utilisateur        
        private static $personalIdQuery         = "select id_personnel from utilisateur 
                                                    inner join personnel using(id_utilisateur)
                                                    where id_utilisateur = :id_utilisateur";

        //Récupère la liste des personnels
        private static $personalListQuery       = 'select nom_utilisateur, prenom_utilisateur, pseudo_utilisateur,
                                                    personnel.*, 
                                                    enseignant.id_enseignant, coalesce(sum(heures_travail), 0) as heures_travail 
                                                    from personnel 
                                                    inner join utilisateur on (utilisateur.id_utilisateur = personnel.id_utilisateur) 
                                                    left join enseignant on (personnel.id_personnel = enseignant.id_personnel) 
                                                    left join heures_travail on (personnel.id_personnel = heures_travail.id_personnel)
                                                    group by utilisateur.id_utilisateur, personnel.id_personnel, enseignant.id_enseignant';



        //Récupère les détails d'un personnel
        private static $personalQuery           = 'select 
                                                    nom_utilisateur, prenom_utilisateur, pseudo_utilisateur,
                                                    personnel.*, 
                                                    enseignant.id_enseignant 
                                                    from personnel 
                                                    inner join utilisateur on (utilisateur.id_utilisateur = personnel.id_utilisateur) 
                                                    left join enseignant on (personnel.id_personnel = enseignant.id_personnel) 
                                                    where personnel.id_personnel = ?
                                                    group by utilisateur.id_utilisateur, personnel.id_personnel, enseignant.id_enseignant ';
        

        //Récupère la liste des utilisateurs qui ne sont pas de personnels
        private static $possiblePersonalQuery   = 'select pseudo_utilisateur as pseudo from utilisateur where id_utilisateur not in(select id_utilisateur from personnel)';
        
        //Récupère les heures de travail d'un personnel, dans une période définie.
        private static $personalWorkQuery       = 'select date_debut::varchar || \' => \' || date_fin::varchar as annee, heures_travail from heures_travail where id_personnel = :id_personal order by annee';
    
        //Ajoute un personnel dans la table des personnels
        private static $insertPersonalQuery     = 'insert into personnel values(default, :id_utilisateur) returning id_personnel ';
    
        //Met à jour les heures de travail d'un personnel
        private static $personalWorkUpdateQuery = 'select setHeuresTravail(:id_personnel, :heures_travail, :debut, :fin) ';


        //Supprime l'historique des heures de travail d'un personnel
        private static $deletePersonalQuery1    = 'delete from heures_travail where id_personnel = :id_personnel ';
        //Supprime un personnel
        private static $deletePersonalQuery2    = 'delete from personnel where id_personnel = :id_personnel';

        private $id_personnel;
        private $informations_personnel;

        /*
         - Crée une instance d'un personnel en se basant sur son identifiant
         - @param id_personnel: L'identifiant du personnel qu'on veut créer
         - @Throws PDOException : Si l'éxecution de la requête à échouée.
        */
        public function __construct($id_personnel){
            parent::__construct(self::$personalCheckQuery, array($id_personnel));
            $this->id_personnel = $id_personnel;
        }


        /*
         - Récupère les informations relatives au personnel courant
         
         - @Return data = {
                id_utilisateur      : integer,
                id_personnel        : integer,
                id_enseignant       : integer or null,
                nom_utilisateur     : string,
                prenom_utilisateur  : string,
                pseudo_utilisateur  : string,
                annee_courante      : string, "départ => fin" 
                heures_travail      : integer (Total des heures de travail pour le semestre courant )
         };
        
         - @Throws NonAutoriseException: Si l'on essaie de récupérer les détails d'un autre personnel 
            et qu'on a pas le droit création_utilisateurs
         - @Throws PDOException : Si l'éxecution de la requête à échouée.
        */

        public function informations_personnel()
        {
            $id_personnel_courant = self::idPersonnelCourant();
            if(!$id_personnel_courant == $this->id_personnel)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');

            if(!$this->informations_personnel){
                $stmt = self::$db->prepare(self::$personalQuery);

                $stmt->bindValue(1, $this->id_personnel);
    
                $stmt->execute();
                
                $resultat = $stmt->fetch(PDO::FETCH_ASSOC);

                $stmt->closeCursor();    

                $this->informations_personnel =  array_merge($resultat,
                                                             array(
                                                                 "annee_courante"=>self::getDBYear(),
                                                                 "heures_travail"=>$this->heures_travail()
                                                            ));
            }
        
            return $this->informations_personnel;
        }


        /*
         - Récupère l'ensemble des heures de travail du personnel
         - @Throws NonAutoriseException: Si l'on essaie de récupérer les détails d'un autre personnel 
            et qu'on a pas le droit création_utilisateurs
         - @Throws PDOException : Si l'éxecution de la requête à échouée.
        */

 
        public function heures_travail()
        {
            $id_personnel_courant = self::idPersonnelCourant();
            if(!$id_personnel_courant == $this->id_personnel)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');

            $stmt = self::$db->prepare(self::$personalWorkQuery);

            $stmt->bindValue(":id_personal", $this->id_personnel);

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();    

            return $resultat;
        }


        /*
            - Modifie les heures de travail du personnel pendant la période courante.
            
            - @Throws PDOException : Si l'éxecution de la requête a échoué
            - @Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit_modification_heures de travail, 
                en plus que le droit creation utilisateurs
        */
        public function modifierHeuresTravail($heures_travail)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            //On récupère la période courante sous la forme:
            // date_depart => date_arrivee
            //annee[0] = date_depart,
            //annee[1] = date_arrivee

            $annee = explode(" => ", self::getDBYear());
            
            $stmt = self::$db->prepare(self::$personalWorkUpdateQuery);
            
            $stmt->bindValue(":heures_travail", $heures_travail);
            $stmt->bindValue(":id_personnel", $this->id_personnel);
            $stmt->bindValue(":debut", $annee[0]);
            $stmt->bindValue(":fin", $annee[1]);

            $stmt->execute();
        }

        /*
            - Supprime un personnel
            - @Throws PDOException.
            - @Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit "création_utilisateurs"
        */
        public function supprimerPersonnel()
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');



            $stmt1 = self::$db->prepare(self::$deletePersonalQuery1);
            $stmt2 = self::$db->prepare(self::$deletePersonalQuery2);

            $stmt1->bindValue(':id_personnel', $this->id_personnel);
            $stmt2->bindValue(':id_personnel', $this->id_personnel);

            $stmt1->execute();
            $stmt2->execute();
        }



        /*
            - Inscrit un personnel dans la base de données
            - @Throws PDOException.
            - @Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit "création_utilisateurs"
        */

        public static function ajouterPersonnel($id_utilisateur)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            $stmtPersonnel = self::$db->prepare(self::$insertPersonalQuery);

            $stmtPersonnel->bindValue(':id_utilisateur', $id_utilisateur);

            $stmtPersonnel->execute();

            return $stmtPersonnel->fetch(PDO::FETCH_ASSOC)['id_personnel'];
        }


        /*
            - Récupère la liste de tous les personnels
            - Chaque ligne du tableau est du type : {
                id_utilisateur      : integer,
                id_personnel        : integer,
                id_enseignant       : integer or null,
                nom_utilisateur     : string,
                prenom_utilisateur  : string,
                pseudo_utilisateur  : string,
                heures_travail      : integer (Total des heures de travail pour ce personnel )
     
            }
            - @Throws PDOException.
            - @Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit "création_utilisateurs"
        */

        public static function getListePersonnels()
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            $stmt = self::$db->prepare(self::$personalListQuery);
            
            $liste_personnel = array();

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();  

            foreach ($resultat as $personnel) {
                array_push($liste_personnel, array(
                    "id"=>$personnel['id_personnel'],
                    "nom"=>$personnel['nom_utilisateur'],
                    "prenom"=>$personnel['prenom_utilisateur'],
                    "num_enseignant"=>$personnel['id_enseignant'],
                    "heures_travail"=>$personnel['heures_travail']
                ));
            }

            return $liste_personnel;
        }


        /*
            -Renvoie la liste du personnel possible
            -@return {
                id_utilisateur: integer,
                pseudo_utilisateur: string,
                nom_utilisateur: string,
                prenom_utilisateur:string
            }
            -@Throws PDOException 
            -@Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit de création du personnel
        */
        public static function personnelsPossible(){
            $stmt = self::$db->prepare(self::$possiblePersonalQuery);
            
            $stmt->execute();
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt->closeCursor();
            
            return $resultat;
        }



        /*
         - Renvoie l'identifiant du personnel courant
         - Si l'utilisateur courant n'existe pas ou n'est pas un personnel,
            alors null est retourné 
        */
        public static function idPersonnelCourant(){
            $utilisateur_courant = Utilisateur::getUtilisateurCourant();
            $id_personnel_courant = false;

            if($utilisateur_courant){
                $stmt = self::$db->prepare(self::$personalIdQuery);
                $stmt->bindValue(':id_utilisateur', $utilisateur_courant->getIdUtilisateur());
                
                try{
                    $stmt->execute();
                    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
                    if($resultat){
                        $id_personnel_courant = $resultat['id_personnel'];
                    }

                }catch(PDOException $e){}
            }

            return $id_personnel_courant;
        }



    }
