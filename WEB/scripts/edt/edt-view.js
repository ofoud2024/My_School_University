


class EdtTableBody extends React.Component {
    constructor(props) {
        super(props);
        this.stepBack = this.stepBack.bind(this);
        this.stepForward = this.stepForward.bind(this);
    }

    componentDidMount() {

    }

    componentDidUpdate() {

    }

    stepBack() {
        let targetDay = initVars.edt.selectedDay - 1;

        if (initVars.edt.selectedDay == 0) {
            targetDay = initVars.edt.days.length - 1;
        }

        selectDay(targetDay);
    }

    stepForward() {
        let targetDay = initVars.edt.selectedDay + 1;

        if (targetDay == initVars.edt.days.length) {
            targetDay = 0;
        }

        selectDay(targetDay);
    }

    render() {
        let objects = new Array();

        let start = initVars.edt.seance.startHour;
        let end = initVars.edt.seance.endHour;
        let HourCols = initVars.edt.seance.HourCols;

        let header = (
            <tr>
                <th className="bg-transparent border-0"></th>
                <th className="col-lundi text-white col-edt">Lundi</th>
                <th className="col-mardi text-white col-edt">Mardi</th>
                <th className="col-mercredi text-white col-edt">Mercredi</th>
                <th className="col-jeudi text-white col-edt">Jeudi</th>
                <th className="col-vendredi text-white col-edt">Vendredi</th>
            </tr>
        );

        if (initVars.mobile_view) {

            let day = initVars.edt.days[initVars.edt.selectedDay];

            header = (
                <tr>
                    <th className="bg-transparent border-0"></th>
                    <th className={"col-" + day + "  text-white col-edt"}>
                        <div className="edt-day-switcher">
                            <div className="switcher" onClick={this.stepBack}><i className="fas fa-chevron-left"></i></div>
                            <div className="">{day}</div>
                            <div className="switcher" onClick={this.stepForward}><i className="fas fa-chevron-right"></i></div>
                        </div>
                    </th>
                </tr>

            )
        }

        for (let i = start; i < end; i++) {
            for (let j = 0; j < HourCols; j++) {
                objects.push(
                    <EdtTableBodyRow key={i * HourCols + j} time={i + (j / 10 * (6 / HourCols))} />
                );
            }
        }

        return (
            <table className="mx-auto table-border-collapse " >
                <thead className="edt-header">
                    {header}
                </thead>
                <tbody className="bg-white" onClick={afficherAjoutSeance}>
                    {objects}
                </tbody>
            </table>
        );

    }
}


class EdtTableBodyRow extends React.Component {
    constructor(props) {
        super(props);
    }

    render() {

        let hours = parseInt(this.props.time);
        let minuts = parseInt(Math.round((this.props.time * 100), 0) % 100);
        let isNewHour = minuts == 0;
        let classNameAdd = isNewHour ? ' newHour' : '';

        if (initVars.mobile_view) {
            return (
                <tr className={"body-tr" + classNameAdd} style={
                    { height: initVars.edt.seance.defaultHeight }
                }>
                    <td>{zeroPadding(hours)}:{zeroPadding(minuts)}</td>
                    <td></td>
                </tr>
            );
        }
        return (
            <tr className={"body-tr" + classNameAdd} style={
                { height: initVars.edt.seance.defaultHeight }
            }>
                <td>{zeroPadding(hours)}:{zeroPadding(minuts)}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr >
        )
    }
}

class ListeSeances extends React.Component {

    constructor(props) {
        super(props);

        this.state = { liste_seances: this.props.liste_seances };


        this.isSelectedDay = this.isSelectedDay.bind(this);

    }

    //Renvoie le numéro du jour correspondant à posX 
    getDate(posX) {
        let startXArray = initVars.edt.seance.startX;



        for (let i = 1; i < startXArray.length; i++) {
            if (startXArray[i - 1] < posX && startXArray[i] >= posX)
                return i;
        }
        return startXArray.length;
    }

    //Retourne le nombre de 10minutes entre la position du départ et posY
    getStartHour(posY) {
        return initVars.edt.seance.startHour * 6 + Math.round((posY - initVars.edt.seance.startY) / (initVars.edt.seance.defaultHeight / (6 / initVars.edt.seance.HourCols)));
    }


    isSelectedDay(seance) {
        let dayNum = moment(seance.date_seance).weekday() - 1;

        return !initVars.mobile_view || dayNum == initVars.edt.selectedDay;
    }

    render() {
        //On définit la liste des séances dans l'ordre;

        return (
            <div >

                {this.props.liste_seances
                    .filter(seance => validerHeure(seance))
                    .filter(this.isSelectedDay)
                    .map(seance => {
                        return <SeanceEdt data={seance} getDate={this.getDate} getStartHour={this.getStartHour} />;
                    })}
            </div>
        )
    }
}

class SeanceEdt extends React.Component {

