/**
 * signUp.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

var signUpManager = (function(_window, _document){

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
        $(this.dom.signUpPage).css('display', 'block');
        $(this.dom.signUpDialog).css('display', 'block');

        /* Adjust the vertical spacers for current SignUp  
         * dialog displayed */
        adjustVerticalSpacersWithCurrentDialog($(this.dom.signUpDialog), $(this.dom.signUpVerticalSpacer));

        // Hide SingIn page when displaying on mobile
        if(onMobilePortraitMode())
            $(this.dom.signUpPage).css('display', 'none');

        // Fade in Sign Up dialog
        fancyFadeInDialog($(this.dom.signUpDialog), $(this.dom.signUpFormDiv), dlgFadeInTransTime, 
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
        showPreviousGeneralPurposeDialog(dlgFadeOutTransTime, $(this.dom.signUpDialog));

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        (function(_this){
            _window.setTimeout( function(){
                $(_this.dom.signUpPage).css('display', 'none');

                // Redirect to same web-page to get clean php page
                _window.location.href = _this.global.doc_root +  "signin";
            }, (dlgFadeOutTransTime + 150));
        })(this);
    }

    /**
     * Shows the Privacy Policy & Terms Dialog
     *
     * @param dlgFadeInTransTime. Dialog fade-in transition time in ms.
     * @return none
     */
    function showPrivacyPoliciesAndTermsAndConditionsDialog(dlgFadeInTransTime){
        // Hide the signUp dialog, scroll to top and show the PrivacyTerms dialog
        $(this.dom.signUpDialog).css('display', 'none');
        $(this.dom.signUpScrollableDiv).scrollTop = 0;
 
        /* By now the SignIn Page must be hidden if we are on mobile portrait
         * mode, but just in case */
        if(onMobilePortraitMode())
            $(this.dom.signInPage).css('display', 'none');

        // Fade-in dialog
        fancyFadeInDialog($(this.dom.privacyTermsDialog), $(this.dom.privacyTermsBlock), 
                          dlgFadeInTransTime, (dlgFadeInTransTime + 50));

        // Adjust vertical spacer
        adjustVerticalSpacersWithCurrentDialog($(this.dom.privacyTermsDialog), $(this.dom.signUpVerticalSpacer));
    }

    /**
     * Closes the PrivacyPolicies and Terms&Conditions dialog
     *
     * @param dlgFadeOutTransTime. Dialog fade-out transition time in ms.
     * @return none
     */
    function closePrivacyPoliciesAndTermsAndConditionsDialog(dlgFadeOutTransTime){
        // Fade-out current dialog
        centerDialog($(this.dom.privacyTermsDialog));
        fadeOutDialogOnPlace($(this.dom.privacyTermsDialog), dlgFadeOutTransTime);

        /* Show the signUp dialog again */
        (function(_this){
            _window.setTimeout( function(){
                $(_this.dom.signUpDialog).css('display', 'block');
                centerDialog($(_this.dom.signUpDialog));

                // Adjust vertical spacer
                adjustVerticalSpacersWithCurrentDialog($(_this.dom.signUpDialog), $(_this.dom.signUpVerticalSpacer));
            }, (dlgFadeOutTransTime + 100));
        })(this);
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
        (function(_this){
            _window.setTimeout( function(){
                fadeInAndScaleDialog($(_this.dom.confirmationEmailDialog), dlgFadeInTransTime);
                adjustVerticalSpacersWithCurrentDialog($(_this.dom.confirmationEmailDialog), $(_this.dom.signUpVerticalSpacer));
            }, (dlgFadeInTransTime + 150));
        })(this);
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
        fadeOutAndScaleDialog($(this.dom.confirmationEmailDialog), dlgFadeOutTransTime);

        /* Since the dialog will be eventually closed after effect,
         * we need to close also the signUp page too */
        (function(_this){
            _window.setTimeout( function(){
                $(_this.dom.signUpPage).css('display', 'none');

                // Redirect to same web-page to get clean php page
                _window.location.href = _this.global.doc_root +  "signin";
            }, (dlgFadeOutTransTime + 150));
        })(this);
    }

    /**
     * Makes a GET request to resend the verification/confirmation email
     * to user to activate his/her account
     *
     * @param none
     * @return none
     */
    function resendActivationAccountEmailToUser(){
        loadDoc('GET', this.global.h_sUResendVerifEmailLink);
    }
 
    /********************************************************************/
    /********************************************************************/
    /*************** SIGNUP PAGE INIT RENDERING BEHAVIOR ****************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Initialization of SignUp dialog selectCountry comboBox. Applies
     * style for cases it is not possible achieve with CSS.
     *
     * @param none
     * @return none
     */
    function initSelectCountryComboBox(){

        // Change select font color by default(no action)
        if($(this.dom.selectCountry).val() != 'null'){
            $(this.dom.selectCountry).css('color','black');
        }
        else{
            $(this.dom.selectCountry).css('color','gray');
        }

        function selectorChange(){
             var current = $(this.dom.selectCountry).val();
             if (current != 'null'){
                 $(this.dom.selectCountry).css('color','black');
             }else{
                 $(this.dom.selectCountry).css('color','gray');
             }
        }

        // Change select font color after an option is selected
        $(this.dom.selectCountry).bind('change', selectorChange.bind(this));
    }

    /**
     * Initialization of SignUp page, perform the rendering of page
     *
     * @param none
     * @return none
     */
    function init(){

        // Bind the click on DOM elements to handlers
        $(this.dom.signUpDialogCloseMarkSvg).bind('click', closeSignUpDialog.bind(this, 300));
        $(this.dom.signInLink).bind('click', closeSignUpDialog.bind(this, 300));
        $(this.dom.signUpTermsLink).bind('click', showPrivacyPoliciesAndTermsAndConditionsDialog.bind(this, 200));
        $(this.dom.privacyTermsCloseMarkDiv).bind('click', closePrivacyPoliciesAndTermsAndConditionsDialog.bind(this, 250));
        $(this.dom.privacyTermsCloseButton).bind('click', closePrivacyPoliciesAndTermsAndConditionsDialog.bind(this, 250));
        $(this.dom.confirmationEmailCloseMarkSvg).bind('click', closeConfirmationEmailDialog.bind(this, 200));
        $(this.dom.resendAccountVerifEmailLink).bind('click', resendActivationAccountEmailToUser.bind(this));

        $(_window).bind('resize', pageResized.bind(this));
      
        /* Actions to be done if signUp dialog form is submitted */
        if(this.global.h_sUDialogFormSubmitted){
        
            /* SignUp Dialog form submitted and no errors in submitted form.
             * Then hide the current dialog */
            if(!this.global.h_sUErrors){
                // Fade-out signUp dialog
                showNextGeneralPurposeDialog(300, $(this.dom.signUpDialog));

                /* Show ConfirmationEmail dialog, wait until fade-out transition of
                 * signUp dialog finishes to start this one */
                showConfirmationEmailDialog.call(this, 400);
            }
            else{
                /* SignUp Dialog form submitted and errors are present. 
                 * Set the signUpDialog class to standByState so that it is shown at
                 * the center of screen */
                $(this.dom.signUpDialog).css('display', 'block');
                centerDialog($(this.dom.signUpDialog));
            }
        }
        else{
            // Initialize selectCountry comboBox
            initSelectCountryComboBox.call(this);

            /* Since signUp dialog form has not been submitted it means the
             * signUp page has just be loaded, lets show it */
            showSignUpPage.call(this, 200);
        }
    }

    /********************************************************************/
    /********************************************************************/
    /************************* RESPONSIVE DESIGN ************************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Initialization of SignUp page responsive design
     *
     * @param none
     * @return none
     */
    function initResponsiveDesign(){

        /* Behavior when window decreases and reaches the responsive
         * design threshold. Perhaps all of these or some are redundant
         * instructions (from above) but just in case */
        if(onMobilePortraitMode()){
            /* When any of the signUp page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            $(this.dom.signInPage).css('display', 'none');
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            $(this.dom.signInPage).css('display', 'block');

            /* Center dialogs again */
            if($(this.dom.signUpDialog).css('display') == 'block'){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(signUpDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.signUpDialog), 
                                                       $(this.dom.signUpVerticalSpacer));
            }

            if($(this.dom.confirmationEmailDialog).css('display') == 'block'){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(confirmationEmailDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.confirmationEmailDialog), 
                                                       $(this.dom.signUpVerticalSpacer));
            }

            if($(this.dom.privacyTermsDialog).css('display') == 'block'){
                // Do not center here since it impacts fade-in/fade-out transition
                //centerDialog(privacyTermsDialog);
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.privacyTermsDialog),
                                                       $(this.dom.signUpVerticalSpacer));
            }
        }
    }

    /**
      * Function executed when window resize event occurs
      * SMARTPHONE CONDITIONS to remove signInPage while resetting password (signUp page covers 100%)
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
            /* When any of the signUp page dialogs is being shown, hide
             * the signIn page normally displayed on background */
            $(this.dom.signInPage).css('display', 'none');

            // Remove class that centers dialog
            $(this.dom.confirmationEmailDialog).attr('class', '');
        }
        else{
            // IF NOT onMobilePortraitMode then...
            /* Show SignInPage back again (background) */
            $(this.dom.signInPage).css('display', 'block');

            /* Center dialogs again */
            if($(this.dom.signUpDialog).css('display') == "block"){
                centerDialog($(this.dom.signUpDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.signUpDialog), $(this.dom.signUpVerticalSpacer));
            }

            if($(this.dom.confirmationEmailDialog).css('display') == "block"){
                centerDialog($(this.dom.confirmationEmailDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.confirmationEmailDialog), $(this.dom.signUpVerticalSpacer));
            }

            if($(this.dom.privacyTermsDialog).css('display') == "block"){
                centerDialog($(this.dom.privacyTermsDialog));
                /* Adjust vertical upper/bottom spacers */
                adjustVerticalSpacersWithCurrentDialog($(this.dom.privacyTermsDialog), $(this.dom.signUpVerticalSpacer));
            }
        }
    }

    // Object to reference from global scope
    var publicAPI = {

        // Public methods
        init: init,
        initResponsiveDesign: initResponsiveDesign,

        // signUpManager scope DOM elements
        dom: {},

        // signUpManager global vars
        global: {}
    };

    return publicAPI;
})(window, document);


