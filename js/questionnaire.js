var questionNumber;

function drawQuestion()
{
	return Math.floor(Math.random() * questionNumber);
}

function answear(answear, is_true)
{
		this.answear = answear;
		this.is_true = is_true;
}

var answear1 = new answear ("Warsaw", 1);
var answear2 = new answear ("Bratislav", 0);
var answear3 = new answear ("Berlin", 0);
var answear4 = new answear ("Budapest", 0);

function question(question, answear, answear2, answear3, answear4)
{
	this.question = question;
	this.answear = answear;
	this.answear2 = answear2;
	this.answear3 = answear3;
	this.answear4 = answear4;

}

var question1 = new question(
								 "What is the capital of Poland?",
								 answear1, 
								 answear2,
								 answear3,
								 answear4
							)

function printInfo(information)
{
	document.write(information);
}