    constructor(props) {
        super(props);

        this.getX = this.getX.bind(this);
        this.getY = this.getY.bind(this);
        this.dragOver = this.dragOver.bind(this);
        this.dragStart = this.dragStart.bind(this);
        this.getHeight = this.getHeight.bind(this);
        this.getRang = this.getRang.bind(this);
        this.getWidth = this.getWidth.bind(this);
        this.modifierSeance = this.modifierSeance.bind(this);
        this.getColor = this.getColor.bind(this);
    }


    getFont() {
        let type = typeSeance(this.props.data);
        if (type === "TP")
            return 10;
        else if (type === "TD")
            return 15;
        else
            return 17;
    }


    getRang(type) {
        if (type === "TD" || type === "TP") {
            return groupes_seance_par_type[type].sort(function (a, b) {
                return a.nom_groupe.localeCompare(b.nom_groupe);
            }).findIndex((g) => g.id_groupe == this.props.data.id_groupe);
        } else
            return 0;
    }

    getX() {
        var dayNum = moment(this.props.data.date_seance).weekday();

        var startX = initVars.edt.seance.startX;

        if (!initVars.mobile_view) {
            startX = initVars.edt.seance.startX[dayNum - 1];
        }

        let rang = this.getRang(typeSeance(this.props.data));


        return startX + (rang * this.getWidth());
    }


    getY() {
        var yAjouter = (this.start.hours() - initVars.edt.seance.startHour) * (initVars.edt.seance.defaultHeight * initVars.edt.seance.HourCols);
        yAjouter += this.start.minutes() * (initVars.edt.seance.defaultHeight) / (60 / initVars.edt.seance.HourCols);

        return (initVars.edt.seance.startY + yAjouter);
    }

    getHeight() {
        return this.duration.hours() * (initVars.edt.seance.defaultHeight * initVars.edt.seance.HourCols)
            + this.duration.minutes() * (initVars.edt.seance.defaultHeight + 1) / (60 / initVars.edt.seance.HourCols);
    }

    getWidth() {
        var dayNum = moment(this.props.data.date_seance).weekday();

        var width = initVars.edt.seance.widthTD;

        if (!initVars.mobile_view)
            width = initVars.edt.seance.widthTD[dayNum - 1];


        return width / types_seance[typeSeance(this.props.data)];
    }

    dragOver(e) {
        let weekDay = this.props.getDate(e.pageX);

        let minutes = this.props.getStartHour(e.pageY - this.offsetY);
        let startTime = zeroPadding(parseInt(minutes / 6)) + ":" + zeroPadding(parseInt(minutes % 6) * 10) + ":00";

        window.modifierSeance(
            this.props.data.id_seance,
            {
                date_seance: moment(initVars.url.semaine).weekday(weekDay).format("YYYY-MM-DD"),
                heure_depart_seance: startTime
            }
        );
    }

    dragStart(e) {
        this.setState({ ...this.state, dragging: true });

        this.offsetY = e.pageY - this.getY();

    }


    modifierSeance() {
        afficherModifierSeance(this.props.data);
    }

    abreviation(nom, type, key) {
        let parts = String(nom).trim().split(" ").filter(s => s.toLowerCase() !== "et");

        if (abreviations[type] && abreviations[type][key])
            return abreviations[type][key];


        if (parts.length > 1) {
            return parts.map(s => s[0]).join("");
        } else {
            return nom;
        }

    }

    getColor() {
        var couleur = this.props.data.couleur_module.substring(1);      // strip #
        var rgb = parseInt(couleur, 16);   // convert rrggbb to decimal
        var red = (rgb >> 16) & 0xff;  // extract red
        var green = (rgb >> 8) & 0xff;  // extract green
        var blue = (rgb >> 0) & 0xff;  // extract blue

        var darkness = 1 - (0.299 * red + 0.587 * green + 0.114 * blue) / 255;

        if (darkness > 0.4) {
            return "white";
        } else {
            return "black";
        }



    }


    render() {

        this.start = moment.duration(this.props.data.heure_depart_seance);
        this.duration = moment.duration(this.props.data.duree_seance);

        var style =
        {
            top: this.getY(),
            left: this.getX() + 1,
            width: this.getWidth() + "px",
            height: this.getHeight(),
            backgroundColor: this.props.data.couleur_module,
            color: this.getColor(),
            fontSize: this.getFont() + "px"
        };


        return (
            <div onClick={this.modifierSeance} className="seance-cours" onDragStart={this.dragStart} onDragEnd={this.dragOver} draggable={initVars.user.peut_modifier_edt && !initVars.mobile_view} style={style}>
                <p className="prof-seance">{this.abreviation(this.props.data.prenom_enseignant + " " + this.props.data.nom_enseignant)}</p>
                <div className="module-seance">
                    <p >{this.props.data.abreviation_module}</p>
                    <p className="groupe-seance">{this.props.data.nom_groupe.startsWith("S") ? "Cours" : this.props.data.nom_groupe}</p>
                </div>
                <p className="salle-seance">{this.props.data.nom_salle}</p>
            </div>
        );
    }
}




