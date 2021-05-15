<?php
// Igloohome API url
//$url = 'https://api.igloodeveloper.co/v2';

if (isset($_POST['request'])){ 
    
    include_once 'SQL.php';
    $variance = 1;
    $date = date('c');                      //gives current local time '2021-05-13T22:00:00+02:00' ISO Date in UTC +2h for swedish offset

    //Error handlers
    $conn->query('SET NAMES utf8');
    $sql = 'SELECT * FROM Locks WHERE LockNumber=1';	//Only one Lock exist
    $result = $conn->query($sql);
    
    if ($row = $result->fetch_assoc()) {        			    
        
        // Igloohome API url
        $url = 'https://api.igloodeveloper.co/v2/locks/' . $row['LockID'] . '/pin/onetime'; //onetime needs to turn into a variable from $_POST request
        
        // Request object
        $request = [
            'variance' => $variance,                                              // 1-5 possible active PINs for onetime
            'startDate' => substr_replace($date, '00:00', 14, 5)        // minutes and seconds needs to be 00      
        ];

        // Initializes a new cURL session
        $curl = curl_init($url);

        // Set the CURLOPT_RETURNTRANSFER option to true
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        // Set the CURLOPT_POST option to true for POST request
        curl_setopt($curl, CURLOPT_POST, true);

        // Set the request data as JSON using json_encode function
        curl_setopt($curl, CURLOPT_POSTFIELDS,  json_encode($request));

        // Set custom headers for API Auth and Content-Type header
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
          'X-IGLOOCOMPANY-APIKEY: ' . $row['APIKey'],   //API key from database
          'Content-Type: application/json'
        ]);

        // Execute cURL request with all previous settings
        $response = curl_exec($curl);
        // Close cURL session
        curl_close($curl);
        
        $RespObj = json_decode($response);  //Decode json data to PHP object
        
        //Response contains the PIN
        if($RespObj->pin){          
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO PINs (PIN, PINType, Variance, Date) VALUES ('$RespObj->pin', 'OTP', '$variance', '$date')";
            
            if ($conn->query($sql) === FALSE) {                 //Error inserting PIN into database
                echo "Error: " . $sql . "<br>" . $conn->error;    
            }
            header('Location: ../index.php?code=' . $RespObj->pin);
        }
        //Error requesting the PIN
        else{
            header('Location: ../index.php?code=' . $response);
        }      
    }
    else{
        // Nothing returned from Query
        //Error message here
    }
    $conn->close();
}
else{   
    //If someone tries to acces this page directly
    header('Location: ../index.php?AjaBaja');
}
