<?php

    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../Database.php";
    require_once __DIR__ . "/classe_generique.php";

    class Module extends ClasseGenerique
    {

        //Récupère la liste des module
        private static $modulesQuery        = 'select * from module order by ref_module';

        //Récupère les détails du module
        private static $moduleQuery         = 'select * from module where ref_module = :ref';
        

        //Récupère les enseignants du module
        private static $moduleTeachers      = 'select est_responsable, id_enseignant,
                                               nom_utilisateur, prenom_utilisateur, pseudo_utilisateur 
                                               from module_enseigne_par 
                                               inner join enseignant using(id_enseignant) 
                                               inner join personnel using(id_personnel)
                                               inner join utilisateur using(id_utilisateur)
                                               where ref_module = :ref
                                                ';

        //Récupère les enseignants qui ne sont pas déjà enseignants de ce module
        private static $availableTeachers   = 'select nom_utilisateur, prenom_utilisateur, pseudo_utilisateur, id_enseignant
                                                from enseignant 
                                                inner join personnel using(id_personnel)
                                                inner join utilisateur using(id_utilisateur)
                                                where id_enseignant not in (
                                                    select id_enseignant from module_enseigne_par where ref_module = :ref
                                                ) 
                                                ';


        //Récupère les modules enseignés par un enseignant
        private static $teacherModules     = 'select module.* from module inner join module_enseigne_par using(ref_module)
                                                where est_responsable and id_enseignant = :id_enseignant '; 

        //Ajoute un enseignant en base de données
        private static $insertModuleQuery   = 'insert into module values(
                                                :ref,
                                                :nom,
                                                :coefficient,
                                                :heures_cm,
                                                :heures_tp,
                                                :heures_td,
                                                :couleur_module,
                                                :ref_semestre,
                                                :abreviation)';

        //Ajoute un enseignant à la liste des enseignants du module
        private static $addTeacher          = 'insert into module_enseigne_par values (:est_responsable, :id_enseignant, :ref) ';


        //Met à jour les informations relatives à un module
        private static $updateModuleQuery   = "update module set 
                                                nom_module = :nom,
                                                coefficient_module = :coefficient,
                                                heures_cm_module = :heures_cm,
                                                heures_td_module = :heures_td,
                                                heures_tp_module = :heures_tp,
                                                couleur_module   = :couleur_module,
                                                abreviation_module = :abreviation
                                                where ref_module = :ref";                                        




        //Supprime un enseignant du module
        private static $deleteTeacher       = 'delete from module_enseigne_par where ref_module = :ref
                                                and id_enseignant = :id_enseignant';
        
        //Supprime un enseignant du module                                                
        private static $deleteAllTeachers   = "delete from module_enseigne_par where ref_module = :ref";

        //Supprime un module
        private static $deleteModuleQuery   = 'delete from module where ref_module = :ref';




        private $ref_module;
        private $informations_module;
        private $enseignants_module;
        private $enseignants_a_ajouter;

        /*
            - Instancie un module
            - @param ref_module: La référence du module à instancier
            - @Throws ElementIntrouvable: Si aucun module ne porte cette référence
        */
        public function __construct($ref_module)
        {
            parent::__construct(self::$moduleQuery, array(':ref'=>$ref_module));
            $this->ref_module = $ref_module;
        }


        /*
            - Retourne la liste des enseignants appartenant à ce module
            - @returns tableau de : {
                    id_enseignant: integer,
                    pseudo_utilisateur: string,
                    nom_utilisateur: string,
                    prenom_utilisateur: string,
                    est_responsable: boolean
            }
            -@Throws PDOException
        */
        public function getEnseignantsModule()
        {
            if (!$this->enseignants_module) {
                $stmt = self::$db->prepare(self::$moduleTeachers);

                $stmt->bindValue(':ref', $this->ref_module);
    
                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();      
    
                $this->enseignants_module = $resultat;
            }
            return $this->enseignants_module;
        }


        /*
            -Retourne la liste des enseignants qui n'enseignent pas dans ce module
            -@returns tableau de : {
                    id_enseignant: integer,
                    pseudo_utilisateur: string,
                    nom_utilisateur: string,
                    prenom_utilisateur: string
            }
            -@Throws PDOException
            -@Throws NonAutoriseException: Si l'utilisateur ne possède pas droits_creation_modules.
        */
        public function getEnseignantsAAjouter(){

            Utilisateur::possedeDroit('droits_creation_modules');
            if (!$this->enseignants_a_ajouter) {
                $stmt = self::$db->prepare(self::$availableTeachers);

                $stmt->bindValue(':ref', $this->ref_module);
    
                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();          
                
                $this->enseignants_a_ajouter = $resultat;
            }
            return $this->enseignants_a_ajouter;

        }

        /*
            -Récupère les détails relatives à un module:
            -@return data: {
                ref_module: string,
                nom_module: string,
                coefficient_module: double,
                heures_cm_module: integer,
                heures_td_module: integer,
                heures_tp_module: integer,
                couleur_module: string,
                abreviation_module: string
            }
            -@Throws PDOException
        */

        public function getDetailsModule()
        {
            
            if (!$this->informations_module) {
                $stmt = self::$db->prepare(self::$moduleQuery);

                $stmt->bindValue(':ref', $this->ref_module);
    
                $stmt->execute();

                $this->informations_module = $stmt->fetch(PDO::FETCH_ASSOC);

            }
               
            return $this->informations_module;
        }


        /*
            - Modifie un module
            
            - @param $nom String        : Le nom du module
            - @param $coef double       : Le coefficient du module
            - @param $heures_cm integer : Le nombre des heures de cours magistrales
            - @param $heures_tp integer : Le nombre des heures de TP
            - @param $heures_td integer : Le nombre des heures de TD
            - @param $couleur String    : La couleur du module en hex
            - @param $abreviation String: Le nom du module

            -@Throws PDOException
            -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création des modules
        */
        public function modifierModule($nom, $coef, $heures_cm, $heures_tp, $heures_td, $couleur, $abreviation)
        {
            Utilisateur::possedeDroit('droits_creation_modules');

            $stmt = self::$db->prepare(self::$updateModuleQuery);

            $stmt->bindValue(':ref', $this->ref_module);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':coefficient', $coef);
            $stmt->bindValue(':heures_cm', $heures_cm);
            $stmt->bindValue(':heures_tp', $heures_tp);
            $stmt->bindValue(':heures_td', $heures_td);
            $stmt->bindValue(':couleur_module', $couleur);
            $stmt->bindValue(':abreviation', $abreviation);

            $stmt->execute();
        }


        /*
            -Retire un enseignant de la liste des enseignants
            -@param id_enseignant: L'identifiant de l'enseignant à retirer
            -@throws PDOException
            -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création des modules
        */

        public function retirerEnseignant($id_enseignant)
        {
            Utilisateur::possedeDroit('droits_creation_modules');

            $stmt = self::$db->prepare(self::$deleteTeacher);

            $stmt->bindValue(':ref', $this->ref_module);
            $stmt->bindValue(':id_enseignant', $id_enseignant);
            
            $stmt->execute();
        }


        /*
            -Ajoute un enseignant à la liste des enseignants
            -@param id_enseignant: L'identifiant de l'enseignant à ajouter
            -@param est_responsable Boolean: Si l'enseignant est responsable ou pas de ce module
            -@throws PDOException
            -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création des modules
        */

        public function ajouterEnseignant($id_enseignant, $est_responsable){
            Utilisateur::possedeDroit('droits_creation_modules');

            $stmt = self::$db->prepare(self::$addTeacher);

            $stmt->bindValue(':ref', $this->ref_module);
            $stmt->bindValue(':id_enseignant', $id_enseignant);
            $stmt->bindValue(':est_responsable', $est_responsable);

            $stmt->execute();
        }


        /*
            -Supprime ce module
            -Retire aussi tous les enseignants responsable de ce module.
            -@throws PDOException
            -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création des modules
        */

        public function supprimerModule(){
            Utilisateur::possedeDroit('droits_creation_modules');

            self::$db->beginTransaction();
            
            $stmt1 = self::$db->prepare(self::$deleteAllTeachers);
            $stmt2 = self::$db->prepare(self::$deleteModuleQuery);

            $stmt1->bindValue(':ref', $this->ref_module);
            $stmt2->bindValue(':ref', $this->ref_module);

            $stmt1->execute();
            $stmt2->execute();

            self::$db->commit();
        }



        /*
            -Créer un module
            
            - @param $nom String        : Le nom du module
            - @param $coef double       : Le coefficient du module
            - @param $heures_cm integer : Le nombre des heures de cours magistrales
            - @param $heures_tp integer : Le nombre des heures de TP
            - @param $heures_td integer : Le nombre des heures de TD
            - @param $couleur String    : La couleur du module en hex
            - @param $abreviation String: Le nom du module

            -@throws PDOException        : Si l'insertion a échouée ("Si un module porte déjà la même référence").
            -@Throws NonAutoriseException: Si l'utilisateur courant n'a pas le droit de création des modules
        */

        public static function ajouterModule($ref, $nom, $coef, $heures_cm, $heures_tp, $heures_td, $couleur, $semestre, $abreviation)
        {
            Utilisateur::possedeDroit('droits_creation_modules');

            $stmt = self::$db->prepare(self::$insertModuleQuery);

            $stmt->bindValue(':ref', $ref);
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':coefficient', $coef);
            $stmt->bindValue(':heures_cm', $heures_cm);
            $stmt->bindValue(':heures_tp', $heures_tp);
            $stmt->bindValue(':heures_td', $heures_td);
            $stmt->bindValue(':couleur_module', $couleur);
            $stmt->bindValue(':ref_semestre', $semestre);
            $stmt->bindValue(':abreviation', $abreviation);

            $stmt->execute();
        }


        /*
            -Récupère la liste des modules disponibles
            -@return tableau de data: {
                ref_module: string,
                nom_module: string,
                coefficient_module: double,
                heures_cm_module: integer,
                heures_td_module: integer,
                heures_tp_module: integer,
                couleur_module: string,
                ref_semestre: string,
                abreviation_module: string
            }
            -@Throws PDOException

        */
        public static function listeModules()
        {
            $stmt = self::$db->prepare(self::$modulesQuery);

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();      
    
            return $resultat;
        }

        /*
            -Retourne l'ensemble des modules enseignés par un enseignant
            -@param id_enseignant: L'identifiant de l'enseignant que l'on veut récupérer ses modules
            -@Throws PDOException
        */

        public static function modulesEnseignant($id_enseignant){
            $stmt = self::$db->prepare(self::$teacherModules);

            $stmt->bindValue(':id_enseignant', $id_enseignant);

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();      

            return $resultat;
        }
    }
