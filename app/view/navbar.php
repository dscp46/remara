<?php $f3=Base::instance(); ?>
    <nav class="navbar sticky-top navbar-expand-md navbar-dark bg-dark mb-4">
      <div class="container-fluid">
        <a class="navbar-brand" href="/"><img src="/assets/brand/logo.svg" alt="Logo" width="30" height="24" class="d-inline-block align-text-top"> <?= $APPNAME ?></a>
<?php if( $f3->exists('SESSION.user') && !empty( $f3->get('SESSION.user')) ) { ?>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav me-auto mb-2 mb-md-0">
<?php 
	$cur_module = $cur_module ?? '';
	foreach ( $MENU as $mnu_key => $mnu_val )
	{
		if( is_array( $mnu_val) )
		{
			$child_mod_active = false;
			foreach( $mnu_val as $ddwn_key => $ddwn_val)
				if( $cur_module == $ddwn_key )
					$child_mod_active = true; ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle<?php if($child_mod_active) { echo(' active'); } ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><?= $f3->get($mnu_val['_root']) ?></a>
              <ul class="dropdown-menu">
<?php
			foreach ( $mnu_val as $ddwn_key => $ddwn_val )
			{
				if( $ddwn_key == '_root') 
					continue;
				if( $ddwn_key == '_hr')
				{ ?>
                <li><hr class="dropdown-divider"></li>
<?php
					continue;
				} ?>
                <li><a class="dropdown-item<?php if($cur_module == $ddwn_key) { echo(' active'); } ?>" href="/<?= $ddwn_key ?>"><?= $f3->get($ddwn_val) ?></a></li>
<?php
	    		} // foreach ( $mnu_val ...
?>
              </ul>
            </li>
<?php
		} // if( is_array( $mnu_val)
		else
		{
			// Simple entry
?>
            <li class="nav-item"><a class="nav-link<?php if($cur_module == $mnu_key) { echo(' active'); } ?>" aria-current="page" href="/<?= $mnu_key ?>"><?= $f3->get($mnu_val) ?></a></li>
<?php

		} // else if( is_array( $mnu_val)
	} // foreach $MENU
?>
          </ul>
          <form class="d-flex" action="/logout" method="GET">
            <button class="btn btn-sm btn-outline-secondary" type="submit"><?= $f3->get('mui_gui_logout') ?></button>
          </form>
        </div>
<?php } // isset SESSION.user ?>
      </div>
    </nav>
