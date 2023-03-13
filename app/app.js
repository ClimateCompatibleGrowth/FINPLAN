var d = getdecimal();
var decimal = 'd' + d.toString();
$(document).ready(function () {
    changeLang(Cookies.get('langCookie'));
    getPageTitle();

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });
})

function getTabs(tabs, translates) {
    var html = "";
    if (tabs != null) {
        html += '<ul class="nav nav-tabs">';
        for (var i = 0; i < tabs.length; i++) {
            if (tabs[i] == "sales_sale" || tabs[i] == "sales_purchase") {
                html += '<li role="presentation"><a href="#/SalePurchase/' + tabs[i] + '" id="' + tabs[i] + '"><span lang="en">' + translates[i] + '</span></a></li>';
            } else {
                html += '<li role="presentation"><a href="#/GetData/' + tabs[i] + '" id="' + tabs[i] + '"><span lang="en">' + translates[i] + '</span></a></li>';
            }
        }
        html += '</ul>';
        $('#tabs').html(html);
    }
    return html;
}

function exportExcel(g, id) {
    showloader();
    var activelink = $("#gridTitle").html().replace("/", "-");
    $("#" + g).jqxGrid('exportdata', 'xls', activelink, true, null, true, 'references/jqwidgets/save-file.php');
    hideloader();
}

function decUp(g) {
    window.d++;
    window.decimal = 'd' + parseInt(window.d);
    //console.log(window.decimal);
    $('#' + g).jqxGrid('updateBoundData', 'cells');
}

function decDown(g) {
    if (window.d >= 0) {
        window.d--;
        window.decimal = 'd' + parseInt(window.d);
        $('#' + g).jqxGrid('updateBoundData', 'cells');
    }
}

function ShowSuccessMessage(message = "Data saved successfully") {
    $('#msgcontainer').html('');
    divmessage = document.createElement('div');
    var classsuccess = "alert alert-success alert-dismissable box-shadow--2dp";
    $(divmessage).addClass(classsuccess)
        .attr('id', 'msg')
        .html('<b>' + window.lang.translate("Success") + '</b></br>' + window.lang.translate(message))
        .appendTo($("#msgcontainer"))
    $('#msg').delay(3000).fadeOut('slow');
}

function ShowErrorMessage(message) {
    $('#msgcontainer').html('');
    divmessage = document.createElement('div');
    $(divmessage).addClass("alert alert-danger box-shadow--2dp")
        .attr('id', 'msg')
        .html('<b>Error !</b></br>' + message)
        .appendTo($("#msgcontainer"))
    $('#msg').delay(5000).fadeOut('slow');
}

function ShowInfoMessage(message) {
    $('#msgcontainer').html('');
    divmessage = document.createElement('div');
    $(divmessage).addClass("alert alert-info box-shadow--2dp")
        .attr('id', 'msg')
        .html('<b>Info !</b></br>' + message)
        .appendTo($("#msgcontainer"))
    $('#msg').delay(5000).fadeOut('slow');
}

function ShowWarningMessage(message) {
    $('#msgcontainer').html('');
    divmessage = document.createElement('div');
    $(divmessage).addClass("alert alert-warning alert-dismissable box-shadow--2dp")
        .attr('id', 'msg')
        .html('<b>Warning !</b></br>' + message)
        .appendTo($("#msgcontainer"))
    $('#msg').delay(5000).fadeOut('slow');
}

function getPageTitle() {
    var login = Cookies("l");
    if (login == "0") {
        $("#loginAbout").show();
    }
    if (login == "1") {
        $("#loginMenu").show();
    }

    $("#pageTitle").html("");
    $("#subtitle").show();
    $('.nav-list li.active').removeClass('active');
    var lng = Cookies.get("langCookie");
    var id = Cookies.get('id');

    

    var title = $('#' + id + ' span').html();

    
    var group = getGroup(id);
    // console.log('id ', id)
    // console.log('group ', group)
    // console.log('title ', title)

    switch (group) {
        case "home":
            $("#subtitle").hide();
            break;

        case "general":
            title = "General data";
            break;

        case "taxation":
            title = "Taxation data";
            break;

        case "balancesheet":
            title = "Initial balance sheet";
            break;

        case "sales":
            title = "Sales and purchase data";
            break;

        case "coefficient":
            title = "Coefficients";
            var idsector = Cookies.get("idsector");
            $("#" + idsector).parent().addClass('active');
            break;

        case "result":
        case "electricity":
        case "economic":
            title = $('#' + group + ' span').html();
            break;

        case "industry":
        case "transport":
        case "household":
        case "service":
            title = $('#energy').html();
            break;
        case "study":
            $("#subtitle").hide();
            break;
        case "about":
            title = "About";
            $("#subtitle").hide();
            break;
        case "accounts":
            title = "Accounts";
            $("#subtitle").hide();
            break;

        default:
            break;
    }
    $("#pageTitle").html(title);
    var titlecs = Cookies("titlecs");
    $("#studyNameTitle").html(titlecs);
    $('#' + id).parent().addClass('active');
    if (group != "")
        $('#' + group).parent().addClass('active');
    $("#" + lng).addClass("active").siblings().removeClass("active");
}

