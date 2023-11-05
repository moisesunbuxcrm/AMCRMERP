import React from "react"
import ReactDOM from 'react-dom';
import EstimatesApp from './components/EstimatesApp';
import store from './stores/EstimateStore'

window.GetParameterValues = function(param, def) {  
    var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');  
    for (var i = 0; i < url.length; i++) {  
        var urlparam = url[i].split('=');  
        if (urlparam[0] == param) {  
            return urlparam[1];  
        }  
    }  
    return def;
}  

var appElement = $('#estimates-app');
var poid = window.GetParameterValues('poid', null);
var action = window.GetParameterValues('action', poid == null ? 'new' : 'view');
var custid = window.GetParameterValues('socid', null);
var backtopage = window.GetParameterValues('backtopage', null);
if (backtopage != null)
    backtopage = decodeURIComponent(backtopage);

var estimatesApp = ReactDOM.render(
  <EstimatesApp store={store} initialAction={action} initialPOID={poid} custId={custid} backToPage={backtopage} />,
  appElement.get(0)
);

;