<?php $f3=Base::instance(); ?>
    <main class="container">
      <div class="py-5 text-center">
        <h2><?= $mui_rpt_list ?></h2>
      </div>
      <div class="row mb-3 g-5">
      <table class="table table-striped table-sm" style="overflow: visible;">
        <tr>
          <th><?= $mui_qrz_mqtt ?></th>
	  <th><?= $mui_svx_user ?></th>
	  <th><?= $mui_type ?></th>
	  <th><?= $mui_descr ?></th>
          <th></th>
        </tr>
<?php 

if( isset($repeaters) && count($repeaters) > 0 )
{
	foreach ( $repeaters as $rpt )
	{
		// Skip bridges
		//if( $rpt['type'] == 'bridge' )
		//	continue;
?>
        <tr>
          <td id="qrz-<?= $rpt['id'] ?>"><?= $rpt['qrz'] ?></td>
          <td><?= $rpt['svx_user'] ?></td>
          <td><?= $mui_rpt_type[$rpt['type']] ?></td>
          <td><?= $rpt['comment'] ?></td>
          <td>
            <div class="btn-group">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><?= $mui_rpt_action_action ?></button>
	      <ul class="dropdown-menu">
<?php if( $rpt['type'] != 'bridge' ) { ?>
                <li><h6 class="dropdown-header"><?= $mui_rpt_header_remote ?></h6></li>
                <li><a class="dropdown-item" id="ping-<?= $rpt['id'] ?>"><?= $mui_rpt_action_ping ?></a></li>
                <li><a class="dropdown-item" id="chmod-<?= $rpt['id'] ?>"><?= $mui_rpt_action_chmod ?></a></li>
                <li><a class="dropdown-item" id="disc-<?= $rpt['id'] ?>"><?= $mui_rpt_action_disconnect ?></a></li>
                <li><a class="dropdown-item" id="txon-<?= $rpt['id'] ?>"><?= $mui_rpt_action_txon ?></a></li>
                <li><a class="dropdown-item" id="txoff-<?= $rpt['id'] ?>"><?= $mui_rpt_action_txoff ?></a></li>
                <li><a class="dropdown-item" id="reboot-<?= $rpt['id'] ?>"><?= $mui_rpt_action_reboot ?></a></li>
		<li><hr class="dropdown-divider"></li>
<?php } ?>
                <li><h6 class="dropdown-header"><?= $mui_rpt_header_admin ?></h6></li>
                <li><a class="dropdown-item" href="/repeaters/<?= $rpt['id'] ?>"><?= $mui_rpt_action_edit ?></a></li>
                <li><a class="dropdown-item" id="chpwd-<?= $rpt['id'] ?>"><?= $mui_rpt_action_chpwd ?></a></li>
                <li><a class="dropdown-item" id="delete-<?= $rpt['id'] ?>"><?= $mui_delete ?></a></li>
              </ul>
            </div>
          </td>
        </tr>
<?php
	} // foreach
} // !empty($repeaters)
else
{
?>
        <tr>
	<td colspan="5"><i><?= $mui_noresult ?></i></td>
        </tr>
<?php } // empty($repeaters) ?>
      </table>
<?php 
	if( $f3->exists('SESSION.user') && !empty($f3->get('SESSION.user')) && $f3->get('SESSION.user') != 'sysop') 
      	{ 
?>
      </div>
      <div class="mb-5 row g-5">
        <div class="col-sm-12">
	  <a class="btn btn-success" role="button" href="/repeaters/new"><?= $mui_add ?></a>
        </div>
      </div>
<?php } ?>
    </main>
    <div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3">
    </div>
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="confirmModalLabel">Suppression d'un relais</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div id="confirmModalBody" class="modal-body">...</div>
	  <div class="modal-footer">
            <input type="hidden" id="confirmModalCmd" value="" />
            <input type="hidden" id="confirmModalArg" value="" />
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Sortir</button>
            <button type="button" id="confirmModalSend" class="btn btn-danger">Supprimer</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="alertModal" aria-hidden="true" aria-labelledby="alertModalLabel" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="alertModalLabel"></h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="alertModalBody">...</div>
          <div class="modal-footer">
            <button class="btn btn-primary" id="alertModalSend">Retour</button>
          </div>
        </div>
      </div>
    </div>
    <script type="text/javascript" src="/js/rooms"></script>
    <script type="text/javascript" src="/js/mui"></script>
    <script type="text/javascript" src="/assets/js/repeater-list.js"></script>
