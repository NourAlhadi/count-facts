<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Visits And Numbers</title>

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
        }

        body {
            font-family: "Roboto", sans-serif;
        }

        #container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-image: radial-gradient(circle, #4d7bca, #2b4b80);
            color: #fff;
        }

        #heading {
            margin: 0 0 10px;
            font-size: 8em;
            text-shadow: 0px 1px 4px #393333;
            font-weight: 700;
        }

        #content {
            margin: 0 auto;
            text-align: center;
            font-size: 1.5em;
            letter-spacing: 2px;
            width: 75%;
        }
    </style>

</head>
<body>
    
    <div id="container">
        <h1 id="heading"></h1>
        <p id="content"></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
              
    <script type="text/javascript">
        
        // All action will happen on page load
        window.onload = function(){    
            <?php
                // We use encryption to disallow user to change the number of visits in cookie
                // Set global encryption / decryption variables
                $encryptionMethod = "AES-256-CBC";
                $secretHash = "25c6c7ff35b9979b151f2136cd13b0ff";

                // By default this is the first visit
                $number = 1;
                // If the user visited the page before extract the info from cookie
                if(isset($_COOKIE['visit'])){
                    $number = openssl_decrypt($_COOKIE['visit'],$encryptionMethod, $secretHash);
                }
            ?>

            // JS Variables to manipulate DOM
            var number = <?= $number ?>;
            var info = "";
            
            // Send the ajax request to get number info
            $.get('http://numbersapi.com/'+ number +'/trivia?notfound=floor&fragment', function(data) {
                
                // here is the info
                info = data;

                // update info on page
                document.getElementById('heading').innerText = number;
                document.getElementById('content').innerText = info;

                <?php

                    // Check if this is a new visit to the page (assume its true)
                    $newVisit = true;
                    if (isset($_COOKIE['last_visit'])){
                        $last = $_COOKIE['last_visit'];
                        $change = time() - $last;

                        // If last visit less than hour ago its just a refreh
                        // here we set the needed interval of new visit
                        if ($change < 3600){
                            $newVisit = false;
                        }
                    }
                
                    // if it is a new visit
                    if ($newVisit){
                        
                        // set the new number of visit (plus 1 visit)
                        $number = openssl_encrypt($number+1,$encryptionMethod,$secretHash);

                        // Update count and last_visit cookies
                        setcookie("last_visit", time(), time() + (86400 * 30), "/");
                        setcookie("visit",$number, time() + (86400 * 30), "/");
                    }
                ?>
            });
        };  
    
    </script>

</body>
</html>