//get data
function getexchangerate(results) {
    var cedata = results['ceData'];
    var startYear = results['startYear'];
    var endYear = results['endYear'];
    var curTypeSel = results['curTypeSel'].split(',');
    var datar = [];
    for (var i = startYear - 1; i <= endYear; i++) {

        var data = new Array();
        data['id'] = i;

        if( i == startYear - 1){
            data['item'] = i.toString() + ' (for initial balance sheet)';
        }
        else if( i == startYear){
            data['item'] = i.toString() + ' (start year)';
        }
        else{
            data['item'] = i.toString();
        }
        //data['item'] = i.toString();
        for (var j = 0; j < curTypeSel.length; j++) {
            data[curTypeSel[j]] = cedata[curTypeSel[j] + '_' + i];
        }
        datar.push(data);
    }
    return datar;
}

function showData(results) {
    $("#additionalData").load("app/data/additional.html", function () {
        var cedata = results['ceData'];
        var baseCurrency = results['baseCurrency'];
        var curTypeSel = results['curTypeSel'].split(',');
        var currencies = results['currencies'];
        
        var baseCurrencyName = $.grep(currencies, function (v) {
            return v.id === baseCurrency;
        })[0]['value'];

        var tblcontrols = "<tr>";
        var cols = [];
        var edit = {};
        //console.log(curTypeSel);

        if (results['curTypeSel'].length > 0) {
            for (var j = 0; j < curTypeSel.length; j++) {

                if(currencies != ''){
                    var currencyName = $.grep(currencies, function (v) {
                        return v.id === curTypeSel[j];
                    })[0]['value'];
                }


                var checkedSR = "";
                var editable = true;
                var cellclassname = "";
                edit[curTypeSel[j]] = true;


                var checkedII = "checked";

                if (cedata["RateType" + curTypeSel[j]] == "SR") {
                    checkedSR = "checked";
                    checkedII = "";
                   // editable = false;
                  //  cellclassname = "readonly";
                  edit[curTypeSel[j]] = false;
                }

                
                var disabled='';

                if (cedata["RateType" + curTypeSel[j]] == "II") {
                    checkedII = "checked";
                    //   editable = false;
                    //   cellclassname = "readonly";
                    disabled = "disabled";
                }


                var checkedYR = "";
                if (cedata["RateType" + curTypeSel[j]] == "YR") {
                    checkedYR = "checked";
                    checkedII = "";
                    disabled = "disabled"
                }

                var yearlyrate = cedata["YearlyRate" + curTypeSel[j]];
                if (yearlyrate === undefined)
                    yearlyrate = "";

                tblcontrols += "<td class='box-shadow card backwhite'><b>" + baseCurrencyName + " (" + currencyName + ") (%)</b><br/> \
                    <div class='row'> \
                    <div class='col-md-6'> \
                        <span class='pure-radiobutton'> \
                            <input type='radio' id='SR" + curTypeSel[j] + "' name='RateType" + curTypeSel[j] + "' " + checkedSR + " value='SR' onclick='setRateType(this.id,false)'/><label for='SR" + curTypeSel[j] + "'>Steady Rate</label> \
                        </span> \
                    </div> \
                    <div class='col-md-6'>\
                        <input id='YearlyRate" + curTypeSel[j] + "' type='text' class='form-control' size='50' autocomplete='off' onkeyup='onlyDecimal(this)' value='" + yearlyrate + "' " + disabled + "/> \
                    </div> \
                    </div> \
                    <span class='pure-radiobutton'> \
                    <input type='radio' id='II" + curTypeSel[j] + "' name='RateType" + curTypeSel[j] + "'  " + checkedII + " value='II' onclick='setRateType(this.id,false)'/><label for='II" + curTypeSel[j] + "'>Exchange Rate Reflects Inflation Rates</label> \
                </span> <div style='height:7px'></div>\
                    <span class='pure-radiobutton'> \
                        <input type='radio' id='YR" + curTypeSel[j] + "' name='RateType" + curTypeSel[j] + "'  " + checkedYR + " value='YR' onclick='setRateType(this.id,true)'/><label for='YR" + curTypeSel[j] + "'>Yearly Exchange Rate</label> \
                    </span> \
                    </td><td style='width:5px'></td>";

                    
                cols.push({
                    name: curTypeSel[j],
                    editable: editable,
                    cellclassname: cellclassname,
                    map: curTypeSel[j],
                    text: baseCurrencyName + ' (' + currencyName + ')'
                });
            }
            $("#controls").append(tblcontrols);

            
            console.log('curTypeSel[j] ', curTypeSel[j])
            console.log('cols ', cols)

            CreateGrid(cols, getexchangerate(results));
            $('#notevalueentered').html(``);
            $('#notevalueentered').html(`Note that only data for the first two rows needs to be entered when selecting Steady Rate or Exchange Rate Reflects Inflation Rates`);
            
                    //v.k. 03012022 bug fix steady rate default allows data to be entered yearly
            // for (var j = 0; j < curTypeSel.length; j++) {
                    
            //     $('#gsFlexGrid').jqxGrid('setcolumnproperty', curTypeSel[j], 'editable', edit[curTypeSel[j]]);
            //     $('#gsFlexGrid').jqxGrid('setcolumnproperty', curTypeSel[j], 'cellclassname', edit[curTypeSel[j]] == false ? 'readonly' : '');
            // }
           
        } else {
            $("#controls").append("No foreign currency selected");
        }
    })
}

