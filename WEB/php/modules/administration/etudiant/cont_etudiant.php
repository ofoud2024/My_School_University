<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/vue_etudiant.php";
    require_once __DIR__ . "/modele_etudiant.php";
    require_once __DIR__ . "/../../../common/cont_generique.php";

    class ContEtudiant extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueEtudiant();
            $this->modele = new ModeleEtudiant($this);
        }

        public function afficher_etudiants()
        {   
            $token = $this->genererToken();

            $liste_etudiants = $this->modele->liste_etudiants();
            
            $this->vue->afficher_etudiants($liste_etudiants, $token);
        }

        public function ajouter_etudiant()
        {
            $this->validerToken();

            $num = isset($_POST['num_etudiant']) ? htmlspecialchars($_POST['num_etudiant']) : $this->pasAssezDeParametres("numéro de l'etudiant");
            $id_utilisateur = isset($_POST['pseudo_etudiant']) ? htmlspecialchars($_POST['pseudo_etudiant']) : $this->pasAssezDeParametres('pseudo étudiant');

            $this->modele->ajouter_etudiant($num, $id_utilisateur);

            header('Location: index.php?module=administration&type=etudiant&action=liste_etudiant');
        }

        public function afficher_etudiant()
        {
            $token = $this->genererToken();

            $num = isset($_GET['num']) ? htmlspecialchars($_GET['num']) : $this->pasAssezDeParametres('numéro étudiant');

            $liste_semestre = $this->modele->listeSemestres(); 

            $etudiant = $this->modele->getEtudiant($num);

            $periode_courante = $this->modele->getPeriodeCourante();

            $this->vue->afficher_etudiant($etudiant->detailsEtudiant(), $num, $liste_semestre, $periode_courante, $token);
        }

        public function supprimer_etudiant()
        {
            $this->validerToken();

            $num = isset($_GET['num']) ? htmlspecialchars($_GET['num']) : $this->pasAssezDeParametres('numéro étudiant');

            $this->modele->supprimer_etudiant($num);

            header('Location: index.php?module=administration&type=etudiant&action=liste_etudiant');
        }

        public function modifier_semestre_etudiant(){
            $this->validerToken();

            $num = isset($_GET['num_etudiant']) ? htmlspecialchars($_GET['num_etudiant']) : $this->pasAssezDeParametres('numéro étudiant');
            
            $semestre = isset($_POST['ref_semestre_etudiant']) ? htmlspecialchars($_POST['ref_semestre_etudiant']) : $this->pasAssezDeParametres('référence du semestre actuel');

            $this->modele->modifier_semestre_etudiant($num, $semestre);

            header('Location: index.php?module=administration&type=etudiant&action=afficher_etudiant&num='.$num);
        }

        public function modifier_moyenne_semestre(){
            $this->validerToken();
            
            $num = isset($_GET['num_etudiant']) ? htmlspecialchars($_GET['num_etudiant']) : $this->pasAssezDeParametres('numéro étudiant');

            $est_valide = isset($_POST['semestre_est_valide']) && $_POST['semestre_est_valide'] === 'on' ? 'true' : 'false' ;

            $moyenne = isset($_POST['moyenne_semestre']) ? htmlspecialchars($_POST['moyenne_semestre']) : $this->pasAssezDeParametres("moyenne de l'étudiant");

            $this->modele->modifier_moyenne_etudiant($num, $moyenne, $est_valide);

            header('Location: index.php?module=administration&type=etudiant&action=afficher_etudiant&num='.$num);

        }


    }
