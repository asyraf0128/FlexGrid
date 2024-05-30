<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>chat</title>
        <style media="screen">
            body{
                background-color: grey;
                margin: 0px;
                padding: 0px;
            }

            #p1{
                background-color: burlywood;
                color: white;
                text-align: center;
                font-size: 40px;
                padding: 8px;
            }

            #div1{
                width: 800px;
                margin-left: 350px;
                margin-top: -20px;
                background-color: whitesmoke;
                padding-bottom: 30px;
                height: 610px;
                margin-bottom: 30px;
            }

            #chat{
                width: 600px;
                height: 50px;
                float: left;
                margin-left: 30px;
                margin-bottom: 30px;
                margin-top: 50px;
                font-size: 21px;
            }

            #sendbutton{
                float: left;
                margin-top: 50px;
                width: 150px;
                height: 55px;
                font-size: 25px;
                font-weight: bold;
                margin-left: 5px;
            }

            table{
                margin-left: 470px;
            }

            #td1{
                float: right;
                background-color: #0083fe;
                padding: 10px;
                color: white;
                border-radius: 20px;
                max-width: 230px;
                font-size: 19px;
                margin-top: 6px;

            }

            #div2{
                overflow: auto;
                width: 770px;
                height: 400px;
                padding: 10px;
            }

        </style>
    </head>
    <body>
        <div id="div1">
            <p id="p1">Chat</p>
            <div id="div2">

            <table>
                <?php
                error_reporting(0);
                include"db.php";
                $sql1="SELECT * FROM chat";
                $query1=mysqli_query($conn,$sql1);
                    while ($info=mysqli_fetch_array($query1)) {
                        ?>
                        <tr>
                            <td id="td1"><?php echo $info['chat']; ?></td>
                            <td id="td2"><?php echo formatDate($info["time"]); ?></td>
                            
                        </tr>
       
                        <?php
                    }


                ?>
            </table>

            </div>
            
            <form action="chat.php" method="post">
                <textarea id="chat" name="chat" rows="8" cols="80" placeholder="Write Message" required></textarea>
                <input id="sendbutton" type="submit" name="send" value="send">

            </form>
            <?php
                //include 'db.php';
                if (isset($_POST['send'])) {
                    header("Location: chat.php");
                    $chat=$_POST['chat'];
                    $sql="INSERT INTO chat(chat) values('$chat')";
                    $query=mysqli_query($conn,$sql);
  
                // code...
             }

            ?>

        </div>

    </body>
</html>