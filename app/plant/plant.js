var url="app/plant/plant.php";
$(document).ready(function () {
    getData();

    $('.add').click(function() {
        $(this).addClass("readonly1").siblings().removeClass("readonly1");
    })
});

function getData(){
    $.ajax({
        url: url,
        type: 'POST',
        data:{
            action: "get"
      },
        success: function (results) {
            $("#plants tbody").html("");
            var res = JSON.parse(results);
            var plants=res['data'];
            var plants = Object.keys(plants).map(key => {
                return plants[key];
            });
            //console.log(plants);
            var curr=res['caseData']['CurTypeSel'].split(",");
    
            for(var i=0; i<plants.length;i++){
            $("#plants tbody").append("<tr class='add'>\
                <td><a class='pointer' id='plantgeneral_"+plants[i].id+"' onclick='getDataPlant(\"plant_general\",\""+curr[0]+"\","+plants[i].id+","+i+")'><i class='material-icons btnblue' data-lang-content='false' data-toggle='tooltip' lang='en' data-placement='top' title='' data-original-title='Edit plant'>edit</i></a></td>\
                <td>"+plants[i].name+"</td>\
                <td>"+plants[i].plantType+"</td>\
                <td>"+plants[i].Status+"</td>\
                <td>"+plants[i].Ownership+"</td>\
                <td>"+plants[i].unitSize+"</td>\
                <td>"+plants[i].FOyear+"</td>\
                <td>"+plants[i].CPeriod+"</td>\
                <td>"+plants[i].Plantlife+"</td>\
                <td>"+plants[i].CurTypeSel+"</td>\
                <td><a class='pointer' onclick='deletePlant("+plants[i].id+")'><i class='material-icons btnred' data-lang-content='false' data-toggle='tooltip' lang='en' data-placement='top' title='' data-original-title='Delete plant'>close</i></td>\
                </tr>");
            }
        }
    })
}

function newPlant(){
    Cookies("id", "plant_general");
    $("#planttabs").hide();
    $("#plantcontent").load('app/plant/plant_general.html',function(){
        $("#plant_general_header").html("<div class='card'> \
        <div class='cardtitle backwhite'> \
            <span lang='en'>Plant details</span> \
            <a onclick='savePlant()' class='pull-right' id='savePlant'> \
            <i class='material-icons btngreen carddivider' data-toggle='tooltip' title='Save data' data-lang-content='false' lang='en'>save</i></a> \
        </div>");
    $("#savePlant").show();
    });
}

function getDataPlant(obj, curr, plantid, i){
    if(obj!==undefined)
        Cookies("id", obj);

    $("#planttabs").show();

    if(plantid!==undefined){
        Cookies("plantid", plantid);
            var thisClosest = $("#plantgeneral_" + plantid).closest('tr');
            thisClosest.addClass("readonly1").siblings().removeClass("readonly1");
    }
        
    if(curr!==undefined)
        Cookies("curr", curr);
    $('#'+obj).parent().addClass('active');
    $("#plantcontent").load('app/data/data.html');
}

function deletePlant(id){

    bootbox.confirm({
        title: "MESSAGE",
        message: "Are You sure that You want to DELETE Plant " + id + "?",
        buttons: {
            cancel: { label: '<i class="material-icons btnred link mti17">close</i> Close'},
            confirm: { label: '<i class="material-icons link mti17">done</i> Confirm'}
        },
        callback: function (resultcs) {
            if (resultcs) {

    $.ajax({
        url: url,
        type: 'POST',
        data:{
            action: "delete",
            id:id
      },
        success: function (results) {
            getData();
        }
    })
}
        }
    })
    
}

function savePlant(){
    if(!(required("name", "Name is required!") &&
    required("unitSize","Unit size is required!") &&
    required("FOyear", "First operation year is required!") &&
    required("CPeriod", "Construction period is required!") &&
    required("Plantlife", "Construction period is required!")))
    return false;

    var product=$('input[name=product]:checked');

    if(product.length==0){
        $('#tdproduct').parent().addClass('has-error');
        ShowErrorMessage("Product is required!");
        return false;
    }

    var object = {};
    var inputs = $("#plant_general_content").find("input, select");
    var curtypesel="";
   for(var a=0; a<inputs.length; a++){
       if(inputs[a]["type"]=="radio" && inputs[a]["checked"]==true)
       object[inputs[a]["name"]]=inputs[a]["value"];

       if(inputs[a]["type"]=="text" && inputs[a]["disabled"]==false)
       object[inputs[a]["id"]]=inputs[a]["value"];

       if(inputs[a]["type"]=="checkbox" && inputs[a]["checked"]==true)
        curtypesel=curtypesel+inputs[a]["value"]+",";
        
       if(inputs[a]["type"]=="select-one")
       object[inputs[a]["id"]]=inputs[a]["value"];

       if(inputs[a]["type"]=="hidden")
       object[inputs[a]["id"]]=inputs[a]["value"];
   }

   if(curtypesel!="")
        object["CurTypeSel"]=curtypesel.slice(0,-1);

    $.ajax({
        url: url,
        type: 'POST',
        data:{
            'action': "update",
            'data': object
      },
        success: function (results) {
            getData();
            var message="Data saved succefuly";
            if(object['id']==""){
                $("#plantcontent").html("");
                message="Plant added succefuly";
            }
            
            ShowSuccessMessage(message);
        }
    })
}