var urlData="app/data/data.php";
var charttype='line';
var d = getdecimal();
var decimal='d'+d.toString();

    $(document).ready(function () {
        showloader();
        var id = Cookies('id');
        var folder=getGroup(id);
        //console.log('folder ', folder)
        //console.log('id ', id)
        
        $.getScript('app/'+folder+'/' + id + '.js', function () { 
            $.ajax({
                url: urlData,
                data: { id: id, action: 'get' },
                type: 'POST',
                success: function (res) {
                    var results = jQuery.parseJSON(res);
                    $("#startyear").val(results["startYear"]);
                    $("#endyear").val(results["endYear"]);
                    $("#baseCurrency").val(results["baseCurrency"]);
                    //console.log('results ', results)
                    showData(results);
                    $('#dataNotes').val(results['datanotes']);
                    hideloader();
                },
                error: function (xhr, status, error) {
                    ShowErrorMessage(error);
                    hideloader();
                }
            });
        });

        let group=getGroup(id);
       if(group!="plant"){
            getTabs(tabs[group], translates[group]);
        }
        $('#gridTitle').html(translates[group][tabs[group].indexOf(id)]);
        $('#' + id).parent().addClass('active').siblings().removeClass('active');

        $('#export').attr('download', id);

        $(".changeChart").on('click', function(e){
            e.preventDefault();
            e.stopImmediatePropagation();
            window.charttype=$(this).attr('id');

            var chart1 = $('#chartResults').jqxChart('getInstance');
            chart1.seriesGroups[0].type = window.charttype;
            chart1.update();
        });
    });

    function showChart(g){
        $("#chartModalInput").modal('show');
            $('#chartModalInput').on('shown.bs.modal', function (e) {
                getchartresults(g);
        })
    }

    function loadInfo() {
        var infoelement = Cookies("id");
        $.ajax({
            type: "GET",
            url: "app/info.xml",
            dataType: "xml",
            success: function (xml) {
                $("#infotext").html($(xml).find(infoelement).text());
                $('#infoModal').appendTo("body").modal('show');
            }
        });
    }

    function saveData() {
        var rows = $('#gsFlexGrid').jqxGrid('getrows');
        var cols = $('#gsFlexGrid').jqxGrid('columns');
        var object = {};
        var inputs = $("#additionalData").find("input, select");
       for(var a=0; a<inputs.length; a++){
        if(inputs[a]["type"]=="radio" && inputs[a]["checked"]==true)
        object[inputs[a]["name"]]=inputs[a]["value"];

        if(inputs[a]["type"]=="text" && inputs[a]["disabled"]==false)
        object[inputs[a]["id"]]=inputs[a]["value"];

        if(inputs[a]["type"]=="checkbox" && inputs[a]["checked"]==true)
        object[inputs[a]["id"]]=inputs[a]["value"];

        if(inputs[a]["type"]=="select-one")
        object[inputs[a]["id"]]=inputs[a]["value"];
           
        if(inputs[a]["type"]=="hidden")
        object[inputs[a]["id"]]=inputs[a]["value"];
       }
        var id = Cookies('id');
            object['sid'] = '1';

        //D
        if (rows!==undefined && rows.length>0) {
            cols=cols.records;
            for (var i = 1; i < cols.length; i++) {
                for (var j = 0; j < rows.length; j++) {
                    if(rows[j][cols[i]['datafield']] && cols[i]["editable"]==true){
                        if(id=='plant_investments' || id=='plant_sources'){
                            var item=rows[j]['item'].split(":");
                            object[cols[i]['datafield'] + '_' + item[0]] = rows[j][cols[i]['datafield']];
                        }else{
                            object[cols[i]['datafield'] + '_' + rows[j]['item'].substring(0,4)] = rows[j][cols[i]['datafield']];
                        }
                    }
                }
            }
        }
        datanotes=$('#dataNotes').val();

        // console.log('cols ', cols)
        // console.log('rows ', rows)
        // console.log('JSON.stringify(object) ', object)
        //return false;
        $.ajax({
            url: urlData,
            data: {
                'data': JSON.stringify(object),
                'datanotes':datanotes,
                'id': id,
                'action': 'edit'
            },
            type: 'POST',
            success: function (result) {
                ShowSuccessMessage("Data saved successfully");
            },
            error: function (xhr, status, error) {
                ShowErrorMessage(error);
            }
        });
    }

    function getchartresults(g) {
        showloader();
        $('#titlechartcard').text($('.modultitle').html());
        var grid = $("#"+g).jqxGrid('getRows');
        var columns = $("#"+g).jqxGrid("columns");
        var cols=[];
        for (var i = 1; i < columns.records.length; i++) {
            cols[i] = columns.records[i].text;
        }
        //console.log(columns);
        //console.log(grid);
        var series=[];
        var startyear = $('#startyear').val();
        var endyear = $('#endyear').val();
        var unit='';
        var datachart = [];
        var max = 0;
        for (var i = 0; i < grid.length; i++) {
             var row = { 'item': grid[i]['item'] };
             for (var j = 1; j < columns.records.length; j++) {
                row[j+"."+columns.records[j].text]=grid[i][columns.records[j].datafield]
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
            series1.push({ dataField: k+"."+cols[k], displayText: k+"."+cols[k] });
        }
        var settings = {
            title: $('#gridTitle').html(),
            description: "",
            enableAnimations: true,
            showLegend: true,
            padding: { left: 20, top: 5, right: 20, bottom: 5 },
            titlePadding: { left: 90, top: 10, right: 0, bottom: 10 },
            source: datachart,
            xAxis:
            {
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
            seriesGroups:
                [
                    {
                        type: 'line',
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
    }