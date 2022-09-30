<label class="col-sm-2 control-label">&nbsp;</label>
<div class="col-sm-10">
<table width="100%" cellspacing="0" cellpadding="0" border="1">
	<thead>
		<tr>
			<th width="100px">Bulan</th>
			<th width="150px">RAPB</th>
			<th width="150px">REAL</th>
		</tr>
	</thead>
	<tbody>
		<?php
			error_reporting(0);
			$KodeDealer = addslashes($_REQUEST['KodeDealer']);
			$KodeAkun = addslashes($_REQUEST['KodeAkun']);
			include '../inc/koneksi.php';
			if ($msg=='0') {
				echo "0";
			} else if ($msg=='1') {
				echo "1";
			} else if (!mssql_select_db("[$table]",$connCab)) {
				echo "2";
			} else if ($msg=='3') {
				$month = array('01' => 'Januari','02' => 'Februari','03' => 'Maret','04' => 'April','05' => 'Mei','06' => 'Juni','07' => 'Juli','08' => 'Agustus','09' => 'September','10' => 'Oktober','11' => 'Nopember','12' => 'Desember');
				$month2 = array('01' => 'Jan','02' => 'Feb','03' => 'Mar','04' => 'Apr','05' => 'Mei','06' => 'Jun','07' => 'Jul','08' => 'Ags','09' => 'Sep','10' => 'Okt','11' => 'Nov','12' => 'Dsm');
				$acc = "ACC".$kodecabang;
				$ra = "RA".$kodecabang;
				$sql3 = "
					SELECT Tahun,a.Kodegl, a.Namagl,Jan,Feb,Mar,Apr,Mei,Jun,
					case when ISNULL(julR,0)=0 then Jul else julR end as Jul,
					case when ISNULL(AgsR,0)=0 then Ags else AgsR end as Ags,
					case when ISNULL(SepR,0)=0 then Sep else SepR end as Sep,
					case when ISNULL(OktR,0)=0 then Okt else OktR end as Okt,
					case when ISNULL(NovR,0)=0 then Nov else NovR end as Nov,
					case when ISNULL(DsmR,0)=0 then Dsm else DsmR end as Dsm,
				";
					for ($a=1; $a <= 12; $a++) { 
						if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
						$test = $month2[$hm];
						if (mssql_select_db("[$acc-".$tahun."".$hm."]",$connCab)) {
							$sql3 .= "
								(
									SELECT Case 
									When (G.Kategori in ('B','C') or (G.Kodegl < '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
									When (G.Kategori = 'A') then (G.JBulanIni)
									When (G.Kategori in ('D','G') or (G.Kodegl >= '40000000' and G.Typerek = '19')) then (G.JBulanIni)*-1
									When (G.Kategori in ('E','F','H')) then (G.JBulanIni)
									End as realMei
									from [$acc-".$tahun."".$hm."]..glmst G
									inner join [$ra]..ra F on G.KodeGl=F.KodeGl
									where G.Kodegl = a.Kodegl and Tahun=b.Tahun
								) as real$test,
							";
						} else {
							$sql3 .= "'-' as real$test,";
						}
					}
				$sql3 .="
					NUll as selesai
					from [$table]..glmst a
					inner join [$ra]..ra b on a.KodeGl=b.KodeGl
					where a.Kodegl = '".$KodeAkun."' and Tahun='".$tahun."'
				";
				// echo $sql3;
				$result = mssql_query($sql3,$connCab);
				$rowz = mssql_fetch_array($result);
				$rapbThun = 0;
				$realThun = 0;
				for ($a=1; $a <= 12; $a++) { 
					if (strlen($a)==1) { $hm = "0".$a; } else { $hm = $a; }
					$test2 = $month2[$hm];
					$rapbThun += round($rowz[$test2]);
					if ($rowz["real".$test2]=='-') {
						$realThun += 0;
						$real_ = '-';
					} else {
						$realThun += round($rowz["real".$test2]);
						$real_ = number_format($rowz["real".$test2],0,",",".");
					}
				}

				$rapbBln = $rowz[$month2[$bln]];
				$realBln = $rowz["real".$month2[$bln]];
				$rapbOg = 0;
				for ($i=1; $i <= $bln; $i++) { 
					if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
					$rapbOg += $rowz[$month2[$hm]];
				}
				$realOg = 0;
				for ($i=1; $i <= $bln; $i++) { 
					if (strlen($i)==1) { $hm = "0".$i; } else { $hm = $i; }
					$realOg = $rowz["real".$month2[$hm]];
				}
			}
		?>
		<tr>
			<th>Bulan <?php echo $month[$bln] ?></th>
			<td><?php echo number_format($rapbBln,0,",","."); ?></td>
			<td><?php echo number_format($realBln,0,",","."); ?></td>
		</tr>
		<tr>
			<th>s/d</th>
			<td><?php echo number_format($rapbOg,0,",","."); ?></td>
			<td><?php echo number_format($realOg,0,",","."); ?></td>
		</tr>
		<tr>
			<th>Tahun <?php echo $tahun ?></th>
			<td><?php echo number_format($rapbThun,0,",","."); ?></td>
			<td><?php echo number_format($realThun,0,",","."); ?></td>
		</tr>
	</tbody>
</table>
</div>