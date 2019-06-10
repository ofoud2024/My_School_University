<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/cont_generique.php";
    require_once __DIR__ . "/vue_groupe.php";
    require_once __DIR__ . "/modele_groupe.php";

    class ContGroupe extends ContGenerique 
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueGroupe();
            $this->modele = new ModeleGroupe($this);
        }

        public function afficherListeGroupes()
        {
            $token = $this->genererToken();
            
            $liste_groupe = $this->modele->getListeGroupes();

            $liste_droits = $this->modele->getListeDroits();
            
            $this->vue->afficherListeGroupe($liste_groupe, $liste_droits, $token);
        }

        public function afficherModification()
        {
            $token = $this->genererToken();

            $id_groupe = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres("identifiant du groupe");
            
            $groupe = $this->modele->detailsGroupe($id_groupe);

            $this->vue->afficherDetailsGroupe($groupe->detailsGroupe(), $groupe->utilisateursGroupe(), $groupe->sousGroupes(), $token);
        }


        public function ajouterGroupe()
        {
            $this->validerToken();

            $nom_groupe = isset($_POST['nom_groupe']) ? htmlspecialchars($_POST['nom_groupe']) : $this->pasAssezDeParametres("nom du groupe");
            $nom_droits = isset($_POST['droits']) ? htmlspecialchars($_POST['droits']) : $this->pasAssezDeParametres("droits du groupe");

            $this->modele->ajouterGroupe($nom_groupe, $nom_droits);
        
            header('Location: index.php?module=administration&type=groupe&action=liste_groupes');
        }

        public function ajouterSousGroupe()
        {
            $this->validerToken();

            $id_groupe = isset($_GET['id_groupe']) ? htmlspecialchars($_GET['id_groupe']) : $this->pasAssezDeParametres("identifiant du groupe parent");
            $id_groupe_fils = isset($_POST['groupe_fils']) ? htmlspecialchars($_POST['groupe_fils']) : $this->pasAssezDeParametres("identifiant du groupe fils");

            $this->modele->ajouterSousGroupe($id_groupe_fils, $id_groupe);

            header('Location: index.php?module=administration&type=groupe&action=afficher_modification&id='.$id_groupe);
        }

        public function ajouterUtilisateur()
        {
            $this->validerToken();

            $id_groupe = isset($_GET['id_groupe']) ? htmlspecialchars($_GET['id_groupe']) : $this->pasAssezDeParametres("identifiant du groupe");
            $pseudo_utilisateur = isset($_POST['pseudo_utilisateur']) ?  htmlspecialchars($_POST['pseudo_utilisateur']) : $this->pasAssezDeParametres("pseudo utilisateur");

            $this->modele->ajouterUtilisateur($pseudo_utilisateur, $id_groupe);

            header('Location: index.php?module=administration&type=groupe&action=afficher_modification&id='.$id_groupe);
        }



        public function retirerUtilisateur()
        {
            $this->validerToken();

            $id_utilisateur = isset($_GET['id_utilisateur']) ? htmlspecialchars($_GET['id_utilisateur']) :$this->pasAssezDeParametres("identifiant de l'utilisateur à supprimer");
            
            $id_groupe = isset($_GET['id_groupe']) ? htmlspecialchars($_GET['id_groupe']) : $this->pasAssezDeParametres("identifiant du groupe");

            $this->modele->retirerUtilisateur($id_utilisateur, $id_groupe);

            header('Location: index.php?module=administration&type=groupe&action=afficher_modification&id='.$id_groupe);
        }

        public function retirerGroupe()
        {
            $this->validerToken();

            $sous_groupe = isset($_GET['sous_groupe']) ? htmlspecialchars($_GET['sous_groupe']) : $this->pasAssezDeParametres("identifiant de sous groupe à supprimer");
            
            $id_groupe = isset($_GET['id_groupe']) ? htmlspecialchars($_GET['id_groupe']) : $this->pasAssezDeParametres("identifiant du groupe parent");

            $this->modele->retirerSousGroupe($sous_groupe, $id_groupe);

            header('Location: index.php?module=administration&type=groupe&action=afficher_modification&id='.$id_groupe);
        }

        public function supprimerGroupe(){
            $this->validerToken();

            $id_groupe = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : $this->pasAssezDeParametres("identifiant du groupe");

            $this->modele->supprimerGroupe($id_groupe);

            header('Location: index.php?module=administration&type=groupe&action=liste_groupes');
        }
    }
