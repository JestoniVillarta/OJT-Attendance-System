<?php
include '../CONNECTION/connection.php';

// Fetch existing attendance settings
$query = "SELECT * FROM attendance_settings_tbl WHERE id = 1";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $start_time = date("H:i", strtotime($row['MORNING_TIME_IN']));
    $start_time_end = date("H:i", strtotime($row['TIME_IN_END']));
    $morning_time_out = date("H:i", strtotime($row['MORNING_TIME_OUT']));
    $morning_time_out_end = date("H:i", strtotime($row['TIME_OUT_END']));
    $afternoon_time_in = date("H:i", strtotime($row['AFTERNOON_TIME_IN']));
    $afternoon_time_in_end = date("H:i", strtotime($row['AFTERNOON_TIME_IN_END']));
    $afternoon_time_out = date("H:i", strtotime($row['AFTERNOON_TIME_OUT']));
    $afternoon_time_out_end = date("H:i", strtotime($row['AFTERNOON_TIME_OUT_END']));
} else {
    // Set default empty values if no records exist
    $start_time = $start_time_end = $morning_time_out = $morning_time_out_end = "";
    $afternoon_time_in = $afternoon_time_in_end = $afternoon_time_out = $afternoon_time_out_end = "";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_time = $_POST['start_time'];
    $start_time_end = $_POST['start_time_end'];
    $morning_time_out = $_POST['morning_time_out'];
    $morning_time_out_end = $_POST['morning_time_out_end'];
    $afternoon_time_in = $_POST['afternoon_time_in'];
    $afternoon_time_in_end = $_POST['afternoon_time_in_end'];
    $afternoon_time_out = $_POST['afternoon_time_out'];
    $afternoon_time_out_end = $_POST['afternoon_time_out_end'];

    // Validate the input fields
    if (empty($start_time) || empty($start_time_end) || empty($morning_time_out) || empty($morning_time_out_end) || empty($afternoon_time_in) || empty($afternoon_time_in_end) || empty($afternoon_time_out) || empty($afternoon_time_out_end)) {
        echo "All fields are required.";
        exit();
    }

    // Convert time to 12-hour format
    $start_time_12hr = date("h:i A", strtotime($start_time));
    $start_time_end_12hr = date("h:i A", strtotime($start_time_end));
    $morning_time_out_12hr = date("h:i A", strtotime($morning_time_out));
    $morning_time_out_end_12hr = date("h:i A", strtotime($morning_time_out_end));
    $afternoon_time_in_12hr = date("h:i A", strtotime($afternoon_time_in));
    $afternoon_time_in_end_12hr = date("h:i A", strtotime($afternoon_time_in_end));
    $afternoon_time_out_12hr = date("h:i A", strtotime($afternoon_time_out));
    $afternoon_time_out_end_12hr = date("h:i A", strtotime($afternoon_time_out_end));

    // Check the time sequence
    if (strtotime($start_time) >= strtotime($start_time_end) || strtotime($morning_time_out) >= strtotime($morning_time_out_end) || strtotime($afternoon_time_in) >= strtotime($afternoon_time_in_end) || strtotime($afternoon_time_out) >= strtotime($afternoon_time_out_end)) {

        exit();
    }

    // Update the attendance settings
    if ($result->num_rows > 0) {
        $query = "UPDATE attendance_settings_tbl SET MORNING_TIME_IN = ?, TIME_IN_END = ?, MORNING_TIME_OUT = ?, TIME_OUT_END = ?, AFTERNOON_TIME_IN = ?, AFTERNOON_TIME_IN_END = ?, AFTERNOON_TIME_OUT = ?, AFTERNOON_TIME_OUT_END = ? WHERE id = 1";
    } else {
        $query = "INSERT INTO attendance_settings_tbl (MORNING_TIME_IN, TIME_IN_END, MORNING_TIME_OUT, TIME_OUT_END, AFTERNOON_TIME_IN, AFTERNOON_TIME_IN_END, AFTERNOON_TIME_OUT, AFTERNOON_TIME_OUT_END) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssssss', $start_time_12hr, $start_time_end_12hr, $morning_time_out_12hr, $morning_time_out_end_12hr, $afternoon_time_in_12hr, $afternoon_time_in_end_12hr, $afternoon_time_out_12hr, $afternoon_time_out_end_12hr);


    $stmt->execute();
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Set Attendance Time</title>
    <link rel="stylesheet" href="CSS/set_time.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>


<body>

    <div class="container">


        <div class="nav">
            <?php include 'sidenav.php'; ?>
        </div>


        <div class="content-container">

            <div class="header-container">
                <h3>Set Attendance Time</h3>
            </div>

            <div class="form_wrapper">
                <form action="" method="post" onsubmit="showModal(event)">

                    <div class="time-container">

                        <div class="time-group">
                            <label for="start_time">Morning Time In:</label>
                            <input type="time" id="start_time" name="start_time" value="<?php echo $start_time; ?>" required>
                            <label for="start_time_end" class="end-label">to</label>
                            <input type="time" id="start_time_end" name="start_time_end" value="<?php echo $start_time_end; ?>" required>
                        </div>

                        <div class="time-group">
                            <label for="morning_time_out">Morning Time Out:</label>
                            <input type="time" id="morning_time_out" name="morning_time_out" value="<?php echo $morning_time_out; ?>" required>
                            <label for="morning_time_out_end" class="end-label">to</label>
                            <input type="time" id="morning_time_out_end" name="morning_time_out_end" value="<?php echo $morning_time_out_end; ?>" required>
                        </div>

                        <br>
                        <br>
                        <br>

                        <div class="time-group">
                            <label for="afternoon_time_in">Afternoon Time In:</label>
                            <input type="time" id="afternoon_time_in" name="afternoon_time_in" value="<?php echo $afternoon_time_in; ?>" required>
                            <label for="afternoon_time_in_end" class="end-label">to</label>
                            <input type="time" id="afternoon_time_in_end" name="afternoon_time_in_end" value="<?php echo $afternoon_time_in_end; ?>" required>
                        </div>

                        <div class="time-group">
                            <label for="afternoon_time_out">Afternoon Time Out:</label>
                            <input type="time" id="afternoon_time_out" name="afternoon_time_out" value="<?php echo $afternoon_time_out; ?>" required>
                            <label for="afternoon_time_out_end" class="end-label">to</label>
                            <input type="time" id="afternoon_time_out_end" name="afternoon_time_out_end" value="<?php echo $afternoon_time_out_end; ?>" required>
                        </div>

                        <button type="submit" class="set_time">Set Time</button>

                    </div>




                    <script>
                        $(document).ready(function() {
                            $("form").submit(function(event) {
                                event.preventDefault();

                                // Get all the time inputs
                                const startTime = document.getElementById('start_time').value;
                                const startTimeEnd = document.getElementById('start_time_end').value;
                                const morningTimeOut = document.getElementById('morning_time_out').value;
                                const morningTimeOutEnd = document.getElementById('morning_time_out_end').value;
                                const afternoonTimeIn = document.getElementById('afternoon_time_in').value;
                                const afternoonTimeInEnd = document.getElementById('afternoon_time_in_end').value;
                                const afternoonTimeOut = document.getElementById('afternoon_time_out').value;
                                const afternoonTimeOutEnd = document.getElementById('afternoon_time_out_end').value;

                                // Validate time sequence
                                if (startTime >= startTimeEnd ||
                                    morningTimeOut >= morningTimeOutEnd ||
                                    afternoonTimeIn >= afternoonTimeInEnd ||
                                    afternoonTimeOut >= afternoonTimeOutEnd) {
                                    swal({
                                        title: "Invalid Time Settings",
                                        text: "Please ensure that end times are later than start times for each period.",
                                        icon: "error",
                                        button: "OK",
                                    });
                                    return;
                                }

                                // If validation passes, submit the form using AJAX
                                const formData = new FormData(this);

                                fetch(this.action, {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => {
                                        if (response.ok) {
                                            swal({
                                                title: "Success!",
                                                text: "Time settings have been successfully updated.",
                                                icon: "success",
                                                button: {
                                                    text: "OK",
                                                    className: "custom-success-btn" // Assign a unique class
                                                }

                                            }).then(() => {
                                                window.location.href = 'set_time.php';
                                            });
                                        } else {
                                            throw new Error('Network response was not ok');
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                    });
                            });
                        });
                    </script>





</body>

</html>