//get data
function getinvestments(results) {
    var data = results['data'];
    var cedata = results['ceData'];
    var cfdata = results['cfData'];
    var startYear = results['startYear'];
    var bothCurr = results['bothCurr'].split(',');

    var iddata = 0;
    if (cfdata['id'])
        iddata = cfdata['id'];
    Cookies('iddata', iddata);
    
    var datar = [];
    for (var i = 1; i <= cedata["CPeriod"]; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString() + ":" + (cedata['FOyear']-cedata['CPeriod']+i-1);
        for (var j = 0; j < bothCurr.length; j++) {
            data[bothCurr[j]] = cfdata[bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var tblcontrols = "<tr>";
        var currencies = results['currencies'];
        var cfdata = results['cfData'];
        var bothCurr = results['bothCurr'].split(',');
        var cols = [];
        for (var j = 0; j < bothCurr.length; j++) {
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[j];
            })[0]['value'];
            cols.push({
                name: bothCurr[j],
                map: bothCurr[j],
                text: currencyName + " (% distribution)",
                editable: true,
                validation:function (cell, value) {
                    if (value > 100) {
                        return { result: false, message: "Max value: 100" };
                    }
                    return true;
                }
            });

            tblcontrols += "<td class='box-shadow card backwhite'><b>" + currencyName + " (Million)</b><br/> \
            <input id='Tot_" + bothCurr[j] + "' type='text' class='form-control' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + check(cfdata['Tot_'+bothCurr[j]]) + "'/> \
            </td><td style='width:5px'></td>";
        }

        $("#controls").append(tblcontrols);
        CreateGrid(cols, getinvestments(results));
        $("#notevalueentered").html("Inflation will be applied to investment costs");
    })
}