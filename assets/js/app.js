$(document).ready( function (){
	console.log('init game');
});
$("#msgsendconsole").keydown(function(event){
	switch(event.keyCode)
	{
		case 13 :
			$.ajax({
				url : "play.php",
				type : 'POST',
				data : 'message=' + $("#msgsendconsole").val(),
				success: function(html){
					if (html){
						$("#msgsendconsole").val('');
						$('.formsendmsg').css('display','none');
					}
				}
			});
			break;
	}
});
function sendaction(event)
{
	switch(event.keyCode)
	{/*
		case 13 :
			$.ajax({
				url : "play.php",
				type : 'POST',
				data : 'agir=frapper',
				success: function(html){
					if (html){
					}
				}
			});
			break;*/
		case 83 :
			$.ajax({
				url : "play.php",
				type : 'POST',
				data : 'agir=endormir',
				success: function(html){
					if (html){
					}
				}
			});
			break;
		case 84 :
			if ($('.formsendmsg').css('display')=='none')
			{
				$('.formsendmsg').css('display','block');
				$("#msgsendconsole").focus();
			}
			break;
		case 27 :
			if ($('.formsendmsg').css('display')=='block')
			{
				$("#msgsendconsole").val('');
				$('.formsendmsg').css('display','none');
			}
			break;
		case 37 :
			moveon('gauche');
			break;
		case 38 : 
			moveon('haut');
			break;
		case 39 : 
			moveon('droite');
			break;
		case 40 :
			moveon('bas');
			break;
	}
}
function moveon(direction){
	$.ajax({
		url : "play.php",
		type : 'POST',
		data : 'direction=' + direction,
		success: function(html){
			if (html=='Success'){
			} else if (html=='Passage') {
				$("#porte").get(0).play();
			} else {
				$("#cantmove").get(0).play();
			}
		}
	});
}
