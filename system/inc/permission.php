<?php
	session_start();
	$denie = "Haha";
	if($_SESSION['UserID'] && $_SESSION['pwd'] && $_SESSION['UserName'] && $_SESSION['level'] && $_SESSION['kodedealer']) {
		$page = isset($_GET['page']) ? $_GET['page'] : null;
		$p = explode("-", $page);
		
        $result=mssql_num_rows(mssql_query("select * from sys_permission p LEFT JOIN sys_menu m on p.IdMenu=m.IdMenu where link='".$p[0]."'"));
        $othermenu = array('account');
        if ($result==0 && !in_array($p[0], $othermenu)) {
			echo $denie; 
		}
	} else { 
		echo $denie;
	}
?>