<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/cont_generique.php";
    require_once __DIR__ . "/vue_semestre.php";
    require_once __DIR__ . "/modele_semestre.php";

    class ContSemestre extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueSemestre();
            $this->modele = new ModeleSemestre($this);
        }

        public function afficher_semestres()
        {
            $token = $this->genererToken();

            $liste_semestres = $this->modele->liste_semestres();
            
            $this->vue->afficher_semestres($liste_semestres, $token);
        }

        public function afficher_semestre()
        {
            $token = $this->genererToken();

            $id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('référence du semestre');

            $semestre = $this->modele->getSemestre($id);

            $annee = $this->modele->annee_courante();

            $this->vue->afficher_semestre($semestre->detailsSemestre(), $semestre->anneesSemestre(), $semestre->etudiantsSemestre(), $annee, $token);
        }


        public function ajouter_semestre()
        {
            $this->validerToken();

            $ref = isset($_POST['reference']) ? htmlspecialchars($_POST['reference']) : $this->pasAssezDeParametres('référence semestre');
            $nom = isset($_POST['nom_semestre']) ? htmlspecialchars($_POST['nom_semestre']) : $this->pasAssezDeParametres('nom du semestre');
            $pts_ets = isset($_POST['points_ets']) ? htmlspecialchars($_POST['points_ets']) : $this->pasAssezDeParametres('points ets');
            $periode = isset($_POST['periode']) ? htmlspecialchars($_POST['periode']) : $this->pasAssezDeParametres('période semestre');

            $this->modele->ajouter_semestre($ref, $nom, $pts_ets, $periode);

            header('Location: index.php?module=administration&type=semestre&action=liste_semestre');
        }



        public function modifier_semestre()
        {
            $this->validerToken();

            $ref = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('référence semestre');
            $nom = isset($_POST['nom_semestre']) ? htmlspecialchars($_POST['nom_semestre']) : $this->pasAssezDeParametres('nom de semestre');
            $pts_ets = isset($_POST['points_ets']) ? htmlspecialchars($_POST['points_ets']) : $this->pasAssezDeParametres('points ets du semestre');

            $this->modele->modifier_semestre($ref, $nom, $pts_ets);

            header('Location: index.php?module=administration&type=semestre&action=afficher_semestre&id='.$ref);
        }

        public function retirer_etudiant()
        {
            $this->validerToken();
            
            $ref = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('référence semestre');

            $num_etudiant = isset($_GET['etudiant']) ? htmlspecialchars($_GET['etudiant']) : $this->pasAssezDeParametres('numéro de l\'étudiant à retirer');
        
            $this->modele->retirer_etudiant($ref, $num_etudiant);

            header('Location: index.php?module=administration&type=semestre&action=afficher_semestre&id='.$ref);
        }

        public function supprimer_semestre()
        {
            $this->validerToken();
            $ref = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres('référence semestre');

            $this->modele->supprimer_semestre($ref);

            header('Location: index.php?module=administration&type=semestre&action=liste_semestre');
        }
    }
