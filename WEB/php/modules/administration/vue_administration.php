<?php
    require_once __DIR__ . "/../../verify.php";
    require_once __DIR__ . "/../../common/vue_generique.php";

    class VueAdministration extends VueGenerique
    {
        public function __construct($type)
        {
            $liste_liens = "";

            $liste_selection = "";

            $utilisateur_actif = $type === 'utilisateur' ? 'active' : '';
            $personnel_actif = $type === 'personnel' ? 'active' : '';
            $etudiants_actif = $type === 'etudiant' ? 'active' : '';
            $groupes_actif = $type === 'groupe' ? 'active' : '';
            $droits_actif = $type === 'droits' ? 'active' : '';
            $semestres_actif = $type === 'semestre' ? 'active' : '';
            $modules_actif = $type === 'module' ? 'active' : '';
            $salle_actif = $type === 'salle' ? 'active' : '';


            $utilisateur_selected = $type === 'utilisateur' ? 'selected' : '';
            $personnel_selected = $type === 'personnel' ? 'selected' : '';
            $etudiants_selected = $type === 'etudiant' ? 'selected' : '';
            $groupes_selected = $type === 'groupe' ? 'selected' : '';
            $droits_selected = $type === 'droits' ? 'selected' : '';
            $semestres_selected = $type === 'semestre' ? 'selected' : '';
            $modules_selected = $type === 'module' ? 'selected' : '';
            $salle_selected = $type === 'salle' ? 'selected' : '';


            $liste_liens .= $this->getButton("index.php?module=administration&type=utilisateur&action=liste_utilisateurs", 'Utilisateurs', $utilisateur_actif, 'droit_creation_utilisateurs');
            $liste_liens .= $this->getButton("index.php?module=administration&type=personnel&action=liste_personnels"    , 'Personnel'   , $personnel_actif  , 'droit_creation_utilisateurs');
            $liste_liens .= $this->getButton("index.php?module=administration&type=etudiant&action=liste_etudiants"      , 'Etudiant'    , $etudiants_actif  , 'droit_creation_utilisateurs');
            $liste_liens .= $this->getButton("index.php?module=administration&type=groupe&action=liste_groupes"          , 'Groupes'     , $groupes_actif    , 'droit_creation_groupes');
            $liste_liens .= $this->getButton("index.php?module=administration&type=droits&action=liste_droits"           , 'Droits'      , $droits_actif     , 'droit_modification_droits');
            $liste_liens .= $this->getButton("index.php?module=administration&type=module&action=liste_modules"          , 'Modules'     , $modules_actif    , 'droits_creation_modules');
            $liste_liens .= $this->getButton("index.php?module=administration&type=semestre&action=liste_semestre"       , 'Semestres'   , $semestres_actif  , 'droit_creation_cours');
            $liste_liens .= $this->getButton("index.php?module=administration&type=salle&action=liste_salles"            , 'Salles'      , $salle_actif      , 'droit_creation_cours');

            $liste_selection .= $this->getSelection("index.php?module=administration&type=utilisateur&action=liste_utilisateurs", 'Utilisateurs', $utilisateur_selected, 'droit_creation_utilisateurs');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=personnel&action=liste_personnels"    , 'Personnel'   , $personnel_selected  , 'droit_creation_utilisateurs');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=etudiant&action=liste_etudiants"      , 'Etudiant'    , $etudiants_selected  , 'droit_creation_utilisateurs');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=groupe&action=liste_groupes"          , 'Groupes'     , $groupes_selected    , 'droit_creation_groupes');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=droits&action=liste_droits"           , 'Droits'      , $droits_selected     , 'droit_modification_droits');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=module&action=liste_modules"          , 'Modules'     , $modules_selected    , 'droits_creation_modules');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=semestre&action=liste_semestre"       , 'Semestres'   , $semestres_selected  , 'droit_creation_cours');
            $liste_selection .= $this->getSelection("index.php?module=administration&type=salle&action=liste_salles"            , 'Salles'      , $salle_selected      , 'droit_creation_cours');

        //     echo '            
        //     <div class="container-fluid row justify-content-around mt-3 mt-md-0 administration mx-auto ">
        //         <div class="col-lg-3 px-md-0 mb-4 container d-none d-lg-block">
            
        //             <div class="list-group">
        //                 '.$liste_liens.'
        //                 <button type="button" class="list-group-item list-group-item-action">
        //                     Absences
        //                 </button>
            
        //             </div>
        //         </div>

        //     <div class="mb-4 row justify-content-center container d-lg-none ">
        //         <select class="form-control col-8 col-md-6" id="administration-choice">
        //             '. $liste_selection .'
        //             <option value="" >Absences</option>
        //         </select>
        
        //     </div>

            
        //     <div class="col-lg-8 container content mt-2 mb-3 mt-md-0">
        // ';

        echo '<div class="col-lg-11 container content mt-2 mb-3 mt-md-0">';
        }


        public function getButton($lien, $nom, $actif, $droit_obligatoire ){
            try{
                Utilisateur::possedeDroit($droit_obligatoire);
                return '
                        <a href="'.$lien.'">
                            <button type="button" class=" list-group-item list-group-item-action '. $actif .'">
                                '. $nom .'
                            </button>
                        </a>';
            }catch(NonAutoriseException $e){
                return "";
            }
        }

        public function getSelection($lien, $nom, $actif, $droit_obligatoire){
            try{
                Utilisateur::possedeDroit($droit_obligatoire);
                return '
                        <option value="'. $lien . '" '. $actif .'>
                            '. $nom .'
                        </option>';
            }catch(NonAutoriseException $e){
                return "";
            }
        }

    }
