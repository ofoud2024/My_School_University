<?php
    require_once __DIR__ . "./../../../verify.php";
    require_once __DIR__ . "./../../../common/cont_generique.php";
    require_once __DIR__ . "/vue_droits.php";
    require_once __DIR__ . "/modele_droits.php";

    class ContDroits extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueDroits();
            $this->modele = new ModeleDroits($this);
        }

        public function afficherListeDroits()
        {
            $liste_droits = $this->modele->getListeDroits();
            $this->vue->afficherDetailsListeDroits($liste_droits);
        }

        public function afficher_ajouter_droits()
        {
            $token = $this->genererToken();
            $this->vue->ajouterDroits($token);
        }


        public function ajouterDroits()
        {
            $this->validerToken();

            $nom_droits = isset($_POST['nom_droits']) ? $_POST['nom_droits'] : $this->pasAssezDeParametres("nom des droits");
            $creation_utilisateurs = isset($_POST['creation_utilisateurs']) ? 1 : 0;
            $creation_modules = isset($_POST['creation_modules']) ? 1 : 0;
            $creation_cours = isset($_POST['creation_cours']) ? 1 : 0;
            $creation_groupes = isset($_POST['creation_groupes']) ? 1 : 0;
            $modification_abscences = isset($_POST['modification_abscences']) ? 1 : 0;
            $modification_heures_travail = isset($_POST['modification_heures_travail']) ? 1 : 0;
            $modifications_droits = isset($_POST['modifications_droits']) ? 1 : 0;
            $statistiques = isset($_POST['statistiques']) ? 1 : 0;

            $this->modele->ajouterDroits(
                            $nom_droits,
                            $creation_utilisateurs,
                            $creation_modules,
                            $creation_cours,
                            $creation_groupes,
                            $modification_abscences,
                            $modifications_droits,
                            $modification_heures_travail,
                            $statistiques
                         );
            
            header('Location: index.php?module=administration&type=droits&action=liste_droits');
        }



        public function afficherModification()
        {
            $token = $this->genererToken();
            $nom_droit = isset($_GET['nom_droits']) ? $_GET['nom_droits'] : $this->pasAssezDeParametres("nom des droits");
            $droit = $this->modele->getDroit($nom_droit);
            $liste_groupe = $droit->getListeGroupes();
            $liste_utilisateurs = $droit->getListeUtilisateurs();
            $this->vue->modifierDroits($droit->getData(), $liste_utilisateurs, $liste_groupe, $token);
        }


        public function modifierDroit()
        {
            $this->validerToken();

            $nom_droits = isset($_GET['nom_droits']) ? $_GET['nom_droits'] : $this->pasAssezDeParametres("nom des droits");
            
            $suppression = isset($_POST['supprimer']) ? true : false;

            $modification = isset($_POST['modifier']) ? true : false;

            if ($modification) {
                $creation_utilisateurs = isset($_POST['creation_utilisateurs']) ? 1 : 0;
                $creation_modules = isset($_POST['creation_modules']) ? 1 : 0;
                $creation_cours = isset($_POST['creation_cours']) ? 1 : 0;
                $creation_groupes = isset($_POST['creation_groupes']) ? 1 : 0;
                $modification_abscences = isset($_POST['modification_abscences']) ? 1 : 0;
                $modification_heures_travail = isset($_POST['modification_heures_travail']) ? 1 : 0;
                $modifications_droits = isset($_POST['modifications_droits']) ? 1 : 0;
                $statistiques = isset($_POST['statistiques']) ? 1 : 0;
                
                $this->modele->modifierDroits(
                    $nom_droits,
                    $creation_utilisateurs,
                    $creation_modules,
                    $creation_cours,
                    $creation_groupes,
                    $modification_abscences,
                    $modifications_droits,
                    $modification_heures_travail,
                    $statistiques
                 );
                header('Location: index.php?module=administration&type=droits&action=liste_droits');
            } elseif ($suppression) {
                $this->modele->supprimerDroits($nom_droits);
            } else {
                ErrorHandler::afficherErreur(
                    new ParametresInsuffisantsException(),
                    INVALID_ACTION_ERROR_TITLE,
                    INVALID_ACTION_ERROR_MESSAGE
                );
            }

            header('Location: index.php?module=administration&type=droits&action=liste_droits');
        }
    }
