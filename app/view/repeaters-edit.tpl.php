<?php $f3=Base::instance();
$ctcss = array( 
          '' => "{$mui_none}",  '67.0' =>  '67.0 Hz',  '69.3' =>  '69.3 Hz',  '71.9' =>  '71.9 Hz',
	 '74.4' =>  '74.4 Hz',  '77.0' =>  '77.0 Hz',  '79.7' =>  '79.7 Hz',  '82.5' =>  '82.5 Hz',
	 '85.4' =>  '85.4 Hz',  '88.5' =>  '88.5 Hz',  '91.5' =>  '91.5 Hz',  '94.8' =>  '94.8 Hz',
	 '97.4' =>  '97.4 Hz', '100.0' => '100.0 Hz', '103.5' => '103.5 Hz', '107.2' => '107.2 Hz', 
	'110.9' => '110.9 Hz', '114.8' => '114.8 Hz', '118.8' => '118.8 Hz', '123.0' => '123.0 Hz', 
	'127.3' => '127.3 Hz', '131.8' => '131.8 Hz', '136.5' => '136.5 Hz', '141.3' => '141.3 Hz',
	'146.2' => '146.2 Hz', '151.4' => '151.4 Hz', '156.7' => '156.7 Hz', '159.8' => '159.8 Hz',
	'162.2' => '162.2 Hz', '165.5' => '165.5 Hz', '167.9' => '167.9 Hz', '171.3' => '171.3 Hz',
	'173.8' => '173.8 Hz', '177.3' => '177.3 Hz', '179.9' => '179.9 Hz', '183.5' => '183.5 Hz',
	'186.2' => '186.2 Hz', '189.9' => '189.9 Hz', '192.8' => '192.8 Hz', '196.6' => '196.6 Hz',
	'199.5' => '199.5 Hz', '203.5' => '203.5 Hz', '206.5' => '206.5 Hz', '210.7' => '210.7 Hz',
	'218.1' => '218.1 Hz', '225.7' => '225.7 Hz', '229.1' => '229.1 Hz', '233.6' => '233.6 Hz',
	'241.8' => '241.8 Hz', '250.3' => '250.3 Hz', '254.1' => '254.1 Hz',
); 
$modes = array(
	'' => "{$mui_none}", 'LSB' => 'LSB', 'USB' => 'USB', 'AM' => 'AM', 'NFM' => 'NFM', 
	'DSTAR' => 'D-Star', 'DMR' => 'DMR', 'C4FM' => 'C4FM', 'TETRA' => 'Tetra', 'M17' => 'Project M17', 
	'FREEDV' => 'FreeDV', 'OTHER' => "{$mui_other}",
);
?>
    <main class="container">
      <div class="py-5 text-center">
        <h2><?= $mui_rpt_file ?></h2>
      </div>
      <form id="inputFrm" action="<?= isset( $repeater) ? "/repeaters/{$repeater['id']}" : '/repeaters/new' ?>" method="POST">
        <input type="hidden" name="token" value="<?= $f3->CSRF ?>" />
<?php if( isset( $repeater) ) { ?>
        <input type="hidden" name="id" value="<?= $repeater['id'] ?>" />
<?php } // isset( $repeater) ?>
        <div class="row g-5">
          <h4 class="mb-3">Informations générales</h4>
        </div>
        
        <div class="row g-3">
          <div class="col-sm-6">
            <label for="qrz"><?= $mui_qrz_mqtt ?></label>
            <input type="text" class="form-control" id="qrz" name="qrz" placeholder="f9zwx-t" value="<?= $repeater['qrz'] ?? '' ?>" required />
          </div>
          <div class="col-sm-6">
            <label for="svx_user"><?= $mui_svx_user ?></label>
            <input type="text" class="form-control" id="svx_user" name="svx_user" placeholder="(42) F9ZWX T" value="<?= $repeater['svx_user'] ?? '' ?>" required />
          </div>
          <div class="col-sm-6">
            <label for="type"><?= $mui_type ?></label>
            <select type="text" class="form-select" id="type" name="type">
<?php 
foreach ( $f3->get('mui_rpt_type') as $key => $label )
{
?>              <option value="<?= $key ?>"<?php if(isset($repeater) && $key == $repeater['type']) { echo(' selected="true"'); } ?>><?= $label ?></option>
<?php
}

?>
            </select>
          </div>
          <div class="col-sm-6">
            <label for="dep"><?= $mui_dep ?></label>
            <input type="text" class="form-control" id="dep" name="dep" placeholder="42" value="<?= $repeater['dep'] ?? '' ?>" >
          </div>
          <div class="col-sm-6">
            <label for="lat"><?= $mui_lat ?></label>
            <input type="text" class="form-control" id="lat" name="lat" placeholder="45.930234" value="<?= $repeater['lat'] ?? '' ?>" >
          </div>
          <div class="col-sm-6">
            <label for="lon"><?= $mui_lon ?></label>
            <input type="text" class="form-control" id="lon" name="lon" placeholder="-3.12039" value="<?= $repeater['lon'] ?? '' ?>">
          </div>
          <div class="col-sm-6">
            <label for="asl"><?= $mui_asl ?></label>
            <input type="text" class="form-control" id="asl" name="asl" placeholder="120" value="<?= $repeater['asl'] ?? '' ?>">
          </div>
          <div class="col-sm-6">
            <label for="height"><?= $mui_height ?></label>
            <input type="text" class="form-control" id="height" name="height" placeholder="15" value="<?= $repeater['height'] ?? '' ?>">
          </div>
          <div class="col-sm-12">
            <label for="comment"><?= $mui_comment ?></label>
            <input type="text" class="form-control" id="comment" name="comment" placeholder="RU15" value="<?= $repeater['comment'] ?? '' ?>">
          </div>
