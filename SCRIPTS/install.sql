DROP USER IF EXISTS etablissement_ofoudane;
DROP DATABASE IF EXISTS etablissement_ofoudane;

CREATE USER etablissement_ofoudane password 'etablissement';
CREATE DATABASE etablissement_ofoudane with owner etablissement_ofoudane;


\c etablissement_ofoudane
\i Script_tables/script-final.sql


--TRIGGERS
\i Triggers/semestre.sql
\i Triggers/seance.sql
\i Triggers/moodle.sql
\i Triggers/groupe.sql


--FUNCTIONS
\i Fonctions/user/utilisateur.sql
\i Fonctions/user/personnel.sql
\i Fonctions/user/etudiant.sql

\i Fonctions/Connexion/connect.sql

\i Fonctions/groupe.sql
\i Fonctions/moodle.sql
\i Fonctions/edt.sql  
\i Fonctions/mail.sql


--CREATE ADMIN USER
INSERT INTO PAYS VALUES('FR', 'FRANCE');
INSERT INTO DROITS values ('administrateur', true, true, true, true, true, true, true, true);
INSERT INTO ville values ('92230', 'GENNEVILLIERS');
 
INSERT INTO public.utilisateur values(
	DEFAULT,
	'admin',
	'admin@admin.com',
	'administrateur',
	'administrateur',
	'',
	true,
	'06XXXXXXXX',
	now(),
	'$6$rounds=5000$jljA7uJicFbvUpjj$Fb7tVepTJ3KIe4rBjSHuAsYwcz0Y3wyAI0aYaUKX/RyMCNczqRjS2PZJwU7EWclih0bHXJz5uhTkvus.aoSZz0',
	'AyGiVSkwyYMNpCKt1tAoM74wlmimgqyDD01MxxeVLHS00wVVKZSujl7rgT10',
	now(),
	'FR',
	'administrateur',
	'92230'
);
INSERT INTO PERSONNEL VALUES(default, 1);
INSERT INTO ENSEIGNANT VALUES (default, 1);
INSERT INTO ETUDIANT VALUES ('ADMIN-ETUD', 1);

GRANT ALL ON DATABASE etablissement_ofoudane to etablissement_ofoudane;
GRANT ALL ON ALL TABLES IN SCHEMA PUBLIC TO etablissement_ofoudane;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO etablissement_ofoudane;
