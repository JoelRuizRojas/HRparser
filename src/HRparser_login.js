    
    /********************************************************************/
    /********************************************************************/
    /****************** HTML ELEMENTS ID REFERENCES *********************/
    /********************************************************************/
    /********************************************************************/
 
    /***** SignUp Page references *****/
    
    var signUpPage = document.getElementById("signUpPage");
    var signUpVerticalSpacer = document.getElementsByClassName("signUpVerticalSpacer");
    var signUpDialog = document.getElementById("signUpDialog");
    var signUpFormDiv = document.getElementById("signUpFormDiv");
    var signInPageLink = document.getElementById("signInLink");

    var signUpTermsPageLink = document.getElementById("signUpTermsLink");
    var signUpDialogCloseMarkSvg = document.getElementById("signUpDialogCloseMarkSvg");

    var privacyTermsDialog = document.getElementById("privacyTermsDialog");
    var privacyTermsBlock = document.getElementById("privacyTermsBlock");
    var privacyTermsCloseMark = document.getElementById("privacyTermsCloseMark");
    var privacyTermsCloseBtn = document.getElementById("privacyTermsCloseButton");
    
    /***** End SignUp Page references *****/

    /***** SignIn Page references *****/ 

    var signInPage = document.getElementById("signInPage");
    var forgotPwdLink = document.getElementById("forgotPwdLink");
    var signUpPageLink = document.getElementById("signUpLink");

    /***** End SignIn Page references *****/ 

    /***** General Purpose Page references *****/

    var generalPurposePage = document.getElementById("generalPurposePage");
    var generalPurposeScrollableDiv = document.getElementById("generalPurposeScrollableDiv");
    var generalPurposeVerticalSpacer = document.getElementsByClassName("generalPurposeVerticalSpacer");
    var generalPurposeDialogsContainer = document.getElementById("dialogsContainer");

    var confirmationEmailDialog = document.getElementById("confirmationEmailDialog");
    var confirmationEmailCloseMark = document.getElementById("confirmationEmailCloseMarkSvg");

    var forgotPwdDialog = document.getElementById("forgotPwdDialog");
    var forgotPwdDialogCloseMarkSvg = document.getElementById("forgotPwdDialogCloseMarkSvg");
    var forgotPwdDlgBodyContainer = document.getElementById("forgotPwdDlgBodyContainer");

    var verifyEmailDialog = document.getElementById("verifyEmailDialog");
    var verifyEmailDialogCloseMarkSvg = document.getElementById("verifyEmailDialogCloseMarkSvg");

    var createNewPwdDialog = document.getElementById("createNewPwdDialog");
    var createNewPwdDialogCloseMarkSvg = document.getElementById("createNewPwdDialogCloseMarkSvg");
    var changePassword = document.getElementById("changePassword");

    /***** End General Purpose Page references *****/

    /********************************************************************/
    /********************************************************************/
    /*********************** GENERAL UTILITIES **************************/
    /********************************************************************/
    /********************************************************************/ 

    /**
     * Checks if we are on mobile portrait mode
     *
     * @param none
     * @return true/false flag to determine if we are in mobile portrait mode or not
     * */
    function onMobilePortraitMode(){
	// Get Css root variable to determine if we are on Mobile portrait mode or not
        return Number(getComputedStyle(document.documentElement).getPropertyValue('--onMobilePortraitMode'));
    }

    /**
     * Centers the dialog horizontally
     *
     * @param dialogById. Dialog to center
     * @return none
     * */
    function centerDialog(dialogById){
	// Css root scope to define css variables
        var r = document.querySelector(':root');
        r.style.setProperty("--dialogWidth", dialogById.offsetWidth.toString() + "px");

	dialogById.className = "standByState";
    }

    /**
     * Adjust the upper/bottom vertical spacers height based on the
     * current dialog being displayed.
     *
     * @param dialogById. Id of the current dialog
     * @param verticalSpacersArrayByClass. Input array with the vertical spacers elements
     * @return none
     */
    function adjustVerticalSpacersWithCurrentDialog(dialogById, verticalSpacersArrayByClass){
        for(var i = 0; i < verticalSpacersArrayByClass.length; i++){
            verticalSpacersArrayByClass[i].style.setProperty("height",
                  "calc(((100vh - " + dialogById.offsetHeight + "px) / 2) - 0px)");
        }
    }

    /**
     * Applies a fade-in effect to dialog as follows:
     *    - Fade-in dialog outline definition (only opacity transition)
     *    - Fade-in dialog inner block (opacity + left displacement)
     *
     * Contraints:
     *    - Dialog must be horizontally centered on window
     *    - Dialog display css property must be block
     *    - Dialog position css property must be relative (movement of inner block is relative to parent)
     *    - Dialog position absolute defined before effects leads to unexpected behavior
     *      if this attribute is necessary, define it after this function finishes execution.
     *
     * @param fadeInDlgById. Id of the dialog to fade in
     * @param dlgInnerBlock. Id of the dialog inner div that contains most of the dialog content
     * @param fadeInDlgTransTime. Time in ms to fade-in dialog outline definition
     * @param dlgInnerBlockTransTime. Time in ms to fade-in dialog inner block
     * @return none
     */
    function fancyFadeInDialog(fadeInDlgById, dlgInnerBlock, fadeInDlgTransTime, dlgInnerBlockTransTime){
        // Before fadingIn given dialog, remove all classes
        fadeInDlgById.className = "";
	dlgInnerBlock.className = "";
	
	// Make sure dialog to fade-in is displayed
	fadeInDlgById.style.display = "block";
	
	// Css root scope to define css variables
	var r = document.querySelector(':root');
	r.style.setProperty("--opacityTransTime", fadeInDlgTransTime.toString().trim() + "ms");
	r.style.setProperty("--fadeInTransTime", dlgInnerBlockTransTime.toString().trim() + "ms");
	r.style.setProperty("--dialogWidth", fadeInDlgById.offsetWidth.toString() + "px");

	// Make sure dialogs starts off from center
	fadeInDlgById.className += " standByState";

	// Fade in dialog outline definition
        fadeInDlgById.className += " fadeableInOnPlace";
        window.setTimeout( function(){
            fadeInDlgById.className += " fadeInOnPlace";
        }, 50);

        /* Fade in inner block of dialog
	 * To make the inner block independent from its parent lets 
	 * momentarily position it in an absolute way for transition */
	dlgInnerBlock.style.setProperty("position", "absolute");
	dlgInnerBlock.className += " fadeableInToLeft_10vw";
        window.setTimeout( function(){
            dlgInnerBlock.className += " fadeIn";
        }, 50);

	/* Remove classes attributes and absolute position from
	 * the dlgInnerBlock */
	window.setTimeout( function(){
            dlgInnerBlock.style.position = "";
	    fadeInDlgById.className = "standByState";
	    dlgInnerBlock.className = "";
        }, Math.max(fadeInDlgTransTime, dlgInnerBlockTransTime) + 100);
    }

    /**
     * Applies a fade-in + scale up effect to dialog
     *
     * Contraints:
     *    - Transition starts horizontally centered on window
     *    - Dialog display css property must be block
     *    - Dialog position absolute defined before effects leads to unexpected behavior
     *      if this attribute is necessary, define it after this function finishes execution.
     *
     * @param fadeInDlgById. Id of the dialog to fade in
     * @param transitionTime. Time in ms to fade-in dialog
     * @return none
     */
    function fadeInAndScaleDialog(fadeInDlgById, transitionTime){
	// Before fadingIn given dialog, remove all classes
        fadeInDlgById.className = "";

	// Make sure dialog to fade-in is displayed
        fadeInDlgById.style.display = "block";
	
	// Css root scope to define css variables
        var r = document.querySelector(':root');
        r.style.setProperty("--fadeInTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--dialogWidth", fadeInDlgById.offsetWidth.toString() + "px");
	
	/* Make dialog position property to absolute in case there is
	 * a dialog still fading-out */
	fadeInDlgById.style.setProperty("position", "absolute");
	fadeInDlgById.className = "standByState";

	// Fade in dialog
        fadeInDlgById.className += " fadeableInToTopAndScale_10vh";
        window.setTimeout( function(){
            fadeInDlgById.className += " fadeInToTopAndScale";
        }, 50);

	/* Remove classes attributes and absolute position from
         * the dlgInnerBlock */
        window.setTimeout( function(){
	    fadeInDlgById.style.setProperty("position", "");
            fadeInDlgById.className = "standByState";
        }, transitionTime + 100);
    }

    /**
     * Applies a fade-out + scale down effect to dialog
     *
     * Contraints:
     *    - Transition starts horizontally centered on window
     *    - Dialog display css property must be block
     *    - Dialog position absolute defined before effects leads to unexpected behavior
     *      if this attribute is necessary, define it after this function finishes execution.
     *
     * @param fadeOutDlgById. Id of the dialog to fade out
     * @param transitionTime. Time in ms to fade-out dialog
     * @return none
     */
    function fadeOutAndScaleDialog(fadeOutDlgById, transitionTime){
	// Before fading-out given dialog, remove all classes
        fadeOutDlgById.className = "";

	// Make sure dialog to fade-out is displayed
        fadeOutDlgById.style.display = "block";
	
	// Css root scope to define css variables
        var r = document.querySelector(':root');
        r.style.setProperty("--fadeOutTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--dialogWidth", fadeOutDlgById.offsetWidth.toString() + "px");
	r.style.setProperty("--dialogHeight", fadeOutDlgById.offsetHeight.toString() + "px");
	
	/* Make dialog position property to absolute in case there is
	 * a dialog still fading-out */
	fadeOutDlgById.style.setProperty("position", "absolute");
	fadeOutDlgById.className = "standByState";

	// Fade in dialog
        fadeOutDlgById.className += " fadeableOutToTopAndScale";
        window.setTimeout( function(){
            fadeOutDlgById.className += " fadeOutToTopAndScale_50vh";
        }, 50);

	/* Remove classes attributes and absolute position from
         * the dialog */
        window.setTimeout( function(){
	    fadeOutDlgById.className = "";
            fadeOutDlgById.style.position = "";
            fadeOutDlgById.style.display = "none";
        }, transitionTime + 100);
    }

    /**
     * Applies a fade-out + scale down effect to dialog
     *
     * Contraints:
     *    - Dialog must be horizontally centered on window
     *    - Dialog display css property must be block
     *    - Dialog position absolute defined before effects leads to unexpected behavior
     *      if this attribute is necessary, define it after this function finishes execution.
     *
     * @param fadeOutDlgById. Id of the dialog to fade out
     * @param transitionTime. Time in ms to fade-out dialog
     * @return none
     */
    function fadeOutDialogOnPlace(fadeOutDlgById, transitionTime){
	// Make sure dialog to fade-out is displayed
        fadeOutDlgById.style.display = "block";
	
	// Css root scope to define css variables
        var r = document.querySelector(':root');
        r.style.setProperty("--fadeOutTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--dialogWidth", fadeOutDlgById.offsetWidth.toString() + "px");
	r.style.setProperty("--dialogHeight", fadeOutDlgById.offsetHeight.toString() + "px");
	
	/* Make dialog position property to absolute in case there is
	 * a dialog still fading-out */
	fadeOutDlgById.style.setProperty("position", "absolute");

	// Fade in dialog
        fadeOutDlgById.className += " fadeableOut";
        window.setTimeout( function(){
            fadeOutDlgById.className += " fadeOutOnPlace";
        }, 50);

	/* Remove classes attributes and absolute position from
         * the dialog */
        window.setTimeout( function(){
	    fadeOutDlgById.className = "";
            fadeOutDlgById.style.position = "";
            fadeOutDlgById.style.display = "none";
        }, transitionTime + 100);
    }


    /********************************************************************/
    /********************************************************************/
    /************************** SIGN IN PAGE ****************************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Shows the Forgot Password Dialog
     *
     * @param none
     * @return none
     */
    forgotPwdLink.onclick = function(){	
	// Show first the General Purpose Page and then dialog
        generalPurposePage.style.display = "block";
	generalPurposeDialogsContainer.style.display = "block";
        forgotPwdDialog.style.display = "block";

	/* Adjust the vertical spacers for current Forgot 
	 * Password dialog displayed */
	adjustVerticalSpacersWithCurrentDialog(forgotPwdDialog, generalPurposeVerticalSpacer); 

	// Hide SingIn page when displaying on mobile
        if(onMobilePortraitMode()){
            signInPage.style.display = "none";

	    // Remove class that centers dialog
            forgotPwdDialog.className = "";
	}

	// Fade-in forgotPwd dialog
        fancyFadeInDialog(forgotPwdDialog, forgotPwdDlgBodyContainer, 200, 250);
    }

    /**
     * Shows the SignUp Page Dialog
     *
     * @param none
     * @return none
     */
    signUpPageLink.onclick = function(){
	// Show signUp page and dialog
        signUpPage.style.display = "block";
        signUpDialog.style.display = "block";

	/* Adjust the vertical spacers for current SignUp  
         * dialog displayed */
        adjustVerticalSpacersWithCurrentDialog(signUpDialog, signUpVerticalSpacer);

	// Hide SingIn page when displaying on mobile
	if(onMobilePortraitMode())
	    signInPage.style.display = "none";
	
	// Fade in Sign Up dialog
	fancyFadeInDialog(signUpDialog, signUpFormDiv, 200, 250);
    }

    /********************************************************************/
    /********************************************************************/
    /************************** SIGN UP PAGE ****************************/
    /********************************************************************/
    /********************************************************************/ 

    /***** Inner elements cfg *****/

    $(document).ready(function() {
        // Change select font color by default(no action)
        if($('#selectCountry').val() != 'null'){
            $('#selectCountry').css('color','black');
        }
        else{
            $('#selectCountry').css('color','gray');
        }

        // Change select font color after an option is selected
        $('#selectCountry').change(function() {
            var current = $('#selectCountry').val();
            if (current != 'null') {
                $('#selectCountry').css('color','black');
            }else{
                $('#selectCountry').css('color','gray');
            }
        });

    });
    
    /***** End Inner elements cfg *****/

    /* SignUp Dialog form submitted and no errors in submitted form.
     * Then hide the current dialog */
    if(h_sUDialogFormSubmitted && !h_sUErrors){
        // Show next dialog
        showNextGeneralPurposeDialog(300, signUpDialog);
    }

    /* SignUp Dialog form submitted and errors are present. 
     * Set the signUpDialog class to standByState so that it is shown at
     * the center of screen */
    if(h_sUDialogFormSubmitted && h_sUErrors){
	signUpDialog.style.display= "block";
        centerDialog(signUpDialog);
    }

    // Default actions when dialogs forms are submitted
    if(h_sUDialogFormSubmitted && !onMobilePortraitMode()){
        /* When page is re-served due to a dialog submit, by default the SignInPage
         * is hidden(to avoid the flickering effect). It is only when the General 
         * Purpose page and the dialog is active when the SignInPage is shown in
         * the background again */
        signInPage.style.display = "block";

        /* Adjust vertical upper/bottom spacers when page is re-served due to a 
         * dialog been submitted */
        adjustVerticalSpacersWithCurrentDialog(signUpDialog, signUpVerticalSpacer);
    }

    /**
     * Shows the Privacy Policy & Terms Dialog
     *
     * @param none
     * @return none
     */
    signUpTermsPageLink.onclick = function(){
	/* Show General Purpose Page and dialog and hide
	 * the signUpPage */
        generalPurposePage.style.display = "block";
        privacyTermsDialog.style.display = "block";
        signUpPage.style.display = "none";

	/* By now the SignIn Page must be hidden if we are on mobile portrait
	 * mode, but just in case */
	if(onMobilePortraitMode())
	    signInPage.style.display = "none";
	
	// Fade-in dialog
	fancyFadeInDialog(privacyTermsDialog, privacyTermsBlock, 200, 250);
    }

    /**
     * Shows the SignIn Page Dialog
     *
     * @param none
     * @return none
     */
    signInPageLink.onclick = function(){
        // Fade out current dialog 
	showPreviousGeneralPurposeDialog(300, signUpDialog);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the general purpose page too */
        window.setTimeout( function(){
            signUpPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = "http://localhost:8092/HRparser/src/HRparser_login.php";
        }, 450);
    }

    /**
     * Closes the SignUp dialog and reloads the SignInPage
     *
     * @param none
     * @return none
     */
    signUpDialogCloseMarkSvg.onclick = function(){
        // Fade out current dialog 
        showPreviousGeneralPurposeDialog(300, signUpDialog);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the general purpose page too */
        window.setTimeout( function(){
	    signUpPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = "http://localhost:8092/HRparser/src/HRparser_login.php";
        }, 450);
    }

    /********************************************************************/
    /********************************************************************/
    /*********************** GENERAL PURPOSE PAGE ***********************/
    /********************************************************************/
    /********************************************************************/ 
 
    /***** General purpose dialog transition effect *****/

    /**
     * Shows the next general purpose dialog
     * Only applies for any of the next dialogs:
     *     - ForgotPasswordDialog
     *     - VerifyEmailDialog
     *     - CreateNewPwdDialog
     *
     * Constraints:
     *     - Dialogs transition occur around center of window
     *     - Dialogs display property must be block
     *
     * @param transitionTime. Time in ms that takes the transition
     * @param fadeOutDlgById. Id of the dialog to fade out
     * @param fadeInDlgById. Id of the dialog to fade in
     * @return none
     */
    function showNextGeneralPurposeDialog(transitionTime, fadeOutDlgById, fadeInDlgById){
	// Before fadingOut/fadingIn current/next dialogs remove all classes
        fadeOutDlgById.className = "";
        if(fadeInDlgById !== undefined)
            fadeInDlgById.className = "";

        /* Make sure dialog display property is set (they might be not display 
	 * when page is re-served). Since dialogs "share" space during transition
	 * we need to set the position property to absolute */
        fadeOutDlgById.style.display = "block";
	fadeOutDlgById.style.position = "absolute";
        if(fadeInDlgById !== undefined){
            fadeInDlgById.style.display = "block";
	    fadeInDlgById.style.position = "absolute";}

	// Css root scope to define css variables
        var r = document.querySelector(':root');
	r.style.setProperty("--fadeOutTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--fadeInTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--dialogWidth", fadeOutDlgById.offsetWidth.toString() + "px");

	// Make sure dialog to fade-out starts off from center
        fadeOutDlgById.className += " standByState";
	if(fadeInDlgById !== undefined)
	    fadeInDlgById.className += " standByState";

        // Fade out current dialog
        fadeOutDlgById.className += " fadeableOut";
        window.setTimeout( function(){
            fadeOutDlgById.className += " fadeOutLeft_20vw";
        }, 50);

        // Fade in next dialog
        if(fadeInDlgById !== undefined){
            // Set opacity to 0 so that when timer expires the fadeIn effect is started
            fadeInDlgById.className += " fadeableInToLeft_20vw";
            window.setTimeout( function(){
                fadeInDlgById.className += " fadeIn";
            }, 50);
        }

	/* Remove classes attributes and absolute position from
         * the dialogs */
        window.setTimeout( function(){
            fadeOutDlgById.className = "";
	    fadeOutDlgById.style.position = "";
	    fadeOutDlgById.style.display = "none";

	    if(fadeInDlgById !== undefined){
                fadeInDlgById.className = "standByState";
		fadeInDlgById.style.position = "";}
        }, transitionTime + 100);
    }

    /**
     * Shows the previous general purpose dialog
     * Only applies for any of the next dialogs:
     *     - ForgotPasswordDialog
     *     - VerifyEmailDialog
     *     - CreateNewPwdDialog
     * 
     * Constraints:
     *     - Dialogs transition occur around center of window
     *     - Dialogs display property must be block
     *
     * @param transitionTime. Time in ms that takes the transition
     * @param fadeOutDlgById. Id of the dialog to fade out
     * @param fadeInDlgById. Id of the dialog to fade in
     * @return none
     */
    function showPreviousGeneralPurposeDialog(transitionTime, fadeOutDlgById, fadeInDlgById){
	// Before fadingOut/fadingIn current/previous dialogs remove all classes
        fadeOutDlgById.className = "";
        if(fadeInDlgById !== undefined)
            fadeInDlgById.className = "";

	/* Make sure dialog display property is set (they might be not display 
         * when page is re-served). Since dialogs "share" space during transition
         * we need to set the position property to absolute */
        fadeOutDlgById.style.display = "block";
        fadeOutDlgById.style.position = "absolute";
        if(fadeInDlgById !== undefined){
            fadeInDlgById.style.display = "block";
            fadeInDlgById.style.position = "absolute";}

	// Css root scope to define css variables
        var r = document.querySelector(':root');
        r.style.setProperty("--fadeOutTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--fadeInTransTime", transitionTime.toString().trim() + "ms");
        r.style.setProperty("--dialogWidth", fadeOutDlgById.offsetWidth.toString() + "px");
	
        // Make sure dialog to fade-out starts off from center
        fadeOutDlgById.className += " standByState";
	if(fadeInDlgById !== undefined)
            fadeInDlgById.className += " standByState";

        // Fade out current dialog
        fadeOutDlgById.className += " fadeableOut";
        window.setTimeout( function(){
            fadeOutDlgById.className += " fadeOutRight_20vw";
        }, 50);

	// Fade in next previous
        if(fadeInDlgById !== undefined){
            // Set opacity to 0 so that when timer expires the fadeIn effect is started
            fadeInDlgById.className += " fadeableInToRight_20vw";
            window.setTimeout( function(){
                fadeInDlgById.className += " fadeIn";
            }, 50);
        }

        /* Remove classes attributes and absolute position from
         * the dialogs */
        window.setTimeout( function(){
            fadeOutDlgById.className = "";
            fadeOutDlgById.style.position = "";
            fadeOutDlgById.style.display = "none";

            if(fadeInDlgById !== undefined){
                fadeInDlgById.className = "standByState";
                fadeInDlgById.style.position = "";}
        }, transitionTime + 100);
    }

    /***** End General purpose dialog transition effect *****/


    /********************************************************************/
    /********************************************************************/
    /***************** PRIVACY POLICY & TERMS DIALOG ********************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Closes the PrivacyPolicy&Terms Dialog by the 
     * right-upper close mark button.
     *
     * @param none
     * @return none
     */
    privacyTermsCloseMark.onclick = function(){
        // Fade-out current dialog
	centerDialog(privacyTermsDialog);
        fadeOutDialogOnPlace(privacyTermsDialog, 250);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        window.setTimeout( function(){
            // Important: Scroll to top before hiding generalPurposePage
            generalPurposeScrollableDiv.scrollTop = 0;
            generalPurposePage.style.display = "none";

            // Show signUp page back again
            signUpPage.style.display = "block";
	    centerDialog(signUpDialog);
        }, 450);    
    }

    /**
     * Closes the PrivacyPolicy&Terms Dialog by the 
     * bottom close button.
     *
     * @param none
     * @return none
     */
    privacyTermsCloseBtn.onclick = function(){
        // Fade-out current dialog
	centerDialog(privacyTermsDialog);
        fadeOutDialogOnPlace(privacyTermsDialog, 250);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        window.setTimeout( function(){
	    // Important: Scroll to top before hiding generalPurposePage
            generalPurposeScrollableDiv.scrollTop = 0;
            generalPurposePage.style.display = "none";

	    // Show signUp page back again
            signUpPage.style.display = "block";
        }, 450);
    }


    /********************************************************************/
    /********************************************************************/
    /******************** CONFIRMATION EMAIL DIALOG *********************/
    /********************************************************************/
    /********************************************************************/

    /* SignUp Dialog form submitted and no errors in submitted form.
     * Then show confirmationEmail dialog */
    if(h_sUDialogFormSubmitted && !h_sUErrors){
        /* Show next dialog, wait until fade-out transition of
	 * signUp dialog finishes to start this one */
	window.setTimeout( function(){ 
            fadeInAndScaleDialog(confirmationEmailDialog, 400);
	    adjustVerticalSpacersWithCurrentDialog(confirmationEmailDialog, signUpVerticalSpacer);
	}, 550);
    }

    /**
     * Closes the Confirmation Email Dialog.
     *
     * @param none
     * @return none
     */
    confirmationEmailCloseMark.onclick = function(){
	// Fade-out current dialog
	fadeOutAndScaleDialog(confirmationEmailDialog, 250);

	/* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        window.setTimeout( function(){
            signUpPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = "http://localhost:8092/HRparser/src/HRparser_login.php";
        }, 450);
    }


    /********************************************************************/
    /********************************************************************/
    /********************* FORGOT PASSWORD DIALOG ***********************/
    /********************************************************************/
    /********************************************************************/

    /* Forgot Pwd Dialog form submitted and no errors in submitted form.
     * Then hide/show the forgotPwdDialog and verifyEmailDialog, respectively */
    if(h_fPDialogFormSubmitted && !h_fPDialogError){
	// Show next dialog
        showNextGeneralPurposeDialog(300, forgotPwdDialog, verifyEmailDialog);
    }
          
    /* Forgot Pwd Dialog form submitted and errors are present. 
     * Set the forgotPwdDialog class to standByState so that it is shown at
     * the center of screen */
    if(h_fPDialogFormSubmitted && h_fPDialogError)
	centerDialog(forgotPwdDialog);

    // Default actions when dialogs forms are submitted
    if(h_fPDialogFormSubmitted){
	if(!onMobilePortraitMode()){
            /* When page is re-served due to a dialog submit, by default the SignInPage
             * is hidden(to avoid the flickering effect). It is only when the General 
             * Purpose page and the dialog is active when the SignInPage is shown in
             * the background again */
            signInPage.style.display = "block";
	}

        /* Adjust vertical upper/bottom spacers when page is re-served due to a 
         * dialog been submitted */
        adjustVerticalSpacersWithCurrentDialog(forgotPwdDialog, generalPurposeVerticalSpacer);
    }

    /**
     * Closes the Forgot Password Dialog
     *
     * @param none
     * @return none
     */
    forgotPwdDialogCloseMarkSvg.onclick = function(){
        // Fade out current dialog 
        showPreviousGeneralPurposeDialog(300, forgotPwdDialog);

        /* Since the dialog will be eventually closed after effect,
	 * we need to close also the general purpose page too */
        window.setTimeout( function(){
            generalPurposePage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = "http://localhost:8092/HRparser/src/HRparser_login.php";
        }, 450);
    }

    /********************************************************************/
    /********************************************************************/
    /*********************** VERIFY EMAIL DIALOG ************************/
    /********************************************************************/
    /********************************************************************/
    
    /* Verify Email Dialog form submitted and no errors in submitted form.
     * Then hide/show the verifyEmailDialog and createNewPwdDialog, respectively */
    if(h_vEDialogFormSubmitted && !h_vEDialogError){
	// Show next dialog
        showNextGeneralPurposeDialog(300, verifyEmailDialog, createNewPwdDialog);
    }

    /* Verify Email Dialog form submitted and errors are present. 
     * Set the verifyEmailDialog class to standByState so that it is shown at
     * the center of screen */
    if(h_vEDialogFormSubmitted && h_vEDialogError)
	centerDialog(verifyEmailDialog);

    // Default actions when dialogs forms are submitted
    if(h_vEDialogFormSubmitted){
	if(!onMobilePortraitMode()){
            /* When page is re-served due to a dialog submit, by default the SignInPage
             * is hidden(to avoid the flickering effect). It is only when the General 
             * Purpose page and the dialog is active when the SignInPage is shown in
             * the background again */
            signInPage.style.display = "block";
	}

        /* Adjust vertical upper/bottom spacers when page is re-served due to a 
         * dialog been submitted */
        adjustVerticalSpacersWithCurrentDialog(verifyEmailDialog, generalPurposeVerticalSpacer);
    }

    /**
     * Closes the Verify Email Dialog
     *
     * @param none
     * @return none
     */
    verifyEmailDialogCloseMarkSvg.onclick = function(){	
        // Fade out current dialog / fade in previous dialog
        showPreviousGeneralPurposeDialog(300, verifyEmailDialog, forgotPwdDialog);	
    }

    /***** Verify Code Inputs *****/
    
    const codeInputs = [...document.querySelectorAll('input.code-input')]

    codeInputs.forEach((ele,index)=>{

	// When focus on codeInput delete its current content
	ele.addEventListener('focus', (e)=>{
            codeInputs[index].value = '';
	})

	/* When keydown detected and it corresponds to backspace key, 
	 * go to previous codeInput */
	ele.addEventListener('keydown',(e)=>{
            if(e.keyCode === 8 && e.target.value==='') 
                codeInputs[Math.max(0,index-1)].focus();
	})

	/* When codeInput content is changed, check that corresponds to
	 * a number and move to next codeInput */
	ele.addEventListener('input',(e)=>{
            // take the first character of the input
	    // this actually breaks if you input an emoji like 👨<200d>👩<200d>👧<200d>👦....
	    // but I'm willing to overlook insane security code practices.
            const [first,...rest] = e.target.value;

            /* First will be undefined when backspace was entered, 
	     * so set the input to "" */
            e.target.value = first ?? '';
            const lastInputBox = index===codeInputs.length - 1;
            const didInsertContent = first!==undefined;
	
            // Only proceed if an actual key was inserted
            if(didInsertContent) {
		    
                // Only numbers allowed	    
                if((e.target.value.charCodeAt(0) < 48) || 
                   (e.target.value.charCodeAt(0) > 57)){
                    codeInputs[index].value = '';
                }
                else{
                    // Last codeInput handle
                    if(!lastInputBox){	
                        codeInputs[index+1].focus();
                        codeInputs[index+1].value = '';
                    }
                }
            }
        })
    })

    /********************************************************************/
    /********************************************************************/
    /********************** CREATE NEW PWD DIALOG ***********************/
    /********************************************************************/
    /********************************************************************/

    /* Create New Pwd Dialog form submitted and no errors in submitted form.
     * Then close the createNewPwdDialog */
    if(h_nPDialogFormSubmitted && !h_nPErrors){
	// Move on from current dialog
        showNextGeneralPurposeDialog(300, createNewPwdDialog);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the general purpose page too */
        window.setTimeout( function(){
            generalPurposePage.style.display = "none";
            
	    // Redirect to same web-page to get clean php page
            window.location.href = "http://localhost:8092/HRparser/src/HRparser_login.php";
        }, 450);
    }

    /* Create New Password Dialog form submitted and errors are present. 
     * Set the createNewPwdDialog class to standByState so that it is shown at
     * the center of screen */
    if(h_nPDialogFormSubmitted && h_nPErrors)
	centerDialog(createNewPwdDialog);

    // Default actions when dialogs forms are submitted
    if(h_nPDialogFormSubmitted){
	if(!onMobilePortraitMode()){
            /* When page is re-served due to a dialog submit, by default the SignInPage
             * is hidden(to avoid the flickering effect). It is only when the General 
             * Purpose page and the dialog is active when the SignInPage is shown in
             * the background again */
            signInPage.style.display = "block";
	}

        /* Adjust vertical upper/bottom spacers when page is re-served due to a 
         * dialog been submitted */
        adjustVerticalSpacersWithCurrentDialog(createNewPwdDialog, generalPurposeVerticalSpacer); 
    }

    /**
     * Clears the inputs for the new password being created
     *
     * @param none
     * @return none
     */
    changePassword.onclick = function(){
	document.getElementById("createNewPwdInput").value = "";
	document.getElementById("confirmNewPwdInput").value = "";
    }

    /**
     * Closes the Create New Password Dialog
     *
     * @param none
     * @return none
     */
    createNewPwdDialogCloseMarkSvg.onclick = function(){
        // Fade out current dialog / fade in previous dialog
        showPreviousGeneralPurposeDialog(300, createNewPwdDialog, verifyEmailDialog);

	// To recover the autofocus on first code input
	document.getElementById("verificationCode0").focus();
    }

    /***** End Verify Code Inputs *****/


    /********************************************************************/
    /********************************************************************/
    /*********************** MOBILE DEVICES CFGS ************************/
    /********************************************************************/
    /********************************************************************/

    /* Initial conditions */
    {	
	/* Behavior when window decreases and reaches the responsive
         * design threshold. Perhaps all of these or some are redundant
	 * instructions (from above) but just in case */
        if(onMobilePortraitMode()){
            /* Hide SignIn Page if any of the dialogs below is displayed */
            if(signUpDialog.style.display == "block")
                signInPage.style.display = "none";

	    if(confirmationEmailDialog.style.display == "block"){
                signInPage.style.display = "none";

                // Remove class that centers dialog
               // confirmationEmailDialog.className = "";
            }

	    if(privacyTermsDialog.style.display == "block")
                signInPage.style.display = "none";

            if(forgotPwdDialog.style.display == "block"){
                signInPage.style.display = "none";

                /* If class is removed here it impacts the transition
                 * of this dialog. Do not remove the current class */
                //forgotPwdDialog.className = "";
            }

	    if(verifyEmailDialog.style.display == "block"){
                signInPage.style.display = "none";

                /* If class is removed here it impacts the transition
		 * of this dialog. Do not remove the current class */
                //verifyEmailDialog.className = "";
            }

	    if(createNewPwdDialog.style.display == "block"){
                signInPage.style.display = "none";

                /* If class is removed here it impacts the transition
                 * of this dialog. Do not remove the current class */
                //createNewPwdDialog.className = "";
            }
        } 
    } 

    /* Window resize event 
     * SMARTPHONE CONDITIONS to remove signInPage while signing up (signUp page covers 100%)
     * Since we need 2 conditions we can not do it with media query */
    $(window).resize(function(){
        // Get Css root variable to determine if we are on Mobile portrait mode or not
        var onMobilePortraitMode = Number(getComputedStyle(document.documentElement).getPropertyValue('--onMobilePortraitMode'));

	/* Behavior when window decreases and reaches the responsive
         * design threshold*/
        if(onMobilePortraitMode){
	    /* Hide SignIn Page if any of the dialogs below is displayed */
            if(signUpDialog.style.display == "block")
                signInPage.style.display = "none";
	
	    if(confirmationEmailDialog.style.display == "block"){
                signInPage.style.display = "none";

		// Remove class that centers dialog
                confirmationEmailDialog.className = "";
	    }
	
	    if(privacyTermsDialog.style.display == "block")
                signInPage.style.display = "none";

            if(forgotPwdDialog.style.display == "block"){
                signInPage.style.display = "none";

		// Remove class that centers dialog
		forgotPwdDialog.className = "";
	    }

	    if(verifyEmailDialog.style.display == "block"){
                signInPage.style.display = "none";

                // Remove class that centers dialog
                verifyEmailDialog.className = "";
            }

	    if(createNewPwdDialog.style.display == "block"){
                signInPage.style.display = "none";

                // Remove class that centers dialog
                createNewPwdDialog.className = "";
            }
	}

	/* Behavior when window increases and exceeds the responsive
	 * design threshold*/
        if(!onMobilePortraitMode){
	    /* Show SignInPage back again (background) */
            signInPage.style.display = "block";

	    /* Center dialogs since below responsive design
	     * threshold these dialogs are not centered */
	    if(signUpDialog.style.display == "block")
	        centerDialog(signUpDialog);

	    if(confirmationEmailDialog.style.display == "block")
		centerDialog(confirmationEmailDialog);

	    if(privacyTermsDialog.style.display == "block")
		centerDialog(privacyTermsDialog);

	    if(forgotPwdDialog.style.display == "block")
		centerDialog(forgotPwdDialog);

	    if(verifyEmailDialog.style.display == "block")
                centerDialog(verifyEmailDialog);

	    if(createNewPwdDialog.style.display == "block")
                centerDialog(createNewPwdDialog);
        }
    });

    /***** End SignIn Page Cfgs *****/
 
