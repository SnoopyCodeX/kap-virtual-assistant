<?php  
$transactionsResult = $conn->query("SELECT * FROM $transactionsTable WHERE user_id='${userInfo['id']}' ORDER BY created_at DESC");
?>

<div class="container-fluid">

  <?php if ($hasError) { ?>
    <div class="col mb-2">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="text-danger" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <?php if ($hasSuccess) { ?>
    <div class="col mb-2">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="text-success" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-receipt"></i> Transaction History</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="transactions" width="100%" cellspacing="0">
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

</div>