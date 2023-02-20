<?php  
$reportsResult = $conn->query("SELECT * FROM $reportsTable ORDER BY created_at DESC");
?>


<div class="container-fluid">

  <?php if($hasError) { ?>
    <div class="col mb-2">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="text-danger" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <?php if($hasSuccess) { ?>
    <div class="col mb-2">
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <span class="text-success" id="message"><?= $message ?></span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
  <?php } ?>

  <?php if($reportsResult->num_rows == 0) { ?>
    <div class="col mb-2">
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="text-danger" id="message">There are currently no reports to fetch as of <strong><?= date("F d, Y \\@ h:i A", time()) ?></strong>.</span>
      </div>
    </div>
  <?php } ?>

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-bullhorn"></i> Reports</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="reports" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>ID</th>
              <th>Report Content</th>
              <th>Date Reported</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $reportsResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['content'] ?></td>
                <td><?= $row['datetime'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>