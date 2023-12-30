
<?php
   date_default_timezone_set('Asia/Kolkata');
   $current_time = date("Y-m-d H:i:s");

   $current_hour = date('H', strtotime($current_time));
   $imgurl;
   if ($current_hour >= 4 && $current_hour <= 11) {
       $imgurl = "img/Morning.jpg";
   } else if ($current_hour >= 12 && $current_hour <= 16) {
       $imgurl = "img/Noon.jpg";
   } else if ($current_hour >= 16 && $current_hour <= 20) {
       $imgurl = "img/Evening.jpg";
   } else if ($current_hour >= 21 && $current_hour <= (3 + 24)) {
       $imgurl = "img/nightsky.jpg";
   }

   $temperature="";
   $description="";
   $iconUrl="";
   $city_name="";
   $cityname_err="";
   if(isset($_GET['cityname'])){
     if(empty(trim($_GET['cityname']))){
        $cityname_err="Cityname empty";
     }
     else{
        $cityname=$_GET['cityname'];
     }

     if ($cityname_err == "") {
        $apikey = '6f99a92120d6daafdc06de831c3454d6';
        $unit = 'metric';
        $city = $cityname;
        $url = "http://api.openweathermap.org/data/2.5/weather?q=$city&units=$unit&appid=$apikey";
        
        // Use stream context to handle HTTP errors without displaying warnings
        // stream_context_create function, and it's used to configure the behavior of the file_get_contents function when making an HTTP request.
        $context=stream_context_create(['http' => ['ignore_errors'=> true]]);
        //By setting ignore_errors to true in the stream context, the function will not trigger a warning if the HTTP request results in an error (e.g., a 404 Not Found or 500 Internal Server Error). Instead, it will return false, and you can check for this condition explicitly.
        $response = file_get_contents($url, false, $context);
    
        // Check if the HTTP request was successful
        if ($response !== false) {
            $data = json_decode($response, true);
    
            if (isset($data['cod'])) {
                if ($data['cod'] === 200) {    // ==loose equalit checks only value but ===strict equality checks value and date type
                    // API response is successful, proceed with extracting data
                    $temperature = $data['main']['temp'];
                    $description = $data['weather'][0]['description'];
                    $iconcode = $data['weather'][0]['icon'];
                    $city_name=$data['name'];
    
                    // Construct the icon URL
                    $iconUrl = "http://openweathermap.org/img/w/$iconcode.png";
                } else {
                    // API response indicates an error
                    echo '<script>window.alert(" '. $data['message'].' ")</script>';
                    // die('Error: ' . $data['message']);
                }
            } else {
                // The 'cod' field is not present in the response, handle it as an unexpected error
                die("Error: Unexpected response from the API");
            }
        } else {
            // Handle the case where the HTTP request was not successful
            die("Error: Unable to fetch data from the API");
        }
    } else {
        // Handle the case where $cityname_err is not empty
        echo '<script>window.alert(" '. $cityname_err.' ")</script>';
    }
    
   }    
   


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <META HTTP-EQUIV="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="Main-icon.png" type="image/x-icon"/>
    <title>Weather App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
          integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
</head>
<body>
<div class="glass-container">
    <h1>Weather App</h1>
    <div class="search">
        <form action="" method="GET" id="searchform">
            <input type="text" placeholder="Enter your City" name="cityname" required>
            <i class="fa-solid fa-magnifying-glass-location" onclick="submitform()"></i>
        </form> 
    </div>
    <div id="weather-icon">

    </div>
    <?php

    if(!empty($city_name)){
        echo '<h3>'.$city_name.'</h3>';
    }
    if(!empty($temperature)){
        echo '<h3>'.$temperature.'°C</h3>';
    }
    if(!empty($description)){
        echo '<h3>'.$description.'</h3>';
    }

    ?>
</div>
<script>
    function submitform(){
        document.getElementById('searchform').submit();
    }
</script>
</body>
<style>
    * {
        margin: 0;
        border: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        height: 100vh;
        background-image: url('<?php echo $imgurl; ?>');
        background-size: cover;
    }

    .glass-container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 30%;
        height: 70%;
        display:flex;
        justify-content:space-evenly;
        flex-direction:column;
        align-items:center;
    }

    .search{
        width:100%;
        height:20%;
        display:flex;
        justify-content:space-evenly;
        align-items:center;
    }
    .search form{
        height:100%;
        width:100%;
        display:flex;
        justify-content:space-evenly;
        align-items:center;
    }
    .search input{
        width:60%;
        height:40%;
        text-align:center;
        font-weight:bold;
        border:none;
        border-radius:4px;
        transition: border 0.3s;
    }
    .search input:focus {
         border: 2px solid green;
    }

    .search i{
        color: #fff;
        cursor: pointer;
        transform:scale(200%,200%);
    }
    .search i:active{
        color: #fff;
        transform:scale(180%,180%);
    }
    #weather-icon {
        background-image: url('<?php echo $iconUrl; ?>');
        background-size: cover;
        width:35%;
        height:20%;
    }
    .glass-container h3{
        text-transform:capitalize;
    }

</style>
</html>


