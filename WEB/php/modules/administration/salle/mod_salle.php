<?php
    require_once __DIR__ . "/cont_salle.php";
    require_once __DIR__ . "/../../../verify.php";

    class ModSalle
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContSalle();

            switch ($action) {
                case 'liste_salle':
                    $cont->afficher_salles();
                break;
                
                case 'ajouter_salle':
                    $cont->ajouter_salle();
                break;

                case 'afficher_salle':
                    $cont->afficher_salle();
                break;

                case 'modifier_salle':
                    $cont->modifier_salle();
                break;

                case 'supprimer_salle':
                    $cont->supprimer_salle();
                break;

                default:
                    header('Location: index.php?module=administration&type=salle&action=liste_salle');
                break;

            }
        }
    }
