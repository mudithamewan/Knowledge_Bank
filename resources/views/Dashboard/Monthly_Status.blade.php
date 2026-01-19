<div class="col-xl-12">
    <div class="card">
        <div class="card-body">
            <h4 class="card-title text-primary mb-4">MONTHLY SALES OVERVIEW</h4>

            <div id="column_chart2" class="apex-charts" dir="ltr"></div>
        </div>
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
            data: [<?= $sale_amounts ?>]
        }, {
            name: "COLLECTION",
            data: [<?= $collection_amount ?>]
        }, {
            name: "CREDIT",
            data: [<?= $credit_amount ?>]
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
    (chart = new ApexCharts(document.querySelector("#column_chart2"), options)).render();
</script>

<!-- App js -->
<!-- <script src="{{url('/')}}/assets/js/app.js"></script> -->