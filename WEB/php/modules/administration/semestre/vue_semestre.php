<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueSemestre extends VueGenerique
    {
        public function __construct()
        {
        }

        public function afficher_semestres($liste_semestres, $token)
        {
            $input_token = $this->inputToken($token);

            try{
                Utilisateur::possedeDroit('droit_creation_cours');                
              }catch(NonAutoriseException $e){
                  ErrorHandler::afficherErreur(
                      $e, 
                      NOT_ENOUGH_ROLES_TITLE, 
                      NOT_ENOUGH_ROLES_MESSAGE, 
                      array('action'=>"afficher la liste des semestres")
                  );                        
            } 
  
            echo '<h2 class="text-center text-dark underline mb-4 pt-2 underline">
                     Gestion des semestres
                </h2>';
            
            $this->afficherTableau(
                $liste_semestres,
                array('ref_semestre', 'nom_semestre', 'points_ets_semestre', 'nombre_reussite', 'nombre_echoue'),
                'index.php?module=administration&type=semestre&action=afficher_semestre&id=',
                'ref_semestre',
                array('reference', 'nom', 'points ets', 'nombre réussite', 'nombre échecs')
            );

            echo '
                <form
                autocomplete="off"
                method="post"
                action="index.php?module=administration&type=semestre&action=ajouter_semestre"
                >
                '.$input_token.'
                <h4 class=" text-center mt-3 mb-1 underline">Ajout de semestre</h4>
                <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="reference">Référence</label>
                    <input
                    type="text"
                    class="form-control"
                    id="reference"
                    name="reference"
                    placeholder="S1"
                    required
                    />
                </div>
                <div class="form-group col-md-6">
                    <label for="nom_semestre">Nom</label>
                    <input
                    type="text"
                    class="form-control"
                    id="nom_semestre"
                    name="nom_semestre"
                    placeholder="Semestre 1"
                    required
                    />
                </div>
                </div>
            
                <div class="form-row">
                <div class="form-group col-md-6 ">
                    <label for="points_ets">Points ets</label>
                    <input
                    type="number"
                    class="form-control"
                    min="0"
                    id="points_ets"
                    name="points_ets"
                    placeholder="30 pts"
                    required
                    />
                </div>
            
                <div class="form-group col-md-6">
                    <label for="periode">Période de l\'année</label>
                    <select class="form-control" id="periode" name="periode" required>
                    <option value="1">Première partie</option>
                    <option value="2">Deuxième partie</option>
                    </select>
                </div>
                </div>
            
                <div class="container-fluid row justify-content-center">
                <button type="submit" class="btn btn-success mb-2">Ajouter</button>
                </div>
            </form>
          
            ';
        }

        public function afficher_semestre($semestre, $annee_semestre, $etudiants_semestre, $annee_courante, $token)
        {
            $input_token = $this->inputToken($token);
            try{
                Utilisateur::possedeDroit('droit_creation_cours');                
              }catch(NonAutoriseException $e){
                  ErrorHandler::afficherErreur(
                      $e, 
                      NOT_ENOUGH_ROLES_TITLE, 
                      NOT_ENOUGH_ROLES_MESSAGE, 
                      array('action'=>"afficher les détails du semestre")
                  );                        
            } 

            echo "<h2 class='text-center underline'> Modification du semestre ${semestre['ref_semestre']}</h2>";

            echo '
            <form autocomplete="off" method="post" action="index.php?module=administration&type=semestre&action=modifier_semestre&id='.$semestre['ref_semestre'].'">
                '.$input_token.'
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="nom_semestre">Nom</label>
                        <input type="text" class="form-control" id="nom_semestre" name="nom_semestre" placeholder="Semestre 1"
                            required value = "'.$semestre['nom_semestre'].'"/>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="points_ets">Points ets</label>
                        <input type="number" class="form-control" min="0" id="points_ets" name="points_ets" placeholder="30 pts"
                            required value = "'.$semestre['points_ets_semestre'].'"/>
                    </div>
                </div>

                
            
                <div class="container-fluid row justify-content-center">
                    <button type="submit" class="btn btn-outline-primary mb-2">Modifier</button>
                </div>
            </form>';



            echo '<fieldset class="px-3 pb-3"><legend align="center" class="col-auto px-0">Les années du semestre</legend>';

            $this->afficherTableau(
                $annee_semestre,
                array('annee', 'moyenne', 'nombre_reussite', 'taux_reussite', 'nombre_echecs', 'taux_echecs')
            );

            echo '</fieldset>';


            echo '<fieldset class="px-3 pb-3"><legend align="center" class="col-auto px-0">Etudiants du semestre</legend>';

            $this->afficherTableauSuppression(
                $etudiants_semestre,
                array('annee', 'num_etudiant', 'nom_utilisateur', 'prenom_utilisateur', 'moyenne'),
                'num_etudiant',
                'index.php?module=administration&type=semestre&token='.$token.'&action=retirer_etudiant&id='.$semestre['ref_semestre'].'&etudiant=',
                array('annee', 'numéro', 'nom', 'prenom', 'moyenne'),
                function ($etudiant_semestre) use ($annee_courante) {
                    return $etudiant_semestre != null && $etudiant_semestre['annee'] == $annee_courante;
                }
            );


            echo '</fieldset>';


            echo "<div class='container-fluid justify-content-center mt-2 row'>
                    <a href='index.php?module=administration&type=semestre&action=supprimer_semestre&token=${token}&id=${semestre['ref_semestre']}'>
                        <button class='col-auto btn btn-outline-danger'>Supprimer</button>
                    </a>
                  </div>";
        }
    }
