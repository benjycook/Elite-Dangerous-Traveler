Array.prototype.map = function(a) {for(var i in this) this[i]= a(this[i])}

var substringMatcher = function(strs) {
  return function findMatches(q, cb) {
    var matches, substrRegex;
 
    // an array that will be populated with substring matches
    matches = [];
 
    // regex used to determine if a string contains the substring `q`
    substrRegex = new RegExp(q, 'i');
 
    // iterate through the pool of strings and for any string that
    // contains the substring `q`, add it to the `matches` array
    $.each(strs, function(i, str) {
      if (substrRegex.test(str)) {
        // the typeahead jQuery plugin expects suggestions to a
        // JavaScript object, refer to typeahead docs for more info
        matches.push({ value: str });
      }
    });
 
    cb(matches);
  };
};
 

function getService(params,callback) {
    var path="http://dev.benjycook.com/EliteDangerousTraveler/server/service.php";
    $.get(path,params,callback)
}

$(document).ready(function(){
    
    
    getService({command:"get_commodities_by_categories"},function(data){
        for(var i in data) {
            var cat = data[i];
            for(var j in cat.commodities) {
                var opt = $("<option></option>");
                opt.text(cat.commodities[j].name);
                opt.attr("value",cat.commodities[j].name)
                $("#commodities").append(opt)
            }
        }
    })


    getService({command:"get_systems"},function(data){
        data.map(function(a){return a.name})
        var systems = data;
        console.log("systems in ",systems);

        $('#location .typeahead').typeahead({
          hint: true,
          highlight: true,
          minLength: 1
        },
        {
          name: 'systems',
          displayKey: 'value',
          source: substringMatcher(systems)
        });
    })

    function refreshResults() {
        var params = {};
        params.buy_sell = $("#sortBy").val()
        params.commodity = $("#commodities").val()
        params.system = $("#namedlocation").val()
        params.command = "price_query";
        getService(params,function(json){
            $("#dataTable").remove();
            $("#dataView").append('<div id="dataTable"></div>');
            $('#dataTable').columns({
              data:json
            });
        })
    }

    $("#sidebarView").on('keydown',function(e){
        if(e.keyCode==13) refreshResults();
    })

    
    
})