<?php
    require_once __DIR__ . "/utilisateur/mod_utilisateur.php";
    require_once __DIR__ . "/droits/mod_droits.php";
    require_once __DIR__ . "/groupe/mod_groupe.php";
    require_once __DIR__ . "/semestre/mod_semestre.php";
    require_once __DIR__ . "/module/mod_module.php";
    require_once __DIR__ . "/etudiant/mod_etudiant.php";
    require_once __DIR__ . "/salle/mod_salle.php";

    require_once __DIR__ . "/vue_administration.php";
    class ModAdministration
    {
        public function __construct()
        {
            $type = isset($_GET['type']) ? htmlspecialchars($_GET['type']) : null;

            $vue = new VueAdministration($type);

            switch ($type) {
                case 'utilisateur': case 'personnel':
                    $mod = new ModUtilisateur();
                break;

                case 'droits':
                    $mod = new ModDroits();
                break;

                case 'groupe':
                    $mod = new ModGroupe();
                break;

                case 'semestre':
                    $mod = new ModSemestre();
                break;
               
                case 'etudiant':
                    $mod = new ModEtudiant();
                break;

                case 'module':
                    $mod = new ModModule();
                break;
                
                case 'salle':
                	$mod = new ModSalle();
                break;

                default:
                    $this->redirectToGrantedType();
                    header("Location: index.php?module=error&title=Action invalide&message=".INVALID_ACTION_ERROR_MESSAGE);
                    exit(0);
            }

            require_once __DIR__ . "/html/administration-2.html";
        }

        public function redirectToGrantedType(){
            $this->redirectIfGranted("droit_creation_utilisateurs", "utilisateur");
            $this->redirectIfGranted("droit_modification_droits", "droits");
            $this->redirectIfGranted("droit_creation_groupes", "groupe");
            $this->redirectIfGranted("droits_creation_modules", "module");
            $this->redirectIfGranted("droit_creation_cours", "salle");
            $this->redirectIfGranted("droit_modification_absences", "absences");
        }
        

        public function redirectIfGranted($role_name, $type){
            try{
                Utilisateur::possedeDroit($role_name);
                header('Location: index.php?module=administration&type='.$type);
                exit(0);
            }catch(NonAutoriseException $e) {
                return ;
            }
        }
    }
