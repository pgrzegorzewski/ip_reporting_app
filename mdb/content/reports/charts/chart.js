function loadItemChart(labels, data) {
var ctx = document.getElementById("canvas_chart").getContext('2d');

var datasets = new Array();

for (index = 0; index <= data.length; index++) {
  datasets.push({label:labels[index], backgroundColor: 'rgba(75, 192, 192, 0.4)', data:[data[index]]});
  if(index > 10) {
    break;
  }
}


var data = {datasets: datasets};

   var itemChart = new Chart(ctx, {
     type: 'bar',
     data: data,
     options: {
       barValueSpacing: 5,
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
