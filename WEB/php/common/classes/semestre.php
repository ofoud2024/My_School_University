<?php
  require_once __DIR__ . "/../../verify.php";
  require_once __DIR__ . "/classe_generique.php";
  
  class Semestre extends ClasseGenerique
  {
      //Vérifie si un semestre éxiste
      private static $semestreQuery         = "select * from semestre where ref_semestre = :ref";


      //Retourne tous les résultats des années où ce semestre a été enseigné
      private static $semestreYearsQuery    = "select date_debut::varchar || ' => ' || date_fin::varchar as annee, 
                                                avg(moyenne) as moyenne,
                                                sum(case when est_valide then 1 else 0 end) as nombre_reussite,
                                                sum(case when not est_valide then 1 else 0 end) as nombre_echecs,
                                                round(sum(case when est_valide then 1 else 0 end) / count(ref_semestre), 2) as taux_reussite,
                                                round(sum(case when not est_valide then 1 else 0 end) / count(ref_semestre), 2) as taux_echecs
                                                from etudie_en 
                                                where ref_semestre = :ref
                                                group by ref_semestre, date_debut::varchar || ' => ' || date_fin::varchar
                                                order by annee desc";

      //Retourne la liste des étudiants du semestre
      private static $semestreStudents      = "select date_debut::varchar || ' => ' || date_fin::varchar as annee, etudiant.num_etudiant, nom_utilisateur, prenom_utilisateur, moyenne
                                                from etudie_en inner join etudiant using(num_etudiant)
                                                inner join utilisateur using (id_utilisateur)
                                                where ref_semestre = :ref 
                                                order by annee desc
                                                ";

      //Retourne la liste de tous les semestres
      private static $allSemestresQuery     = "select semestre.*, 
                                                sum(case when est_valide then 1 else 0 end) as nombre_reussite,
                                                sum(case when not est_valide then 1 else 0 end) as nombre_echoue
                                                from semestre left join etudie_en using(ref_semestre)
                                                group by ref_semestre order by nom_semestre
                                                ";

      //Retourne tous les groupes qui appartenant au semestre
      private static $semestreGroupsQuery   = "select distinct parent.id_groupe as id_semestre,
                                                      enfant.id_groupe, enfant.nom_groupe
                                                      from groupe as parent 
                                                      inner join sous_groupe on (est_un_sous_groupe(id_groupe_fils, parent.id_groupe))
                                                      inner join groupe as enfant on(id_groupe_fils = enfant.id_groupe)
                                                      where parent.nom_groupe = :nom_semestre
                                                      order by enfant.nom_groupe DESC
                                                ";

      private static  $semestreModulesQuery  = "select ref_module, nom_module, ref_semestre, couleur_module, abreviation_module  
                                                  from module where ref_semestre = :ref order by ref_module";

      //Ajoute un semestre dans la base de donnnées
      private static $insertSemestreQuery   = "insert into semestre values(:ref, :nom, :pts_ets, :periode)";

      //Met à jour les données d'un semestre
      private static $updateSemestreQuery   = "update semestre set nom_semestre = :nom, points_ets_semestre = :pts_ets where ref_semestre = :ref";

      //Supprime un étudiant du semestre
      private static $deleteStudentQuery    = "delete from etudie_en where ref_semestre = :ref and num_etudiant = :num_etudiant and date_debut = :debut";

      //Supprime un semestre
      private static $deleteSemestreQuery   = "delete from semestre where ref_semestre = :ref";


      private $ref_semestre;
      private $informations_semestre;
      private $modules;
      private $anneesSemestre;
      private $etudiantsSemestre;
    
      /*
        - Instancie un semestre
        - @param ref_semestre: le semestre que l'on veut instancier
        - @Throws PDOException
        - @Throws ElementIntrouvable: Si aucun semestre ne porte cette référence
      */
      public function __construct($ref_semestre)
      {
          parent::__construct(self::$semestreQuery,array(":ref"=>$ref_semestre));
          $this->ref_semestre = $ref_semestre;
      }


      /*
        - Renvoie les détails du semestre
        - @return data: {
            ref_semestre        : string,
            nom_semestre        : string,
            points_ets_semestre : integer,
            periode_semestre    : 1 ou 2, (Sa période dans l'année)
        }
        - @Throws PDOException
      */
      public function detailsSemestre()
      {
          if (!$this->informations_semestre) {
              $stmt = self::$db->prepare(self::$semestreQuery);

              $stmt->bindValue(":ref", $this->ref_semestre);
  
              $stmt->execute();

              $this->informations_semestre = $stmt->fetch(PDO::FETCH_ASSOC);

              $stmt->closeCursor();
          }

          return $this->informations_semestre;
      }

      /*
        - Renvoie la liste des années où s'est déroulé ce semestre.
        - @returns tableau de : {
                annee           : string (date_debut => date_fin), 
                moyenne         : float,
                nombre_reussite : integer,
                nombre_echecs   : integer,
                taux_reussite   : double,
                taux_echecs     : double
            } 
         - @Throws PDOException
         - @Throws NonAutoriseException : Si l'utilisateur ne possède le droit creation modules
      */
      public function anneesSemestre()
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          if (!$this->anneesSemestre) {
              $stmt = self::$db->prepare(self::$semestreYearsQuery);

              $stmt->bindValue(":ref", $this->ref_semestre);
            
              $stmt->execute();

              $this->anneesSemestre = $stmt->fetchAll(PDO::FETCH_ASSOC);

              $stmt->closeCursor();
          }

          return $this->anneesSemestre;
      }

    /*
        - Renvoie la liste des étudiants qui ont passé ce semestre.
        - @returns tableau de : {
                annee               : string (date_debut => date_fin), 
                num_etudiant        : integer,
                nom_utilisateur     : string,
                prenom_utilisateur  : string,
                moyenne             : double
            } 
         - @Throws PDOException
         - @Throws NonAutoriseException : Si l'utilisateur ne possède le droit visualisation statistique
    */
      public function etudiantsSemestre()
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          if (!$this->etudiantsSemestre) {
              $stmt = self::$db->prepare(self::$semestreStudents);

              $stmt->bindValue(":ref", $this->ref_semestre);
            
              $stmt->execute();

              $this->etudiantsSemestre = $stmt->fetchAll(PDO::FETCH_ASSOC);

              $stmt->closeCursor();
          }

          return $this->etudiantsSemestre;
      }

      /*
        - Retourne la liste des modules associé à ce semestre
        - @returns tableau de :{
            ref_module: string,
            ref_semestre: string,
            nom_module: string,
            couleur_module: string,
            abreviation_module: string
        }
        -@Throws PDOException
      */

      public function modulesSemestre(){
        
        if (!$this->modules) {
            $stmt = self::$db->prepare(self::$semestreModulesQuery);

            $stmt->bindValue(":ref", $this->ref_semestre);
          
            $stmt->execute();

            $this->modules = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();
        }

        return $this->modules;

      }

      /*
        - Renvoie la liste des groupes associés à ce semestre.
        - @returns un tableau de :{
            id_semestre : L'identifiant du groupe ayant le même nom que la référence du semestre,
            id_groupe   : L'identifiant du groupe fils,
            nom_groupe  : Le nom du groupe fils
        }
        - @Throws PDOException
      */

      public function groupes_semestre(){
        $stmt = self::$db->prepare(self::$semestreGroupsQuery);

        $stmt->bindValue(':nom_semestre', $this->ref_semestre);

        $stmt->execute();

        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();

        return $resultat;
    }




      /*
        - Modifie un semestre
        - On ne peut modifier que le nom ou les points ets du semestre.
        - @param nom : string
        - @param pts_ets integer : Les points ets du semestre
        - @Throws PDOException : Si la modification a été refusée
        - @Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit droits_creation_modules
      */
      public function modifierSemestre($nom, $pts_ets)
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          $stmt = self::$db->prepare(self::$updateSemestreQuery);

          $stmt->bindValue(":ref", $this->ref_semestre);
          $stmt->bindValue(":nom", $nom);
          $stmt->bindValue(":pts_ets", $pts_ets);
      
          $stmt->execute();
      }


      
      /*
        - Retire un étudiant de la liste des étudiants du semestre
        - On ne peut retirer que les étudiants de l'année courante
        - @param num_etudiant integer: Le numéro de l'étudiant à retirer
        - @Throws PDOException : Si la modification a été refusée
        - @Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit droits_creation_modules
      */
      
      public function retirerEtudiant($num_etudiant)
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          $stmt = self::$db->prepare(self::$deleteStudentQuery);

          $stmt->bindValue(":ref", $this->ref_semestre);
          $stmt->bindValue(":num_etudiant", $num_etudiant);
          $stmt->bindValue(":debut", explode(" => ",self::getDBYear())[0]);
          
          $stmt->execute();
      }


      /*
        - Supprime ce semestre
        - @Throws PDOException : Si la suppression a été refusé en raison de contraintes d'integrités
        - @Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit droits_creation_modules
      */

      public function supprimerSemestre()
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          $stmt = self::$db->prepare(self::$deleteSemestreQuery);
          
          $stmt->bindValue(':ref', $this->ref_semestre);
          
          $stmt->execute();
      }

      /*
        - Renvoie la liste des semestres
        - Attention, cette function retourne plus de détails que prévu
        - @returns {
                detailsSemestre : Comme détails semestre,
                nombre_reussite : integer,
                nombre_echoue   : integer                                       
            }
        - @Throws PDOException
      */

      public static function liste_semestres()
      {
          $stmt = self::$db->prepare(self::$allSemestresQuery);
        
          $stmt->execute();

          $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

          $stmt->closeCursor();
  
          return $resultat;
      }





      /*
        - Ajoute un semestre
        - @param ref: La référence du nouveau semestre 
        - @param nom : string
        - @param pts_ets integer : Les points ets du semestre
        - @param periode         : La période de l'année dans laquelle va se dérouler se semestre
        - @Throws PDOException : Si l'insertion a échoué pour des raisons d'invalidité du semestre
        - @Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit droits_creation_modules
        - @Throws ParametresIncorrectes: Si la période est différente de 1 et 2.
      */


      public static function ajouter_semestre($ref, $nom, $points_ets, $periode)
      {
          Utilisateur::possedeDroit('droits_creation_modules');

          if($periode == 1 || $periode == 2){
            $stmt = self::$db->prepare(self::$insertSemestreQuery);

            $stmt->bindValue(':ref', $ref);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':pts_ets', $points_ets);
            $stmt->bindValue(':periode', $periode);
  
            $stmt->execute();  
          }else{
              throw new ParametresIncorrectes(); 
          }
      }
      

      
  }
