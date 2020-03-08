var scrolled = 0;
var result = 0;
var answears = 0;

window.onload = function()
{
	var x = document.getElementsByClassName("predefined_test_btn");
	var idButton = 0;
	for(i = 0; i < x.length; i++)
	{
		x[i].onclick = function(){
			this.className = ("predefined_test_btn clicked");
			this.value = this.id;
			this.name = 'clicked';
			this.style.backgroundColor = "#6ac65b";
			this.style.color = "white";
			predefinedTestIdPass(this.id);
		}
	}
}

function predefinedTestIdPass(id)
{
	//console.log(id);
	$.post('http://localhost/app/learningapp/php/predefined_test_choose.php', {predefinedTestId : id}, function(data){
		$("#test_questions").html(data);
	})
}

function checkTestQuestionAnswer(button, idValue, size, isQuestionImage, username)
{
	var id = 'answear' + (idValue - 1);
	var img_id = 'answear_img' + (idValue - 1);
	var image = document.createElement("img");
	var is_true;
	
	image.setAttribute("height", "50px");
	image.setAttribute("width", "50px");
	
	$.ajax({
		type: "POST",
		url: "../php/test_question_answer_check.php",
		data:{
			question_answer_id: button.id,
			user: username
		},
		success: function(data){
			this.is_true = data;
			
			if(this.is_true == 1){
				image.setAttribute("src", "../resources/img/correct.png");
				button.classList.remove('btn-info');
				button.classList.add('btn-success');
				result = result+1;
			}
			else{
				image.setAttribute("src", "../resources/img/error.png");
				button.classList.remove('btn-info');
				button.classList.add('btn-danger');
			}
			blockTestButtons(idValue);
			document.getElementById(id).innerText = drawAnswearAlert(this.is_true);
			document.getElementById(img_id).appendChild(image);

			if(answears == size){
				
				printResult();
				setTimeout(function(){ 
					location.reload();
				}, 10000);
				
			}
			
		}
	})
	
	blockTestButtons(idValue);
	answears = answears + 1;
	scrollDownToNextQuestion(isQuestionImage);
	console.log(answears);
	console.log(size);
	
}

function blockTestButtons(id)
{
	var idName = "question"+id;
	$('#' + idName).find(".btn").attr("disabled", "disabled");
}

function scrollDownToNextQuestion(isImage)
{
	var scrollLenght = 0;
	if(isImage == 0){
		scrollLenght = 215;
	}
	else{
		scrollLenght = 887;
	}
	scrolled = scrolled + scrollLenght;
	$('html, body').animate({
	        scrollTop:  scrolled
	   }, 900);
}

function printResult()
{
	
	var resultComment;
	if(answears == result){
		resultComment = 'Kujon 100/100';
	}else if(result/answears < 1 && result/answears >= 0.8){
		resultComment = 'Rządzisz! super;)';
	}else if(result/answears < 0.8 && result/answears >= 0.6){
		resultComment = 'Zdane:)';
	}else if(result/answears < 0.6 && result/answears >= 0.5){
		resultComment = 'Zdane ale mogłoby być lepiej!';
	}else if(result/answears < 0.5){
		resultComment = 'Lepiej jakbys jeszcze sobie powtórzył materiał;/';
	}
	document.getElementById('result_text').hidden = false;
	document.getElementById('result_award').hidden = false;
	document.getElementById('result_text').innerText = 'Twój wynik ' + result + '/' + answears + ' ' +  resultComment;
		
}