<?php if( !isset( $repeater) ) { ?>
	  <div class="col-sm-6">
            <label for="asl"><?= $mui_password ?></label>
            <input type="password" class="form-control" id="password" name="password" placeholder="" value="" required>
          </div>
          <div class="col-sm-6">
            <label for="height"><?= $mui_passcfrm ?></label>
            <input type="password" class="form-control" id="passcfrm" name="passcfrm" placeholder="" value="" required>
	  </div>
<?php } // !isset($repeater) ?>
        </div>
        <hr class="my-4" />
        <div class="row g-5">
          <h4 class="mb-3"><?= $mui_freq ?></h4>
        </div>
<?php 

if( isset($frequencies) && count($frequencies) > 0 )
{
	$f_num=0;

	foreach ( $frequencies as $f )
	{
?>
        <div class="row mb-3 g-3" id="freq-<?= $f_num ?>">
          <h5 class=""><?= $mui_freq_num ?><?= $f_num ?>&nbsp;&nbsp;&nbsp;&nbsp; <button type="button" id="del_f_<?= $f_num ?>" class="btn btn-danger"><?= $mui_delete ?></button> </h5>
          <div class="col-sm-6">
            <label for="down-<?= $f_num ?>"><?= $mui_freq_downlink ?> (<?= $mui_mhz ?>)</label>
            <input type="text" class="form-control" id="down-<?= $f_num ?>" name="down-<?= $f_num ?>" placeholder="-3.12039" value="<?= $f['down'] ?? '' ?>">
          </div>
          <div class="col-sm-6">
            <label for="dup-<?= $f_num ?>"><?= $mui_dup_shift ?> (<?= $mui_mhz ?>)</label>
            <input type="text" class="form-control" id="dup-<?= $f_num ?>" name="dup-<?= $f_num ?>" placeholder="-3.12039" value="<?= $f['dup'] ?? '' ?>">
          </div>
          <div class="col-sm-4">
            <label for="ctcss-<?= $f_num ?>">CTCSS</label>
            <select class="form-select" id="ctcss-<?= $f_num ?>" name="ctcss-<?= $f_num ?>">
<?php foreach ( $ctcss as $key => $value) { ?>
              <option value="<?= $key ?>"<?php if( $key == $f['ctcss'] ) { echo( ' selected="true"'); } ?>><?= $value ?></option>
<?php } ?>
            </select>
          </div>
          <div class="col-sm-4">
            <label for="power-<?= $f_num ?>"><?= $mui_power ?> (dBm)</label>
            <input type="text" class="form-control" id="power-<?= $f_num ?>" name="power-<?= $f_num ?>" placeholder="10" value="<?= $f['power'] ?? '' ?>">
          </div>
          <div class="col-sm-4">
            <label for="mode-<?= $f_num ?>"><?= $mui_mode ?></label>
            <select class="form-select" id="mode-<?= $f_num ?>" name="mode-<?= $f_num ?>">
<?php 
foreach ( $modes as $key => $label )
{
?>              <option value="<?= $key ?>"<?php if( $key == $f['mode']) { echo( ' selected="true"'); } ?>><?= $label ?></option>
<?php
}

?>
            </select>
          </div>
        </div>
<?php
		++$f_num;
	} // foreach
} // !empty
else
{
?>
        <div class="row mb-3 g-3">
        </div>
<?php
} // empty
?>
<?php 
	if( $f3->exists('SESSION.user') && !empty($f3->get('SESSION.user')) && $f3->get('SESSION.user') != 'sysop') 
      	{ 
?>
        <div class="mb-3 row g-5">
          <div class="col-sm-12">
            <button class="btn btn-success" type="button" id="add_freq"><?= $mui_add ?></button>
          </div>
        </div>
        <hr class="my-4" />
        <div class="row mb-5 g-5">
          <div class="col-sm-12">
            <button type="submit" class="btn btn-primary"><?= $mui_submit ?></button>
          </div>
        </div>
<?php } ?>
      </form>
    </main>
    <script type="text/javascript" src="/assets/js/repeater-editor.js"></script>
    <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="d-flex">
        <div class="toast-body">
          Commande acquittée par f1zev-r.
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
      </div>
    <script type="text/javascript" src="/js/mui"></script><?php
	// Store the Anti CSRF token
	$f3->set( 'SESSION.csrf', $f3->CSRF);
?>
