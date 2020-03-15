function loadItemChart(chart_data) {
  var ctx = document.getElementById("canvas_chart").getContext('2d');

  var datasets = new Array();

  chart_data = _.orderBy(chart_data, ['label'], ['asc']);
  console.log(chart_data);

  for (index = 0; index <= chart_data.length; index++) {
    datasets.push({label:chart_data[index].label, backgroundColor: 'rgba(75, 192, 192, 0.4)', data:[chart_data[index].data]});
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
