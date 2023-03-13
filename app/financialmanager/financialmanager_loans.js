//get data
function getloans(results) {
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
            data['A_' + bothCurr[j]] = ctdata['A_' + bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var bothCurr = results['bothCurr'].split(',');
        var currencies = results['currencies'];
        var ctdata = results['ctData'];

        var cols = [];
        var tblcontrols = "<tr>";
        for (var j = 0; j < bothCurr.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];
            var s = check(ctdata['S_' + bothCurr[j]]);
            var t = check(ctdata['T_' + bothCurr[j]]);
            tblcontrols += "<td class='box-shadow card backwhite'><b>" + currencyName + " (Million)</b><br/> \
            <div class='row'> \
            <div class='col-md-6'> \
            <span>Interest spread above Inflation (%)</span> \
            </div> \
            <div class='col-md-6'>\
            <input id='S_" + bothCurr[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + s + "'/> \
            </div> \
            </div> \
            <div class='row'> \
            <div class='col-md-6'> \
            <span>Term (Year's)</span> \
            </div> \
            <div class='col-md-6'>\
            <input id='T_" + bothCurr[j] + "' type='text' class='form-control' size='50' onkeyup='onlyDecimal(this)' autocomplete='off' value='" + t + "'/> \
            </div> \
            </div> \
            </td><td style='width:5px'></td>";

            cols.push({
                name: 'A_' + bothCurr[j],
                editable: true,
                map: 'A_' + bothCurr[j],
                text: currencyName + " - Drawdown (Million)"
            });
        }
        CreateGrid(cols, getloans(results));
        $("#controls").append(tblcontrols);
        $("#notevalueentered").html("Values are only considered for the year they are entered in this entry form.");
    })
}