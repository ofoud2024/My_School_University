<?php
    require_once __DIR__ . "/../../../verify.php";
    require_once __DIR__ . "/../classe_generique.php";
    require_once __DIR__ . "/../../exceptions/NonConnecterException.php";

    class Utilisateur extends ClasseGenerique
    {
        //Vérifie si un utilisateur porte l'identifiant :id_utilisateur
        private static $checkForUserQuery       = "select id_utilisateur from utilisateur where id_utilisateur = ?";

        //Renvoie la liste des utilisateurs
        private static $userListQuery           = 'select * from utilisateur';
        
        //Renvoie les détails d'un utilisateur
        private static $userQuery               = 'select * from utilisateur 
                                                    left join ville on (utilisateur.code_postal_ville = ville.code_postal_ville)
                                                    left join pays on (utilisateur.code_pays = pays.code_pays) where id_utilisateur = :id_utilisateur';

        //Renvoie l'identifiant de l'utilisateur correspondant à un pseudo
        private static $pseudoToIdQuery         = 'select id_utilisateur from utilisateur where pseudo_utilisateur = :pseudo';




        private static $simpleUserDetails       = 'select id_utilisateur, pseudo_utilisateur, nom_utilisateur, prenom_utilisateur,
                                                    ref_semestre as semestre, id_enseignant is null as est_enseignant,
                                                    id_personnel is null as est_personnel, num_etudiant is null as est_etudiant,
                                                    droit_creation_cours as peut_modifier_edt
                                                    from utilisateur
                                                    inner join droits using (nom_droits)
                                                    left join etudiant as et using(id_utilisateur) 
                                                    left join personnel using(id_utilisateur)
                                                    left join enseignant using(id_personnel) 
                                                    left join (
                                                        select num_etudiant, ref_semestre, max(date_debut)  from etudie_en
                                                        where etudie_en.num_etudiant in (
                                                            select num_etudiant from etudiant inner join utilisateur using(id_utilisateur)
                                                            where id_utilisateur = :id_utilisateur
                                                        )
                                                        group by num_etudiant, ref_semestre 
                                                    ) as t using(num_etudiant)
                                                    where utilisateur.id_utilisateur = :id_utilisateur
                                                    ';

        //Renvoie la liste des droits de l'utilisateur
        private static $userRoleQuery           = "select * from droits_utilisateur(:id_utilisateur)";

    
        //Insère un utilisateur dans la base de données
        private static $insertUserQuery         = 'insert into utilisateur values(
                                                    default, ?, ?, ?,
                                                    ?, ?, ?, ?, ?, ?,
                                                    ?,now(), 
                                                    ?, ?, ?)';

        //Modifie un utilisateur
        private static $modifyUserQuery         = "update utilisateur 
                                                    set mail_utilisateur = ?, nom_utilisateur = ?,
                                                    prenom_utilisateur = ?, tel_utilisateur = ?,
                                                    adresse_utilisateur = ?, genre = ?,
                                                    date_naissance_utilisateur = ?, nom_droits = ?,
                                                    code_pays = ?, code_postal_ville = ?
                                                    where id_utilisateur = ?";
       
        //Modifie le mot de passe d'un utilisateur                                            
        private static $modifyUserPasswordQuery = "update utilisateur set mot_de_passe_utilisateur = ? where id_utilisateur = ?";
    
    
    

        //Supprime un utilisateur, si c'est possible
        private static $deleteUserQuery         = "delete from utilisateur where id_utilisateur = :id_utilisateur";
    
        private $id_utilisateur;
        private $informations_simples;
        private $informations_utilisateur;
        private $droits;

    
        /*
         - Crée une instance d'un utilisateur déjà éxistant
         - @param id_utilisateur: L'identifiant de l'utilisateur éxistant
         - @param pseudo_utilisateur: Le pseudo de l'utilisateur éxistant
         - @throws PDOException : Si un problème se produit lors de l'éxecution de la requête
         - @throws ElementIntrouvable: Si l'utilisateur n'éxiste pas
        */
        public function __construct($id_utilisateur = "", $pseudo_utilisateur = ""){

            //Si l'identifiant n'est pas de type numérique alors il n'est pas valide
            //On se réfère donc au pseudo utilisateur.
            if(!is_numeric($id_utilisateur)){
                $id_utilisateur = self::pseudoEnIdUtilisateur($pseudo_utilisateur);
            }

            parent::__construct(self::$checkForUserQuery, array($id_utilisateur));
        
            $this->id_utilisateur = $id_utilisateur;
        }   


        /*
         - Récupère les informations de l'utilisateur
         - Les informations de l'utilisateurs sont stockés dans une variable d'instance
           pour qu'il soit possible de récupèrer l'éxception par le déclencheur 
           et ne plus avoir à requeté une deuxième fois. Cela évitera d'avoir des excéptions non gérées
         
         - @throws PDOException: Si les informations_utilisateur n'a jamais été lancée auparavant
           et qu'une erreur se produise lors de l'éxecution de la requête.
         - @throws NonAutoriseException: Si l'utilisateur actif n'a pas le droit de création des utilisateurs 
           et s'il veut consulter les droits d'un utilisateur autre que lui-même

         - Attention cette fonction permet de récupérer l'ensemble des informations relatives à l'utilisateur
           Il faudra filter les données après l'éxecution de cette requête.
        */
        public function informations_utilisateur(){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            //Déclenche une erreur si le droit n'est pas accordé
            if(!$utilisateur || $utilisateur->getIdUtilisateur() != $this->id_utilisateur)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');

            if(!$this->informations_utilisateur){
                $stmt = self::$db->prepare(self::$userQuery);
            
                $stmt->bindValue('id_utilisateur',$this->id_utilisateur);
                
                $stmt->execute();
                
                $this->informations_utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
            }
            
            return $this->informations_utilisateur;
        }

        /*
            -Récupère les informations non sensibles de l'utilisateur
            -@returns data: {
                id_utilisateur: integer,
                nom_utilisateur: string,
                pseudo_utilisateur: string,
                prenom_utilisateur: string,
                semestre: string,
                est_enseignant: boolean,
                est_personnel : boolean,
                est_etudiant : boolean
            }
            -@Throws PDOException: A la première éxecution
            -@Throws NonAutoriseException : Si l'utilisateur courant ne possède pas le droit de création des utilisateur
                et s'on cherche les informations d'un autre utilisateur.
        */
        public function informations_simples(){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            //Déclenche une erreur si le droit n'est pas accordé
            if(!$utilisateur || $utilisateur->getIdUtilisateur() != $this->id_utilisateur)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');

            if(!$this->informations_simples){
                $stmt = self::$db->prepare(self::$simpleUserDetails);
                $stmt->bindValue(':id_utilisateur', $this->id_utilisateur);
                $stmt->execute();
                $this->informations_simples = $stmt->fetch(PDO::FETCH_ASSOC);    
            }
            return $this->informations_simples;
        }

        /*
         - Récupère les droits de cet utilisateur. Ces droits inclus aussi 
           les droits du sous groupes auxquels il appartient.
         - @throws PDOException: Si une erreur se produit lors de l'éxecution de la requête
         - @throws NonAutoriseException: Si l'utilisateur actif n'a pas le droit de création des utilisateurs 
           et s'il veut consulter les droits d'un utilisateur autre que lui-même
         */
        public function droitsUtilisateur(){
            $utilisateur = Utilisateur::getUtilisateurCourant();
            //Déclenche une erreur si le droit n'est pas accordé
            if(!$utilisateur || $utilisateur->getIdUtilisateur() != $this->id_utilisateur)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');
        
            if(!$this->droits){
            
                $stmt = self::$db->prepare(self::$userRoleQuery);
            
                $stmt->bindValue(':id_utilisateur', $this->id_utilisateur);
            
                $stmt->execute();
            
                $this->droits = $stmt->fetch(PDO::FETCH_ASSOC);
            
            }

            return $this->droits;
        }


        /*
         - Modifie un utilisateur donnée.
         - @param data: Un tableau associative contenant l'ensemble des informations relatives à l'utilisateur
         data : {
             'email'            : string,
             'nom'              : string,
             'prenom'           : string,
             'tel'              : string,
             'adresse'          : string,
             'est_homme'        : boolean,
             'date_naissance'   : date,
             'droits'           : string (le nom du droits associés),
             'pays_naissance'   : string,
             'code_postal'      : string
         }

         - @throws PDOException : Si la modification de l'utilisateur a échouée
         - @throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de création utilisateurs
        */
        public function modifierUtilisateur($data)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            //La liste des clés obligatoires permettant de valider data
            $keyList = array('email','nom', 'prenom', 'tel', 'addresse', 'est_homme', 'date_naissance', 'droits', 'pays_naissance', 'code_postal');
            
            //On transforme le tableau associative en tableau normale (avec des indices numériques)
            $user = associativeToNumArray($keyList, $data);

            //On ajouter à la fin du tableau l'identifiant d'utilisateur (pour la condition where)
            array_push($user, $this->id_utilisateur);

            $stmt = self::$db->prepare(self::$modifyUserQuery);
            
            $stmt->execute($user);
        }


        /*
         - Modifie le mot de passe de l'utilisateur
         - @param mdp: Le nouveau mot de passe de l'utilisateur
         - @throws PDOException
         - @throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de création utilisateurs
           et s'il veut changer un mot de passe d'un autre utilisateur autre que lui-même

        */

        public function modifierMDPUtilisateur($mdp)
        {
            $utilisateur = Utilisateur::getUtilisateurCourant();
            //Déclenche une erreur si le droit n'est pas accordé
            if(!$utilisateur || $utilisateur->getIdUtilisateur() != $this->id_utilisateur)
                Utilisateur::possedeDroit('droit_creation_utilisateurs');


            $stmt = self::$db->prepare(self::$modifyUserPasswordQuery);

            $stmt->execute(array(crypt($mdp, SALT_KEY), $this->id_utilisateur));
        }



        /*
         - Supprime cet utilisateur
         - @throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de création utilisateurs
         - @throws PDOException: Si la suppression de cet utilisateur est impossible.
            Cela peut être dû à plusieurs raisons tels que : 
            -L'identifiant de l'utilisateur est déjà une clé étrangère dans une autre table
        */
        public function supprimerUtilisateur()
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            $stmt = self::$db->prepare(self::$deleteUserQuery);
            $stmt->bindValue(':id_utilisateur', $this->id_utilisateur);
            $stmt->execute();
        }


        /*
        GETTERS
        */
        public function getIdUtilisateur(){
            return $this->id_utilisateur;
        }








        /*
         - Renvoie la liste de tous les utilisateurs éxistants dans la base de données
         - Cette liste contient: 
                --L'identifiant de l'utilisateur
                --Pseudo de l'utilisateur
                --Nom de l'utilisateur
                --Prenom de l'utilisateur
                --N°Tel de l'utilisateur
                --Mail d'inscription de l'utilisateur
                --Date de naissance de l'utilisateur
          - @Throws PDOException

        */  

        public static function getListeUtilisateurs()
        {
            $stmt = self::$db->prepare(self::$userListQuery);
            
            $liste_utilisateurs = array();

            $stmt->execute();
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();  

            foreach ($resultat as $utilisateur) {
                array_push($liste_utilisateurs, array(
                    "id"=>$utilisateur['id_utilisateur'],
                    "pseudo"=>$utilisateur['pseudo_utilisateur'],
                    "nom"=>$utilisateur['nom_utilisateur'],
                    "prenom"=>$utilisateur['prenom_utilisateur'],
                    "tel"=>$utilisateur['tel_utilisateur'],
                    "mail"=>$utilisateur['mail_utilisateur'],
                    "date_naissance"=>$utilisateur['date_naissance_utilisateur']
                ));
            }

            return $liste_utilisateurs;
        }


        


        /*
         - Permet la création d'un nouveau utilisateur
         - @param 
            data : {
                'email'            : string,
                'nom'              : string,
                'prenom'           : string,
                'tel'              : string,
                'adresse'          : string,
                'est_homme'        : boolean,
                'date_naissance'   : date,
                'droits'           : string (le nom du droits associés),
                'pays_naissance'   : string,
                'code_postal'      : string
            }        
         - @throws PDOException 
         - @throws NonAutoriseException: Si l'utilisateur ne possède pas le droit de création utilisateurs

        */
        public static function ajouterUtilisateur($data)
        {
            Utilisateur::possedeDroit('droit_creation_utilisateurs');

            //L'ensemble des clés requises pour l'insertion d'un utilisateur dans la base de données
            $cle_requis = array('pseudo', 'email','nom', 'prenom', 'addresse', 'est_homme', 'tel', 'date_naissance', 'mot_de_passe', 'cle_recuperation_mdp','pays_naissance', 'droits', 'code_postal');
            
            //Définir ou changer quelques paramètres.
            $data["mot_de_passe"] = crypt($data['mot_de_passe'], SALT_KEY);
            $data['cle_recuperation_mdp'] = randomString(LONG_CLE_RECUPERATION);
            $data['pseudo'] = self::genererPseudo($data['nom'], $data['prenom']);

            //Transformer en un tableau normal
            $utilisateur = associativeToNumArray($cle_requis, $data);
            
            $stmt = self::$db->prepare(self::$insertUserQuery);
            
            $stmt->execute($utilisateur);
        }

        /*
         - Renvoie l'identifiant d'utilisateur correspondant au pseudo passé en paramètre
         - @param pseudo: Le pseudo de l'utilisateur 
         - @throws PDOException
        */
        private static function pseudoEnIdUtilisateur($pseudo)
        {
            $stmt = self::$db->prepare(self::$pseudoToIdQuery);

            $stmt->bindValue(":pseudo", $pseudo);

            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if($result != null)
                return $result['id_utilisateur'];
            else
                return null;
        }


        /*
         - Forme le pseudo de l'utilisateur
         - Le pseudo de l'utilisateur est soit constitué des premières lettre du prenom suivi du nom
            Soit on y ajouter un nombre aléatoire entre 0 et 400.
         - @param nom: Le nom de l'utilisateur
         - @param prenom: Le prenom de l'utilisateur
         - @throws PDOException

        */
        private static function genererPseudo($nom, $prenom)
        {
            //ON récupère toutes les parties du prénoms (séparées par un espace)
            $liste_mots = explode($prenom, " ");
            
            $pseudo = "";
            
            
            if (count($liste_mots) > 1) {//Si le prenom est constitué de plusieurs mots
                foreach ($liste_mots as $partie) {
                    $pseudo .= $partie[0];
                }
                $pseudo .= ".";
            } else {
                $pseudo .= $prenom[0];
            }

            $pseudo .= $nom;

            //On vérifie si un utilisateur porte déjà le même pseudo 
            $stmt = self::$db->prepare("select * from utilisateur where lower(pseudo_utilisateur) = lower(?)");
            
            $stmt->execute(array($pseudo));

            if (false !== $stmt->fetch(PDO::FETCH_NUM)) {
                return self::genererPseudo($nom . rand(0, 400), $prenom);
            }

            return strtolower($pseudo);
        }


        /*
         - Renvoie vrai si l'utilisateur est connecté
        */
        public static function estConnecte()
        {
            return isset($_SESSION['utilisateur_connecte']);
        }

        /*
         - Renvoie l'instance de l'utilisateur actif
        */
        public static function getUtilisateurCourant(){
            if(self::estConnecte()){
                return unserialize($_SESSION['utilisateur_connecte']);
            }
        }


        /*
            -Renvoie l'identifiant de l'utilisateur connecter
            -@Throws NonConnecterException : Si aucun utilisateur n'est connecter
        */
        public static function idUtilisateurCourant(){
            $utilisateur = self::getUtilisateurCourant();
            if($utilisateur){
                return $utilisateur->getIdUtilisateur();
            }else{
                throw new NonConnecterException();
            }
        }

        /*
         - Vérifie si l'utilisateur actif possède le droit nom_droit
         - @param nom_droit : Le nom du droit que l'on souhaite vérifier
         - @throws NonAutoriseException : Si le droit n'est pas accordé
        */
        public static function possedeDroit($nom_droit){
            $utilisateur = self::getUtilisateurCourant();

            $possede_droit = false;

            if($utilisateur){
              try{
                $liste_droits = $utilisateur->droitsUtilisateur();

                if(isset($liste_droits[$nom_droit])){
                    $possede_droit = $liste_droits[$nom_droit];
                }

            }catch(PDOException $e){}
            }

            if(!$possede_droit){
                throw new NonAutoriseException();
            }
        }
    }
