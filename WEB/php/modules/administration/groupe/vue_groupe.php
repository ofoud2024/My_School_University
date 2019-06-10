<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../../../common/vue_generique.php";

    class VueGroupe extends VueGenerique
    {
        public function __construct()
        {
        }

        public function afficherListeGroupe($liste_groupe, $liste_droits, $token)
        {
            try{
                Utilisateur::possedeDroit('droit_creation_groupes');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste des groupes")
                );                        
            }

            $input_token = $this->inputToken($token);

            $this->afficherTableau(
                $liste_groupe,
                array('id_groupe', 'nom_groupe', 'nom_droits', 'nombre_utilisateurs', 'nombre_sous_groupes'),
                'index.php?module=administration&action=afficher_modification&type=groupe&id=',
                'id_groupe',
                array('#', 'nom', 'droits', 'nombre utilisateurs', 'nombre sous groupes')
            );

            echo '
            <form class="row justify-content-center container-fluid" method="post" action="index.php?module=administration&type=groupe&action=ajouter_groupe">
                '.$input_token.'
                <div class="row container-fluid  justify-content-around">
                    <div class="form-group col-md-5">
                        <label for="nom_groupe">Nom de groupe</label>
                        <input type="text" class="form-control" id="nom_groupe" name="nom_groupe" placeholder="nom_groupe">
                    </div>
                    <div class="form-group col-md-5">
                        <label for="droits">Droits de groupe</label>
            ';
            
            $this->afficherListeDroits($liste_droits);
            
            echo '
                    </div>
                </div>
      
                <button class="btn btn-outline-success" type="submit">Ajouter</button>
            </form>
            ';
        }


        public function afficherDetailsGroupe($groupe, $liste_utilisateurs, $sous_groupes, $token)
        {
            try{
                Utilisateur::possedeDroit('droit_creation_groupes');                
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder aux détails du groupe")
                );                        
            }

            $input_token = $this->inputToken($token);

            foreach ($liste_utilisateurs as &$utilisateur) {
                if (is_numeric($utilisateur['id_enseignant'])) {
                    $utilisateur['niveau'] = 'Enseignant';
                } elseif (is_numeric($utilisateur['id_personnel'])) {
                    $utilisateur['niveau'] = 'Personnel';
                } elseif (is_numeric($utilisateur['num_etudiant'])) {
                    $utilisateur['niveau'] = 'Etudiant';
                } else {
                    $utilisateur['niveau'] = '';
                }
            }

            $tab_utilisateurs = $this->transformerEnTableauSuppression(
                $liste_utilisateurs,
                array('pseudo_utilisateur', 'niveau', 'periode'),
                'id_utilisateur',
                'index.php?module=administration&type=groupe&action=retirer_utilisateur&token='.$token.'&id_groupe='.$groupe['id_groupe'].'&id_utilisateur='
            );

            $tab_groupes = $this->transformerEnTableauSuppression(
                $sous_groupes,
                array('nom_groupe', 'nombre_utilisateurs'),
                'id_groupe',
                'index.php?module=administration&type=groupe&action=retirer_groupe&token='.$token.'&id_groupe='.$groupe['id_groupe'].'&sous_groupe='
            );

            echo '
        <h2 class="text-center text-dark underline mb-4 pt-2 underline">
            Groupe '.$groupe['nom_groupe'].'
        </h2>
        
        <fieldset >
            <legend align="center" class="col-auto px-0">Utilisateurs</legend>
            <div class="table-responsive px-3">
                <table id="data-table" class="small-table text-center table table-striped table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Pseudo</th>
                            <th scope="col">Status</th>
                            <th scope="col">Période</th>
                            <th scope="col">Retirer</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$tab_utilisateurs.'
                    </tbody>
                </table>
                <form class="form-inline" method="post" action="index.php?module=administration&type=groupe&action=ajouter_utilisateur&id_groupe='.$groupe['id_groupe'].'">
                    '.$input_token.'
                    <div class="container-fluid row justify-content-around mt-3 mb-3">
                        <div class="container col-9 row justify-content-around py-0 my-0">
                            <label class="col-4 pt-1">Pseudo</label>
                            <input type="text" id="pseudo_utilisateur" list="pseudos_utilisateurs" name="pseudo_utilisateur" class="form-control col-8" />
                        </div>
                        <button type="submit" class="btn btn-success">Ajouter</button>
                    </div>
                    <datalist id="pseudos_utilisateurs"></datalist>
                </form>
        
            </div>
        </fieldset>
        
        
        <fieldset class="mt-4">
            <legend align="center" class="col-auto px-0">Sous groupes</legend>
            <div class="table-responsive px-3">
                <table  class="data-table small-table  text-center table table-striped table-hover table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Sous_groupe</th>
                            <th scope="col">Nombre_utilisateurs</th>
                            <th scope="col">Retirer</th>
                        </tr>
                    </thead>
                    <tbody>
                        '.$tab_groupes.'
                    </tbody>
                </table>
        
                <form class="form-inline mb-4" method="post" action="index.php?module=administration&type=groupe&action=ajouter_sous_groupe&id_groupe='.$groupe['id_groupe'].'">
                    '.$input_token.'
                    <div class="container-fluid row justify-content-around mt-2">
                        <div class="container col-9 row justify-content-around py-0 my-0">
                            <label class="col-4 pt-2">groupe</label>
                            <input type="text" class="form-control col-8" list="groupes_fils" id="groupe_fils"  name="groupe_fils"/>
                        </div>
                        <button type="submit" class="btn btn-success ">Ajouter</button>
                    </div>
                    <datalist id="groupes_fils">
                    </datalist>
                </form>
        
            </div>
        </fieldset>
        
        <div class="container-fluid row justify-content-center mt-3">
            <a href="index.php?module=administration&type=groupe&action=supprimer_groupe&token='.$token.'&id='.$groupe['id_groupe'].'">
                <button class="btn-outline-danger col-auto btn">Supprimer</button> 
            </a>
        </div>
        
        ';
        }
    }
