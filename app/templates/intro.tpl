<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>{$APPLICATION_TITLE} {$APPLICATION_VERSION}</title>
        <link href="{$CONFIG_CSS}/images/favicon.ico" type="image/x-icon" rel="shortcut icon"/>
        <link  rel ="stylesheet" type="text/css" href="{$CONFIG_CSS}/style.css"/>
        <script type="text/javascript" src="{$CONFIG_JS}/jquery/jquery-1.10.1.min.js"></script>
        <script type="text/javascript" src="{$CONFIG_JS}/jquery/jquery.validate.js"></script>
    </head>
    <body id="intro">
        <div id="main">			
            <div>
                <table width="100%" style="table-layout:fixed;">
                    <tr>
                        <td style="width: 211px; overflow:hidden;">
                            <img style="border: 0px; margin-bottom: 16px;" src="{$CONFIG_CSS}/img/wave.gif">
                        </td>					
                        <td style="width: 177px;">
                            <img style="border: 0px;" src="{$CONFIG_CSS}/img/logo.png">
                        </td>
                        <td style="width: 211px; overflow:hidden;">
                            <img style="border: 0px; margin-bottom: 16px;" src="{$CONFIG_CSS}/img/wave.gif">
                        </td>
                    </tr>
                </table>
            </div>
            <div id="login_form">
                <table width="100%">
                    <tr>
                        <td>
                            <input type="button" class="invite log" value="I HAVE AN INVITE" />
                            <input type="password" class="invite dn" value="" />
                        </td>					
                        <td>
                            <input type="button" class="invite" value="ASK FOR AN INVITE" />                            
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <script type="text/javascript">
            {literal}
                
                $( document ).ready(function() {
                    $(".log").on( "click", function() {
                        $(".log").hide();
                        $(".log").next().show(); 
                        $(".log").next().focus();
                    });
                });   
                    
            {/literal}
        </script>
    </body>
</html>