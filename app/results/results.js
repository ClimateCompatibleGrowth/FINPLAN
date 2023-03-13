var urlDResults="app/results/results.php";
var d = Cookies('decimal');
if(d===undefined){
    d=3;
}
var decimal='d'+d.toString();
var charttype='line';
$(document).ready(function() {   
    $("#reportType").on('change', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        generateresults(this.value);
    });

    $("#decUp").on('click', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            window.d++;
            window.decimal = 'd' + parseInt(window.d);
            $('#gridResults').jqxGrid('updateBoundData', 'cells');
        });
        $("#decDown").on('click', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            window.d--;
            window.decimal = 'd' + parseInt(window.d);
            $('#gridResults').jqxGrid('updateBoundData', 'cells');
        });

        $(".changeChart").on('click', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            window.charttype=$(this).attr('id');

            var chart1 = $('#chartResults').jqxChart('getInstance');
            chart1.seriesGroups[0].type = window.charttype;
            chart1.update();
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var target = $(e.target).attr("href") // activated tab
            if (target=="#chart"){
                generatechart($("#reportType").val(), $('#change').html());
            }
          });
});

function generateresults(id) {
    showloader();
    $.ajax({
        url: urlDResults,
        data: {
            action: 'table',
            id: id
        },
        type: 'POST',
        success: function (result) {
            $("#table14").html("");
            $('#resultModal').modal('show');
            $("#reportType").val(id);
            $('#gridTitle').html($("#reportType option:selected").text());          
            result=jQuery.parseJSON(result);
            $('#tabs a[href="#table"]').tab('show');
            showDataGrid(result, id);
            if(id=="1.4.")
            $("#table14").html("<table class='table'><tr><td style='width:150px'>NPV project</td><td>"+result['npv']+"</td></tr><tr><td>IRR</td><td>"+result['irr']+" %</td></tr></table>");
            hideloader();
        },
        error: function (xhr, status, error) {
            hideloader();
            ShowErrorMessage(error);
        }
    });
}

function generatechart(id) {
    showloader();
    $.ajax({
        url: urlDResults,
        data: {
            action: 'table',
            id: id
        },
        type: 'POST',
        success: function (result) {
                $('#reportType').val(id);
                $('#chartTitle').html($("#reportType option:selected").text());    
                results=jQuery.parseJSON(result);
                $("#chartResults").html("");
                var data=results['result']; 
                var series=results['series']; 
                var allyears=results['allyears'];
                var unit=results['unit']; 
                var datachart=[];
                var max=0;
                for(var i=0; i<allyears.length;i++){
                    var row={'item':allyears[i]};
                    for(var j=0;j<data.length;j++){
                        if(data[j]['chart']){
                        row[data[j]['item']]=data[j][allyears[i]];
                        if(max<data[j][allyears[i]]){
                            max=data[j][allyears[i]];
                        }
                    }
                    }
                    datachart.push(row);
                }

                var series1=[];
                for(var k=0;k<series.length;k++){
                    series1.push({ dataField: series[k], displayText: series[k]});
                }
                    var settings = {
                        title: $('#title').text().replace(id,""),
                        description:"",
                        enableAnimations: true,
                        showLegend: true,
                        padding: { left: 20, top: 5, right: 20, bottom: 5 },
                        titlePadding: { left: 90, top: 10, right: 0, bottom: 10 },
                        source: datachart,
                        borderLineColor: '#ffffff',
                        xAxis:
                            {
                                type:'basic',
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
                        seriesGroups:
                            [
                                {
                                    type: window.charttype,
                                    columnsGapPercent: 50,
                                    seriesGapPercent: 0,
                                    valueAxis:
                                    {
                                        visible: true,
                                        title: { text: unit }
                                    },
                                    series: series1
                                }
                            ]
                    };
                    $('#chartResults').jqxChart(settings);
                hideloader();
        },
        error: function (xhr, status, error) {
            hideloader();
            ShowErrorMessage(error);
        }
    });
}

//get data
function showDataGrid(result, id) {
    var allyears=result['allyears'];
    var res=result['result'];
    var datastructure = [];
    datastructure.push({ name: 'item', map: 'item', type: 'string' });
    datastructure.push({ name: 'unit', map: 'unit', type:'string' });
    datastructure.push({ name: 'css', map: 'css', type:'string' });
    if(id=="1.4.")
    datastructure.push({ name: allyears[0]-1, map: (allyears[0]-1).toString(),  type:"number" });

    for (var i = 0; i < allyears.length; i++) {
        datastructure.push({ name: allyears[i], map: allyears[i].toString(),  type:"number" });
    }
    var source =
    {
        localdata: res,
        datatype: "array",
        datafields:datastructure
    };
    var dataAdapter = new $.jqx.dataAdapter(source);
    var cellclassname = function (row, column, value, data) {
            return data.css;
    };

    let cellsrenderer = function(row, columnfield, value, defaulthtml, columnproperties) {
        if((value!='' || value=='0') && value!='OK' && value!='Ok' && value!='Pb'){ 
        var formattedValue = $.jqx.dataFormat.formatnumber(value, window.decimal);
        return '<span style="margin: 4px; float:right; ">' + formattedValue + '</span>';
        }
    }; 

    var plcolumns = [];
    plcolumns.push({ text: result['unit'], datafield: 'item', align: 'right', cellsalign: 'left', width: '300px', editable: false, cellclassname: cellclassname });
    if(id=="1.4.")
    plcolumns.push({ text: allyears[0]-1, datafield: allyears[0]-1, align: 'right', cellsalign: 'center', width: '100px', editable: false, cellsformat: decimal,
        cellsrenderer:cellsrenderer, 
        cellclassname: cellclassname  });

    for (i = 0; i < allyears.length; i++) {
        plcolumns.push({
            text: allyears[i], datafield: allyears[i], cellsalign: 'center', align: 'right', width:'100px', editable: false, cellsformat: decimal, 
            cellsrenderer:cellsrenderer, 
            cellclassname: cellclassname 
        });
}

$("#gridResults").jqxGrid(
    {
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
        columns: plcolumns

    });
}

function ArrayToObject(arr){
    var obj = {};
    for (var i = 0;i < arr.length;i++){
        obj[arr[i]] = arr[i];
    }
    return obj
}


function exportResults(){
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