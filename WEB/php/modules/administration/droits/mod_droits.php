<?php
    require_once __DIR__ . "./../../../verify.php";
    require_once __DIR__ . "/cont_droits.php";

    class ModDroits
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContDroits();

            switch ($action) {
                case 'liste_droits':
                    $cont->afficherListeDroits();
                break;

                case 'afficher_ajouter_droits':
                    $cont->afficher_ajouter_droits();
                break;

                case 'ajouter_droits':
                    $cont->ajouterDroits();
                break;
                
                case 'afficher_modification':
                    $cont->afficherModification();
                break;

                case 'modifier_droits':
                    $cont->modifierDroit();
                break;

                default:
                    header('Location: index.php?module=administration&type=droits&action=liste_droits');
            }
        }
    }
