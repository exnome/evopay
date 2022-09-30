<a id="leftmenu-trigger" class="tooltips" data-toggle="tooltip" data-placement="right" title="Toggle Sidebar"></a>
<a style="display: none" id="rightmenu-trigger" class="tooltips" data-toggle="tooltip" data-placement="left" title="Toggle Infobar"></a>

<div class="navbar-header pull-left">
    <a class="navbar-brand" href="">Evo Pay</a>
</div>

<ul class="nav navbar-nav pull-right toolbar">
	<li class="dropdown">
		<a href="#" class="hasnotifications dropdown-toggle" data-toggle='dropdown'>
            <?php echo $_SESSION['UserName'] ?> <i class="fa fa-user"></i>
        </a>
		<ul class="dropdown-menu userinfo arrow">
			<li class="username">
                <a href="#">
				    <div class="pull-left">
                        <h5><?php echo $_SESSION['UserName'] ?></h5>
                        <small>Logged on <span><?php echo $_SESSION['level'] ?></span></small>
                        <br>
                        <input type="hidden" id="IdUser" value="<?php echo $_SESSION[UserID]; ?>">
                        <input type="hidden" id="user_div" value="<?php echo $user[divisi]; ?>">
                        <input type="hidden" id="user_dept" value="<?php echo $user[department]; ?>">
                    </div>
                </a>
			</li>
			<li class="userlinks">
				<ul class="dropdown-menu">
					<li><a href="master-account">Account <i class="pull-right fa fa-cog"></i></a></li>
					<li><a href="#" onclick="logout();">Sign Out <i class="pull-right fa fa-sign-out"></i></a></li>
				</ul>
			</li>
		</ul>
	</li>
</ul>