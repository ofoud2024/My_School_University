<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/cont_generique.php";
    require_once __DIR__ . "/vue_moodle.php";
    require_once __DIR__ . "/modele_moodle.php";
    
    class ContMoodle extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueMoodle();
            $this->modele = new ModeleMoodle($this);
        }



        /*
                Enseignant
        */

        public function afficher_depot_cours(){
            $token = $this->genererToken();
            $liste_supports = $this->modele->liste_supports();
            $this->vue->afficher_depot_cours($liste_supports, $token);    
            
        }

        public function afficher_ouvrir_depot(){
            $token = $this->genererToken();
            $depots_ouverts = $this->modele->liste_depots();
            $this->vue->afficher_ouvrir_depot($depots_ouverts, $token);
        }

        public function afficher_acces_depot(){
            $token = $this->genererToken();
            $this->vue->afficher_acces_depot($token);
        }

        public function effectuer_depot_cours(){
            $this->validerToken();

            $nom_depot = isset($_POST['titre_cours']) ? htmlspecialchars($_POST['titre_cours']) : $this->pasAssezDeParametres("titre du cours");
            $module_depot = isset($_POST['module_cours']) ? htmlspecialchars($_POST['module_cours']) : $this->pasAssezDeParametres('module de cours');
            $date_ouverture = isset($_POST['date_ouverture_depot']) ? htmlspecialchars($_POST['date_ouverture_depot']) : $this->pasAssezDeParametres("date d'ouverture du dépôt");
            $est_cachee = isset($_POST['cours_est_visible']) ? 'false' : 'true';

            if($module_depot === ""){
                $this->pasAssezDeParametres("module");
            }

            try{
                $fichier = new FileUpload("support_cours");
            }catch(FichierInexistant $e){
                $this->pasAssezDeParametres('fichier du support de cours');
            }

            $this->modele->ajouter_support_cours($nom_depot, $module_depot, $date_ouverture, $fichier, $est_cachee);
        
            header('Location: index.php?module=moodle&action=depot_cours');
        }

        public function effectuer_ouvrir_depot(){
            $this->validerToken();

            $nom_depot      = isset($_POST['nom_depot']) ? htmlspecialchars($_POST['nom_depot']) : $this->pasAssezDeParametres("nom du dépôt");
            $module_depot   = isset($_POST['module_depot']) ? htmlspecialchars($_POST['module_depot']) : $this->pasAssezDeParametres("Module du dépôt");
            $groupe_depot   = isset($_POST['groupe_depot']) ? htmlspecialchars($_POST['groupe_depot']) : $this->pasAssezDeParametres("Groupe du dépôt");
            $date_ouverture = isset($_POST['date_ouverture_depot']) ? htmlspecialchars($_POST['date_ouverture_depot']) : $this->pasAssezDeParametres("date ouverture du dépôt");
            $date_debut     = isset($_POST['date_debut_depot']) ? htmlspecialchars($_POST['date_debut_depot']) : $this->pasAssezDeParametres("date début des dépôts");
            $date_fin       = isset($_POST['date_fermeture_depot']) ? htmlspecialchars($_POST['date_fermeture_depot']) : $this->pasAssezDeParametres("date de fermeture du dépôt");
            $coefficient    = isset($_POST['coefficient_depot']) ? htmlspecialchars($_POST['coefficient_depot']) : 0;
            
            if($module_depot === ""){
                $this->pasAssezDeParametres("module");
            }
            
            try{
                $fichier = new FileUpload("support_depot");
            }catch(FichierInexistant $e){
                $this->pasAssezDeParametres("Support du dépôt");
            }

            $this->modele->ouvrir_depot($nom_depot, $module_depot, $groupe_depot,$date_debut, $date_fin,  $date_ouverture, $coefficient, $fichier);
        
            header('Location: index.php?module=moodle&action=ouvrir_depot');
        }

        public function supprimer_depot(){
            $this->validerToken();
            $id_depot = isset($_GET['id_depot']) ? htmlspecialchars($_GET['id_depot']) : $this->pasAssezDeParametres($_GET['id_depot']);
            $this->modele->supprimer_depot($id_depot);
            header('Location: index.php?module=moodle&action=acces_depot');
        }



        public function afficher_ajouter_controle(){
            $token = $this->genererToken();
            
            $liste_controles = $this->modele->liste_controles();

            $this->vue->ajouterControle($liste_controles, $token);
        }



        public function charger_notes(){
            $this->validerToken();

            $nom_controle = isset($_POST['nom_controle']) ? htmlspecialchars($_POST['nom_controle']) : $this->pasAssezDeParametres('nom du contrôle');
            $date_controle = isset($_POST['date_controle']) ? htmlspecialchars($_POST['date_controle']) : $this->pasAssezDeParametres('date de contrôle');
            $module_controle = isset($_POST['module_controle']) ? htmlspecialchars($_POST['module_controle']) : $this->pasAssezDeParametres('module du contrôle');
            $coefficient = isset($_POST['coefficient_controle']) ? htmlspecialchars($_POST['coefficient_controle']) : $this->pasAssezDeParametres("coefficient du contrôle");
            $separateur =  isset($_POST['separateur_fichier']) ? htmlspecialchars($_POST['separateur_fichier']) : $this->pasAssezDeParametres("séparateur du fichier");
            $col_pseudo = isset($_POST['col_pseudo']) ? htmlspecialchars($_POST['col_pseudo']) : $this->pasAssezDeParametres('colonne du pseudo');
            $col_note = isset($_POST['col_note']) ? htmlspecialchars($_POST['col_note']) : $this->pasAssezDeParametres('colonne de la note');
            $col_commentaire = isset($_POST['col_commentaire']) ? htmlspecialchars($_POST['col_commentaire']) : $this->pasAssezDeParametres('colonne de commentaire');

            if($module_controle === ""){
                $this->pasAssezDeParametres("module");
            }

            try{
                $fichier_note = new FileUpload("fichier_notes");
            }catch(FichierInexistant $e){
                $this->pasAssezDeParametres('Fichier de notes');
            }

            $this->modele->chargerNotes($nom_controle, $date_controle, $module_controle, $coefficient, $fichier_note, $separateur, $col_pseudo - 1, $col_note - 1 , $col_commentaire - 1);

            header('Location: index.php?module=moodle&action=ajouter_controle');
        }


        public function details_notes_etudiant(){
            $id_controle = isset($_GET['id_controle']) ? htmlspecialchars($_GET['id_controle']) : $this->pasAssezDeParametres('identifiant du contrôle');
            $token = $this->genererToken();
            $details_controle = array();
            $notes_etudiants = $this->modele->details_notes_etudiant($id_controle, $details_controle);
            $this->vue->details_notes_etudiant($details_controle, $notes_etudiants, $token);
        }

        public function changer_notes_etudiant(){
            $this->validerToken(); 
            $id_controle = isset($_GET['id_controle']) ? htmlspecialchars($_GET['id_controle']) : $this->pasAssezDeParametres('identifiant du contrôle');

            if(isset($_POST['modifier'])){
                $notes = isset($_POST['notes']) && is_array($_POST['notes']) ? $_POST['notes'] : $this->pasAssezDeParametres('les notes'); 
    
                $liste_notes = array();
    
                foreach($notes as $pseudo=>$note){
                    if(isset($note['note']) && isset($note['commentaire'])){
                        array_push($liste_notes, array(
                            "pseudo"=>htmlspecialchars($pseudo),
                            "note"=>htmlspecialchars($note['note']),
                            "commentaire"=>htmlspecialchars($note['commentaire'])
                        ));
                    }else{
                        $this->pasAssezDeParametres("note étudiants");
                    }
                }
    
    
                $this->modele->modifier_notes($id_controle, $liste_notes);

                header('Location: index.php?module=moodle&action=details_notes_etudiants&id_controle='.$id_controle);

            }else if(isset($_POST['supprimer'])){
                $this->modele->supprimer_controle($id_controle);
                header('Location: index.php?module=moodle&action=ajouter_controle');
            }else{
                $this->pasAssezDeParametres("l'action à effectuer (supprimer où modifier)");
            }


            
            
        }


        public function changer_etat_support(){
            $this->validerToken();
            $id_support = isset($_GET['id_support']) ? htmlspecialchars($_GET['id_support']) : $this->pasAssezDeParametres('identifiant du support');
            $this->modele->changer_etat_support($id_support);
            header('Location: index.php?module=moodle&action=depot_cours');
        }


        public function supprimer_support(){
            $this->validerToken();
            $id_support = isset($_GET['id_support']) ? htmlspecialchars($_GET['id_support']) : $this->pasAssezDeParametres('identifiant du support');
            $this->modele->supprimer_support($id_support);
            header('Location: index.php?module=moodle&action=depot_cours');
        }

        /*
            Etudiant
        */

        public function afficherListeModules(){
            $liste_semestres = $this->modele->listeSemestresAvecModules();
            $this->vue->afficherListeModules($liste_semestres);
        }

        public function afficherListeSupports(){
            $ref_module = isset($_GET['ref_module']) ? htmlspecialchars($_GET['ref_module']) : $this->pasAssezDeParametres("référence du module"); 
            
            $liste_cours = array();
            
            $liste_depots = $this->modele->liste_supports_module($ref_module, $liste_cours);
            
            $this->vue->afficherListeSupports($liste_cours, $liste_depots);
        }

        public function detailsDepotEtudiant(){
            $token = $this->genererToken();

            $id_depot = isset($_GET['id_depot']) ? htmlspecialchars($_GET['id_depot']) : $this->pasAssezDeParametres('identifiant du dépôt');

            $details_depot = $this->modele->detailsDepot($id_depot);
            
            $this->vue->detailsDepotEtudiant($details_depot, $token);
        }


        public function deposer_exercice(){
            $this->validerToken();

            try{
                $fichier = new FileUpload("fichier_depot_etudiant");
            }catch(FichierInexistant $e){
                $this->pasAssezDeParametres('fichier à déposer');
            }

            $id_depot = isset($_GET['id_depot']) ? htmlspecialchars($_GET['id_depot']) : $this->pasAssezDeParametres('identifiant du dépôt');
            
            $commentaire = isset($_POST['commentaire']) ? htmlspecialchars($_POST['commentaire']) : $this->pasAssezDeParametres("commentaire dépôt");

            $this->modele->deposer_exercice($id_depot, $fichier, $commentaire);

            header('Location: index.php?module=moodle&action=details_depot&id_depot='.$id_depot);
        }


        public function notes_etudiant(){
            $moyenne_modules = $this->modele->moyenne_etudiant();
            $this->vue->notes_etudiant($moyenne_modules);
        }

    }
    