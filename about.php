<?php
  session_start();
  $xml = new DOMDocument('1.0', 'UTF-8');
  $xml->load('poslovnice.xml');
  $error_telefon = false;
  $error_rvrijeme = false;
  $error_vecPostojeci = false;
  $edit = false;
  $i = -1;

  if(isset($_POST['obrisiDugme']))
  {
    //$_POST['obrisiDugme']) dat ce value dugmeta koje je pritisnuto
    $docElement = $xml->documentElement;
    $podaci = $docElement->getElementsByTagName('Podaci');
    $rmv = null;
    $i = $_POST['obrisiDugme'];
    $rmv = $podaci[$i];
    if($rmv != null) $docElement->removeChild($rmv);
                  
    file_put_contents('poslovnice.xml', $xml->saveXML());
  }

  if(isset($_POST['editDugme']))
  {
      $edit = true;  
      $i = $_POST['editDugme'];
  }

  // Ako je admin vršio neke promjene i pritisnuo dugme Spasi
  if(isset($_POST['spasi']))
  {
    $data = $xml->getElementsByTagName('Podaci');

    $i = $_POST['spasi'];
    
    $adrese = $data->item($i)->getElementsByTagName('Adresa');
    $telefoni = $data->item($i)->getElementsByTagName('BrojTelefona');
    $vrijeme = $data->item($i)->getElementsByTagName('RadnoVrijeme');

    $novaAdresa = $_POST['adresaEdit'];
    $noviTelefon = $_POST['brojTelefonaEdit'];
    $novoVrijeme = $_POST['radnoVrijemeEdit'];

    $xmldata = simplexml_load_file('poslovnice.xml');
    foreach($xmldata->children() as  $value)
    {
        if($value->Adresa == $_POST['adresaEdit'] || $value->BrojTelefona == $_POST['brojTelefonaEdit'])
        {
            $error_vecPostojeci = true;
            break;
        }
    }

    if(!preg_match("@[0-2][0-9]:[0-5][0-9] - [0-2][0-9]:[0-5][0-9]@", $novoVrijeme))
      $error_rvrijeme = true;

    if(!preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $noviTelefon))
      $error_telefon = true;

    if($novaAdresa != "" && 
      preg_match("@[0-2][0-9]:[0-5][0-9] - [0-2][0-9]:[0-5][0-9]@", $novoVrijeme) && 
      preg_match("@0[0-9][0-9] [0-9][0-9][0-9] [0-9][0-9][0-9]@", $noviTelefon) &&
      strlen($noviTelefon) < 12 &&
      !error_vecPostojeci)
    {
      $adrese->item(0)->childNodes->item(0)->nodeValue = htmlspecialchars($novaAdresa, ENT_QUOTES, "UTF-8");
      $telefoni->item(0)->childNodes->item(0)->nodeValue = htmlspecialchars($noviTelefon, ENT_QUOTES, "UTF-8");
      $vrijeme->item(0)->childNodes->item(0)->nodeValue = htmlspecialchars($novoVrijeme, ENT_QUOTES, "UTF-8");

      file_put_contents('poslovnice.xml', $xml->saveXML());
    }
  }

