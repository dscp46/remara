// Repeaters.js

class RepeaterList
{
	#toastCounter
	#toastContainer
	#regstdLinks
	#alertModal
	#confirmModal
	#confirmModalCmd
	#confirmModalArg

	show_toast( text, color)
	{
		var toastId = "toast-"+(this.toastCounter++);
		var toastContainer = document.getElementById( 'toastContainer');

		var toast = document.getElementById( toastId);
		if( arguments.length == 1)
		{
			color = 'text-bg-primary';
		}
		else
		{
			color = 'text-bg-'+color;
		}
		
		var t_outer = document.createElement('div');
		t_outer.id = toastId;
		t_outer.classList.add('toast');
		t_outer.classList.add('align-items-center');
		t_outer.classList.add( color);
		t_outer.classList.add('border-0');
		t_outer.setAttribute( 'role', 'alert');
		t_outer.setAttribute( 'aria-live', 'assertive');
		t_outer.setAttribute( 'aria-atomic', 'true');

		var t_dflex = document.createElement( 'div');
		t_dflex.classList.add('d-flex');

		var t_body = document.createElement( 'div');
		t_body.id = toastId+'Body';
		t_body.classList.add( 'toast-body');
		t_body.innerHTML = text;

		var t_button = document.createElement( 'button');
		t_button.setAttribute( 'type', 'button');
		t_button.classList.add('btn-close');
		t_button.classList.add('btn-close-white');
		t_button.classList.add('me-2');
		t_button.classList.add('m-auto');
		t_button.setAttribute( 'data-bs-dismiss', 'toast');
		t_button.setAttribute( 'aria-label', 'Close');


		t_dflex.append(t_body);
		t_dflex.append(t_button);
		t_outer.append(t_dflex);
		toastContainer.append( t_outer);

		var t = bootstrap.Toast.getOrCreateInstance( document.getElementById(toastId));
		t.show();
	}

	xhr_response_handler( xhr)
	{
		if( xhr.readyState != XMLHttpRequest.DONE)
			return;

		console.log( "Received response with status code: " + xhr.status);

		if(xhr.status == 200 || xhr.status == 204)
		{
			this.show_toast( mui.mui_toast_ack, 'success');

		}
		else if(xhr.status >= 501)
			this.show_toast( mui.mui_toast_nak, 'warning');
		else
			this.show_toast( mui.mui_toast_err, 'danger');

		// Reload page if prompted to do by the called API endpoint.
		var must_reload = xhr.getResponseHeader( 'X-Must-Reload');
		if( must_reload != null && must_reload != "false" && must_reload != 0 )
			location.reload();
	}

	send_command( method, url, body)
	{
		var req = new XMLHttpRequest();
		req.addEventListener( "readystatechange", () => { this.xhr_response_handler(req); });
		req.open( method, url);
		if( method == "POST" )
			req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		req.setRequestHeader( "X-Requested-With", "XMLHttpRequest");
		if( body != null)
			req.send(body);
		else
			req.send();
	}

	set_alert_modal_body( title, body, callback)
	{
		document.getElementById('alertModalLabel').innerHTML = title;
		document.getElementById('alertModalBody').innerHTML = body;
		document.getElementById('alertModalSend').addEventListener( "click", callback);
	}
	confirm_modal_send( e)
	{
		var cmd = this.confirmModalCmd.value;
		var arg = this.confirmModalArg.value;
		console.log("Confirmed action " + cmd + "     arg: " + arg);
		switch( cmd)
		{
		case 'chmod':
			var target = document.getElementById('chmodArg').value;
			this.send_command( 'POST', '/api/v1/repeaters/'+arg+'/chmod' , "room="+target);
			this.confirmModal.hide();
			break;

		case 'txoff':
			this.send_command( 'POST', '/api/v1/repeaters/'+arg+'/txoff' , null);
			this.confirmModal.hide();
			break;

		case 'reboot':
			this.send_command( 'POST', '/api/v1/repeaters/'+arg+'/reboot' , null);
			this.confirmModal.hide();
			break;

		case 'chpwd':
			var passwd     = document.getElementById('passwdArg').value;
			var passwdCfrm = document.getElementById('passwdCfrmArg').value;
			if( passwd.length >= 10 && passwd == passwdCfrm )
			{
				this.send_command( 'POST', '/api/v1/repeaters/'+arg+'/passwd' , "password="+encodeURIComponent( passwd));
				this.confirmModal.hide();
			} else {
				// Warn of unsatisfied conditions
				this.set_alert_modal_body( mui.mui_chpwd_err_lbl, mui.mui_chpwd_err_body, () => { this.alertModal.hide(); this.confirmModal.show(); });
				this.confirmModal.hide( );
				this.alertModal.show();
			}
			break;

		case 'delete':
			this.send_command( 'DELETE', '/api/v1/repeaters/'+arg , null);
			this.confirmModal.hide();
			break;
		default:
		}
	}

