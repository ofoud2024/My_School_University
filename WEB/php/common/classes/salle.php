<?php
  require_once __DIR__ . "/../../verify.php";
  require_once __DIR__ . "/classe_generique.php";

  class Salle extends ClasseGenerique
  {

      //Détails d'une salle
      private static $salleQuery            = "select * from salle where nom_salle= :nom;";

      //Récupère l'ensemble des salles
      private static $allSallesQuery        = "select * from salle;";

      //Ajoute une salle
      private static $insertSalleQuery      = "insert into salle values(:nom, :nb_pc, :nb_places, :contient_projecteur);";

      //Supprime une salle
      private static $deleteSalleQuery      = "delete from salle where nom_salle= :nom;";


      //Met à jour les informations concernant une salle
      private static $updateSalleQuery      = "update salle set
                                              nombre_ordinateurs_salle= :nb_pc, 
                                              nombre_places_salle= :nb_places, 
                                              contient_projecteur_salle= :contient_projecteur 
                                              where nom_salle= :nom;";

      private $nom_salle;

      private $informations_salle;
    
      /*
        - Instance une salle
        - @param nom_salle: le nom de la salle
        - @Throws PDOException
        - @Throws ElementIntrouvable : Si aucune salle ne porte ce nom
      */
      public function __construct($nom_salle)
      {
          parent::__construct(self::$salleQuery, array(":nom"=>$nom_salle));
          $this->nom_salle = $nom_salle;
      }



      /*
        - Renvoie les détails de la salle
        - @return {
                nom_salle : string,
                nombre_ordinateurs_salle: integer,
                nombre_places_salle     : integer,
                contient_projecteur_salle:boolean
        }
        -@Throws PDOException
      */
       public function detailsSalle()
      {
          if (!$this->informations_salle) {
              $stmt = self::$db->prepare(self::$salleQuery);

              $stmt->bindValue(":nom", $this->nom_salle);
  
              $stmt->execute();

              $this->informations_salle = $stmt->fetch(PDO::FETCH_ASSOC);
          }
          return $this->informations_salle;
      }


        /*
            - Supprime une salle
            - @Throws PDOException
            - @Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit création cours
      */
      public function supprimerSalle() {
          $stmt = self::$db->prepare(self::$deleteSalleQuery);
          
          $stmt->bindValue(':nom', $this->nom_salle);
          
          $stmt->execute();
      }

    /*
        -Modifie la salle courante
        
        -@param nb_pc: nombre d'ordinateurs dans la salle
        -@param nb_places: nombre de tables dans la salle
        -@param contient_projecteur: Vrai si la salle contient un vidéo-projecteur
        
        -@Throws PDOException
        -@Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit de création des cours

    */
    public function modifierSalle($nb_pc, $nb_places, $contient_projecteur)
      {
          Utilisateur::possedeDroit('droit_creation_cours');

          $stmt = self::$db->prepare(self::$updateSalleQuery);

          $stmt->bindValue(":nom", $this->nom_salle);
          
          $stmt->bindValue(':nb_pc', $nb_pc);

          $stmt->bindValue(':nb_places', $nb_places);

          if($contient_projecteur){
            $stmt->bindValue(':contient_projecteur', 'true');
          }else{
            $stmt->bindValue(':contient_projecteur', 'false');
          }

          $stmt->execute();
      }


      /*
        -Renvoie la liste des salles
        -returns tableau de : {
            nom_salle: string,
            nombre_ordinateurs_salle: integer,
            nombre_places_salle     : integer,
            contient_projecteur_salle:boolean
        }
        -@Throws PDOException
      */

      public static function liste_salles() {
        $stmt = self::$db->prepare(self::$allSallesQuery);
      
        $stmt->execute();

        $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt->closeCursor();      

        return $resultat;
    }


    /*
        -Ajoute une salle
        
        -@param nom: Le nom de la salle
        -@param nb_pc: nombre d'ordinateurs dans la salle
        -@param nb_places: nombre de tables dans la salle
        -@param contient_projecteur: Vrai si la salle contient un vidéo-projecteur
        
        -@Throws PDOException
        -@Throws NonAutoriseException : Si l'utilisateur ne possède pas le droit de création des cours

    */
    public static function ajouter_salle($nom, $nb_pc, $nb_places, $contient_projecteur)
    {
        Utilisateur::possedeDroit('droit_creation_cours');

        $stmt = self::$db->prepare(self::$insertSalleQuery);

        $stmt->bindValue(':nom', $nom);

        $stmt->bindValue(':nb_pc', $nb_pc);

        $stmt->bindValue(':nb_places', $nb_places);

        if($contient_projecteur){
          $stmt->bindValue(':contient_projecteur', 'true');
        }else{
          $stmt->bindValue(':contient_projecteur', 'false');
        }

        $stmt->execute();
    }

  }
