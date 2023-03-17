<?php

/**
 * SessionAttr.php
 *
 * Author: Joel Ruiz
 * Copyright (c) HR parser. All rights reserved.
 */

namespace HRparser\CMS;                            // Namespace declaration

class SessionAttr
{
    /**
     * @var Variables that reference to session attributes
     */
    public $m_id;
    public $m_forename;
    public $m_role;
    public $m_isLoggedInFlag;
    
    /**
     * Class constructor
     *
     * @param none
     * @return none
     */
    public function __construct($m_id = -1, $m_forename = '', $m_role = 'public',
                                $m_isLoggedInFlag = false)
    {
        $this->m_id              = $m_id;
        $this->m_forename        = $m_forename;
        $this->m_role            = $m_role;
        $this->m_isLoggedInFlag  = $m_isLoggedInFlag;
    }
}

?>

