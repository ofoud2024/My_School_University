<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/cont_connexion.php";
    
    class ModConnexion
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContConnexion();
            switch ($action) {
                case 'afficherConnexion':
                    $cont->afficherConnexion();
                break;
                case 'seConnecter':
                    $cont->seConnecter();
                break;

                case 'seDeconnecter':
                    $cont->seDeconnecter();
                break;
                default:
                    header("Location: index.php?module=connexion&action=afficherConnexion");
            }
        }
    }
