var url="app/data/data.php";
//get data
function showData(results) {
    $("#additionalData").load("app/general/general_information.html", function(){
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();

        //console.log('RESULS ', results)
        var geninf=results['geninf'];
        var currencies=results["currencies"];
        $('#studyNameTitle').html(geninf['studyName']);
        $('#id').val(geninf['id']);
        $('#studyName').val(geninf['studyName']);
        $('#startYear').val(geninf['startYear']);
        $('#endYear').val(geninf['endYear']);
        $('#Desc').val(geninf['note']);
        var boxA=[];
        var boxB=[];
        var CurTypeSel=geninf['CurTypeSel'].split(",");
        for(var i=0; i<currencies.length;i++){
            $('#Currency').append("<option value="+currencies[i]['id']+">"+currencies[i]['value']+"</option>"); 
            if (CurTypeSel.indexOf(currencies[i]['id']) >= 0) {
                boxB.push({"id":currencies[i]['id'], "value":currencies[i]['value']});
            }else{
                boxA.push({"id":currencies[i]['id'], "value":currencies[i]['value']});
            }
        }
        $("#listBoxA").jqxListBox({ filterable: true, allowDrop: true, allowDrag: true, source: boxA, height: 300, theme: 'metro' });
        $("#listBoxB").jqxListBox({ allowDrop: true, allowDrag: true, source: boxB, height: 300, theme: 'metro' });
        $('#Currency').val(geninf['baseCurrency']);
        $('#studyNameTitle').html(geninf['studyName']);
    })
}
function saveData(){
    var studyName=$('#studyName').val();
    if(!studyName){
        $('#studyName').parent().addClass('has-error');
        ShowErrorMessage("Study name is required!");
        return false;
    }
    var startyear=$('#startYear').val();
    if(!startyear){
        $('#startYear').parent().addClass('has-error');
        ShowErrorMessage("Start year is required!");
        return false;
    }

    var endyear=$('#endYear').val();
    if(!endyear){
        $('#endYear').parent().addClass('has-error');
        ShowErrorMessage("End year is required!");
        return false;
    }

    if (clearName(studyName)) {
        $('#fgstudyname').addClass('has-error');
        ShowErrorMessage("Allowed characters [a-Z] [0-9] [-_]!");
        return false;
    }

    var listBoxB=$("#listBoxB").jqxListBox('getItems');
    var CurTypeSel="";
    for (var a=0; a<listBoxB.length; a++ ){
        CurTypeSel+=","+listBoxB[a].value;
    }
        $.ajax
            ({
                type: "POST",
                url: url,
                data:{
                      id:$('#id').val(),
                      studyName: studyName,
                      startYear: startyear,
                      endYear: endyear,
                      note:$('#Desc').val(),
                      studyType: $('#studyType').val(),
                      baseCurrency: $('#Currency').val(),
                      CurTypeSel: CurTypeSel,
                      timeOpt: "A",
                      action: "edit",
                      idaction:"general_information"
                },
                success: function () {ShowSuccessMessage('Data saved successfully'); $('#fgstudyname').removeClass('has-error'); },
                failure: function () { ShowErrorMessage("Error!"); }
            });
        
}

function validYears(n){
    $('#'+n.id).val(n.value.replace(/[^\d,]+/g, ''));
 }