
function checkStartApplicationForm(form){
    const userAdmin = form.userAdmin.value;
    const emailAdmin = form.emailAdmin.value;
    const passwordAdmin = form.passwordAdmin.value;
    const passwordRepeat = form.passwordRepeat.value;
    const dbName = form.dbName.value;
    const dbUserName = form.dbUserName.value;
    const dbPassword = form.dbPassword.value;

    if(!userAdmin) { alert("The Name field cant be empty!"); return false; }
    if(!emailAdmin) { alert("The Email field cant be empty!"); return false; }
    if(!passwordAdmin) { alert("The Password field cant be empty!"); return false; }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailAdmin)) { alert("Invalid email!"); return false; }
    if (passwordAdmin !== passwordRepeat) { alert("the password does not correspond with the repetition!"); return false; }

    if(!dbName) { alert("The Database name field cant be empty!"); return false; }
    if(!dbUserName) { alert("The Database user name field cant be empty!"); return false; }
    if(!dbPassword) { alert("The Database password field cant be empty!"); return false; }

    return true;
}

function checkRegisterForm(form) {
    const name = form.name.value;
    const email = form.email.value;
    const password = form.password.value;
    const repeatPassword = form.passwordRepeat.value;

    if(!name) { alert("The name field cant be empty!"); return false; }
    if(!email) { alert("The email field cant be empty!"); return false; }
    if(!password) { alert("The password field cant be empty!"); return false; }
    if(!repeatPassword) { alert("The password repeat field cant be empty!"); return false; }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { alert("Invalid email!"); return false; }
    if (password !== repeatPassword) { alert("the password does not correspond with the repetition!"); return false; }

    return true;
}

function CheckRequiredFieldsInForm(event, fields) {
    const form = event.target;
    const elements = form.elements;
    for (var i=0; i<elements.length; i++) {        
        var item = elements.item(i);
        
       if (fields.includes(item.name)) {
           if (item.value === "") {
               alert("You must fill in mandatory fields!");
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
        
        if (obj.tagName === "FORM") {
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
    let target = objectId ? document.getElementById(objectId) : target = object.parentNode;

    if (object.className === 'button-dropdown button-dropdown-contracted') {
        target.className = "box";
        object.className = 'button-dropdown button-dropdown-expanded'
        
    } else {
        target.className = "box box-expanding";
        object.className = 'button-dropdown button-dropdown-contracted'
    }
}

// EXPANDIR CAIXA DE TEXT
function expandTextarea(objectId,increase = 100)
{
    var textarea = document.getElementById(objectId);
    // obtém a altura atual
    var atualHeight = textarea.clientHeight;
    // configura nova altura
    textarea.style.height = (atualHeight+increase)+'px';
}

/**
 *  WINDOW ON LOAD
 */
window.onload = () => {

  /**
   * contagem de caracteres no campos description do thing type
   * @type {HTMLCollectionOf<Element>}
   */
  const thingDescriptions = document.getElementsByClassName('thing-description');
  if (thingDescriptions) {
    for (let i=0; i < thingDescriptions.length; i++) {
      const maxLenght = 255;
      const legend = thingDescriptions[i].firstChild;
      const input = thingDescriptions[i].lastChild;
      const spanCount = document.createElement('span');
      spanCount.style.fontSize = "0.9em";
      const textCount = document.createTextNode(" Max: 255 ch");
      spanCount.appendChild(textCount);
      spanCount.innerHTML = setCount(maxLenght - input.value.length);
      legend.appendChild(spanCount);
      //
      input.addEventListener('keyup', () => {
        spanCount.innerHTML = setCount(maxLenght - input.value.length);
      })

      function setCount(charsRemaining) {
        const color = charsRemaining < 0 ? '#ff6161' : 'inherit';
        return " max chars: <span style='color:"+color+"'>"+charsRemaining+"</span>"
      }
    }
  }
  /**   *    */
  const buttonDropdown = document.getElementsByClassName('button-dropdown');
  if (buttonDropdown) {
    for (let i=0; i < buttonDropdown.length; i++) {
      const element = buttonDropdown[i];
      element.addEventListener('click', () => {
        const top = element.getBoundingClientRect().top - 10;
        window.scrollTo({ top: top, behavior: 'smooth' });
      })
    }
  }
}


