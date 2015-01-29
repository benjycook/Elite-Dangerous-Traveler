Array.prototype.map = function(a) {for(var i in this) this[i]= a(this[i])}



function getService(endpoint,action,params,callback) {
    var path="";
    params["action"]=action;
    $.get(path+endpoint+".php",params,callback)
}

$(document).ready(function(){
    var json = [{"Location":"row1", "Uncertainty (+/-)":"row1", "Distance":"row1","Price":"row1","Historic":"row1"}]; 
    $('#dataTable').columns({
      data:json
    });
    
    getService("commodities","list",{},function(){
        
    })
    
})