
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
}

// EXPANDIR BOX DE EDIÇÃO DE POSTAGEM
function expandBox(object, objectId)
{
    var target = objectId ? document.getElementById(objectId) : target = object.parentNode;
    
    if (object.className === 'button-dropdown button-dropdown-contracted') {
        target.style.maxHeight = 'none';
        target.className = "box";
        object.className = 'button-dropdown button-dropdown-expanded'
        
    } else {
        target.style.maxHeight = '1rem';
        target.className = "box box-expanding";
        object.className = 'button-dropdown button-dropdown-contracted'
    }
}



