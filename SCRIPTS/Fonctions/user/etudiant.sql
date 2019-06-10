CREATE OR REPLACE FUNCTION update_semestre_etudiant(
    semestre varchar,
    etudiant varchar,
    debut_periode date,
    fin_periode date 
) returns void as $$

begin
    PERFORM * FROM ETUDIE_EN WHERE  
    num_etudiant = etudiant  and date_debut = debut_periode and date_fin = fin_periode;

    IF(FOUND) THEN
        UPDATE ETUDIE_EN SET ref_semestre = semestre where
        num_etudiant = etudiant  and date_debut = debut_periode and date_fin = fin_periode;
    ELSIF(fin_periode > now()) THEN
        insert into etudie_en(date_debut, date_fin, num_etudiant, ref_semestre) 
        values (debut_periode, fin_periode, etudiant, semestre);
    ELSE
        RAISE EXCEPTION 'INSERTION REFUSÉE' USING HINT = 'Vous ne pouvez pas créer un semestre dans le passé';
    END IF;

end;
$$ language plpgsql;


CREATE OR REPLACE FUNCTION modifier_absences(
    id_seance_courante integer,
    pseudo varchar 
)  returns void as $$

declare
    groupe_seance integer;
    etudiant varchar;
begin
    select id_groupe into groupe_seance from seance 
        where id_seance = id_seance_courante; 

    IF(FOUND) THEN
        select num_etudiant into etudiant from utilisateur inner join etudiant using(id_utilisateur)
            where pseudo_utilisateur = pseudo and utilisateur_appartient_a_groupe(id_utilisateur, groupe_seance);
        IF(FOUND) THEN
            perform * from etudiant_absent where id_seance = id_seance_courante and num_etudiant = etudiant;
            IF(NOT FOUND) THEN
                INSERT INTO etudiant_absent values(false, '', etudiant, id_seance_courante);
            END IF;
        END IF;

    END IF;

end;
$$ language plpgsql;

CREATE OR REPLACE FUNCTION nettoyer_absences(
    enseignant integer
) RETURNS integer as $$
declare
    id_seance_courante integer;
BEGIN
    select id_seance into id_seance_courante from seance 
        where id_enseignant = enseignant  
        and (now(), Interval '00:00:00') overlaps (date_seance + heure_depart_seance::time::interval, duree_seance ::time ) limit 1;
    IF(FOUND) THEN
        DELETE FROM etudiant_absent where id_seance = id_seance_courante;
    ELSE
        RAISE EXCEPTION 'Séance invalide';
    END IF;

    RETURN id_seance_courante;
END;
$$ LANGUAGE PLPGSQL;