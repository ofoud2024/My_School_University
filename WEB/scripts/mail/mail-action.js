if (initVars.url.module === "mail") {

    $(document).on("click", ".mail-title-container", function (e) {
        if (initVars.mail.selected)
            initVars.mail.selected.removeClass("selected-mail");

        initVars.mail.selected = $(this);

        $(this).addClass('selected-mail');

        let id_mail = $(this).attr("mail-id");

        window.setDetailsMail(id_mail);
    })
}


function setDetailsMail(id_mail) {
    $.get(
        "php/api/index.php",
        {
            type: "mail",
            action: "details_mail",
            id_mail: id_mail
        }
    ).done(function (data) {
        let mail = JSON.parse(data);
        ReactDOM.render(
            <MailView mail={mail.mail} utilisateurs={mail.utilisateurs} groupes={mail.groupes} />,
            document.getElementById("mail-content-container")
        );
    }).fail(console.log);
}
