$(document).ready(function(){ 
	$('#r_lgn').keyup(function(){
		$('#er_lgn').html('');
		var pseudo = $(this).val();
		if(pseudo != ""){
			$.ajax({
				type: 'GET',
				url: URL + '/function/v_register.php',
				data: 'r_lgn=' + encodeURIComponent(pseudo) + '&type=' + 0,
				success: function(data){
					if(data == "true"){
						document.getElementById('r_lgn').style.border = "1px solid #26A65B";
						
					}else{
						document.getElementById('r_lgn').style.border = "1px solid #D91E18";
						$('#er_lgn').html(data).fadeIn(2000);
					}
				}
			});
		}
	}); 
});

$(document).ready(function(){
	var cpt = 0;
    $('#send_pfe').click(function(){
	    cpt += 1;
	    if(cpt > 1){
		    $('#send_pfe').prop('disabled',true);
	    }
    });
});

$(document).ready(function(){
	$('#a_sp').click(function(e){
		e.preventDefault();
	});
	$('#btn_dlt').click(function(){
		$('#sb_dlt').submit();
	});
	$('#btn_wrn').click(function(){
		$('#sb_wrn').submit();
	});
});

$(document).ready(function(){
	
	setTimeout(closefauto, 3000);
	
	function closefauto(){
		$(".closef").parent().slideUp();
	}
	
	
	$('.closef').click(function(e){
		e.preventDefault();
		$(this).parent().slideUp();
	});
});

$(document).ready(function(){
	$('#r_ville').autocomplete({
    	source : URL + '/_function/ville',
    	minLength: 2,
    	delay: 250
	});
	$( "#r_ville" ).autocomplete( "option", "appendTo", "#r_form" );
});

$(document).ready(function(){ 
	$('#r_mail').keyup(function(){
		$('#er_mail').html('');
		var mail = $(this).val();
		if(mail != ""){
			$.ajax({
				type: 'GET',
				url: URL + '/function/v_register.php',
				data: 'r_mail=' + encodeURIComponent(mail) + '&type=' + 1,
				success: function(data){
					if(data == "true"){
						document.getElementById('r_mail').style.border = "1px solid #26A65B";
						
					}else{
						document.getElementById('r_mail').style.border = "1px solid #D91E18";
						$('#er_mail').html(data).fadeIn(2000);
					}
				}
			});
		}
	}); 
});

$(document).one('focus.autoExpand', 'textarea.autoExpand', function(){
    var savedValue = this.value;
    this.value = '';
    this.baseScrollHeight = this.scrollHeight;
    this.value = savedValue;
    
}).on('input.autoExpand', 'textarea.autoExpand', function(){
    var minRows = this.getAttribute('data-min-rows')|0, rows;
    this.rows = minRows;
    rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 20);
    this.rows = minRows + rows;
});