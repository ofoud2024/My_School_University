/*Fonctions */
window.compareSeance = function (s1, s2) {
    let debut1 = moment(s1.date_seance).add(moment.duration(s1.heure_depart_seance));
    let fin1 = moment(s1.date_seance)
    fin1.add(moment.duration(s1.heure_depart_seance)).add(moment.duration(s1.duree_seance));

    let debut2 = moment(s2.date_seance).add(moment.duration(s2.heure_depart_seance));
    let fin2 = moment(s2.date_seance)
    fin2.add(moment.duration(s2.heure_depart_seance)).add(moment.duration(s2.duree_seance));

    return (debut1 <= debut2 && debut2 < fin1) || (debut2 <= debut1 && debut1 < fin2);
}

window.appartientAuGroupe = function (id_groupe1, id_groupe2) {
    let sous_groupe1 = window.sous_groupes[id_groupe1];
    let sous_groupe2 = window.sous_groupes[id_groupe2];

    if (sous_groupe1) {
        if (sous_groupe1.find(function (g) {
            return g.id_groupe === id_groupe2;
        }))
            return true;
    }

    if (sous_groupe2) {
        if (sous_groupe2.find(function (g) {
            return g.id_groupe === id_groupe1;
        }))
            return true;
    }

    return sous_groupe2 === sous_groupe1;
}

window.validerHeure = function (seance) {
    let heure_depart = moment.duration(seance.heure_depart_seance).asHours();
    let heure_fin = moment.duration(seance.heure_depart_seance).add(seance.duree_seance).asHours();

    let date_seance = moment(seance.date_seance);
    let jour_semaine = 7;

    if (date_seance.isSameOrAfter(initVars.url.semaine, 'day') && date_seance.isBefore(initVars.edt.semaine_prochaine, 'day'))
        jour_semaine = date_seance.weekday();


    return jour_semaine < 6 && heure_depart >= initVars.edt.seance.startHour && heure_fin <= initVars.edt.seance.endHour;
}

window.validerSeance = function (seance) {

    let index = liste_seances.findIndex(function (s) { return s.id_seance == seance.id_seance });

    let seance_similaire = liste_seances.filter(function (s, i) {
        return i != index && compareSeance(seance, s);
    }).find(function (s) {
        return appartientAuGroupe(seance.id_groupe, s.id_groupe)
    });

    return !seance_similaire && validerHeure(seance);
}

window.typeSeance = function (groupe) {
    let nom = groupe.nom_groupe;

    if (nom) {
        if (nom.toUpperCase().startsWith("TD"))
            return "TD";
        else if (nom.toUpperCase().startsWith("TP"))
            return "TP";
    }
    return "AMPHI";
}

$(function () {
    initVars.edt.semaine_prochaine = moment(initVars.url.semaine).weekday(1).add(1, 'weeks').format('YYYY-MM-DD');
    initVars.edt.semaine_precedante = moment(initVars.url.semaine).subtract(1, 'weeks').weekday(1).format('YYYY-MM-DD');

    $(".semaine_courante").text(moment(initVars.url.semaine).weekday(1).format('DD-MM-YYYY'));
    $(".semaine_prochaine").text(moment(initVars.edt.semaine_prochaine).format('DD-MM-YYYY'));
    $(".edt .previous_week").attr('href', 'index.php?module=edt&semaine=' + initVars.edt.semaine_precedante + "&semestre=" + initVars.url.semestre);
    $(".edt .next_week").attr('href', 'index.php?module=edt&semaine=' + initVars.edt.semaine_prochaine + "&semestre=" + initVars.url.semestre);

    $("select#ref_semestre").change(function () {
        let semestre = $(this).val();
        window.location = "index.php?module=" + initVars.url.module + "&semaine=" + initVars.url.semaine + "&semestre=" + semestre;
    })

})

