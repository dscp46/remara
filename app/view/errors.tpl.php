<?php $f3=Base::instance(); ?>
  <main class="container">
    <div class="alert alert-danger" role="alert">
      <?php 
	switch( $errcode) 
	{
	case 400:
		echo($f3->get('mui_error_bad_req_alert'));
		break;
	case 403:
		echo($f3->get('mui_error_forbidden_alert'));
		break;
	case 404:
		echo($f3->get('mui_error_notfound_alert'));
		break;
	case 500:
		echo($f3->get('mui_error_ise_alert'));
		break;
	default:
		echo($f3->get('mui_error_generic_alert'));
	}
	?>
    </div>
<?php if($f3->exists('ERROR')) { ?>
    <h3><?= $f3->get('mui_error_details_h3') ?></h3>
    <p><?= $f3->get('ERROR.code') ?> - <?= $f3->get('ERROR.status') ?></p>
    <p><?= $f3->get('ERROR.text') ?></p>
    <h4><?= $f3->get('mui_error_stacktrace_h4') ?></h4>
    <pre><?php var_dump($f3->get( 'ERROR.trace')) ?></pre>

<?php } ?>
    
  </main>
