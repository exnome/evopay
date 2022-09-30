<ul class="acc-menu" id="sidebar">
   <!-- <li class="<?php $act = ($page=='')?"active" : ""; echo "$act";?>">
        <a href="home"><i class="fa fa-home"></i> <span>Home</span></a>
    </li>
    <li class="<?php $act = ($p[0]=='dashboard')?"active" : ""; echo "$act";?>">
        <a href="dashboard"><i class="fa fa-desktop"></i> <span>Dashboard</span></a>
    </li>-->
    <?php
		 $qry1 = mssql_query("select distinct Icon,KategoriMenu,KategoriUrut, link, icon, menu, MenuUrut
							from sys_permission p 
							LEFT JOIN sys_menu m on p.IdMenu=m.IdMenu 
							where p.IdUser='".$IdUser."' and m.active='1' 
							and levelmenu = '1'
							order by MenuUrut",$conns); 

        while($dt_kt = mssql_fetch_array($qry1)){
            $dt=mssql_fetch_array(mssql_query("select KategoriMenu from sys_permission p LEFT 
                JOIN sys_menu m on p.IdMenu=m.IdMenu where m.link='".$p[0]."' and m.active='1'"));
            
			$act = ($dt[0]==strtolower($dt_kt['menu']))?"active" : "";
			
			//$act = ($page=='dashboard')?"active" : ""; 
            echo "
                <li class='$act'>
					<a href='".$dt_kt['link']."'><i class='".$dt_kt['icon']."'></i> <span>".$dt_kt['menu']."</span></a>
                </li>
            ";
        }
		
        $qry1 = mssql_query("select distinct Icon,KategoriMenu,KategoriUrut 
							from sys_permission p 
							LEFT JOIN sys_menu m on p.IdMenu=m.IdMenu 
							where p.IdUser='".$IdUser."' and m.active='1' and levelmenu = '2' 
							order by KategoriUrut",$conns); 

        while($dt_kt = mssql_fetch_array($qry1)){
            $dt=mssql_fetch_array(mssql_query("select KategoriMenu from sys_permission p LEFT 
                JOIN sys_menu m on p.IdMenu=m.IdMenu where m.link='".$p[0]."' and m.active='1'"));
            
            $act1 = ($dt[0]==$dt_kt['KategoriMenu'])?"open active" : "";
            echo "
                <li class='$act1 hasChild'>
                    <a href='javascript:;'>
                        <i class='$dt_kt[Icon]'></i>
                        <span>".$dt_kt['KategoriMenu']."</span> 
                    </a>
                    <ul class='acc-menu' id='".$dt_kt['KategoriUrut']."'>";
                    
                    $qry2 = mssql_query("select * from sys_permission p LEFT JOIN sys_menu m on p.IdMenu=m.IdMenu where KategoriMenu='".$dt_kt['KategoriMenu']."'
                        and p.IdUser='".$IdUser."' and m.active='1' order by KategoriUrut,MenuUrut asc",$conns); 
                    while($dt_mn = mssql_fetch_array($qry2)){
                        $act2 = ($p[0]==strtolower($dt_mn['link']))?"active" : "";
                        echo "
                            <li class='$act2'>
                                <a href='".str_replace(" ", "", strtolower($dt_kt['KategoriMenu']))."-".strtolower($dt_mn['link'])."'>".$dt_mn['Menu']."</a>
                            </li>
                        ";
                    }
            echo "
                    </ul>
                </li>
            ";
        }
    ?>
</ul>