<?php
require_once("includes/db.inc.php");


if(isset($_GET['file'])) {
    // Decrypt the file id
    $complaintBlotterID = $conn->real_escape_string(kap_decrypt($_GET['file']));

    // If the encryption is invalid, do not continue
    if($complaintBlotterID === false) {
        echo "<script>window.close();</script>";
        exit();
    }
    
    // Fetch all the evidences from the table
    $evidenceFetchResult = $conn->query("SELECT * FROM $evidencesTable WHERE complaint_blotter_id='$complaintBlotterID'");

    // If the query did not return any result, do not continue
    if($evidenceFetchResult->num_rows == 0) {
        echo "<script>window.close();</script>";
        exit();
    }

    // Grab all the path of the evidences and store in an array
    $evidences = [];
    while($evidence = $evidenceFetchResult->fetch_assoc()) 
        array_push($evidences, str_replace("../", "", $evidence['path']));

    // Zip the evidences
    $filename = "assets/uploads/evidences/evidence-${complaintBlotterID}.zip";
    ZipUtil::zip($filename, $evidences);

    // Create 2 streams, readable and writable stream
    $readableStream = fopen($filename, 'rb');
    $writableStream = fopen('php://output', 'wb');

    // Send the information of the zip file to the header
    header('Content-Type: ' . mime_content_type($filename));
    header('Content-Disposition: attachment; filename="'. basename($filename) .'"');

    // Stream the file to the writable stream
    stream_copy_to_stream($readableStream, $writableStream);

    // Flush the output buffer (OB)
    ob_flush();
    flush();

    // Delete the zip file
    unlink($filename);
}

// Close the window
echo "<script>window.close();</script>";
exit();

?>