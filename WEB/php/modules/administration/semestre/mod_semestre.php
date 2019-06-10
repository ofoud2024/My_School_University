<?php
    require_once __DIR__ . "/cont_semestre.php";
    require_once __DIR__ . "/../../../verify.php";

    class ModSemestre
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContSemestre();

            switch ($action) {
                case 'liste_semestre':
                    $cont->afficher_semestres();
                break;
                
                case 'ajouter_semestre':
                    $cont->ajouter_semestre();
                break;

                case 'afficher_semestre':
                    $cont->afficher_semestre();
                break;

                case 'modifier_semestre':
                    $cont->modifier_semestre();
                break;

                case 'retirer_etudiant':
                    $cont->retirer_etudiant();
                break;

                case 'supprimer_semestre':
                    $cont->supprimer_semestre();
                break;

                default:
                    header('Location: index.php?module=administration&type=semestre&action=liste_semestre');
                break;

            }
        }
    }
