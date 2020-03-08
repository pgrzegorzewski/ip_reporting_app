window.addEventListener("resize", answearPositionChange);
window.addEventListener("load", answearPositionChange);

//var success_alerts = ["Yeah, You are right;)", "Great, You earn a point", "Wow! Amazing", "Point for You;)"];
//var failure_alerts = ["Maybe next time will be better", "Nope, not this time:(", "Unfortunately not;/", "That s not Your day;/"];

var success_alerts = ["Racja;)", "Punkt dla Ciebie", "Wypaśnie", "Umisz to!"];
var failure_alerts = ["Niestety nie", "Nie tym razem;/", "Słabo:(", "Ej no weź"];


function checkAnswer(button)
{
	var image = document.createElement("img");
	image.setAttribute("height", "50px");
	image.setAttribute("width", "50px");
	var isTrue;
	
	$.ajax({
		type: "POST",
		url: "../php/answer_check.php",
		data:{
			question_answer_id: button.id
		},
		success: function(data){
			this.isTrue = data;
			
			if(this.isTrue == 1){
				image.setAttribute("src", "../resources/img/correct.png");	
				button.classList.remove('btn-info');
				button.classList.add('btn-success');
			}
			else{
				image.setAttribute("src", "../resources/img/error.png");
				button.classList.remove('btn-info');
				button.classList.add('btn-danger');
			}
			
			blockButtons();
			document.getElementById("answear").innerText = drawAnswearAlert(this.isTrue);
			document.getElementById("answear_img").appendChild(image);
			createButtonDrawAgain();
		}
	})	
}


function createButtonDrawAgain()
{
	document.getElementById("refresh").innerText ='Losuj kolejne pytanie';
	
	document.getElementById("refresh_img").hidden = false;
}

function drawNextQuestion()
{
	location.reload();
}

function drawAnswearAlert(answearType)
{
	if(answearType == 1)
		return success_alerts[Math.floor(Math.random() * success_alerts.length)];
	else
		return failure_alerts[Math.floor(Math.random() * failure_alerts.length)];
		
}

function changeClassButton(button, id)
{
	if(id == 1)
	{
		button.classList.remove('btn-info');
		button.classList.add('btn-success');
	}
	else
	{
		button.classList.remove('btn-info');
		button.classList.add('btn-danger');
	}
	
}

function blockButtons()
{
	$(".btn").attr("disabled", "disabled");
}


	
function answearPositionChange() {
	if(window.innerWidth < 765){
		$(".answear_header").height(20);
		//$("#answear").width($(".col-sm-6").width()-100);				//
		//$("#answear").css('text-align',  'center');
		//$("#answear > img").attr('align' ,'middle');
	}
	else
	{
		$(".answear_header").height(76);
	}
}