function topFunction() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}

$.fn.serializeObject = function () {
    var o = {};
    var a = this.serializeArray();
    $.each(a, function () {
        if (o[this.name]) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};

function showloader() {
    $('#preloader').show();
}

function hideloader() {
    $('#preloader').hide();
}

function loadXMLDoc(dname) {
    var xmlDoc;
    try {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', dname, false);
        xmlhttp.setRequestHeader('Content-Type', 'text/xml');
        xmlhttp.setRequestHeader("Cache-Control", "no-cache, no-store, must-revalidate");
        xmlhttp.send('');
        xmlDoc = xmlhttp.responseXML;
    } catch (e) {
        try {
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
        } catch (e) {
            console.error(e.message);
        }
    }
    return xmlDoc;
}

function exportChart() {
    var title = $("#titlechart").html();
    if (title == undefined) {
        title = $("#gridTitle").html();
    }
    $('#chartResults').jqxChart('saveAsJPEG', title + '.jpeg', 'references/jqwidgets/export.php');
}

function removeModal() {
    $('.modal').remove();
    $('.modal-backdrop').remove();
    $('body').removeClass("modal-open");
}

function InArray(number, array) {
    if (jQuery.inArray(number, array) != -1) {
        return true;
    } else {
        return false;
    }
}

function CreateGrid(cols, result, columngroups) {
    var datastructure = [];
    datastructure.push({
        name: 'item',
        map: 'item',
        type: 'string'
    });
    for (var y = 0; y < cols.length; y++) {
        datastructure.push({
            name: cols[y]['name'],
            map: cols[y]['map'],
            type: 'number'
        });
    }
    var source = {
        localdata: result,
        datatype: "array",
        datafields: datastructure
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    // var cellclassname = function (row, column, value, data) {
    //         return data.css;
    // };

    let cellsrenderer = function (row, columnfield, value, defaulthtml, columnproperties) {
        if (value != '' || value == '0') {
            var formattedValue = $.jqx.dataFormat.formatnumber(value, window.decimal);
            return '<span style="margin: 4px; float:right; ">' + formattedValue + '</span>';
        }
    };

    var plcolumns = [];
    plcolumns.push({
        text: 'Year',
        datafield: 'item',
        align: 'left',
        cellsalign: 'left',
        width: '100px',
        editable: false
    });
    for (var y = 0; y < cols.length; y++) {
        plcolumns.push({
            text: cols[y]['text'],
            datafield: cols[y]['name'],
            cellsalign: 'right',
            align: 'right',
            columngroup: cols[y]['columngroup'],
            editable: cols[y]['editable'],
            cellsformat: decimal,
            columntype: 'numberinput',
            cellsrenderer: cellsrenderer,
            cellclassname: cols[y]['cellclassname']==undefined ? '' :cols[y]['cellclassname'],
            validation:cols[y]['validation']
        });
    }

    $("#gsFlexGrid").jqxGrid({
        width: '100%',
        theme: 'metro',
        source: dataAdapter,
        selectionmode: 'multiplecellsadvanced',
        pageable: false,
        autoheight: true,
        sortable: false,
        altrows: true,
        enabletooltips: true,
        editable: true,
        columnsresize : true,
        columns: plcolumns,
        columngroups: columngroups
    });

    $("#notevalueentered").show();
}

function ReadOnlyRow(grid) {
    grid.beginningEdit.addHandler(function (s, e) {
        if ((e.col > 3 && e.col < s.columns.length - 1 && grid.itemsSource.items[e.row]['readonly'] == true)) {
            e.cancel = true;
        }
    });
}

function FormatGrid(grid) {
    jQuery.each(grid.itemsSource.items, function (i, val) {
        if (val['css'] != undefined && val['css'] != null) {
            grid.rows[i].cssClass = val['css'];
        }
    });
    grid.refresh();
}

function isNumber(number) {
    return isNaN(parseFloat(number)) ? '' : parseFloat(number);
}

function check100(number) {
    var ret = false;
    if (!isNaN(number) && number !== '') {
        if (number.toFixed(2) == 100.00) {
            ret = true;
        }
    }
    return ret;
}

function getdecimal() {
    var dec = Cookies('decimal');
    if (dec === undefined) {
        dec = 3;
    }
    return dec;
}

function getGroup(id) {
    var ret = "";
    if (id) {
        ret = id.split("_")[0];
    }
    return ret;
}

var clearName = function (name) {
    return name.match(/[^a-z0-9 _-]/gi, '') ? true : false;
};

function setValues(data) {
    //console.log('input data ', data)
    var inputs = $("#additionalData").find("input");

    var spans = $("#additionalData").find("span");

    //console.log('inputs ', inputs)
    //console.log('spans ', spans)
    for (var a = 0; a < inputs.length; a++) {
        //console.log('input ', inputs[a]["type"])
        if (inputs[a]["type"] == "radio")
            $("input[name=" + inputs[a]["name"] + "][value=" + data[inputs[a]["name"]] + "]").attr('checked', 'checked');

        if (inputs[a]["type"] == "text")
            $("#" + inputs[a]["id"]).val(data[inputs[a]["id"]]);

        if (inputs[a]["type"] == "checkbox")
            $("#" + inputs[a]["id"] + "[value=" + data[inputs[a]["id"]] + "]").prop('checked', 'checked');
    }

    for (var a = 0; a < spans.length; a++) {
        //console.log('span ', spans[a]["id"])
        if (spans[a]["id"] != "" && data[spans[a]["id"]] != undefined){
            $("#" + spans[a]["id"]).text(spans[a]["id"].replace(/_/g, ' ') +' (Million '+ data[spans[a]["id"]] +')' );
            //console.log('jd ', data[spans[a]["id"]])
        }
           
    }
}

function onlyDecimal(n) {
    $('#' + n.id).val(n.value.replace(/[^\d,.-]+/g, ''));
    $('#'+n.id).parent().removeClass('has-error');
}

function onlyNumber(n) {
    $('#' + n.id).val(n.value.replace(/[^\d-]+/g, ''));
    $('#'+n.id).parent().removeClass('has-error');
}

function maxValue(n, value) {
    if($('#' + n.id).val()>value){
        $('#' + n.id).addClass("error-text");
        ShowErrorMessage("Max value: "+value);
    }else{
        $('#' + n.id).removeClass("error-text");
    }
    $('#' + n.id).val(n.value.replace(/[^\d,.-]+/g, ''));
    $('#'+n.id).parent().removeClass('has-error');
}

function required(name, message){
    $('#'+name).parent().removeClass('has-error');
    var ret=true;
    var value=$('#'+name).val();
    console.log(name, value)
    if(!value){
        $('#'+name).parent().addClass('has-error');
        ShowErrorMessage(message);
        ret=false;
    }
    return ret;
}

function check(value){
    if (value==undefined){
        return "";
    }else{
        return value;
    }
}

var tabs = [];
//D
tabs['general'] = ['general_information', 'general_inflation', 'general_exchangerate'];
tabs['taxation'] = ['taxation_depreciation', 'taxation_royalty'];
tabs['balancesheet'] = ['balancesheet_initial', 'balancesheet_oldloans', 'balancesheet_oldbonds', 'balancesheet_investment'];
tabs['sales'] = ['sales_sale', 'sales_purchase', 'sales_consumers', 'sales_revenues'];
tabs['plantoperation'] = ['plantoperation_general', 'plantoperation_production', 'plantoperation_costs',
    'plantoperation_fuel', 'plantoperation_expenses'
];
tabs['plantinvestment'] = ['plantinvestment_investment'];
tabs['plantsourcesfinancing'] = ['plantinvestment_investment'];
tabs['planttermsfinancing'] = ['planttermsfinancing_export1', 'planttermsfinancing_export2', 'planttermsfinancing_loans'];
tabs['plantdepreciation'] = ['plantdepreciation_depreciation', 'plantdepreciation_decommissioning'];
tabs['financialmanager'] = ['financialmanager_equity', 'financialmanager_loans', 'financialmanager_bonds', 'financialmanager_other'];
tabs['plant'] = ['plant_general', 'plant_production', 'plant_omcosts', 'plant_fuelcosts', 'plant_generalexpenses', 'plant_investments', 'plant_depreciation','plant_decommissioning', 'plant_sources'];

var translates = [];
//D
translates['general'] = ['General information', 'Inflation information', 'Currency exchage rates'];
translates['taxation'] = ['Tax and depreciation information', 'Royalty payment'];
translates['balancesheet'] = ['Assets and liabilities', 'Old commercial loans', 'Old bonds data', 'Committed investment data'];
translates['sales'] = ['Sales data','Purchase data','Consumers contribution and deposits', 'Fixed revenues and other income'];
translates['plantoperation'] = ['Data of the plants in the study', 'Production data', 'Operation and maintenance costs',
    'Fuel cost information', 'General expenses data'
];
translates['plantinvestment'] = ['Investment cost in constant prices'];
translates['plantsourcesfinancing'] = ['plantinvestment_investment'];
translates['planttermsfinancing'] = ['Export credit 1', 'Export credit 2', 'Project loans'];
translates['plantdepreciation'] = ['Depreciation', 'Decommissioning cost'];
translates['financialmanager'] = ['Equity', 'New commercial loans', 'New bonds', 'Other financial data'];
translates['plant'] = ['Plant data', 'Plant production', 'Operation & Maintenance costs', 'Fuel cost information', 'General expenses data', 'Investment cost in constant prices', 'Depreciation', 'Decommissioning', 'Sources of financing']