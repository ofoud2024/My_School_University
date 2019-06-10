<?php

    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/classe_generique.php";

  class Etudiant extends ClasseGenerique
  {

      //Vérifie l'éxistance d'un étudiant
      private static $studentCheckQuery     = "select num_etudiant from etudiant where num_etudiant= :num;";

      //Renvoie le numéro d'étudiant correspondant à un identifiant d'utilisateur
      private static $studentIdQuery        = "select num_etudiant from etudiant natural join utilisateur where id_utilisateur = :id_utilisateur";
      
      //Récupère la liste des étudiants 
      private static $allStudentsQuery      = "select etudiant.num_etudiant,
                                                nom_utilisateur, pseudo_utilisateur,
                                                prenom_utilisateur,
                                                coalesce(sum(semestre.points_ets_semestre), 0) as points_ets 
                                                from utilisateur inner join etudiant using(id_utilisateur) 
                                                left join etudie_en using(num_etudiant) 
                                                left join semestre using(ref_semestre) 
                                                group by id_utilisateur, num_etudiant;";

      //Récupère ses résultats dans les semestres qu'il a passé. 
      private static $studentYears          = "select moyenne, est_valide, ref_semestre, date_debut, date_fin  from etudie_en where num_etudiant = :num";

      //Récupère la liste des utilisateurs qui ne sont pas des etudiants
      private static $possibleStudentsQuery = "select id_utilisateur, pseudo_utilisateur from utilisateur 
                                                where id_utilisateur not in (select id_utilisateur from etudiant)"; 


      //Ajoute un étudiant dans la base de données
      private static $insertStudentQuery   = "insert into etudiant values(:num_etudiant, :id_utilisateur);";


      //Supprime un étudiant
      private static $deleteStudentQuery   = "delete from etudiant where num_etudiant=:num;";

      //Met à jour le semestre actuel d'un étudiant.
      private static $updateStudentSemesterQuery   = "select update_semestre_etudiant(:ref, :num, :date_debut, :date_fin)";

      private static $updateStudentMarkQuery       = "update etudie_en set moyenne = :moyenne, est_valide = :est_valide 
                                                        where num_etudiant = :num and date_debut = :date_debut and date_fin = :date_fin";
      private $num_etudiant;
      private $informations_etudiant;
    

      /*
       - Instancie un étudiant.
       - @Throws PDOException: Si l'éxecution de la requête a échouée
       - @Throws ElementIntrouvable : Si l'étudiant n'éxiste pas.
      */
      public function __construct($num_etudiant)
      {
        parent::__construct(self::$studentCheckQuery, array($num_etudiant));
        $this->num_etudiant = $num_etudiant;
      }




      /*
        - Récupère la moyenne de cet étudiant pendant tous les semestres qu'il a passé
        - @return data = {
            moyenne : float,
            ref_semestre: string,
            date_debut: date,
            date_fin : date
        }
        -@Throws PDOException.
        -@Throws NonAutoriseException : Si on essaie d'accéder aux détails d'un autre étudiant
        et qu'on a pas le droit creation_utilisateurs
       */
      public function detailsEtudiant()
      {
          if(self::numEtudiantCourant() == $this->num_etudiant){
            Utilisateur::possedeDroit('droit_creation_utilisateurs');
          }

          if (!$this->informations_etudiant) {
              $stmt = self::$db->prepare(self::$studentYears);

              $stmt->bindValue(":num", $this->num_etudiant);
  
              $stmt->execute();

              $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

              $stmt->closeCursor();      

              $this->informations_etudiant = $resultat;
          }

          return $this->informations_etudiant;
      }


      /*
        -Supprime cet étudiant
        -@Throws PDOException : Si le num étudiant est associé à d'autre informations en BD.
        -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création utilisateurs
      */

    public function supprimerEtudiant() {
        Utilisateur::possedeDroit('droit_creation_utilisateurs');

        $stmt = self::$db->prepare(self::$deleteStudentQuery);
        
        $stmt->bindValue(':num', $this->num_etudiant);
        
        $stmt->execute();
    }



    /*
     - Modifie le semestre de l'étudiant
     - @param semestre string: Le nouveau semestre de l'étudiant.
     - @Throws PDOException : Si la modification a échouée
     - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de création utilisateurs
    */

    public function modifierSemestreEtudiant($semestre)
    {
        Utilisateur::possedeDroit('droit_creation_utilisateurs');

        $periode = explode(" => ", self::getDBYear());

        $stmt = self::$db->prepare(self::$updateStudentSemesterQuery);

        $stmt->bindValue(":num", $this->num_etudiant);
        $stmt->bindValue(":ref", $semestre);
        $stmt->bindValue(":date_debut", $periode[0]);
        $stmt->bindValue(":date_fin", $periode[1]);

        $stmt->execute();
    }


    public function modifierMoyenneEtudiant($moyenne, $est_valide){
        Utilisateur::possedeDroit('droit_creation_utilisateurs');

        if($moyenne < 0 || $moyenne > 20){
            throw new ParametresIncorrectes();
        }
        
        $periode = explode(" => ", self::getDBYear());

        $stmt = self::$db->prepare(self::$updateStudentMarkQuery);

        $stmt->bindValue(":num", $this->num_etudiant);
        $stmt->bindValue(":moyenne", $moyenne);
        $stmt->bindValue(":est_valide", $est_valide);
        $stmt->bindValue(":date_debut", $periode[0]);
        $stmt->bindValue(":date_fin", $periode[1]);

        $stmt->execute();

    }


    /*
      - Récupère la liste des étudiants.
      - Les informations retournées pour chaque étudiant sont: 
          -num_etudiant       : string,
          -pseudo_utilisateur : string,
          -nom_utilisateur    : string,
          -prenom_utilisateur : string,
          -points_ets         : integer
      -@Throws PDOException.
    */
    public static function liste_etudiants() {
        
        $stmt = self::$db->prepare(self::$allStudentsQuery);
      
        $stmt->execute();
        
        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();      

        return $resultat;
    }


    /*
     - Ajoute un étudiant dans la base de données
     - @param num string: Le numéro d'étudiant.
     - @param id_utilisateur int: L'identifiant de l'utilisateur que l'on souhaite rendre un étudiant.
     - @Throws PDOException : Si l'insertion a échouée
     - @Throws NonAutoriseException : Si l'utilisateur courant n'a pas le droit de création utilisateurs
    */
    public static function ajouter_etudiant($num, $id_utilisateur)
    {
        Utilisateur::possedeDroit('droit_creation_utilisateurs');

        $stmt = self::$db->prepare(self::$insertStudentQuery);

        $stmt->bindValue(':num_etudiant', $num);

        $stmt->bindValue(':id_utilisateur', $id_utilisateur);

        $stmt->execute();
    }



    /*
      - Renvoie la liste des utilisateur pouvant être des étudiants
      - @return tableau de : {
        id_utilisateur: integer,
        pseudo_utilisateur: string
      }
      -@Throws PDOException .
      -@Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit création utilisateurs
    */

    public static function utilisateursPossible(){
        Utilisateur::possedeDroit('droit_creation_utilisateurs');

        $stmt = self::$db->prepare(self::$possibleStudentsQuery);

        $stmt->execute();

        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();      

        return $resultat;
    }

    /*
      - Renvoie le numéro de l'étudiant courant
      - Si l'utilisateur courant n'existe pas ou n'est pas un étudiant,
        alors false est retourné 
    */
    public static function numEtudiantCourant(){
      $utilisateur_courant = Utilisateur::getUtilisateurCourant();
      $num_etudiant_courant = false;

      if($utilisateur_courant){
          $stmt = self::$db->prepare(self::$studentIdQuery);
          $stmt->bindValue(':id_utilisateur', $utilisateur_courant->getIdUtilisateur());
          
          try{
              $stmt->execute();
              $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
              
              if($resultat){
                  $num_etudiant_courant = $resultat['num_etudiant'];
              }

          }catch(PDOException $e){}
      }

      return $num_etudiant_courant;
    }

  }
