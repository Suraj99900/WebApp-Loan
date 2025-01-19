<?php
session_start();
class SessionManager
{
    public $iUserID;
    public $sUserMobileNo;
    public $sUserName;
    public $sUserEmail;
    public $sUserUniqueId;
    public $iUserType;
    public $isLoggedIn;

    // Constructor
    function __construct()
    {
        // Start session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION["isLoggedIn"])) {
            $this->iUserID = $_SESSION['iUserID'];
            $this->sUserMobileNo = $_SESSION['sUserMobileNo'];
            $this->sUserName = $_SESSION['sUserName'];
            $this->sUserEmail = $_SESSION['sUserEmail'];
            $this->sUserUniqueId = $_SESSION['sUserUniqueId'];
            $this->iUserType = $_SESSION['iUserType'];
            $this->isLoggedIn = $_SESSION['isLoggedIn'];

            // Close session write
            session_write_close();
        }
    }

    /**
     * Set session data
     * @param array $aSessionData
     */
    public function fSetSessionData($aSessionData)
    {
        // Start session
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Set session data
        $_SESSION['iUserID'] = $aSessionData['id'];
        $_SESSION['sUserName'] = $aSessionData['username'];
        $_SESSION['sUserMobileNo'] = $aSessionData['phoneNumber'];
        $_SESSION['sUserEmail'] = $aSessionData['email'];
        $_SESSION['sUserUniqueId'] = $aSessionData['user_id'];
        $_SESSION['iUserType'] = $aSessionData['User_type'];
        $_SESSION['isLoggedIn'] = $aSessionData['login'];

        // Assign data to class properties
        $this->iUserID = $_SESSION['iUserID'];
        $this->sUserMobileNo = $_SESSION['sUserMobileNo'];
        $this->sUserName = $_SESSION['sUserName'];
        $this->sUserEmail = $_SESSION['sUserEmail'];
        $this->sUserUniqueId = $_SESSION['sUserUniqueId'];
        $this->iUserType = $_SESSION['iUserType'];
        $this->isLoggedIn = $_SESSION['isLoggedIn'];

        // Close session write
        session_write_close();
    }

    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->isLoggedIn;
    }
}
