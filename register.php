  <?php
    session_start();
    $error = false;
    if(isset($_POST['register']))
    {
        if(isset($_POST['name']) && isset($_POST['surname']) && isset($_POST['email']) && isset($_POST['password']))
        {
            $ime = htmlspecialchars($_POST['name'], ENT_QUOTES, "UTF-8");
            $prezime = htmlspecialchars($_POST['surname'], ENT_QUOTES, "UTF-8");
            $email = htmlspecialchars($_POST['email'], ENT_QUOTES, "UTF-8");
            $password = md5($_POST['password']);

            // $veza = new PDO("mysql:dbname=starwarsdb;host=localhost;charset=utf8", "swuser", "swpass");
            $veza = new PDO('mysql:host=' . getenv('MYSQL_SERVICE_HOST') . ';port=3306;dbname=starwarsdb', 'swuser', 'swpass');
            // $veza = new PDO("mysql:dbname=starwarsdb;host=mysql-57-centos7", "swuser", "swpass");
            /* PROVJERA DA LI U BAZI VEC POSTOJI KORISNIK SA ISTIM EMAILOM */
            $provjera = $veza->prepare("select COUNT(*) FROM Osoba WHERE email=?");
            $provjera->bindValue(1, htmlspecialchars($email, ENT_QUOTES, "UTF-8"), PDO::PARAM_STR);
            $provjera->execute();
            $broj = $provjera->fetchColumn();

            // Ako postoji korisnik, ispisat će se poruka o grešci
            if($broj > 0) $error = true;

            // Ako ne postoji, dodat će se u BP             
            else
            {
                if($_SESSION['user'] == "admin")
                {
                    $upit = $veza->prepare('insert into osoba (id, ime, prezime, email, password, uloga) 
                                values (NULL, ?, ?, ?, ?, "sef")');
                }
                else
                {
                    $upit = $veza->prepare('insert into osoba Osoba (id, ime, prezime, email, password, uloga) 
                                values (NULL, ?, ?, ?, ?, "user")');
                }
                $upit->execute(array($ime, $prezime, $email, $password));

                if($_SESSION['user'] != "admin")
                {
                    header('Location:index.php');   
                    $_SESSION['user'] = "guest";
                }
                else
                {
                    header('Location:about.php');   
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php if(isset($_SESSION['user']) && $_SESSION['user'] != "admin") {?> 
        <title>Sign Up!</title>
        <?php } ?>
        <link rel="stylesheet" href="css/style.css">
    </head>
     <body class="forme">
    <div class="header-standard">
      <img id="menicon" src="./images/menu-icon.png" data-toggle="dropdown" onclick="showMenu()">
      <?php if(isset($_SESSION['user'])){
        if($_SESSION['user'] == "guest"){ ?>
        <ul id="meni">
          <li><a id="home-link" href="index.php">Star Wars Details</a></li>
          <li><a href="planets.php">Planets</a></li>
          <li><a href="jedi.php">Jedi</a></li>
          <li><a href="siths.php">Siths</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="about.php">About us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="logout.php">Log out</a></li>
        </ul>
        <?php }
        if($_SESSION['user'] == "admin" || $_SESSION['user'] == "sef"){ ?>
        <ul id="meni">
          <li><a id="home-link" href="index.php">Star Wars Details</a></li>
          <li><a href="planets.php">Planets</a></li>
          <li><a href="jedi.php">Jedi</a></li>
          <li><a href="siths.php">Siths</a></li>
          <li><a href="shop.php">Shop</a></li>
          <li><a href="stock.php">Stock</a></li>
          <li><a href="about.php">About us</a></li>
          <li><a href="logout.php">Log out</a></li>
        </ul>
        <?php }
        }
        // Neregistrovan posjetilac stranice ne može posjetiti shop
          if((!isset($_SESSION['user']) || $_SESSION['user'] == "unknown")) { ?>
            <ul id="meni">
              <li><a id="home-link" href="index.php">Star Wars Details</a></li>
              <li><a href="planets.php">Planets</a></li>
              <li><a href="jedi.php">Jedi</a></li>
              <li><a href="siths.php">Siths</a></li>
              <li><a href="about.php">About us</a></li>
              <li><a href="contact.php">Contact</a></li>
              <li><a href="login.php">Log in</a></li>
              <li><a href="register.php">Sign up</a></li>
            </ul>
        <?php } ?>
        </div>
            <div class="forma-register">
                <form id="register_form" action="register.php" method="post" onsubmit="return validacijaRegistracija()">
                    <ul>
                        <li> 
                            <input id="nameRegister" name="name" type="text" placeholder="Name" required>
                        </li>
                        <li> 
                            <input id="surname" name="surname" type="text" placeholder="Last name" required>
                        </li>
                        <li> 
                            <input id="emailRegister" name="email" type="text" placeholder="Email" required>
                        </li>
                        <li >
                            <input id="password1" name="password" type="password" placeholder="Password" required>
                        </li>
                        <li >
                            <input id="password2" name="repeatpassword" type="password" placeholder="Repeat password" required>
                        </li>
                         <?php if($error == true) { ?>
                        <p style="padding-top:1.5%; padding-bottom:0.2%; margin-left:-50px;" id="warningMessage"> Postoji korisnik sa istim emailom! </p>
                         <?php } ?>
                        <li id="submit-button-li">
                            <input id="submit-button" name="register" type="submit" value="Sign up" />
                        </li>
                    </ul>

                </form>
            </div>
         </div>
            <script src="script/skripta.js" type="text/javascript"></script>
     </body>
</html>