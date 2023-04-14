// Repeaters.js

class RepeaterEditor
{
	#rgstd_del_buttons;
	#add_freq_bound;
	#freq_max;

	constructor()
	{
		this.rgstd_del_buttons = new Array();
		this.add_freq_bound = false;
		this.freq_max = 0;
		this.bind_events();
	}
	
	delete_frequency( e)
	{
		var r_delfreq = /^del_f_(\d+)/;
		var f_num = e.target.id.match( r_delfreq);
		console.log( "Deleting frequency "+f_num[1]);
		document.getElementById("freq-"+f_num[1]).remove();
	}

	add_frequency()
	{
		var next_freq = ++this.freq_max;
		console.log("Adding frequency #"+next_freq);
		
		var newNode = document.createElement('div');
		newNode.classList.add( "row");
		newNode.classList.add( "mb-3");
		newNode.classList.add( "g-3");
		newNode.id = "freq-"+next_freq;
		newNode.innerHTML = '<h5 class="">'+mui.mui_freq_num+next_freq+'&nbsp;&nbsp;&nbsp;&nbsp; <button type="button" id="del_f_'+ next_freq +'" class="btn btn-danger">Supprimer</button> </h5>' +
			'<div class="col-sm-6">' +
			'<label for="down-'+next_freq+'">'+mui.mui_freq_downlink+' ('+mui.mui_mhz+')</label>' +
			'<input type="text" class="form-control" id="down-'+next_freq+'" name="down-'+next_freq+'" placeholder="-3.12039" value="432.5375">' +
			'</div>' +
			'<div class="col-sm-6">' +
			'<label for="dup-'+next_freq+'">'+mui.mui_dup_shift+' ('+mui.mui_mhz+')</label>' +
			'<input type="text" class="form-control" id="dup-'+next_freq+'" name="dup-'+next_freq+'" placeholder="-3.12039" value="0">' +
			'</div>' +
			'<div class="col-sm-4">' +
			'<label for="ctcss-'+next_freq+'">CTCSS</label>' +
			'<select class="form-select" id="ctcss-'+next_freq+'" name="ctcss-'+next_freq+'">' +
			'<option value="">'+mui.mui_none+'</option>' +
			'<option value="67.0">67.0 Hz</option>' +
			'<option value="69.3">69.3 Hz</option>' +
			'<option value="71.9">71.9 Hz</option>' +
			'<option value="74.4">74.4 Hz</option>' +
			'<option value="77.0">77.0 Hz</option>' +
			'<option value="79.7">79.7 Hz</option>' +
			'<option value="82.5">82.5 Hz</option>' +
			'<option value="85.4">85.4 Hz</option>' +
			'<option value="88.5">88.5 Hz</option>' +
			'<option value="91.5">91.5 Hz</option>' +
			'<option value="94.8">94.8 Hz</option>' +
			'<option value="97.4">97.4 Hz</option>' +
			'<option value="100.0">100.0 Hz</option>' +
			'<option value="103.5">103.5 Hz</option>' +
			'<option value="107.2">107.2 Hz</option>' +
			'<option value="110.9">110.9 Hz</option>' +
			'<option value="114.8">114.8 Hz</option>' +
			'<option value="118.8">118.8 Hz</option>' +
			'<option value="123.0" selected="true">123.0 Hz</option>' +
			'<option value="127.3">127.3 Hz</option>' +
			'<option value="131.8">131.8 Hz</option>' +
			'<option value="136.5">136.5 Hz</option>' +
			'<option value="141.3">141.3 Hz</option>' +
			'<option value="146.2">146.2 Hz</option>' +
			'<option value="151.4">151.4 Hz</option>' +
			'<option value="156.7">156.7 Hz</option>' +
			'<option value="159.8">159.8 Hz</option>' +
			'<option value="162.2">162.2 Hz</option>' +
			'<option value="165.5">165.5 Hz</option>' +
			'<option value="167.9">167.9 Hz</option>' +
			'<option value="171.3">171.3 Hz</option>' +
			'<option value="173.8">173.8 Hz</option>' +
			'<option value="177.3">177.3 Hz</option>' +
			'<option value="179.9">179.9 Hz</option>' +
			'<option value="183.5">183.5 Hz</option>' +
			'<option value="186.2">186.2 Hz</option>' +
			'<option value="189.9">189.9 Hz</option>' +
			'<option value="192.8">192.8 Hz</option>' +
			'<option value="196.6">196.6 Hz</option>' +
			'<option value="199.5">199.5 Hz</option>' +
			'<option value="203.5">203.5 Hz</option>' +
			'<option value="206.5">206.5 Hz</option>' +
			'<option value="210.7">210.7 Hz</option>' +
			'<option value="218.1">218.1 Hz</option>' +
			'<option value="225.7">225.7 Hz</option>' +
			'<option value="229.1">229.1 Hz</option>' +
			'<option value="233.6">233.6 Hz</option>' +
			'<option value="241.8">241.8 Hz</option>' +
			'<option value="250.3">250.3 Hz</option>' +
			'<option value="254.1">254.1 Hz</option>' +
			'</select>' +
			'</div>' +
			'<div class="col-sm-4">' +
			'<label for="power-'+next_freq+'">'+mui.mui_power+' (dBm)</label>' +
			'<input type="text" class="form-control" id="power-'+next_freq+'" name="power-'+next_freq+'" placeholder="10" value="">' +
			'</div>' +
			'<div class="col-sm-4">' +
			'<label for="mode-'+next_freq+'">'+mui.mui_mode+'</label>' +
			'<select class="form-select" id="mode-'+next_freq+'" name="mode-'+next_freq+'">' +
			'<option value="">'+mui.mui_none+'</option>' +
			'<option value="LSB">LSB</option>' +
			'<option value="USB">USB</option>' +
			'<option value="AM">AM</option>' +
			'<option value="NFM" selected="true">NFM</option>' +
			'<option value="DSTAR">D-Star</option>' +
			'<option value="DMR">DMR</option>' +
			'<option value="C4FM">C4FM</option>' +
			'<option value="TETRA">Tetra</option>' +
			'<option value="M17">Project M17</option>' +
			'<option value="FREEDV">FreeDV</option>' +
			'<option value="OTHER">'+mui.mui_other+'</option>' +
			'</select>' +
			'</div>' +
			'</div>';
		document.getElementById('add_freq').parentElement.parentElement.before(newNode);
		document.getElementById('del_f_'+next_freq).addEventListener( "click", this.delete_frequency );	
	}
	
