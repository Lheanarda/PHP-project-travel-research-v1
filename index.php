


<?php 
$resultPage = 0;
include "include/config.php" ;
$awalsebelum = '';
$stmtDelObyek = $connection->prepare('DELETE FROM hasilobyek');
$stmtDelObyek -> execute();

$stmtDelKueri = $connection->prepare('DELETE FROM hasilkueri');
$stmtDelKueri ->execute();


//CB VAR
$popular = '';
$downtown = '';
//END CB VAR

//SHOW BUTTON
if(isset($_POST['Show'])){
  $stmtDelObyek = $connection->prepare('DELETE FROM hasilobyek');
  $stmtDelObyek -> execute();

  $stmtDelKueri = $connection->prepare('DELETE FROM hasilkueri');
  $stmtDelKueri ->execute();
  $resultPage = 1; //SHOW RESULT PAGE 

  //INISIALISI
  $kabupatenNAMA = $_POST['mulai'];
  $dateStart = $_POST['dtpStart'];
  $dateEnd = $_POST['dtpEnd'];

  if(isset($_POST['cbPopular']))$popular = $_POST['cbPopular'];
  if(isset($_POST['cbDowntown'])) $downtown = $_POST['cbDowntown'];

  //END INISIALISI

  //KABUPATEN KODE
  $qKabupatenKode = $connection->prepare("SELECT*FROM kabupaten WHERE kabupatenNAMA = :kabNAMA");
  $qKabupatenKode->execute(['kabNAMA' => $kabupatenNAMA]);
  $rKabupatenKode = $qKabupatenKode->fetch();
  $kabupatenKODE = $rKabupatenKode['kabupatenKODE'];
  //END KABUPATEN KODE

  //TEMPORER VARIABLE
  $caseKabKODE = $kabupatenKODE;
  //END TEMPORER VARIABLE

  //INPUT HASIL KUERI
  $qObyekWisata = $connection->prepare("SELECT ow.*, k.kabupatenKODE FROM obyekwisata ow, kecamatan k WHERE k.kecamatanKODE = ow.kecamatanKODE AND k.kabupatenKODE = :kabKODE");
  $qObyekWisata->execute(['kabKODE' => $kabupatenKODE ]);
  if($qObyekWisata->rowCount() > 0){
      while($rObyekWisata = $qObyekWisata->fetch()){
        $obyekKODE = $rObyekWisata ['obyekKODE'];
        $kecamatanKODE = $rObyekWisata ['kecamatanKODE'];
        $kabupatenKODE = $rObyekWisata ['kabupatenKODE'];
        $kategoriKODE = $rObyekWisata ['kategoriKODE'];
        $obyekPOPULARITAS = $rObyekWisata ['obyekPOPULARITAS'];
        $obyekKEMUDAHAN = $rObyekWisata ['obyekKEMUDAHAN'];
        $obyekWAKTUKUNJUNG = $rObyekWisata ['obyekWAKTUKUNJUNG'];
        $jamBUKA = $rObyekWisata ['obyekJAMBUKA'];
        $jamTUTUP = $rObyekWisata ['obyekJAMTUTUP'];
        $customer = '';

        $stmt = $connection->prepare("INSERT INTO hasilkueri VALUES (:obyekKODE,:kecKODE,:kabKODE,:katKODE,:obyekPOP,:obyekKEM,:obyekWAK,:jamBUKA,:jamTUTUP,:customer)");
        $stmt->execute(['obyekKODE'=>$obyekKODE,'kecKODE'=>$kecamatanKODE,'kabKODE'=>$kabupatenKODE, 'katKODE'=>$kategoriKODE,'obyekPOP'=>$obyekPOPULARITAS,'obyekKEM'=>$obyekKEMUDAHAN,'obyekWAK'=>$obyekWAKTUKUNJUNG,'jamBUKA'=>$jamBUKA, 'jamTUTUP'=>$jamTUTUP,'customer'=>$customer]);
    }
  }
  
  //END INPUT HASIL KUERI

  //VARIABEL KONDISI  
  $free = '';
  $choosePOPULARITAS = '';
  $chooseKEMUDAHAN = '';
  $chooseBOTH = '';
  //END VARIABEL KONDISI

  //CARI WAKTU KUNJUNG PALING LAMA 
  $qObyekWaktu=$connection->prepare("SELECT*FROM hasilkueri WHERE obyekWAKTUKUNJUNG = (SELECT MAX(obyekWAKTUKUNJUNG) FROM hasilkueri) LIMIT 1;");
  $qObyekWaktu->execute();
  if($qObyekWaktu->rowCount()>0){
    $rObyekWaktu = $qObyekWaktu->fetch();
    $free = $rObyekWaktu['obyekKODE'];
  }
  
  //END WWAKTU KUNJUNG PALING LAMA

  //CARI POPULARITAS PALING BESAR
  $qObyekPopularitas=$connection->prepare("SELECT*FROM hasilkueri WHERE obyekPOPULARITAS = (SELECT MAX(obyekPOPULARITAS) FROM hasilkueri) LIMIT 1; ");
  $qObyekPopularitas->execute();
  if($qObyekPopularitas->rowCount()>0){
    $rObyekPOPULARITAS = $qObyekPopularitas->fetch();
    $choosePOPULARITAS = $rObyekPOPULARITAS['obyekKODE'];
  }
  //END CARI POPULARITAS PALING BESAR

  //CARI KEMUDAHAN PALING BESAR
  $qObyekKEMUDAHAN=$connection->prepare("SELECT*FROM hasilkueri WHERE obyekKEMUDAHAN = (SELECT MAX(obyekKEMUDAHAN) FROM hasilkueri) LIMIT 1;");
  $qObyekKEMUDAHAN->execute();
  if($qObyekKEMUDAHAN->rowCount()>0){
    $rObyekKEMUDAHAN = $qObyekKEMUDAHAN->fetch();
    $chooseKEMUDAHAN = $rObyekKEMUDAHAN['obyekKODE'];
  }


  //END CARI KEMUDAHAN PALING BESAR

  //CARI DOUBLE CHECK
  $qObyekBOTH= $connection->prepare("SELECT*FROM hasilkueri WHERE obyekKEMUDAHAN = (SELECT MAX(obyekKEMUDAHAN) FROM hasilkueri) AND obyekPOPULARITAS = (SELECT MAX(obyekPOPULARITAS) FROM hasilkueri) LIMIT 1;");
  $qObyekBOTH->execute();
  if($qObyekBOTH->rowCount()>0){
    $rChooseBOTH = $qObyekBOTH->fetch();
    $chooseBOTH = $rChooseBOTH['obyekKODE'];
  }
  //END CARI DOUBLE CHECK

  //KONDISI ATTRACTION
  $default  = '';
  if($popular == '' AND $downtown == '') $default = $free;
  else if($popular == 'cbPopular' AND $downtown == '') {$default = $choosePOPULARITAS;}
  else if($popular == '' AND $downtown == 'cbDowntown') $default = $chooseKEMUDAHAN;
  else if($popular == 'cbPopular' AND $downtown == 'cbDowntown')  $default = $chooseBOTH;

  //END KONDISI ATTRACTION

  //GREEDY DEFAULT
  $i = 0;
  $cust = 1;
  $jarak=0;
  $tempuh = 0;
  $destinasi = '';
  $jumlahjarak = 0;
  $jumlahwaktu = 0;
  $attraction = 0;
  $attraction2 = 0;
  $kontrol = 1; //else if control
  while($i<$qObyekWisata->rowCount()){
    $qSorting=$connection->prepare("SELECT jo.ruteKODE, jo.obyekKODEasal, owa.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGmulai, owa.obyekPOPULARITAS AS obyekPOPULARITASmulai, owa.obyekKEMUDAHAN as obyekKEMUDAHANmulai, jo.obyekKODEtujuan, owd.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGdestinasi, owd.obyekPOPULARITAS AS obyekPOPULARITASdestinasi, owd.obyekKEMUDAHAN AS obyekKEMUDAHANdestinasi, jo.obyekjarak   , jo.obyektempuh, jo.obyekRUTE FROM jarakobyek jo, obyekwisata owa, obyekwisata owd, kecamatan keca, kecamatan kecd WHERE jo.obyekKODEasal = owa.obyekKODE AND jo.obyekKODEtujuan = owd.obyekKODE AND obyekKODEasal = :default AND keca.kecamatanKODE = owa.kecamatanKODE AND kecd.kecamatanKODE = owd.kecamatanKODE AND keca.kabupatenKODE = :kabKODE AND kecd.kabupatenKODE = :kabKODE ");

    $qSorting->execute(['default'=>$default,'kabKODE'=>$kabupatenKODE]);
    if($qSorting->rowCount()>0){
      while($row = $qSorting->fetch()){
        //KONDISI ATTRACTION
        if($popular == '' AND $downtown == '') $check = $row['obyekWAKTUKUNJUNGdestinasi'];
        else if($popular == 'cbPopular' AND $downtown == '') $check = $row['obyekPOPULARITASdestinasi'];
        else if($popular == '' AND $downtown == 'cbDowntown') $check = $row['obyekKEMUDAHANdestinasi'];
        else if($popular == 'cbPopular' AND $downtown == 'cbDowntown') {$check = $row['obyekKEMUDAHANdestinasi'] ; $check2 = $row['obyekPOPULARITASdestinasi'];}
        //END KONDISI ATTRACTION

        if($popular == 'cbPopular' AND $downtown == 'cbDowntown'){
            if($attraction == 0 OR ($check >$attraction AND $check2 > $attraction2) ){
              $jarak = $row['obyekjarak'];
              $default = $row ['obyekKODEasal'];
              $destinasi = $row ['obyekKODEtujuan'];
              $tempuh = $row['obyektempuh'];
              $attraction = $check;
              $attraction2 = $check2;
            }
        }else{
            if($attraction == 0 OR $check >$attraction ){
            $jarak = $row['obyekjarak'];
            $default = $row ['obyekKODEasal'];
            $destinasi = $row ['obyekKODEtujuan'];
            $tempuh = $row['obyektempuh'];
            $attraction = $check;

          }
        }
        
      }
    }else if ($qSorting->rowCount() ==0 AND $kontrol == 1){
      //PROBLEM
      $qLihatHasilKueri = $connection->prepare("SELECT*FROM hasilkueri");
      $qLihatHasilKueri->execute();

      $totalWisata = $qLihatHasilKueri->rowCount()-1;
      $cWisata = 0; //counter wisata
      $queriText = " obyekKODE != '$default' ";

        //KONDISI ATTRACTION
        if($popular == '' AND $downtown == '') $checks= 'obyekWAKTUKUNJUNG';
        else if($popular == 'cbPopular' AND $downtown == '') $checks = 'obyekPOPULARITAS';
        else if($popular == '' AND $downtown == 'cbDowntown') $checks = 'obyekKEMUDAHAN';
        else if($popular == 'cbPopular' AND $downtown == 'cbDowntown')  {$checks = 'obyekKEMUDAHAN'; $checks2 = 'obyekPOPULARITAS';}
        //END KONDISI ATTRACTION
        $defaults = '';
      while($cWisata < $totalWisata ){

          
          if($popular == 'cbPopular' AND $downtown == 'cbDowntown'){
            $qAttraction = $connection->prepare("SELECT*FROM hasilkueri WHERE :checks = (SELECT MAX(:checks ) FROM hasilkueri WHERE :queriTEXT ) OR :checks2 = (SELECT MAX(:checks2) FROM hasilkueri WHERE :queriTEXT )  AND :queriTEXT LIMIT 1");
            $qAttraction->execute(['checks'=>$checks,'queriTEXT'=>$queriText,'checks2'=>$checks2]);

            if($qAttraction->rowCount()>0){
              $rAttraction = $qAttraction->fetch();
              $defaults = $rAttraction['obyekKODE'];
            }
          }else{
            $qAttraction = $connection->prepare("SELECT*FROM hasilkueri WHERE :checks = (SELECT MAX(:checks ) FROM hasilkueri WHERE :queriTEXT ) AND :queriTEXT LIMIT 1");
            $qAttraction->execute(['checks'=>$checks,'queriTEXT'=>$queriText]);

            if($qAttraction->rowCount()>0){
              $rAttraction = $qAttraction->fetch();
              $defaults = $rAttraction['obyekKODE'];
            }
            
          }

          $qSorting = $connection->prepare("SELECT jo.ruteKODE, jo.obyekKODEasal, owa.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGmulai, owa.obyekPOPULARITAS AS obyekPOPULARITASmulai, owa.obyekKEMUDAHAN as obyekKEMUDAHANmulai, jo.obyekKODEtujuan, owd.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGdestinasi, owd.obyekPOPULARITAS AS obyekPOPULARITASdestinasi, owd.obyekKEMUDAHAN AS obyekKEMUDAHANdestinasi, jo.obyekjarak   , jo.obyektempuh, jo.obyekRUTE FROM jarakobyek jo, obyekwisata owa, obyekwisata owd, kecamatan keca, kecamatan kecd WHERE jo.obyekKODEasal = owa.obyekKODE AND jo.obyekKODEtujuan = owd.obyekKODE AND obyekKODEasal = :defaults AND keca.kecamatanKODE = owa.kecamatanKODE AND kecd.kecamatanKODE = owd.kecamatanKODE AND keca.kabupatenKODE = :kabKODE AND kecd.kabupatenKODE = :kabKODE");

          $qSorting->execute(['defaults'=>$defaults,'kabKODE'=>$kabupatenKODE]);

          if($qSorting->rowCount()>0){
            while($row = $qSorting->fetch()){
              //KONDISI ATTRACTION
              if($popular == '' AND $downtown == '') $check = $row['obyekWAKTUKUNJUNGdestinasi'];
              else if($popular == 'cbPopular' AND $downtown == '') $check = $row['obyekPOPULARITASdestinasi'];
              else if($popular == '' AND $downtown == 'cbDowntown') $check = $row['obyekKEMUDAHANdestinasi'];
              else if($popular == 'cbPopular' AND $downtown == 'cbDowntown') {$check = $row['obyekKEMUDAHANdestinasi'];$check2 = $row['obyekPOPULARITASdestinasi'];} ;
              //END KONDISI ATTRACTION

              if($popular == 'cbPopular' AND $downtown == 'cbDowntown'){
                  $jarak = $row['obyekjarak'];
                  $default = $row ['obyekKODEasal'];
                  $destinasi = $row ['obyekKODEtujuan'];
                  $tempuh = $row['obyektempuh'];
                  $attraction = $check;
                  $attraction2 = $check2;
              }else{
                  if($attraction == 0 OR $check >$attraction ){
                  $jarak = $row['obyekjarak'];
                  $default = $row ['obyekKODEasal'];
                  $destinasi = $row ['obyekKODEtujuan'];
                  $tempuh = $row['obyektempuh'];
                  $attraction = $check;

                }
              }
              
            }
          }
          


          $queriText = $queriText." AND obyekKODE != '$defaults'";
          $cWisata++; 
        
      }
      
      $kontrol=$kontrol+1;
    }
    $kontrol=$kontrol+1;

    //BREAK
    if($destinasi == $default OR $destinasi == '' OR $destinasi ==$awalsebelum ){
      break;
    }
    $awalsebelum = $default;
    //END BREAK
    $jumlahwaktu = $jumlahwaktu+$tempuh;
    $jumlahjarak = $jumlahjarak+$jarak;
    $i = $i+1;
    $customer = 'A'.$cust;

    $stmt = $connection->prepare("INSERT INTO hasilobyek  VALUES (:default,:destinasi,:jarak,:tempuh,:jumlahjarak,:jumlahwaktu,:kabupatenKODE,:kabupatenKODE,:customer,:i)");
    $stmt->execute(['default'=>$default,'destinasi'=>$destinasi,'jarak'=>$jarak,'tempuh'=>$tempuh,'jumlahjarak'=>$jumlahjarak,'jumlahwaktu'=>$jumlahwaktu,'kabupatenKODE'=>$kabupatenKODE,'customer'=>$customer,'i'=>$i]);

    //RESET
    $attraction = 0;
    $default = $destinasi;
    //END RESET
  }

 //SECOND LOOP
  $qLoopHasilKueri = $connection->prepare("SELECT*FROM hasilkueri");
  $qLoopHasilKueri->execute();
  $qLoopHasilObyek = $connection->prepare("SELECT*FROM hasilobyek");
  $qLoopHasilObyek->execute();
  $counter = $qLoopHasilObyek->rowCount();
  while($counter< $qLoopHasilKueri->rowCount()){

    //SISA - CARI YANG WAKTU KUNJUNG PALING BANYAK
    // $currentAttraction = 0;
    // $currentAttraction2 = 0;
    $currentObyekKODE = '';

    $qOpenHasilKueri = $connection->prepare("SELECT obyekKODE,obyekWAKTUKUNJUNG,obyekPOPULARITAS,obyekKEMUDAHAN FROM hasilkueri a
                                                  WHERE NOT EXISTS (
                                                      SELECT obyekKODEasal FROM hasilobyek
                                                      WHERE obyekKODEasal = a.obyekKODE)
                                                      AND
                                                      NOT EXISTS (
                                                       SELECT obyekKODEtujuan FROM hasilobyek
                                                       WHERE obyekKODEtujuan = a.obyekKODE) ORDER BY RAND() LIMIT 1;");
    $qOpenHasilKueri->execute();
    if($qOpenHasilKueri->rowCount()>0){
      while($row = $qOpenHasilKueri->fetch()){

        //FIND RANDOM KODE
        $currentObyekKODE = $row['obyekKODE'];
        //END RANDOM KODE
        
        
      }
      
    }
    //END SISA
    $default = $currentObyekKODE;
    $customer = 'A'.$cust;
    $cust = $cust+1;
    $i = 0;
    $jarak=0;
    $tempuh = 0;
    $destinasi = '';
    //CHANGE
    // $jumlahjarak = 0;
    // $jumlahwaktu = 0;
    $attraction = 0;
    //GREEDY 
    while($i<$qObyekWisata->rowCount()){
      $qSorting = $connection->prepare("SELECT jo.ruteKODE, jo.obyekKODEasal, owa.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGmulai,owa.obyekPOPULARITAS AS obyekPOPULARITASmulai, owa.obyekKEMUDAHAN as obyekKEMUDAHANmulai , jo.obyekKODEtujuan, owd.obyekWAKTUKUNJUNG AS obyekWAKTUKUNJUNGdestinasi, owd.obyekPOPULARITAS AS obyekPOPULARITASdestinasi, owd.obyekKEMUDAHAN AS obyekKEMUDAHANdestinasi , jo.obyekjarak   , jo.obyektempuh, jo.obyekRUTE FROM jarakobyek jo, obyekwisata owa, obyekwisata owd, kecamatan keca, kecamatan kecd WHERE jo.obyekKODEasal = owa.obyekKODE AND jo.obyekKODEtujuan = owd.obyekKODE AND obyekKODEasal = :default AND keca.kecamatanKODE = owa.kecamatanKODE AND kecd.kecamatanKODE = owd.kecamatanKODE AND keca.kabupatenKODE = :kodeKAB AND kecd.kabupatenKODE = :kodeKAB
        AND NOT EXISTS (SELECT obyekKODEtujuan FROM hasilobyek
                        WHERE obyekKODEtujuan = jo.obyekKODEtujuan)
        AND NOT EXISTS (SELECT obyekKODEasal FROM hasilobyek
                        WHERE obyekKODEasal = jo.obyekKODEtujuan LIMIT 1 )");
      $qSorting->execute(['kodeKAB'=>$kabupatenKODE,'default'=>$default]);

      if($qSorting->rowCount()>0){
        while($row = $qSorting->fetch()){
          //KONDISI ATTRACTION
          if($popular == '' AND $downtown == '') $check = $row['obyekWAKTUKUNJUNGdestinasi'];
          else if($popular == 'cbPopular' AND $downtown == '') $check = $row['obyekPOPULARITASdestinasi'];
          else if($popular == '' AND $downtown == 'cbDowntown') $check = $row['obyekKEMUDAHANdestinasi'];
          else if($popular == 'cbPopular' AND $downtown == 'cbDowntown') {$check = $row['obyekKEMUDAHANdestinasi']; $check2 = $row['obyekPOPULARITASdestinasi'];} ;
          //END KONDISI ATTRACTION
          if($popular == 'cbPopular' AND $downtown == 'cbDowntown'){
              if($attraction == 0 OR ($check>$attraction AND $check2>$attraction2) ){
                $jarak = $row['obyekjarak'];
                $default = $row ['obyekKODEasal'];
                $destinasi = $row ['obyekKODEtujuan'];
                $tempuh = $row['obyektempuh'];
                $attraction = $check;
                $attraction2 = $check2;
              }
          }else{
              if($attraction == 0 OR $check>$attraction ){
              $jarak = $row['obyekjarak'];
              $default = $row ['obyekKODEasal'];
              $destinasi = $row ['obyekKODEtujuan'];
              $tempuh = $row['obyektempuh'];
              $attraction = $check;
            }
          }
          
        }
      }

      
      
      //BREAK
      if($destinasi == $default ){
        break;
      }
      //END BREAK


      $jumlahwaktu = $jumlahwaktu+$tempuh;
      $jumlahjarak = $jumlahjarak+$jarak;
      $i = $i+1;
      $customer = 'A'.$cust;

      if($tempuh == 0 AND $jarak == 0){
        $stmt = $connection->prepare("INSERT INTO hasilobyek(obyekKODEasal,Customer_ID,jmljarak,jmlwaktutempuh) VALUES (:obyekKODEasal,:Customer_ID,:jmljarak,:jmlwaktutempuh);");
        $stmt->execute(['obyekKODEasal'=>$default,'Customer_ID'=>$customer,'jmljarak'=>$jumlahjarak,'jmlwaktutempuh'=>$jumlahwaktu]);
      }else{
         $stmt=$connection->prepare("INSERT INTO hasilobyek  VALUES (:default,:destinasi,:jarak,:tempuh,:jumlahjarak,:jumlahwaktu,:kabupatenKODE,:kabupatenKODE, :customer, :i)");
         $stmt->execute(['default'=>$default,'destinasi'=>$destinasi,'jarak'=>$jarak,'tempuh'=>$tempuh,'jumlahjarak'=>$jumlahjarak,'jumlahwaktu'=>$jumlahwaktu,'kabupatenKODE'=>$kabupatenKODE,'customer'=>$customer,'i'=>$i]);
      }
     

      //RESET
      $attraction = 0;
      $default = $destinasi;
      //END RESET
    }
    //END GREEDY 

    //+ counter
    $counter = $counter+1;
    //END + counter

  }

  //END SECOND LOOP

  
  //END GREEDY DEFAULT

}
//END SHOW BUTTON


//QUERY 
$qdisticntAsal = $connection->prepare("SELECT DISTINCT kabupatenNAMA FROM kabupaten");
$qdisticntAsal->execute();


$qHasilKueri = $connection->prepare("SELECT ow.obyekNAMA, kec.kecamatanNAMA, kab.kabupatenNAMA, kat.kategoriNAMA, hk.obyekPOPULARITAS, hk.obyekKEMUDAHAN, hk.obyekWAKTUKUNJUNG, hk.jamBUKA,hk.jamTUTUP  FROM hasilkueri hk, obyekwisata ow, kecamatan kec, kabupaten kab, kategoriwisata kat WHERE hk.obyekKODE = ow.obyekKODE AND hk.kecamatanKODE = kec.kecamatanKODE AND hk.kabupatenKODE = kab.kabupatenKODE AND hk.kategoriKODE = kat.kategoriKODE ");
$qHasilKueri->execute();
//END QUERY

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <?php include "include/import.php" ?>

</head>

<body>

  <!-- ======= Header ======= -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  <?php include "include/menu.php" ?>     

  <!-- End Header -->

  <!-- ======= Hero Section ======= -->
  <section id="hero">
    <div class="hero-container" data-aos="fade-up">
      <h1>Welcome to Pesona Jawa</h1>
      <h2>make your travel easier</h2>
      <a href="#input" class="btn-get-started scrollto">Mulai</a>
    </div>
  </section><!-- End Hero -->

  <main id="main">

    <!-- ======= About Section ======= -->
    
    <form method="POST">
    <section id="input" class="about">
      <div class="container" data-aos="fade-up">

        

        <div class="row">

          <div class="col-lg-6 video-box align-self-baseline" style="margin-top : 16px; margin-bottom: 16px;">
              <img src="images/background.jpg" class="img-fluid" alt="">
            <a href="https://youtu.be/U5DvqDLSxbQ" class="venobox play-btn mb-4" data-vbtype="video" data-autoplay="true"></a>
          </div>

          <div class="col-lg-6 pt-3 pt-lg-0 content" style="margin-top : 16px">
            <h3>Travel Guide</h3>
            <p class="font-italic">
              new and easy way to plan your trip
            </p>
            <div class = "form-box" method="POST">
                <div class="booking-form">
                  
                  <label>Choose Destination</label>
                  
                  <select class="form-control select2" name = "mulai">

                      <?php 
                      $control = 0; //variabel control
                      if($qdisticntAsal->rowCount()>0){
                        while($row=$qdisticntAsal->fetch()){?>

                          <?php 
                          if ($resultPage == 0){?>
                              <option>
                                <?php echo $row["kabupatenNAMA"] ?>

                              </option>
                          <?php } else if ($resultPage == 1){ ?>
                            
                                <?php if($control == 0){?>
                                  <option selected>
                                  <?php echo $kabupatenNAMA; ?>
                                  </option>
                               <?php }  ?>
                            
                              
                              
                                <?php 
                                  if($row['kabupatenNAMA']!= $kabupatenNAMA){ ?>
                                    <option>
                                    <?php echo $row["kabupatenNAMA"]; ?>
                                    </option>
                                  <?php }
                                ?>
                              
                          <?php }
                          ?>
                          

                        <?php $control = $control+1; }

                      } ?>
                  </select>

                  <div class="input-grp">
                  <label>Start</label>
                  <input type="date" class="form-control select-date" name = "dtpStart">
                </div>

                <div class="input-grp">
                  <label>End</label>
                  <input type="date" class="form-control select-date" name = "dtpEnd">
                </div>

                 <div class="input-grp">
                  <label>Attraction</label>
                    <div class="custom-control custom-checkbox" style="margin-left: -24px">
                      <?php if ($popular !='') { ?>
                          <input type="checkbox" class="custom-control-input" id="popularCheck" name = "cbPopular" value = "cbPopular" checked="">
                          <label class = "custom-control-label" for ="popularCheck" style="padding-left: 20px; padding-top: 3px">Popular</label>
                      <?php } else { ?>
                      <input type="checkbox" class="custom-control-input" id="popularCheck" name = "cbPopular" value = "cbPopular">
                      <label class = "custom-control-label" for ="popularCheck" style="padding-left: 20px; padding-top: 3px">Popular</label>
                    <?php } ?>
                    </div>
                    <div class="custom-control custom-checkbox" style="margin-left: -24px">
                      <?php if ($downtown !='') { ?>
                          <input type="checkbox" class="custom-control-input" id="centerCheck" name = "cbDowntown" value="cbDowntown" checked>
                          <label class = "custom-control-label" for ="centerCheck" style="padding-left: 20px; padding-top: 3px">Downtown / Pusat Kota</label>
                      <?php } else { ?>
                          <input type="checkbox" class="custom-control-input" id="centerCheck" name = "cbDowntown" value="cbDowntown">
                          <label class = "custom-control-label" for ="centerCheck" style="padding-left: 20px; padding-top: 3px">Downtown / Pusat Kota</label>
                      <?php } ?>
                    </div>

                </div>

                  
                  <br>
                  
                  <div class="input-grp">

                    <button type="submit" class="btn btn-primary travel" name = "Show" value="Show">Show</button>
                    
                  </div>
                  

                </div>
              </div>


          </div>

        

        </div>
      </div>


       <script>$('.select2').select2(); </script>


    </section><!-- End About Section -->


    <!--RESULT TEXT-->

    
    <?php 
    if($resultPage==1){?>
      

      <section id="input" class="about">
        <div class="container" data-aos="fade-up" style="background-color: #94c045; color: #fff ">
          <?php
            if($popular == '' AND $downtown == ''){ ?> <h3 style="padding-top: 10px">Your Travel Guide In <?php echo $kabupatenNAMA ?> by Default (Kunjungan Paling Lama) </h3> <?php }
            else if($popular == 'cbPopular' AND $downtown == '') { ?> <h3 style="padding-top: 10px">Your Travel Guide In <?php echo $kabupatenNAMA ?> by Popularity </h3> <?php }
            else if($popular == '' AND $downtown == 'cbDowntown') { ?> <h3 style="padding-top: 10px">Your Travel Guide In <?php echo $kabupatenNAMA ?> by Downtown (Kemudahan) </h3> <?php }
            else if($popular == 'cbPopular' AND $downtown == 'cbDowntown') { ?> <h3 style="padding-top: 10px">Your Travel Guide In <?php echo $kabupatenNAMA ?> by Popularity and Downtown </h3> <?php }
          ?>
            
        </div>
      </section>
  <?php } ?>
      
    <!--END RESULT TEXT -->

    <!--RESULT PAGE TRAVEL-->
    <?php 
    $custom = '';
    $control= 0;
    $no =1;
    $lamaPerjalanan = 0;
    if($resultPage==1){
      $qResult = $connection->prepare("SELECT*FROM hasilobyek");
      $qResult->execute();
      if($qResult->rowCount()>0){
        while($row = $qResult->fetch()){
          //BANDINGKAN APAKAH KODE CUSTOMER SAMA DENGAN ROW SEBELUMNYA
          $lamaPerjalanan = $row['jmlwaktutempuh'];
          $checkCustom = $row['Customer_ID'];
          if($custom != $checkCustom){
            $kodeAWAL = $row['obyekKODEasal'];

            $qObyekAwal = $connection->prepare("SELECT*FROM obyekwisata WHERE obyekKODE = :kodeAWAL");
            $qObyekAwal->execute(['kodeAWAL'=>$kodeAWAL]);
            $rObyekAwal= $qObyekAwal->fetch();
            $namaAWAL = $rObyekAwal['obyekNAMA'];
            $kunjungAWAL = $rObyekAwal['obyekWAKTUKUNJUNG'];

            
            //AWAL RESULT
            if($control == 0){?>
                <section id="input" class="about">
                  <div class="container" data-aos="fade-up" >
                    <p> <?php echo $no.'. '.$namaAWAL.' dengan waktu kunjungan '.$kunjungAWAL.' menit'; $no = $no+1; $control = $control+1;  ?> </p>
                  </div>
                </section>

                 <?php
                if($row['obyekKODEtujuan']!= ''){
                  $kodeBERIKUT = $row['obyekKODEtujuan'];
                  $qObyekBerikut = $connection->prepare("SELECT*FROM obyekwisata WHERE obyekKODE = :kodeBERIKUT ");
                  $qObyekBerikut->execute(['kodeBERIKUT'=>$kodeBERIKUT]);
                  $rObyekBerikut= $qObyekBerikut->fetch();
                  $namaBERIKUT = $rObyekBerikut['obyekNAMA'];
                  $kunjungBERIKUT = $rObyekAwal['obyekWAKTUKUNJUNG']; ?>
                  <section id="input" class="about">
                  <div class="container" data-aos="fade-up" >
                    <p><?php echo $no.'. Lanjut ke '.$namaBERIKUT.' dengan jarak tempuh '.$row['obyekjarak'].' km dalam waktu '.$row['obyekwaktutempuh'].' menit dengan waktu kunjungan '.$kunjungBERIKUT.' menit';
                    $no = $no+1;  ?> </p>
                  </div>
                </section>
                <?php }
                ?>
                
            <?php }else{?>
              <!--RESULT KALO JALUR GA NEMU TAPI LANJUT-->
              <section id="input" class="about">
                  <div class="container" data-aos="fade-up" >
                    <p><?php echo $no.'. Lanjut ke '.$namaAWAL.' dengan jarak tempuh  [belum diketahui] dalam waktu [belum diketahui] dengan waktu kunjungan '.$kunjungAWAL.' menit';$no=$no+1; ?> </p>
                  </div>
                </section> 

                <?php
                if($row['obyekKODEtujuan']!= ''){
                  $kodeBERIKUT = $row['obyekKODEtujuan'];
                  $qObyekBerikut = $connection->prepare("SELECT*FROM obyekwisata WHERE obyekKODE = :kodeBERIKUT ");
                  $qObyekBerikut->execute(['kodeBERIKUT'=>$kodeBERIKUT]);
                  $rObyekBerikut= $qObyekBerikut->fetch();
                  $namaBERIKUT = $rObyekBerikut['obyekNAMA'];
                  $kunjungBERIKUT = $rObyekAwal['obyekWAKTUKUNJUNG']; ?>
                  <section id="input" class="about">
                  <div class="container" data-aos="fade-up" >
                    <p><?php echo $no.'. Lanjut ke '.$namaBERIKUT.' dengan jarak tempuh '.$row['obyekjarak'].' km dalam waktu '.$row['obyekwaktutempuh'].' menit dengan waktu kunjungan '.$kunjungBERIKUT.' menit';
                    $no = $no+1;  ?> </p>
                  </div>
                </section>
                <?php }
                ?>
                

            <?php }
            //END AWAL RESULT
          }else{
            $kodeBERIKUT = $row['obyekKODEtujuan'];
            $qObyekBerikut = $connection->prepare("SELECT*FROM obyekwisata WHERE obyekKODE = :kodeBERIKUT ");
                  $qObyekBerikut->execute(['kodeBERIKUT'=>$kodeBERIKUT]);
                  $rObyekBerikut= $qObyekBerikut->fetch();
            $namaBERIKUT = $rObyekBerikut['obyekNAMA'];
            $kunjungBERIKUT = $rObyekAwal['obyekWAKTUKUNJUNG']; 
            ?>

            <section id="input" class="about">
                  <div class="container" data-aos="fade-up" >
                    <p><?php echo $no.'. Lanjut ke '.$namaBERIKUT.' dengan jarak tempuh '.$row['obyekjarak'].' km dalam waktu '.$row['obyekwaktutempuh'].' menit dengan waktu kunjungan '.$kunjungBERIKUT.' menit';
                    $no = $no+1;   ?> </p>
                  </div>
                </section>
          <?php }
          $custom = $checkCustom;
        }
      }
      ?>


    <?php }
    ?>


    <!--KESIMPULAN-->
    <?php if($resultPage==1){

      $qJumlahWaktuKunjung = $connection->prepare("SELECT SUM(obyekWAKTUKUNJUNG) AS SUM FROM hasilkueri");
      $qJumlahWaktuKunjung->execute();
      if($qJumlahWaktuKunjung->rowCount()>0){
        $rJumlah = $qJumlahWaktuKunjung->fetch();
        $lamaKunjung = $rJumlah['SUM'];
      }
      $totalWaktu = $lamaKunjung+$lamaPerjalanan; $convertJAM = round($totalWaktu/60); $totalwisata = $no-1;


     ?>
    <section id="input" class="about">
        <div class="container" data-aos="fade-up" style="background-color: #94c045; color: #fff ">
          <p style="padding-top: 10px"><?php echo 'Untuk mengunjungi obyek wisata di '.$kabupatenNAMA.', diperlukan '.$totalWaktu.' menit (+-'.$convertJAM.' jam) dengan waktu perjalanan '.$lamaPerjalanan.' menit dan total lama kunjungan adalah '.$lamaKunjung.' menit untuk mengunjungi '.$totalwisata.' obyek wisata.' ?></p>
        </div>
      </section>


    <?php } ?>
    <!--END KESIMPULAN-->

    <!--END RESULT PAGE TRAVEL-->

    

    <!--RESULT PAGE LIST -->

    <?php 
      if ($resultPage == 1){?>

        <section id="input" class="about">

          <div class="container" data-aos="fade-up">
            <h3>List Wisata <?php echo $kabupatenNAMA ?> </h3>
            <!--RESULT-->
            <table class="table table-hover">
              <!-- membuat judul -->
              <tr class="info">
                    <th>No</th>
                    <th>Nama Obyek</th>
                    <th>Nama Kecamatan</th>
                    <th>Nama Kabupaten</th>
                    <th>Nama Kategori</th>
                    <th>Popularitas</th>
                    <th>Kemudahan</th>
                    <th>Waktu Kunjung</th>
                    <th>Jam Buka</th>
                    <th>Jam Tutup</th>
              </tr>
              <?php
                /** Memeriksa apakah data yang dipanggil tersebut tersedia atau tidak **/
                if(isset($_POST['Show'])){
                if($qHasilKueri->rowCount()>0) 
              {?>
                <?php $no=1; $nomor = 1; ?>
                <?php while ($row = $qHasilKueri->fetch()) 
                  {  ?>
                    <tr class="info">
                      <td><?php echo $nomor ; ?> </td>
                      <td><?php echo $row['obyekNAMA']; ?> </td>
                      <td><?php echo $row['kecamatanNAMA']; ?> </td>
                      <td><?php echo $row['kabupatenNAMA']; ?> </td>
                      <td><?php echo $row['kategoriNAMA']; ?> </td>
                      <td><?php echo $row['obyekPOPULARITAS']; ?> </td>
                      <td><?php echo $row['obyekKEMUDAHAN']; ?> </td>
                      <td><?php echo $row['obyekWAKTUKUNJUNG']; ?> </td>
                      <td><?php echo $row['jamBUKA']; ?> </td>
                      <td><?php echo $row['jamTUTUP']; ?> </td>
                     
                    </tr>
                    <?php $no++; ?> 
                  <?php $nomor = $nomor+1; } ?>
              <?php  }
              } ?>
              </table>

             
            <!--END RESULT-->

          </div>
        </section>
      <?php }
    ?>
    
    <!--END RESULT PAGE LIST-->
    
  </form>



    

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <?php include "include/footer.php" ?>
  <!-- End Footer -->

  <!--=======Java Script========-->
  <?php include "include/js.php" ?>


  <!--=======END Java Script========-->

  <!-- Material unchecked -->

</body>

</html>