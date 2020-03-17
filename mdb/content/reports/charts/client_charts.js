function loadClientChart(chart_data) {

  var colors = ['#ecf9ee', '#c6eccc', '#9fdfaa', '#79d288', '#53c666', '#39ac4d', '#2d863c', '#20602b', '#13391a', '#061309'];
  var canvas1 = document.getElementById("canvas_chart_1").getContext('2d');
  var canvas2 = document.getElementById("canvas_chart_2").getContext('2d');
  var canvas3 = document.getElementById("canvas_chart_3").getContext('2d');
  var datasets = new Array();

  chart_data_suma_wartosci = _.orderBy(chart_data, ['suma_wartosci'], 'desc');

  for (index = 0; index < chart_data_suma_wartosci.length; index++) {
    datasets.push({label:chart_data_suma_wartosci[index].label, backgroundColor: colors[index], data:[chart_data_suma_wartosci[index].suma_wartosci]});
    if(index == 9) {
      break;
    }
  }
  var data = {datasets: datasets};

  var clientValueTotalChart = new Chart(canvas1, {
   type: 'bar',
   data: data,
   options: {
     responsive:true,
     maintainAspectRatio: false,
     title: {
       display: true,
       text: 'Top 10 kontrahentów (wartość)'
     },
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

  datasets = [];
  chart_data_procent = _.orderBy(chart_data, ['procent'], 'desc');

  for (index = 0; index < chart_data_procent.length; index++) {
    datasets.push({label:chart_data_procent[index].label, backgroundColor: colors[index], data:[chart_data_procent[index].procent]});
    if(index == 9) {
      break;
    }
  }

  data = {datasets: datasets};

  var clientPercentChart = new Chart(canvas2, {
   type: 'bar',
   data: data,
   options: {
     title: {
       display: true,
       text: 'Top 10 kontahentów (procent)'
     },
     barValueSpacing: 5,
     scales: {
       yAxes: [{
         ticks: {
           min: -10,
         }
       }]
     }
   }
  });

  datasets = [];
  chart_data_marza = _.orderBy(chart_data, ['suma_marz'], 'desc');

  for (index = 0; index < chart_data_marza.length; index++) {
    datasets.push({label:chart_data_marza[index].label, backgroundColor: colors[index], data:[chart_data_marza[index].suma_marz]});
    if(index == 9) {
      break;
    }
  }
  data = {datasets: datasets};

  var clientProfitMarginChart = new Chart(canvas3, {
   type: 'bar',
   data: data,
   options: {
     title: {
       display: true,
       text: 'Top 10 produktów (marża)'
     },
     barValueSpacing: 5,
     scales: {
       yAxes: [{
         ticks: {
           min: -10,
         }
       }]
     }
   }
  });

}
