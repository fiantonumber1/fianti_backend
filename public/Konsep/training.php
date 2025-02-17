<?php
function submit_click($list_push) {
    // Save data to a JSON file
    $json_filename = 'backupsementara.json';
    file_put_contents($json_filename, json_encode($list_push));

    // Import data from the JSON file
    $json_content = file_get_contents($json_filename);
    $list_imported = json_decode($json_content, true);

    // Print imported data for testing
    print_r($list_imported);

    // Data to be sent to PHP endpoint
    $data_to_php = array(
        "namatable" => "shopeechat",
        "namapatokan" => "pertanyaan",
        "data" => $list_imported
    );

    // Send data to PHP endpoint
    $php_endpoint = "https://api.materialkokoh.com/apishopee/posttablecolumn.php";
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json",
            'method' => 'POST',
            'content' => json_encode($data_to_php)
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($php_endpoint, false, $context);

    // Return the response
    return $response;
}

$url = "https://api.materialkokoh.com/apishopee/postgetshopee.php";
$table_name = "shopeechat";  // Ganti dengan nama tabel yang ingin Anda akses

// Function to send POST request
function requests_post($url, $data) {
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/x-www-form-urlencoded",
            'method' => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

// Retrieve data from the API
$payload = array('table' => $table_name);
$responseku = requests_post($url, $payload);
$hasil = json_decode($responseku, true);

// Create a list of items with 'None' for the 'balasan' field
$list_push = array();
foreach ($hasil as $item) {
    if ($item['balasan'] === null) {
        $list_push[] = array('pertanyaan' => $item['pertanyaan'], 'balasan' => null);
    }
}

// Limit the loop to 10 input responses
// Loop through the list_push array
foreach ($list_push as &$datasatuan) {
    // Access the 'pertanyaan' from the current $datasatuan
    $pertanyaan = $datasatuan['pertanyaan'];

    // Check if there is user input for the current 'pertanyaan'
    if (isset($_POST['balasan'][$pertanyaan])) {
        // Assign the user input to the 'balasan' field
        $datasatuan['balasan'] = $_POST['balasan'][$pertanyaan];
    }
}

if (isset($_POST['submit_balasan'])) {
    $response = submit_click($list_push);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pertanyaan</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        input[type="text"] {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Daftar Pertanyaan dan Balasan</h1>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div style="margin-top: 20px;">
            <!-- Loop through the first 10 items in the $list_push array -->
            <?php foreach (array_slice($list_push, 0, 10) as $item): ?>
                <label><?php echo $item['pertanyaan']; ?>
                    <input type="text" name="balasan[<?php echo $item['pertanyaan']; ?>]" placeholder="Isi balasan">
                </label>
            <?php endforeach; ?>
        </div>

        <button type="submit" name="submit_balasan">Submit Balasan</button>
    </form>

</body>
</html>






