<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/vue_salle.php";
    require_once __DIR__ . "/modele_salle.php";
    
    class ContSalle extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueSalle();
            $this->modele = new ModeleSalle($this);
        }

        public function afficher_salles()
        {
            $token = $this->genererToken();

            $liste_salles = $this->modele->liste_salles();
            
            $this->vue->afficher_salles($liste_salles, $token);
        }

        public function afficher_salle()
        {
            $token = $this->genererToken();

            $nom_salle = isset($_GET['nom_salle']) ? htmlspecialchars($_GET['nom_salle']) : $this->pasAssezDeParametres('nom de la salle');

            $salle = $this->modele->getSalle($nom_salle);

            $this->vue->afficher_salle($salle->detailsSalle(), $nom_salle, $token);
        }


        public function ajouter_salle()
        {
            $this->validerToken();

            $nom_salle = isset($_POST['nom_salle']) ? htmlspecialchars($_POST['nom_salle']) : $this->pasAssezDeParametres('nom de la salle');
            $nb_pc = isset($_POST['nombre_ordinateurs_salle']) ? htmlspecialchars($_POST['nombre_ordinateurs_salle']) : $this->pasAssezDeParametres('nombre des ordinateurs dans la salle');
            $nb_places = isset($_POST['nombre_places_salle']) ? htmlspecialchars($_POST['nombre_places_salle']) : $this->pasAssezDeParametres('nombre des tables dans la salle');
            $contient_projecteur = isset($_POST['contient_projecteur']) ? true : false;

            $this->modele->ajouter_salle($nom_salle, $nb_pc, $nb_places, $contient_projecteur);

            header('Location: index.php?module=administration&type=salle&action=liste_salle');
        }


        public function supprimer_salle()
        {
            $this->validerToken();

            $nom_salle = isset($_GET['nom_salle']) ? htmlspecialchars($_GET['nom_salle']) : $this->pasAssezDeParametres('nom de la salle');

            $this->modele->supprimer_salle($nom_salle);

            header('Location: index.php?module=administration&type=salle&action=liste_salle');
        }

        public function modifier_salle(){
            $this->validerToken();
            
            $nom_salle = isset($_GET['nom_salle']) ? htmlspecialchars($_GET['nom_salle']) : $this->pasAssezDeParametres('nom de la salle');
            $nb_pc = isset($_POST['nombre_ordinateurs_salle']) ? htmlspecialchars($_POST['nombre_ordinateurs_salle']) : $this->pasAssezDeParametres('nombre des ordinateurs dans la salle');
            $nb_places = isset($_POST['nombre_places_salle']) ? htmlspecialchars($_POST['nombre_places_salle']) : $this->pasAssezDeParametres('nombre de tables dans la salle');
            $contient_projecteur = isset($_POST['contient_projecteur']) ? true : false;

            if (isset($_POST['modifier'])) {
                    $salle = $this->modele->modifier_salle($nom_salle, $nb_pc, $nb_places, $contient_projecteur);
            } else if (isset($_POST['supprimer'])) {
                    $this->modele->supprimer_salle($nom_salle);
            }else{
                ErrorHandler::afficherErreur(
                    new ParametresInsuffisantsException(),
                    INVALID_ACTION_ERROR_TITLE,
                    INVALID_ACTION_ERROR_MESSAGE
                );
            }

            header('Location: index.php?module=administration&type=salle&action=liste_salle');
        }
    }
