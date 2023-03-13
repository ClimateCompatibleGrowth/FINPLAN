//get data
function getinflation(results) {
    console.log(results);
    var cedata = results['ceData'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var bothCurr = results['bothCurr'].split(',');
    var datar = [];
    for (var i = startYear; i <= endYear; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString();
        for (var j = 0; j < bothCurr.length; j++) {
            data[bothCurr[j]] = cedata[bothCurr[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var cedata = results["ceData"];
        var bothCurr = results['bothCurr'].split(',');
        var currencies = results['currencies'];

        //console.log('bothCurr ', bothCurr)

        var cols = [];
        var tblcontrols = "<tr>";
        var edit = {};

        for (var j = 0; j < bothCurr.length; j++) {

            if(currencies != ''){
                var currencyName = $.grep(currencies, function (v) {
                    return v.id === bothCurr[j];
                })[0]['value'];
            }


            var checkedSR = "checked";
            edit[bothCurr[j]] = false;
            var editable = true;

            

            //console.log('bothCurr[j] ', bothCurr[j])
            //edit[bothCurr[j]] = true;


            var cellclassname = "";
            if (cedata["RateType" + bothCurr[j]] == "SR") {
                checkedSR = "checked";
               //editable = false;
               edit[bothCurr[j]] = false;
               // cellclassname = "readonly"
            }

            var checkedYR = "";
            var disabled = "";
            if (cedata["RateType" + bothCurr[j]] == "YR") {
                checkedYR = "checked";
                checkedSR = "";
                disabled = "disabled"
                edit[bothCurr[j]] = true;
            }

            var steadyinf = cedata["SteadyInf_" + bothCurr[j]];
            if (steadyinf === undefined)
                steadyinf = "";

            cols.push({
                name: bothCurr[j],
                editable: editable,
                cellclassname: cellclassname,
                map: bothCurr[j],
                text: currencyName + " (%)"
            });
            

            tblcontrols += "<td class='box-shadow card backwhite'><b>" + currencyName + " (%)</b><br/> \
           <div class='row'> \
            <div class='col-md-6'> \
                <span class='pure-radiobutton'> \
                    <input type='radio' id='SR" + bothCurr[j] + "' name='RateType" + bothCurr[j] + "' " + checkedSR + " onclick='setRateType(this.id, false)' value='SR'/><label for='SR" + bothCurr[j] + "'>Steady Rate</label> \
                </span> \
            </div> \
            <div class='col-md-6'>\
                <input id='SteadyInf_" + bothCurr[j] + "' type='text' class='form-control' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + steadyinf + "' " + disabled + "/> \
            </div> \
            </div> \
           <span class='pure-radiobutton'> \
                <input type='radio' id='YR" + bothCurr[j] + "' name='RateType" + bothCurr[j] + "'  " + checkedYR + " onclick='setRateType(this.id, true)' value='YR' onkeyup='onlyDecimal(this)'/><label for='YR" + bothCurr[j] + "'>Yearly Input</label> \
            </span> \
            </td><td style='width:5px'></td>";
        }

        $("#controls").append(tblcontrols);
        CreateGrid(cols, getinflation(results));
        //console.log('edit ', edit)

        //v.k. 03012022 bug fix steady rate default allows data to be entered yearly
        for (var j = 0; j < bothCurr.length; j++) {
                
            $('#gsFlexGrid').jqxGrid('setcolumnproperty', bothCurr[j], 'editable', edit[bothCurr[j]]);
            $('#gsFlexGrid').jqxGrid('setcolumnproperty', bothCurr[j], 'cellclassname', edit[bothCurr[j]] == false ? 'readonly' : '');
        }

    })

}

function setRateType(input, editable) {
    var column = input.slice(2);
    //console.log('column ', input)
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'editable', editable);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'cellclassname', editable == false ? 'readonly' : '');
    // if (editable) {
    //     $('#SteadyInf_' + column).prop('disabled', 'disabled');
    // } else {
    //     $('#SteadyInf_' + column).removeAttr('disabled');
    // }
}