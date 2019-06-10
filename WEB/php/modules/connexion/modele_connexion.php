<?php
    require_once __DIR__ . "/../../verify.php";

    require_once __DIR__ . "/../../common/Database.php";

    class ModeleConnexion extends Database
    {
        private static $connect_query = 'select connect_user(:username, :password)';
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }

        /*
        Permet la connexion d'un utilisateur
        --Si l'utilisateur est connecté, alors il sera enregistré dans la variable du session
        --Si la connexion a échouée, le motif de cet échec sera affichée au client
        */
        public function seConnecter($username, $password)
        {
            $stmt = self::$db->prepare(self::$connect_query);

            $password = crypt($password, SALT_KEY);

            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $password);

            try {
                $stmt->execute();
                $id_utilisateur = $stmt->fetch(PDO::FETCH_NUM)[0];
                $this->enregistre_utilisateur($id_utilisateur);
            } catch (PDOException $e) {
                $this->cont->setError(Database::getPDOHint($e));
            }

        }
    

        /*
            Enregistre l'utilisateur dans une variable de session
            Attention, la variable de session ne contient que l'utilisateur avec son identifiant
            Les autres champs de cet utilisateur ne doivent pas être remplis, tel que ces droits...
        */
        public function enregistre_utilisateur($id_utilisateur)
        {
            $utilisateur = new Utilisateur($id_utilisateur);
            $_SESSION['utilisateur_connecte'] = serialize($utilisateur);        
        }
    }
