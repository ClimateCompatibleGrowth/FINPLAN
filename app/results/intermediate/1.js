//get data
function getbalancesheet(results) {
    var ctdata = results['results'];
    var cadata = results['caData'];
    var cbdata = results['cbData'];
    var codata = results['coData'];

    var assets=ctdata['GrossFixedAssets']*1- 
    ctdata['LessDepreciation']*1-
    ctdata['ConsumerContribution']*1+
    ctdata['WorkProgress']*1+
    ctdata['Receivables']*1+
    ctdata['ShortTermDeposits']*1;
    
    var equity=ctdata['Equity']*1 + 
    ctdata['RetainedEarning']*1 + 
    cbdata['IBB']*1 + 
    cadata['IBL']*1 + 
    ctdata['ConsumerDeposits']*1 + 
    codata['CMBL']*1;

    var datar = [];

    var data = new Array();
    data['assets'] = 'Gross Fixed Assets';
    data['assetsvalue'] = ctdata['GrossFixedAssets'];
    data['equity'] = 'Equity';
    data['equityvalue'] = ctdata['Equity'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Less: Accumulated Depreciation';
    data['assetsvalue'] = ctdata['LessDepreciation'];
    data['equity'] = 'Retained Earning';
    data['equityvalue'] = ctdata['RetainedEarning'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Less: Accumulated Consumer Contribution';
    data['assetsvalue'] = ctdata['ConsumerContribution'];
    data['equity'] = 'Net Bonds Outstanding ';
    data['equityvalue'] = ctdata['IBB'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Net Fixed Assets';
    data['assetsvalue'] = ctdata['NetFxdAsst'];
    data['equity'] = 'Net Loans Outstanding';
    data['equityvalue'] = ctdata['IBL'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Work in Progress';
    data['assetsvalue'] = ctdata['WorkProgress'];
    data['equity'] = 'Consumer Deposits';
    data['equityvalue'] = ctdata['ConsumerDeposits'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Receivables (including VAT)';
    data['assetsvalue'] = ctdata['Receivables'];
    data['equity'] = 'Current Maturity';
    data['equityvalue'] = codata['CMBL'];
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Short Term Deposits';
    data['assetsvalue'] = ctdata['ShortTermDeposits'];
    data['equity'] = '';
    data['equityvalue'] = '';
    datar.push(data);

    var data = new Array();
    data['assets'] = 'Total';
    data['assetsvalue'] =assets;
    data['equity'] = 'Total';
    data['equityvalue'] = equity;
    datar.push(data);

    return datar;
}

function showData(results) {
    var currencies=results['currencies'];
    var baseCurrency=results['baseCurrency'];
    var startYear=results['startYear'];
    var initialYear=startYear*1-1;

    var baseCurrencyName = $.grep(currencies, function(v) {
        return v.id === baseCurrency;
    })[0]['value'];

    var cols=[];
    var columngroups = [];
    columngroups.push({
        text: "Initial balance sheet of "+initialYear+" (Million " +baseCurrencyName+")",
        align: 'center',
        name: baseCurrency
    });

    cols.push({
        name: 'assets',
        columngroup: baseCurrency,
        map: 'assets',
        text: 'Assets',
        type:'string'
    });
    cols.push({
        name: 'assetsvalue',
        columngroup: baseCurrency,
        map: 'assetsvalue',
        text: 'Values',
        type:'number'
    });
    cols.push({
        name: 'equity',
        columngroup: baseCurrency,
        map: 'equity',
        text: 'Equity and liabilities',
        type:'string'
    });
    cols.push({
        name: 'equityvalue',
        columngroup: baseCurrency,
        map: 'equityvalue',
        text: 'Values',
        type:'number'
    });
      
    CreateGridBS(cols, getbalancesheet(results), columngroups);
}

function CreateGridBS(cols, result, columngroups) {
    var datastructure = [];
    for (var y = 0; y < cols.length; y++) {
        datastructure.push({
            name: cols[y]['name'],
            map: cols[y]['map'],
            type: cols[y]['type']
        });
    }
    var source = {
        localdata: result,
        datatype: "array",
        datafields: datastructure
    };
    var dataAdapter = new $.jqx.dataAdapter(source);

    var plcolumns = [];
    for (var y = 0; y < cols.length; y++) {
        plcolumns.push({
            text: cols[y]['text'],
            datafield: cols[y]['name'],
            cellsalign: 'left',
            align: 'left',
            columngroup: cols[y]['columngroup'],
            editable: cols[y]['editable'],
            width:'25%',
            cellclassname: cols[y]['cellclassname']==undefined ? '' :cols[y]['cellclassname']
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
        columns: plcolumns,
        columngroups: columngroups

    });
}
