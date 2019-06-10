<?php
    require_once __DIR__ . "/../verify.php";
    require_once __DIR__ . "/modele_mail_api.php";
    require_once __DIR__ . "/../mod_api_generique.php";

    class ModMailAPI extends ModAPIGenerique
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? strtolower(htmlspecialchars($_GET['action'])) : $this->pasAssezDeParametres('action');

            $modele = new ModeleMailAPI();

            switch($action){
                case 'details_mail':
                    $id_mail = isset($_GET['id_mail']) ? htmlspecialchars($_GET['id_mail']) : $this->pasAssezDeParametres("identifiant du mail");
        
                    $modele->detailsMail($id_mail);
                break;

                case 'ajouter_reponse':
                    $id_mail = isset($_POST['id_mail']) ? htmlspecialchars($_POST['id_mail']) : $this->pasAssezDeParametres("identifiant du mail");
                    $message_reponse =  isset($_POST['message_reponse']) ? htmlspecialchars($_POST['message_reponse']) : $this->pasAssezDeParametres("réponse au mail");
                    $modele->ajouterReponse($id_mail, $message_reponse);
                break;

                case 'reponses_mail':
                    $id_mail = isset($_GET['id_mail']) ? htmlspecialchars($_GET['id_mail']) : $this->pasAssezDeParametres("identifiant du mail");
                    $modele->reponses_mail($id_mail);
                break;

                case 'telecharger_piece_jointe':
                    $id_mail = isset($_GET['id_mail']) ? htmlspecialchars($_GET['id_mail']) : $this->pasAssezDeParametres("identifiant du mail");
                    $modele->telecharger_mail($id_mail);
                break;
            }

        }

    }