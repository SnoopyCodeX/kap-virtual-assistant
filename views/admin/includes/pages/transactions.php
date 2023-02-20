<?php  
$transactionsResult = $conn->query("SELECT * FROM $transactionsTable ORDER BY created_at DESC");

$transactionCountSummaryResult = $conn->query("SELECT DATE_FORMAT(t.created_at, '%M-%Y') AS month, 
  SUM(CASE WHEN created_at=t.created_at THEN 1 ELSE 0 END) AS transaction_count 
  FROM $transactionsTable t
  GROUP BY DATE_FORMAT(created_at, '%M-%Y')
");

$transactionTypeSummaryResult = $conn->query("SELECT t.transaction_type,
  SUM(CASE WHEN transaction_type=t.transaction_type THEN 1 ELSE 0 END) AS transaction_count
  FROM $transactionsTable t 
  GROUP BY transaction_type
");

$detailedTransactionSummaryResult = $conn->query("SELECT t.transaction_type AS type_of_service, 
  DATE_FORMAT(t.created_at, '%M %d, %Y') AS date_of_transaction, 
  COUNT(CASE WHEN DAY(t.created_at) = DAY(t.created_at) AND MONTH(t.created_at) AND YEAR(t.created_at)=YEAR(t.created_at) AND t.user_id = u.id THEN 1 END) AS day_total,
  COUNT(CASE WHEN MONTH(t.created_at) = MONTH(t.created_at) AND YEAR(t.created_at)=YEAR(t.created_at) AND t.user_id = u.id THEN 1 END) AS month_total, 
  u.fullname AS name_of_resident 
  FROM transactions t
  INNER JOIN users u
  ON t.user_id=u.id
  GROUP BY u.fullname, t.transaction_type, YEAR(t.created_at), MONTH(t.created_at), DAY(t.created_at)
  ORDER BY u.fullname, t.transaction_type, YEAR(t.created_at), MONTH(t.created_at), DAY(t.created_at) DESC
");

$transactionCountSummaries = [];
$transactionTypeSummaries = [];

if($transactionCountSummaryResult->num_rows > 0) {
  while($row = $transactionCountSummaryResult->fetch_assoc())
    array_push($transactionCountSummaries, $row);
}

if($transactionTypeSummaryResult->num_rows > 0) {
  while($row = $transactionTypeSummaryResult->fetch_assoc())
    array_push($transactionTypeSummaries, $row);
}

$jsonTransactionCountSummary = json_encode($transactionCountSummaries);
$jsonTransactionTypeSummary = json_encode($transactionTypeSummaries);
?>

<!-- Chart.JS Scripts -->
<script src="../../assets/chartjs/chart.helper.js"></script>
<script src="../../assets/chartjs/chart.js"></script>
<script src="../../assets/chartjs/chartjs-plugin-datalabels.min.js"></script>
<script src="../../assets/chartjs/chartjs-plugin-nodatamessage.js"></script>

<div class="container-fluid">

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-clipboard"></i> Transaction Summary</h6>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-sm">
          <canvas class="w-auto" height="300" id="pie-chart"></canvas>
        </div>
        <div class="col-md">
          <canvas class="w-auto" height="300" id="line-chart"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-receipt"></i> Transaction History</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="transactions" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Transaction No.</th>
              <th>Transaction Type</th>
              <th>Date Created</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php while($transaction = $transactionsResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $transaction['transaction_number'] ?></td>
                <td><?= $transaction['transaction_type'] ?></td>
                <td><?= $transaction['date_created'] ?></td>
                <td class="<?= $transaction['status'] == 'pending' || $transaction['status'] == 'declined' ? 'table-danger text-danger' : 'table-success text-success' ?>"><?= ucfirst($transaction['status']) ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-receipt"></i> Users Transaction History</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="detailed_transactions" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Name of Resident</th>
              <th>Type of Service</th>
              <th>Date of Transaction</th>
              <th>Day Total</th>
              <th>Month Total</th>
            </tr>
          </thead>
          <tbody>
            <?php while($detailedSummary = $detailedTransactionSummaryResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $detailedSummary['name_of_resident'] ?></td>
                <td><?= $detailedSummary['type_of_service'] ?></td>
                <td><?= $detailedSummary['date_of_transaction'] ?></td>
                <td><?= $detailedSummary['day_total'] ?></td>
                <td><?= $detailedSummary['month_total'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<!-- Code for rendering the doughnut chart and line chart -->
<script>
  const pie = document.querySelector("#pie-chart").getContext('2d');
  const line = document.querySelector("#line-chart").getContext('2d');

  const jsonPieData = JSON.parse('<?= $jsonTransactionTypeSummary ?>');
  const pieLabels = jsonPieData.map(row => row.transaction_type);
  const pieDataCount = jsonPieData.map(row => parseInt(row.transaction_count));
  const pieRGBColors = pieDataCount.map((data, index) => hexToRGB(colorLuminance(generateRandomColor(), (index % 2 == 0 ? -0.2 : -0.5))));
  const pieRGBAColors = pieRGBColors.map(rgb => transparentizeRGBColor(rgb));

  let lineDelayed;
  const lineLabels = months({count: 12});
  const jsonLineData = JSON.parse('<?= $jsonTransactionCountSummary ?>');

  let lineYearsData = [(new Date().getFullYear())];
  for(let row of jsonLineData) {
    const [_, year] = row.month.split("-");

    if(lineYearsData.indexOf(parseInt(year)) == -1)
      lineYearsData.push(parseInt(year));
  }

  const monthYearMap = jsonLineData.reduce((acc, row) => {
    const [month, year] = row.month.split("-");

    if (!acc[year]) {
      acc[year] = {};
    }

    acc[year][month] = parseInt(row.transaction_count);
    return acc;
  }, {});

  const lineDataCount = lineYearsData.map((year, index) => {
    let color = hexToRGB(colorLuminance(generateRandomColor(), (index % 2 == 0 ? -0.2 : -0.5)));

    return {
      label: `Year ${year}`,
      data: lineLabels.map(month => monthYearMap[year] && monthYearMap[year][month] ? monthYearMap[year][month] : 0),
      backgroundColor: color,
      borderColor: color
    };
  });

  const pieData = {
    labels: pieLabels,
    datasets: [
      {
        label: 'Dataset 1',
        data: pieDataCount,
        backgroundColor: pieRGBAColors,
        borderColor: pieRGBColors
      }
    ]
  };

  const lineData = {
    labels: lineLabels,
    datasets: [...lineDataCount]
  };

  const pieConfig = {
    type: 'doughnut',
    data: pieData,
    plugins: [ChartDataLabels, NoDataMessage],
    
    options: {
      maintainAspectRatio: false,
      responsive: true,
      tooltips: {
        enabled: false
      },
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: "Most Requested Transactions"
        },
        datalabels: {
          formatter: (value, ctx) => {
            let sum = 0;
            
            ctx.chart.data.datasets[0].data.map(data => {
              sum += data
            });
            
            let percentage = ((value * 100) / sum).toFixed(2) + "%";
            return percentage;
          },
          color: '#fff'
        }
      }
    }
  };

  const lineConfig = {
    type: 'line',
    data: lineData,
    plugins: [NoDataMessage],

    options: {
      maintainAspectRatio: false,
      responsive: true,

      scales: {
        y: {
          display: true,
          ticks: {
            beginAtZero: true,
            stepSize: 1,
          },
          min: 0
        },
      },
      
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: true,
          text: "Monthly Total Transactions"
        }
      },

      animation: {
        onComplete: () => {
          lineDelayed = true;
        },

        delay: (context) => {
          let delay = 0;

          if(context.type === 'data' && context.mode === 'default' && !lineDelayed)
            delay = context.dataIndex * 300 + context.datasetIndex * 100;

          return delay;
        }
      }
    }
  };

  const pieChart = new Chart(pie, pieConfig);
  const lineChart = new Chart(line, lineConfig);
</script>