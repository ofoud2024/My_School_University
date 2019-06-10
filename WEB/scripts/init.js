toastr.options = {
  closeButton: true,
  debug: false,
  newestOnTop: false,
  progressBar: true,
  positionClass: "toast-top-full-width",
  preventDuplicates: false,
  showDuration: "300",
  hideDuration: "1000",
  timeOut: "2000",
  extendedTimeOut: "1000",
  showEasing: "swing",
  hideEasing: "linear",
  showMethod: "fadeIn",
  hideMethod: "fadeOut"
};


function setDataListItems(datalist, data, valueName, htmlTextName) {
  data = JSON.parse(data);

  let i = 0;

  datalist.empty();

  while (i < MAX_OPTIONS && i < data.length) {
    datalist.append(
      `<option value='${data[i][valueName]}'>${data[i][htmlTextName]}</option>`
    );
    i++;
  }
}


function durationToString(duration) {
  return zeroPadding(duration.hours()) + ":" + zeroPadding(duration.minutes()) + ":" + zeroPadding(duration.seconds());
}

function zeroPadding(val, zeroCount = 2) {
  let result = String(val);
  for (let i = result.length; i < zeroCount; i++) {
    result = "0" + result;
  }

  return result;
}

var initVars = {
  url: {
    module: new URL(window.location.href).searchParams.get("module"),
    type: new URL(window.location.href).searchParams.get("type") || "",
    action: new URL(window.location.href).searchParams.get("action"),
    semestre: new URL(window.location.href).searchParams.get("semestre"),
    semaine: new URL(window.location.href).searchParams.get("semaine")
  },
  mobile_view: false,
  edt: {
    seance: {
      startHour: 9,
      endHour: 18,
      defaultHeight: 20,
      HourCols: 6,
      startPosition: {
        top: 0,
        left: 0
      },
      width: 0,
      order: [],
      changeTeacher: null
    },
    days: ["lundi", "mardi", "mercredi", "jeudi", "vendredi"],
    selectedDay: moment().weekday() - 1
  },
  user: {

  },
  mail: {},

  tables: {}
};

const windowResizeListeners = new Array();


function setSelectContent(elementSelector, array, valueKey, textKey, placeholder = "---Faites votre choix---") {
  let html = "<option value ='vide' selected>" + placeholder + "</option>";

  array.forEach(element => {
    html += "<option value='" + element[valueKey] + "'>" + element[textKey] + "</option>";
  })

  $(elementSelector).html(html);

}


function formatDuration(duration) {
  let s = "";

  s += zeroPadding(duration.hours()) + ":";
  s += zeroPadding(duration.minutes()) + ":";
  s += zeroPadding(duration.seconds());

  return s;
}

var abreviations = {
  modules: {
    "M3101": "Système",
    "M3102": "BDS2"
  }
};


$(document).ready(function () {

  window.initVars.token = $("input#token").val();

  $(".data-table").each(function (index) {
    let table = $(this).DataTable({
      pageLength: 5
    });

    if ($(this).attr("id") !== '') {
      window.initVars.tables[$(this).attr("id")] = table;
    }

  });


  //Récupère les données de l'utilisateur courant
  $.get('php/api/index.php?type=utilisateur&action=details_utilisateur_courant')
    .done(function (data) {
      let json_data = JSON.parse(data);
      initVars.user = json_data;
      $(".set_full_name").text(
        json_data.nom_utilisateur + " " + json_data.prenom_utilisateur
      );
    });


  $(".convert-duration").each(function () {
    let duration = moment.duration($(this).attr('duration'));
    let durationFormat = duration.minutes() + " minutes";
    if (Math.round(duration.asHours()) > 0) {
      durationFormat = Math.round(duration.asHours()) + " heures et " + durationFormat
    } else if (duration.minutes() == 0) {
      durationFormat = "0 heures";
    }
    $(this).text(durationFormat);
  })

  let toastr_message = new URL(window.location.href).searchParams.get("toastr");
  let toastr_type = new URL(window.location.href).searchParams.get("toastr_type") || "success";

  if (toastr_message) {
    toastr[toastr_type](toastr_message);
  }


});





