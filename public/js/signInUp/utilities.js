/**
 * utilities.js
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

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
	
	    // Center dialog
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
     * @param scrollableDivById. Container div with scrolling enabled
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
	
	    // Center dialog
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
        }, (transitionTime + 100));
    }

    /********************************************************************/
    /********************************************************************/
    /****** GENERAL UTILITIES TO FADE OUT/IN DIALOGS SIMULTANEOUSLY *****/
    /********************************************************************/
    /********************************************************************/ 

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
            fadeInDlgById.style.position = "absolute";
        }

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
                fadeInDlgById.style.position = "";
            }
        }, transitionTime + 100);
    }

