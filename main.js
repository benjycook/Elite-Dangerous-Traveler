Array.prototype.map = function(a) {for(var i in this) this[i]= a(this[i])}

var levDist = function(s, t) {
    var d = []; //2d matrix

    // Step 1
    var n = s.length;
    var m = t.length;

    if (n == 0) return m;
    if (m == 0) return n;

    //Create an array of arrays in javascript (a descending loop is quicker)
    for (var i = n; i >= 0; i--) d[i] = [];

    // Step 2
    for (var i = n; i >= 0; i--) d[i][0] = i;
    for (var j = m; j >= 0; j--) d[0][j] = j;

    // Step 3
    for (var i = 1; i <= n; i++) {
        var s_i = s.charAt(i - 1);

        // Step 4
        for (var j = 1; j <= m; j++) {

            //Check the jagged ld total so far
            if (i == j && d[i][j] > 4) return n;

            var t_j = t.charAt(j - 1);
            var cost = (s_i == t_j) ? 0 : 1; // Step 5

            //Calculate the minimum
            var mi = d[i - 1][j] + 1;
            var b = d[i][j - 1] + 1;
            var c = d[i - 1][j - 1] + cost;

            if (b < mi) mi = b;
            if (c < mi) mi = c;

            d[i][j] = mi; // Step 6

            //Damerau transposition
            if (i > 1 && j > 1 && s_i == t.charAt(j - 2) && s.charAt(i - 2) == t_j) {
                d[i][j] = Math.min(d[i][j], d[i - 2][j - 2] + cost);
            }
        }
    }

    // Step 7
    return d[n][m];
}

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

function say(str) {
    var u = new SpeechSynthesisUtterance(str);
    speechSynthesis.speak(u);
}

var recognizing = false;
    var recognition = new webkitSpeechRecognition();
 
  recognition.continuous = false;
  recognition.interimResults = false;
 
  recognition.onstart = function() {
    recognizing = true;
  };
 
  recognition.onerror = function(event) {
    console.log(event.error);
  };
 
  recognition.onend = function() {
    recognizing = false;
  };
 
  recognition.onresult = function(event) {
    var input = event.results[0][0];
    console.log(input);
    if(input.confidence<0.75 && input.confidence>0) {
        say("sorry didn't catch that")

    }

    if(input.transcript.toLowerCase().indexOf("buy")>=0) {
        console.log("BUY")
        $("#sortBy").val("buy");
    }
    if(input.transcript.toLowerCase().indexOf("sell")>=0) {
        console.log("SELL")
        $("#sortBy").val("sell");
    }
    var commArray = [];
    for(var i in $("#commodities option")) {
        var e = $($("#commodities option")[i]);
        commArray.push(e.text().toLowerCase());
    }
    commArray.sort(function(a,b){
        return levDist(a,input.transcript.toLowerCase())-levDist(b,input.transcript.toLowerCase())
    });
    console.log(commArray[0]);
    window.refreshResults()

  };

function recordVoice() {
 

  if (recognizing) {
    recognition.stop();
    return;
  }
  
  recognition.lang = 'en-US';
  recognition.start();
  setTimeout(function(){
      recognition.stop();
  },5000)
}

$(document).ready(function(){
    $("#voice").click(recordVoice)
    
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
        $("#namedlocation").prop('disabled', false);
        $('#location .typeahead').typeahead({
          hint: true,
          highlight: true,
          minLength: 2
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
        var reversed = true;
        if(params.buy_sell=="Sell") reversed = false
        getService(params,function(json){
            $("#dataTable").remove();
            $("#dataView").append('<div id="dataTable"></div>');
            $('#dataTable').columns({
              data:json,
              size:10000,
              sortBy: "Distance"
            });
            $("td:nth-of-type(3)").click(function(){
                $("#namedlocation").val($(this).text())
                refreshResults()
            }).css("cursor","pointer").css("textDecoration","underline")
        })
    }
    window.refreshResults = refreshResults

    $("#sidebarView").on('keydown',function(e){
        if(e.keyCode==13) refreshResults();
    })

    $("#price_query").on('click',function(){
        refreshResults();
    })

    $("select").on('change',function(){
        refreshResults()
    })
    
    
})