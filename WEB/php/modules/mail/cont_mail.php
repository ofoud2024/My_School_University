<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/vue_mail.php";
    require_once __DIR__ . "/modele_mail.php";
    
    class ContMail extends ContGenerique
    {
        private $vue;
        private $modele;

        public function __construct()
        {
            $this->vue = new VueMail();
            $this->modele = new ModeleMail($this);
        }

        public function afficher_mails_recus()
        {
            $liste_mails_recus = $this->modele->liste_mails_recus();
            
            $this->vue->afficher_liste_mails($liste_mails_recus, true);
        }

        public function afficher_mails_envoyes()
        {            
            $liste_mails_envoyes = $this->modele->liste_mails_envoyes();
            $this->vue->afficher_liste_mails($liste_mails_envoyes, false);
        }

        public function afficher_envoyer_mail(){
            $this->vue->afficher_envoyer_mail();
        }


        public function envoyer_mail()
        {
            $sujet_mail = isset($_POST['sujet_mail']) ? htmlspecialchars($_POST['sujet_mail']) : $this->pasAssezDeParametres('sujet du mail');
            
            $message_mail = isset($_POST['message_mail']) ? htmlspecialchars($_POST['message_mail']) : $this->pasAssezDeParametres('message du mail');
            
            $utilisateurs = isset($_POST['utilisateurs_destinataire']) ? htmlspecialchars($_POST['utilisateurs_destinataire']) : '';
            
            $groupes = isset($_POST['groupes_destinataire']) ? htmlspecialchars($_POST['groupes_destinataire']) : '';

            try{
                $fichier = new FileUpload("piece_jointe");
            }catch(FichierInexistant $e){
                $fichier = null;
            }

            $this->modele->envoyer_mail($sujet_mail, $message_mail, $utilisateurs, $groupes, $fichier);

            header('Location: index.php?module=mail&action=liste_mails_recus');
        }


        public function supprimer_mail()
        {
            $id_mail = isset($_GET['id_mail']) ? htmlspecialchars($_GET['id_mail']) : $this->pasAssezDeParametres('identifiant du mail');

            $this->modele->supprimer_mail($id_mail);

            header('Location: index.php?module=mail&action=liste_mails_envoyes');
        }

        public function cacher_mail(){
            $id_mail = isset($_GET['id_mail']) ? htmlspecialchars($_GET['id_mail']) : $this->pasAssezDeParametres('identifiant du mail');

            $this->modele->cacher_mail($id_mail);

            header('Location: index.php?module=mail&action=liste_mails_recus');
        }

    }
