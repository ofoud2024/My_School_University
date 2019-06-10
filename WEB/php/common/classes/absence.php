<?php
  require_once __DIR__ . "./../../verify.php";
  require_once __DIR__ . "/../Database.php";
  require_once __DIR__ . "/classe_generique.php";
  

  class Absence extends ClasseGenerique
  { 
    private static $requete_absences_etudiant = "select seance.*, 
                                                    module.*,
                                                    commentaire_absence,
                                                    absence_est_justifiee
                                                    from etudiant_absent 
                                                    inner join seance using(id_seance)
                                                    inner join module using(ref_module)
                                                    where num_etudiant = :num_etudiant
                                                    and date_seance between :date_debut and :date_fin
                                                 ";
                                                 
    private static $requete_somme_absences    = "select sum(case when absence_est_justifiee then duree_seance else null end) as absences_justifies,
                                                    sum(case when not absence_est_justifiee then duree_seance else null end) as absences_non_justifies
                                                    from etudiant_absent inner join seance using(id_seance)
                                                    where num_etudiant = :num_etudiant 
                                                    and date_seance between :date_debut and :date_fin
                                                    ";


    private static $requete_liste_etudiants   = "select distinct nom_utilisateur, prenom_utilisateur, pseudo_utilisateur, 
                                                    case when absence_est_justifiee is null then false else true end as est_absent 
                                                    from seance inner join groupe using(id_groupe)
                                                    inner join utilisateur on (utilisateur_appartient_a_groupe(id_utilisateur, id_groupe))
                                                    inner join etudiant using(id_utilisateur)
                                                    left join etudiant_absent  on(etudiant_absent.id_seance = seance.id_seance and etudiant.num_etudiant = etudiant_absent.num_etudiant)
                                                    where id_enseignant = :id_enseignant
                                                    and (now(), Interval '00:00:00') overlaps 
                                                    (date_seance + heure_depart_seance::time::interval, duree_seance ::time )
                                                ";


    private static $requete_modifier_absences = "select modifier_absences(:id_seance, :pseudo)";


    private static $requete_nettoyer_absences = "select nettoyer_absences(:id_enseignant)";

    public static function absencesEtudiant(){
        
        $num_etudiant = Etudiant::numEtudiantCourant();
        
        if($num_etudiant !== false){
            $stmt = self::$db->prepare(self::$requete_absences_etudiant);
            
            $periode = explode(" => ", Database::getDBYear());

            $stmt->bindValue(':num_etudiant', $num_etudiant);
            
            $stmt->bindValue(':date_debut', $periode[0]);

            $stmt->bindValue(':date_fin', $periode[1]);

            $stmt->execute();    
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $stmt->closeCursor();
            
            return $resultat;
        }else{
            throw new PasEtudiantException();
        }
    }

    public static function sommeAbsencesEtudiant(){
        $num_etudiant = Etudiant::numEtudiantCourant();
        
        if($num_etudiant !== false){
            $stmt = self::$db->prepare(self::$requete_somme_absences);
            
            $periode = explode(" => ", Database::getDBYear());

            $stmt->bindValue(':num_etudiant', $num_etudiant);

            $stmt->bindValue(':date_debut', $periode[0]);

            $stmt->bindValue(':date_fin', $periode[1]);
            
            $stmt->execute();    
            
            $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt->closeCursor();
            
            return $resultat;
        }else{
            throw new PasEtudiantException();
        }

    }

    public static function etudiantsSeance(){
        $id_enseignant = Enseignant::idEnseignantCourant();

        $stmt = self::$db->prepare(self::$requete_liste_etudiants);
            
        $stmt->bindValue(':id_enseignant', $id_enseignant);
        
        $stmt->execute();    
        
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt->closeCursor();
        
        return $resultat;
    
    }

    public static function appliquerAbsences($liste_etudiants){
        self::$db->beginTransaction();
        
        $stmt_nettoyage = self::$db->prepare(self::$requete_nettoyer_absences);
        $stmt_nettoyage->bindValue(':id_enseignant', Enseignant::idEnseignantCourant());
        $stmt_nettoyage->execute();

        $id_seance = $stmt_nettoyage->fetch(PDO::FETCH_NUM)[0];

        $stmt_absences = self::$db->prepare(self::$requete_modifier_absences);
        
        $stmt_absences->bindValue(':id_seance', $id_seance);
        $stmt_absences->bindParam(':pseudo', $pseudo_etudiant);

        foreach($liste_etudiants as $pseudo){
            $pseudo_etudiant = $pseudo;
            $stmt_absences->execute();
        }

        self::$db->commit();
    }

  }