	bind_events()
	{
		var    divs = document.getElementsByTagName('div');
		var    btns = document.getElementsByTagName('button');
		var  nb_div = divs.length;
		var  nb_btn = btns.length;
		var r_delfreq = /^del_f_(\d+)/;

		for( var i=0; i<nb_btn; ++i)
		{
			// For each "Delete Frequency" button
			if( r_delfreq.test( btns[i].id) == true )
			{
				// Check if we've already added the event listener
				if( this.rgstd_del_buttons.indexOf( btns[i].id) == -1 )
				{
					// If not, add the event listener
					btns[i].addEventListener( "click", this.delete_frequency );
					
					// Add the button in the list for which we've added the handler
					this.rgstd_del_buttons.push( btns[i].id);
					
					// Bump the value of freq_max if we're superior to the previous value;
					var f_num = btns[i].id.match( r_delfreq);
					if( this.freq_max < f_num[1] )
						this.freq_max = f_num[1];
				}
			}
			
			// Bind the "Add Frequency" button
			if( this.add_freq_bound == false && btns[i].id == "add_freq" )
			{
				btns[i].addEventListener( "click", this.add_frequency.bind(this) );
				this.add_freq_bound = true;
			}
		}

		// Bind the password validation constraint if applicable
		if( document.getElementById( 'password') != null )
		{
			document.getElementById('inputFrm').addEventListener( "click", function() 
			{
				var i_pass     = document.getElementById( 'password');
				var i_passCfrm = document.getElementById( 'passcfrm');
				i_pass.setCustomValidity( (i_pass.value.length <= 9) ? mui.mui_pass_10chars : '');
				i_passCfrm.setCustomValidity( (i_pass.value != i_passCfrm.value) ? mui.mui_pass_mismatch : '');
			});
		}
	}
}

document.addEventListener('DOMContentLoaded', function() { var r=new RepeaterEditor();} );
