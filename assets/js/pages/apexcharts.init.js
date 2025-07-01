function getChartColorsArray(e) {
  if (null !== document.getElementById(e)) {
    var t = document.getElementById(e).getAttribute('data-colors');
    if (t)
      return (t = JSON.parse(t)).map(function (e) {
        var t = e.replace(' ', '');
        if (-1 === t.indexOf(',')) {
          var r = getComputedStyle(document.documentElement).getPropertyValue(
            t
          );
          return r || t;
        }
        var o = e.split(',');
        return 2 != o.length
          ? t
          : 'rgba(' +
              getComputedStyle(document.documentElement).getPropertyValue(
                o[0]
              ) +
              ',' +
              o[1] +
              ')';
      });
    console.warn('data-colors Attribute not found on:', e);
  }
}

// Chart for "Fuel"
var columnChartColors1 = getChartColorsArray('column_chart1');
if (columnChartColors1) {
  var options1 = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: {
      bar: { horizontal: !1, columnWidth: '45%', endingShape: 'rounded' },
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ['transparent'] },
    series: [
      { name: 'Series 1', data: [46, 57, 59, 54] },
      { name: 'Series 2', data: [74, 83, 102, 97] },
      { name: 'Series 3', data: [37, 42, 38, 26] },
    ],
    colors: columnChartColors1,
    xaxis: {
      categories: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
    },
    yaxis: { title: { text: '$ (thousands)', style: { fontWeight: '500' } } },
    grid: { borderColor: '#f1f1f1' },
    fill: { opacity: 1 },
    tooltip: {
      y: {
        formatter: function (e) {
          return '$ ' + e + ' thousands';
        },
      },
    },
  };

  var chart1 = new ApexCharts(
    document.querySelector('#column_chart1'),
    options1
  );
  chart1.render();
}

// Chart for "Mileage"
var columnChartColors2 = getChartColorsArray('column_chart2');
if (columnChartColors2) {
  var options2 = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: {
      bar: { horizontal: !1, columnWidth: '45%', endingShape: 'rounded' },
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ['transparent'] },
    series: [
      { name: 'Series 1', data: [46, 57, 59, 54] },
      { name: 'Series 2', data: [74, 83, 102, 97] },
      { name: 'Series 3', data: [37, 42, 38, 26] },
    ],
    colors: columnChartColors2,
    xaxis: {
      categories: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
    },
    yaxis: { title: { text: '$ (thousands)', style: { fontWeight: '500' } } },
    grid: { borderColor: '#f1f1f1' },
    fill: { opacity: 1 },
    tooltip: {
      y: {
        formatter: function (e) {
          return '$ ' + e + ' thousands';
        },
      },
    },
  };

  var chart2 = new ApexCharts(
    document.querySelector('#column_chart2'),
    options2
  );
  chart2.render();
}

// Chart for "Expense"
var columnChartColors3 = getChartColorsArray('column_chart3');
if (columnChartColors3) {
  var options3 = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: {
      bar: { horizontal: !1, columnWidth: '45%', endingShape: 'rounded' },
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ['transparent'] },
    series: [
      { name: 'Series 1', data: [46, 57, 59, 54] },
      { name: 'Series 2', data: [74, 83, 102, 97] },
      { name: 'Series 3', data: [37, 42, 38, 26] },
    ],
    colors: columnChartColors3,
    xaxis: {
      categories: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
    },
    yaxis: { title: { text: '$ (thousands)', style: { fontWeight: '500' } } },
    grid: { borderColor: '#f1f1f1' },
    fill: { opacity: 1 },
    tooltip: {
      y: {
        formatter: function (e) {
          return '$ ' + e + ' thousands';
        },
      },
    },
  };

  var chart3 = new ApexCharts(
    document.querySelector('#column_chart3'),
    options3
  );
  chart3.render();
}

