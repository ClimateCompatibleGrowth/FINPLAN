var urlData = "app/results/results_intermediate.php";
$(document).ready(function () {
    getcontentresults();

    $("#reportType").on('change', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        generateresults(this.value, $('#reportTypeSector').val());
    });

    $("#reportTypeSector").on('change', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        getTables(this.value);
        generateresults($("#reportType").val(), this.value);
    });

    $("#decUp").on('click', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        window.d++;
        window.decimal = 'd' + parseInt(window.d);
        $('#gsFlexGrid').jqxGrid('updateBoundData', 'cells');
    });
    $("#decDown").on('click', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        window.d--;
        window.decimal = 'd' + parseInt(window.d);
        $('#gsFlexGrid').jqxGrid('updateBoundData', 'cells');
    });

    $(".changeChart").on('click', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        window.charttype = $(this).attr('id');

        var chart1 = $('#chartResults').jqxChart('getInstance');
        chart1.seriesGroups[0].type = window.charttype;
        chart1.update();
    });

    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var target = $(e.target).attr("href") // activated tab
        if (target == "#chart") {
            getchartresults("gsFlexGrid");
        }
    });
})

function generateresults(id, group, title) {
    $.getScript('app/results/intermediate/' + group.replace('.', '') + '.js', function () {
        $.ajax({
            url: urlData,
            data: {
                id: id,
                action: 'get'
            },
            type: 'POST',
            success: function (res) {
                $("#tabdetail").hide();
                var results = jQuery.parseJSON(res);
                $('#resultModal').modal('show');
                $('#reportTypeSector').val(group);
                getTables(group, id);
                //$('#gridTitle').html(title);
                //$('#chartTitle').html(title);
                $('#gridTitle').html($("#reportType option:selected").text().replace(id, ''));
                $('#chartTitle').html($("#reportType option:selected").text().replace(id, ''));
               
                $("#startyear").val(results["startYear"]);
                $("#endyear").val(results["endYear"]);
                $("#baseCurrency").val(results["baseCurrency"]);
                showData(results);
            },
            error: function (xhr, status, error) {
                ShowErrorMessage(error);
            }
        });
    });
}

function getcontentresults() {
    $('#reportTypeSector').html('');
    htmlstring = "";
    var htmlarr = [];
    var content = getcontenttable();
    for (i = 0; i < content.length; i++) {
        htmlstring = "";
        htmlstring += '<div class="panel panel-default">\
    <div class="panel-heading" style="padding-right: 0px !important;">\
    <table style="width: 100%;">\
    <tr>\
    <td style="width:50px"><b>' + content[i]['id'] + '</b></td>\
    <td>\
    <b><a data-toggle="collapse" class="pstitle" style="display:block; width:100%" data-parent="#accordion" id="psid_' + content[i]['title'] + '" href="#collapse_' + content[i]['title'].replace(/[^A-Z0-9]/ig, "") + '">\
    <span lang="en">' + content[i]['title'] + '</span> </a></b>\
    </td>\
    </tr>\
    </table>\
    </div>\
    <div id="collapse_' + content[i]['title'].replace(/[^A-Z0-9]/ig, "") + '" class="panel-collapse collapse">\
    <div class="panel-body" style="border: 0 !important;">\
    <table class="table table-hover" style="width: 100%;">';

        $('#reportTypeSector').append('<option value=' + content[i]['id'] + '>' + content[i]['id'] + ' ' + window.lang.translate(content[i]['title']) + '</option>');

        $.each(content[i]['tables'], function (index, value) {
            htmlstring += '<tr>\
            <td style="width: 50px;"></td>\
            <td style="width: 50px;">' + value['id'] + '</td>\
            <td>\
            <a style="display:block; cursor: pointer;"  onclick="generateresults(\'' + value['id'] + '\',\'' + content[i]['id'] + '\',\'' + value['title'] + '\')" lang="en" data-lang-token="' + value['title'] + '">' + value['title'] + '</a>\
            </td>\
            <td style="width:70px; text-align:center"> \
            <a  class="' + value['notexisticons'] + '" onclick="generateresults(\'' + value['id'] + '\',\'' + content[i]['id'] + '\',\'' + value['title'] + '\')">\
            <i class="material-icons btnblue" data-toggle="tooltip"  title="TABLE" lang="en" data-lang-content="false">view_module</i></a></td>\
            <td style="width:70px; text-align:center">\
            <a  class="' + value['notexisticons'] + '" data-toggle="tooltip"  title="CHART" lang="en" data-lang-content="false" onclick="generateresults(\'' + value['id'] + '\',\'' + content[i]['id'] + '\',\'' + value['title'] + '\')">\
            <i class="material-icons btnorange">equalizer</i></a></td>\
            </tr>';
        })

        htmlstring += '</table>\
        </div>\
        </div>\
        </div>';
        htmlarr.push(htmlstring);
    }
    $("#accordionresults").html(htmlarr.join(""));

}

