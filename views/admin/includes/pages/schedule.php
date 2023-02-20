<?php

if (isset($_POST['createSchedule'])) {
  $event = $conn->real_escape_string($_POST['event']);
  $allDay = $conn->real_escape_string($_POST['allDay']);
  $location = $conn->real_escape_string($_POST['location']);
  $start = $conn->real_escape_string($_POST['start']);
  $end = $conn->real_escape_string($_POST['end']);

  $startDatetime = date("Y-m-d H:i:s", strtotime($start, time()));
  $endDatetime = date("Y-m-d H:i:s", strtotime($end, time()));

  $addScheduleResult = $conn->query("INSERT INTO $schedulesTable(
    owner_id, 
    event, 
    fromAdmin,
    start_datetime, 
    end_datetime,
    allDay,
    location
  ) VALUES(
    '${adminInfo['id']}',
    '$event',
    '1',
    '$startDatetime',
    '$endDatetime',
    '$allDay',
    '$location'
  )");

  if ($addScheduleResult) {
    $hasSuccess = true;
    $hasError = false;
    $message = "Successfully added a new schedule!";
  } else {
    $hasSuccess = false;
    $hasError = true;
    $message = "Failed to add new schedule. <strong>Reason: '" . $conn->error . "'</strong>";
  }
}

if (isset($_POST['updateSchedule'])) {
  $id = $conn->real_escape_string($_POST['id']);
  $event = $conn->real_escape_string($_POST['event']);
  $allDay = $conn->real_escape_string($_POST['allDay']);
  $location = $conn->real_escape_string($_POST['location']);
  $start = $conn->real_escape_string($_POST['start']);
  $end = $conn->real_escape_string($_POST['end']);

  if(empty($event) && empty($allDay) && empty($location) && empty($start) && empty($end)) {}
  else {
    $columnsToUpdate = [];

    if(!empty($event))
      $columnsToUpdate[] = ['event' => $event];

    if(!empty($allDay))
      $columnsToUpdate[] = ['allDay' => $allDay];

    if(!empty($location))
      $columnsToUpdate[] = ['location' => $location];

    if(!empty($start))
      $columnsToUpdate[] = ['start_datetime' => date("Y-m-d H:i:s", strtotime($start, time()))];

    if(!empty($end))
      $columnsToUpdate[] = ['end_datetime' => date("Y-m-d H:i:s", strtotime($end, time()))];

    $updateQuery = "UPDATE $schedulesTable SET ";
    foreach($columnsToUpdate as $columns)
      foreach($columns as $column => $value)
        $updateQuery .= "$column='$value', ";

    $updateQuery = substr($updateQuery, 0, strlen($updateQuery) - 2);
    $updateQuery .= " WHERE id='$id'";
    $updateScheduleResult = $conn->query($updateQuery);

    if ($updateScheduleResult) {
      $hasSuccess = true;
      $hasError = false;
      $message = "Successfully updated the schedule!";
    } else {
      $hasSuccess = false;
      $hasError = true;
      $message = "Failed to update schedule. <strong>Reason: '" . $conn->error . "'</strong>";
    }
  }
}

if (isset($_POST['deleteSchedule'])) {
  $id = $conn->real_escape_string($_POST['id']);

  $deleteScheduleResult = $conn->query("DELETE FROM $schedulesTable WHERE id='$id'");

  if ($deleteScheduleResult) {
    $hasSuccess = true;
    $hasError = false;
    $message = "Successfully deleted schedule!";
  } else {
    $hasSuccess = false;
    $hasError = true;
    $message = "Failed to delete schedule. <strong>Reason: '" . $conn->error . "'</strong>";
  }
}

// Fetch all schedules
$schedulesResult = $conn->query("
  SELECT s.*, CONCAT(u.firstname, ' ', u.lastname) as owner_name FROM $schedulesTable s
  INNER JOIN $usersTable u
  ON s.owner_id=u.id
  ORDER BY created_at DESC
");
$schedulesArray = [];

if($schedulesResult->num_rows > 0) {
  while($row = $schedulesResult->fetch_assoc()) {
    $row['start_datetime'] = date('Y-m-d\TH:m:s', strtotime($row['start_datetime']));
    $row['end_datetime'] = date('Y-m-d\TH:m:s', strtotime($row['end_datetime']));

    array_push($schedulesArray, $row);
  }
}

$schedulesJSON = json_encode($schedulesArray);
?>

<style>
  #current-month-year {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    justify-items: center;
  }
</style>

<!-- MAIN CONTENT -->

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
      <h6 class="m-0 font-weight-bold" style="color:#223D3C;"><i class="fa fa-calendar"></i> Schedules </h6>
    </div>
  </div>

  <div class="col">
    <div class="mb-2" id="current-month-year">
      <button class="btn btn-dark" id="prev-calendar">Prev</button>
      <h4 id="display-current-month-year"></h4>
      <button class="btn btn-dark" id="next-calendar">Next</button>
    </div>
  </div>
  <div id="calendar" style="height: 800px;"></div>

</div>

<!-- END OF MAIN CONTENT -->



<!-- Toast UI Calendar Library -->
<link rel="stylesheet" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css"/>
<script src="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.js"></script>

<link rel="stylesheet" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css"/>
<script src="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.js"></script>

<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css"/>
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>
<!-- End Toast UI Calendar Library -->

<!-- UUID Library -->
<script src="https://cdn.jsdelivr.net/npm/short-uuid/dist/short-uuid.min.js"></script>
<!-- END UUID Library -->

