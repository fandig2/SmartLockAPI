<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Parawind</title>
    </head>
    <body>
        <H1>Amis coola sida</H1>
        <?php           
            if (isset($_GET['code'])){
                echo 'Your code is: ' . htmlspecialchars($_GET["code"]);
            }           
        ?>
        
        <!--<P> Body    <br></p>-->
        
        <div id="body-wr" style="text-align: center">
            <form action="Processing/PINRequest.php" method="post">
                <input type="submit" name="request" value="Ge mig koden" />
            </form>
        </div>
    </body>
</html>
