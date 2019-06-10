$(function () {
    if (initVars.url.module === "edt") {
        window.chargeListeSeances = function () {
            $.get(
                "php/api/index.php",
                {
                    type: "edt",
                    action: "liste_seances",
                    date_debut: initVars.url.semaine,
                    date_fin: initVars.edt.semaine_prochaine,
                }
            ).done(function (data) {
                let json_data = JSON.parse(data);
                setListeSeances(json_data);
            })
        }


        /*
        GET LISTS FROM SERVER 
        */



        $.get(
            "php/api/index.php?type=semestre&action=liste_semestres"
        ).done(function (data) {
            let json_data = JSON.parse(data);
            setSelectContent("#ref_semestre", json_data, "ref", "nom");
            if (initVars.user.semestre || initVars.url.semestre) {
                $("#ref_semestre").val(initVars.url.semestre);
            } else {
                window.location = "index.php?module=edt&semaine=" + initVars.url.semaine + "&semestre=" + json_data[0].ref;
            }
        });

        $.get(
            "php/api/index.php",
            {
                type: "semestre",
                action: "groupes_semestre",
                semestre: initVars.url.semestre
            }
        ).done(function (data) {
            let json_data = JSON.parse(data);
            if (json_data.length > 0)
                json_data.splice(0, 0, {
                    id_groupe: json_data[0].id_semestre,
                    nom_groupe: initVars.url.semestre
                });

            setSelectContent("#groupe_seance", json_data, "id_groupe", "nom_groupe", "--Choisissez un groupe--");

            window.sous_groupes = {};

            json_data.forEach(g => {
                window.sous_groupes[g.id_groupe] = getSousGroupes(g.id_groupe)
            });

            window.groupes_seance_par_type = {
                "AMPHI": [],
                "TD": [],
                "TP": []
            }
            window.types_seance = {
                "AMPHI": 0,
                "TD": 0,
                "TP": 0
            }
            json_data.forEach(g => {
                let type = window.typeSeance(g);
                window.types_seance[type]++;
                window.groupes_seance_par_type[type].push(g);
            })

            chargeListeSeances();

        });


        $.get(
            "php/api/index.php",
            {
                type: "module",
                action: "liste_modules",
            }
        ).done(function (data) {
            let json_data = JSON.parse(data);
            setSelectContent("#module_seance", json_data, "ref_module", "nom_module", '--Choisissez un module--');
        });


        $("#module_seance").change(function (e) {
            let valeur = $(this).val();
            $.get(
                "php/api/index.php",
                {
                    type: "utilisateur",
                    action: "enseignants_module",
                    module: valeur
                }
            ).done(function (data) {
                let json_data = JSON.parse(data);
                json_data = json_data.map(row => {
                    return { ...row, text_value: row.prenom_utilisateur + " " + row.nom_utilisateur };
                })
                setSelectContent("#enseignant", json_data, "id_enseignant", "text_value", '--Choisissez un enseignant--');

                if (initVars.edt.changeTeacher) {
                    $("#enseignant").val(initVars.edt.changeTeacher);
                }

            });
        });

        $(".edt .edt-absences button").click(function () {
            $("#modal_absences").modal("show");
        })





    }
})
