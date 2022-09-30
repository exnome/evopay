<?php
  include "QR_BarCode.php"; 

  // QR_BarCode object 
  $qr = new QR_BarCode(); 

  // create text QR code 
  $qr->text("CodexWorld"); 

  // display QR code image
  $qr->qrCode();
?>