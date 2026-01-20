 <ul class="nav nav-tabs" role="tablist">
     <li class="nav-item">
         <a class="nav-link active" data-bs-toggle="tab" href="#daily" role="tab">
             <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
             <span class="d-none d-sm-block">DAILY</span>
         </a>
     </li>
     <li class="nav-item">
         <a class="nav-link" data-bs-toggle="tab" href="#month" role="tab">
             <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
             <span class="d-none d-sm-block">MONTH</span>
         </a>
     </li>
 </ul>
 <div class="tab-content p-0 text-muted">
     <div class="tab-pane active" id="daily" role="tabpanel">

         <div id="column_chart3" class="apex-charts" dir="ltr"></div>

     </div>
     <div class="tab-pane" id="month" role="tabpanel">

         <div id="column_chart4" class="apex-charts" dir="ltr"></div>

     </div>
 </div>



 <script src="{{url('/')}}/assets/libs/apexcharts/apexcharts.min.js"></script>

 <script>
     options = {
         chart: {
             height: 350,
             type: "bar",
             toolbar: {
                 show: !1
             }
         },
         plotOptions: {
             bar: {
                 horizontal: !1,
                 columnWidth: "45%",
                 endingShape: "rounded"
             }
         },
         dataLabels: {
             enabled: !1
         },
         stroke: {
             show: !0,
             width: 2,
             colors: ["transparent"]
         },
         series: [{
             name: "SALE",
             data: [<?= $day_sale_amounts ?>]
         }, {
             name: "COLLECTION",
             data: [<?= $day_collection_amount ?>]
         }, {
             name: "CREDIT",
             data: [<?= $day_credit_amount ?>]
         }],
         colors: ["#556ee6", "#34c38f", "#f46a6a"],
         xaxis: {
             categories: [<?= $dates ?>]
         },
         yaxis: {
             title: {
                 text: "Amount",
                 style: {
                     fontWeight: "500"
                 }
             }
         },
         grid: {
             borderColor: "#f1f1f1"
         },
         fill: {
             opacity: 1
         },
         tooltip: {
             y: {
                 formatter: function(e) {
                     return e
                 }
             }
         }
     };
     (chart = new ApexCharts(document.querySelector("#column_chart3"), options)).render();

     options = {
         chart: {
             height: 350,
             type: "bar",
             toolbar: {
                 show: !1
             }
         },
         plotOptions: {
             bar: {
                 horizontal: !1,
                 columnWidth: "45%",
                 endingShape: "rounded"
             }
         },
         dataLabels: {
             enabled: !1
         },
         stroke: {
             show: !0,
             width: 2,
             colors: ["transparent"]
         },
         series: [{
             name: "SALE",
             data: [<?= $month_sale_amounts ?>]
         }, {
             name: "COLLECTION",
             data: [<?= $month_collection_amount ?>]
         }, {
             name: "CREDIT",
             data: [<?= $month_credit_amount ?>]
         }],
         colors: ["#556ee6", "#34c38f", "#f46a6a"],
         xaxis: {
             categories: [<?= $months ?>]
         },
         yaxis: {
             title: {
                 text: "Amount",
                 style: {
                     fontWeight: "500"
                 }
             }
         },
         grid: {
             borderColor: "#f1f1f1"
         },
         fill: {
             opacity: 1
         },
         tooltip: {
             y: {
                 formatter: function(e) {
                     return e
                 }
             }
         }
     };
     (chart = new ApexCharts(document.querySelector("#column_chart4"), options)).render();
 </script>