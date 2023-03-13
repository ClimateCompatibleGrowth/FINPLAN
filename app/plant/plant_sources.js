//get data
function getsources(results) {
    var cidata=results['ciData'];
    var cedata = results['ceData'];
    var cfdata = results['cfData'];
    var startYear = results['startYear'];
    var financesources = results['financesources'];

    var iddata = 0;
    if (cfdata['id'])
        iddata = cfdata['id'];

    Cookies('iddata', iddata);

    var curr=Cookies('curr');
    var datar = [];
    for (var i = 1; i <= cedata["CPeriod"]; i++) {
        var data = new Array();
        data['id'] = i;
        data['item'] = i.toString() + ":" + (cedata['FOyear']-cedata['CPeriod']+i-1);
        var infval = 'Tot_'+curr;
	    var perval = curr+'_'+i;
	    var perc = cidata[perval];
	    var totcost = cidata[infval];
	    var tot = perc * totcost/100 ;
        data['tot']=tot;
        for (var j = 0; j < financesources.length; j++) {
            data[financesources[j]['id']] = cfdata[financesources[j]['id'] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var currencies = results['currencies'];
        var financesources = results['financesources'];
        var bothCurr = results['bothCurr'].split(',');
        var baseCurrency = results['baseCurrency'];
        var cfdata = results['cfData'];

        var curr = "";
        for (var k = 0; k < bothCurr.length; k++) {
            var selectedcurr = "";
            var currencyName = $.grep(currencies, function (v) {
                return v.id === bothCurr[k];
            })[0]['value'];
            if (bothCurr[k] == Cookies("curr"))
                selectedcurr = "selected";

            curr += "<option value=" + bothCurr[k] + " " + selectedcurr + ">" + currencyName + "</option>";
        }

        var tblcontrols = "<tr><td class='box-shadow card backwhite'><select id='cid' class='form-control' onchange='getDataPlant(\"plant_sources\",this.value)'>" + curr + "</select></td><td style='width:300px'></td>";

        var cols = [];
        cols.push({
            name: 'tot',
            map: 'tot',
            text: 'Constant Prices (Million)',
            editable: false,
            cellclassname: 'readonly'
        });
        for (var j = 0; j < financesources.length; j++) {
            var fnName = $.grep(financesources, function (v) {
                return v.id === financesources[j]['id'];
            })[0]['value'];

            var checked='';
            var display='none';
            var editable=false;
            var val=cfdata[financesources[j]['id']];
            if(val=='YES'){
                checked='checked';
                display='inline';
                editable=true;
            }

            if (baseCurrency !== Cookies("curr")) {
                cols.push({
                    name: financesources[j]['id'],
                    map: financesources[j]['id'],
                    text: fnName +' (%)',
                    editable: editable,
                    width:225,
                    cellclassname: editable==false ? 'readonly':''
                });
                   
                tblcontrols += "<td class='box-shadow card backwhite'> \
            <div class='pure-checkbox'> \
            <input type='checkbox' class='basic' id='" + financesources[j]['id'] + "' value='YES' "+checked+" onclick='clickFinanceSource(this)'/> \
            <label for=" + financesources[j]['id'] + "> " + financesources[j]['value'] + "  </label><br/> \
            <a class='btn btn-primary' id='a_" + financesources[j]['id'] + "' style='display:"+display+"' onclick='getDataTerms(\""+financesources[j]['id']+"\")'> \
            Terms of financing</a> \
            </div> \
            </td>";
            }

            if (baseCurrency == Cookies("curr") && financesources[j]['type'] == 'C') {
                var checked='';
                var display='none';
                var editable=false;
                var val=cfdata[financesources[j]['id']];
                if(val=='YES'){
                    checked='checked';
                    display='inline';
                    editable=true;
                }

                cols.push({
                    name: financesources[j]['id'],
                    map: financesources[j]['id'],
                    text: fnName,
                    editable: editable,
                    width:225,
                    cellclassname: editable==false ? 'readonly':''
                });

                tblcontrols += "<td class='box-shadow card backwhite'> \
            <div class='pure-checkbox'> \
            <input type='checkbox' class='basic' id='" + financesources[j]['id'] + "' value='YES' "+checked+" onclick='clickFinanceSource(this)'/> \
            <label for=" + financesources[j]['id'] + "> " + financesources[j]['value'] + "  </label><br/> \
            <a class='pointer' id='a_" + financesources[j]['id'] + "' style='display:"+display+"' onclick='getDataTerms(\""+financesources[j]['id']+"\")'>(Terms of financing)</a> \
            </div> \
            </td>";
            }


        }
        tblcontrols += "</tr>";
        $("#controls tbody").append(tblcontrols);
        CreateGrid(cols, getsources(results));
        $("#notevalueentered").hide();
    });
}

function getDataTerms(fs){
    Cookies("id", 'plant_termsfinancing');
    if(fs!==undefined)
    Cookies("fs", fs);
    $("#plantcontent").load("app/data/data.html", function(){
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();
        $("#gridTitle").html("Terms of financing");
    });
}

function enableGridColumn(input) {
    var column = input.slice(2);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'editable', editable);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'cellclassname', editable == false ? 'readonly' : '');
    if (editable) {
        $('#SteadyInf_' + column).prop('disabled', 'disabled');
    } else {
        $('#SteadyInf_' + column).removeAttr('disabled');
    }
}

function clickFinanceSource(el){
    var editable=true;
    if(el.checked){
       $("#a_"+el.id).show();
       
    }else{
        $("#a_"+el.id).hide();
        editable=false;
    }
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', el.id, 'editable', editable);
    $('#gsFlexGrid').jqxGrid('setcolumnproperty', el.id, 'cellclassname', editable == false ? 'readonly' : '');
}