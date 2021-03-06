<?php
  session_start();
  // $veza = new PDO("mysql:dbname=starwarsdb;host=localhost;charset=utf8", "swuser", "swpass");
  $veza = new PDO('mysql:host=' . getenv('MYSQL_SERVICE_HOST') . ';port=3306;dbname=starwarsdb', 'swuser', 'swpass');
  // $veza = new PDO("mysql:dbname=starwarsdb;host=mysql-57-centos7", "swuser", "swpass");
  $veza->exec("set names utf8");
  $error_telefon = false;
  $error_vecPostojeci = false;
  $edit = false;
  $i = -1;

  //DONE
  if(isset($_POST['obrisiDugme']))
  {
    $id  = $_POST['id'];
    $query = $veza->prepare("delete from Poslovnica where id=:ID");
    $query->bindValue(":ID", $id, PDO::PARAM_INT);
    $value = $query->execute();
  }

  if(isset($_POST['editDugme']))
  {
      $edit = true;
      $i = $_POST['editDugme'];
  }

  // DONE
  if(isset($_POST['spasi']))
  {
     $novaAdresa = htmlspecialchars($_POST['adresaEdit'], ENT_QUOTES, "UTF-8");
     $noviTelefon = htmlspecialchars($_POST['brojTelefonaEdit'], ENT_QUOTES, "UTF-8");
     
     if(preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $noviTelefon) && $novaAdresa != "")
     {
        $id  = $_POST['id'];
        /* PROVJERA DA LI POSLOVNICA SA ADRESOM VEĆ POSTOJI */
        $provjera = $veza->prepare("select count(*) from poslovnica where adresa=:Adresa");
        $provjera->bindValue(":Adresa", $novaAdresa, PDO::PARAM_STR);
        $provjera->execute();
        $broj = $provjera->fetchColumn();
        if($broj > 0) $error_vecPostojeci = true;
        else
        {
            $query = $veza->prepare("update Poslovnica set adresa=:Adresa, telefon=:Telefon where id=:ID");
            $query->execute(array($novaAdresa, $noviTelefon, $id));
        }
      }

     if(!preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $noviTelefon)) $error_telefon = true;

     header('Location:'.$_SERVER['PHP_SELF']);
  }

// DONE
if(isset($_POST['dodajPoslovnicu']))
{
    if(isset($_POST['adresa']) && isset($_POST['brojTelefona']) && preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $_POST['brojTelefona']))
    {
        $adresa = htmlspecialchars($_POST['adresa'], ENT_QUOTES, "UTF-8");
        $brojTelefona = htmlspecialchars($_POST['brojTelefona'], ENT_QUOTES, "UTF-8");
        $id = htmlspecialchars($_POST['id'], ENT_QUOTES, "UTF-8");

          /* PROVJERA DA LI POSLOVNICA SA ADRESOM VEĆ POSTOJI */
          $provjera = $veza->prepare("select COUNT(*) from poslovnica where adresa=:Adresa");
          $provjera->bindValue(":Adresa", $adresa, PDO::PARAM_STR);
          $provjera->execute();
          $broj = $provjera->fetchColumn();

          if($broj > 0) $error_vecPostojeci = true;

          else
          {
              $upit = $veza->prepare('insert into poslovnica (id, adresa, telefon, sef)
                VALUES (NULL, :Adresa, :Telefon, :Sef)');
              $upit->execute(array($adresa, $brojTelefona, $id));
              header('Location:'.$_SERVER['PHP_SELF']);
          }
     }
  }
