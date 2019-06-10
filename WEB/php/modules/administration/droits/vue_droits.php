<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueDroits extends VueGenerique
    {
        public function __construct()
        {
        }

        public function afficherDetailsListeDroits($liste_droits)
        {
            try{
                Utilisateur::possedeDroit('droit_modification_droits');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des droits")
                );                        
            }

            require_once "html/liste_droits_1.html";

            echo "<tbody>";
            if (count($liste_droits) > 0) {
                foreach ($liste_droits as $droit) {
                    echo "<tr onclick='window.location.href=\"index.php?module=administration&type=droits&action=afficher_modification&nom_droits=".$droit['nom_droits']."\"'>";

                    echo "<td>${droit['nom_droits']}</td>";

                    echo "<td>".$this->showCond($droit['droit_creation_utilisateurs'])."</td>";
                    echo "<td>".$this->showCond($droit['droits_creation_modules'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_creation_cours'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_creation_groupes'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_modification_absences'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_modification_droits'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_modification_heures_travail'])."</td>";
                    echo "<td>".$this->showCond($droit['droit_visualisation_statistique'])."</td>";

                    echo "</tr>";
                }
            } 

            require_once __DIR__ . "/html/liste_droits_2.html";
        }


        public function ajouterDroits($token)
        {
            try{
                Utilisateur::possedeDroit('droit_modification_droits');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajouter un droit")
                );                        
            }

            $input_token = $this->inputToken($token);
            echo '
            <h2 class="text-center text-dark underline mb-4 pt-2 underline">
                Créer un droit
            </h2>
            
            <form method="post" action="index.php?module=administration&type=droits&action=ajouter_droits" class="container-fluid row mt-2">
            
                <div class="col-12 form-group row ">
                    <label for="nom_droits" class="text-info text-right col-form-label col-md-4">Nom de droit</label>
                    <div class="col-md-6 ">
                        <input type="text" required class="mt-1 small-input form-control" id="nom_droits" name="nom_droits" />
                    </div>
            
                </div>
            
                <div class="form-check col-md-4 justify-content-center">
                    <input class="form-check-input" type="checkbox" name="creation_utilisateurs" id="creation_utilisateurs" />
                    <label class="form-check-label" for="creation_utilisateurs">
                        création utilisateurs
                    </label>
                </div>
            
                <div class="form-check col-md-4 justify-content-center">
                    <input class="form-check-input" type="checkbox" name="creation_modules" id="creation_modules" />
                    <label class="form-check-label" for="creation_modules">
                        création modules
                    </label>
                </div>
            
                <div class="form-check col-md-4  justify-content-center">
                    <input class="form-check-input" type="checkbox" name="creation_cours" id="creation_cours" />
                    <label class="form-check-label" for="creation_cours">
                        création cours
                    </label>
                </div>
            
                <div class="form-check col-md-4 justify-content-center">
                    <input class="form-check-input" type="checkbox" name="creation_groupes" id="creation_groupes" />
                    <label class="form-check-label" for="creation_groupes">
                        création groupes
                    </label>
                </div>
            
                <div class="form-check col-md-4 justify-content-center">
                    <input class="form-check-input" type="checkbox" name="modification_abscences" id="modification_abscences" />
                    <label class="form-check-label" for="modification_abscences">
                        modification absences
                    </label>
                </div>
            
                <div class="form-check col-md-4  justify-content-center">
                    <input class="form-check-input" type="checkbox" name="statistiques" id="statistiques" />
                    <label class="form-check-label" for="statistiques">
                        Visualisation statistiques
                    </label>
                </div>
            
                <div class="form-check col-md-6  justify-content-center">
                    <input class="form-check-input" type="checkbox" name="modification_heures_travail" id="modification_heures_travail" />
                    <label class="form-check-label" for="modification_heures_travail">
                        modification heures de travail
                    </label>
                </div>
            
                <div class="form-check col-md-4 offset-md-2  justify-content-center">
                    <input class="form-check-input" type="checkbox" name="modifications_droits" id="modifications_droits" />
                    <label class="form-check-label" for="modifications_droits">
                        modification droits
                    </label>
                </div>
                '.$input_token.'
            
            
                <div class="container-fluid justify-content-center row">
                    <button type="submit" class="btn btn-outline-primary">Créer</button>
                </div>
            
        
            
            </form>
            ';
        }




        public function modifierDroits($droit, $liste_utilisateurs = array(), $liste_groupes = array(), $token)
        {
            try{
                Utilisateur::possedeDroit('droit_modification_droits');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modifier un droit")
                );                        
            }

            $input_token = $this->inputToken($token);

            $creation_utilisateur = $droit['droit_creation_utilisateurs'] == 1 ? 'checked' : '';
            $creation_modules = $droit['droits_creation_modules'] == 1 ? 'checked' : '';
            $creation_cours = $droit['droit_creation_cours'] == 1 ? 'checked' : '';
            $creation_groupes = $droit['droit_creation_groupes'] == 1 ? 'checked' : '';
            $modification_absences = $droit['droit_modification_absences'] == 1 ? 'checked' : '';
            $modification_droits = $droit['droit_modification_droits'] == 1 ? 'checked' : '';
            $modification_heures_travail = $droit['droit_modification_heures_travail'] == 1 ? 'checked' : '';
            $statistiques = $droit['droit_visualisation_statistique'] == 1 ? 'checked' : '';


            echo '
        <h2 class="text-center text-dark underline mb-4 pt-2 underline">
            Modification du droit : '.$droit['nom_droits'].'
        </h2>
        
        <form method="post" action="index.php?module=administration&type=droits&action=modifier_droits&nom_droits='.$droit['nom_droits'] .'" class="container-fluid row mt-2">
                
            <div class="form-check col-md-4 justify-content-center">
                <input class="form-check-input" type="checkbox" '.$creation_utilisateur.' name="creation_utilisateurs" id="creation_utilisateurs" />
                <label class="form-check-label" for="creation_utilisateurs">
                    création utilisateurs
                </label>
            </div>
        
            <div class="form-check col-md-4 justify-content-center">
                <input class="form-check-input" type="checkbox" '.$creation_modules.' name="creation_modules" id="creation_modules" />
                <label class="form-check-label" for="creation_modules">
                    création modules
                </label>
            </div>
        
            <div class="form-check col-md-4  justify-content-center">
                <input class="form-check-input" type="checkbox" '.$creation_cours.' name="creation_cours" id="creation_cours" />
                <label class="form-check-label" for="creation_cours">
                    création cours
                </label>
            </div>
        
            <div class="form-check col-md-4 justify-content-center">
                <input class="form-check-input" type="checkbox" '.$creation_groupes.' name="creation_groupes" id="creation_groupes" />
                <label class="form-check-label" for="creation_groupes">
                    création groupes
                </label>
            </div>
        
            <div class="form-check col-md-4 justify-content-center">
                <input class="form-check-input" type="checkbox" '.$modification_absences.' name="modification_abscences" id="modification_abscences" />
                <label class="form-check-label" for="modification_abscences">
                    modification absences
                </label>
            </div>
        
            <div class="form-check col-md-4  justify-content-center">
                <input class="form-check-input" type="checkbox" '.$statistiques.' name="statistiques" id="statistiques" />
                <label class="form-check-label" for="statistiques">
                    Visualisation statistiques
                </label>
            </div>
        
            <div class="form-check col-md-6  justify-content-center">
                <input class="form-check-input" type="checkbox" '.$modification_heures_travail.' name="modification_heures_travail" id="modification_heures_travail" />
                <label class="form-check-label" for="modification_heures_travail">
                    modification heures de travail
                </label>
            </div>
            '.$input_token.'
            <div class="form-check col-md-4 offset-md-2  justify-content-center">
                <input class="form-check-input" type="checkbox" '. $modification_droits . ' name="modifications_droits" id="modifications_droits" />
                <label class="form-check-label" for="modifications_droits">
                    modification droits
                </label>
            </div>
        
            <div class="container-fluid justify-content-around row mt-3">
                <button type="submit" name="modifier" class="btn btn-outline-primary">Modifier</button>
                <button type="submit" name="supprimer" class="btn btn-outline-danger">Supprimer</button>
            </div>
        
        
        </form>';
        }
    }
