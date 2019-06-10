<?php

    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../Database.php";
    require_once __DIR__ . "/classe_generique.php";


    /*
        - Classe Edt représente une séance.
        - Une séance est valide si : 
            1-Sa durée en minutes est un multiple de 10.
            2-Si le jour de la séance n'est ni samedi ni dimanche.
            3-Si l'heure de départ est entre 8h:00 du matin et 19h:00 du soir
            4-Si l'enseignant du module n'est pas déjà occupé avec un autre module.
            5-Si une autre séance n'est pas planifié dans la même salle
    */
    class Edt extends ClasseGenerique
    {

        private static $requete_verification = "select id_seance from seance where id_seance = ?";

        //Récupère la liste des séances dans un intervalle de temps
        private static $requete_liste_seances = "select seance.*, 
                                                    groupe.nom_groupe, groupe.id_groupe,
                                                    enseignant.id_enseignant,
                                                    utilisateur.nom_utilisateur as nom_enseignant, utilisateur.prenom_utilisateur as prenom_enseignant, 
                                                    module.couleur_module, module.ref_semestre, module.abreviation_module
                                                    from seance 
                                                    inner join enseignant using(id_enseignant)
                                                    inner join personnel using(id_personnel)
                                                    inner join utilisateur using(id_utilisateur) 
                                                    inner join salle using(nom_salle)
                                                    inner join module using(ref_module) 
                                                    inner join groupe using(id_groupe)
                                                    where seance.date_seance between 
                                                    :startDate and :endDate
                                                    ";

        //Modifie ou crée une séance                                                    
        private static $requete_modifier_seance = "select modifier_seance(
                                                        :id_seance,
                                                        :date_seance,
                                                        :heure_depart,
                                                        :duree,
                                                        :groupe,
                                                        :enseignant,
                                                        :module,
                                                        :salle
                                                    ) as id_seance";

        private static $requete_supprimer_absences = "delete from etudiant_absent where id_seance = :id_seance";
        private static $requete_supprimer_seance = "delete from seance where id_seance = :id_seance";

        
        private $id_seance;

        /*
            - Instancie une nouvelle séance
            - @param id_seance: L'identifiant de la séance
            - @throws PDOException : Si la requête de vérification a échouée
            - @throws ElementIntrouvable: Si aucune séance ne porte l'identifiant du séance passé en paramètre
        */

        public function __construct($id_seance){
            parent::__construct(self::$requete_verification, array($id_seance));
            $this->id_seance = $id_seance;
        }


        /*
            - Permet de modifier cette séance
            
            - @param date_seance:  La nouvelle date de début (YYYY-MM-DD)
            - @param heure_depart : L'heure dans laquelle va commencé la séance
            - @param duree : La durée de séance 'hh:mm:ss'
            - @param groupe: l'identifiant du groupe 
            - @param enseignant: L'identifiant d'enseignant qui s'occupera de cette séance
            - @param module : La référence du module enseigné dans cette séance
            - @param salle : Le nom de la salle dans laquelle va se dérouler cette séance
            
            -@return : L'identifiant de la séance modifiée
            
            - @Throws PDOException: Si la séance n'est pas valide (S'elle ne respecte pas les conditions déclarée ci-dessus)
            - @Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit droit_creation_cours 
        */
        public function modifierSeance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle){
            Utilisateur::possedeDroit('droit_creation_cours');
            
            $stmt = self::$db->prepare(self::$requete_modifier_seance);

            $stmt->bindValue(':id_seance', $this->id_seance);
            $stmt->bindValue(':date_seance', $date_seance);
            $stmt->bindValue(':heure_depart', $heure_depart);
            $stmt->bindValue(':duree', $duree);
            $stmt->bindValue(':groupe', $groupe);
            $stmt->bindValue(':enseignant', $enseignant);
            $stmt->bindValue(':module', $module);
            $stmt->bindValue(':salle', $salle);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        }

        public function supprimerSeance(){
            self::$db->beginTransaction();

            $stmt1 = self::$db->prepare(self::$requete_supprimer_absences);
            $stmt2 = self::$db->prepare(self::$requete_supprimer_seance);

            $stmt1->bindValue(':id_seance', $this->id_seance);
            $stmt2->bindValue(':id_seance', $this->id_seance);

            $stmt1->execute();
            $stmt2->execute();

            self::$db->commit();
        }


        /*
            - Renvoie la liste des séances entre deux dates
            
            - @param debut  : La date du début de la séance
            - @param fin    : La date maximale des séances recherchées

            -@return data   : {
                id_seance: integer,
                id_groupe: integer,
                id_enseignant: integer,
                ref_module: string,
                ref_semestre: string, 
                nom_groupe: string,
                nom_salle: string,
                date_seance : date 'YYYY-MM-DD',
                heure_depart_seance: time 'hh:mm:ss',
                duree_seance : time,
                nom_groupe: string,
                nom_enseignant: string,
                prenom_enseignant: string,
                nom_module: string,
                couleur_module: string,
                abreviation_module: string
            }

            - @Throws PDOException: Si un problème survient au niveau de la base de données
        */
        public static function liste_seances($debut, $fin){
            $stmt = self::$db->prepare(self::$requete_liste_seances);

            $stmt->bindValue(':startDate', $debut);
            $stmt->bindValue(':endDate', $fin);

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();      
            
            return $resultat;
        }


        /*
            -Ajoute une séance dans la base de données
            -@param : Comme pour modifierSeance
            -@return : L'identifiant de la nouvelle séance
            -@Throws PDOException: Si la séance n'est pas valide, c'est-à-dire 
                qu'elle ne respecte pas les conditions de validité d'une séance
            -@Throws NonAutoriseException: Si l'utilisateur courant ne possède pas le droit droit_creation_cours
        */
        public static function ajouterSeance($date_seance, $heure_depart, $duree, $groupe, $enseignant, $module, $salle){
            Utilisateur::possedeDroit('droit_creation_cours');

            $stmt = self::$db->prepare(self::$requete_modifier_seance);

            $stmt->bindValue(':id_seance', -1);
            $stmt->bindValue(':date_seance', $date_seance);
            $stmt->bindValue(':heure_depart', $heure_depart);
            $stmt->bindValue(':duree', $duree);
            $stmt->bindValue(':groupe', $groupe);
            $stmt->bindValue(':enseignant', $enseignant);
            $stmt->bindValue(':module', $module);
            $stmt->bindValue(':salle', $salle);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        }
    }
?>