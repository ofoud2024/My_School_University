if (initVars.url.module === "edt") {

    var seance_courante;
    var semaine_courante;

    window.afficherAjoutSeance = function () {
        if (initVars.user.peut_modifier_edt) {
            $("#appliquer-modification-seance").attr('onClick', 'ajouterSeance()');
            $("#supprimer_seance").attr('onClick', '');
            $("#appliquer-modification-seance").text("Ajouter");
            $("#supprimer_seance").text("Annuler");
            $('#modal_ajouter_seance').modal('show');


        }
    }

    window.afficherModifierSeance = function (seance) {
        if (initVars.user.peut_modifier_edt) {
            let date_depart = moment(seance.date_seance).add(moment.duration(seance.heure_depart_seance));
            let duree = moment.duration(seance.duree_seance).asMinutes();

            let indice = liste_seances.findIndex(c => c.id_seance == seance.id_seance);

            $("#depart_seance").val(date_depart.format("YYYY-MM-DDTkk:mm"));
            $("#module_seance").val(seance.ref_module);
            $("#nom_salle").val(seance.nom_salle);
            $("#groupe_seance").val(seance.id_groupe);
            $("#duree_seance").val(duree);
            $("#module_seance").trigger("change");

            initVars.edt.changeTeacher = seance.id_enseignant;

            $("#appliquer-modification-seance").attr('onClick', 'modifierSeanceSaisie(' + indice + ')');
            $("#supprimer_seance").attr('onClick', 'supprimerSeance(' + seance.id_seance + ')');
            $("#supprimer_seance").text("Supprimer");
            $("#appliquer-modification-seance").text("Modifier");
            $('#modal_ajouter_seance').modal('show');
        }
    }

    window.get_saisie_seance = function (id_seance) {

        if (initVars.user.peut_modifier_edt) {
            let date_debut = moment($("#depart_seance").val());
            let minutes = parseInt(Math.round($("#duree_seance").val() / 10, 0));
            let duree_seance = moment.duration(minutes * 10, "minute");
            let module_seance = $("#module_seance").val();
            let salle = $("#nom_salle").val();
            let enseignant = $("#enseignant").val();
            let groupe = $("#groupe_seance").val();

            if (
                date_debut.isValid() && !isNaN(minutes) &&
                duree_seance.isValid() && module_seance !== "" &&
                salle !== "" && enseignant !== "" && groupe !== ""
            ) {
                let newSeance = {
                    id_seance: id_seance,
                    heure_depart_seance: date_debut.format("kk:mm:ss"),
                    duree_seance: formatDuration(duree_seance),
                    date_seance: date_debut.format("YYYY-MM-DD"),
                    id_groupe: groupe,
                    id_enseignant: enseignant,
                    nom_salle: salle,
                    ref_module: module_seance
                }

                if (validerSeance(newSeance)) {
                    $("#modal_ajouter_seance").modal("hide");
                    return newSeance;
                }
                else {
                    toastr["error"](i18n.messages.seance.seance_invalide);
                    return null;
                }

            } else {
                toastr["error"](i18n.messages.seance.entree_invalid);
                return null;
            }
        }

    }

    window.ajouterSeance = function () {
        let saisie = get_saisie_seance();
        if (saisie) {
            $.post(
                "php/api/index.php?type=edt&action=ajouter_seance",
                saisie
            ).done(function (data) {
                toastr.success("La séance du module " + saisie.ref_module + " a bien été ajoutée");
                chargeListeSeances();
            });
        }
    }

    window.modifierSeanceSaisie = function (indice) {

        let saisie = get_saisie_seance(liste_seances[indice].id_seance);


        if (saisie) {
            modifierSeance(liste_seances[indice].id_seance, saisie);
        }

    }


    window.modifierSeance = function (id_seance, nouvelleSeance) {
        let indiceSeance = window.liste_seances.findIndex(seance => seance.id_seance == id_seance);
        let reussi = false;

        if (indiceSeance >= 0) {
            nouvelleSeance = { ...window.liste_seances[indiceSeance], ...nouvelleSeance };

            if (window.validerSeance(nouvelleSeance)) {
                $.post(
                    "php/api/index.php?type=edt&action=modifier_seance&id_seance=" + nouvelleSeance.id_seance,
                    nouvelleSeance
                ).done(function () {
                    toastr.success("La séance séance du module " + nouvelleSeance.ref_module + " a bien été modifiée");
                    chargeListeSeances();
                }).fail(function () {
                    toastr.error("La modification de la séance a échoué: La séance est invalide");
                });
            } else {
                toastr.error("La modification de la séance a échoué: La séance est invalide");
            }

        } else {
            toastr.error("La modification de la séance a échoué");
        }

        return reussi;
    }

    window.supprimerSeance = function (id_seance) {
        $.post(
            "php/api/index.php?type=edt&action=supprimer_seance&id_seance=" + id_seance
        ).done(
            function () {
                toastr.success("Suppression de la séance réussi");
                window.chargeListeSeances();
            }
        ).fail(function () {
            toastr.error("La séance n'a pas été supprimée");
        });
    }

    window.setListeSeances = function (seances) {

        if (seances) {
            //Si la liste envoyé en paramètre n'est pas null alors on change de liste
            liste_seances = seances;
        }

        initVars.edt.seance.widthTD = new Array();
        initVars.edt.seance.startX = new Array();
        initVars.mobile_view = window.innerWidth < 678;

        window.initTableauEdt();

        if (!initVars.mobile_view) {
            for (let i = 0; i < 5; i++) {
                let el = $("#edt-body tbody tr:nth-child(1) td:nth-child(" + (i + 2) + ")");

                // $("#edt-body tr td:nth-child(0)").hide();

                initVars.edt.seance.widthTD[i] = el.innerWidth();
                initVars.edt.seance.startX[i] = el.offset().left;
                initVars.edt.seance.startY = el.offset().top;

            }
        } else {
            let el = $("#edt-body tbody tr:nth-child(1) td:nth-child(2)");

            initVars.edt.seance.widthTD = el.innerWidth();
            initVars.edt.seance.startX = el.offset().left;
            initVars.edt.seance.startY = el.offset().top;
        }

        ReactDOM.render(<ListeSeances liste_seances={liste_seances} />, document.getElementById("edt-seance-container"));
    }


    window.initTableauEdt = function () {
        ReactDOM.render(<EdtTableBody />, document.getElementById("edt-body"));
    }


    window.selectDay = function (day) {
        initVars.edt.selectedDay = day;
        window.initTableauEdt();
        ReactDOM.render(<ListeSeances liste_seances={liste_seances} />, document.getElementById("edt-seance-container"));
    }

    windowResizeListeners.push(function () {
        setListeSeances();
    });

    initTableauEdt();

}
