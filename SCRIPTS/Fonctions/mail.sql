CREATE OR REPLACE FUNCTION envoyer_mail(
    sujet_mail varchar,
    message_mail text,
    lien_piece_jointe varchar,
    nom_piece_jointe varchar,
    id_expediteur integer
) RETURNS INTEGER AS $$
DECLARE
    new_id_mail integer;
BEGIN
    insert into mail values(
        default,
        sujet_mail, 
        message_mail,
        lien_piece_jointe,
        now(), 
        null,
        id_expediteur,
        nom_piece_jointe
    ) returning id_mail into new_id_mail;

    return new_id_mail;
END;
$$ LANGUAGE PLPGSQL;