	constructor()
	{
		this.toastCounter = 0;
		this.toastContainer = document.getElementById('toastContainer');
		this.regstdLinks = new Array();
		this.alertModal = new bootstrap.Modal('#alertModal');
		this.confirmModal = new bootstrap.Modal('#confirmModal');
		this.confirmModalCmd = document.getElementById('confirmModalCmd');
		this.confirmModalArg = document.getElementById('confirmModalArg');
		this.bind_events();
		document.getElementById('confirmModalSend').addEventListener( 'click', this.confirm_modal_send.bind(this));
	}

	set_confirm_modal_body( title, cmd, arg, modalBody, confirmText, buttonType)
	{
		if( arguments.length == 5 )
		{
			buttonType = 'btn-primary';
		}

		var confirmModalSend = document.getElementById('confirmModalSend');
		document.getElementById('confirmModalLabel').innerHTML = title;
		this.confirmModalCmd.value = cmd;
		this.confirmModalArg.value = arg;
		document.getElementById('confirmModalBody').innerHTML = modalBody;
		
		confirmModalSend.classList.remove( ...confirmModalSend.classList);
		confirmModalSend.classList.add( 'btn');
		confirmModalSend.classList.add( buttonType);
		confirmModalSend.innerHTML = confirmText;
	}

	// Event triggered when the users wants to ping a repeater
	ping_repeater( e)
	{
		var r_ping = /^ping-(\d+)$/;
		var rpt_num = e.target.id.match( r_ping)[1];
		this.send_command( 'POST', '/api/v1/repeaters/'+rpt_num+'/ping' , null);
	}

	// Event triggered when the user wants to change the repeater module
	chmod_repeater( e)
	{
		var r_chmod = /^chmod-(\d+)$/;
		var rpt_num = e.target.id.match( r_chmod)[1];
		var qrz = document.getElementById('qrz-'+rpt_num).innerHTML;
		var content = "<div class=\"row\"><div class=\"col-sm-12\"><p>"+mui.mui_chmod_mdl_body+" <b>"+qrz+"</b>.</p></div>";
		content += "<div class=\"col-sm-12\">";
		content += "<label for=\"chmodArg\">"+mui.mui_chmod_mdl_new_mod+"</label><select class=\"form-select\" id=\"chmodArg\">";
		for( const [key, val] of Object.entries(rooms) )
		{
			content += `<option value="${key}">${val}</object>`;
		}
		content += "</select>";
		content += "</div></div>";
		this.set_confirm_modal_body( 
			mui.mui_chmod_mdl_lbl, 
			'chmod', 
			rpt_num, 
			content, 
			mui.mui_submit
		);
		this.confirmModal.show();
	}

	// Event triggered when the user wants to disconnect a repeater
	disconnect_repeater( e)
	{
		var r_disc = /^disc-(\d+)$/;
		var rpt_num = e.target.id.match( r_disc)[1];
		this.send_command( 'POST', '/api/v1/repeaters/'+rpt_num+'/disc' , null);
	}

	// Event triggered when the user wants to enable a repeater logic
	txon_repeater( e)
	{
		var r_txon = /^txon-(\d+)$/;
		var rpt_num = e.target.id.match( r_txon)[1];
		this.send_command( 'POST', '/api/v1/repeaters/'+rpt_num+'/txon' , null);
	}

