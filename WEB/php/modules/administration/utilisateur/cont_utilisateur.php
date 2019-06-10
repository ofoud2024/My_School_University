<?php
    require_once __DIR__ . "./../../../verify.php";
    require_once __DIR__ . "./../../../common/cont_generique.php";
    require_once __DIR__ . "/vue_utilisateur.php";
    require_once __DIR__ . "/modele_utilisateur.php";

    class ContUtilisateur extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueUtilisateur();
            $this->modele = new ModeleUtilisateur($this);
        }

        /****************************************************************************************************/
        /*******************************************UTILISATEURS*********************************************/
        /****************************************************************************************************/


        public function afficherListeUtilisateurs()
        {
            $liste_utilisateurs = $this->modele->getListeUtilisateurs();
            $this->vue->afficherListeUtilisateurs($liste_utilisateurs);
        }



        public function afficherCreationUtilisateur()
        {
            $token = $this->genererToken();
            $liste_droits = $this->modele->getListeDroits();
            $this->vue->afficherInsertionUtilisateurs($liste_droits, $token);
        }

        public function afficherModifierUtilisateur()
        {
            $token = $this->genererToken();

            if(isset($_GET['id'])){
                $id_utilisateur = htmlspecialchars($_GET['id']);
            }else{
                $this->pasAssezDeParametres("id utilisateur");
            }
            
            $liste_droits = $this->modele->getListeDroits();
            
            $utilisateur = $this->modele->getUtilisateur($id_utilisateur);

            $this->vue->afficherUtilisateur($utilisateur, $liste_droits, $token);
        }


        public function inscription()
        {
            $this->validerToken();

            $keyList = array('email','nom', 'prenom', 'tel', 'addresse', 'est_homme', 'date_naissance', 'mot_de_passe','droits',  'pays_naissance', 'code_postal');
            
            if (checkArrayForKeys($keyList, $_POST)) {
                $params = array_map(function($el){return htmlspecialchars($el);},$_POST);
                $this->modele->ajouterUtilisateur($params);
            } else {
                $this->pasAssezDeParametres("ensemble des paramètres de l'utilisateur ");
            }

            header('Location: index.php?module=administration&type=utilisateur&action=liste_utilisateurs');

        }


        public function modifierUtilisateur()
        {
            $this->validerToken();

            $required_key = array('email','nom', 'prenom', 'tel', 'addresse', 'est_homme', 'date_naissance','droits', 'pays_naissance', 'code_postal');

            if (checkArrayForKeys($required_key, $_POST) && isset($_GET['id'])) {
                $id = htmlspecialchars($_GET['id']);
                $data = array_map(function($el){
                    return htmlspecialchars($el);
                }, $_POST);

                if (isset($_POST['modifier'])) {
                    $this->modele->modifierUtilisateur($data, $id);
                } elseif (isset($_POST['supprimer'])) {
                    $this->modele->supprimerUtilisateur($id);
                } else {
                    $this->pasAssezDeParametres("action");
                }
            } else {
                $this->pasAssezDeParametres("ensemble des paramètres de l'utilisateur ");
            }

            header('Location: index.php?module=administration&type=utilisateur&action=liste_utilisateurs');


        }


        /****************************************************************************************************/
        /*******************************************PERSONNELS***********************************************/
        /****************************************************************************************************/


        public function afficherListePersonnels()
        {
            $token = $this->genererToken();
            $liste_personnel = $this->modele->getListePersonnels();
            $this->vue->afficherListePersonnels($liste_personnel, $token);
        }

        public function afficherModifierPersonnel()
        {
            $token = $this->genererToken();

            $id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres("identifiant du personnel");

            $personnel = $this->modele->getPersonnel($id);

            $this->vue->modifierPersonnel($personnel, $token);
        }

        public function ajouterPersonnel()
        {
            $this->validerToken();

            $pseudo = isset($_POST['pseudo']) ? htmlspecialchars($_POST['pseudo']) : $this->pasAssezDeParametres("pseudo utilisateur");

            $estEnseignant = isset($_POST['estEnseignant']) && $_POST['estEnseignant'] === 'on' ? true : false;

            $this->modele->ajouterPersonnel($pseudo, $estEnseignant);

            header('Location: index.php?module=administration&type=personnel&action=liste_personnels');
        }




        public function modifierPersonnel()
        {
            $this->validerToken();

            $id_personnel = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres("identifiant du personnel");
            $est_enseignant = isset($_POST['estEnseignant']) ? htmlspecialchars($_POST['estEnseignant']) === 'on' : false;
            $heures_travail = isset($_POST['heures_travail']) ? htmlspecialchars($_POST['heures_travail']) : $this->pasAssezDeParametres("heures travaillées");

            if (isset($_POST['modification'])) {
                $this->modele->modifierPersonnel($id_personnel, $est_enseignant, $heures_travail);
                header('Location: index.php?module=administration&type=personnel&action=afficher_modification_personnel&id='.$id_personnel);
            } elseif (isset($_POST['suppression'])) {
                $this->modele->supprimerPersonnel($id_personnel);
                header('Location: index.php?module=administration&type=personnel&action=liste_personnels');
            }else{
                ErrorHandler::afficherErreur(
                    new ParametresInsuffisantsException(),
                    INVALID_ACTION_ERROR_TITLE,
                    INVALID_ACTION_ERROR_MESSAGE
                );
            }
        }
    }
