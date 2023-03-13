var url="app/plant/plant.php";
function showData(results) {
    $("#additionalData").load("app/plant/plant_general.html", function(){
    showloader();
    $("#chartGrid").hide();
    $("#decDown").hide();
    $("#decUp").hide();
    $("#exportgrid").hide();
    
    Cookies("id", 'plant_general');
    var id=Cookies.get("plantid");

        $.ajax({
            url: url,
            data: {
                action: 'getplant',
                id: id
            },
            type: 'POST',
            success: function (results) {
                var res = JSON.parse(results);
                var plant=res['data'];
                setValues(plant);

                $("#plantType option").filter(function() {
                    return $(this).text() == plant['plantType'];
                }).prop('selected', true);

                var currtypesel=plant['CurTypeSel'].split(',');
                for(var i=0; i<currtypesel.length; i++){
                    $("#"+currtypesel[i]).prop('checked', true);
                }
                if(plant.id==undefined)
                plant.id="";
                $("#id").val(plant.id);

                $("#savePlant").show();

                $("#savedata").attr("onclick","savePlant()");


                hideloader();
            },
            error: function (xhr, status, error) {
                hideloader();
                ShowErrorMessage(error);
            }
        });
    })
}