if(isset($_POST['dodajPoslovnicu']))
{
  // Provjera da li već postoji poslovnica sa zadanim imenom ili brojem telefona
    $xmldata = simplexml_load_file('poslovnice.xml');
    foreach($xmldata->children() as  $value)
    {
        if($value->Adresa == $_POST['adresa'] || $value->BrojTelefona == $_POST['brojTelefona']){
            $error_vecPostojeci = true;
            break;
        }
    }

    if($_POST['adresa'] != "" && !$error_vecPostojeci && preg_match("@[0-2][0-9]:[0-5][0-9] - [0-2][0-9]:[0-5][0-9]@", $_POST['radnoVrijeme']) && preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $_POST['brojTelefona']))
    {
        // $_SESSION['user'] = $_POST;
        $rootTag = $xml->getElementsByTagName("Poslovnice")->item(0);
        
        $dataTag = $xml->createElement("Podaci");
        
        $adresaTag = $xml->createElement("Adresa");
        $adresaTag->appendChild($xml->createTextNode(htmlspecialchars($_POST['adresa'], ENT_QUOTES, "UTF-8")));

        $brojTelefonaTag  = $xml->createElement("BrojTelefona");
        $brojTelefonaTag->appendChild($xml->createTextNode(htmlspecialchars($_POST['brojTelefona'], ENT_QUOTES, "UTF-8")));

        $vrijemeTag = $xml->createElement("RadnoVrijeme");
        $vrijemeTag->appendChild($xml->createTextNode(htmlspecialchars($_POST['radnoVrijeme'], ENT_QUOTES, "UTF-8")));
        
        $dataTag->appendChild($adresaTag);
        $dataTag->appendChild($brojTelefonaTag);
        $dataTag->appendChild($vrijemeTag);
        
        $rootTag->appendChild($dataTag);
        $xml->save('poslovnice.xml');
        header('Location:'.$_SERVER['PHP_SELF']);
    }
    if (!preg_match("@[0-2][0-9]:[0-5][0-9] - [0-2][0-9]:[0-5][0-9]@", $_POST['radnoVrijeme'])) $error_rvrijeme = true;
    if(!preg_match("@0[0-9]{2}[ ][0-9]{3}[ ][0-9]{3}@", $_POST['brojTelefona'])) $error_telefon = true;
}
?>

  <!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>About</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
            function prikaziRezultate(unos) 
            {
                var rezultati = document.getElementById("rezultati");
                
                //Ako nije nista upisano
                if (unos.length == 0) 
                { 
                    rezultati.innerHTML = "";
                    rezultati.style.border = "0px";
                    return;
                }

                if (window.XMLHttpRequest) httprequest = new XMLHttpRequest();
                else httprequest = new ActiveXObject("Microsoft.XMLHTTP");
                
                httprequest.onreadystatechange = function()
                {
                    if (this.readyState == 4 && this.status == 200) 
                    {
                        rezultati.innerHTML = this.responseText;
                        rezultati.style.position = "absolute";
                        rezultati.style.backgroundColor = "black";
                        rezultati.style.color = "white";
                    }
                }

                httprequest.open("GET", "search.php?q=" + unos, true);
                httprequest.send();
            }
        </script>
  </head>
  <body class="page">

  <!-- Meni -->
    <div class="header-standard">
      <img id="menicon" src="./images/menu-icon.png" data-toggle="dropdown" onclick="showMenu()">
      <?php if(isset($_SESSION['user'])){
        if($_SESSION['user'] == "admin" || $_SESSION['user'] == "guest") { ?>
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
        <?php } } 
          if((!isset($_SESSION['user']) || $_SESSION['user'] == "unknown")) { ?>
            <ul id="meni">
              <li><a id="home-link" href="index.php">Star Wars Details</a></li>
              <li><a href="planets.php">Planets</a></li>
              <li><a href="jedi.php">Jedi</a></li>
              <li><a href="siths.php">Siths</a></li>
              <li><a href="shop.php">Shop</a></li>
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
    <table border="1px solid black" align="center" style="width:75%" bgcolor="#C0C0C0">
     <!-- Zaglavlje tabele -->
      <tr>
        <th>Adresa poslovnice</th>
        <th>Broj telefona</th>
        <th>Radno vrijeme</th>
      </tr>

      <!-- PHP kod za dodavanje poslovnica iz XML file-a u tabelu -->
      <?php
        $xml = simplexml_load_file('poslovnice.xml');
        $x = 0;
        foreach ($xml->children() as  $value) { ?>
        <!-- Red tabele sadrži adresu, broj telefona poslovnice i dugmad za dodavanje i brisanje poslovnice -->
        <tr>
          <!-- Ako je zatraženo editovanje, u tom redu tabele se nalaze 3 inputa, sa već zadanim vrijednostima, koje admin može mijenjati -->
          <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin" && ($edit == "true" && $x == $i)) { ?>
            <form action='about.php' method='post'>
           <td align="center"> <input type="text" name="adresaEdit" value= "<?php print $value->Adresa ?>"> </td>
           <td align="center"> <input type="text" name="brojTelefonaEdit" value= "<?php print $value->BrojTelefona ?>"> </td>
           <td align="center"> <input type="text" name="radnoVrijemeEdit" value= "<?php print $value->RadnoVrijeme ?>"> </td>
           <td align="center">
              <button type="submit" name="spasi" value="<?php echo $x;?>"> Spasi </button>
              <button type="submit" name="odustani" value="<?php echo $x;?>"> Odustani </button>
              </form>
            </td>
          <?php $x++; continue; } 
          else {?>
          <td align="center"> <?php print $value->Adresa ?> </td>
          <td align="center"> <?php print $value->BrojTelefona ?> </td> 
          <td align="center"> <?php print $value->RadnoVrijeme ?> </td> 
          <?php } ?>
          <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin"){ ?>
          <td align="center"> 
            <form action='about.php' method='post'>
            <button type="submit" name="editDugme" value="<?php echo $x;?>"> Edituj </button>
            <button type="submit" name="obrisiDugme" value="<?php echo $x;?>"> Obriši </button>
              <?php }?>
            </form>
          </td>
        </tr>
        <?php $x++; }  ?>
        </table>

        <?php
        // Ako je user admin, onda moze dodavati nove poslovnice, vidjeti PDF izvjestaj i downloadovati CSV file 
        if(isset($_SESSION['user']) && $_SESSION['user'] == "admin"){ ?>

          <form align='center' id='poslovniceForma' action='about.php' method='post'>;
          <br>
           <input type='text' id="adresaPoslovnice" name='adresa' placeholder='Adresa'>
            <input type='text' id="brTelefonaPoslovnice" name='brojTelefona' placeholder='Broj telefona'>
            <input type='text' id="vrijeme" name='radnoVrijeme' placeholder='Radno vrijeme'>
            <input id='dodajPoslovnicu-button' name='dodajPoslovnicu' type='submit' value='Dodaj' />
            <?php if($error_rvrijeme == true) { ?>
              <p style="padding-top:1.5%; padding-bottom:0.2%; margin-left:-50px;" id="warningMessage"> Podaci o radnom vremenu nisu u ispravnom formatu! </p>
        <?php } else if($error_telefon == true) {?>
              <p style="padding-top:1.5%; padding-bottom:0.2%; margin-left:-50px;" id="warningMessage"> Podaci o broju telefona nisu u ispravnom formatu! </p>
        <?php } else if ($error_vecPostojeci == true) { ?>
              <p style="padding-top:1.5%; padding-bottom:0.2%; margin-left:-50px;" id="warningMessage"> Adresa i broj telefona se ne mogu ponavljati! </p>
        <?php }}?>
          </form>

          <!-- Izvjestaj u PDF-u i csv file -->
          <!-- Izvjestaj u PDF-u mogu vidjeti svi, pa čak i neregistrovani korisnici, dok csv file može downloadovati samo admin-->
          <div style="padding-left:43%; padding-top:2%;">
            <form <?php if(!isset($_SESSION['user']) || $_SESSION['user'] == "guest") { ?> style="display:inline-block; margin-left: 50px;" <?php } else { ?>  style="display:inline-block;" <?php } ?> id="izvjestajForma" action="izvjestaj.php">
              <input id="izvjestaj-button" type="submit" value="PDF izvještaj">
            </form>
            <?php if(isset($_SESSION['user']) && $_SESSION['user'] == "admin") { ?>
            <form style="display:inline-block;" id="downloadForma" action="downloadcsv.php">
              <input id="download-button" type="submit" value="Download csv">
            </form>
            <?php } ?>
            </br>
            </br>
            </br>
            <!-- Forma za search -->
            <form style="margin-left: -20px">
              <input style="color:black;" type="text" size="25" placeholder="Search" onkeyup="prikaziRezultate(this.value)">
              <button> Pretraži </button>
              <div id="rezultati"></div>
            </form>
          </div>
      <script src="script/skripta.js" type="text/javascript"></script>
  </body>
  </html>