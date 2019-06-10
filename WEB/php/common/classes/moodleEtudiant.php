<?php
  require_once __DIR__ . "./../../verify.php";
  require_once __DIR__ . "/../Database.php";
  require_once __DIR__ . "/classe_generique.php";
  require_once __DIR__ . "/../exceptions/PasEtudiantException.php";
  require_once __DIR__ . "/module.php";

  class MoodleEtudiant extends ClasseGenerique{

    //Vérifie si le dépôt éxiste et si l'étudiant courant peut y accéder
    private static $verif_depot_etudiant       = " select id_depot_exercice from depot_exercice 
                                                        where id_depot_exercice = ? and utilisateur_appartient_a_groupe(?, groupe_depot)"; 


    private static $requete_prochains_depots   =  "select id_support, id_depot_exercice, nom_support, date_debut_depot_exercice, 
                                                        date_fermeture_depot_exercice,
                                                        date_debut_depot_exercice < now() as depot_a_commencer,
                                                        date_fermeture_depot_exercice < now() as depot_est_fermer
                                                        from depot_exercice 
                                                        inner join support_pedagogique using(id_support)
                                                        where utilisateur_appartient_a_groupe(:id_utilisateur, groupe_depot)
                                                        and ref_module = :ref_module and not support_est_cachee
                                                        order by depot_est_fermer asc, depot_a_commencer desc";

    //Requête permettant de récupérer la liste des cours ouverts pour un module
    private static $requete_cours_module       =   "select nom_support, date_depot_support, id_support 
                                                        from support_pedagogique where id_support not in (
                                                            select id_support from depot_exercice
                                                        ) and ref_module = :ref_module and not support_est_cachee";


    private static $requete_telecharger_support = "select nom_support, lien_fichier_support from support_pedagogique
                                                        where not support_est_cachee and date_ouverture_support < now() 
                                                        and id_support = :id_support";

    private static $requete_aug_nb_visu         = "update support_pedagogique set nb_consultation_support = nb_consultation_support + 1 
                                                            where id_support = :id_support";

    private static $requete_details_depot       = "select support_pedagogique.*,
                                                        depot_exercice.*,
                                                        commentaire_depot, note_depot,
                                                        nom_depot_etudiant, commentaire_depot_etudiant, date_depot_etudiant,
                                                        date_fermeture_depot_exercice < now() as est_fermee,
                                                        nom_groupe
                                                        from depot_exercice 
                                                        inner join support_pedagogique using(id_support)
                                                        left join enseignant_commente_depot on(
                                                            depot_exercice.id_depot_exercice = enseignant_commente_depot.id_depot_exercice
                                                            and enseignant_commente_depot.num_etudiant = :num_etudiant
                                                        )
                                                        left join depot_etudiant on(
                                                            depot_exercice.id_depot_exercice = depot_etudiant.id_depot_exercice
                                                            and depot_etudiant.num_etudiant = :num_etudiant
                                                        )
                                                        inner join groupe on(id_groupe = groupe_depot)
                                                        where depot_exercice.id_depot_exercice = :id_depot
                                                    ";
            
    private static $requete_reponse_depot       = "select ajouter_depot_etudiant (
                                                        :commentaire,
                                                        :lien_depot,
                                                        :num_etudiant,
                                                        :id_depot,
                                                        :nom_depot
                                                    )";
    private static $requete_liste_controles_module = "select note_controle as note,
                                                        date_controle,
                                                        coefficient_controle as coefficient,
                                                        nom_controle
                                                        from notes_controle
                                                        inner join controle using(id_controle)
                                                        where ref_module = :ref_module 
                                                        and num_etudiant = :num_etudiant
                                                        and coefficient_controle > 0
                                                        and note_controle is not null";
    private static $requete_liste_depots_module_notes = "select 
                                                            note_depot as note,
                                                            date_fermeture_depot_exercice::date as date_depot,
                                                            coefficient_depot as coefficient,
                                                            nom_support as nom_depot
                                                            from enseignant_commente_depot
                                                            inner join depot_exercice using(id_depot_exercice)
                                                            inner join support_pedagogique using(id_support)
                                                            where num_etudiant = :num_etudiant
                                                            and ref_module = :ref_module
                                                            and coefficient_depot > 0
                                                            and note_depot is not null";

    private $id_depot;
    private $num_etudiant;
    private $details_depot;
        
    public function __construct($id_depot){
        if(false !== Etudiant::numEtudiantCourant()){
            parent::__construct(self::$verif_depot_etudiant, array($id_depot, Utilisateur::idUtilisateurCourant()));
            $this->id_depot = $id_depot;
            $this->num_etudiant = Etudiant::numEtudiantCourant();
        }else{
            throw new NonAutoriseException();
        }
    }       
    
    public function getDetailsDepot(){
        if(!$this->details_depot){
            $stmt = self::$db->prepare(self::$requete_details_depot);

            $stmt->bindValue(':id_depot', $this->id_depot);
            $stmt->bindValue(':num_etudiant', $this->num_etudiant);

            $stmt->execute();

            $this->details_depot = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        return $this->details_depot;
    }

    public function deposer_exercice($nom_depot, $lien_depot, $commentaire){
        $stmt = self::$db->prepare(self::$requete_reponse_depot);
        
        $stmt->bindValue(':nom_depot', $nom_depot);
        $stmt->bindValue(':lien_depot', $lien_depot);
        $stmt->bindValue(':commentaire', $commentaire);
        $stmt->bindValue(':id_depot', $this->id_depot);
        $stmt->bindValue(':num_etudiant', $this->num_etudiant);

        $stmt->execute();
    }

    public static function liste_depots_ouvert($ref_module){
        $resultat = array();

        if(false !== Etudiant::numEtudiantCourant()){
            $stmt = self::$db->prepare(self::$requete_prochains_depots);
        
            $stmt->bindValue(':ref_module', $ref_module);
            $stmt->bindValue(':id_utilisateur', Utilisateur::idUtilisateurCourant());
            
            $stmt->execute();
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt->closeCursor();
    
            $stmt->execute();    
        }

        return $resultat;
    }


    public static function cours_module($ref_module){
        $stmt = self::$db->prepare(self::$requete_cours_module);
        $stmt->bindValue(':ref_module', $ref_module);
        $stmt->execute();
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $resultat;
    }

    public static function fichier_support($id_support){
        $stmt = self::$db->prepare(self::$requete_telecharger_support);
        $stmt->bindValue(':id_support', $id_support);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function incrementer_nombre_visualisation($id_support){
        $stmt = self::$db->prepare(self::$requete_aug_nb_visu);
        $stmt->bindValue(':id_support', $id_support);
        $stmt->execute();
    }


    /*
        - Calcule la moyenne de l'étudiant en fonction des contrôles qui a fait
        - @returns json : {
            moyenne_calculee: float,
            coefficient_total : float,
            modules: [
                ref_module: string,
                nom_module: string,
                moyenne: float,
                coefficient_module: float,
                liste_depots: [
                    nom_depot: string, 
                    date_depot: date,
                    coefficient: float,
                    note: float
                ],
                liste_controles: [
                    nom_controle: string,
                    date_controle: date,
                    coefficient: float,
                    note: float
                ]
            ]
        }
        -@Throws PasEtudiantException: Lorsque l'utilisateur courant n'est pas un étudiant
        -@Throws PDOException : Si une requête échoue
    */

    public static function moyenne_etudiant(){
        $num_etudiant = Etudiant::numEtudiantCourant();
        if(false !== $num_etudiant){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            if($utilisateur){
                $semestre_etudiant = $utilisateur->informations_simples()["semestre"];
                $liste_modules = Module::listeModules();
                $coefficient_total;
                $moyenne_modules = self::moyennes_modules($liste_modules, $semestre_etudiant);
                $moyenne_calculee = self::calculerMoyenne($moyenne_modules, $coefficient_total, "moyenne", "coefficient_module");

                return array(
                    "moyenne_calculee"=>$moyenne_calculee,
                    "coefficient_total"=>$coefficient_total,
                    "modules"=>$moyenne_modules
                );
            }
        }else{
            throw new PasEtudiantException();
        }
    }

    private static function moyennes_modules($liste_modules, $semestre){
        $modules_semestre = array_filter($liste_modules, function($module) use($semestre){
            return $module['ref_semestre'] == $semestre;
        });
        
        $moyennes = array();

        foreach($modules_semestre as $module){
            $liste_controles = self::listeControlesModules($module['ref_module']);
            $liste_depots    = self::listeDepotsNotes($module['ref_module']);
            
            $coefficient_depots ;
            $coefficient_controles;

            $moyenne_controles =  self::calculerMoyenne($liste_controles, $coefficient_controles);
            $moyenne_depots   = self::calculerMoyenne($liste_depots, $coefficient_depots);
            
            $moyenne_module = null;

            if($moyenne_controles && $moyenne_depots){
                $moyenne_module  = ($moyenne_controles * $coefficient_controles + $moyenne_depots * $coefficient_depots) / ($coefficient_controles + $coefficient_depots);
            }else if($moyenne_controles){
                $moyenne_module = $moyenne_controles;
            }else{
                $moyenne_module = $moyenne_depots;
            }

            array_push($moyennes, array(
                "ref_module"=>$module['ref_module'],
                "nom_module"=>$module['nom_module'],
                "coefficient_module"=>$module['coefficient_module'],
                "moyenne"=>$moyenne_module,
                "liste_depots"=>$liste_depots,
                "liste_controles"=>$liste_controles
            ));
        }

        return $moyennes;
    }


    private static function listeControlesModules($ref_module){
        $stmt = self::$db->prepare(self::$requete_liste_controles_module);
        $stmt->bindValue(':num_etudiant', Etudiant::numEtudiantCourant());
        $stmt->bindValue(':ref_module', $ref_module);
        $stmt->execute();
        $liste_controles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $liste_controles;
    }

    private static function listeDepotsNotes($ref_module){
        $stmt = self::$db->prepare(self::$requete_liste_depots_module_notes);
        $stmt->bindValue(':num_etudiant', Etudiant::numEtudiantCourant());
        $stmt->bindValue(':ref_module', $ref_module);
        $stmt->execute();
        $liste_controles = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $liste_controles;
    }

    private static function calculerMoyenne($liste_notes, &$coefficient_total, $cle_notes = "note", $cle_coefficient = "coefficient", $minVal = 0, $maxVal = 20){
        $somme = 0;
        $coefficient_total = 0;
        
        foreach($liste_notes as $details_note){
            $note = $details_note[$cle_notes];

            if($note !== null && $note !== false && $note >= $minVal && $note <= $maxVal){
                $coefficient_total += $details_note[$cle_coefficient];
                $somme += $note * $details_note[$cle_coefficient];
            }

        }

        if($coefficient_total == 0 ){
            return null;
        }

        return $somme / $coefficient_total;
    }

  }