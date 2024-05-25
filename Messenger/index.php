<!DOCTYPE html>
<html>
    <head>
        <title>messenger</title>
    </head>
<style type="text/css">

    #font-face{
        font-family: ;
        src: url(ui/fonts/Summer-Vibes-OTF.otf)
    }
    #wrapper{

        max-width: 900px;
        min-height: 500px;
        display: flex;
        margin: auto;
        color: white;

    }

    #left_pannel{
        
        min-height: 500px;
        background-color: turquoise;
        flex: 1;
    }

    #right_pannel{
        
        min-height: 500px;
        background-color: grey;
        flex: 4;
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
    }
</style>
<body>

    <div id="wrapper">

       <div id="left_pannel">

       </div>
       <div id="right_pannel">
        <div id="header">Messenger</div>
        <div id="container" style="display: flex">
            <div id="inner_left_pannel"></div>
            <div id="inner_right_pannel"></div>
        </div>
       </div>
    </div>
</body>
</html>