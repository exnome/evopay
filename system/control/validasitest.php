<?php
	include ('../inc/conn.php');
	
	$KodeDealer = "2010";
	include '../inc/koneksi.php';
	
	mssql_query("BEGIN TRAN",$conns);
	
	$query1 = mssql_query("
insert into DataEvoVal (nobukti,kodedealer,level,tglentry, deptterkait) 
values ('VP00/18/01/22/005','2010','FINANCE',getdate(), '')",$conns);

	$query2  = mssql_query("update DataEvoVal set validasi='Accept',uservalidasi='Anung',tglvalidasi=getdate(), ketvalidasi=' ', 
ketvalidasi2='', ketreject='', ipentry = '10.10.27.4',
 useragent = 'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:96.0) Gecko/20100101 Firefox/96.0' 
 where nobukti = 'VP00/18/01/22/005' and ISNULL(validasi, '')='' and level = 'DIV. HEAD'",$conns);
		
	$query3 = mssql_query("
insert into [ACC00-202201]..gltrn (NoBukti,KodeGl,TglTrn,KodeSumber,TypeTrn,
Keterangan,JlhDebit,JlhKredit,DelIndex,LastUpdated,KodeUser,KodeLgn,SumberForm,NoFPS) 
					values('VP00/18/01/22/005','60503050','2022-01-26','AP','03','VP ','308000','0','AP15667724',getdate(),'galih','XRM1213-2978','EvoPay',' '),('VP00/18/01/22/005','21441400','2022-01-26','AP','03','BMHD BIAYA KIRIM DOKUMEN KE TAM DAN DEALER TGL 10 DES - 17 JANUARI 2022','0','308000','AP15667724',getdate(),'galih','XRM1213-2978','EvoPay',' ');
insert into [ACC00-202201]..aptrn 
					(KodeLgn,NoFaktur,TypeTrn,NoBukti,TglTrn,TglJthTmp,TglTrnFaktur,TglJtpFaktur,
					Statusgiro,Keterangan,JumlahTrn,TglEntry,Kodeuser,Kodesumber,DelIndex,KodeJurnal) values('XRM1213-2978','VP00/18/01/22/005','C','VP00/18/01/22/005','2022-01-26','2022-01-26','2022-01-26','2022-01-26','C','DPP BMHD BIAYA KIRIM DOKUMEN KE TAM DAN DEALER TGL 10 DES - 17 JANUARI 2022',308000-0,getdate(),'galih','AP','AP15667724','21441400')",$connCab);
		
	if ($query1 && $query2 && $query3) {
		mssql_query("COMMIT TRAN",$conns);	
		
	} else {
	
		mssql_query("ROLLBACK TRAN",$conns);
	}
	mssql_query("return",$conns);
	
    ?>