// Chart for "Driving License"
var columnChartColors4 = getChartColorsArray('column_chart4');
if (columnChartColors4) {
  var options4 = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: {
      bar: { horizontal: !1, columnWidth: '45%', endingShape: 'rounded' },
    },
    dataLabels: { enabled: !1 },
    stroke: { show: !0, width: 2, colors: ['transparent'] },
    series: [
      { name: 'Series 1', data: [46, 57, 59, 54] },
      { name: 'Series 2', data: [74, 83, 102, 97] },
      { name: 'Series 3', data: [37, 42, 38, 26] },
    ],
    colors: columnChartColors4,
    xaxis: {
      categories: ['Category 1', 'Category 2', 'Category 3', 'Category 4'],
    },
    yaxis: { title: { text: '$ (thousands)', style: { fontWeight: '500' } } },
    grid: { borderColor: '#f1f1f1' },
    fill: { opacity: 1 },
    tooltip: {
      y: {
        formatter: function (e) {
          return '$ ' + e + ' thousands';
        },
      },
    },
  };

  var chart4 = new ApexCharts(
    document.querySelector('#column_chart4'),
    options4
  );
  chart4.render();
}

var columnChartDatalabelColors = getChartColorsArray('column_chart_datalabel');
columnChartDatalabelColors &&
  ((options = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: { bar: { dataLabels: { position: 'top' } } },
    dataLabels: {
      enabled: !0,
      formatter: function (e) {
        return e + '%';
      },
      offsetY: -22,
      style: { fontSize: '12px', colors: ['#304758'] },
    },
    series: [
      {
        name: 'Inflation',
        data: [2.5, 3.2, 5, 10.1, 4.2, 3.8, 3, 2.4, 4, 1.2, 3.5, 0.8],
      },
    ],
    colors: columnChartDatalabelColors,
    grid: { borderColor: '#f1f1f1' },
    xaxis: {
      categories: [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
        'May',
        'Jun',
        'Jul',
        'Aug',
        'Sep',
        'Oct',
        'Nov',
        'Dec',
      ],
      position: 'top',
      labels: { offsetY: -18 },
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      crosshairs: {
        fill: {
          type: 'gradient',
          gradient: {
            colorFrom: '#D8E3F0',
            colorTo: '#BED1E6',
            stops: [0, 100],
            opacityFrom: 0.4,
            opacityTo: 0.5,
          },
        },
      },
      tooltip: { enabled: !0, offsetY: -35 },
    },
    fill: {
      gradient: {
        shade: 'light',
        type: 'horizontal',
        shadeIntensity: 0.25,
        gradientToColors: void 0,
        inverseColors: !0,
        opacityFrom: 1,
        opacityTo: 1,
        stops: [50, 0, 100, 100],
      },
    },
    yaxis: {
      axisBorder: { show: !1 },
      axisTicks: { show: !1 },
      labels: {
        show: !1,
        formatter: function (e) {
          return e + '%';
        },
      },
    },
    title: {
      text: 'Monthly Inflation in Argentina, 2002',
      floating: !0,
      offsetY: 330,
      align: 'center',
      style: { color: '#444', fontWeight: '500' },
    },
  }),
  (chart = new ApexCharts(
    document.querySelector('#column_chart_datalabel'),
    options
  )).render());
var barChartColors = getChartColorsArray('bar_chart');
barChartColors &&
  ((options = {
    chart: { height: 350, type: 'bar', toolbar: { show: !1 } },
    plotOptions: { bar: { horizontal: !0 } },
    dataLabels: { enabled: !1 },
    series: [{ data: [380, 430, 450, 475, 550, 584, 780, 1100, 1220, 1365] }],
    colors: barChartColors,
    grid: { borderColor: '#f1f1f1' },
    xaxis: {
      categories: [
        'South Korea',
        'Canada',
        'United Kingdom',
        'Netherlands',
        'Italy',
        'France',
        'Japan',
        'United States',
        'China',
        'Germany',
      ],
    },
  }),
  (chart = new ApexCharts(
    document.querySelector('#bar_chart'),
    options
  )).render());
