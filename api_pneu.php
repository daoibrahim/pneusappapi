<?php

require_once('../database/database.php');

//Token checking to make sure request is guenine
if(isset($_POST['token'])&&$_POST['token']=="Azbrhyyt-fkflb:;cjutht@&jgkh"){
    //Endpoint de connexion
    if($_POST['name']=="login"){
        $query = "SELECT id, full_name, email, role FROM `users`  where email='".$_POST['email']."' and password='".$_POST['password']."'";
        $resultQuery = $db->executeQuery($query);
        $rows = array();
        $result = array();
        $result['message']="Identifiant ou mot de passe incorrect";//Par defaut
        $result['code']="400";
        $result['content']=[];
        while($r = mysqli_fetch_assoc($resultQuery)) {
            $result['content'] = $r;
            $result['message']="Connexion réuissie";
            $result['code']="200";
        }
        echo json_encode($result);
    }
    //Endpoint de changement de mot de passe
    else if($_POST['name']=="changepassword"){
      $query = "SELECT * from users where id=".$_POST['userid']." and password='".$_POST['oldPass']."'";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      if(sizeof($myArray)>0){
        $query = "update users set password='".$_POST['newPass']."' where id =".$_POST['userid'];
        $result = mysqli_query($conn, $query) or die(mysqli_error());
        $data['success'] = 'True';
        $data['code'] = '200';
        $data['result'] = json_encode($myArray);
        echo $error_msg = json_encode($data);
      }
      else {
        $data['success'] = 'False';
        $data['code'] = '300';
        $data['result'] = json_encode($myArray);
        echo $error_msg = json_encode($data);
      }
    }
    //Endpoint de la liste des pneus
    else if($_POST['name']=="listpneus"){
      $query = "SELECT * from pneu  order by id desc";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['success'] = 'True';
      $data['result'] = json_encode($myArray);
      echo $error_msg = json_encode($data);
    }
    //Liste des gestionnaires
    else if($_POST['name']=="listgestionnaires"){
      $query = "SELECT full_name, email from users where  role=3 or role=7 order by id desc";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['success'] = 'True';
      $data['result'] = json_encode($myArray);
      echo $error_msg = json_encode($data);
    }
    //Liste des usures
    else if($_POST['name']=="listusures"){
      $query = "SELECT * from usure  order by date desc";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['success'] = 'True';
      $data['result'] = json_encode($myArray);
      echo $error_msg = json_encode($data);
    }
    //Liste des Usures en magasin
    else if($_POST['name']=="listusuresmagasin"){
      $query = "SELECT * from usure_magasin  order by date desc";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['success'] = 'True';
      $data['result'] = json_encode($myArray);
      echo $error_msg = json_encode($data);
    }
    //Filtre pour les usures
    else if($_POST['name']=="listusurestrier"){
      $date = str_replace('/', '-', $_POST['datedebut']);
      $dateDebut=date('Y-m-d', strtotime($date));

      $date = str_replace('/', '-', $_POST['datefin']);
      $dateFin=date('Y-m-d', strtotime($date));
      //Trie pour les gestionnaires
      $query = "SELECT a.* from usure a inner join pneu p on p.id=a.id_pneu where a.date between '$dateDebut' and '$dateFin' and (p.partie_camion_immatriculation='".$_POST['citerne']."' or p.partie_camion_immatriculation='".$_POST['tracteur']."' ) and a.validated=1 order  by a.id desc";
      $data['query1']=$query;
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['result'] = json_encode($myArray);

      //Trie pour les usures magasin
      $query = "SELECT a.* from usure_magasin a inner join pneu p on p.id=a.id_pneu where a.date between '$dateDebut' and '$dateFin' and (p.partie_camion_immatriculation='".$_POST['citerne']."' or p.partie_camion_immatriculation='".$_POST['tracteur']."' ) order  by a.id desc";
      $data['query2']=$query;
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray1 = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray1[] = $row;
      }
      $data['resultMagasin'] = json_encode($myArray1);

      $data['success'] = 'True';
      echo $error_msg = json_encode($data);
    }
    //Liste des parties des camions
    else if($_POST['name']=="listpartiescamions"){
      $query = "SELECT * from partie_camion";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $myArray = array();
      while($row = $result->fetch_array(MYSQLI_ASSOC)) {
          $myArray[] = $row;
      }
      $data['success'] = 'True';
      $data['result'] = json_encode($myArray);
      echo $error_msg = json_encode($data);
    }
    //Mise a jour d'une usure
    else if($_POST['name']=="updateusure"){
      $query = "update usure set taux_usure='".$_POST['usure']."' where id=".$_POST['idusure'];
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $query = "update pneu set usure_actuelle=".$_POST['usure']." where id =".$_POST['idpneu'];
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      if (mysqli_affected_rows($conn)>0) {
        $data['success'] = 'True';
        $data['code'] = '200';
        $data['result'] = "Enregistrement éffectué";
      }
      else {
        $data['success'] = 'false';
        $data['code'] = '300';
        $data['result'] = "Une erreur est survenue pendant l'enregistrement";
      }
      echo $error_msg = json_encode($data);
    }
    //Ajouter un pneu
    else if($_POST['name']=="addpneu"){
      $query = "insert into pneu (numero_serie, essieu, positio_sur_lessieu, marque, kilometrage_montage, kilometrage_remplacement,
      partie_camion_immatriculation, etat, usure_actuelle, date_achat, date_montage, date_demontage)
      values ('".$_POST['numeroserie']."','".$_POST['essieu']."','".$_POST['positionsuressieu']."','".$_POST['marque']."','".$_POST['kmaumontage']."',
      '','".$_POST['immatriculation']."','".$_POST['etat']."','0','".$_POST['dateachat']."','".$_POST['datemontage']."','0000-00-00')";
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      if (mysqli_affected_rows($conn)>0) {
        $data['success'] = 'True';
        $data['code'] = '200';
        $data['result'] = "Enregistrement éffectué";
      }
      else {
        $data['success'] = 'false';
        $data['code'] = '300';
        $data['result'] = "Une erreur est survenue pendant l'enregistrement";
      }
      echo $error_msg = json_encode($data);
    }
    //Supprimer une usure
    else if($_POST['name']=="supprimerrusure"){
      $query = "update usure set validated=5 where id=".$_POST['idusure'];
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      if (mysqli_affected_rows($conn)>0) {
        $data['success'] = 'True';
        $data['code'] = '200';
        $data['result'] = "Enregistrement éffectué";
      }
      else {
        $data['success'] = 'false';
        $data['code'] = '300';
        $data['result'] = "Une erreur est survenue pendant l'enregistrement";
      }
      echo $error_msg = json_encode($data);
    }
    //Valider une usure
    else if($_POST['name']=="validerusure"){
      $query = "update usure set validated=3 where id=".$_POST['idusure'];
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      $data['success'] = 'True';
      $data['code'] = '200';
      $data['result'] = "Enregistrement éffectué";
      echo $error_msg = json_encode($data);
    }
    else if($_POST['name']=="demonterpneu"){
      $query = "update pneu set etat=2, kilometrage_remplacement='".$_POST['kmdemontage']."',date_demontage='".date("Y/m/d")."' where id=".$_POST['idpneu'];
      $result = mysqli_query($conn, $query) or die(mysqli_error());
      if (mysqli_affected_rows($conn)>0) {
        $data['success'] = 'True';
        $data['code'] = '200';
        $data['result'] = "Pneu démonté";
      }
      else {
        $data['success'] = 'false';
          $data['code'] = '300';
        $data['result'] = "Une erreur est survenue pendant l'enregistrement";
      }
      echo $error_msg = json_encode($data);
    }
}
else echo "Token incorrect";
