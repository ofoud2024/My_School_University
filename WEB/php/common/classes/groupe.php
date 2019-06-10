<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../Database.php";
    require_once __DIR__ . "/classe_generique.php";

    class Groupe extends ClasseGenerique
    {

        //Vérifie l'éxistance d'un groupe
        private static $checkGroupQuery    = "select id_groupe from groupe where id_groupe = ?";

        //Récupère la liste des groupes éxistants
        private static $allGroupsQuery     = "select groupe.*, 
                                               count(distinct id_groupe_fils) as nombre_sous_groupes,
                                               count(distinct id_utilisateur) as nombre_utilisateurs
                                               from groupe left join sous_groupe on(est_un_sous_groupe(id_groupe_fils, groupe.id_groupe))
                                               left join membres_de_groupe on(utilisateur_appartient_a_groupe(id_utilisateur, groupe.id_groupe)) 
                                               group by groupe.id_groupe order by groupe.id_groupe";
        

        private static $possibleUsersQuery = "select id_utilisateur, nom_utilisateur, prenom_utilisateur, pseudo_utilisateur from utilisateur 
                                                where id_utilisateur not in (select id_utilisateur from membres_de_groupe where id_groupe = :id_groupe)";

        private static $possibleGroupsQuery= "select id_groupe, nom_groupe from groupe 
                                              where id_groupe not in (
                                                    select id_groupe from groupe where est_un_sous_groupe(:id_groupe ,id_groupe)
                                              )";

        //Récupère la liste des utilisateur appartenant au groupe directement
        private static $groupUsersQuery     = "select utilisateur.id_utilisateur, pseudo_utilisateur,
                                                personnel.id_personnel, id_enseignant, num_etudiant, date_debut || ' => ' || date_fin as periode
                                                from membres_de_groupe 
                                                inner join utilisateur on (membres_de_groupe.id_utilisateur = utilisateur.id_utilisateur) 
                                                left join personnel on (utilisateur.id_utilisateur = personnel.id_utilisateur)
                                                left join enseignant on (enseignant.id_personnel = personnel.id_personnel)
                                                left join etudiant on (etudiant.id_utilisateur = utilisateur.id_utilisateur)
                                                where id_groupe = :id_groupe
                                                ";

        //Récupère la liste des sous_groupes directes                                       
        private static $groupChildQuery    = "select id_groupe_fils as id_groupe, groupe.nom_groupe as nom_groupe, count(distinct id_utilisateur) as nombre_utilisateurs
                                                from sous_groupe inner join groupe on(id_groupe_fils = id_groupe) 
                                                left join membres_de_groupe on (utilisateur_appartient_a_groupe(id_utilisateur, id_groupe_fils))
                                                where id_groupe_parent = :groupe_parent
                                                group by groupe.id_groupe, id_groupe_fils";

        //Récupère la liste de tous les sous_groupes
        private static $allSubGroupQuery   = "select id_groupe, nom_groupe from groupe
                                                where est_un_sous_groupe(id_groupe, :id_groupe_parent)
                                                or id_groupe = :id_groupe_parent
                                                ";

        //Récupère les détails du groupe
        private static $groupDetailsQuery  = "select * from groupe where id_groupe = :id_groupe";


        //Ajoute un utilisateur à un groupe
        private static $addUserQuery       = "insert into membres_de_groupe values (:id_groupe, :id_utilisateur, :debut, :fin)";
        
        //Ajoute un groupe 
        private static $addGroupQuery      = "insert into sous_groupe values(:groupe_parent, :groupe_enfant)";
        
        //Ajoute un sous_groupe
        private static $insertGroupQuery   = "insert into groupe values (default, :nom_groupe, :nom_droits)";

        //Retire un sous groupe du groupe
        private static $deleteChildQuery   = "delete from sous_groupe where id_groupe_parent = :groupe_parent and id_groupe_fils = :groupe_enfant";
        
        //Retire un utilisateur du groupe
        private static $deleteUserQuery    = "delete from membres_de_groupe where id_groupe = :id_groupe and id_utilisateur = :id_utilisateur";

        //Retire tous les sous groupes
        private static $deleteSubGroups    = "delete from sous_groupe where id_groupe_parent = :id_groupe";
        
        //Retire tous les utilisateurs
        private static $deleteAllUsers     = "delete from membres_de_groupe where id_groupe = :id_groupe";
        
        //Supprime le groupe
        private static $deleteGroupQuery   = "delete from groupe where id_groupe = :id_groupe";

        private $id_groupe;
        private $details_groupe;
        private $utilisateurs;
        private $sous_groupes;
        private $tous_les_sous_groupes;

        /*
            - Instancie un groupe
            - @Throws PDOException
            - @Throws ElementIntrouvable: Si aucun groupe ne porte l'id_groupe passé en paramètre
        */
        public function __construct($id_groupe)
        {
            parent::__construct(self::$checkGroupQuery, array($id_groupe));
            $this->id_groupe = $id_groupe;
        }


        /*
            - Récupère tous les utilisateurs appartenant directement au groupe et non pas à l'un des sous groupes.
            - @return tableau de data : {
                    id_utilisateur: integer,
                    pseudo_utilisateur: string,
                    id_personnel: integer,
                    id_enseignant: integer,
                    num_etudiant: integer,
                    periode : String date_debut || ' => ' || date_fin
                }
            - @Throws PDOException.
        */
        public function utilisateursGroupe()
        {
            if (!$this->utilisateurs) {
                $stmt = self::$db->prepare(self::$groupUsersQuery);
                $stmt->bindValue(':id_groupe', $this->id_groupe);
                $stmt->execute();
                $this->utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
            }

            return $this->utilisateurs;
        }



        /*
         - Retourne la liste des sous groupes directes de ce groupe.
         - @return tableau de data: {
                id_groupe : integer, 
                nom_groupe: string,
                nombre_utilisateurs: integer
         }
         - @Throws PDOException
        */
        public function sousGroupes()
        {
            if (!$this->sous_groupes) {
                $stmt = self::$db->prepare(self::$groupChildQuery);
                $stmt->bindValue(':groupe_parent', $this->id_groupe);
                $stmt->execute();
                $this->sous_groupes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
            }

            return $this->sous_groupes;
        }


        /*
            - Récupère les détails du groupes
            - @return data : {
                - id_groupe : integer, 
                - nom_groupe: string,
                - nom_droits: string
            }
            -@Throws PDOException
            -@Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de droit_creation_groupes
        */
        public function detailsGroupe()
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            if (!$this->details_groupe) {
                $stmt = self::$db->prepare(self::$groupDetailsQuery);
                $stmt->bindValue(':id_groupe', $this->id_groupe);
                $stmt->execute();
                $this->details_groupe = $stmt->fetch(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
            }

            return $this->details_groupe;
        }


        /*
            - Récupère tous les sous_groupes directes ou pas d'un groupe
            - Le groupe courant sera aussi inscrit dans la liste des groupes.
            - @return tableau de data : {
                id_groupe: integer,
                nom_groupe: integer
            }
            - @Throws PDOException.
        */
        public function tousLesSousGroupes(){
            $stmt = self::$db->prepare(self::$allSubGroupQuery);

            $stmt->bindValue(':id_groupe_parent', $this->id_groupe);

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $resultat;
        }
        
        /*
            -Renvoie la liste des utilisateurs qui ne sont pas dans ce groupe
            -@return tableau de :{
                id_utilisateur: integer,
                pseudo_utilisateur: string,
                nom_utilisateur: string,
                prenom_utilisateur: string
            }
            -@Throws PDOException
            -@Throws NonAutoriseException : Si l'utilisateur courant ne possède pas 
            le droit de création des groupes
        */

        public function utilisateursPossibles(){
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$possibleUsersQuery);
            $stmt->bindValue(':id_groupe', $this->id_groupe);
            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultat;

        }



         /*
            -Renvoie la liste des groupes qui ne font pas partie des sous groupes directes de ce groupe
            -@return tableau de :{
                id_utilisateur  : integer,
                nom_groupe      : string
            }
            -@Throws PDOException
            -@Throws NonAutoriseException : Si l'utilisateur courant ne possède pas 
            le droit de création des groupes
        */

        public function groupesPossibles(){
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$possibleGroupsQuery);
            $stmt->bindValue(':id_groupe', $this->id_groupe);
            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();

            return $resultat;

        }

        /*
            - Ajoute un utilisateur au groupe
            - @param id_utilisateur: l'identifiant de l'utilisateur à ajouter
            - @Throws PDOException : Si l'insertion a échouée
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit création groupes
        */
        public function ajouterUtilisateur($id_utilisateur)
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$addUserQuery);
            $periode = explode(" => ", self::getDBYear());

            $stmt->bindValue(':id_groupe', $this->id_groupe);
            $stmt->bindValue(':id_utilisateur', $id_utilisateur);
            $stmt->bindValue(':debut', $periode[0]);
            $stmt->bindValue(':fin', $periode[1]);

            $stmt->execute();
        }


        /*
            - Ajoute un sous_groupe au groupe
            - @param sous_groupe: l'identifiant du groupe à ajouter
            - @Throws PDOException : Si l'insertion a échouée ou si on essaie d'ajouter un sous_groupe dont il est déjà enfant
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit création groupes
        */
        public function ajouterSousGroupe($sous_groupe)
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$addGroupQuery);
            
            $stmt->bindValue(':groupe_parent', $this->id_groupe);
            $stmt->bindValue(':groupe_enfant', $sous_groupe);

            $stmt->execute();
        }


        /*
            - Retire un utilisateur de la liste des utilisateurs directes du groupe
            - Attention: Cette méthode ne retire pas l'utilisateur des sous_groupes.
            - @param id_utilisateur: l'identifiant de l'utilisateur à ajouter
            - @Throws PDOException : Si la suppresison a échouée
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit création groupes
        */
  

        public function retirerUtilisateur($id_utilisateur)
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$deleteUserQuery);
            
            $stmt->bindValue(':id_groupe', $this->id_groupe);
            $stmt->bindValue(':id_utilisateur', $id_utilisateur);

            $stmt->execute();
        }


        /*
            - Retire un sous_groupe au groupe
            - @param sous_groupe: l'identifiant du groupe à ajouter
            - @Throws PDOException : Si la supression a échouée
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit création groupes
        */

        public function retirerSousGroupe($sous_groupe)
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$deleteChildQuery);
            
            $stmt->bindValue(':groupe_parent', $this->id_groupe);
            $stmt->bindValue(':groupe_enfant', $sous_groupe);

            $stmt->execute();
        }


        /*
            - Supprime ce groupe
            - Supprime aussi tous les sous groupes et les utilisateurs appartenant à ce groupe
            - @Throws PDOException : Si la supression a échouée
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit création groupes
        */

        public function supprimer()
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt_sous_groupes = self::$db->prepare(self::$deleteSubGroups);
            $stmt_utilisateurs = self::$db->prepare(self::$deleteAllUsers);
            $stmt_suppression  = self::$db->prepare(self::$deleteGroupQuery);
            
            $stmt_sous_groupes->bindValue(':id_groupe', $this->id_groupe);
            $stmt_utilisateurs->bindValue(':id_groupe', $this->id_groupe);
            $stmt_suppression->bindValue(':id_groupe', $this->id_groupe);


            self::$db->beginTransaction();

            $stmt_sous_groupes->execute();
            $stmt_utilisateurs->execute();
            $stmt_suppression->execute();

            //Si on arrive içi alors toutes les opérations se sont bien déroulées
            self::$db->commit();
        }





        /*
            - Renvoie la liste de tous les groupes éxistants:
            - @returns tableau de : {
                id_groupe           : integer,
                nom_groupe          : integer,
                nom_droits          : string,
                nombre_sous_groupes : integer (Les sous_groupes des sous groupes sont inclus),
                nombre_utilisateurs : integer (Les utilisateurs des sous groupes sont inclus)
            }
            - @Throws PDOException
        */

        public static function getListeGroupes()
        {
            $stmt = self::$db->prepare(self::$allGroupsQuery);
            $stmt->execute();
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
            return $resultat;
        }


        /*
            -Crée un groupe
            -@param nom_groupe: Le nom du nouveau groupe
            -@param nom_droits: Le nom du droit associé

            -@returns null
            -@Throws PDOException: Si l'insertion a échouée
            -@Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit de création des groupes
        */

        public static function ajouterGroupe($nom_groupe, $nom_droits)
        {
            Utilisateur::possedeDroit('droit_creation_groupes');

            $stmt = self::$db->prepare(self::$insertGroupQuery);
            
            $stmt->bindValue(":nom_groupe", $nom_groupe);
            $stmt->bindValue(":nom_droits", $nom_droits);

            $stmt->execute();
        }


    }
