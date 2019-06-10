<?php
  require_once __DIR__ . "/../../verify.php";
  require_once __DIR__ . "/classe_generique.php";
  
  class Mail extends ClasseGenerique
  { 
        //Vérifie l'éxistance d'un mail
        private static $verifyMailQuery           = "select id_mail from mail
                                                        left join destinataire_utilisateur as u using(id_mail)
                                                        left join destinataire_groupe as g using(id_mail)
                                                        where id_mail = :id_mail 
                                                        and (
                                                            mail.id_utilisateur = :id_utilisateur or 
                                                            u.id_utilisateur = :id_utilisateur or
                                                            utilisateur_appartient_a_groupe(:id_utilisateur, g.id_groupe)
                                                        )
                                                        limit 1
                                                        ";

        //Récupère un mail
        private static $mailQuery                 = "select id_utilisateur, prenom_utilisateur, nom_utilisateur, pseudo_utilisateur, 
                                                     mail.*
                                                     from mail 
                                                     inner join utilisateur using (id_utilisateur) 
                                                     where id_mail = :id_mail";

        //Récupère les utilisateurs destinataires  du mail
        private static $utilisateursDestinataires = "select id_utilisateur, nom_utilisateur, prenom_utilisateur, pseudo_utilisateur
                                                        from destinataire_utilisateur
                                                        inner join utilisateur using(id_utilisateur) 
                                                        where id_mail = :id_mail";

        //Récupère les groupes destinataires du mail
        private static $groupesDestinataires      = "select id_groupe, nom_groupe
                                                        from destinataire_groupe
                                                        inner join groupe using(id_groupe) 
                                                        where id_mail = :id_mail";


        //Récupère la liste des mails recus
        private static $allMailsReceivedQuery     = "select distinct mail.*, 
                                                        utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.pseudo_utilisateur  
                                                        from mail 
                                                        left join destinataire_utilisateur using(id_mail)
                                                        left join destinataire_groupe using(id_mail)
                                                        left join utilisateur on(mail.id_utilisateur = utilisateur.id_utilisateur)
                                                        left join reponse_mail using(id_mail)
                                                        where
                                                        id_mail not in (
                                                            select id_mail from  mail_supprimes where id_utilisateur = :id_utilisateur
                                                        ) and (
                                                        destinataire_utilisateur.id_utilisateur = :id_utilisateur
                                                        or utilisateur_appartient_a_groupe(:id_utilisateur, id_groupe)
                                                        or (id_reponse_mail is not null and reponse_mail.id_utilisateur = :id_utilisateur)
                                                        )

                                                        order by date_envoi_mail desc
                                                        ";

        //Récupère la liste des mails envoyés
        private static $allMailsSentQuery         = "select mail.*, utilisateur.id_utilisateur,
                                                        utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur,
                                                        utilisateur.pseudo_utilisateur 
                                                        from mail
                                                        inner join utilisateur using(id_utilisateur)
                                                        where id_utilisateur = :id_utilisateur
                                                        order by date_envoi_mail desc
                                                        ";

        private static $requete_reponses_mail     = "select reponse_mail.*,
                                                        utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur,
                                                        utilisateur.pseudo_utilisateur
                                                        from reponse_mail
                                                        inner join utilisateur using(id_utilisateur)
                                                        where id_mail = :id_mail";
        
        //Crée un mail
        private static $createMailQuery           = "select envoyer_mail(
                                                        :sujet_mail,
                                                        :message_mail,
                                                        :lien_piece_jointe,
                                                        :nom_piece_jointe,
                                                        :id_expediteur
                                                    )";

        //Ajoute les utilisateurs detinataires du mail
        private static $addUserReceiverQuery      = "insert into destinataire_utilisateur values(
                                                        :id_utilisateur,
                                                        :id_mail
                                                    )";
        //Ajoute les groupes destinataires du mail
        private static $addGroupReceiverQuery     = "insert into destinataire_groupe values(
                                                        :id_groupe,
                                                        :id_mail
                                                    )";

        private static $requete_ajouter_reponse   = "insert into reponse_mail values(default, now(), :message, :id_mail, :id_utilisateur)";


        private static $addMailToDeletedQuery     = "insert into mail_supprimes values(:id_utilisateur, :id_mail)";
        //Supprime le mail courant
        private static $deleteMailQuery           = "delete from mail where id_mail = :id_mail";
        //Supprime les groupes destinataires
        private static $deleteMailGroupes         = "delete from destinataire_groupe where id_mail = :id_mail ";
        //Supprime les utilisateurs destinataires
        private static $deleteMailUsers           = "delete from destinataire_utilisateur where id_mail = :id_mail";

        private static $deleteMailReplyQuery      = "delete from reponse_mail where id_mail = :id_mail";
            
        private static $deleteDeletedMailQuery    = "delete from mail_supprimes where id_mail = :id_mail";

        private $id_utilisateur;

        private $id_mail;

        private $informations_mail;

        private $utilisateurs_destinataires;

        private $groupes_destinataires;

        private $reponses_mail;
        
        /*
            -Instancie un email.
            -@param id_mail : l'identifiant du mail
            -@Throws PDOException
            -@Throws ElementIntrouvable: Si aucun mail ne porte cet identifiant
        */
        public function __construct($id_mail)
        {            
            parent::__construct(self::$verifyMailQuery, array(
                ":id_mail"=>$id_mail, 
                ":id_utilisateur"=>Utilisateur::idUtilisateurCourant()));
            
            $this->id_utilisateur = Utilisateur::idUtilisateurCourant();

            $this->id_mail = $id_mail;
        }


        /*
            -Récupère les détails d'un mail
            -@returns data : {
                id_utilisateur : integer, 
                prenom_utilisateur: string,
                nom_utilisateur: string, 
                pseudo_utilisateur: string, 
                sujet_mail   : string, 
                pieces_jointe_mail: string,
                nom_piece_jointe: string,
                message_mail: string
            }
            -@Throws PDOException : Possible seulement si c'est le premier appel de la fonction
        */
        public function detailsMail()
        {
            if (!$this->informations_mail) {
                $stmt = self::$db->prepare(self::$mailQuery);

                $stmt->bindValue(":id_mail", $this->id_mail);

                $stmt->execute();

                $this->informations_mail = $stmt->fetch(PDO::FETCH_ASSOC);    

            }
            return $this->informations_mail;
        }

        /*
            -Renvoie la liste des utilisateurs destinataires de ce mail 
            -@return liste de {
                id_utilisateur: integer,
                nom_utilisateur: string,
                prenom_utilisateur: string,
                pseudo_utilisateur: string
            }
            -@Throws PDOException : Possible seulement si c'est le premier appel de la fonction
        */

        public function getUtilisateursDestinataire(){
            if (!$this->utilisateurs_destinataires) {
                $stmt = self::$db->prepare(self::$utilisateursDestinataires);

                $stmt->bindValue(":id_mail", $this->id_mail);

                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();    


                $this->utilisateurs_destinataires = $resultat;
            }
            return $this->utilisateurs_destinataires;
        }


        /*
            -Renvoie la liste des groupes destinataires du mail
            -@returns tableau de  :{
                id_groupe: integer,
                nom_groupe: string
            }
            -@Throws PDOException : Possible seulement si c'est le premier appel de la fonction
        */
        public function getGroupesDestinataires(){
            if (!$this->groupes_destinataires) {
                $stmt = self::$db->prepare(self::$groupesDestinataires);

                $stmt->bindValue(":id_mail", $this->id_mail);

                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();    

                $this->groupes_destinataires = $resultat;
            }
            return $this->groupes_destinataires;
        }

        public function getReponsesMail(){
            if(!$this->reponses_mail) {
                $stmt = self::$db->prepare(self::$requete_reponses_mail);

                $stmt->bindValue(":id_mail", $this->id_mail);

                $stmt->execute();

                $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $stmt->closeCursor();    

                $this->reponses_mail = $resultat;

            }
            return $this->reponses_mail;
        }

        public function ajouterReponse($message){
            $stmt = self::$db->prepare(self::$requete_ajouter_reponse);

            $stmt->bindValue(':message', $message);
            $stmt->bindValue(':id_mail', $this->id_mail);
            $stmt->bindValue(':id_utilisateur', $this->id_utilisateur);

            $stmt->execute();
        }

        /*
            -Ajoute les utilisateurs destinataires du mail
            -@param liste_utilisateurs: la liste des identifiants des utilisateurs à ajouter
            -@Throws PDOException
        */

        private function ajouterDestinataireUtilisateur($liste_utilisateurs){
            $stmt = self::$db->prepare(self::$addUserReceiverQuery);
            $stmt->bindValue(':id_mail', $this->id_mail);

            //BindParam crée une variable qui est attaché au paramètre id_utilisateur
            $stmt->bindParam(':id_utilisateur', $utilisateur);

            //On vérifie qu'on a bien une liste des utilisateurs
            if(is_array($liste_utilisateurs)){
                
                foreach($liste_utilisateurs as $id_utilisateur){
                    if(is_numeric($id_utilisateur)){
                        $utilisateur = $id_utilisateur;
                        $stmt->execute();    
                    }
                }

            }
        }


        /*
            -Ajoute les groupes destinataires du mail
            -@param liste_groupes: la liste des identifiants des groupes à ajouter
            -@Throws PDOException
        */
        private function ajouterDestinataireGroupe($liste_groupes){
            $stmt = self::$db->prepare(self::$addGroupReceiverQuery);
            $stmt->bindValue(':id_mail', $this->id_mail);

            //BindParam crée une variable qui est attaché au paramètre id_groupe
            $stmt->bindParam(':id_groupe', $groupe);

            //On vérifie qu'on a bien une liste des groupes
            if(is_array($liste_groupes)){
                
                foreach($liste_groupes as $id_groupe){
                    if(is_numeric($id_groupe)){
                        $groupe = $id_groupe;
                        $stmt->execute();    
                    }
                }

            }
        }



        /*
            -Supprime le mail courant
            -@Warning : Dès que le mail a été supprimé, tous les destinataires ne pourrant plus le consulter
            -@Throws PDOException
        */
        public function supprimerMail() {
            $id_expediteur = $this->detailsMail()["id_utilisateur"];
            if($id_expediteur === $this->id_utilisateur){
                self::$db->beginTransaction();

                $stmt1 = self::$db->prepare(self::$deleteMailGroupes); 
                $stmt2 = self::$db->prepare(self::$deleteMailUsers);
                $stmt3 = self::$db->prepare(self::$deleteMailReplyQuery);
                $stmt4 = self::$db->prepare(self::$deleteDeletedMailQuery);
                $stmt5 = self::$db->prepare(self::$deleteMailQuery);
    
                $stmt1->bindValue(':id_mail', $this->id_mail);
                $stmt2->bindValue(':id_mail', $this->id_mail);
                $stmt3->bindValue(':id_mail', $this->id_mail);
                $stmt4->bindValue(':id_mail', $this->id_mail);
                $stmt5->bindValue(':id_mail', $this->id_mail);

                $stmt1->execute();
                $stmt2->execute();
                $stmt3->execute();
                $stmt4->execute();
                $stmt5->execute();

                self::$db->commit();    
            }else{
                throw new NonAutoriseException("Non autorisé pour supprimer ce mail");
            }
            
        }

        public function cacherMail(){

            $stmt = self::$db->prepare(self::$addMailToDeletedQuery);
            $stmt->bindValue(":id_utilisateur", $this->id_utilisateur);
            $stmt->bindValue(":id_mail", $this->id_mail);
            $stmt->execute();
            
        }


        /*
            -Récupère la liste des mails reçus
            -@returns tableau de : {
                id_mail : integer,
                sujet_mail: string,
                message_mail: string,
                pieces_jointe_mail: string,
                date_envoi_mail : date,
                date_lecture_mail: date,
                id_utilisateur : integer,
                nom_utilisateur: string,
                prenom_utilisateur: string,
                pseudo_utilisateur: string             
            }
            -@Throws PDOException
            -@Throws NonConnecterException: Si aucun utilisateur n'est connecté
        */

        public static function liste_mails_recus() {
            $stmt = self::$db->prepare(self::$allMailsReceivedQuery);
    
            $stmt->bindValue(':id_utilisateur', Utilisateur::idUtilisateurCourant());

            $stmt->execute();
            
            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $resultat;    
        }

        /*
            -Récupère la liste des mails envoyés
            -@returns tableau de : {
                id_mail : integer,
                sujet_mail: string,
                message_mail: string,
                pieces_jointe_mail: string,
                date_envoi_mail : date,
                date_lecture_mail: date,
                id_utilisateur : integer,
            }
            -@Throws PDOException
            -@Throws NonConnecterException: Si aucun utilisateur n'est connecté
        */

        public static function liste_mails_envoyes() {
            $stmt = self::$db->prepare(self::$allMailsSentQuery);
        
            $stmt->bindValue(':id_utilisateur', Utilisateur::idUtilisateurCourant());

            $stmt->execute();

            $resultat = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt->closeCursor();

            return $resultat;
        }

        /*
            -Envoie un mail
            -@param id_expediteur integer:  Identifiant de l'utilisateur expediteur
            -@param sujet_mail string: Le sujet de l'émail
            -@param message_mail string: Le message du mail
            -@param lien_piece_jointe string: Le chemin vers la piece jointe
            -@param liste_utilisateurs_destinataires int[]: Tableau des identifiants des utilisateurs destinataires
            -@param liste_groupes_destinataire int[]: Tableau des identifiants des groupes destinataires
            
            -@Throws PDOException
            -@Throws NonConnecterException: Si aucun utilisateur n'est connecté
        */


        public static function envoyer_mail( $sujet_mail, $message_mail, $lien_piece_jointe, $nom_piece_jointe, $liste_utilisateurs_destinataire, $liste_groupes_destinataire)
        {
            /*
                Une enregistre le contenu du mail
            */
            self::$db->beginTransaction();

            $stmt = self::$db->prepare(self::$createMailQuery);

            $stmt->bindValue(':sujet_mail', $sujet_mail);
            $stmt->bindValue(':message_mail', $message_mail);
            $stmt->bindValue(':lien_piece_jointe', $lien_piece_jointe);
            $stmt->bindValue(':id_expediteur', Utilisateur::idUtilisateurCourant());
            $stmt->bindValue(':nom_piece_jointe', $nom_piece_jointe);
            
            $stmt->execute();

            $id_mail = $stmt->fetch(PDO::FETCH_NUM)[0];
            
            //La fonction envoyer mail retourne l'identifiant du nouveau mail crée
            $mail = new Mail($id_mail);
            
            $mail->ajouterDestinataireUtilisateur($liste_utilisateurs_destinataire);
            $mail->ajouterDestinataireGroupe($liste_groupes_destinataire);

            //On valide la transaction
            self::$db->commit();

        }


  }
