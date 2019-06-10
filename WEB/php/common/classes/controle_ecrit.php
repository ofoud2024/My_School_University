<?php
  require_once __DIR__ . "./../../verify.php";
  require_once __DIR__ . "/../Database.php";
  require_once __DIR__ . "/classe_generique.php";
  

  class ControleEcrit extends ClasseGenerique
  {

    private static $requete_verif_controle      = "select * from controle 
                                                    inner join module using(ref_module)
                                                    inner join module_enseigne_par using(ref_module)
                                                    where id_controle = ? and id_enseignant = ? 
                                                    and est_responsable";

    private static $requete_creer_controle       = "select ajouter_controle_papier(
                                                        :nom_controle,
                                                        :date_controle,
                                                        :module_controle, 
                                                        :coefficient,
                                                        :id_enseignant
                                                    )";

    private static $requete_modifier_note        = "select modifier_note_controle(
                                                        :id_controle,
                                                        :pseudo, 
                                                        :note,
                                                        :commentaire
                                                    )";

    private static $requete_liste_controle    = "select pseudo_utilisateur, nom_utilisateur, 
                                                    prenom_utilisateur, note_controle, commentaire_controle
                                                    from notes_controle 
                                                    inner join etudiant using(num_etudiant)
                                                    inner join utilisateur using(id_utilisateur)
                                                    where id_controle = :id_controle 
                                                   ";

    private static $requete_liste_controles     = "select controle.* from controle 
                                                        inner join module using(ref_module)
                                                        inner join module_enseigne_par using(ref_module)
                                                        where id_enseignant = :id_enseignant
                                                        and est_responsable";

    private static $requete_details_controle    = "select * from controle where id_controle = :id_controle";
    

    private static $requete_supprimer_notes     = "delete from notes_controle where id_controle = :id_controle";
    private static $requete_supprimer_controle  = "delete from controle where id_controle = :id_controle";

    private $id_controle;
    private $liste_notes;
    private $details_controle;

    public function __construct($id_controle){
        $id_enseignant = Enseignant::idEnseignantCourant();
        parent::__construct(self::$requete_verif_controle, array($id_controle,$id_enseignant));
        $this->id_controle = $id_controle;
    }

    public function ajouterNote($pseudo, $note, $commentaire){
        $stmt = self::$db->prepare(self::$requete_modifier_note);
        
        $stmt->bindValue(":id_controle", $this->id_controle);
        $stmt->bindValue(":pseudo", $pseudo);
        $stmt->bindValue(":note", $note);
        $stmt->bindValue(":commentaire", $commentaire);

        $stmt->execute();

    }

    public function ajouterNotes($notes){
        $already_in_Transaction = self::$db->inTransaction();

        if(!$already_in_Transaction){
            self::$db->beginTransaction();
        }
        
        foreach($notes as $note){
            $this->ajouterNote($note['pseudo'], $note['note'], $note['commentaire']);
        }

        if(!$already_in_Transaction){
            self::$db->commit();
        }
    }

    public function detailsControle(){
        if(!$this->details_controle){
            $stmt = self::$db->prepare(self::$requete_details_controle);
            $stmt->bindValue(':id_controle', $this->id_controle);
            $stmt->execute();
            $this->details_controle = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        return $this->details_controle;
    }
                     
    public function getListeNotes(){
        
        if(!$this->liste_notes){
            $stmt = self::$db->prepare(self::$requete_liste_controle);
            $stmt->bindValue(':id_controle', $this->id_controle);
            $stmt->execute();
            $this->liste_notes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();    
        }
        
        return $this->liste_notes;

    }

    public function supprimerControle(){
        self::$db->beginTransaction();

        $stmt1 = self::$db->prepare(self::$requete_supprimer_notes);
        $stmt2 = self::$db->prepare(self::$requete_supprimer_controle);

        $stmt1->bindValue(':id_controle', $this->id_controle);
        $stmt2->bindValue(':id_controle', $this->id_controle);

        $stmt1->execute();
        $stmt2->execute();

        self::$db->commit();
    }

    public static function creerControle($nom_controle, $date_controle, $module_controle, $coefficient){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_creer_controle);
        
        $stmt->bindValue(":nom_controle", $nom_controle);
        $stmt->bindValue(":date_controle", $date_controle);
        $stmt->bindValue(":module_controle", $module_controle);
        $stmt->bindValue(":coefficient", $coefficient);
        $stmt->bindValue(':id_enseignant', $id_enseignant);

        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_NUM)[0];
    }

    public static function listeControles(){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_liste_controles);

        $stmt->bindValue(':id_enseignant',$id_enseignant);

        $stmt->execute();
        
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $resultat;
    }
    
    
  }