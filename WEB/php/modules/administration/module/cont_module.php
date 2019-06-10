<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/cont_generique.php";
    require_once __DIR__ . "/vue_module.php";
    require_once __DIR__ . "/modele_module.php";

    class ContModule extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueModule();
            $this->modele = new ModeleModule($this);
        }


        public function liste_modules(){
            $token = $this->genererToken();
            $modules = $this->modele->liste_modules();
            $this->vue->afficher_modules($modules, $token);
        }


        public function afficher_module(){
            $token = $this->genererToken();
            $id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('référence du module');
            $module = $this->modele->getModule($id);
            $this->vue->afficher_module($module->getDetailsModule(), $module->getEnseignantsModule(), $token);
        }

        public function ajouter_module(){
            $token = $this->validerToken();

            $ref = isset($_POST['reference_module']) ? htmlspecialchars($_POST['reference_module']) : $this->pasAssezDeParametres('réference module');
            $nom = isset($_POST['nom_module']) ? htmlspecialchars($_POST['nom_module']) : $this->pasAssezDeParametres('nom module');
            $coef = isset($_POST['coefficient_module']) ? htmlspecialchars($_POST['coefficient_module']) : $this->pasAssezDeParametres('coefficient module');
            $heures_cm = isset($_POST['heures_cm']) ? htmlspecialchars($_POST['heures_cm']) : $this->pasAssezDeParametres('heures cm');
            $heures_td = isset($_POST['heures_td']) ? htmlspecialchars($_POST['heures_td']) : $this->pasAssezDeParametres('heures td');
            $heures_tp = isset($_POST['heures_tp']) ? htmlspecialchars($_POST['heures_tp']) : $this->pasAssezDeParametres('heures tp');
            $couleur = isset($_POST['couleur']) ? htmlspecialchars($_POST['couleur']) : $this->pasAssezDeParametres('couleur module');
            $semestre = isset($_POST['semestre']) ? htmlspecialchars($_POST['semestre']) : $this->pasAssezDeParametres('semestre module');
            $abreviation = isset($_POST['abreviation_module']) ? htmlspecialchars($_POST['abreviation_module']) : $this->pasAssezDeParametres('abréviation module');

            $this->modele->ajouter_module($ref, $nom, $coef, $heures_cm, $heures_td, $heures_tp, $couleur, $semestre, $abreviation, $token);

            header('Location: index.php?module=administration&type=module&action=liste_modules');
        }



        public function modifier_module(){
            $token = $this->validerToken();

            $ref = isset($_GET['ref_module']) ? htmlspecialchars($_GET['ref_module']) : $this->pasAssezDeParametres('réference module');
            $nom = isset($_POST['nom_module']) ? htmlspecialchars($_POST['nom_module']) : $this->pasAssezDeParametres('nom module');
            $coef = isset($_POST['coefficient_module']) ? htmlspecialchars($_POST['coefficient_module']) : $this->pasAssezDeParametres('coefficient module');
            $heures_cm = isset($_POST['heures_cm']) ? htmlspecialchars($_POST['heures_cm']) : $this->pasAssezDeParametres('heures cm');
            $heures_td = isset($_POST['heures_td']) ? htmlspecialchars($_POST['heures_td']) : $this->pasAssezDeParametres('heures td');
            $heures_tp = isset($_POST['heures_tp']) ? htmlspecialchars($_POST['heures_tp']) : $this->pasAssezDeParametres('heures tp');
            $couleur = isset($_POST['couleur']) ? htmlspecialchars($_POST['couleur']) : $this->pasAssezDeParametres('couleur module');
            $abreviation = isset($_POST['abreviation_module']) ? htmlspecialchars($_POST['abreviation_module']) : $this->pasAssezDeParametres('abréviation');

            $this->modele->modifier_module($ref, $nom, $coef, $heures_cm, $heures_td, $heures_tp, $couleur, $abreviation);

            header('Location: index.php?module=administration&type=module&action=liste_modules');
        }

        public function retirer_enseignant(){
            $this->validerToken();
            
            $ref_module = isset($_GET['ref_module']) ? htmlspecialchars($_GET['ref_module']) : $this->pasAssezDeParametres('référence module');
            $id_enseignant = isset($_GET['id_enseignant']) ? htmlspecialchars($_GET['id_enseignant']) : $this->pasAssezDeParametres('identifiant de l\'enseignant');

            $this->modele->retirer_enseignant($ref_module, $id_enseignant);

            header('Location: index.php?module=administration&type=module&action=afficher_module&id='.$ref_module);
        }


        public function ajouter_enseignant(){
            $this->validerToken();

            $ref_module = isset($_GET['ref_module']) ? htmlspecialchars($_GET['ref_module']) : $this->pasAssezDeParametres('référence module');
            $id_enseignant = isset($_POST['id_enseignant']) ? htmlspecialchars($_POST['id_enseignant']) : $this->pasAssezDeParametres('identifiant de l\'enseignant');
            $est_responsable = isset($_POST['estResponsable']) && $_POST['estResponsable'] === 'on' ? 1 : 0;

            $this->modele->ajouter_enseignant($ref_module, $id_enseignant, $est_responsable);

            header('Location: index.php?module=administration&type=module&action=afficher_module&id='.$ref_module);
        }

        public function supprimer_module(){
            $this->validerToken(); 

            $ref_module = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('id module');
            
            $this->modele->supprimer_module($ref_module);
            
            header('Location: index.php?module=administration&type=module&action=liste_modules');
        }
    }