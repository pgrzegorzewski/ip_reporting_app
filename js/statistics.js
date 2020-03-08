var colors = ['rgba(72, 111, 175, 0.6)', 'rgba(112, 239, 121, 0.6)', 'rgba(232, 136, 92, 0.6)', 'rgba(217, 232, 92, 0.6)', 'rgba(183, 20, 36, 0.6)', 
				'rgba(68, 189, 196, 0.6)', 'rgba(213, 141, 239, 0.6)', 'rgba(48, 135, 33, 0.6)', 'rgba(242, 226, 213, 0.6)'];

window.onload = function()
{
	
	$.ajax({
		type: "POST",
		url: "../php/test_ajax/user_question_answer_statistics.php",

		success: function(data){
			
			var statistics = {
				true_cnt : [],
				false_cnt : [],
				day : []					
			}
			
			var len = JSON.parse(data).length;
			data = JSON.parse(data);
			
			for(var i = 0; i < len; i++)
			{
				statistics.day.push(data[i].created_date);
				statistics.true_cnt.push(data[i].true_cnt);
				statistics.false_cnt.push(data[i].false_cnt);
			}
			
			var ctx = document.getElementById("user_answer_statistics").getContext('2d');

			var data = {
					  labels: statistics.day,
					  datasets: [{
					    label: 'Poprawne',
					    backgroundColor: 'rgba(75, 192, 192, 0.4)',
					    data: statistics.true_cnt
					  }, {
					    label: "Niepoprawne",
					    backgroundColor: 'rgba(255, 99, 132, 0.4)',
					    data: statistics.false_cnt
					  }]
					};

					var myBarChart = new Chart(ctx, {
					  type: 'bar',
					  data: data,
					  options: {
					    barValueSpacing: 20,
					    scales: {
					      yAxes: [{
					        ticks: {
					          min: 0,
					        }
					      }]
					    }
					  }
				});
			
		}
	});
	
	
	$.ajax({
		type: "POST",
		url: "../php/test_ajax/user_question_answer_current_month_statistics.php",

		success: function(data){
			
			var obj = JSON.parse(data);
			var ctx = document.getElementById("user_answer_current_month_statistics").getContext('2d');

			var data = {
							labels:  ["Poprawne", "Niepoprawne"],
							datasets: [{
								  data: [obj[0].true_cnt, obj[0].false_cnt],
								  backgroundColor: [ 'rgb(51, 204, 51, 0.6)', 'rgba(255, 99, 132, 0.6)']
							}]
						};
			var myPieChart = new Chart(ctx,{
			    type: 'pie',
			    data: data
			});
			
		}
	});

	$.ajax({
		type: "POST",
		url: "../php/test_ajax/user_question_answer_last_month_statistics.php",

		success: function(data){
			
			var obj = JSON.parse(data);
			var ctx = document.getElementById("user_answer_last_month_statistics").getContext('2d');

			var data = {
							labels: ["Poprawne", "Niepoprawne"],
							datasets: [{
								  data: [obj[0].true_cnt, obj[0].false_cnt],
								  backgroundColor: [ 'rgb(51, 204, 51, 0.6)', 'rgba(255, 99, 132, 0.6)']
							}]
						};
			var myPieChart = new Chart(ctx,{
			    type: 'pie',
			    data: data
			});
			
		}
	});
	
	$.ajax({
		type: "POST",
		url: "../php/test_ajax/user_activity_summary.php",

		success: function(data){
			
			var obj = JSON.parse(data);
			var ctx = document.getElementById("user_activity_summary").getContext('2d');
			console.log(data);
			var data = {
							labels: ["Zalogowano", "Niezalogowano"],
							datasets: [{
								  data: [obj[0].days_logged, obj[0].days_not_logged],
								  backgroundColor: [ 'rgb(204, 255, 153, 0.6)', 'rgba(204, 0, 102, 0.6)']
							}]
						};
			var myDoughnutChart = new Chart(ctx,{
			    type: 'doughnut',
			    data: data
			});
			
		}
	});
	
	
	$.ajax({
		type: "POST",
		url: "../php/test_ajax/user_category_question_per_level_summary.php",

		success: function(data){
			console.log(data);
			
			var statistics = {
				count : [],
				category_name : [],
				level : []					
			};
			
			data_json = JSON.parse(data);
			
			data_json_inner = data_json[0];
						
			statistics.category_name = Object.keys(JSON.parse(data_json_inner.summary));
			
			var innerKeys = [];
			var len = statistics.category_name.length;
			
			
			for(var i = 0; i < len; i++){
				
				tempKeys =  Object.keys(Object(JSON.parse(data_json_inner.summary))[statistics.category_name[i]]);
				
				for (var j = 0; j< tempKeys.length; j++){
					
					if(innerKeys.includes(tempKeys[j]) == false){
						innerKeys.push(tempKeys[j]);
					}
					
				}
			}
			console.log(innerKeys);
			
			var levelValuesArray = new Array(innerKeys.length)
			
			for(var i = 0; i < innerKeys.length; i++){
				
				levelValuesArray[i] = new Array(statistics.category_name.length);
				for(var j = 0; j < statistics.category_name.length; j++){
					
					console.log("elo");
					console.log(Object(Object(JSON.parse(data_json_inner.summary))[statistics.category_name[j]])[innerKeys[i]])
					if(Object(Object(JSON.parse(data_json_inner.summary))[statistics.category_name[j]])[innerKeys[i]]){
						levelValuesArray[i][j] = Object(Object(JSON.parse(data_json_inner.summary))[statistics.category_name[j]])[innerKeys[i]];
					}else{
						levelValuesArray[i][j] = 0;
					}
					
				}
			}
			
			console.log(levelValuesArray);
			
			var ctx = document.getElementById("user_category_question_per_level_summary").getContext('2d');
			var barChart = new Chart(ctx, {
			    type: 'bar',
			    data: {
			        labels: statistics.category_name,
			        datasets: [{
						      backgroundColor: colors[0],
			            label: 'poziom '+ innerKeys[0],
			            data: levelValuesArray[0]
			            }]
					},
				options: {
			        scales: {
			            xAxes: [{
			                stacked: true
			            }],
			            yAxes: [{
			                stacked: true
			            }]
			        }
			    }
			})
			
			for(var i = 1; i < innerKeys.length; i++)
			{
					addData(barChart, 'poziom '+ innerKeys[i], '#ff0000', levelValuesArray[i]);
			}
			
			function addData(chart, label, color, data) {
					chart.data.datasets.push({
				    label: label,
			      backgroundColor: colors[i],
			      data: data
			    });
			    chart.update();
			}
			
		}
	})

}