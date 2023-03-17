/**
 * signIn.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

var signInManager = (function(_window){

    /********************************************************************/
    /********************************************************************/
    /********************* SIGN IN PAGE UTILITIES ***********************/
    /********************************************************************/
    /********************************************************************/

    /**
     * Requests the Reset Password Page
     *
     * @param none
     * @return none
     */
    function requestResetPasswordPage(){
        // Request the signUp page
        _window.location.href = this.global.doc_root +  "reset-password";
    }

    /**
     * Requests the SignUp Page
     *
     * @param none
     * @return none
     */
    function requestSignUpPage(evt){
        // Request the signUp page
        _window.location.href = this.global.doc_root +  "signup";
    }

    /**
     * Initialization of SignIn page, perform the rendering of page
     *
     * @param none
     * @return none
     */
    function init(){
        // Bind the click on DOM elements to handlers
        $(this.dom.forgotPwdLink).bind('click', requestResetPasswordPage.bind(this));
        $(this.dom.signUpLink).bind('click', requestSignUpPage.bind(this));
    }

    // Object to reference from global scope
    var publicAPI = {
        init: init,
 
        // signInManager scope DOM elements
        dom: {},

        // signInManager global vars
        global: {}
    };

    return publicAPI;

})(window); 

$(document).ready(function(){

    /* Populate the dom elements to be used by the signInManager */
    signInManager.dom = {

        forgotPwdLink: "#forgotPwdLink",
        signUpLink: "#signUpLink"
    };

    /* Populate the global vars to be used by the signInManager
     * These includes: Php variables, ...*/
    signInManager.global = {
        doc_root: doc_root
    };

    signInManager.init();
});

