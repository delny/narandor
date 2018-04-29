$(document).ready( function (){
	console.log('init game');
});
/*
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
});*/
function sendaction(event)
{
	switch(event.keyCode)
	{
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
	}
}
