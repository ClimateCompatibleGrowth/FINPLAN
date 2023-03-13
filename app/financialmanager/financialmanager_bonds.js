//get data
function getbonds(results) {
    var ctdata = results['ctData'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var bothCurr = results['bothCurr'].split(',');
    var datar = [];
    for (var i = startYear; i <= endYear; i++) {

        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        for (var j = 0; j < bothCurr.length; j++) {
            data['B_' + bothCurr[j]] = ctdata['B_' + bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var ctdata = results['ctData'];
        var bothCurr = results['bothCurr'].split(',');
        var currencies = results['currencies'];

        var cols = [];
        var tblcontrols = "<tr>";
        for (var j = 0; j < bothCurr.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];

            var er = check(ctdata['ER_' + bothCurr[j]]);
            var bt = check(ctdata['BT_' + bothCurr[j]]);
            tblcontrols += "<td class='box-shadow card backwhite'><b>" + currencyName + " (Million)</b><br/> \
        <div class='row'> \
         <div class='col-md-6'> \
         <span>Expected Rate (%)</span> \
         </div> \
         <div class='col-md-6'>\
         <input id='ER_" + bothCurr[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + er + "'/> \
         </div> \
         </div> \
         <div class='row'> \
         <div class='col-md-6'> \
         <span>Bonds Term (Year's)</span> \
         </div> \
         <div class='col-md-6'>\
         <input id='BT_" + bothCurr[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + bt + "'/> \
         </div> \
         </div> \
         </td><td style='width:5px'></td>";

            cols.push({
                name: 'B_' + bothCurr[j],
                editable: true,
                map: 'B_' + bothCurr[j],
                text: currencyName + " - Issued (Million)"
            });
        }
        CreateGrid(cols, getbonds(results));
        $("#controls").append(tblcontrols);
        $("#notevalueentered").html("Values are only considered for the year they are entered in this entry form.");
    })
}