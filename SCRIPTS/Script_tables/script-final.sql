------------------------------------------------------------
--        Script Postgre 
------------------------------------------------------------



------------------------------------------------------------
-- Table: utilisateur
------------------------------------------------------------
CREATE TABLE public.utilisateur(
	id_utilisateur               SERIAL NOT NULL ,
	pseudo_utilisateur           VARCHAR (50) NOT NULL UNIQUE,
	mail_utilisateur             VARCHAR (150) NOT NULL UNIQUE,
	nom_utilisateur              VARCHAR (30) NOT NULL ,
	prenom_utilisateur           VARCHAR (80) NOT NULL ,
	adresse_utilisateur          VARCHAR (150) NOT NULL ,
	genre                        BOOL  NOT NULL ,
	tel_utilisateur              VARCHAR (10) NOT NULL ,
	date_naissance_utilisateur   DATE  NOT NULL ,
	mot_de_passe_utilisateur     VARCHAR (180) NOT NULL ,
	cle_recuperation_utilisateur VARCHAR (180) NOT NULL ,
	date_creation_utilisateur    DATE  NOT NULL default now(),
	code_pays                    VARCHAR (3) NOT NULL ,
	nom_droits                   VARCHAR (40) NOT NULL ,
	code_postal_ville            VARCHAR (5)  NOT NULL,
	CONSTRAINT prk_constraint_utilisateur PRIMARY KEY (id_utilisateur)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: pays
------------------------------------------------------------
CREATE TABLE public.pays(
	code_pays VARCHAR (3) NOT NULL ,
	nom_pays  VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_pays PRIMARY KEY (code_pays)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: droits
------------------------------------------------------------
CREATE TABLE public.droits(
	nom_droits                        VARCHAR (40) NOT NULL ,
	droit_creation_utilisateurs       BOOL  NOT NULL ,
	droits_creation_modules           BOOL  NOT NULL ,
	droit_creation_cours              BOOL  NOT NULL ,
	droit_creation_groupes            BOOL  NOT NULL ,
	droit_modification_absences       BOOL  NOT NULL ,
	droit_modification_droits         BOOL  NOT NULL ,
	droit_modification_heures_travail BOOL  NOT NULL ,
	droit_visualisation_statistique   BOOL  NOT NULL ,
	CONSTRAINT prk_constraint_droits PRIMARY KEY (nom_droits)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: groupe
------------------------------------------------------------
CREATE TABLE public.groupe(
	id_groupe  SERIAL NOT NULL ,
	nom_groupe VARCHAR (50) NOT NULL ,
	nom_droits VARCHAR (40) NOT NULL ,
	CONSTRAINT prk_constraint_groupe PRIMARY KEY (id_groupe)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: etudiant
------------------------------------------------------------
CREATE TABLE public.etudiant(
	num_etudiant   VARCHAR (25) NOT NULL ,
	id_utilisateur INT UNIQUE NOT NULL,
	CONSTRAINT prk_constraint_etudiant PRIMARY KEY (num_etudiant)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: semestre
------------------------------------------------------------
CREATE TABLE public.semestre(
	ref_semestre        VARCHAR (25) NOT NULL ,
	nom_semestre        VARCHAR (40) NOT NULL UNIQUE,
	points_ets_semestre INT  NOT NULL check (points_ets_semestre > 0),
	periode_semestre    INT  NOT NULL check (periode_semestre in (1, 2)),
	CONSTRAINT prk_constraint_semestre PRIMARY KEY (ref_semestre)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: periode_semestre
------------------------------------------------------------
CREATE TABLE public.periode_semestre(
	date_debut DATE  NOT NULL ,
	date_fin   DATE  NOT NULL UNIQUE,
	CONSTRAINT prk_constraint_periode_semestre PRIMARY KEY (date_debut)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: ville
------------------------------------------------------------
CREATE TABLE public.ville(
	code_postal_ville VARCHAR (5) NOT NULL ,
	nom_ville         VARCHAR (50)  ,
	CONSTRAINT prk_constraint_ville PRIMARY KEY (code_postal_ville)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: personnel
------------------------------------------------------------
CREATE TABLE public.personnel(
	id_personnel   SERIAL NOT NULL ,
	id_utilisateur INT  UNIQUE NOT NULL ,
	CONSTRAINT prk_constraint_personnel PRIMARY KEY (id_personnel)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: enseignant
------------------------------------------------------------
CREATE TABLE public.enseignant(
	id_enseignant SERIAL NOT NULL ,
	id_personnel INT UNIQUE NOT NULL REFERENCES public.personnel(id_personnel),
	CONSTRAINT prk_constraint_enseignant PRIMARY KEY (id_enseignant)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: support_pedagogique
------------------------------------------------------------
CREATE TABLE public.support_pedagogique(
	id_support              SERIAL NOT NULL ,
	nom_support             VARCHAR (150) NOT NULL ,
	lien_fichier_support    VARCHAR (150) NOT NULL ,
	date_depot_support      DATE  NOT NULL ,
	date_ouverture_support  DATE   ,
	support_est_cachee      BOOL  NOT NULL ,
	nb_consultation_support INT  NOT NULL ,
	id_enseignant           INT  NOT NULL ,
	ref_module              VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_support_pedagogique PRIMARY KEY (id_support)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: module
------------------------------------------------------------
CREATE TABLE public.module(
	ref_module         VARCHAR (80) NOT NULL ,
	nom_module         VARCHAR (140) NOT NULL ,
	coefficient_module FLOAT  NOT NULL ,
	heures_cm_module   INT  NOT NULL ,
	heures_tp_module   INT  NOT NULL ,
	heures_td_module   INT  NOT NULL ,
	couleur_module     VARCHAR (7) NOT NULL ,
	ref_semestre       VARCHAR (25) NOT NULL ,
	CONSTRAINT prk_constraint_module PRIMARY KEY (ref_module)
)WITHOUT OIDS;



------------------------------------------------------------
-- Table: seance
------------------------------------------------------------
CREATE TABLE public.seance(
	id_seance           SERIAL NOT NULL ,
	heure_depart_seance TIMETZ  NOT NULL ,
	duree_seance        TIMETZ  NOT NULL ,
	ref_module          VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_seance PRIMARY KEY (id_seance)
)WITHOUT OIDS;





------------------------------------------------------------
-- Table: salle
------------------------------------------------------------
CREATE TABLE public.salle(
	nom_salle                 VARCHAR (8) NOT NULL ,
	nombre_ordinateurs_salle  INT   ,
	nombre_places_salle       INT   ,
	contient_projecteur_salle BOOL   ,
	CONSTRAINT prk_constraint_salle PRIMARY KEY (nom_salle)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: depot_exercice
------------------------------------------------------------
CREATE TABLE public.depot_exercice(
	id_depot_exercice             SERIAL NOT NULL ,
	date_debut_depot_exercice     DATE DEFAULT NOW()  ,
	date_fermeture_depot_exercice DATE  NOT NULL ,
	coefficient_depot             FLOAT   ,
	id_support                    INT   ,
	CONSTRAINT prk_constraint_depot_exercice PRIMARY KEY (id_depot_exercice)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: controle
------------------------------------------------------------
CREATE TABLE public.controle(
	id_controle          SERIAL NOT NULL ,
	coefficient_controle FLOAT  NOT NULL ,
	nom_controle         VARCHAR (150)  ,
	date_controle        DATE  NOT NULL ,
	ref_module           VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_controle PRIMARY KEY (id_controle)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: mail
------------------------------------------------------------
CREATE TABLE public.mail(
	id_mail            SERIAL NOT NULL ,
	sujet_mail         VARCHAR (300) NOT NULL ,
	message_mail       VARCHAR (2000)  NOT NULL ,
	pieces_jointe_mail VARCHAR (2000)   ,
	date_envoi_mail    DATE  NOT NULL ,
	date_lecture_mail  DATE   ,
	id_utilisateur     INT NOT NULL ,
	CONSTRAINT prk_constraint_mail PRIMARY KEY (id_mail)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: reponse_mail
------------------------------------------------------------
CREATE TABLE public.reponse_mail(
	id_reponse_mail   SERIAL NOT NULL ,
	date_reponse_mail DATE  NOT NULL ,
	contenu_reponse   VARCHAR (2000)  NOT NULL ,
	id_mail           INT  NOT NULL ,
	CONSTRAINT prk_constraint_reponse_mail PRIMARY KEY (id_reponse_mail)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: membres_de_groupe
------------------------------------------------------------
CREATE TABLE public.membres_de_groupe(
	id_groupe      INT  NOT NULL ,
	id_utilisateur INT  NOT NULL ,
	date_debut     DATE  NOT NULL ,
	date_fin       DATE  NOT NULL ,
	CONSTRAINT prk_constraint_membres_de_groupe PRIMARY KEY (id_utilisateur,date_debut,date_fin, id_groupe)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: sous_groupe
------------------------------------------------------------
CREATE TABLE public.sous_groupe(
	id_groupe_parent INT  NOT NULL ,
	id_groupe_fils   INT  NOT NULL ,
	CONSTRAINT prk_constraint_sous_groupe PRIMARY KEY (id_groupe_parent,id_groupe_fils)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: etudie_en
------------------------------------------------------------
CREATE TABLE public.etudie_en(
	moyenne      FLOAT  default null ,
	est_valide   BOOL  default false,
	date_debut   DATE  NOT NULL ,
	date_fin     DATE  NOT NULL ,
	num_etudiant VARCHAR (25) NOT NULL ,
	ref_semestre VARCHAR (25) NOT NULL ,
	CONSTRAINT prk_constraint_etudie_en PRIMARY KEY (date_debut,date_fin,num_etudiant)
)WITHOUT OIDS;



------------------------------------------------------------
-- Table: heures_travail
------------------------------------------------------------
CREATE TABLE public.heures_travail(
	heures_travail INT  NOT NULL ,
	date_debut     DATE  NOT NULL ,
	date_fin       DATE  NOT NULL ,
	id_personnel   INT  NOT NULL ,
	CONSTRAINT prk_constraint_heures_travail PRIMARY KEY (date_debut,date_fin,id_personnel)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: module_enseigne_par
------------------------------------------------------------
CREATE TABLE public.module_enseigne_par(
	est_responsable BOOL  NOT NULL ,
	id_enseignant   INT  NOT NULL ,
	ref_module      VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_module_enseigne_par PRIMARY KEY (id_enseignant,ref_module)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: commentaire_cours
------------------------------------------------------------
CREATE TABLE public.commentaire_cours(
	commentaire_cours VARCHAR (2000)   ,
	avis_cours        INT   ,
	id_support_cours        INT  NOT NULL ,
	num_etudiant      VARCHAR (25) NOT NULL ,
	CONSTRAINT prk_constraint_commentaire_cours PRIMARY KEY (id_support_cours,num_etudiant)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: commentaire_module
------------------------------------------------------------
CREATE TABLE public.commentaire_module(
	commentaire_module VARCHAR (2000)   ,
	avis_module        INT   ,
	num_etudiant       VARCHAR (25) NOT NULL ,
	ref_module         VARCHAR (80) NOT NULL ,
	CONSTRAINT prk_constraint_commentaire_module PRIMARY KEY (num_etudiant,ref_module)
)WITHOUT OIDS;



------------------------------------------------------------
-- Table: depot_concerne_groupe
------------------------------------------------------------
CREATE TABLE public.depot_concerne_groupe(
	id_depot_exercice INT  NOT NULL ,
	id_groupe         INT  NOT NULL ,
	CONSTRAINT prk_constraint_depot_concerne_groupe PRIMARY KEY (id_depot_exercice,id_groupe)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: enseignant_commente_depot
------------------------------------------------------------
CREATE TABLE public.enseignant_commente_depot(
	commentaire_depot VARCHAR (2000)   ,
	note_depot        FLOAT   ,
	num_etudiant      VARCHAR (25) NOT NULL ,
	id_depot_exercice INT  NOT NULL ,
	id_enseignant     INT  NOT NULL ,
	CONSTRAINT prk_constraint_enseignant_commente_depot PRIMARY KEY (num_etudiant,id_depot_exercice,id_enseignant)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: depot_etudiant
------------------------------------------------------------
CREATE TABLE public.depot_etudiant(
	commentaire_depot_etudiant VARCHAR (2000)  NOT NULL ,
	date_depot_etudiant        DATE   ,
	lien_depot_etudiant        VARCHAR (180)  ,
	num_etudiant               VARCHAR (25) NOT NULL ,
	id_depot_exercice          INT  NOT NULL ,
	CONSTRAINT prk_constraint_depot_etudiant PRIMARY KEY (num_etudiant,id_depot_exercice)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: notes_controle
------------------------------------------------------------
CREATE TABLE public.notes_controle(
	note_controle        FLOAT  NOT NULL ,
	commentaire_controle VARCHAR (2000)   ,
	num_etudiant         VARCHAR (25) NOT NULL ,
	id_controle          INT  NOT NULL ,
	CONSTRAINT prk_constraint_notes_controle PRIMARY KEY (num_etudiant,id_controle)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: destinataire_mail
------------------------------------------------------------
CREATE TABLE public.destinataire_mail(
	id_utilisateur INT  NOT NULL ,
	id_mail        INT  NOT NULL ,
	id_groupe      INT  NOT NULL ,
	CONSTRAINT prk_constraint_destinataire_mail PRIMARY KEY (id_utilisateur,id_mail,id_groupe)
)WITHOUT OIDS;


------------------------------------------------------------
-- Table: etudiant_absent
------------------------------------------------------------
CREATE TABLE public.etudiant_absent(
	absence_est_justifiee BOOL  NOT NULL ,
	commentaire_absence   VARCHAR (2000)   ,
	num_etudiant          VARCHAR (25) NOT NULL ,
	id_seance             INT  NOT NULL ,
	CONSTRAINT prk_constraint_etudiant_absent PRIMARY KEY (num_etudiant,id_seance)
)WITHOUT OIDS;



ALTER TABLE public.utilisateur ADD CONSTRAINT FK_utilisateur_code_pays FOREIGN KEY (code_pays) REFERENCES public.pays(code_pays);
ALTER TABLE public.utilisateur ADD CONSTRAINT FK_utilisateur_nom_droits FOREIGN KEY (nom_droits) REFERENCES public.droits(nom_droits);
ALTER TABLE public.utilisateur ADD CONSTRAINT FK_utilisateur_code_postal_ville FOREIGN KEY (code_postal_ville) REFERENCES public.ville(code_postal_ville);
ALTER TABLE public.groupe ADD CONSTRAINT FK_groupe_nom_droits FOREIGN KEY (nom_droits) REFERENCES public.droits(nom_droits);
ALTER TABLE public.etudiant ADD CONSTRAINT FK_etudiant_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id_utilisateur);
ALTER TABLE public.personnel ADD CONSTRAINT FK_personnel_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id_utilisateur);
ALTER TABLE public.support_pedagogique ADD CONSTRAINT FK_support_pedagogique_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES public.enseignant(id_enseignant);
ALTER TABLE public.support_pedagogique ADD CONSTRAINT FK_support_pedagogique_ref_module FOREIGN KEY (ref_module) REFERENCES public.module(ref_module);
ALTER TABLE public.module ADD CONSTRAINT FK_module_ref_semestre FOREIGN KEY (ref_semestre) REFERENCES public.semestre(ref_semestre);
ALTER TABLE public.seance ADD CONSTRAINT FK_seance_ref_module FOREIGN KEY (ref_module) REFERENCES public.module(ref_module);
ALTER TABLE public.depot_exercice ADD CONSTRAINT FK_depot_exercice_id_support FOREIGN KEY (id_support) REFERENCES public.support_pedagogique(id_support);
ALTER TABLE public.controle ADD CONSTRAINT FK_controle_ref_module FOREIGN KEY (ref_module) REFERENCES public.module(ref_module);
ALTER TABLE public.mail ADD CONSTRAINT FK_mail_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id_utilisateur);
ALTER TABLE public.reponse_mail ADD CONSTRAINT FK_reponse_mail_id_mail FOREIGN KEY (id_mail) REFERENCES public.mail(id_mail);
ALTER TABLE public.membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_id_groupe FOREIGN KEY (id_groupe) REFERENCES public.groupe(id_groupe);
ALTER TABLE public.membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id_utilisateur);
ALTER TABLE public.membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_date_debut FOREIGN KEY (date_debut) REFERENCES public.periode_semestre(date_debut);
ALTER TABLE public.membres_de_groupe ADD CONSTRAINT FK_membres_de_groupe_date_fin FOREIGN KEY (date_fin) REFERENCES public.periode_semestre(date_fin);
ALTER TABLE public.sous_groupe ADD CONSTRAINT FK_sous_groupe_id_groupe_parent FOREIGN KEY (id_groupe_parent) REFERENCES public.groupe(id_groupe);
ALTER TABLE public.sous_groupe ADD CONSTRAINT FK_sous_groupe_id_groupe_fils FOREIGN KEY (id_groupe_fils) REFERENCES public.groupe(id_groupe);
ALTER TABLE public.etudie_en ADD CONSTRAINT FK_etudie_en_date_debut FOREIGN KEY (date_debut) REFERENCES public.periode_semestre(date_debut);
ALTER TABLE public.etudie_en ADD CONSTRAINT FK_etudie_en_date_fin FOREIGN KEY (date_fin) REFERENCES public.periode_semestre(date_fin);
ALTER TABLE public.etudie_en ADD CONSTRAINT FK_etudie_en_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.etudie_en ADD CONSTRAINT FK_etudie_en_ref_semestre FOREIGN KEY (ref_semestre) REFERENCES public.semestre(ref_semestre);
ALTER TABLE public.heures_travail ADD CONSTRAINT FK_heures_travail_date_debut FOREIGN KEY (date_debut) REFERENCES public.periode_semestre(date_debut);
ALTER TABLE public.heures_travail ADD CONSTRAINT FK_heures_travail_date_fin FOREIGN KEY (date_fin) REFERENCES public.periode_semestre(date_fin);
ALTER TABLE public.heures_travail ADD CONSTRAINT FK_heures_travail_id_personnel FOREIGN KEY (id_personnel) REFERENCES public.personnel(id_personnel);
ALTER TABLE public.module_enseigne_par ADD CONSTRAINT FK_module_enseigne_par_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES public.enseignant(id_enseignant);
ALTER TABLE public.module_enseigne_par ADD CONSTRAINT FK_module_enseigne_par_ref_module FOREIGN KEY (ref_module) REFERENCES public.module(ref_module);
ALTER TABLE public.commentaire_cours ADD CONSTRAINT FK_commentaire_cours_id_support_cours FOREIGN KEY (id_support_cours) REFERENCES public.support_pedagogique(id_support);
ALTER TABLE public.commentaire_cours ADD CONSTRAINT FK_commentaire_cours_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.commentaire_module ADD CONSTRAINT FK_commentaire_module_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.commentaire_module ADD CONSTRAINT FK_commentaire_module_ref_module FOREIGN KEY (ref_module) REFERENCES public.module(ref_module);
ALTER TABLE public.depot_concerne_groupe ADD CONSTRAINT FK_depot_concerne_groupe_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES public.depot_exercice(id_depot_exercice);
ALTER TABLE public.depot_concerne_groupe ADD CONSTRAINT FK_depot_concerne_groupe_id_groupe FOREIGN KEY (id_groupe) REFERENCES public.groupe(id_groupe);
ALTER TABLE public.enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES public.depot_exercice(id_depot_exercice);
ALTER TABLE public.enseignant_commente_depot ADD CONSTRAINT FK_enseignant_commente_depot_id_enseignant FOREIGN KEY (id_enseignant) REFERENCES public.enseignant(id_enseignant);
ALTER TABLE public.depot_etudiant ADD CONSTRAINT FK_depot_etudiant_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.depot_etudiant ADD CONSTRAINT FK_depot_etudiant_id_depot_exercice FOREIGN KEY (id_depot_exercice) REFERENCES public.depot_exercice(id_depot_exercice);
ALTER TABLE public.notes_controle ADD CONSTRAINT FK_notes_controle_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.notes_controle ADD CONSTRAINT FK_notes_controle_id_controle FOREIGN KEY (id_controle) REFERENCES public.controle(id_controle);
ALTER TABLE public.destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_utilisateur FOREIGN KEY (id_utilisateur) REFERENCES public.utilisateur(id_utilisateur);
ALTER TABLE public.destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_mail FOREIGN KEY (id_mail) REFERENCES public.mail(id_mail);
ALTER TABLE public.destinataire_mail ADD CONSTRAINT FK_destinataire_mail_id_groupe FOREIGN KEY (id_groupe) REFERENCES public.groupe(id_groupe);
ALTER TABLE public.etudiant_absent ADD CONSTRAINT FK_etudiant_absent_num_etudiant FOREIGN KEY (num_etudiant) REFERENCES public.etudiant(num_etudiant);
ALTER TABLE public.etudiant_absent ADD CONSTRAINT FK_etudiant_absent_id_seance FOREIGN KEY (id_seance) REFERENCES public.seance(id_seance);

ALTER TABLE depot_exercice alter column date_debut_depot_exercice type timestamp ;
alter table depot_exercice alter column date_fermeture_depot_exercice type timestamp ;
alter table seance alter column duree_seance type time without time zone ;
alter table seance alter column heure_depart_seance type time without time zone ;


ALTER TABLE depot_exercice ADD COLUMN groupe_depot integer not null;
ALTER TABLE depot_exercice ADD CONSTRAINT FK_groupe_depot_exercice FOREIGN KEY (groupe_depot) references groupe(id_groupe);


ALTER TABLE seance ADD COLUMN ID_GROUPE INTEGER NOT NULL;
ALTER TABLE seance ADD CONSTRAINT FK_groupe_seance FOREIGN KEY (id_groupe) references groupe(id_groupe);

ALTER TABLE seance ADD COLUMN nom_salle VARCHAR(8) NOT NULL;
ALTER TABLE seance ADD CONSTRAINT FK_salle_seance FOREIGN KEY (nom_salle) references salle(nom_salle);


ALTER TABLE seance ADD COLUMN id_enseignant integer NOT NULL;
ALTER TABLE seance ADD CONSTRAINT FK_enseignant_seance FOREIGN KEY (id_enseignant) references enseignant(id_enseignant);
ALTER TABLE seance ADD COLUMN date_seance date NOT NULL;

ALTER TABLE MODULE ADD COLUMN abreviation_module varchar(30) NOT NULL;


--Modifications sur les mails

ALTER TABLE destinataire_mail DROP CONSTRAINT FK_destinataire_mail_id_groupe;
ALTER TABLE destinataire_mail DROP CONSTRAINT prk_constraint_destinataire_mail;
ALTER TABLE destinataire_mail DROP COLUMN id_groupe;

ALTER TABLE destinataire_mail RENAME TO destinataire_utilisateur;
ALTER TABLE destinataire_utilisateur ADD CONSTRAINT prk_constraint_destinataire_utilisateurs PRIMARY KEY(id_utilisateur, id_mail);

CREATE TABLE public.destinataire_groupe(
	id_groupe INT  NOT NULL ,
	id_mail        INT  NOT NULL ,
	CONSTRAINT prk_constraint_destinataire_groupe PRIMARY KEY (id_groupe,id_mail)
)WITHOUT OIDS;

ALTER TABLE destinataire_groupe ADD CONSTRAINT FK_destinataire_groupe_id_groupe FOREIGN KEY (id_groupe) REFERENCES groupe (id_groupe);  
ALTER TABLE destinataire_groupe ADD CONSTRAINT FK_destinataire_groupe_id_mail FOREIGN KEY (id_mail) REFERENCES mail (id_mail);  
ALTER TABLE depot_etudiant ALTER COLUMN date_depot_etudiant TYPE TIMESTAMP WITHOUT TIME ZONE ;
ALTER TABLE depot_etudiant ADD COLUMN nom_depot_etudiant VARCHAR(70);


/*
TABLE DES TOKENS
*/
CREATE TABLE TOKEN (
	id_utilisateur integer,
	token varchar(80),
	constraint uk_token unique(id_utilisateur, token),
	constraint fk_token_id_utilisateur foreign key(id_utilisateur) references utilisateur(id_utilisateur)
);


CREATE TABLE mail_supprimes (
	id_utilisateur integer,
	id_mail integer,
	constraint pk_mail_supprimes primary key(id_utilisateur, id_mail),
	constraint fk_mail_mail_supprimes foreign key(id_mail) references mail(id_mail),
	constraint fk_utilisateur_mail_supprimes foreign key(id_utilisateur) references utilisateur(id_utilisateur)
);




alter table reponse_mail add column id_utilisateur integer;

alter table reponse_mail add constraint fk_id_utilisateur foreign key(id_utilisateur) references utilisateur(id_utilisateur);

ALTER TABLE mail add column nom_piece_jointe varchar(150);


ALTER TABLE TOKEN ADD COLUMN EXPIRATION timestamp without time zone not null default now();