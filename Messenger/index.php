<!DOCTYPE html>
<html>
    <head>
        <title>messenger</title>
    </head>
<style type="text/css">

    #font-face{
        font-family: ;
        src: url(ui/fonts/Summer-Vibes-OTF.otf);
    }

    #font-face{
        font-family: ;
        src: url(ui/fonts/Summer-Vibes-OTF.otf);
    }

    #wrapper{

        max-width: 900px;
        min-height: 500px;
        display: flex;
        margin: auto;
        color: white;
        font-family: myFONT;
        font-size: 13px;

    }

    #left_pannel{
        
        min-height: 500px;
        background-color: grey;
        flex: 1;
        text-align: center;
    }

    #profile_image{
        width: 80%;
        border: solid thin white;
        border-radius: 50%;
        margin: 10px;
        
    }

    #left_pannel label{
        width: 100%;
        height: 20px;
        display: block;
        font-size: 14px;
        background-color: #404b56;
        border-bottom: solid thin #ffffff55;
        cursor: pointer;
        padding: 5px;
        transition: all 0.5s ease;

    }

    #left_pannel label:hover{
        
        background-color: #778593;

    }

    #left_pannel label img{
        float: right;
        width: 25px;



    }

    #right_pannel{
        
        min-height: 500px;
        background-color: grey;
        flex: 4;
        text-align: center;
    }

    #header{

        background-color: #485b6c;
        height: 70px;
        font-size: 40px;
        text-align: center;
        font-family: ;
    }

    #inner_left_pannel{
        background-color: purple;
        flex: 1;
        min-height: 430px;
    }

    #inner_right_pannel{
        background-color: pink;
        flex: 2;
        min-height: 430px;
        transition: all 0.5s ease;
    }

    #radio_contacts:checked ~ #inner_right_pannel{
        flex: 0;
    }

    #radio_settings:checked ~ #inner_right_pannel{
        flex: 0;
    }

</style>
<body>

    <div id="wrapper">

       <div id="left_pannel">

         <div style="padding: 10px;">
          <img id="profile_image" src="ui/images/user3.jpg">
          <br>
          ASYONTHEHOUSE
          <br>
          <span style="font-size: 12px;opacity: 0.5;">asyonthehouse@gmail.com</span>

          <br>
          <br>
          <br>
          <div>
            <label for="radio_chat">Chat <img src="ui/icons/chat.png"></label>
            <label for="radio_contacts">Contacts <img src="ui/icons/contacts.png"></label>
            <label for="radio_settings">Settings <img src="ui/icons/settings.png"></label>
          </div>

         </div>
       </div>
       <div id="right_pannel">
        <div id="header">Messenger</div>
        <div id="container" style="display: flex">

            <div id="inner_left_pannel">
                
            </div>

            <input  type="radio" id="radio_chat" name="myradio" style="display: none;">
            <input  type="radio" id="radio_contacts" name="myradio" style="display: none;">
            <input  type="radio" id="radio_settings" name="myradio" style="display: none;">

            <div id="inner_right_pannel">

            </div>
        </div>
       </div>
    </div>
</body>
</html>