<?php
    require_once __DIR__ . "/cont_etudiant.php";
    require_once __DIR__ . "/../../../verify.php";

    class ModEtudiant
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? $_GET['action'] : null;
            $cont = new ContEtudiant();

            switch ($action) {
                case 'liste_etudiant':
                    $cont->afficher_etudiants();
                break;
                
                case 'ajouter_etudiant':
                    $cont->ajouter_etudiant();
                break;
                
                case 'supprimer_etudiant':
                    $cont->supprimer_etudiant();
                break;

                case 'modifier_etudiant':
                    $cont->modifier_etudiant();
                break;

                case 'afficher_etudiant':
                    $cont->afficher_etudiant();
                break;

                case 'modifier_semestre_etudiant':
                    $cont->modifier_semestre_etudiant();    
                break;
                
                case 'modifier_moyenne_semestre':
                    $cont->modifier_moyenne_semestre();    
                break;
                default:
                    header('Location: index.php?module=administration&type=etudiant&action=liste_etudiant');
                break;
            }
        }
    }
