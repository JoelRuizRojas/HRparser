/**
 * resetPassword.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

var resetPasswordManager = (function(_window, _document){

    /********************************************************************/
    /********************************************************************/
    /******************* RESET PASSWORD PAGE UTILITIES ******************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Shows the ResetPassword Page, forgotPassword dialog is shown by default.
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showRecoverPasswordPage(dlgFadeInTransTime){
        // Show ResetPassword Page and then dialog
        $(this.dom.resetPasswordPage).css('display', 'block');
        $(this.dom.forgotPwdDialog).css('display', 'block');

        /* Adjust the vertical spacers for current Forgot
         * Password dialog displayed */
        adjustVerticalSpacersWithCurrentDialog($(this.dom.forgotPwdDialog), 
                                               $(this.dom.resetPasswordPageVerticalSpacer));

        // Hide SingIn page when displaying on mobile
        if(onMobilePortraitMode()){
            $(this.dom.signInPage).css('display', 'none');

            // Remove class that centers dialog
            $(this.dom.forgotPwdDialog).attr('class', '');
        }

        // Fade-in forgotPwd dialog
        fancyFadeInDialog($(this.dom.forgotPwdDialog), $(this.dom.forgotPwdDlgBodyContainer), dlgFadeInTransTime,
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
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, $(this.dom.forgotPwdDialog));

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the resetPassword page too */
        (function(_this){
            _window.setTimeout( function(){
                $(_this.dom.resetPasswordPage).css('display', 'none');

                // Redirect to same web-page to get clean php page
                _window.location.href = _this.global.doc_root +  "signin";
            }, (dlgFadeOutTransTime + 150));
        })(this);
    }

    /**
     * Quits to Verify Email Dialog
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function quitToVerifyEmailDialog(dlgFadeOutTransTime){
        // Fade out current dialog / fade in previous dialog
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, $(this.dom.verifyEmailDialog), 
                                         $(this.dom.forgotPwdDialog));
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
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, $(this.dom.createNewPwdDialog), 
                                         $(this.dom.verifyEmailDialog));

        // To reset the autofocus on first code input
        $(this.dom.verificationCode0).focus();
    }

    /**
     * Clears the inputs for the new password being created in
     * CreateNewPassword dialog
     *
     * @param none
     * @return none
     */
    function clearInputsOnCreateNewPasswordDialog(){
        $(this.dom.createNewPwdInput).val('');
        $(this.dom.confirmNewPwdInput).val('');
    }

    /**
     * Makes a POST request to resend the verification code to user
     *
     * @param none
     * @return none
     */
    function resendVerificationCodeToUser(){
        loadDoc('POST', this.global.h_vEResendVerifCodePostRequestData.url, 
                        this.global.h_vEResendVerifCodePostRequestData.name + '=1&' + 
                        'signInEmail=' + this.global.h_vEResendVerifCodePostRequestData.email, 
                clearInputErrorOnVerifyEmailDialog.bind(this));
    }

    /**
     * Clears the error from verification code inputs on
     * VerifyEmailDialog
     *
     * @param none
     * @return none
     */
    function clearInputErrorOnVerifyEmailDialog(xhttp){
        $(this.dom.verifyEmailErrorMsg).text(' ');
    }

    /********************************************************************/
    /********************************************************************/
    /*************** RESET PASSWORD PAGE RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/
    
    /**
      * Initialization of Reset Password page, perform the rendering of page
      *
      * @param none
      * @return none
      */
    function init(){

        /* Since any of the resetPassword page dialog forms has been submitted, 
         * it means the resetPassword page has just be loaded, lets show it */
        if(!(this.global.h_fPDialogFormSubmitted) && 
           !(this.global.h_vEDialogFormSubmitted) && 
           !(this.global.h_nPDialogFormSubmitted)){
            showRecoverPasswordPage.call(this, 200);
        }

        $(_window).bind('resize', pageResized.bind(this));
    }

    /********************************************************************/
    /********************************************************************/
    /************ FORGOT PASSWORD DIALOG RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/

    /**
      * Initialization of Forgot Password dialog, perform the rendering of page
      *
      * @param none
      * @return none
      */
    function initForgotPasswordDialog(){

        // Bind the click on DOM elements to handlers
        $(this.dom.forgotPwdDialogCloseMarkSvg).bind('click', quitToForgotPwdDialog.bind(this, 300));

        /* Actions to be done if forgotPassword dialog form is submitted */
        if(this.global.h_fPDialogFormSubmitted){

            /* Forgot Pwd Dialog form submitted and no errors in submitted form.
             * Then hide/show the forgotPwdDialog and verifyEmailDialog, respectively */
            if(!this.global.h_fPDialogError){
                // Show next dialog
                showNextGeneralPurposeDialog(300, $(this.dom.forgotPwdDialog), 
                                             $(this.dom.verifyEmailDialog));
            }
            else{
                /* Forgot Pwd Dialog form submitted and errors are present.
                 * Set the forgotPwdDialog class to standByState so that it is shown at
                 * the center of screen */
                centerDialog($(this.dom.forgotPwdDialog));
            }
        }
    }

    /********************************************************************/
    /********************************************************************/
    /*************** VERIFY EMAIL DIALOG RENDERING BEHAVIOR *************/
    /********************************************************************/
    /********************************************************************/
 
    /**
      * Initialization of Verify Email dialog, perform the rendering of page
      *
      * @param none
      * @return none
      */
    function initVerifyEmailDialog(){

        // Bind the click on DOM elements to handlers
        $(this.dom.resendCodeLink).bind('click', resendVerificationCodeToUser.bind(this));
        $(this.dom.verifyEmailDialogCloseMarkSvg).bind('click', quitToVerifyEmailDialog.bind(this, 300));

        /* Actions to be done if verifyEmail dialog form is submitted */
        if(this.global.h_vEDialogFormSubmitted){

            /* Verify Email Dialog form submitted and no errors in submitted form.
             * Then hide/show the verifyEmailDialog and createNewPwdDialog, respectively */
            if(!this.global.h_vEDialogError){
                // Show next dialog
                showNextGeneralPurposeDialog(300, $(this.dom.verifyEmailDialog), 
                                             $(this.dom.createNewPwdDialog));
            }
            else{
                /* Verify Email Dialog form submitted and errors are present.
                 * Set the verifyEmailDialog class to standByState so that it is shown at
                 * the center of screen */
                centerDialog($(this.dom.verifyEmailDialog));
            }
        }

        /***** Verify Code Inputs *****/
        const codeInputs = [..._document.querySelectorAll('input' + this.dom.code_input)]

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
    }
 
    /********************************************************************/
    /********************************************************************/
    /************** CREATE NEW PWD DIALOG RENDERING BEHAVIOR ************/
    /********************************************************************/
    /********************************************************************/

    /**
      * Initialization of Create New Password dialog, perform the rendering of page
      *
      * @param none
      * @return none
      */
    function initCreateNewPasswordDialog(){

        // Bind the click on DOM elements to handlers
        $(this.dom.createNewPwdDialogCloseMarkSvg).bind('click', quitToCreateNewPwdDialog.bind(this, 300));
        $(this.dom.changePassword).bind('click', clearInputsOnCreateNewPasswordDialog.bind(this));

        /* Actions to be done if createNewPassword dialog form is submitted */
        if(this.global.h_nPDialogFormSubmitted){
            /* Create New Pwd Dialog form submitted and no errors in submitted form.
             * Success on dialog form then close the createNewPwdDialog */
            if(!this.global.h_nPErrors){
                // Move on from current dialog
                showNextGeneralPurposeDialog(300, $(this.dom.createNewPwdDialog));

                /* Since the dialog will be eventually closed after effect,
                 * we need to close also the resetPassword page too */
                (function(_this){
                    _window.setTimeout( function(){
                        $(_this.dom.resetPasswordPage).css('display', 'none');

                        // Redirect to same web-page to get clean php page
                        _window.location.href = _this.global.doc_root +  "signin";
                    }, 450);
                })(this);
            }
            else{
                /* Create New Password Dialog form submitted and errors are present. 
                 * Set the createNewPwdDialog class to standByState so that it is shown at
                 * the center of screen */
                centerDialog($(this.dom.createNewPwdDialog));
            }
        }
    }

    /********************************************************************/
    /********************************************************************/
    /************************* RESPONSIVE DESIGN ************************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Initialization of ResetPassword page responsive design
     *
     * @param none
     * @return none
     */
    function initResponsiveDesign(){

        /* Behavior when window decreases and reaches the responsive
         * design threshold. Perhaps all of these or some are redundant
         * instructions (from above) but just in case */
        if(onMobilePortraitMode()){
            /* When any of the resetPassword page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            $(this.dom.signInPage).css('display', 'none');
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            $(this.dom.signInPage).css('display', 'block');

            /* Center dialogs again */
            if($(this.dom.forgotPwdDialog).css('display') == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(forgotPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.forgotPwdDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }

            if($(this.dom.verifyEmailDialog).css('display') == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(verifyEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.verifyEmailDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }

            if($(this.dom.createNewPwdDialog).css('display') == "block"){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(createNewPwdDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.createNewPwdDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }
        }
    }
    
    /**
     * Function executed when window resize event occurs
     * SMARTPHONE CONDITIONS to remove signInPage while resetting password (resetPassword page covers 100%)
     * Since we need 2 conditions we can not do it with media query
     *
     * @param none
     * @return none
     */
    function pageResized(){

        // Get Css root variable to determine if we are on Mobile portrait mode or not
        var onMobilePortraitMode = Number(getComputedStyle(_document.documentElement).getPropertyValue('--onMobilePortraitMode'));

        /* Behavior when window decreases and reaches the responsive
         * design threshold*/
        if(onMobilePortraitMode){
            /* When any of the resetPassword page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            $(this.dom.signInPage).css('display', 'none');

            // Remove class that centers dialog
            $(this.dom.forgotPwdDialog).attr('class', '');
            $(this.dom.verifyEmailDialog).attr('class', '');
            $(this.dom.createNewPwdDialog).attr('class', '');
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            $(this.dom.signInPage).css('display', 'block');

            /* Center dialogs again */
            if($(this.dom.forgotPwdDialog).css('display') == "block"){
                centerDialog($(this.dom.forgotPwdDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.forgotPwdDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }

            if($(this.dom.verifyEmailDialog).css('display') == "block"){
                centerDialog($(this.dom.verifyEmailDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.verifyEmailDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }

            if($(this.dom.createNewPwdDialog).css('display') == "block"){
                centerDialog($(this.dom.createNewPwdDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.createNewPwdDialog), 
                                                       $(this.dom.resetPasswordPageVerticalSpacer));
            }
        }
    }

    publicAPI = {

        // Public methods
        init: init,
        initForgotPasswordDialog: initForgotPasswordDialog,
        initVerifyEmailDialog: initVerifyEmailDialog,
        initCreateNewPasswordDialog: initCreateNewPasswordDialog,
        initResponsiveDesign: initResponsiveDesign,
        
        // ResetPasswordManager scope  DOM elements
        dom: {},

        // ResetPasswordManager global vars
        global: {}
    };

    return publicAPI;

})(window, document);

$(document).ready(function(){
    
    /* Populate the dom elements to be used by the resetPasswordManager */
    resetPasswordManager.dom = {

        signInPage: "#signInPage",
        resetPasswordPage: "#resetPasswordPage",
        resetPasswordPageVerticalSpacer: ".resetPasswordPageVerticalSpacer",
        forgotPwdDialog: "#forgotPwdDialog",
        forgotPwdDlgBodyContainer: "#forgotPwdDlgBodyContainer",
        forgotPwdDialogCloseMarkSvg: "#forgotPwdDialogCloseMarkSvg",
        verifyEmailDialog: "#verifyEmailDialog",
        code_input: ".code-input",
        verificationCode0: "#verificationCode0",
        resendCodeLink: "#resendCodeLink",
        verifyEmailErrorMsg: "#verifyEmailErrorMsg",
        verifyEmailDialogCloseMarkSvg: "#verifyEmailDialogCloseMarkSvg",
        createNewPwdDialog: "#createNewPwdDialog",
        createNewPwdInput: "#createNewPwdInput",
        confirmNewPwdInput: "#confirmNewPwdInput",
        changePassword: "#changePassword",
        createNewPwdDialogCloseMarkSvg: "#createNewPwdDialogCloseMarkSvg"
    };

    /* Populate the global vars to be used by the resetPasswordManager
     * These includes: Php variables, ...*/
    resetPasswordManager.global = {
        h_fPDialogFormSubmitted: h_fPDialogFormSubmitted,
        h_vEDialogFormSubmitted: h_vEDialogFormSubmitted,
        h_nPDialogFormSubmitted: h_nPDialogFormSubmitted,
        h_vEResendVerifCodePostRequestData: h_vEResendVerifCodePostRequestData,
        h_fPDialogError: h_fPDialogError,
        h_vEDialogError: h_vEDialogError,
        h_nPErrors: h_nPErrors,
        doc_root: doc_root
    };

    resetPasswordManager.init();
    resetPasswordManager.initForgotPasswordDialog();
    resetPasswordManager.initVerifyEmailDialog();
    resetPasswordManager.initCreateNewPasswordDialog();
    resetPasswordManager.initResponsiveDesign();
});
