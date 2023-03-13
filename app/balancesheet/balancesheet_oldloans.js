//get data
function getoldloans(results) {
    //console.log(results);
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
            data['L_' + bothCurr[j]] = ctdata['L_' + bothCurr[j] + '_' + i];
            data['R_' + bothCurr[j]] = ctdata['R_' + bothCurr[j] + '_' + i];
            data['I_' + bothCurr[j]] = ctdata['I_' + bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
   // console.log(results);
    $("#additionalData").load("app/data/additional.html", function () {
        var ctdata = results["ctData"];
        var bothCurr = results['bothCurr'].split(',');
        var currencies = results['currencies'];
        var cols = [];
        var columngroups = [];
        var tblcontrols = "<tr>";
        for (var j = 0; j < bothCurr.length; j++) {

            if(currencies != ''){
                var currencyName = $.grep(currencies, function (v) {
                    return v.id === bothCurr[j];
                })[0]['value'];
            }

            var ol = ctdata["OL_" + bothCurr[j]];
            if (ol === undefined)
                ol = "";

            var checkedSR = "";
            var editable = true;
            var cellclassname = "";
            if (ctdata["RateType_" + bothCurr[j]] == "SR") {
                checkedSR = "checked";
                editable = false;
                cellclassname = "readonly";
            }

            var checkedYR = "";
            var disabled = "";
            if (ctdata["RateType_" + bothCurr[j]] == "YR") {
                checkedYR = "checked";
                disabled = "disabled";
            }

            var avg = ctdata["Avg_" + bothCurr[j]];
            if (avg === undefined)
                avg = "";

            tblcontrols += "<td class='box-shadow card backwhite'><b>" + currencyName + " (Million)</b><br/> \
            <div class='row'> \
             <div class='col-md-6'> \
             <span>Outstanding loans</span> \
             </div> \
             <div class='col-md-6'>\
             <input id='OL_" + bothCurr[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + ol + "'/> \
             </div> \
             </div> \
            <div class='row'> \
             <div class='col-md-6'> \
                 <span class='pure-radiobutton'> \
                     <input type='radio' id='SR" + bothCurr[j] + "' name='RateType_" + bothCurr[j] + "' " + checkedSR + " value='SR' onclick='setAvgType(this.id, false)'/><label for='SR" + bothCurr[j] + "'>Average Return Rate</label> \
                 </span> \
             </div> \
             <div class='col-md-6'>\
                 <input id='Avg_" + bothCurr[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + avg + "' " + disabled + "/> \
             </div> \
             </div> \
            <span class='pure-radiobutton'> \
                 <input type='radio' id='YR" + bothCurr[j] + "' name='RateType_" + bothCurr[j] + "'  " + checkedYR + " value='YR' onclick='setAvgType(this.id, true)'/><label for='YR" + bothCurr[j] + "'>Yearly Return Payment</label> \
             </span> \
             </td><td style='width:5px'></td>";
            columngroups.push({
                text: currencyName + ' (Million)',
                align: 'center',
                name: currencyName
            });

            cols.push({
                name: 'L_' + bothCurr[j],
                columngroup: currencyName,
                editable: true,
                map: 'L_' + bothCurr[j],
                text: 'Committed Drawdown'
            });
            cols.push({
                name: 'R_' + bothCurr[j],
                columngroup: currencyName,
                editable: true,
                map: 'R_' + bothCurr[j],
                text: 'Repayment'
            });
            cols.push({
                name: 'I_' + bothCurr[j],
                columngroup: currencyName,
                editable: editable,
                cellclassname: cellclassname,
                map: 'I_' + bothCurr[j],
                text: 'Investment'
            });
        }
        $("#controls").append(tblcontrols);
        CreateGrid(cols, getoldloans(results), columngroups);

    })
}

function setAvgType(input, editable) {
    var column = input.slice(2);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'I_' + column, 'editable', editable);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', 'I_' + column, 'cellclassname', editable == false ? 'readonly' : '');
    if (editable) {
        $('#Avg_' + column).prop('disabled', 'disabled');
    } else {
        $('#Avg_' + column).removeAttr('disabled');
    }
}