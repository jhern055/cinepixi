<div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" id="sidebar-wrapper">
 
<?php $user=$this->session->userdata("user"); ?>
    <?php if($user): ?>
    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form">
                        <input type="text" class="form-control" id="search_module" placeholder="Buscar modulo....">
                        <span class="input-group-btn">
                        <button class="btn btn-default" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                    </div>
                    <!-- /input-group -->
                </li>
            </ul>

            <div id="cssmenu">
            <?php echo $menu; ?>
            </div>


        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <?php endif; ?>

</div>
<!-- /.col -->
<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
    <a href="#menu-toggle" class="btn btn-default leftarrow" id="menu-toggle">
    </a>
<div id="mainContainer">  