function getcontenttable() {
    var content = [];
    var row = new Array();
    row['id'] = '1.';
    row['title'] = 'Initial balance sheet';

    row['tables'] = [{
        'id': '1.1.',
        'title': 'Initial balance sheet'
    }];
    content.push(row);

    //Old loans

    row = new Array();
    row['id'] = '2.';
    row['title'] = 'Old loans';

    row['tables'] = [{
            'id': '2.1.',
            'title': 'Old loans by currency'
        },
        {
            'id': '2.2.',
            'title': 'Total old loans in Local Currency'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '3.';
    row['title'] = 'Old bonds';

    row['tables'] = [{
            'id': '3.1.',
            'title': 'Old bonds by currency'
        },
        {
            'id': '3.2.',
            'title': 'Total old bonds in Local Currency'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '4.';
    row['title'] = 'Economic parameters';

    row['tables'] = [{
            'id': '4.1.',
            'title': 'Inflation index'
        },
        {
            'id': '4.2.',
            'title': 'Exchange rates'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '5.';
    row['title'] = 'Financing';

    row['tables'] = [{
            'id': '5.1.',
            'title': 'Investments expenditures'
        },
        {
            'id': '5.2.',
            'title': 'Stand by facilitiy'
        },
        {
            'id': '5.3.',
            'title': 'Short term deposits'
        }
    ];
    content.push(row);


    row = new Array();
    row['id'] = '6.';
    row['title'] = 'New loans';

    row['tables'] = [{
            'id': '6.1.',
            'title': 'New commercial loans'
        },
        {
            'id': '6.2.',
            'title': 'Project loans'
        },
        {
            'id': '6.3.',
            'title': 'Project loans by currency'
        },
        {
            'id': '6.4.',
            'title': 'Total new loans in local currency'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '7.';
    row['title'] = 'New bonds';

    row['tables'] = [{
            'id': '7.1.',
            'title': 'New bonds by currency'
        },
        {
            'id': '7.2.',
            'title': 'Total new bonds'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '8.';
    row['title'] = 'Export credits';

    row['tables'] = [{
            'id': '8.1.',
            'title': 'Export credits by plant'
        },
        {
            'id': '8.2.',
            'title': 'Export credits by currency'
        },
        {
            'id': '8.3.',
            'title': 'Export credits in local currency'
        },
        {
            'id': '8.4.',
            'title': 'Total export credits by currency'
        },
        {
            'id': '8.5.',
            'title': 'Total export credits'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '9.';
    row['title'] = 'Equity and dividend';

    row['tables'] = [{
        'id': '9.1.',
        'title': 'Equity and dividend in local currency'
    }];
    content.push(row);

    row = new Array();
    row['id'] = '10.';
    row['title'] = 'Foreign currencies';

    row['tables'] = [{
            'id': '10.1.',
            'title': 'Foreign currencies requirements'
        },
        {
            'id': '10.2.',
            'title': 'Foreign currencies outstanding'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '11.';
    row['title'] = 'Fuel costs';

    row['tables'] = [{
            'id': '11.1.',
            'title': 'Fuel costs by plant'
        },
        {
            'id': '11.2.',
            'title': 'Total fuel cost'
        }
    ];
    content.push(row);

    row = new Array();
    row['id'] = '12.';
    row['title'] = 'O&M costs';

    row['tables'] = [{
            'id': '12.1.',
            'title': 'O&M costs'
        },
        {
            'id': '12.2.',
            'title': 'Total O&M costs'
        }

    ];
    content.push(row);

    row = new Array();
    row['id'] = '13.';
    row['title'] = 'Depreciation';

    row['tables'] = [{
            'id': '13.1.',
            'title': 'Total depreciation'
        },
        {
            'id': '13.2.',
            'title': 'Depreciation by plant'
        }

    ];
    content.push(row);

    row = new Array();
    row['id'] = '14.';
    row['title'] = 'Decommissioning costs';

    row['tables'] = [{
            'id': '14.1.',
            'title': 'Total decommissioning costs'
        },
        {
            'id': '14.2.',
            'title': 'Decommissioning costs by plant'
        }

    ];
    content.push(row);

    row = new Array();
    row['id'] = '15.';
    row['title'] = 'Sales';

    row['tables'] = [{
            'id': '15.1.',
            'title': 'Sales - currency wise'
        },
        {
            'id': '15.2.',
            'title': 'Sales - product wise'
        },
        {
            'id': '15.3.',
            'title': 'Total revenues from sales'
        }

    ];
    content.push(row);

    row = new Array();
    row['id'] = '16.';
    row['title'] = 'Purchases';

    row['tables'] = [{
            'id': '16.1.',
            'title': 'Purchase - currency wise'
        },
        {
            'id': '16.2.',
            'title': 'Purchase - product wise'
        },
        {
            'id': '16.3.',
            'title': 'Total expenditures on purchases'
        }

    ];
    content.push(row);

    row = new Array();
    row['id'] = '17.';
    row['title'] = 'Others';

    row['tables'] = [{
            'id': '17.1.',
            'title': 'Other incomes'
        },
        {
            'id': '17.2.',
            'title': 'Taxes and royalty'
        },
        {
            'id': '17.3.',
            'title': 'Sources of funds'
        },
        {
            'id': '17.4.',
            'title': 'Application of funds'
        }

    ];
    content.push(row);

    return content;
}

function exportResults() {
    $.ajax({
        url: 'app/results/results_export.php',
        type: 'POST',
        success: function (result) {
            window.location = 'app/results/results_excel.php';
            ShowSuccessMessage("Excel file successfully created")
            hideloader();
        },
        error: function (xhr, status, error) {
            hideloader();
            ShowErrorMessage(error);
        }
    });
}

function getchartresults(g) {
    showloader();
    //   $('#titlechartcard').text($('.modultitle').html());
    var grid = $("#" + g).jqxGrid('getRows');
    var columns = $("#" + g).jqxGrid("columns");
    var cols = [];
    for (var i = 1; i < columns.records.length; i++) {
        cols[i] = columns.records[i].text;
    }
    // var series=[];
    // var startyear = $('#startyear').val();
    // var endyear = $('#endyear').val();
    var unit = '';
    var datachart = [];
    var max = 0;
    for (var i = 0; i < grid.length; i++) {
        var row = {
            'item': grid[i]['item']
        };
        for (var j = 1; j < columns.records.length; j++) {
            row[j + "." + columns.records[j].text] = grid[i][columns.records[j].datafield]
        }
        // for (var j = 0; j < data.length; j++) {
        //     if (max < data[j][allyears[i]]) {
        //         max = data[j][allyears[i]];
        //     }
        // }
        datachart.push(row);
    }
    var series1 = [];
    for (var k = 1; k < cols.length; k++) {
        series1.push({
            dataField: k + "." + cols[k],
            displayText: k + "." + cols[k]
        });
    }
    var settings = {
        title: $('#gridTitle').html(),
        description: "",
        enableAnimations: true,
        showLegend: true,
        padding: {
            left: 20,
            top: 5,
            right: 20,
            bottom: 5
        },
        titlePadding: {
            left: 90,
            top: 10,
            right: 0,
            bottom: 10
        },
        source: datachart,
        xAxis: {
            type: 'basic',
            textRotationAngle: 0,
            dataField: 'item',
            showTickMarks: true,
            tickMarksInterval: 1,
            tickMarksColor: '#888888',
            unitInterval: 1,
            showGridLines: false,
            gridLinesInterval: 1,
            gridLinesColor: '#888888',
            axisSize: 'auto'
        },
        colorScheme: 'scheme01',
        seriesGroups: [{
            type: 'line',
            columnsGapPercent: 50,
            seriesGapPercent: 0,
            valueAxis: {
                visible: true,
                title: {
                    text: unit
                }
            },
            series: series1
        }]
    };
    $('#chartResults').jqxChart(settings);
    hideloader();
}

function getTables(idsector, idtable) {
    $('#reportType').html('');
    var content = getcontenttable();
    for (i = 0; i < content.length; i++) {
        if (content[i]['id'] == idsector) {
            $.each(content[i]['tables'], function (index, value) {
                var title = value['id'] + ' ' + window.lang.translate(value['title']);
                var selected = ''
                if (value['id'] == idtable) {
                    selected = 'selected';
                    $('#title').html(title);
                    $('#titlechart').html(title);
                }
                $('#reportType').append('<option value=' + value['id'] + ' ' + selected + '>' + title + '</option>');
            })
        }
    }
}

function getDataDetail(detailid, detailname, element, rowid) {
    showloader();

    $("#gsFlexGrid").hide();
    $(element).parent().addClass('active').siblings().removeClass('active');
    $.ajax({
        url: urlData,
        data: {
            id: detailname,
            pid: detailid,
            rowid: rowid,
            action: 'get'
        },
        type: 'POST',
        success: function (res) {
            var results = jQuery.parseJSON(res);
            $("#gsFlexGrid").show();
            showData(results);           
            hideloader();

        },
        error: function (xhr, status, error) {
            ShowErrorMessage(error);
            hideloader();
        }
    });
}

function checkval(val) {
    if (val == "" || val == null || val == undefined) {
        val = 0;
    } else {
        val = val;
    }
    return val;
}