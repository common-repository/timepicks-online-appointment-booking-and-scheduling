<?php
// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
?>
<?php
$isTpUser = FALSE;
if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE && get_option(TIME_PICKS_SUBDOMAIN) != ""):
    $isTpUser = TRUE;
endif;

?>

<script type= "text/javascript"  src="<?php echo TP_PLUGIN_DOMAIN; ?>js/common.js" ></script>
<script type= "text/javascript"  src="<?php echo TP_PLUGIN_DOMAIN; ?>js/jQuery/jquery-1.2.6.js" ></script>
<script type="text/javascript">

    function submitDetails() {
        window.ok_to_go = true;

        if (document.getElementById("signup_firstName").value == "") {
            document.getElementById("signup_firstName").style.border = "1px solid red";
            window.ok_to_go = false;
        }
        else {
            document.getElementById("signup_firstName").style.border = "1px solid #7F9DB9";
        }

        if (document.getElementById("signup_password").value == "") {
            document.getElementById("signup_password").style.border = "1px solid red";
            window.ok_to_go = false;
        }
        else {
            document.getElementById("signup_password").style.border = "1px solid #7F9DB9";
            if (window.ok_to_go == true)
                validatePassword(document.getElementById("signup_password").value);
        }

        if (document.getElementById("signup_email").value == "") {
            document.getElementById("signup_email").style.border = "1px solid red";
            window.ok_to_go = false;
        }
        else {
            document.getElementById("signup_email").style.border = "1px solid #7F9DB9";
        }

        if (window.ok_to_go === true) {
            checkEmailAgain(document.getElementById("signup_email").value);
        }
    }


    function registerform_signup(fname, signupemail, passwd, signupurl) {
        window.loginemail = signupemail;
        window.loginpass = passwd;
        var parameters = "&fname=" + fname + "&signupemail=" + signupemail + "&passwd=" + passwd + "&signupurl=" + signupurl;
        $.getJSON("<?php echo TP_PLUGIN_DIR_URL; ?>ajax/checkifParameterExists.php", parameters, function(data){
            if (data.status == "true"){
                    $('#email').val(signupemail);
                    $('#passwd').val(passwd);
                    $('#tp-fieldset').show();$('#tp-fieldset-1').hide();
                    $('#tpcreateusersub').trigger('click');

                window.ok_to_go = false;

            } 
            else{
                alert('Account not created');
                window.ok_to_go = true;
            }
        });
    }


    function checkEmail(value) {
        if (validateEmail(value)) {
            var parameters = "&email=" + value;
            $.getJSON("<?php echo TP_PLUGIN_DIR_URL; ?>ajax/checkifEmailExists.php", parameters, function(data) {
                if (data.status == "true") {
                    document.getElementById("signup_email").style.border = "1px solid red";
                    document.getElementById("emailStatus").style.backgroundColor = "#FFC9BB";
                    document.getElementById("emailStatus").innerHTML = "Email already registered."
                    window.ok_to_go = false;
                }
                else {
                    document.getElementById("signup_email").style.border = "1px solid #7F9DB9";
                    document.getElementById("emailStatus").style.backgroundColor = "";
                    document.getElementById("emailStatus").innerHTML = "";
                    window.ok_to_go = true;
                    checkURLDuplicate(document.getElementById("signup_accountURL").value);
                }
                console.log('window.goto in checkEmail is ' + window.ok_to_go);
            });
        } else
        {//invalid email string
            document.getElementById("signup_email").style.border = "1px solid red";
            document.getElementById("emailStatus").style.backgroundColor = "#FFC9BB";
            document.getElementById("emailStatus").innerHTML = "Email address not valid.";
            window.ok_to_go = false;
        }
    }



    function validatePassword(value) {
        if (isAlphabetNumeric(value)) {
            console.log('It is alphanumeric')
            if (value.length < 6) {
                console.log('It value is less than 6')
                document.getElementById("signup_password").style.border = "1px solid red";
                document.getElementById("passwordStatus").style.background = "#FFC9BB";
                document.getElementById("passwordStatus").innerHTML = "password needs to be at least 6 characters.";
                window.ok_to_go = false;
            }
            else {
                console.log('It value is greater than 6')
                document.getElementById("signup_password").style.border = "1px solid #7F9DB9";
                document.getElementById("passwordStatus").style.background = "";
                document.getElementById("passwordStatus").innerHTML = "";
                if (window.ok_to_go == false)
                    window.ok_to_go = true;
            }
        }
        else {
            console.log('It is not alphanumeric')
            document.getElementById("signup_password").style.border = "1px solid red";
            document.getElementById("passwordStatus").style.background = "#FFC9BB";
            document.getElementById("passwordStatus").innerHTML = "password must contains only numbers and letters";
            window.ok_to_go = false;
        }

        console.log('window.goto in Validate password is ' + window.ok_to_go);
    }


    function checkURLDuplicate(v) {
        if (document.getElementById("signup_accountURL").value == '') {
            document.getElementById("signup_accountURL").style.border = "1px solid red";
            document.getElementById("accountURLStatus").style.backgroundColor = "#FFC9BB";
            document.getElementById("accountURLStatus").innerHTML = "Please provide any url.";
            window.ok_to_go = false;
        }
        else {
            var parameters = "&url=" + v;
            $.getJSON("<?php echo TP_PLUGIN_DIR_URL; ?>ajax/checkIfURLExists.php", parameters, function(data) {
                if (data.status == "true") {
                    document.getElementById("signup_accountURL").style.border = "1px solid red";
                    document.getElementById("accountURLStatus").style.backgroundColor = "#FFC9BB";
                    document.getElementById("accountURLStatus").innerHTML = "This address is taken.Try another.";
                    window.ok_to_go = false;
                }
                else {
                    document.getElementById("signup_accountURL").style.border = "1px solid #7F9DB9";
                    document.getElementById("accountURLStatus").style.backgroundColor = "";
                    document.getElementById("accountURLStatus").innerHTML = "";
                    window.ok_to_go = true;
                }
            });
        }

    }


    function checkEmailAgain(value)
    {
        if (validateEmail(value)) {
            var parameters = "&email=" + value;
            $.getJSON("<?php echo TP_PLUGIN_DIR_URL; ?>ajax/checkifEmailExists.php", parameters, function(data) {
                if (data.status == "true") {
                    document.getElementById("signup_email").style.border = "1px solid red";
                    document.getElementById("emailStatus").style.backgroundColor = "#FFC9BB";
                    document.getElementById("emailStatus").innerHTML = "Email already registered."
                    okToGo = 'false';
                    window.ok_to_go = false;

                    if (document.getElementById("signup_accountURL").value == "") {
                        document.getElementById("signup_accountURL").style.border = "1px solid red";
                        window.ok_to_go = false;
                    } else
                    {
                        document.getElementById("signup_accountURL").style.border = "1px solid #7F9DB9";
                        checkURLDuplicateAgain(document.getElementById("signup_accountURL").value);
                    }
                }
                else {
                    document.getElementById("signup_email").style.border = "1px solid #7F9DB9";
                    document.getElementById("emailStatus").style.backgroundColor = "";
                    document.getElementById("emailStatus").innerHTML = "";
                    okToGo = 'true';
//                                window.ok_to_go = true;
                    if (document.getElementById("signup_accountURL").value == "") {
                        document.getElementById("signup_accountURL").style.border = "1px solid red";
                        window.ok_to_go = false;
                    } else
                    {
                        document.getElementById("signup_accountURL").style.border = "1px solid #7F9DB9";
                        checkURLDuplicateAgain(document.getElementById("signup_accountURL").value);
                    }
                }
                console.log('window.goto in Check Email again is ' + window.ok_to_go);
            });
        }
        else {//invalid signup_email string
            document.getElementById("signup_email").style.border = "1px solid red";
            document.getElementById("emailStatus").style.backgroundColor = "#FFC9BB";
            document.getElementById("emailStatus").innerHTML = "Email address not valid.";
            okToGo = 'false';
            window.ok_to_go = false;

        }

    }


    function checkURLDuplicateAgain(v) {
        var parameters = "&url=" + v;
        $.getJSON("<?php echo TP_PLUGIN_DIR_URL; ?>ajax/checkifURLExists.php", parameters, function(data) {
            if (data.status == "true") {
                document.getElementById("signup_accountURL").style.border = "1px solid red";
                document.getElementById("accountURLStatus").style.backgroundColor = "#FFC9BB";
                document.getElementById("accountURLStatus").innerHTML = "This address is taken.Try another.";
                window.ok_to_go = false;
            }
            else {
                document.getElementById("signup_accountURL").style.border = "1px solid #7F9DB9";
                document.getElementById("accountURLStatus").style.backgroundColor = "";
                document.getElementById("accountURLStatus").innerHTML = "";
                window.ok_to_go = true;
            }

            console.log('window.goto in Check URL Duplicate again is ' + window.ok_to_go);

            if (window.ok_to_go == true) {
                var fname = document.getElementById("signup_firstName").value;
                var signupemail = document.getElementById("signup_email").value;
                var passwd = document.getElementById("signup_password").value;
                var signupurl = document.getElementById("signup_accountURL").value;
                registerform_signup(fname, signupemail, passwd, signupurl);
            }

        });
    }




