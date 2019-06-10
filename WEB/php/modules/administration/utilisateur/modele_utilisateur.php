<?php
    require_once __DIR__ ."/../../../verify.php";
    require_once __DIR__ ."/../../../common/Database.php";
    require_once __DIR__ ."/../../../common/classes/user/utilisateur.php";
    require_once __DIR__ ."/../../../common/classes/user/personnel.php";
    require_once __DIR__ ."/../../../common/classes/user/enseignant.php";
    require_once __DIR__ ."/../../../common/classes/droits.php";

    class ModeleUtilisateur extends Database
    {
        private $cont;

        public function __construct($cont)
        {
            $this->cont = $cont;
        }


        /****************************************************************************************************/
        /*******************************************Utilisateurs**********************************************/
        /****************************************************************************************************/
        
        public function getListeUtilisateurs()
        {
            try{
                return Utilisateur::getListeUtilisateurs();
            }catch(PDOException $e) {
                ErrorHandler::afficherErreur($e);
            }
        }


        public function getUtilisateur($param)
        {
            try{
                $utilisateur = new Utilisateur($param, $param);
                return $utilisateur->informations_utilisateur();
            }catch(PDOException $e){                
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$param)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder aux détails d'un utilisateur")
                );
            }
        }


        public function ajouterUtilisateur($data)
        {
            try{
                $data['pays_naissance'] =  $this->getCodePays($data['pays_naissance']);
                Utilisateur::ajouterUtilisateur($data);    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>'cet utilisateur')
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"Création utilisateur")
                );
            }
        }

        public function modifierUtilisateur($data, $id)
        {
            
            $data['pays_naissance'] =  $this->getCodePays($data['pays_naissance']);
            try{
                $utilisateur = new Utilisateur($id, $id);

                $utilisateur->modifierUtilisateur($data);
                if (strlen($data['mot_de_passe']) > 3) {
                    $utilisateur->modifierMDPUtilisateur($data['mot_de_passe']);
                }    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"cet utilisateur", 'id'=>$id)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$id)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modification utilisateur")
                );
            }
        }

        public function supprimerUtilisateur($id)
        {
            try{
                $utilisateur = new Utilisateur($id, $id);
                $utilisateur->supprimerUtilisateur();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"cet utilisateur", 'id'=>$id)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$id)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"suppression utilisateur")
                );
            }
        }




        /****************************************************************************************************/
        /***************************************Fin Utilisateurs**********************************************/
        /****************************************************************************************************/


        /****************************************************************************************************/
        /*******************************************PERSONNEL**********************************************/
        /****************************************************************************************************/

        public function getListePersonnels()
        {
            try{
                return Personnel::getListePersonnels();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder à la liste détailler des personnels")
                );
            }
        }

        public function getPersonnel($id)
        {
            try{
                $personnel = new Personnel($id);
                return $personnel->informations_personnel();
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'ce personnel', 'id'=>$id)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"accéder aux détails du personnel")
                );
            }
        }


        public function ajouterPersonnel($pseudo, $estEnseignant)
        {

            self::$db->beginTransaction();

            try{
                $utilisateur = new Utilisateur('', $pseudo);
            
                $id_personnel = Personnel::ajouterPersonnel($utilisateur->getIdUtilisateur());
                
                if ($estEnseignant) {
                    Enseignant::ajouterEnseignant($id_personnel);
                }
    
                self::$db->commit();    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    INSERT_ERROR_TITLE,
                    INSERT_ERROR_MESSAGE, 
                    array('type'=>"ce personnel")
                );

            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'l\'utilisateur', 'id'=>$pseudo)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"ajout personnel")
                );
            }

        }

        public function modifierPersonnel($id_personnel, $est_enseignant, $heures_travail)
        {
            self::$db->beginTransaction();

            try{
                $personnel = new Personnel($id_personnel);

                $informations_personnel = $personnel->informations_personnel();

                $personnel->modifierHeuresTravail($heures_travail);
    
                if (null === $informations_personnel['id_enseignant'] && $est_enseignant) {
                    Enseignant::ajouterEnseignant($id_personnel);
                } elseif (null !== $informations_personnel['id_enseignant'] && !$est_enseignant) {
                    Enseignant::supprimerEnseignant($id_personnel);
                }
                self::$db->commit();    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    UPDATE_ERROR_TITLE,
                    UPDATE_ERROR_MESSAGE, 
                    array('type'=>"ce personnel", 'id'=>$id_personnel)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le personnel', 'id'=>$id_personnel)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"modification personnel")
                );
            }
            
        }

        public function supprimerPersonnel($id_personnel)
        {
            try{
                $personnel = new Personnel($id_personnel);
                Enseignant::supprimerEnseignant($id_personnel);
                $personnel->supprimerPersonnel();    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur(
                    $e,
                    DELETE_ERROR_TITLE,
                    DELETE_ERROR_MESSAGE, 
                    array('type'=>"ce personnel", 'id'=>$id_personnel)
                );
            }catch(ElementIntrouvable $e){
                ErrorHandler::afficherErreur(
                    $e,
                    NOT_FOUND_ERROR_TITLE, 
                    NOT_FOUND_ERROR_MESSAGE, 
                    array('element'=>'le personnel', 'id'=>$id_personnel)
                );
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"suppression du personnel")
                );
            }
        }


        /****************************************************************************************************/
        /****************************************FIN PERSONNEL**********************************************/
        /****************************************************************************************************/


        
        /****************************************************************************************************/
        /****************************************DROITS***********************************************/
        /****************************************************************************************************/

        public function getListeDroits()
        {
            $liste_droits = array();

            try{
                foreach (Droits::getListeDroits() as $droits) {
                    array_push($liste_droits, $droits['nom_droits']);
                }    
            }catch(PDOException $e){
                ErrorHandler::afficherErreur($e);
            }catch(NonAutoriseException $e){
                ErrorHandler::afficherErreur(
                    $e, 
                    NOT_ENOUGH_ROLES_TITLE, 
                    NOT_ENOUGH_ROLES_MESSAGE, 
                    array('action'=>"récupération des droits")
                );
            }

            return $liste_droits;
        }
  

        /****************************************************************************************************/
        /****************************************FIN DROITS**********************************************/
        /****************************************************************************************************/
    
        /*
            La seule requête executée ici, 
            Y'a pas intêret de créer une classe pour un objet aussi peu utilisée (Les pays).
        */
        private function getCodePays($nomPays)
        {
         
            $stmt = self::$db->prepare("select code_pays from pays where nom_pays = ?");
           
            $result = false;

            try {
                $stmt->execute(array($nomPays));
                $result = $stmt->fetch(PDO::FETCH_ASSOC)['code_pays'];
            } catch (PDOException $e) {
                ErrorHandler::afficherErreur(
                    $e
                );
            }

            if($result == null)
            ErrorHandler::afficherErreur(
                    $e,
                    INVALID_COUNTRY_TITLE,
                    INVALID_COUNTRY_MESSAGE
                );
        
            return $result;
        }
    }
