CREATE OR REPLACE FUNCTION ouvrir_depot(                                                      
    nom_depot varchar,
    module_depot varchar,
    groupe_depot integer,
    lien_depot varchar,
    date_debut timestamp,
    date_fermeture timestamp,
    date_ouverture date,
    id_enseignant integer,
    coefficient float
) RETURNS void as $$
DECLARE
    new_id_support integer;
BEGIN
    insert into support_pedagogique values(default, nom_depot, lien_depot, now(), date_ouverture, false, 0, id_enseignant, module_depot)
        returning id_support into new_id_support;
    IF(new_id_support is not null) then
        insert into depot_exercice values(default, date_debut, date_fermeture, coefficient, new_id_support, groupe_depot);
    end if;
END;
$$LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION changer_note(
    etudiant varchar,
    enseignant integer,
    id_depot integer,
    note float,
    commentaire varchar
) RETURNS VOID AS $$

BEGIN
    perform * from enseignant_commente_depot where num_etudiant = etudiant and id_enseignant = enseignant and id_depot_exercice = id_depot;

    IF(FOUND) then
        Update enseignant_commente_depot 
        set note_depot = note, commentaire_depot = commentaire 
        where num_etudiant = etudiant and id_enseignant = enseignant and id_depot_exercice = id_depot;
    ELSE   
        Insert into enseignant_commente_depot values (commentaire, note, etudiant, id_depot, enseignant);
    END IF;

END;
$$ LANGUAGE PLPGSQL;


CREATE OR REPLACE FUNCTION ajouter_depot_etudiant (
    commentaire VARCHAR,
    lien_depot VARCHAR,
    etudiant VARCHAR,
    depot INTEGER,
    nom_depot VARCHAR
) RETURNS VOID AS $$

BEGIN
    PERFORM * FROM DEPOT_ETUDIANT WHERE id_depot_exercice = depot AND num_etudiant = etudiant;
    IF(FOUND) then
        UPDATE DEPOT_ETUDIANT SET commentaire_depot_etudiant = commentaire, 
        lien_depot_etudiant = lien_depot,  nom_depot_etudiant = nom_depot
        where id_depot_exercice = depot AND num_etudiant = etudiant;
    ELSE
        INSERT INTO DEPOT_ETUDIANT VALUES(
            commentaire,
            now(),
            lien_depot, 
            etudiant,
            depot,
            nom_depot
        );
    END IF;
END;
$$LANGUAGE PLPGSQL;


CREATE OR REPLACE FUNCTION ajouter_controle_papier(
    current_nom_controle varchar,
    current_date_controle date,
    module_controle varchar, 
    coefficient float,
    enseignant integer
) returns integer as $$
DECLARE
    new_id_controle integer;
BEGIN
    perform * from module_enseigne_par where id_enseignant = enseignant and ref_module = module_controle and est_responsable;
    
    IF(NOT FOUND) then
        RAISE EXCEPTION 'Cet enseignant n''est pas responsable de ce module' using hint = 'Cet enseignant n''est pas responsable de ce module';
    ELSE
        insert into controle values(default, coefficient, current_nom_controle, current_date_controle, module_controle) 
        returning id_controle into new_id_controle;
    END IF;

    RETURN new_id_controle;

END;
$$ language plpgsql;


CREATE OR REPLACE FUNCTION modifier_note_controle(
    controle integer,
    pseudo varchar,
    note float,
    commentaire varchar
) returns void as $$

DECLARE 
    etudiant varchar;
BEGIN
    select num_etudiant into etudiant from utilisateur 
        inner join etudiant using(id_utilisateur)
        where pseudo_utilisateur = pseudo;

    IF(NOT FOUND) then
        RAISE EXCEPTION 'Etudiant introuvable %', pseudo USING HINT = 'Aucun etudiant n''est associé à ce pseudo : ' || pseudo;
    END IF;

    PERFORM * from notes_controle where id_controle = controle and num_etudiant = etudiant; 
    
    IF(FOUND) then
        UPDATE notes_controle set commentaire_controle = commentaire, note_controle = note
        where id_controle = controle and num_etudiant = etudiant;
    ELSE
        INSERT INTO notes_controle values(note, commentaire, etudiant, controle);
    END IF;

END;
$$ language plpgsql;


CREATE OR REPLACE FUNCTION supprimer_depot(depot integer) returns void as $$
DECLARE 
    support integer;
BEGIN
    select id_support into support from support_pedagogique 
        inner join depot_exercice using(id_support)
        where id_depot_exercice = depot;
    IF(FOUND) THEN
        DELETE FROM enseignant_commente_depot where id_depot_exercice = depot;
        DELETE FROM depot_exercice where id_depot_exercice = depot;
        DELETE FROM support_pedagogique where id_support = support;
    END IF;

END;
$$LANGUAGE PLPGSQL;