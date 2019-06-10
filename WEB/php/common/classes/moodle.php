<?php
  require_once __DIR__ . "./../../verify.php";
  require_once __DIR__ . "/../Database.php";
  require_once __DIR__ . "/classe_generique.php";
  
  /*
    -Un enseignant ne peut ajouter les cours qu'aux modules dont il est responsable
    -L'erreur PDOException sera déclenchée dans le cas où un enseignant essaie de charger 
        un fichier dans un module qui lui appartienent pas.
  */

/*
    -Cette classe modélise un dépôt. 
    -Elle permet aussi de récupérer les informations relatives aux cours
*/
  class Moodle extends ClasseGenerique
  {
    //Vérifie l'éxistance d'un dépôt
    private static $requete_verifier_depot  = "select * from depot_exercice 
                                                 inner join support_pedagogique using(id_support) 
                                                 where id_depot_exercice = ? and id_enseignant = ?";

    //Insère un support pédagogique(Un cours)
    private static $requete_ajout_support    =   "insert into support_pedagogique values(
                                                    default,
                                                    :nom_support,
                                                    :lien,
                                                    now(),
                                                    :date_ouverture,
                                                    :est_cache,
                                                    0,
                                                    :id_enseignant,
                                                    :module
                                                )"; 

    //Liste tous les supports disponibles concernant un enseignant donnée
    private static $requete_liste_support        =   "select * from support_pedagogique
                                                    where id_enseignant = :id_enseignant
                                                    order by  date_depot_support desc, ref_module "; 

    //Liste tous les dépôts ouverts par un enseignant
    private static $requete_liste_depots        =   "select groupe.id_groupe, groupe.nom_groupe,
                                                    support_pedagogique.* , depot_exercice.*
                                                    from depot_exercice 
                                                    inner join support_pedagogique using(id_support) 
                                                    inner join groupe on(groupe_depot = id_groupe)
                                                    where id_enseignant = :id_enseignant
                                                    order by ref_module, nom_support "; 

 
    //Récupère tous les dépôts des étudiants pour un dépôt ouvert.
    private static $requete_depots_etudiants     = "select pseudo_utilisateur,  
                                                    commentaire_depot_etudiant as commentaire_etudiant, etudiant.num_etudiant,
                                                    depot.id_depot_exercice,
                                                    commentaire_depot as commentaire_enseignant, note_depot
                                                    from depot_etudiant as depot
                                                    inner join etudiant  using(num_etudiant) 
                                                    inner join utilisateur using(id_utilisateur)
                                                    left join enseignant_commente_depot as e 
                                                        on(depot.id_depot_exercice = e.id_depot_exercice  and etudiant.num_etudiant = e.num_etudiant)
                                                    where depot.id_depot_exercice = :id_depot 
                                                    ";

    //Récupère les détails d'un dépôt étudiant
    private static $details_depot_etudiant       = "select pseudo_utilisateur, nom_support,
                                                        depot_etudiant.* 
                                                        from depot_etudiant
                                                        inner join support_pedagogique using(id_support)
                                                        inner join etudiant using(num_etudiant)
                                                        inner join utilisateur using(id_utilisateur) 
                                                        where num_etudiant = :id_etudiant 
                                                        and id_depot_exercice = :id_depot";

    //Ouvre un dépôt
    private static $requete_ouvrir_depot         =   "select ouvrir_depot(
                                                        :nom_depot,
                                                        :module_depot,
                                                        :groupe_depot,
                                                        :lien_depot,
                                                        :date_debut,
                                                        :date_fermeture,
                                                        :date_ouverture,
                                                        :enseignant,
                                                        :coefficient
                                                    )";


    //Change la note ou le commentaire d'une correction du dépôt
    private static $requete_changer_note         =   "select changer_note(
                                                        :id_etudiant,
                                                        :id_enseignant,
                                                        :id_depot,
                                                        :note,
                                                        :commentaire
                                                     )";

    private static $requete_changer_status_support = "update support_pedagogique set support_est_cachee = not support_est_cachee 
                                                        where id_support = :id_support and id_enseignant = :id_enseignant";
    

    private static $requete_supprimer_support     = "delete from support_pedagogique 
                                                        where id_support = :id_support and id_enseignant = :id_enseignant";

    
    private static $requete_supprimer_depot       = "select supprimer_depot(
                                                            :id_depot
                                                    )";
    
    private $id_depot;
    private $details_depot;
    private $depots_etudiants;


    /*
        - Instancie un dépôt
        - @param id_depot: L'identifiant du dépôt à instancier
        - @Throws ElementIntrouvable : Si le dépôt n'éxiste pas ou si il appartient à un autre enseignant
        - @Throws PDOException
    */
    public function __construct($id_depot){
        try{
            $id_enseignant = Enseignant::idEnseignantCourant();
        }catch(PasEnseignantException $e){
            $id_enseignant = null;
        }

        parent::__construct(self::$requete_verifier_depot, array($id_depot, $id_enseignant));    

        $this->id_depot = $id_depot;
    }

    /*
        -Récupère l'ensemble des dépôts des étudiants

        -@return tableau de : {
            pseudo_utilisateur      : string,
            num_etudiant            : string,
            commentaire_etudiant    : string,
            commentaire_enseignant  : string,
            id_depot_exercice       : integer,
            note_depot              : float
        }
        -@Throws PDOException: Si la récupération de la liste a échouée.
    */

    public function depots_etudiants(){
    
        if(!$this->depots_etudiants){
            $stmt = self::$db->prepare(self::$requete_depots_etudiants);

            $stmt->bindValue(':id_depot', $this->id_depot);
    
            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();      
    

            $this->depots_etudiants = $resultat;
        }

        return $this->depots_etudiants;

    
    }


    /*
        -Récupère le dépôt de l'étudiant
        -@param id_etudiant: L'identifiant de l'étudiant
        -@return tableau de : {
            pseudo_utilisateur         : string,
            nom_support                : string,
            commentaire_depot_etudiant : string,
            date_depot_etudiant        : string,
            lien_depot_etudiant        : string,
            num_etudiant               : string,
            id_depot_exercice          : integer
        }

        -@Throws PDOException
    */
    public function getDepotEtudiant($id_etudiant){
        $stmt = self::$db->prepare(self::$details_depot_etudiant);

        $stmt->bindValue(":id_etudiant", $id_etudiant);
        $stmt->bindValue(":id_depot", $this->id_depot);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
        -Modifie le commentaire ou la note d'un dépôt étudiant
        -@param id_etudiant : L'identifiant de l'étudiant voulu.
        -@param note integer: La nouvelle note de l'étudiant.
        -@param commentaire string: Le commentaire de l'enseignant sur le dépôt étudiant
        -Cette function ne fonctionne ne produit aucun résultat si le dépôt de l'étudiant n'éxiste pas
        -@Throws PDOException
    */

    public function modifierNote($id_etudiant, $note, $commentaire){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_changer_note);

        $stmt->bindValue(':id_etudiant', $id_etudiant);
        $stmt->bindValue(':id_enseignant', $id_enseignant);
        $stmt->bindValue(':id_depot', $this->id_depot);
        $stmt->bindValue(':note', $note);
        $stmt->bindValue(':commentaire', $commentaire);

        $stmt->execute();
    }

    public function supprimerDepot(){
        $stmt = self::$db->prepare(self::$requete_supprimer_depot);
        $stmt->bindValue(':id_depot', $this->id_depot);
        $stmt->execute();
    }

    /*
        -Ajoute un support de cours (Lors d'un dépôt de cours).
        -@param nom_support: Le nom qui apparaîtra sur le support.
        -@param lien: le lien vers le dépôt sur le disque
        -@param date_ouverture: La date de début d'ouverture du dépôt
        -@param est_caché: Si oui, alors le dépôt ne sera pas téléchargeable pour les étudiants
        -@param module: Le module concerné par le dépôt

        -@Throws PDOException: Si l'enseignant n'est pas responsable du module
        -@Throws PasEnseignantException: Si l'utilisateur courant n'est pas un enseignant
    */
    public static function ajouter_support($nom_support, $lien, $date_ouverture, $est_cache, $module){
        $id_enseignant = Enseignant::idEnseignantCourant();
        
        $stmt = self::$db->prepare(self::$requete_ajout_support);

        $stmt->bindValue(":nom_support", $nom_support);
        $stmt->bindValue(":lien", $lien);
        $stmt->bindValue(":date_ouverture", $date_ouverture);
        $stmt->bindValue(":est_cache", $est_cache);
        $stmt->bindValue(":id_enseignant", $id_enseignant);
        $stmt->bindValue(":module", $module);

        $stmt->execute();
    }


    /*
        - Renvoie la liste des supports pédagogiques déposés par l'enseignant courant
        - @Throws PDOException.
        - @Throws PasEnseignantException: Si l'utilisateur actuel n'est pas un enseignant.
    */

    public static function liste_supports(){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_liste_support);

        $stmt->bindValue(":id_enseignant", $id_enseignant);

        $stmt->execute();

        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();      

        return $resultat;
    }


    /*
        -Renvoie la liste des dépôts ouvert par l'utilisateur actuel
        -@return tableau de data: {
                id_groupe                       : integer,
                id_support                      : integer,
                ref_module                      : string,
                groupe_depot                    : integer,
                nom_groupe                      : string,
                lien_fichier_support            : string,
                date_depot_support              : date,
                date_ouverture_support          : date,
                support_est_cachee              : boolean,
                nb_consultation_support         : integer,
                date_debut_depot_exercice       : timestamp,
                date_fermeture_depot_exercice   : timestamp,
                coefficient_depot               : double
        }
        -@Throws PDOException
        -@Throws PasEnseignantException: Si l'utilisateur courant n'est pas un enseignant
    */
    public static function liste_depots(){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_liste_depots);

        $stmt->bindValue(":id_enseignant", $id_enseignant);

        $stmt->execute();

        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();      

        return $resultat;
    }

    

    /*
        -Permet l'ouverture d'un dépôt
        
        -@param string nom_depot        : Le nom que portera le dépôt, ainsi que le support lors de son téléchargement.
        -@param string module           : La référence du module
        -@param integer groupe_depot    : L'identifiant du groupe auquelle est destiné ce dépôt.
        -@param path    lient_depot     : Le chemin vers le support d'éxercice sur le disque
        -@param timestamp date_debut    : La date où le date sera ouvert pour que les étudiants puisse déposé.
        -@param timestamp date_fermeture: La date après laquelle aucun étudiant ne pourra encore déposé.
        -@param date    date_ouverture  : La date après laquelle le dépôt sera visible.
        -@param double  coefficient     : Le coefficient du dépôt, 0 si le dépôt n'est pas noté 
        
        -@throws PDOException: Si l'ouverture du dépôt a échouée.
        -@throws PasEnseignantException: Si l'utilisateur courant n'est pas un enseignant.
    */

    public static function ouvrir_depot($nom_depot,$module,$groupe_depot, $lien_depot, $date_debut, $date_fermeture, $date_ouverture, $coefficient){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_ouvrir_depot);

        $stmt->bindValue(":nom_depot", $nom_depot);
        $stmt->bindValue(":module_depot", $module);
        $stmt->bindValue(":enseignant", $id_enseignant);
        $stmt->bindValue(":groupe_depot", $groupe_depot);
        $stmt->bindValue(":lien_depot", $lien_depot);
        $stmt->bindValue(":date_debut", $date_debut);
        $stmt->bindValue(":date_fermeture", $date_fermeture);
        $stmt->bindValue(":date_ouverture", $date_ouverture);

        $stmt->bindValue(":coefficient", $coefficient);

        $stmt->execute();
    }


    public static function changerEtatSupport($id_support){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_changer_status_support);

        $stmt->bindValue(':id_support', $id_support);
        $stmt->bindValue(':id_enseignant', $id_enseignant);

        $stmt->execute();
    }

    public static function supprimerSupport($id_support){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_supprimer_support);

        $stmt->bindValue(':id_support', $id_support);
        $stmt->bindValue(':id_enseignant', $id_enseignant);

        $stmt->execute();
    }





  }
