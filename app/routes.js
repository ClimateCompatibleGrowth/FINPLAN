crossroads.addRoute('/', function() {
    Cookies("id", "home");
    $(".page-content").load('layout/home.html');
    getPageTitle();
});

crossroads.addRoute('/ManageStudy', function() {
    crossroads.ignoreState = true;
    Cookies("id", "study");
    $(".page-content").load('app/study/study.html');
    getPageTitle();
    $("#finplanmenu").hide();
});

crossroads.addRoute('/PlantSourcesFinancing/', function() {
    Cookies("id", "geninf");
    crossroads.ignoreState = true;
    $(".page-content").load('app/plantsourcesfinancing/sourcesfinancing.html');
    getPageTitle();
});

crossroads.addRoute('/GetData/{id}', function(id) {
    Cookies("id", id);
    $(".page-content").load('app/data/data.html');
    getPageTitle();
});

crossroads.addRoute('/Begin/{name}', function(name) {
    Cookies("id", 'general_information');
    Cookies("titlecs", decodeURI(name));
    $(".page-content").load('app/data/data.html');
    $("#finplanmenu").show();
    getPageTitle();
});

crossroads.addRoute('/Plant/', function() {
    Cookies("id", 'plant');
    crossroads.ignoreState = true;
    $(".page-content").load('app/plant/plant.html');
    getPageTitle();
});

crossroads.addRoute('/SalePurchase/{id}', function(id) {
    Cookies("id", id);
    crossroads.ignoreState = true;
    $(".page-content").load('app/sales/sales_salepurchase.html');
    getPageTitle();
});

crossroads.addRoute('/Calculation', function() {
    showloader();
    Cookies("id","results");
    $.ajax({
        url: 'app/calculation/calculation.php',
        type: 'POST',
        success: function(result) {
            ShowSuccessMessage("Calculation finished succesfuly");
            $(".page-content").load('app/results/results.html');
            hideloader();
        },
        error: function(data) {
            ShowErrorMessage("Error");
            //console.log(data);
            hideloader();
        }
    });
    getPageTitle();
});

crossroads.addRoute('/Results', function() {
    Cookies("id", "results");
    $(".page-content").load('app/results/results.html');
    getPageTitle();
});

crossroads.addRoute('/ResultsIntermediate', function() {
    Cookies("id", "intermediate");
    $(".page-content").load('app/results/results_intermediate.html');
    getPageTitle();
});

crossroads.addRoute('/Logout', function() {
    $.ajax({
        url: "auth/login/logout.php",
        async: true,
        type: 'POST',
        success: function (data) {
            if ($.trim(data) === "1") {
                window.location = 'index.html';
            }
        }
    });
});

crossroads.addRoute('/Data', function() {
    crossroads.ignoreState = false;
    localStorage.setItem("activePage",  null);
    $(".page-content").load('app/data/data.html');
});

crossroads.addRoute('/Users', function() {
    Cookies("id", "accounts");
    $(".page-content").load('auth/users/users.html');
    getPageTitle();
});
crossroads.addRoute('/Info', function() {
    Cookies("id", "about");
    $(".page-content").load('layout/info.html');
    getPageTitle();
});

crossroads.bypassed.add(function(request) {
    console.error(request + ' seems to be a dead end...');
});

//Listen to hash changes
window.addEventListener("hashchange", function() {
    var route = '/';
    var hash = window.location.hash;
    if (hash.length > 0) {
        route = hash.split('#').pop();
    }
    crossroads.parse(route);
});

// trigger hashchange on first page load
window.dispatchEvent(new CustomEvent("hashchange"));
