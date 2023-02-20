<?php

// Fetch all schedules
$schedulesResult = $conn->query("
  SELECT s.*, CONCAT(u.firstname, ' ', u.lastname) as owner_name FROM $schedulesTable s
  INNER JOIN $usersTable u
  ON s.owner_id=u.id WHERE
  s.owner_id='${userInfo['id']}' OR 
  s.fromAdmin='1'
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
    isReadOnly: true,
    usageStatistics: false,
    taskView: false,
    useFormPopup: false,
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

  calendar.on('clickEvent', (event, mouse) => {
    setTimeout(() => document.querySelector("div.toastui-calendar-popup-section.toastui-calendar-section-button").style.display='none', 50);
  });
</script>
<!-- END SCRIPT FOR INITIALIZING THE CALENDAR -->