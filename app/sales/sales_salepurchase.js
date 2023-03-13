var url = "app/sales/sales_salepurchase.php";
$(document).ready(function () {
    getDataSalesPurchases();
    var typesp = Cookies.get('id');
    Cookies("typesp", typesp);
    $('#' + typesp).parent().addClass('active');
});

function getDataSalesPurchases() {
    $.ajax({
        url: url,
        type: 'POST',
        data: {
            action: "get"
        },
        success: function (results) {
            $("#tblsales tbody").html("");
            var res = JSON.parse(results);
            //console.log('res ', res);
            var salespurchase = res['ctData'];
            var producttypes = res['producttypes'];
            var currencies = res['currencies'];
            var salespurchase = Object.keys(salespurchase).map(key => {
                return salespurchase[key];
            });

            for (var i = 0; i < salespurchase.length; i++) {

                var row = $.grep(producttypes, function (v) {
                    return v.id === salespurchase[i].Name;
                })[0];
                
                var name = row['value'] + '(' + row['unit'] + ')';
                var perunit = row['sunit'];

                if(currencies != ''){
                    var currencyName = $.grep(currencies, function (v) {
                        return v.id === salespurchase[i].TradeCurrency;
                    })[0]['value'];
                }

                $("#tblsales tbody").append("<tr>\
                <td><a class='pointer' id='salepurchase_detail" + salespurchase[i].id + "' onclick='getDataSalePurchase(" + salespurchase[i].id + ", \""+row['id']+"\",\""+perunit+"\")'><i class='material-icons btnblue' data-lang-content='false' data-toggle='tooltip' lang='en' data-placement='top' title='' data-original-title='Edit'>edit</i></a></td>\
                <td>" + name + "</td>\
                <td>" + salespurchase[i].ClientName + "</td>\
                <td>" + currencyName + "</td>\
                <td>" + salespurchase[i].Amount + " " + check(salespurchase[i].AmountFixed) + "</td>\
                <td>" + salespurchase[i].PriceBase + "</td>\
                <td><a class='pointer' onclick='deleteSalePurchase(" + salespurchase[i].id + ")'><i class='material-icons btnred' data-lang-content='false' data-toggle='tooltip' lang='en' data-placement='top' title='' data-original-title='Delete'>close</i></td>\
                </tr>");
            }
        }
    })
}

function getDataSalePurchase(id, producttype, perunit) {
    //console.log('params ', id, producttype, perunit)
    Cookies("salepurchaseid", id);
    Cookies("id", "sales_salepurchasedetail");
    $("#salepurchasecontent").load('app/data/data.html', function () {
        $("#tabs").hide();
        $("#gridTitle").html("Details");
        $("#savedata").attr("onclick", "saveDataSalePurchase()");
        $("#producttypeid").val(producttype);
        $("#perunitid").val(perunit);
    });
    if (id != 0) {
        var thisClosest = $("#salepurchase_detail" + id).closest('tr');
        thisClosest.addClass("readonly1").siblings().removeClass("readonly1");
    }
}

function deleteSalePurchase(id) {
    bootbox.confirm({
        title: "MESSAGE",
        message: "Are you sure that you want to DELETE data " + id + "?",
        buttons: {
            cancel: {
                label: '<i class="material-icons btnred link mti17">close</i> Close'
            },
            confirm: {
                label: '<i class="material-icons link mti17">done</i> Confirm'
            }
        },
        callback: function (resultcs) {
            if (resultcs) {

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        action: "delete",
                        id: id,
                    },
                    success: function (results) {
                        getDataSalesPurchases();
                    }
                })
            }
        }
    })

}

function check(value) {
    if (value == undefined) {
        return ""
    } else {
        return value;
    }
}