
function CheckRequiredFieldsInForm(event, fields) 
{
    const form = event.target;
    
    const elements = form.elements;
    
    for (var i=0; i<elements.length; i++) {        
        var item = elements.item(i);
        
       if (fields.includes(item.name)) {
           if (item.value == "") {
               alert("Você precisar preencher todos os campos obrigatórios!");
               return false;
           }
       }
    }
}

/**
 * register history on ADVERTISING contracts
 * 
 * @param {type} obj
 * @returns {Boolean}
 */
function setHistory(obj){
    var summary = prompt("Descreva a sua ação:");
    
    if (summary === null) {
        return false;
        
    } else {
        summary = summary === '' ? "No description" : summary;
        
        if (obj.tagName == "FORM") {
            obj.setAttribute('action', obj.getAttribute('action')+'?summaryHistory='+summary); 
            
        } else {
            obj.setAttribute('formaction', obj.getAttribute('formaction')+'?summaryHistory='+summary); 
        }
        
        return true;
    }
};



