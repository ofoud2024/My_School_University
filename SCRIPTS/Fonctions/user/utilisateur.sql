CREATE TYPE droit as (
    droit_creation_utilisateurs boolean,
    droits_creation_modules boolean,
    droit_creation_cours boolean,
    droit_creation_groupes boolean,
    droit_modification_absences boolean,
    droit_modification_droits boolean,
    droit_modification_heures_travail boolean,
    droit_visualisation_statistique boolean
);

CREATE OR REPLACE FUNCTION droits_utilisateur(mon_id_utilisateur integer) returns droit as $$
DECLARE 
    row record;
    mes_droits droit;
BEGIN

    select droit_creation_utilisateurs, 
            droits_creation_modules, 
            droit_creation_cours,
            droit_creation_groupes,
            droit_modification_absences,
            droit_modification_droits,
            droit_modification_heures_travail,
            droit_visualisation_statistique
        into mes_droits 
        from utilisateur
        inner join droits using(nom_droits)
        WHERE utilisateur.id_utilisateur = mon_id_utilisateur;
    
    FOR row in (select droits.* from groupe 
                    inner join droits using(nom_droits)
                    where utilisateur_appartient_a_groupe(mon_id_utilisateur, id_groupe)
    )LOOP
        
        mes_droits.droit_creation_utilisateurs = mes_droits.droit_creation_utilisateurs or row.droit_creation_utilisateurs;
        mes_droits.droits_creation_modules = mes_droits.droits_creation_modules or row.droits_creation_modules;
        mes_droits.droit_creation_cours = mes_droits.droit_creation_cours or row.droit_creation_cours;
        mes_droits.droit_creation_groupes = mes_droits.droit_creation_groupes or row.droit_creation_groupes;
        mes_droits.droit_modification_absences = mes_droits.droit_modification_absences or row.droit_modification_absences;
        mes_droits.droit_modification_droits = mes_droits.droit_modification_droits or row.droit_modification_droits;
        mes_droits.droit_modification_heures_travail = mes_droits.droit_modification_heures_travail or row.droit_modification_heures_travail;
        mes_droits.droit_visualisation_statistique = mes_droits.droit_visualisation_statistique or row.droit_visualisation_statistique;

    END LOOP;

    return mes_droits; 
END;
$$ language plpgsql;