$(document).ready(function(){

    /* Populate the dom elements to be used by the signUpManager */
    signUpManager.dom = {

        signInPage: "#signInPage",
        signInLink: "#signInLink",
        signUpPage: "#signUpPage",
        signUpScrollableDiv: "#signUpScrollableDiv",
        signUpVerticalSpacer: ".signUpVerticalSpacer",
        signUpDialog: "#signUpDialog",
        signUpFormDiv: "#signUpFormDiv",
        signUpTermsLink: "#signUpTermsLink",
        signUpDialogCloseMarkSvg: "#signUpDialogCloseMarkSvg",
        privacyTermsDialog: "#privacyTermsDialog",
        privacyTermsBlock: "#privacyTermsBlock",
        privacyTermsCloseMarkDiv: "#privacyTermsCloseMarkDiv",
        privacyTermsCloseButton: "#privacyTermsCloseButton",
        confirmationEmailDialog: "#confirmationEmailDialog",
        resendAccountVerifEmailLink: "#resendAccountVerifEmailLink",
        confirmationEmailCloseMarkSvg: "#confirmationEmailCloseMarkSvg",
        selectCountry: "#selectCountry"
    };

    /* Populate the global vars to be used by the signUpManager
     * These includes: Php variables, ...*/
    signUpManager.global = {
        h_sUDialogFormSubmitted: h_sUDialogFormSubmitted,
        h_sUErrors: h_sUErrors,
        h_sUResendVerifEmailLink: h_sUResendVerifEmailLink,
        doc_root: doc_root
    };

    signUpManager.init();
    signUpManager.initResponsiveDesign();
});

