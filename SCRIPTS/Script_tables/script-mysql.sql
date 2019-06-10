#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: utilisateur
#------------------------------------------------------------

CREATE TABLE utilisateur(
        id_utilisateur               int (11) Auto_increment  NOT NULL ,
        pseudo_utilisateur           Varchar (50) NOT NULL ,
        mail_utilisateur             Varchar (150) NOT NULL ,
        nom_utilisateur              Varchar (30) NOT NULL ,
        prenom_utilisateur           Varchar (80) NOT NULL ,
        adresse_utilisateur          Varchar (150) NOT NULL ,
        est_homme_utilisateur        Bool NOT NULL ,
        tel_utilisateur              Varchar (10) NOT NULL ,
        date_naissance_utilisateur   Date NOT NULL ,
        mot_de_passe_utilisateur     Varchar (180) NOT NULL ,
        cle_recuperation_utilisateur Varchar (180) NOT NULL ,
        date_creation_utilisateur    Date NOT NULL ,
        code_pays                    Varchar (3) NOT NULL ,
        nom_droits                   Varchar (40) NOT NULL ,
        code_postal_ville            Varchar (5) ,
        PRIMARY KEY (id_utilisateur ) ,
        UNIQUE (pseudo_utilisateur ,mail_utilisateur )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: pays
#------------------------------------------------------------

CREATE TABLE pays(
        code_pays Varchar (3) NOT NULL ,
        nom_pays  Varchar (80) NOT NULL ,
        PRIMARY KEY (code_pays )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: droits
#------------------------------------------------------------

CREATE TABLE droits(
        nom_droits                        Varchar (40) NOT NULL ,
        droit_creation_utilisateurs       Bool NOT NULL ,
        droits_creation_modules           Bool NOT NULL ,
        droit_creation_cours              Bool NOT NULL ,
        droit_creation_groupes            Bool NOT NULL ,
        droit_modification_absences       Bool NOT NULL ,
        droit_modification_droits         Bool NOT NULL ,
        droit_modification_heures_travail Bool NOT NULL ,
        droit_visualisation_statistique   Bool NOT NULL ,
        PRIMARY KEY (nom_droits )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: groupe
#------------------------------------------------------------

CREATE TABLE groupe(
        id_groupe  int (11) Auto_increment  NOT NULL ,
        nom_groupe Varchar (50) NOT NULL ,
        nom_droits Varchar (40) NOT NULL ,
        PRIMARY KEY (id_groupe )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: etudiant
#------------------------------------------------------------

CREATE TABLE etudiant(
        num_etudiant   Varchar (25) NOT NULL ,
        id_utilisateur Int NOT NULL UNIQUE ,
        PRIMARY KEY (num_etudiant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: semestre
#------------------------------------------------------------

CREATE TABLE semestre(
        ref_semestre        Varchar (25) NOT NULL ,
        nom_semestre        Varchar (40) ,
        points_ets_semestre Int NOT NULL ,
        PRIMARY KEY (ref_semestre ) ,
        UNIQUE (nom_semestre )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: annee
#------------------------------------------------------------

CREATE TABLE annee(
        annee Int NOT NULL ,
        PRIMARY KEY (annee )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: ville
#------------------------------------------------------------

CREATE TABLE ville(
        code_postal_ville Varchar (5) NOT NULL ,
        nom_ville         Varchar (50) NOT NULL,
        PRIMARY KEY (code_postal_ville )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: personnel
#------------------------------------------------------------

CREATE TABLE personnel(
        id_personnel   int (11) Auto_increment  NOT NULL ,
        id_utilisateur Int NOT NULL UNIQUE,
        PRIMARY KEY (id_personnel )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: enseignant
#------------------------------------------------------------

CREATE TABLE enseignant(
        id_enseignant int (11) Auto_increment  NOT NULL ,
        id_personnel int (11) NOT NULL UNIQUE,
        PRIMARY KEY (id_enseignant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: cours
#------------------------------------------------------------

CREATE TABLE cours(
        id_cours              int (11) Auto_increment  NOT NULL ,
        nom_cours             Varchar (80) NOT NULL ,
        lien_fichier_cours    Varchar (150) NOT NULL ,
        date_depot_cours      Date NOT NULL ,
        date_ouverture_cours  Date ,
        cours_est_cachee      Bool NOT NULL ,
        nb_consultation_cours Int NOT NULL ,
        id_enseignant         Int NOT NULL ,
        ref_module            Varchar (80) NOT NULL,
        PRIMARY KEY (id_cours )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: module
#------------------------------------------------------------

CREATE TABLE module(
        ref_module         Varchar (80) NOT NULL ,
        nom_module         Varchar (140) NOT NULL ,
        coefficient_module Float NOT NULL ,
        heures_cm_module   Float NOT NULL ,
        heures_tp_module   Int NOT NULL ,
        heures_td_module   Int NOT NULL ,
        couleur_module     Varchar (7) NOT NULL ,
        ref_semestre       Varchar (25) NOT NULL ,
        PRIMARY KEY (ref_module )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: jours_ouverture
#------------------------------------------------------------

CREATE TABLE jours_ouverture(
        id_jours_ouverture int (11) Auto_increment  NOT NULL ,
        jour_ouverture     Int NOT NULL ,
        mois_ouverture     Int NOT NULL ,
        PRIMARY KEY (id_jours_ouverture )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: seance
#------------------------------------------------------------

CREATE TABLE seance(
        id_seance           int (11) Auto_increment  NOT NULL ,
        heure_depart_seance Time NOT NULL ,
        duree_seance        Time NOT NULL ,
        id_jours_ouverture  int NOT NULL,
        ref_module          Varchar (80) ,
        nom_type_seance     Varchar (50) NOT NULL ,
        PRIMARY KEY (id_seance )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: type_seance
#------------------------------------------------------------

CREATE TABLE type_seance(
        nom_type_seance Varchar (50) NOT NULL ,
        couleur_seance  Varchar (7) ,
        PRIMARY KEY (nom_type_seance )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: salle
#------------------------------------------------------------

CREATE TABLE salle(
        nom_salle      Varchar (8) NOT NULL ,
        nom_type_salle Varchar (25) NOT NULL,
        PRIMARY KEY (nom_salle )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: type_salle
#------------------------------------------------------------

CREATE TABLE type_salle(
        nom_type_salle Varchar (25) NOT NULL ,
        PRIMARY KEY (nom_type_salle )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: depot_exercice
#------------------------------------------------------------

CREATE TABLE depot_exercice(
        id_depot_exercice             int (11) Auto_increment  NOT NULL ,
        date_debut_depot_exercice     Datetime ,
        date_fermeture_depot_exercice Datetime NOT NULL ,
        lien_depot_exercice           TinyText ,
        id_cours                      Int ,
        id_depot_note                 Int ,
        PRIMARY KEY (id_depot_exercice )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: depot_exercice_notee
#------------------------------------------------------------

CREATE TABLE depot_exercice_notee(
        id_depot_note     int (11) Auto_increment  NOT NULL ,
        coefficient_depot Float NOT NULL ,
        id_depot_exercice Int NOT NULL ,
        PRIMARY KEY (id_depot_note )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: controle
#------------------------------------------------------------

CREATE TABLE controle(
        id_controle          int (11) Auto_increment  NOT NULL ,
        coefficient_controle Float NOT NULL ,
        nom_controle         Varchar (150) ,
        date_controle        Datetime NOT NULL ,
        date_note_controle   Date NOT NULL ,
        ref_module           Varchar (80) ,
        PRIMARY KEY (id_controle )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: mail
#------------------------------------------------------------

CREATE TABLE mail(
        id_mail            int (11) Auto_increment  NOT NULL ,
        sujet_mail         Varchar (300) NOT NULL ,
        message_mail       Text NOT NULL ,
        pieces_jointe_mail TinyText ,
        date_envoi_mail    Datetime NOT NULL ,
        date_lecture_mail  Datetime ,
        id_utilisateur     Int ,
        PRIMARY KEY (id_mail )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: reponse_mail
#------------------------------------------------------------

CREATE TABLE reponse_mail(
        id_reponse_mail   int (11) Auto_increment  NOT NULL ,
        date_reponse_mail Datetime NOT NULL ,
        contenu_reponse   Text NOT NULL ,
        id_mail           Int ,
        PRIMARY KEY (id_reponse_mail )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: membres_de_groupe
#------------------------------------------------------------

CREATE TABLE membres_de_groupe(
        id_groupe      Int NOT NULL ,
        id_utilisateur Int NOT NULL ,
        PRIMARY KEY (id_groupe ,id_utilisateur )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: sous_groupe
#------------------------------------------------------------

CREATE TABLE sous_groupe(
        id_groupe_parent   Int NOT NULL ,
        id_groupe_enfant Int NOT NULL ,
        PRIMARY KEY (id_groupe_parent ,id_groupe_enfant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: etudie_en
#------------------------------------------------------------

CREATE TABLE etudie_en(
        moyenne      Float NOT NULL ,
        est_valide   Bool NOT NULL ,
        annee        Int NOT NULL ,
        num_etudiant Varchar (25) NOT NULL ,
        ref_semestre Varchar (25) NOT NULL ,
        PRIMARY KEY (annee ,num_etudiant ,ref_semestre )
)ENGINE=InnoDB;



#------------------------------------------------------------
# Table: heures_travail
#------------------------------------------------------------

CREATE TABLE heures_travail(
        heures_travail Int NOT NULL ,
        annee          Int NOT NULL ,
        id_personnel   Int NOT NULL ,
        PRIMARY KEY (annee ,id_personnel )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: module_enseigne_par
#------------------------------------------------------------

CREATE TABLE module_enseigne_par(
        id_enseignant Int NOT NULL ,
        ref_module    Varchar (80) NOT NULL ,
        PRIMARY KEY (id_enseignant ,ref_module )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: commentaire_cours
#------------------------------------------------------------

CREATE TABLE commentaire_cours(
        commentaire_cours Text ,
        avis_cours        Int ,
        id_cours          Int NOT NULL ,
        num_etudiant      Varchar (25) NOT NULL ,
        PRIMARY KEY (id_cours ,num_etudiant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: commentaire_module
#------------------------------------------------------------

CREATE TABLE commentaire_module(
        commentaire_module Text ,
        avis_module        Int ,
        num_etudiant       Varchar (25) NOT NULL ,
        ref_module         Varchar (80) NOT NULL ,
        PRIMARY KEY (num_etudiant ,ref_module )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: groupe_seance
#------------------------------------------------------------

CREATE TABLE groupe_seance(
        id_groupe Int NOT NULL ,
        id_seance Int NOT NULL ,
        PRIMARY KEY (id_groupe ,id_seance )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: seance_se_deroule_dans
#------------------------------------------------------------

CREATE TABLE seance_se_deroule_dans(
        id_seance Int NOT NULL ,
        nom_salle Varchar (8) NOT NULL ,
        PRIMARY KEY (id_seance ,nom_salle )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: depot_concerne_groupe
#------------------------------------------------------------

CREATE TABLE depot_concerne_groupe(
        id_depot_exercice Int NOT NULL ,
        id_groupe         Int NOT NULL ,
        PRIMARY KEY (id_depot_exercice ,id_groupe )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: enseignant_note_depot
#------------------------------------------------------------

CREATE TABLE enseignant_note_depot(
        note          Float ,
        id_enseignant Int NOT NULL ,
        id_depot_note Int NOT NULL ,
        num_etudiant  Varchar (25) NOT NULL ,
        PRIMARY KEY (id_enseignant ,id_depot_note ,num_etudiant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: enseignant_commente_depot
#------------------------------------------------------------

CREATE TABLE enseignant_commente_depot(
        commentaire       Text ,
        num_etudiant      Varchar (25) NOT NULL ,
        id_depot_exercice Int NOT NULL ,
        id_enseignant     Int NOT NULL ,
        PRIMARY KEY (num_etudiant ,id_depot_exercice ,id_enseignant )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: depot_etudiant
#------------------------------------------------------------

CREATE TABLE depot_etudiant(
        commentaire_depot_etudiant Text NOT NULL ,
        date_depot_etudiant        Date ,
        lien_depot_etudiant        Varchar (180) ,
        num_etudiant               Varchar (25) NOT NULL ,
        id_depot_exercice          Int NOT NULL ,
        PRIMARY KEY (num_etudiant ,id_depot_exercice )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: notes_controle
#------------------------------------------------------------

CREATE TABLE notes_controle(
        note_controle        Float NOT NULL ,
        commentaire_controle Text ,
        num_etudiant         Varchar (25) NOT NULL ,
        id_controle          Int NOT NULL ,
        PRIMARY KEY (num_etudiant ,id_controle )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: destinataire_mail
#------------------------------------------------------------

CREATE TABLE destinataire_mail(
        id_utilisateur Int NOT NULL ,
        id_mail        Int NOT NULL ,
        id_groupe      Int NOT NULL ,
        PRIMARY KEY (id_utilisateur ,id_mail ,id_groupe )
)ENGINE=InnoDB;

ALTER TABLE utilisateur ADD CONSTRAINT FK_utilisateur_code_pays FOREIGN KEY (code_pays) REFERENCES pays(code_pays);
ALTER TABLE utilisateur ADD CONSTRAINT FK_utilisateur_nom_droits FOREIGN KEY (nom_droits) REFERENCES droits(nom_droits);
ALTER TABLE utilisateur ADD CONSTRAINT FK_utilisateur_code_postal_ville FOREIGN KEY (code_postal_ville) REFERENCES ville(code_postal_ville);
ALTER TABLE groupe ADD CONSTRAINT FK_groupe_nom_droits FOREIGN KEY (nom_droits) REFERENCES droits(nom_droits);
ALTER TABLE etudiant ADD CONSTRAINT FK_etudiant_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur);
ALTER TABLE personnel ADD CONSTRAINT FK_personnel_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur);
ALTER TABLE cours ADD CONSTRAINT FK_cours_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant);
ALTER TABLE cours ADD CONSTRAINT FK_cours_ref_module FOREIGN KEY (ref_module) REFERENCES module(ref_module);
ALTER TABLE module ADD CONSTRAINT FK_module_ref_semestre FOREIGN KEY (ref_semestre) REFERENCES semestre(ref_semestre);
ALTER TABLE seance ADD CONSTRAINT FK_seance_id_jours_ouverture FOREIGN KEY (id_jours_ouverture) REFERENCES jours_ouverture(id_jours_ouverture);
ALTER TABLE seance ADD CONSTRAINT FK_seance_ref_module FOREIGN KEY (ref_module) REFERENCES module(ref_module);
ALTER TABLE seance ADD CONSTRAINT FK_seance_nom_type_seance FOREIGN KEY (nom_type_seance) REFERENCES type_seance(nom_type_seance);
ALTER TABLE salle ADD CONSTRAINT FK_salle_nom_type_salle FOREIGN KEY (nom_type_salle) REFERENCES type_salle(nom_type_salle);
ALTER TABLE depot_exercice ADD CONSTRAINT FK_depot_exercice_id_cours FOREIGN KEY (id_cours) REFERENCES cours(id_cours);
ALTER TABLE depot_exercice ADD CONSTRAINT FK_depot_exercice_id_depot_note FOREIGN KEY (id_depot_note) REFERENCES depot_exercice_notee(id_depot_note);
ALTER TABLE depot_exercice_notee ADD CONSTRAINT FK_depot_exercice_notee_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES depot_exercice(id_depot_exercice);
ALTER TABLE controle ADD CONSTRAINT FK_controle_ref_module FOREIGN KEY (ref_module) REFERENCES module(ref_module);
ALTER TABLE mail ADD CONSTRAINT FK_mail_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur);
ALTER TABLE reponse_mail ADD CONSTRAINT FK_reponse_mail_id_mail FOREIGN KEY (id_mail) REFERENCES mail(id_mail);
ALTER TABLE membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_id_groupe FOREIGN KEY (id_groupe) REFERENCES groupe(id_groupe);
ALTER TABLE membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur);
ALTER TABLE sous_groupe ADD CONSTRAINT FK_sous_groupe_id_groupe FOREIGN KEY (id_groupe_parent) REFERENCES groupe(id_groupe);
ALTER TABLE sous_groupe ADD CONSTRAINT FK_sous_groupe_id_groupe_1 FOREIGN KEY (id_groupe_enfant) REFERENCES groupe(id_groupe);
ALTER TABLE etudie_en ADD CONSTRAINT FK_etudie_en_annee FOREIGN KEY (annee) REFERENCES annee(annee);
ALTER TABLE etudie_en ADD CONSTRAINT FK_etudie_en_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE etudie_en ADD CONSTRAINT FK_etudie_en_ref_semestre FOREIGN KEY (ref_semestre) REFERENCES semestre(ref_semestre);
ALTER TABLE enseignant ADD CONSTRAINT FK_enseignant_est_personnel_id_enseignant FOREIGN KEY (id_personnel) REFERENCES personnel(id_personnel);
ALTER TABLE heures_travail ADD CONSTRAINT FK_heures_travail_annee FOREIGN KEY (annee) REFERENCES annee(annee);
ALTER TABLE heures_travail ADD CONSTRAINT FK_heures_travail_id_personnel FOREIGN KEY (id_personnel) REFERENCES personnel(id_personnel);
ALTER TABLE module_enseigne_par ADD CONSTRAINT FK_module_enseigne_par_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant);
ALTER TABLE module_enseigne_par ADD CONSTRAINT FK_module_enseigne_par_ref_module FOREIGN KEY (ref_module) REFERENCES module(ref_module);
ALTER TABLE commentaire_cours ADD CONSTRAINT FK_commentaire_cours_id_cours FOREIGN KEY (id_cours) REFERENCES cours(id_cours);
ALTER TABLE commentaire_cours ADD CONSTRAINT FK_commentaire_cours_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE commentaire_module ADD CONSTRAINT FK_commentaire_module_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE commentaire_module ADD CONSTRAINT FK_commentaire_module_ref_module FOREIGN KEY (ref_module) REFERENCES module(ref_module);
ALTER TABLE groupe_seance ADD CONSTRAINT FK_groupe_seance_id_groupe FOREIGN KEY (id_groupe) REFERENCES groupe(id_groupe);
ALTER TABLE groupe_seance ADD CONSTRAINT FK_groupe_seance_id_seance FOREIGN KEY (id_seance) REFERENCES seance(id_seance);
ALTER TABLE seance_se_deroule_dans ADD CONSTRAINT FK_seance_se_deroule_dans_id_seance FOREIGN KEY (id_seance) REFERENCES seance(id_seance);
ALTER TABLE seance_se_deroule_dans ADD CONSTRAINT FK_seance_se_deroule_dans_nom_salle FOREIGN KEY (nom_salle) REFERENCES salle(nom_salle);
ALTER TABLE depot_concerne_groupe ADD CONSTRAINT FK_depot_concerne_groupe_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES depot_exercice(id_depot_exercice);
ALTER TABLE depot_concerne_groupe ADD CONSTRAINT FK_depot_concerne_groupe_id_groupe FOREIGN KEY (id_groupe) REFERENCES groupe(id_groupe);
ALTER TABLE enseignant_note_depot ADD CONSTRAINT FK_enseignant_note_depot_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant);
ALTER TABLE enseignant_note_depot ADD CONSTRAINT FK_enseignant_note_depot_id_depot_note FOREIGN KEY (id_depot_note) REFERENCES depot_exercice_notee(id_depot_note);
ALTER TABLE enseignant_note_depot ADD CONSTRAINT FK_enseignant_note_depot_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES depot_exercice(id_depot_exercice);
ALTER TABLE enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES enseignant(id_enseignant);
ALTER TABLE depot_etudiant ADD CONSTRAINT FK_depot_etudiant_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE depot_etudiant ADD CONSTRAINT FK_depot_etudiant_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES depot_exercice(id_depot_exercice);
ALTER TABLE notes_controle ADD CONSTRAINT FK_notes_controle_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES etudiant(num_etudiant);
ALTER TABLE notes_controle ADD CONSTRAINT FK_notes_controle_id_controle FOREIGN KEY (id_controle) REFERENCES controle(id_controle);
ALTER TABLE destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur);
ALTER TABLE destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_mail FOREIGN KEY (id_mail) REFERENCES mail(id_mail);
ALTER TABLE destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_groupe FOREIGN KEY (id_groupe) REFERENCES groupe(id_groupe);
ALTER TABLE `etudie_en` CHANGE `moyenne` `moyenne` FLOAT NULL;

