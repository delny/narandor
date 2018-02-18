$(document).ready( function (){
	console.log('init game');
	getcarte();
	getmsg();
});
setInterval (function(){
	getcarte();
	getmsg();
},2000);
setInterval (function(){
	refreshbot();
},1900);
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
						getcarte();
						getmsg();
					}
				}
			});
			break;
	}
});
function sendaction(event)
{
	switch(event.keyCode)
	{
		case 13 :
			$.ajax({
				url : "play.php",
				type : 'POST',
				data : 'agir=frapper',
				success: function(html){
					if (html){
						getcarte();
						getmsg();
					}
				}
			});
			break;
		case 83 :
			$.ajax({
				url : "play.php",
				type : 'POST',
				data : 'agir=endormir',
				success: function(html){
					if (html){
						getcarte();
						getmsg();
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
				getcarte();
				getmsg();
			} else if (html=='Passage') {
				getcarte();
				getmsg();
				$("#porte").get(0).play();
			} else {
				$("#cantmove").get(0).play();
			}
		}
	});
}
function resetconsole () {
	$.ajax({
			url : "play.php",
			type : 'POST',
			data : 'reset=console',
			success: function(html){
				if (html){
					getmsg();
				}
			}
		});
}
function getcarte () {
	$.ajax({
			url : "carte.php",
			type : 'POST',
			data : 'recherche=ok',
			success: function(html){
				if (html){
					$("#cartedejeu").html(html);
				}
			}
		});
}
function getmsg () {
	$.ajax({
			url : "inventory.php",
			type : 'POST',
			data : 'get=inventory',
			success: function(html){
				if (html){
					$("#myinventory").html(html);
				}
			}
		});
}
function refreshbot () {
	$.ajax({
			url : "bot.php",
			type : 'POST',
			data : 'get=bot',
			success: function(html){
				if (html){
					getcarte();
					getmsg();
				}
			}
		});
}
function useobject ($idobjet) {
	$.ajax({
			url : "play.php",
			type : 'POST',
			data : 'use=' + $idobjet,
			success: function(html){
				if (html){
					getcarte();
					getmsg();
				}
			}
		});
}