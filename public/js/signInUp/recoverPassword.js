/**
 * recoverPassword.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

    /********************************************************************/
    /********************************************************************/
    /***************** RECOVER PASSWORD PAGE UTILITIES ******************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Shows the RecoverPassword Page, forgotPassword dialog is shown by default.
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showRecoverPasswordPage(dlgFadeInTransTime){
        // Show RecoverPassword Page and then dialog
        recoverPasswordPage.style.display = "block";
        forgotPwdDialog.style.display = "block";

        /* Adjust the vertical spacers for current Forgot
         * Password dialog displayed */
        adjustVerticalSpacersWithCurrentDialog(forgotPwdDialog, recoverPasswordPageVerticalSpacer);

        // Hide SingIn page when displaying on mobile
        if(onMobilePortraitMode()){
            signInPage.style.display = "none";

            // Remove class that centers dialog
            forgotPwdDialog.className = "";
        }

        // Fade-in forgotPwd dialog
        fancyFadeInDialog(forgotPwdDialog, forgotPwdDlgBodyContainer, dlgFadeInTransTime,
                          (dlgFadeInTransTime + 50));
    }

    /**
     * Quits to Forgot Password Dialog and requests signin page
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function quitToForgotPwdDialog(dlgFadeOutTransTime){
        // Fade out current dialog 
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, forgotPwdDialog);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the recoverPassword page too */
        window.setTimeout( function(){
            recoverPasswordPage.style.display = "none";

            // Redirect to same web-page to get clean php page
            window.location.href = doc_root +  "signin";
        }, (dlgFadeOutTransTime + 150));
    }

    /**
     * Quits to Verify Email Dialog
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function quitToVerifyEmailDialog(dlgFadeOutTransTime){
        // Fade out current dialog / fade in previous dialog
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, verifyEmailDialog, forgotPwdDialog);
    }

    /**
     * Quits to CreateNewPassword Dialog and focus on first verification
     * code input from verifyEmail dialog.
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function quitToCreateNewPwdDialog(dlgFadeOutTransTime){
        // Fade out current dialog / fade in previous dialog
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, createNewPwdDialog, verifyEmailDialog);

        // To recover the autofocus on first code input
        verificationCode0.focus();
    }

    /**
     * Clears the inputs for the new password being created in
     * CreateNewPassword dialog
     *
     * @param none
     * @return none
     */
    function clearInputsOnCreateNewPasswordDialog(){
        createNewPwdInput.value = "";
        confirmNewPwdInput.value = "";
    }

    /********************************************************************/
    /********************************************************************/
    /************* RECOVER PASSWORD PAGE RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/
    
    /* Since any of the recoverPassword page dialog forms has been submitted, 
     * it means the recoverPassword page has just be loaded, lets show it */
    if(!h_fPDialogFormSubmitted && !h_vEDialogFormSubmitted && !h_nPDialogFormSubmitted){
        showRecoverPasswordPage(200);
    }

    /********************************************************************/
    /********************************************************************/
    /************ FORGOT PASSWORD DIALOG RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/

    /* Actions to be done if forgotPassword dialog form is submitted */
    if(h_fPDialogFormSubmitted){

        /* Forgot Pwd Dialog form submitted and no errors in submitted form.
         * Then hide/show the forgotPwdDialog and verifyEmailDialog, respectively */
        if(!h_fPDialogError){
            // Show next dialog
            showNextGeneralPurposeDialog(300, forgotPwdDialog, verifyEmailDialog);
        }
        else{
            /* Forgot Pwd Dialog form submitted and errors are present.
             * Set the forgotPwdDialog class to standByState so that it is shown at
             * the center of screen */
            centerDialog(forgotPwdDialog);
        }
    } 

    /********************************************************************/
    /********************************************************************/
    /*************** VERIFY EMAIL DIALOG RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/
 
    /* Actions to be done if verifyEmail dialog form is submitted */
    if(h_vEDialogFormSubmitted){

        /* Verify Email Dialog form submitted and no errors in submitted form.
         * Then hide/show the verifyEmailDialog and createNewPwdDialog, respectively */
        if(!h_vEDialogError){
            // Show next dialog
            showNextGeneralPurposeDialog(300, verifyEmailDialog, createNewPwdDialog);
        }
        else{
            /* Verify Email Dialog form submitted and errors are present.
             * Set the verifyEmailDialog class to standByState so that it is shown at
             * the center of screen */
            centerDialog(verifyEmailDialog);
        }
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
            const didInsertContent = first !== undefined;

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
    /************** CREATE NEW PWD DIALOG RENDERING BEHAVIOR ************/
    /********************************************************************/
    /********************************************************************/

    /* Actions to be done if createNewPassword dialog form is submitted */
    if(h_nPDialogFormSubmitted){
        /* Create New Pwd Dialog form submitted and no errors in submitted form.
         * Success on dialog form then close the createNewPwdDialog */
        if(!h_nPErrors){
            // Move on from current dialog
            showNextGeneralPurposeDialog(300, createNewPwdDialog);

            /* Since the dialog will be eventually closed after effect,
             * we need to close also the recoverPassword page too */
            window.setTimeout( function(){
                recoverPasswordPage.style.display = "none";

                // Redirect to same web-page to get clean php page
                window.location.href = doc_root +  "signin";
            }, 450);
        }
        else{
            /* Create New Password Dialog form submitted and errors are present. 
             * Set the createNewPwdDialog class to standByState so that it is shown at
             * the center of screen */
            centerDialog(createNewPwdDialog);
        }
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
            /* When any of the recoverPassword page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            signInPage.style.display = "none";
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            signInPage.style.display = "block";

            /* Center dialogs again */
            if(forgotPwdDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(forgotPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(forgotPwdDialog, recoverPasswordPageVerticalSpacer);
            }

            if(verifyEmailDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(verifyEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(verifyEmailDialog, recoverPasswordPageVerticalSpacer);
            }

            if(createNewPwdDialog.style.display == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(createNewPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(createNewPwdDialog, recoverPasswordPageVerticalSpacer);
            }
        }
    }
    
    /* Window resize event 
     * SMARTPHONE CONDITIONS to remove signInPage while recovering password (recoverPassword page covers 100%)
     * Since we need 2 conditions we can not do it with media query */
    $(window).resize(function(){
        // Get Css root variable to determine if we are on Mobile portrait mode or not
        var onMobilePortraitMode = Number(getComputedStyle(document.documentElement).getPropertyValue('--onMobilePortraitMode'));

        /* Behavior when window decreases and reaches the responsive
         * design threshold*/
        if(onMobilePortraitMode){
            /* When any of the recoverPassword page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            signInPage.style.display = "none";

            // Remove class that centers dialog
            forgotPwdDialog.className = "";
            verifyEmailDialog.className = "";
            createNewPwdDialog.className = "";
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            signInPage.style.display = "block";

            /* Center dialogs again */
            if(forgotPwdDialog.style.display == "block"){
                centerDialog(forgotPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(forgotPwdDialog, recoverPasswordPageVerticalSpacer);
            }

            if(verifyEmailDialog.style.display == "block"){
                centerDialog(verifyEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(verifyEmailDialog, recoverPasswordPageVerticalSpacer);
            }

            if(createNewPwdDialog.style.display == "block"){
                centerDialog(createNewPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog(createNewPwdDialog, recoverPasswordPageVerticalSpacer);
            }
        }
    });

