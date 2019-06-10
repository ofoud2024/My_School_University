if (initVars.url.module === 'moodle') {

    $(function () {
        $(document).on('change', ".custom-file-input",
            function (e) {
                let my_label_selector = "label[for='" + $(this).attr("id") + "']";
                let my_label = $(my_label_selector);

                let files = e.target.files;

                if (files.length == 1) {
                    my_label.text(files[0].name);
                }

            }
        );



        if (
            initVars.url.module === "moodle" &&
            initVars.url.action === "depot_cours" ||
            initVars.url.action === "ouvrir_depot" ||
            initVars.url.action === "acces_depot"
        ) {

            $.get(
                "php/api/index.php?type=moodle&action=modules_enseignant",
            ).done(function (data) {
                let result = JSON.parse(data);
                let html = "<option selected value='vide' >Choisissez un module</option>";

                result.forEach(module => {
                    html += "<option value='" + module.ref_module + "'> " + module.nom_module + "</option>";
                });

                $("#module_cours").html(html);
                $("#module_depot").html(html);
            });

            $("#module_depot").change(function (e) {
                module_choisi = $(this).val();

                if (module_choisi !== "vide") {
                    $.get(
                        "php/api/index.php?type=moodle&action=groupes_module",
                        {
                            type: "moodle",
                            action: "groupes_module",
                            module: module_choisi
                        }
                    ).done(function (data) {
                        let result = JSON.parse(data);

                        let html = "<option selected value='vide' >Choisissez un groupe</option>";

                        result.forEach((groupe, ind) => {
                            if (ind == 0) {
                                html += "<option value='" + groupe.id_semestre + "'> " + groupe.nom_semestre + "</option>";
                            }
                            html += "<option value='" + groupe.id_fils + "'> " + groupe.nom_groupe + "</option>";
                        });

                        $("#groupe_depot").html(html);
                    });
                }

            })

        }

        $(document).on("change:flexdatalist", "input#depot_a_lire", function (e, val, param) {
            chargerDepots(val.value);
        })


        $(document).on("click", ".collapse_th", function (e) {
            var target = "#" + $(this).attr("collapse-target");
            if ($(target).attr("is_collapsed") === "false") {
                $(target).css('display', 'none');
                $(target).attr("is_collapsed", "true");
            } else {
                $(target).css('display', 'table')
                $(target).attr("is_collapsed", "false");
            }
        });

    });
}