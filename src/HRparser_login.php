<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="css/HRparser_login.css">
    <link href="https://fonts.googleapis.com/css2?family=Righteous&family=Varela+Round&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@200&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <title>HR PARSER LOGIN PAGE</title>
</head>
<body>
    <?php include 'includes/HRparser_loginUtils.php'; ?>

    <!----------------------------------- SIGN IN PAGE DEFINITION ----------------------------------->
    <!-- To avoid the flickering effect when a dialog is submitted, lets wait until those dialog are 
         displayed and then show the SignInPage in the background -->
    <div class="signInPage" id="signInPage" 
         style="display:
			<?php if($h_sUDialogFormSubmitted || $h_fPDialogFormSubmitted || 
				 $h_vEDialogFormSubmitted || $h_nPDialogFormSubmitted){ 
				  echo('none');} else{ echo('block');} ?>">

        <!-- Div to place big signIn image -->
        <div class="signInImgDiv"></div>
    
        <!-- Sign In dialog -->
        <aside class="signInDialog">   

            <!-- Div to place logo -->       
            <div class="logoDiv">
                <img src="../resources/LogoHRParser.png" alt=""> 
            </div> 
            
            <!-- Div to place the sign ip form -->
            <div class="signInFormDiv">
                <p>Sign in with your personal account</p>

                <!-- Sign in form -->
                <form action="" method="get" class="signInForm">
                    <div class="inputsDiv">
		        <input type="email" id="signInEmail" placeholder="Email">
                        <input type="password" id="signInPwd" placeholder="Password">
                    </div>

                    <div class="signInHelpDiv">
                        <p>Wrong credentials</p>
                        <span href="" class="emulatedLink" id="forgotPwdLink">Forgot Password?</a> 
                    </div>
                                        
                    <!-- Sign in button -->
                    <input type="submit" value="Sign in" id="signInBtn">

                    <div class="signUpDiv">
			<p>Need an account? </p>
			<span href="" class="emulatedLink" id="signUpLink">SIGN UP</span>
                    </div>
                    
                </form> <!-- SIGN IN FORM -->
            </div> <!-- SIGN IN FORM DIV -->
        </aside> <!-- SIGN IN DIALOG -->
    </div> <!-- SIGN IN PAGE -->
    
    <!----------------------------------- SIGN UP PAGE DEFINITION ----------------------------------->
    <!-- SignUp page display property set here to avoid flickering effects caused by doing this later.
         This flickering effect happens in scenarios where SignUp button is pressed (form is submitted) -->
    <div class="signUpPage" id="signUpPage" style="display:<?php if($h_sUDialogFormSubmitted){ echo('block');} else{ echo('none');} ?>" >
	
	<!-- Div used to simply allocate the signUpDialog and avoid that scrolling hides
                the address bar and impacts how the background(signInPage) looks like.
                This is a discovered workaround. -->
        <div class="signUpScrollableDiv">

	    <!-- Top vertical spacer when SignUp dialog overflows -->
	    <!-- Change the height of the upper vertical spacer when confirmationEmail dialog is displayed -->
	    <div class="signUpVerticalSpacer"></div>

	    <!-- White dialog to sign up -->
	    <!-- SignUp dialog display property set here to avoid flickering effects caused by doing this later.
                 This flickering effect happens in scenarios where SignUp button is pressed (form is submitted) -->
	    <div id="signUpDialog" style="display:<?php //if($h_sUDialogFormSubmitted || $h_sUValidationInvSts){ echo('block');} else{ echo('none');} ?>" >

		<div id="signUpDialogCloseMarkSvg"></div>

                <!-- Div to place logo -->
                <div class="logoDiv">
                    <img src="../resources/LogoHRParser.png" alt=""> 
                </div>  
    
                <!-- Div to place the sign up form -->
                <div id="signUpFormDiv">
                    <p>Sign up to access the application</p>
    
                    <!-- Sign up form -->
                    <form action="HRparser_login.php" method="POST" class="signUpForm">                        
			<div class="inputsDiv">

                            <div>			    
			    <input type="text" id="signUpName" name="signUpFirstName" placeholder="First Name" value="<?= $h_sUUser['signUpFirstName'] ?>" >
			    <span class="signUpErrorMsg"><?= $h_sUErrors['signUpFirstName'] ?> </span>
                            </div>

                            <div>
			    <input type="text" id="signUpLastName" name="signUpLastName" placeholder="Last Name" value="<?= $h_sUUser['signUpLastName'] ?>">
			    <span class="signUpErrorMsg"><?= $h_sUErrors['signUpLastName'] ?> </span>
                            </div>

                            <div>
			    <select id="selectCountry" name="country">
			        <option style="color:gray" value="null">Select a country</option> 
				<?php foreach($h_countries as $key => $value){ 
                                          if($value != $h_sUUser['country']){
                                ?>
				<option style="color:black" value="<?= $h_countries[$key] ?>"><?= $h_countries[$key] ?></option>
				<?php     }else{ ?>
                                <option style="color:black" value="<?= $h_countries[$key] ?>" selected="selected"><?= $h_countries[$key] ?></option>
				<?php     } 
                                      } ?>
			    </select>
                            <span class="signUpErrorMsg"><?= $h_sUErrors['country'] ?> </span>
                            </div>

                            <div>
			    <input type="email" id="signUpEmail" name="signUpEmail" placeholder="Email" value="<?= $h_sUUser['signUpEmail'] ?>">
			    <span class="signUpErrorMsg"><?= $h_sUErrors['signUpEmail'] ?> </span>
                            </div>

                            <div>
			    <input type="password" id="signUpPwd" name="signUpPwd" placeholder="Password">
			    <span class="signUpErrorMsg"><?= $h_sUErrors['signUpPwd'][0] ?> </span>
 
			    <?php for($i = 1; $i < count($h_sUErrors['signUpPwd']); $i++){ ?>
				  <span class="signUpErrorMsg"><?= $h_sUErrors['signUpPwd'][$i] ?></span>
			    <?php } ?>
                            </div>

                            <div>
			    <input type="password" id="signUpConfPwd" name="signUpConfPwd" placeholder="Confirm Password">
			    <span class="signUpErrorMsg"><?= $h_sUErrors['signUpConfPwd'] ?> </span>
			    </div>

                            <div>
			    <input type="checkbox" id="signUpTerms" name="signUpTerms" value="true">
                                 <span id="signUpTermsText">I agree to the </span><span href="" class="emulatedLink" id="signUpTermsLink">Privacy Policy and Terms & Conditions</span>
                            <span class="signUpErrorMsg"><?= $h_sUErrors['signUpTerms'] ?> </span>
                            </div>

                        </div> <!-- INPUTS DIV -->
                        
                        <!-- Sign up button -->
                        <input type="submit" name="signUpForm" value="Sign Up" id="signUpBtn">
                        
                        <div class="signInDiv">
			    <p>Already a user? </p>
			    <!-- No need to specify Url since current web page shows the signIn page -->
                            <span href="" class="emulatedLink" id="signInLink">LOG IN</span>
                        </div>
                    </form> <!-- SIGN UP FORM -->
                </div> <!-- SIGN UP FORM DIV -->
                
	    </div> <!-- SIGN UP DIALOG -->

	    <!-- White dialog to show the Confirmation Email dialog -->
	    <!-- ConfirmationEmail dialog display property set here to avoid flickering effects caused by doing this later.
                 This flickering effect happens in scenarios where SignUp button is pressed (form is submitted) -->
	    <div id="confirmationEmailDialog" 
		 style="display:
		                <?php if($h_sUDialogFormSubmitted && !$h_sUValidationInvSts)
					  { echo('none');} else{ echo('none');} ?> ">
		<div id="confirmationEmailCloseMarkSvg"></div>
		
		<div id="confirmationEmailSvg"></div>
		<h1 class="dialogText" style="font-size: 1.3em">THANKS FOR SIGNING UP!</h1>

		
		<p class="dialogText" style="font-size: 0.9em">We have sent you and email confirmation. Check your inbox and confirm your email. Then you'll be redirected to HR Parser domain to sign in.</p>

		<hr>

		<div id="resendEmailContainer"><p id="resendEmailText">If you did not get any email </p><span class="emulatedLink" id="resendEmailLink" style="font-size: 0.8">Resend email confirmation</span></div>
                
            </div> <!-- CONFIRMATION EMAIL DIALOG -->

	    <!-- Bottom vertical spacer when SignUp dialog overflows -->
	    <!-- Change the height of the bottom vertical spacer when confirmationEmail dialog is displayed
		 and scroll bar is active (mobile devices) to center dialog vertically. -->
            <div class="signUpVerticalSpacer"></div>
        </div> <!-- SIGN UP SCROLLABLE DIV -->
    </div> <!-- SIGN UP PAGE -->

    <!----------------------------- GENERAL PURPOSE PAGE DEFINITION  -------------------------------->

    <div class="generalPurposePage" id="generalPurposePage" 
         style="display:
			<?php if($h_fPDialogFormSubmitted || $h_vEDialogFormSubmitted || $h_nPDialogFormSubmitted)
			      { echo('block');} else{ echo('none');} ?>" >
	
	<!-- Div used to simply allocate the privacyTermsDialog/forgotPwdDialog and avoid that scrolling hides
             the address bar and impacts how the background(signInPage) looks like.
             This is a discovered workaround. -->
        <div class="generalPurposeScrollableDiv" id="generalPurposeScrollableDiv">

	    <!-- Top vertical spacer when privacyTermsDialog overflows -->
	    <div class="generalPurposeVerticalSpacer"></div>

	    <!-- White dialog to show terms and conditions -->
	    <div id="privacyTermsDialog">
		<div id="privacyTermsCloseMarkDiv">
                    <a href="#" id="privacyTermsCloseMark"></a> 
                </div>

		<hr>

		<div id="privacyTermsBlock">
		    <h1>Privacy Policy</h1>

		    <div id="privacyTermsDescription"> </div>

		    <button type="button" id="privacyTermsCloseButton">Close</button>

		    <hr>
		</div> <!-- PRIVACY TERMS PAGE  -->

	    </div> <!-- PRIVACY POLICY & TERMS DIALOG DIV -->

	    <div id="dialogsContainer"
		 style="display:
                        <?php if($h_fPDialogFormSubmitted || $h_vEDialogFormSubmitted || $h_nPDialogFormSubmitted)
                              { echo('block');} else{ echo('none');} ?>" >

	    <!-- Dialog to indicate the user how to recover password -->
	    <div id="forgotPwdDialog" 
                 style="display:
                                <?php if($h_fPDialogFormSubmitted && $h_fPDialogError){ 
				          echo('block');} else{ echo('none');} ?>" >
		<div id="forgotPwdDialogCloseMarkSvg"></div>
		
		<!-- Dialog header -->
		<h1 id="bodyHeaderText" style="font-size: 1.3em">Forgot Password</h1>
		
		<!-- Dialog Icon -->
		<div id="forgotPwdDialogIcon" style="background-image: url('../resources/icons/forgotPwdDialogIcon.png')"></div>

		<div id="forgotPwdDlgBodyContainer"> 
		    <p>Please Enter Your Email Address To Receive a Verification Code</p>

		    <!-- Form to capture user email to restore password -->
		    <form action="HRparser_login.php" method="POST" id="forgotPwdDialogForm">
		        <span id="forgotPwdInputSpan">Email Address</span>
			<input type="text" id="forgotPwdEmail" name="signInEmail" placeholder="Email" required/>
			<span id="forgotPwdErrorMsg"><?= $h_fPDialogError ?>  </span>

		        <!-- Submit form -->
                        <input type="submit" name="forgotPwdForm" value="Send" id="forgotPwdDialogFormSubmitBtn">
		    </form> <!-- FORGOT PWD EMAIL DIALOG FORM -->

		</div> <!-- BODY CONTAINER DIV -->

	    </div> <!-- FORGOT PASSWORD DIALOG  -->

	    <!-- Dialog to indicate the user how to recover password -->
	    <div id="verifyEmailDialog" 
                 style="display:
				<?php if(($h_fPDialogFormSubmitted && !$h_fPDialogError) || ($h_vEDialogFormSubmitted && $h_vEDialogError))
				      { echo('block');} else{ echo('none');} ?>" >

		<div id="verifyEmailDialogCloseMarkSvg"></div>
		
		<!-- Dialog header -->
		<h1 id="bodyHeaderText" style="font-size: 1.3em">Verify Your Email</h1>
		
		<!-- Dialog Icon -->
		<div id="forgotPwdDialogIcon" style="background-image: url('../resources/icons/verifyEmailDialogIcon.png')"></div>
		
		<div id="bodyContainer"> 
		    <p>Please Enter The 5 Digit Code Sent To 
                       <?php if(strlen($h_fPUserEmail) < 35){
                                 echo("$h_fPUserEmail");
			     }else{ 
				 echo(substr($h_fPUserEmail, 0, 35) . " . . .");
			     }?></p>

		    <!-- Form to capture user email to restore password -->
                    <form action="HRparser_login.php" method="POST" id="verifyEmailDialogForm">
		
    		        <div>
      		            <input type="tel" autocomplete="off" name='verificationCode0' class='code-input' autofocus="autofocus" id="verificationCode0" required/>
      		            <input type="tel" autocomplete="off" name='verificationCode1' class='code-input' required/>
      		            <input type="tel" autocomplete="off" name='verificationCode2' class='code-input' required/>
      		            <input type="tel" autocomplete="off" name='verificationCode3' class='code-input' required/>
			    <input type="tel" autocomplete="off" name='verificationCode4' class='code-input' required/>

			    <!-- Hidden input to keep track of email to verify -->
			    <input type="hidden" name="signInEmail" value="<?= $h_fPUserEmail ?>">
		        </div>
		        <span id="verifyEmailErrorMsg"><?= $h_vEDialogError ?>  </span>

		        <p><a id="resendCode" href='#'>Resend Code</a></p>

		        <!-- Submit form -->
		        <input type="submit" name="verifyEmailForm" value="Verify" id="verifyEmailDialogFormSubmitBtn">
		    </form> <!-- VERIFY EMAIL DIALOG FORM -->
   
		</div> <!-- BODY CONTAINER DIV -->

	    </div> <!-- VERIFY EMAIL DIALOG  -->

            <!-- Dialog to indicate the user how to create new password -->
	    <div id="createNewPwdDialog" 
		 style="display:
				<?php if(($h_vEDialogFormSubmitted && !$h_vEDialogError) || 
				         ($h_nPDialogFormSubmitted && SignUpUser::implodeArrayContent($h_nPErrors))){
					      echo('block');} else{ echo('none');} ?>" >
		<div id="createNewPwdDialogCloseMarkSvg"></div>
		
		<!-- Dialog header -->
		<h1 id="bodyHeaderText" style="font-size: 1.3em">Create New Password</h1>
		
		<!-- Dialog Icon -->
		<div id="forgotPwdDialogIcon" style="background-image: url('../resources/icons/verifyEmailDialogIcon.png')"></div>
		
		<div id="bodyContainer"> 
		    <p>Your New Password Must Be Different from Previously Used Password</p>

		    <!-- Form to capture user email to restore password -->
                    <form action="HRparser_login.php" method="POST" id="createNewPwdDialogForm">
		
			<div id="inputsDiv">
                            <input type="password" id="createNewPwdInput" name="signUpPwd" placeholder="New Password" required/>
			    <span class="createNewPwdErrorMsg"><?= $h_nPErrors[$sU_map['pwd']][0] ?> </span>
			    <span class="createNewPwdErrorMsg"><?= $h_nPErrors[$sU_map['pwd']][1] ?> </span>
			    <span class="createNewPwdErrorMsg"><?= $h_nPErrors[$sU_map['pwd']][2] ?></span>

			    <input type="password" id="confirmNewPwdInput" name="signUpConfPwd" placeholder="Confirm Password" required/>
			    <span class="createNewPwdErrorMsg"><?= $h_nPErrors[$sU_map['confPwd']] ?> </span>

			    <!-- Hidden input to keep track of email to verify -->
                            <input type="hidden" name="signInEmail" value="<?= $h_fPUserEmail ?>">
    		        </div>	

			<div id="changePasswordDiv">
                            <span href="" class="emulatedLink" id="changePassword">Change Password</span>
                        </div>

		        <!-- Submit form -->
                        <input type="submit" name="createNewPwdForm" value="Save" id="createNewPwdDialogFormSubmitBtn">
   		        
		    </form> <!-- CREATE NEW PASSWORD DIALOG FORM -->

		</div> <!-- BODY CONTAINER DIV -->

	    </div> <!-- CREATE NEW PASSWORD DIALOG  -->

	    
	    </div> <!-- DIALOGS CONTAINER -->

	    <!-- Bottom vertical spacer when privacyTermsDialog overflows -->
            <div class="generalPurposeVerticalSpacer"></div>

        </div> <!-- PRIVACY & TERMS SCROLLABLE DIV   -->
    </div> <!-- PRIVACY POLICY & TERMS AND CONDITIONS PAGE  -->

    <!-- Import php variables into javascript -->
    <script type="text/javascript">
	var h_sUDialogFormSubmitted = "<?= $h_sUDialogFormSubmitted ?>";
	var h_sUErrors = "<?= $h_sUValidationInvSts ?>";

        var h_fPDialogFormSubmitted = "<?= $h_fPDialogFormSubmitted ?>";
	var h_fPDialogError = "<?= $h_fPDialogError ?>";

	var h_vEDialogFormSubmitted = "<?= $h_vEDialogFormSubmitted ?>";
	var h_vEDialogError = "<?= $h_vEDialogError ?>";

	var h_nPDialogFormSubmitted = "<?= $h_nPDialogFormSubmitted ?>";
	var h_nPErrors = "<?= SignUpUser::implodeArrayContent($h_nPErrors); ?>";
    </script>

    <!-- Web site signIn/signUp javascript utilities -->
    <script type="text/javascript" src="HRparser_login.js"></script>
    
</body>
</html>
