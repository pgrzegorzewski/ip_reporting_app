function loadRegionChart(chart_data) {

  var canvas1 = document.getElementById("canvas_chart_1").getContext('2d');
  var canvas2 = document.getElementById("canvas_chart_2").getContext('2d');
  var canvas3 = document.getElementById("canvas_chart_3").getContext('2d');

  var chartColors = new Array();
  var labels = new Array();
  var data = new Array();
  chart_data_suma_wartosci = _.orderBy(chart_data, ['suma_wartosci'], 'desc');

  for (index = 0; index < chart_data_suma_wartosci.length; index++) {
    labels.push(chart_data_suma_wartosci[index].label)
    data.push(chart_data_suma_wartosci[index].suma_wartosci);
    chartColors.push(chart_data_suma_wartosci[index].kolor);
  }


  var regionValueTotalChart = new Chart(canvas1, {
   type: 'doughnut',
   data:{
           labels:  labels,
           datasets: [{
               data: data,
               backgroundColor: chartColors
           }]
         },
   options: {
     responsive:true,
     maintainAspectRatio: false,
     title: {
       display: true,
       text: 'Podsumowanie per wartość całkowita'
     },
   }
  });

  datasets = [];
  chart_data_procent = _.orderBy(chart_data, ['procent'], 'desc');

  for (index = 0; index < chart_data_procent.length; index++) {
    datasets.push({label:chart_data_procent[index].label, backgroundColor: chart_data_procent[index].kolor, data:[chart_data_procent[index].procent]});
  }

  data = {datasets: datasets};

  var regionPercentChart = new Chart(canvas2, {
   type: 'bar',
   data: data,
   options: {
     title: {
       display: true,
       text: 'Podsumowanie per procent'
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

  data = [];
  chart_data_marza = _.orderBy(chart_data, ['suma_marz'], 'desc');

  for (index = 0; index < chart_data_marza.length; index++) {
    data.push(chart_data_marza[index].suma_marz);
  }

  var regionProfitMarginChart = new Chart(canvas3, {
   type: 'doughnut',
   data:{
           labels:  labels,
           datasets: [{
               data: data,
               backgroundColor: chartColors
           }]
         },
   options: {
     title: {
       display: true,
       text: 'Podsumowanie per marża'
     },
   }
  });

}
