<?php
    require_once __DIR__ . "./../../verify.php";
    require_once __DIR__ . "/../../common/Database.php";
    require_once __DIR__ . "/../../common/classes/moodle.php";
    require_once __DIR__ . "/../../common/classes/semestre.php";
    require_once __DIR__ . "/../../common/classes/moodleEtudiant.php";
    require_once __DIR__ . "./../../common/classes/controle_ecrit.php";

    class ModeleMoodle extends Database
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        public function ajouter_support_cours($nom_support, $module_depot, $date_ouverture, $fichier, $est_cachee){
            
            if($fichier->copyFile()){
                try{
                    Moodle::ajouter_support($nom_support, $fichier->getFullPath(), $date_ouverture, $est_cachee, $module_depot);
                }catch(PDOException $e){
                    ErrorHandler::afficherErreur($e, ADD_LESSON_ERROR_TITLE, ADD_LESSON_ERROR_MESSAGE, array("support"=>$nom_support));
                }catch(PasEnseignantException $e){
                    ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
                }
            }else{
                ErrorHandler::afficherErreur(new Exception(FILE_COPY_ERROR));
            }
        }


        public function ouvrir_depot($nom_depot,$module,$groupe_depot, $date_debut, $date_fermeture, $date_ouverture, $coefficient, $fichier){
            
            if($fichier->copyFile()){
                try{
                    Moodle::ouvrir_depot($nom_depot,$module ,$groupe_depot, $fichier->getFullPath(), $date_debut, $date_fermeture, $date_ouverture, $coefficient);
                }catch(PDOException $e){
                    ErrorHandler::afficherErreur($e, OPEN_DEPOSIT_ERROR_TITLE, OPEN_DEPOSIT_ERROR_MESSAGE, array("nom_depot"=>$nom_depot));
                }catch(PasEnseignantException $e){
                    ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
                }
            }else{
                ErrorHandler::afficherErreur(new Exception(FILE_COPY_ERROR));
            }
        }


        public function liste_supports(){
            try{
                return Moodle::liste_supports();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, DATABASE_ERROR_TITLE, DATABASE_ERROR_MESSAGE);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function liste_depots(){
            try{
                return Moodle::liste_depots();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, DATABASE_ERROR_TITLE, DATABASE_ERROR_MESSAGE);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function listeSemestresAvecModules(){
            try{
                $liste_resultat = array();
                $liste_semestres = Semestre::liste_semestres();

                foreach($liste_semestres as $semestre){
                    $instance_semestre = new Semestre($semestre['ref_semestre']);
                    $liste_resultat[$semestre['ref_semestre']] = array(
                        "nom_semestre" => $semestre['nom_semestre'],
                        "modules" => $instance_semestre->modulesSemestre()
                    ); 
                }

                return $liste_resultat;
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, DATABASE_ERROR_TITLE, DATABASE_ERROR_MESSAGE);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur($e, DEFAULT_ERROR_TITLE, DEFAULT_ERROR_MESSAGE);
            }
        }


        public function liste_controles(){
            try{
                return ControleEcrit::listeControles();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function changer_etat_support($id_support){
            try{
                Moodle::changerEtatSupport($id_support);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, UPDATE_ERROR_TITLE, UPDATE_ERROR_MESSAGE, array("type"=>"le support", "id"=>$id_support));
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }
        
        public function supprimer_support($id_support){
            try{
                Moodle::supprimerSupport($id_support);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, DELETE_ERROR_TITLE, DELETE_ERROR_MESSAGE . ". {{newLine}} Attention: Vous ne pouvez pas supprimer des dépôts à partir de cette page", array("type"=>"le support", "id"=>$id_support));
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }
        }

        public function supprimer_depot($id_depot){
            try{
                $depot = new Moodle($id_depot);
                $depot->supprimerDepot();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e, DELETE_ERROR_TITLE, DELETE_ERROR_MESSAGE , array("type"=>"le dépôt", "id"=>$id_depot));
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le dépôt', 'id'=>$id_depot)
                );
            } 

        }

        public function chargerNotes($nom_controle, $date_controle, $module_controle, $coefficient, $fichier_note,$separateur, $col_pseudo, $col_note, $col_commentaire){
            if($fichier_note->checkMimes(FileUpload::$CSV_MIMES) && $fichier_note->copyFile()){
                $donnees = $fichier_note->fichierEnTableau($separateur);
                $notes = array();
                
                array_shift($donnees);
                
                foreach($donnees as $ligne){
                    if(isset($ligne[$col_pseudo]) && isset($ligne[$col_note]) && isset($ligne[$col_commentaire])){
                        array_push($notes,array(
                            "pseudo"=>$ligne[$col_pseudo],
                            "note"=>$ligne[$col_note],
                            "commentaire"=>$ligne[$col_commentaire]
                        ));
                    }else{
                        
                        ErrorHandler::afficherErreur(
                            $e, 
                            INVALID_MARKS_FILE_TITLE, 
                            INVALID_MARKS_FILE_MESSAGE, 
                            array("name"=>$fichier_note->getClientFileName())
                        );
                    }
                }

                try{
                    Database::getDB()->beginTransaction();

                    $id_controle = ControleEcrit::creerControle($nom_controle, $date_controle, $module_controle, $coefficient);
                    
                    $controle = new ControleEcrit($id_controle);

                    $controle->ajouterNotes($notes);
    
                    Database::getDB()->commit();    
                }catch(PDOException $e){
                    ErrorHandler::afficherErreur(
                        $e,
                        MARKS_UPDATE_ERROR_TITLE,
                        Database::getPDOHint($e)
                    );
                }catch(PasEnseignantException $e){
                    ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
                }
            }else{
                ErrorHandler::afficherErreur(new Exception(FILE_COPY_ERROR), INVALID_MARKS_FILE_TITLE, INVALID_MARKS_FILE_MESSAGE, array(
                    "name"=>$fichier_note->getClientFileName()
                ));
            }
        }

        public function modifier_notes($id_controle, $liste_notes){
            try{
                $controle = new ControleEcrit($id_controle);
                $controle->ajouterNotes($liste_notes);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le controle', 'id'=>$id_controle)
                );
            } 
        }
        

        public function supprimer_controle($id_controle){
            try{
                $controle = new ControleEcrit($id_controle);
                $controle->supprimerControle();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le controle', 'id'=>$id_controle)
                );
            }   
        }


        public function details_notes_etudiant($id_controle, &$details_controle){
            try{
                $controle = new ControleEcrit($id_controle);
                $details_controle = $controle->detailsControle();
                return $controle->getListeNotes();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(PasEnseignantException $e){
                ErrorHandler::afficherErreur($e, NOT_TEACHER_ERROR_TITLE, NOT_TEACHER_ERROR_MESSAGE);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le controle', 'id'=>$id_controle)
                );
            }
            
        }


        /*
            PARTIE ETUDIANT
        */

        public function liste_supports_module($ref_module, &$liste_cours = array()){
            try{
                $liste_cours = MoodleEtudiant::cours_module($ref_module);
                return MoodleEtudiant::liste_depots_ouvert($ref_module);
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }
        }


        public function detailsDepot($id_depot){
            try{
                $depot = new MoodleEtudiant($id_depot);
                return $depot->getDetailsDepot();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le dépôt', 'id'=>$id_depot)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à ce dépôt")
                );
            }
        }

        public function deposer_exercice($id_depot, $fichier, $commentaire){
            try{
                if($fichier->copyFile()){
                    $depot = new MoodleEtudiant($id_depot);
                    $chemin_complet = $fichier->getFullPath();
                    $depot->deposer_exercice($fichier->getClientFileName(),$chemin_complet, $commentaire);
                }else{
                    ErrorHandler::afficherErreur(
                        new Exception('Chargement de fichier étudiant échouée'),
                        STUDENT_FILE_UPLOAD_FAILED_TITLE, 
                        STUDENT_FILE_UPLOAD_FAILED_MESSAGE
                    );      
                }

            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    STUDENT_FILE_UPLOAD_FAILED_TITLE, 
                    STUDENT_FILE_UPLOAD_FAILED_MESSAGE
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le dépôt', 'id'=>$id_depot)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"déposer votre fichier")
                );
            }

        }

        public function moyenne_etudiant(){
            try{
                return MoodleEtudiant::moyenne_etudiant();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e
                ); 
            }catch(PasEtudiantException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_STUDENT_ERROR_TITLE,
                    NOT_STUDENT_ERROR_MESSAGE
                );
            }
        }


    }