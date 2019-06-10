<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/vue_connexion.php";
    require_once __DIR__ . "/modele_connexion.php";

    class ContConnexion
    {
        private $vue;
        private $modele;
        
        public function __construct()
        {
            $this->vue = new VueConnexion();
            $this->modele = new ModeleConnexion($this);
        }

        public function afficherConnexion()
        {
            if (!Utilisateur::estConnecte()) {
                $error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : null;
                $this->vue->afficherConnexion($error);
            }else{
                header('Location: index.php?module=edt');
            }
        }

        public function seConnecter()
        {
            $username = isset($_POST['username']) ? $_POST['username'] : $this->setError(UNDEFINED_USERNAME_ERROR);
            $password = isset($_POST['password']) ? $_POST['password'] : $this->setError(UNDEFINED_PASSWORD_ERROR);
            $this->modele->seConnecter($username, $password);
            header('Location: index.php?module=edt');

        }

        public function seDeconnecter(){
            session_destroy();
            header('Location: index.php?module=connexion&action=afficherConnexion');
        }

        public function setError($message)
        {
            header('Location: index.php?module=connexion&action=afficherConnexion&error='.$message);
            exit(0);
        }
    }
