<?php

/**
 * Session.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                            // Namespace declaration

class Session
{
    /**
     * @var $id, Reference to member Id
     */
    public $id;

    /**
     * @var $forename, Reference to member forename
     */
    public $forename;

    /**
     * @var $role, Reference to member role
     */
    public $role;

    /**
     * @var $isLoggedInFlag, Flag to determine if member is logged in for current session
     */
    public $isLoggedInFlag;

    /**
     * Class constructor
     *
     * @param none
     * @return none
     */
    public function __construct()
    {
        // Start or restart session
        session_start();

        $this->id             = $_SESSION['id'] ?? 0;                 // Set id property
        $this->forename       = $_SESSION['forename'] ?? '';          // Set forename property
        $this->role           = $_SESSION['role'] ?? 'public';        // Set role property
        $this->isLoggedInFlag = $_SESSION['isLoggedInFlag'] ?? false; // Set loggedIn flag 
    }

    /**
     * Recreates/updates the session for the given member
     *
     * @param $session. Session properties to recreate/update
     * @return none
     */
    public function create(SessionAttr $session)
    {
        session_regenerate_id(true);

        $_SESSION['id']             = $session->m_id;
        $_SESSION['forename']       = $session->m_forename;
        $_SESSION['role']           = $session->m_role;
        $_SESSION['isLoggedInFlag'] = $session->m_isLoggedInFlag;
    }

    /**
     * Updates the current session. Alias for create()
     *
     * @param $session. Session properties to update
     * @return none
     */
    public function update(SessionAttr $session)
    {
        $this->create($session);
    }

    /**
     * Deletes the existing session
     *
     * @param none
     * @return none
     */
    public function delete()
    {
        $_SESSION = [];                              // Empty $_SESSION superglobal
        $param    = session_get_cookie_params();     // Get session cookie params
        setcookie(session_name(), '', time() - 2400, $param['path'], $param['domain'],
                  $param['secure'], $param['httponly']); // Clear session cookie
        session_destroy();                           // Destroy the session/session file
    }
}

?>
