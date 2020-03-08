var subject;
var classNumber;
var isClassSet = 0;
var isSubjectSet = 0;


function setClass(button, classNumber)
{

	var buttons = document.getElementsByClassName('class_chosen');
	for(i = 0; i < buttons.length; i++)
	{
		buttons[i].classList.remove('class_chosen');
	}

	button.classList.add('class_chosen');
    
	this.classNumber = classNumber; 
    this.isClassSet = 1; 
    if(this.subject)
    {
    	$.post('http://localhost/app/learningapp/php/class_material.php', {subject : this.subject, classNumber: this.classNumber}, function(data){
    		$("#material").html(data);
    	})
    }
}
    
function setSubject(button, subject)
{      
	var buttons = document.getElementsByClassName('subject_chosen');
	for(i = 0; i < buttons.length; i++)
	{
		buttons[i].classList.remove('subject_chosen');
	}

	button.classList.add('subject_chosen');
	
    this.subject = subject;
    this.isSubjectSet = 1; 
    if(this.classNumber)
    {
    	$.post('http://localhost/app/learningapp/php/class_material.php', {subject : this.subject, classNumber: this.classNumber}, function(data){
    		$("#material").html(data);
    	})
    }
}

