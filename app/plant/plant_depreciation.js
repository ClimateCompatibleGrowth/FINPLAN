function showData(results) {
    $("#additionalData").load("app/plant/plant_depreciation.html", function () {
        $("#chartGrid").hide();
        $("#decDown").hide();
        $("#decUp").hide();
        $("#exportgrid").hide();
        var cfdata = results['cfData'];
        setValues(cfdata);

        var iddata = 0;
        if (cfdata['id'])
            iddata = cfdata['id'];
        Cookies('iddata', iddata);
    })
}

function setReadOnly(input1, input2) {
    var inputs = $("#additionalData").find("input, select");
    for (var a = 0; a < inputs.length; a++) {
        if (inputs[a]["type"] == "text")
            $("#" + inputs[a]["id"]).prop("readonly", true);
    }
    $('#' + input1).removeAttr('readonly');
    if (input2 != "")
        $('#' + input2).removeAttr('readonly');
}