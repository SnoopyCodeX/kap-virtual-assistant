<?php  
$feedbacksResult = $conn->query("SELECT f.*, u.fullname as name 
  FROM $feedbacksTable f
  INNER JOIN $usersTable u
  ON f.user_id=u.id
  ORDER BY created_at DESC
");
?>

<!-- Main Content -->
<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-comment"></i> Feedbacks</h6>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered" id="feedbacks" width="100%" cellspacing="0" data-ordering="false">
          <thead>
            <tr>
              <th>Fullname</th>
              <th>Message</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $feedbacksResult->fetch_assoc()) { ?>
              <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['content'] ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<!-- End Main Content -->