function setRateType(input, editable) {
    var column = input.slice(2);
    // $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'editable', editable);
    // $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'cellclassname', editable == false ? 'readonly' : '');

    //$('#notevalueentered').html(`A value entered for one year will also be applicable for subsequent years, until a new value is entered for a future year. `)
    

    if (editable) {
        $('#YearlyRate' + column).prop('disabled', 'disabled');
    } else {
        $('#YearlyRate' + column).removeAttr('disabled');
    }
    if (input.substring(0, 2) == "YR") {
        $('#notevalueentered').html(`Except for the very first year, a value entered for one year will also be applicable for subsequent years, until a new value is entered for a future year`)
    }
    if (input.substring(0, 2) == "II") {
        $('#notevalueentered').html(`Note that only data for the first two rows needs to be entered when selecting Steady Rate or Exchange Rate Reflects Inflation Rates`);
        $('#YearlyRate' + column).prop('disabled', 'disabled');
        // $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'editable', true);
        // $('#gsFlexGrid').jqxGrid('setcolumnproperty', column, 'cellclassname', '');
    }
    if (input.substring(0, 2) == "SR") {
        $('#notevalueentered').html(`Note that only data for the first two rows needs to be entered when selecting Steady Rate or Exchange Rate Reflects Inflation Rates`);
        //applyFilter();
    }
}


function applyFilter() {
    //$('#jqxLoader').jqxLoader('open');
    //$("#jqxLoader").jqxLoader({theme: 'darkblue', imagePosition:"top", isModal:true,width: 500, height: 70, text: "Uploading Hourly Data Paterns..." });
    $('#gsFlexGrid').jqxGrid('clearfilters');

    //filter colum 1 null values
    // var filtergroup1 = new $.jqx.filter();
    // filtergroup1.operator = 'or';
    // var filtertype1 = 'numericfilter';
    // var filter_or_operator1 = 1;
    // var filtervalue1 = null;
    // var filtercondition1 = 'NOT_NULL';

    var filtergroup = new $.jqx.filter();
    var filter_or_operator = 1;
    var filtervalue = '2011';
    var filtercondition = 'contains';
    var filter = filtergroup.createfilter('stringfilter', filtervalue, filtercondition);
    filtergroup.addfilter(filter_or_operator, filter);

    // var filter1 = filtergroup1.createfilter(filtertype1, filtervalue1, filtercondition1);
    // filtergroup1.addfilter(filter_or_operator1, filter1);

    $('#gsFlexGrid').jqxGrid('addfilter', 'item', filtergroup);

    // // apply the filters.
    $('#gsFlexGrid').jqxGrid('applyfilters');
}