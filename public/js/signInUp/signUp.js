/**
 * signUp.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

    /********************************************************************/
    /********************************************************************/
    /********************* SIGN UP PAGE UTILITIES ***********************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Shows the SignUp Page, signUp dialog is shown by default.
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showSignUpPage(dlgFadeInTransTime){

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
        fancyFadeInDialog(signUpDialog, signUpFormDiv, dlgFadeInTransTime, 
                          (dlgFadeInTransTime + 50));
    }

    /**
     * Closes the SignUp Dialog, hides the SignUp page 
     * and requests the SignIn page.
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function closeSignUpDialog(dlgFadeOutTransTime){
        // Fade out current dialog 
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, signUpDialog);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        window.setTimeout( function(){
            signUpPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = doc_root +  "signin";
        }, (dlgFadeOutTransTime + 150));
    }

    /**
     * Shows the Privacy Policy & Terms Dialog
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showPrivacyPoliciesAndTermsAndConditionsDialog(dlgFadeInTransTime){
        // Hide the signUp dialog, scroll to top and show the PrivacyTerms dialog
        signUpDialog.style.display = "none";
        signUpScrollableDiv.scrollTop = 0;
 
        /* By now the SignIn Page must be hidden if we are on mobile portrait
         * mode, but just in case */
        if(onMobilePortraitMode())
            signInPage.style.display = "none";

        // Fade-in dialog
        fancyFadeInDialog(privacyTermsDialog, privacyTermsBlock, 
                          dlgFadeInTransTime, (dlgFadeInTransTime + 50));

        // Adjust vertical spacer
        adjustVerticalSpacersWithCurrentDialog(privacyTermsDialog, signUpVerticalSpacer);
    }

    /**
     * Closes the PrivacyPolicies and Terms&Conditions dialog
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function closePrivacyPoliciesAndTermsAndConditionsDialog(dlgFadeOutTransTime){
        // Fade-out current dialog
        centerDialog(privacyTermsDialog);
        fadeOutDialogOnPlace(privacyTermsDialog, dlgFadeOutTransTime);

        /* Show the signUp dialog again */
        window.setTimeout( function(){
            signUpDialog.style.display = "block";
            centerDialog(signUpDialog);

            // Adjust vertical spacer
            adjustVerticalSpacersWithCurrentDialog(signUpDialog, signUpVerticalSpacer);
        }, (dlgFadeOutTransTime + 100));
    }
 
    /**
     * Shows the Confirmation Email Dialog
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showConfirmationEmailDialog(dlgFadeInTransTime){
        /* Show ConfirmationEmail dialog. Make sure to wait until 
         * fade-out transition of signUp dialog finishes to start this one */
        window.setTimeout( function(){
            fadeInAndScaleDialog(confirmationEmailDialog, dlgFadeInTransTime);
            adjustVerticalSpacersWithCurrentDialog(confirmationEmailDialog, signUpVerticalSpacer);
        }, (dlgFadeInTransTime + 150));
    }

    /**
     * Closes the Confirmation Email Dialog, hides the SignUp page 
     * and requests the SignIn page.
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function closeConfirmationEmailDialog(dlgFadeOutTransTime){
        // Fade-out current dialog
        fadeOutAndScaleDialog(confirmationEmailDialog, dlgFadeOutTransTime);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        window.setTimeout( function(){
            signUpPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = doc_root +  "signin";
        }, (dlgFadeOutTransTime + 150));
    }
 
    /********************************************************************/
    /********************************************************************/
    /****************** SIGNUP PAGE RENDERING BEHAVIOR ******************/
    /********************************************************************/
    /********************************************************************/

    /* Style the select country drop down for cases it is not
     * possible to do with css */
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

    /* Actions to be done if signUp dialog form is submitted */
    if(h_sUDialogFormSubmitted){
	
	    /* SignUp Dialog form submitted and no errors in submitted form.
         * Then hide the current dialog */
	    if(!h_sUErrors){
	        // Fade-out signUp dialog
            showNextGeneralPurposeDialog(300, signUpDialog);

	        /* Show ConfirmationEmail dialog, wait until fade-out transition of
             * signUp dialog finishes to start this one */
            showConfirmationEmailDialog(400);
        }
	    else{
	        /* SignUp Dialog form submitted and errors are present. 
             * Set the signUpDialog class to standByState so that it is shown at
             * the center of screen */
	        signUpDialog.style.display = "block";
            centerDialog(signUpDialog);
	    }
    }
    else{
	    /* Since signUp dialog form has not been submitted it means the
	     * signUp page has just be loaded, lets show it */
	    showSignUpPage(200);
    }

    /********************************************************************/
    /********************************************************************/
    /************************* RESPONSIVE DESIGN ************************/
    /********************************************************************/
    /********************************************************************/

    /* Rendering conditions */
    {
        /* Behavior when window decreases and reaches the responsive
         * design threshold. Perhaps all of these or some are redundant
         * instructions (from above) but just in case */
        if(onMobilePortraitMode()){
            /* When any of the signUp page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            signInPage.style.display = "none";
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            signInPage.style.display = "block";

            /* Center dialogs again */
            if(signUpDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(signUpDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(signUpDialog, signUpVerticalSpacer);
            }

            if(confirmationEmailDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(confirmationEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(confirmationEmailDialog, signUpVerticalSpacer);
            }

            if(privacyTermsDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(privacyTermsDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(privacyTermsDialog, signUpVerticalSpacer);
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
            /* When any of the signUp page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            signInPage.style.display = "none";

            // Remove class that centers dialog
            confirmationEmailDialog.className = "";
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            signInPage.style.display = "block";

            /* Center dialogs again */
            if(signUpDialog.style.display == "block"){
                centerDialog(signUpDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(signUpDialog, signUpVerticalSpacer);
            }

            if(confirmationEmailDialog.style.display == "block"){
                centerDialog(confirmationEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(confirmationEmailDialog, signUpVerticalSpacer);
            }

            if(privacyTermsDialog.style.display == "block"){
                centerDialog(privacyTermsDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(privacyTermsDialog, signUpVerticalSpacer);
            }
        }
    });