</script>
<style>
    /* ----------- My Form ----------- */
    .myform{
        margin:0 auto;
        width:450px;
        padding:14px;
    }

    .myform div
    {
        margin-left:100px; 
    }

    /* ----------- stylized ----------- */
    #stylized h1 {
        font-size:14px;
        font-weight:bold;
        margin-bottom:8px;
    }

    #stylized p{
        font-size:11px;
        color:#666666;
        margin-bottom:20px;
        border-bottom:solid 1px #b7ddf2;
        padding-bottom:10px;
    }
    #stylized label{
        display:block;
        font-weight:bold;
        text-align:left;
        width:140px;
        padding-left:10px;

    }
    #stylized .small{
        color:#666666;
        display:block;
        font-size:11px;
        font-weight:normal;
        text-align:right;
        width:140px;
    }
    #stylized input{
        float:left;
        font-size:12px;
        padding:4px 2px;
        border:solid 1px #aacfe4;
        width:200px;
        margin:2px 0 20px 10px;
    }

    #stylized button{
        clear:both;
        margin-left:110px;
        cursor:pointer; 
        -webkit-box-shadow:rgba(0,0,0,0.0.1) 0 1px 0 0;
        -moz-box-shadow:rgba(0,0,0,0.0.1) 0 1px 0 0;
        box-shadow:rgba(0,0,0,0.0.1) 0 1px 0 0;
        background-color:#5B74A8;
        border:1px solid #29447E;
        font-family:'Lucida Grande',Tahoma,Verdana,Arial,sans-serif;
        font-size:14px;
        font-weight:700;
        padding:2px 6px 6px 2px;

        color:#fff;
        border-radius:5px;
        -moz-border-radius:5px;
        -webkit-border-radius:5px; 
    }


    .section {
        -moz-box-sizing: border-box;
        background: none repeat scroll 0 0 #FFFFFF;
        border: 1px solid #CCCCCC;
        border-radius: 7px 7px 7px 7px;
        margin-bottom: 10px;
        overflow: hidden;
        padding: 10px;
    }


    .box {
        background: none repeat scroll 0 0 #FFFFFF;
        border: 1px solid #CCCCCC;
        border-radius: 7px 7px 7px 7px;
        margin-bottom: 10px;
        padding: 0 10px;
    }

    .box .title {
        border-bottom: 1px solid #DDDDDD;
        color: #0099FF;
        display: block;
        font-size: 14px;
        font-weight: normal;	
        padding-bottom:10px;
    }


    #loginh3{
        cursor:pointer; 
    }


    .input label
    {
        text-align:left;
    }



    #upgrade.button{
        background: none repeat scroll 0 0 #2AA7EA;
        border: 1px solid #CCCCCC;
        border-radius: 5px 5px 5px 5px;
        color: #FFFFFF !important;
        display: inline-block;
        font-size: 18px;
        min-width: 90px;
        padding: 5px 12px;
        text-align: center;
        text-decoration: none;
		height:40px;	
		
    }

    #upgrade.button:hover {
        background: none repeat scroll 0 0 #179CE1;
        box-shadow: 0 1px 4px #333333;
    }

