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
	
        var loginImgUrl = "http://" + this.global.minio_server + ":" + this.global.minio_port + 
                          "/" + this.global.my_resources_bucket + "/HRparser_LoginImg.jpg";
        $(this.dom.signInImgDiv).css('background-image', 'url(' + loginImgUrl + ')');
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
        signUpLink: "#signUpLink",
        signInImgDiv: "#signInImgDiv"
    };

    /* Populate the global vars to be used by the signInManager
     * These includes: Php variables, ...*/
    signInManager.global = {
        doc_root: doc_root,
        minio_server: minio_server,
        minio_port: minio_port,
        my_resources_bucket: my_resources_bucket
    };

    signInManager.init();
});