	// Event triggered when the user wants to disable a repeater logic
	txoff_repeater( e)
	{
		var r_txoff = /^txoff-(\d+)$/;
		var rpt_num = e.target.id.match( r_txoff)[1];
		var qrz = document.getElementById('qrz-'+rpt_num).innerHTML;
		this.set_confirm_modal_body( 
			mui.mui_txoff_mdl_lbl, 
			'txoff', 
			rpt_num, 
			mui.mui_txoff_mdl_body+" <b>"+qrz+"</b>.<br/>"+mui.mui_txoff_mdl_body2,
			mui.mui_deactivate,
			"btn-warning"
		);
		this.confirmModal.show();
	}

	// Event triggered when the user wants to reboot a repeater logic
	reboot_repeater( e)
	{
		var r_reboot = /^reboot-(\d+)$/;
		var rpt_num = e.target.id.match( r_reboot)[1];
		var qrz = document.getElementById('qrz-'+rpt_num).innerHTML;
		this.set_confirm_modal_body( 
			mui.mui_reboot_mdl_lbl, 
			'reboot', 
			rpt_num, 
			mui.mui_reboot_mdl_body+" <b>"+qrz+"</b>.",
			mui.mui_reboot,
			"btn-danger"
		);
		this.confirmModal.show();
	}


	change_password( e)
	{
		var r_chpwd = /^chpwd-(\d+)$/;
		var rpt_num = e.target.id.match( r_chpwd)[1];
		var qrz = document.getElementById('qrz-'+rpt_num).innerHTML;
		console.log( "Changing password for div " + rpt_num );
		var content = "<div class=\"row\"><div class=\"col-sm-12\"><p>"+mui.mui_chpwd_mdl_note+" <b>"+qrz+"</b>.</p></div>";
		content += "<div class=\"col-sm-12\"><p>"+mui.mui_chpwd_mdl_note2+"</p></div>";
		content += "<div class=\"col-sm-12\"><label for=\"passwdArg\">"+mui.mui_chpwd_mdl_pswd+"</label><input type=\"password\" class=\"form-control\" id=\"passwdArg\" value=\"\"></div>";
		content += "<div class=\"col-sm-12\"><label for=\"passwdCfrmArg\">"+mui.mui_chpwd_mdl_pswdconf+"</label><input type=\"password\" class=\"form-control\" id=\"passwdCfrmArg\" value=\"\"></div>";
		content += "</div>";
		this.set_confirm_modal_body( 
			mui.mui_chpwd_mdl_lbl, 
			'chpwd', 
			rpt_num, 
			content,
			mui.mui_submit,
		);
		this.confirmModal.show();
}

	delete_repeater( e)
	{
		var r_delete = /^delete-(\d+)$/;
		var rpt_num = e.target.id.match( r_delete)[1];
		console.log( "Deleting repeater for div " + rpt_num );
		var qrz = document.getElementById('qrz-'+rpt_num).innerHTML;
		this.set_confirm_modal_body( 
			mui.mui_delete_mdl_lbl, 
			'delete', 
			rpt_num, 
			mui.mui_delete_mdl_body+" <b>"+qrz+"</b>.", 
			mui.mui_delete,
			"btn-danger"
		);
		this.confirmModal.show();
	}

	bind_events()
	{
		var links = document.getElementsByTagName( 'a');
		var nb_links = links.length;
		var r_ping = /^ping-(\d+)$/;
		var r_chmod = /^chmod-(\d+)$/;
		var r_disc = /^disc-(\d+)$/;
		var r_txon = /^txon-(\d+)$/;
		var r_txoff = /^txoff-(\d+)$/;
		var r_reboot = /^reboot-(\d+)$/;
		var r_chpwd = /^chpwd-(\d+)$/;
		var r_delete = /^delete-(\d+)$/;

		for( var i=0; i<nb_links; ++i)
		{
			if( this.regstdLinks.indexOf( links[i].id) == -1 )
			{
				if( r_ping.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.ping_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_disc.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.disconnect_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_chmod.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.chmod_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_txon.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.txon_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_txoff.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.txoff_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_reboot.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.reboot_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_chpwd.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.change_password.bind(this));
					this.regstdLinks.push( links[i].id);
				}

				if( r_delete.test( links[i].id) == true )
				{
					links[i].addEventListener( "click", this.delete_repeater.bind(this));
					this.regstdLinks.push( links[i].id);
				}
			}
		}
	}

}

document.addEventListener('DOMContentLoaded', function() { var r=new RepeaterList();} );