</style>
<script type="text/javascript">
    jQuery(function() {
        jQuery(".btnTpDel").click(function() {
            var res = confirm("<?php echo TP_ACCOUNT_DISCONN_MSG; ?> ?");
            if (res)
            {
                location.href = $(this).attr("href");
            }
            return false;
        });

        //TP User Options Admin
        jQuery(".tp-login-options").change(function() {
            if (jQuery(this).attr('id') == "tpNewUserOpt") {
                //Option 1 here
                jQuery("#tp-fieldset").fadeOut('fast', function() {
                    jQuery("#tp-fieldset-1").fadeIn('fast');
                });
            } else {
                //Option 2 here
                jQuery("#tp-fieldset-1").fadeOut('fast', function() {
                    jQuery("#tp-fieldset").fadeIn('fast');
                });
            }
        });

        jQuery("#tp-container #passwd").keypress(function(e) {
            if (e.which == 13) {
                jQuery("#tpcreateusersub").trigger('click');
            }
        });

<?php if ($isTpUser == TRUE): ?>
            jQuery("#tpUserOpt").attr('checked', 'checked');
<?php endif; ?>

    });
</script>
<div class="wrap">
    <div id="icon-index" class="icon32"><br /></div>
    <?php echo "<h2>" . TP_PLUGIN_NAME . "</h2> "; ?> 
    <div id="tp-container">
        <div id="tp_flash_message" style="display:none;"></div>
        <div id="tp-left-container">
            
            <fieldset id="tp-fieldset-1" <?php echo ($isTpUser == FALSE) ? 'style="display:block;float:left;"' : 'style="display:none;"' ?>>
                <div>

                    <div id="signupArea">
                        <div style="padding-left:20px; ">		
                        </div>
                        <div id="stylized" class="myform">
						<legend style="font-size:20px;padding-bottom:10px;"><?php echo TP_PLUGIN_NAME; ?> Account Signup</legend>
                            <form id="form" name="form" method="post" action="<?php echo TP_PLUGIN_ADMIN; ?>">
                                <div class="input">	
                                    <label>First Name</label>
                                    <input type="text" required id="signup_firstName" value="" />
                                </div> 
                                <div class="input">	
                                    <label>Email
                                    </label><span id="emailStatus" class="small"></span>
                                    <input type="email" required id="signup_email" value="" onchange="checkEmail(this.value);"/>
                                </div>
                                <div class="input">
                                    <label>Password
                                    </label><span id="passwordStatus" class="small"></span>
                                    <input type="password" required id="signup_password" value="" onchange="validatePassword(this.value);" />
                                </div>
                                <div class="input">
                                    <label>Booking Page URL</label>	
                                    <span id="accountURLStatus" class="small"></span>
                                    <div style="float:left;margin-left:0px;">
                                        <input type="text" value="" required  id="signup_accountURL" onchange="return checkURLDuplicate(this.value);"    onkeypress="return alphanumericonly(event);" ></input><div style="float:left;margin-left:0px;margin-top:12px;font-weight:bold;font-size:12px;"><b>.timepicks.com</b></div></div>
                                </div>	
                                <button id="upgrade"  class="button" type="button" onclick="submitDetails();">Create My Scheduler</button>
                                <div class="spacer"></div>
                                <input type="hidden" id="instanceId" name="instanceId" value="<?php echo($instanceId); ?>"></input>
                                <input type="hidden" id="instance" name="instance" value="<?php echo($instance); ?>"></input>
                                <input type="hidden" id="compId" name="compId" value="<?php echo($compId); ?>"></input>
                                <input type="hidden" id="origCompId" name="origCompId" value="<?php echo($origCompId); ?>"></input>				
                            </form>
                        </div>
                    </div>


                </div>
            </fieldset>
			
			
            <fieldset id="tp-fieldset" <?php echo ($isTpUser == TRUE) ? 'style="display:none;"' : 'style="display:block;"' ?>>
                <div id="tpLoginArea" <?php echo ($isTpUser == TRUE) ? 'style="display:none;"' : 'style="display:block; margin:auto; width:440px;"' ?>>
                    <legend><?php echo TP_PLUGIN_NAME; ?> Account Login</legend>
                    <form action="<?php echo TP_PLUGIN_ADMIN; ?>&pgcmd=linkaccount" method="post" name="accountsettings" id="tp_accountsettings">
                        <ul>
                            <li>
                                <label for="email">E-mail: </label>
                                <input name="email" type="email" id="email" required/>
                            </li>
                            <li>
                                <label for="password">Password: </label>
                                <input name="passwd" type="password" id="passwd" required/>
                            </li> 
                            <li>
                                <label>&nbsp;</label>
                                <button type="button" name="createuser" id="tpcreateusersub" class="button-primary" style="height:30px;">Login</button>
                                <img src="<?php echo TP_PLUGIN_DIR_URL; ?>images/ajax.loader.gif" id="tpajaxProg" title="Please wait..." style="display: none; float: left; margin: 4px;"/>
                                <br style="clear:both;margin-bottom:4px;" />
                            </li>
                        </ul>
                    </form>
                </div>
            </fieldset>
			
			
			               <div id="timesPickLinkedInfo" <?php if ($isTpUser == TRUE) {echo 'style="display:block;"';} ?>>
                    <div id="timesPickLinkedInfoTitle">
                  
                   <p style="font-size:18px; text-shadow: 0 .5px .5px #333;"> "Welcome to your timepicks account <b>14 day Free Trial</b>.<br/>
