<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/vue_profile.php";
    require_once __DIR__ . "/modele_profile.php";
    require_once __DIR__ . "/../../common/cont_generique.php";

    class ContProfile extends ContGenerique
    {
        private $vue;
        private $modele;
        
        public function __construct()
        {
            $this->vue = new VueProfile();
            $this->modele = new ModeleProfile($this);
        }

        public function afficherProfile(){
            $token = $this->genererToken();

            $profile = $this->modele->details_utilisateur();
            
            $this->vue->afficherProfile($profile, $token);
        }


        public function changer_mdp(){
            $this->validerToken();
            
            $mot_de_passe = isset($_POST['mot_de_passe']) ? htmlspecialchars($_POST['mot_de_passe']) : $this->pasAssezDeParametres('mot de passe');
            $confirmation_mot_de_passe = isset($_POST['confirmation_mot_de_passe']) ? htmlspecialchars($_POST['confirmation_mot_de_passe']) : $this->pasAssezDeParametres('confirmation du mot de passe');

            if($mot_de_passe !== $confirmation_mot_de_passe){
                ErrorHandler::afficherErreur(
                    new Exception("Mot de passe ne correspond pas à la confirmation"),
                    "PASSOWRD_CONFIRMATION_ERROR_TITLE",
                    "PASSOWRD_CONFIRMATION_ERROR_MESSAGE"
                );
            }else{
                $this->modele->changerMdp($mot_de_passe);
            }

            header('Location: index.php?module=profile&toastr=Mot de passe modifié avec succès&toastr_type=success');
            
        }
    }
