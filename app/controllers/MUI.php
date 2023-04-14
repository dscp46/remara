<?php

class MUI
{
	public function export_js_locale( $f3)
	{
		header('Content-Type: text/javascript; charset=utf-8');
		$mui = array(
			'mui_toast_ack' => $f3->get('mui_toast_ack'),
			'mui_toast_nak' => $f3->get('mui_toast_nak'),
			'mui_toast_err' => $f3->get('mui_toast_err'),
		
			'mui_chpwd_err_lbl' => $f3->get('mui_chpwd_err_lbl'),
			'mui_chpwd_err_body' => $f3->get('mui_chpwd_err_body'),
			'mui_back' => $f3->get('mui_back'),
		
			'mui_chmod_mdl_lbl' => $f3->get('mui_chmod_mdl_lbl'),
			'mui_chmod_mdl_body' => $f3->get('mui_chmod_mdl_body'),
			'mui_chmod_mdl_new_mod' => $f3->get('mui_chmod_mdl_new_mod'),
		
			'mui_txoff_mdl_lbl' => $f3->get('mui_txoff_mdl_lbl'),
			'mui_txoff_mdl_body' => $f3->get('mui_txoff_mdl_body'),
			'mui_txoff_mdl_body2' => $f3->get('mui_txoff_mdl_body2'),
			'mui_deactivate' => $f3->get('mui_deactivate'),
		
			'mui_reboot_mdl_lbl' => $f3->get('mui_reboot_mdl_lbl'),
			'mui_reboot_mdl_body' => $f3->get('mui_reboot_mdl_body'),
			'mui_reboot' => $f3->get('mui_reboot'),
		
			'mui_chpwd_mdl_lbl' => $f3->get('mui_chpwd_mdl_lbl'),
			'mui_chpwd_mdl_note' => $f3->get('mui_chpwd_mdl_note'),
			'mui_chpwd_mdl_note2' => $f3->get('mui_chpwd_mdl_note2'),
			'mui_chpwd_mdl_pswd' => $f3->get('mui_chpwd_mdl_pswd'),
			'mui_chpwd_mdl_pswdconf' => $f3->get('mui_chpwd_mdl_pswdconf'),
			'mui_submit' => $f3->get('mui_submit'),
		
			'mui_delete_mdl_lbl' => $f3->get('mui_delete_mdl_lbl'),
			'mui_delete_mdl_body' => $f3->get('mui_delete_mdl_body'),
			'mui_delete' => $f3->get('mui_delete'),

			'mui_pass_10chars' => $f3->get( 'mui_pass_10chars'),
			'mui_pass_mismatch' => $f3->get( 'mui_pass_mismatch'),

			'mui_none' => $f3->get( 'mui_none'),
			'mui_other' => $f3->get( 'mui_other'),
			'mui_dup_shift' => $f3->get( 'mui_dup_shift'),
			'mui_freq_downlink' => $f3->get( 'mui_freq_downlink'),
			'mui_freq_num' => $f3->get( 'mui_freq_num'),
			'mui_mhz' => $f3->get( 'mui_mhz'),
			'mui_power' => $f3->get( 'mui_power'),
			'mui_mode' => $f3->get( 'mui_mode'),
		);
		$mui = json_encode( $mui);
		$mui = str_replace( '\'', "\\'", $mui); 
		echo("var mui = JSON.parse('{$mui}');");
	}

}
