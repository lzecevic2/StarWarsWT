// Funkcije za validacije formi - login, kontakt i sign up

function showMessage(message, p) // Y U NO WORK??
{
    p.style.paddingTop = p.style.paddingBottom = "1.5%";
    p.style.marginLeft = "-50px";
    p.style.color = "white";
    p.innerHTML(message);
}

function validacijaLogin()
{
    var p = document.getElementById("warningMessage");
    var email = document.getElementById("emailLogin").value;
    var password = document.getElementById("passwordLogin").value;
    if(!email && !password)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "Your email or password were incorrect.";
        return false;
    }
    if(!email)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "That's not a valid email address. Please try again.";
        return false;
    }
    if(!password)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "You forgot to enter your password!";
        return false;
    }

    return true;
}

function validacijaKontakt()
{
    var name = document.getElementById("nameContact").value;
    var email = document.getElementById("emailContact").value;
    var message = document.getElementById("message").value;
    var p = document.getElementById("warningMessage");    

    if(!name)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "You forgot to enter your name!";
        return false;
    }
    if(!email)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "That's not a valid email address. Please try again.";
        return false;
    }
    if(!message)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "Please enter your message.";
        return false;     
    }

    return true;
}

function validacijaRegistracija()
{
    var name = document.getElementById("nameRegister").value; 
    var surname = document.getElementById("surname").value;
    var email = document.getElementById("emailRegister").value;
    var password = document.getElementById("password1").value;
    var passwordRepeat = document.getElementById("password2").value;
    var p = document.getElementById("warningMessage");    
    
    if(!name)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "You forgot to enter your name!";
        return false;
    }
    if(!surname)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "You forgot to enter your last name!";
        return false;
    }
    if(!email)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "That's not a valid email address. Please try again.";
        return false;
    }
    if(!password || !password)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "You forgot to enter your password!";
        return false;
    }
    if(password !== passwordRepeat)
    {
        p.style.paddingTop = p.style.paddingBottom = "1.5%";
        p.style.marginLeft = "-50px";
        p.style.color = "white";
        p.innerHTML = "Passwords don't match!";
        return false;
    }

    return true;
}

var meniClicked = false; 

function showMenu()
{
    if(!meniClicked) 
    {
        document.getElementById("dropdownMeni").style.display = "block";
        meniClicked = true;
    }    
    else hideMenu();
}

function hideMenu()
{
    document.getElementById("dropdownMeni").style.display = "none";
    meniClicked = false;
}

function zoomImage()
{
    // var trigger = event.target;
    var image = document.getElementById(event.target.id);
    var parent = image.parentElement;

    for(var i = 0; i < parent.children.length; i++)
    {
        if(parent.children[i].id === "shop-image") continue;
        parent.children[i].style.visibility = "hidden";
    }
    parent.style.position = "absolute";
    parent.style.paddingTop = "0";

    parent.style.backgroundColor = "black";
    image.style.zIndex = "1";
    image.style.width="100%";
    image.style.height="100%";

    // Ova funkcija onemogucava opacity, koji je u css-u podesen na 0.8 za hover
    image.onmouseover = function()
    {
        this.style.opacity = "1";
    }

    window.document.onkeydown = function (e)
    {
        parent.style.position = "static";
        parent.style.paddingTop ="5%";
        parent.style.backgroundColor = "transparent";
        image.style.maxWidth = "80%";
        image.style.height= "auto";

        
        for(var i = 0; i < parent.children.length; i++)
        {
            if(parent.children[i].id === "shop-image") continue;
            parent.children[i].style.visibility = "visible";
        }
    }

}