<b>Getting Started</b><br/>
 Please make sure that you have configured your services and availabilitities by going to the <b><a href="<?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo TP_PLUGIN_HTTP . '://' . get_option(TIME_PICKS_SUBDOMAIN);
    } ?>/myaccount" target="_blank">settings of your timepicks account</a></b>.<br/> To access your settings Log in to your admin panel at <?php echo get_option(TIME_PICKS_SUBDOMAIN); ?>.timepicks.com/myaccount and click settings in the top right."</p>
	<br/>
	
	<div style="font-size:18px; text-shadow: 0 .5px .5px #333;float:right;">For help or assistance <b>support@timepicks.com</b> </div>
    
    
                        <h2>Admin Panel : <a href="<?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo TP_PLUGIN_HTTP . '://' . get_option(TIME_PICKS_SUBDOMAIN);
    } ?>" target="_blank"><?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo get_option(TIME_PICKS_SUBDOMAIN);
    } ?></a></h2>
                        <h3>Access Admin : <a title='Go to your domain on Timepick.com' href="<?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo TP_PLUGIN_HTTP . '://' . get_option(TIME_PICKS_SUBDOMAIN);
    } ?>/myaccount" target="_blank"><?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo get_option(TIME_PICKS_SUBDOMAIN);
    } ?></a></h3>
                    </div>
                    <h3 style="float:left;">Linked Account Info: </h3>
                    <div id="tp-shortcodes">
                        <p id="service_id"><b>Your <?php echo TP_PLUGIN_NAME; ?> SubDomain:</b> <span id="tpSubDomName"><?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo get_option(TIME_PICKS_SUBDOMAIN);
    } ?></span></p>
                        <p id="tp_email"><b><?php echo TP_PLUGIN_NAME; ?> Email:</b> <span id="tpEmailAdd"><?php if (get_option(TIME_PICKS_SUBDOMAIN) != FALSE) {
        echo get_option(TIME_PICKS_EMAIL);
    } ?></span> </p>
                        <a href="<?php echo TP_PLUGIN_ADMIN; ?>&tp_unlink=1" name="unlink_account" id="unlink_account" class="button-primary btnTpDel">Unlink Account</a><br/><br/><br/>
                    </div>
                </div>
        </div>
     

    </div>
</div>