<!-- SCRIPT FOR INITIALIZING THE CALENDAR -->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment-with-locales.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.31/moment-timezone-with-data-2012-2022.min.js"></script>
<script>
  const prevBtn = document.querySelector("#prev-calendar");
  const nextBtn = document.querySelector("#next-calendar");
  const displayCurrentMonthYear = document.querySelector("#display-current-month-year");

  const rawSchedules = JSON.parse('<?= $schedulesJSON ?>');
  const schedules = rawSchedules.map((schedule) => {
    return {
      id: schedule.id,
      calendarId: 'schedules',
      attendees: [schedule.owner_name],
      body: schedule.event,
      title: schedule.event,
      start: schedule.start_datetime,
      end: schedule.end_datetime,
      allDay: schedule.allDay == '1',
      location: schedule.location
    };
  });

  const MONTHS = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
  ];

  function formatTime(time) {
    let _hours = time.getHours();
    let _minutes = time.getMinutes();
    let am_or_pm = "AM";

    // Convert time to 12-hour format
    if(_hours > 12) {
      _hours -= 12;
      am_or_pm = "PM";
    } else if(_hours == 0) {
      _hours = 12;
      am_or_pm = 'AM';
    }

    const hours = `${_hours}`.padStart(2, '0');
    const minutes = `${_minutes}`.padStart(2, '0');

    return `${hours}:${minutes} ${am_or_pm}`;
  }

  function momentTZCalculator(timezone, timestamp) {
    // matches 'getTimezoneOffset()' of Date API
    // e.g. +09:00 => -540, -04:00 => 240
    return moment.tz.zone(timezone).utcOffset(timestamp);
  }

  const calendar = new tui.Calendar('#calendar', {
    defaultView: 'month',
    isReadOnly: false,
    usageStatistics: false,
    taskView: false,
    useFormPopup: true,
    useDetailPopup: true,
    scheduleView: ['time'],

    timezone: {
      zones: [
        {
          timezoneName: 'Asia/Manila',
          tooltip: 'Manila',
          displayLabel: 'GMT+08:00'
        }
      ],
      // customOffsetCalculator: momentTZCalculator
    },

    template: {
      time(event) {
        const {
          start,
          end,
          title
        } = event;

        return `<span style="color: black;">${title} @ ${formatTime(start)}~${formatTime(end)}</span>`;
      },
      allday(event) {
        return `<span style="color: black;">${event.title}</span>`;
      },
    },

    calendars: [
      {
        id: 'schedules',
        name: 'Schedules'
      }
    ]
  });

  calendar.createEvents(schedules);

  calendar.on('beforeCreateEvent', function(data) {

    $.ajax({
      type: 'POST',
      url: './index.php?page=schedule',
      data: {
        'createSchedule': 'createSchedule',
        'event': data.title,
        'allDay': data.isAllday ? '1' : '0',
        'location': data.location,
        'start': `${data.start.getFullYear()}-${data.start.getMonth() + 1}-${data.start.getDate()} ${data.start.getHours()}:${data.start.getMinutes()}:${data.start.getSeconds()}`,
        'end': `${data.end.getFullYear()}-${data.end.getMonth() + 1}-${data.end.getDate()} ${data.end.getHours()}:${data.end.getMinutes()}:${data.end.getSeconds()}`
      },

      success: function(response, status, xhr) {
        console.log('create event');
        console.log(status, response);
        window.location.reload();
      },

      error: function(xhr, status, error) {
        console.log('create event');
        console.log(status, error);
      }
    });

    calendar.createEvents([
      {
        ...data,
        id: new ShortUUID().uuid()
      }
    ])
  });

  calendar.on('beforeUpdateEvent', function(updatedEvent) {
    const {event, changes} = updatedEvent;

    $.ajax({
      type: 'POST',
      url: './index.php?page=schedule',
      data: {
        'updateSchedule': 'updateSchedule',
        'id': event.id,
        'event': changes.title ?? '',
        'allDay': changes.isAllday === undefined ? '' : changes.isAllday ? '1' : '0',
        'location': changes.location ?? '',
        'start': changes.start === undefined ? '' : `${changes.start.getFullYear()}-${changes.start.getMonth() + 1}-${changes.start.getDate()} ${changes.start.getHours()}:${changes.start.getMinutes()}:${changes.start.getSeconds()}`,
        'end': changes.end === undefined ? '' : `${changes.end.getFullYear()}-${changes.end.getMonth() + 1}-${changes.end.getDate()} ${changes.end.getHours()}:${changes.end.getMinutes()}:${changes.end.getSeconds()}`
      },

      success: function(response, status, xhr) {
        console.log('update event');
        console.log(status, response);
      },

      error: function(xhr, status, error) {
        console.log('update event');
        console.log(status, error);
      }
    });

    calendar.updateEvent(event.id, event.calendarId, changes);
  });
  
  calendar.on('beforeDeleteEvent', function(event) {
    $.ajax({
      type: 'POST',
      url: './index.php?page=schedule',
      data: {
        'deleteSchedule': 'deleteSchedule',
        'id': event.id
      },

      success: function(response, status, xhr) {
        console.log('delete event');
        console.log(status, response);
      },

      error: function(xhr, status, error) {
        console.log('delete event');
        console.log(status, error);
      }
    });

    calendar.deleteEvent(event.id, event.calendarId);
  });



  prevBtn.addEventListener("click", function() {
    calendar.prev();
    
    const currentDate = calendar.getDate();
    displayCurrentMonthYear.innerHTML = `${MONTHS[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
  });

  nextBtn.addEventListener("click", function() {
    calendar.next();
    
    const currentDate = calendar.getDate();
    displayCurrentMonthYear.innerHTML = `${MONTHS[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
  });

  let currentDate = calendar.getDate();
  displayCurrentMonthYear.innerHTML = `${MONTHS[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
</script>
<!-- END SCRIPT FOR INITIALIZING THE CALENDAR -->