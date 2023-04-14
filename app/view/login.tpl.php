<?php $f3=Base::instance(); ?>
    <main class="container">
      <form class="form-signin w-100 m-auto" action="/login" method="POST">
<?php if( isset($alert_msg) && !empty($alert_msg)) { ?>
        <div class="alert <?= $alert_severity ?? 'alert-primary' ?>" role="alert">
          <?= $alert_msg ?>
        </div>
        <p>&nbsp;</p>
<?php } // */ ?>
      <img class="mb-4" src="/assets/brand/logo.svg" alt="" width="72" height="57" />
        <h1 class="h3 mb-3 fw-normal"><?= $mui_signin_welcome ?></h1>
  
        <div class="form-floating">
          <input type="text" class="form-control" id="floatingInput" name="callsign" placeholder="F4XXX" />
          <label for="floatingInput"><?= $mui_signin_csgn_helper ?></label>
        </div>
        <div class="form-floating">
          <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" />
          <label for="floatingPassword"><?= $mui_signin_pswd_helper ?></label>
        </div>
  
        <div class="checkbox mb-3">
          <input type="checkbox" id="remember" name="remember" />
          <label for="remember"><?= $mui_signin_remember ?></label>
        </div>
        <input type="hidden" name="token" value="<?= $f3->CSRF ?>" />
        <button class="w-100 btn btn-lg btn-primary" type="submit"><?= $mui_signin_submit ?></button>
      </form>
    </main><?php
	// Store the Anti CSRF token
	$f3->set( 'SESSION.csrf', $f3->CSRF);
?>
