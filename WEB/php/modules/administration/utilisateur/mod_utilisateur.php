<?php
    require_once __DIR__ . "./../../../verify.php";
    require_once __DIR__ . "/cont_utilisateur.php";

    class ModUtilisateur
    {
        public function __construct()
        {
            $action = isset($_GET['action']) ? htmlspecialchars($_GET['action']) : null;
            $cont = new ContUtilisateur();

            switch ($action) {
                case 'liste_utilisateurs':
                    $cont->afficherListeUtilisateurs();
                break;

                case 'liste_personnels':
                    $cont->afficherListePersonnels();
                break;
                
                case 'afficherCreationUtilisateur':
                    $cont->afficherCreationUtilisateur();
                break;

                case 'inscription_utilisateur':
                    $cont->inscription();
                break;

                case 'ajouter_personnel':
                    $cont->ajouterPersonnel();
                break;

                case 'modification':
                    $cont->afficherModifierUtilisateur();
                break;
                
                case 'modification_utilisateur':
                    $cont->modifierUtilisateur();
                break;

                case 'afficher_modification_personnel':
                    $cont->afficherModifierPersonnel();
                break;

                case 'modifier_personnel':
                    $cont->modifierPersonnel();
                break;

                default:
                    header('Location: index.php?module=administration&type=utilisateur&action=liste_utilisateurs');
                    exit(0);
            }
        }
    }