?>

  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>About</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
            function getJSON(id)
            {
                var ajax = new XMLHttpRequest();
                ajax.onreadystatechange = function ()
                {
                  if (ajax.readyState == 4 && ajax.status == 200)
                  {
                      document.open();
                      document.write("<body> <a href='about.php'>Nazad</a> <br> <p>" + ajax.responseText + "</p> </body>");
                      document.close();
                      //  alert(JSON.stringify(ajax.responseText));
                  }
                    if (ajax.readyState == 4 && ajax.status == 404)
                    alert("error");

                }
                ajax.open("GET", "restskripta.php?id=" + id, true);
                ajax.send();
            }
        </script>
  </head>
  <body class="page">

  <!-- Meni -->
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
    <br>
    <br>
    <br>

    <!-- Tabela sa poslovnicama -->
    <table border="1px solid black" align="center" style="width:93%" bgcolor="#ffffff">
     <!-- Zaglavlje tabele -->
      <tr>
        <th>Adresa poslovnice</th>
        <th>Broj telefona</th>
      </tr>

      <!-- PHP kod za dodavanje poslovnica iz baze u tabelu -->
      <?php
        $x = 0;
        $poslovnice = $veza->query("select * from poslovnica");
        foreach($poslovnice as $poslovnica)
        { ?>
        
        <!-- Red tabele sadrži adresu, broj telefona poslovnice i dugmad za dodavanje i brisanje poslovnice -->
        <tr>

          <!-- Ako je zatraženo editovanje, u tom redu tabele se nalaze 2 inputa, sa već zadanim vrijednostima, koje admin može mijenjati -->
          <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin" && ($edit == "true" && $x == $i)) { ?>
            <form action='about.php' method='post'>
           <td align="center"> <input type="text" name="adresaEdit" value= "<?php print $poslovnica['adresa'] ?>"> </td>
           <td align="center"> <input type="text" name="brojTelefonaEdit" value= "<?php print $poslovnica['telefon'] ?>"> </td>
           <td align="center">
              <button type="submit" name="spasi" value="<?php echo $x;?>"> Spasi </button>
              <button type="submit" name="odustani" value="<?php echo $x;?>"> Odustani </button>
              <input type="hidden" name="id" value="<?php print $poslovnica['id'] ?>">
              </form>
            </td>
          <?php $x++; continue; }

          else {?>
          <td align="center"> <?php print $poslovnica['adresa'] ?> </td>
          <td align="center"> <?php print $poslovnica['telefon'] ?> </td>
          <?php } ?>
          <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin"){ ?>
          <td align="center">
            <form style="display:inline-block;" action='about.php' method='post'>
            <button type="submit" name="editDugme" value="<?php echo $x;?>"> Edituj </button>
            <button type="submit" name="obrisiDugme" value="<?php echo $x;?>"> Obriši </button>
            <input type="hidden" name="id" value="<?php print $poslovnica['id']?>">
            </form>
            <button onclick="getJSON( <?php print $poslovnica['id'] ?>)" style="display:inline-block;" type="submit" name="json"> JSON Objekat </button>
          </td>
          <?php }?>
        </tr>
        <?php $x++; }  ?>
        </table>

        <br>
        <br>
        <?php
        // Ako je user admin, onda moze dodavati nove poslovnice, vidjeti PDF izvjestaj i downloadovati CSV file
        if(isset($_SESSION['user']) && $_SESSION['user'] == "admin"){ ?>
        <div class="col-3" style="display:inline-block;">
          <form align='center' id='poslovniceForma' action='about.php' method='post'>
          <br>
           <input type='text' id="adresaPoslovnice" name='adresa' placeholder='Adresa poslovnice'>
            <input type='text' id="brTelefonaPoslovnice" name='brojTelefona' placeholder='Broj telefona poslovnice'>
            <input type='text' id="idSefa" name='id' placeholder='ID šefa'>
            <input id='dodajPoslovnicu-button' name='dodajPoslovnicu' type='submit' value='Dodaj' />
            </form>
            <br>
            <div class="col-1">
            <?php if($error_telefon == true) {?>
              <p  id="warningMessage"> Podaci o broju telefona nisu u ispravnom formatu! </p>
        <?php } else if ($error_vecPostojeci == true) { ?>
              <p  id="warningMessage"> Adresa i broj telefona se ne mogu ponavljati! </p>
        <?php }}?>
        </div>
        </div>

        <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin"){ ?>
        <div class="col-3" style="display:inline-block;">
        <!-- Tabela sa sefovima kojim nije dodijeljena poslovnica -->
        <table border="1px solid black" style="margin-left:-20px;" align="center" style="width:80%" bgcolor="#ffffff">
        <!-- Zaglavlje tabele -->
          <tr>
            <th>ID šefa</th>
            <th>Ime šefa</th>
            <th>Prezime šefa</th>
          </tr>
            <?php
            $array = $veza->query("select * from osoba where uloga='sef' and id not in (select sef from poslovnica)");
            foreach($array as $row)
            {
              ?>
                <tr>
                <td align="center"> <?php print $row['id'] ?> </th>
                <td align="center"> <?php print $row['ime'] ?> </th>
                <td align="center"> <?php print $row['prezime'] ?> </th>
        <?php }?>
        </table>
        <br>
        <form style="padding-left:5%"method="post" action="register.php">
            <input name='dodajSefa' type='submit' value='Dodaj novog šefa'/>
        </form>
        </div>
        <div>
          <form style="padding-left: 3.8%" action="xmltodb.php" method="post">
            <input type='submit' name='korisniciUBazu' value='Prebaci korisnike u bazu'/>
            <br>
            <br>
            <input type='submit' name='poslovniceUBazu' value='Prebaci poslovnice u bazu'/>
            </form>
        </div>
        <br>
        <?php } ?>
          <!--Izvjestaj u PDF-u i csv file -->
          <!-- Izvjestaj u PDF-u mogu vidjeti svi, pa čak i neregistrovani korisnici, dok csv file može downloadovati samo admin-->
          <div style="padding-left:3.8%;">
              <form action="izvjestaj.php" method="post">
              <input id="izvjestaj-button" type="submit" value="PDF izvještaj">
            </form>
            <br>
            <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin") { ?>
            <form style="display:inline-block;" id="downloadForma" action="downloadcsv.php">
              <input id="download-button" type="submit" value="Download csv">
            </form>
            <?php } ?>

      <script src="script/skripta.js" type="text/javascript"></script>
  </body>
  </html>