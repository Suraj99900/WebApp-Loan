<?php
require_once "../config.php";
require_once "DB-Connection.php";

class UserManage
{
    public $sName;
    public $iType;

    function __construct($iId = null)
    {
        if ($iId !== null) {
            $this->fetchById($iId);
        }
    }

    function fetchById($iId)
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";

        $oQueryBuilder
            ->select("*")
            ->from($sTableName)
            ->where('id = :id')
            ->andWhere('status = :iStatus')
            ->andWhere('deleted = 0')
            ->setParameter('id', $iId)
            ->setParameter('iStatus', 1);

        try {
            $oResult = $oQueryBuilder->executeQuery();
            if ($oResult) {
                $aRow = $oResult->fetchAssociative();
                if ($aRow) {
                    $this->sName = $aRow['name'];
                    $this->iType = $aRow['user_type'];
                } else {
                    die("User not found.");
                }
                return $aRow;
            }
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    function addUser($sName, $sPassword, $iType, $sEmail, $sPhone, $iStatus = 1)
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";
        $sUserId = $this->generateUserId();

        $oQueryBuilder
            ->insert($sTableName)
            ->setValue('user_id', ':userId')
            ->setValue('staff_name', ':name')
            ->setValue('password', ':password')
            ->setValue('user_type', ':user_type')
            ->setValue('email', ':email')
            ->setValue('phone', ':phone')
            ->setValue('status', ':status')
            ->setValue('added_on', ':dDate')
            ->setParameter('userId', $sUserId)
            ->setParameter('name', $sName)
            ->setParameter('password', password_hash($sPassword, PASSWORD_BCRYPT))
            ->setParameter('user_type', $iType)
            ->setParameter('email', $sEmail)
            ->setParameter('phone', $sPhone)
            ->setParameter('status', $iStatus)
            ->setParameter('dDate', date("Y-m-d"));

        try {
            $oResult = $oQueryBuilder->executeQuery();
            if ($oResult) {
                $lastInsertId = $oConnection->conn->lastInsertId(); // Retrieve the last inserted ID
                return $lastInsertId;
            }
        } catch (\Exception $e) {
            return false;
        }
    }


    function generateUserId()
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $oQueryBuilder
            ->select('MAX(id) AS max_id')
            ->from('app_user');

        try {
            $row = $oQueryBuilder->executeQuery()->fetchAssociative();

            // Calculate the next ID
            $nextId = ($row['max_id'] ?? 0) + 1;

            // Format it as LN-XX (e.g., LN-01, LN-02)
            $userId = 'LN-' . str_pad($nextId, 2, '0', STR_PAD_LEFT);

            return $userId;
        } catch (\Exception $e) {
            die("Error generating user ID: " . $e->getMessage());
        }
    }


    function updateUser($iId, $sName='', $sPhone='', $iType='',$sEmail = '',$sPassword='')
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";

        $oQueryBuilder
            ->update($sTableName)
            ->set('staff_name', ':name')
            ->set('user_type', ':user_type')
            ->set('email',':email')
            ->set('phone',':phone')
            ->set('password',':password')
            ->where('id = :id')
            ->andWhere('deleted = 0')
            ->setParameter('name', $sName)
            ->setParameter('email', $sEmail)
            ->setParameter('user_type', $iType)
            ->setParameter('phone', $sPhone)
            ->setParameter('password', password_hash($sPassword, PASSWORD_BCRYPT))
            ->setParameter('id', $iId);

        try {
            $oQueryBuilder->executeQuery();
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    function deleteUser($iId)
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";

        $oQueryBuilder
            ->update($sTableName)
            ->set("deleted",":deleted")
            ->where('id = :id')
            ->andWhere('deleted = 0')
            ->setParameter('deleted', 1)
            ->setParameter('id', $iId);

        try {
            $oQueryBuilder->executeQuery();
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }

    function fetchAll($sName = '', $iUserType = '', $sEmail = '')
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";

        $oQueryBuilder
            ->select("*")
            ->from($sTableName)
            ->where('status = :iStatus')
            ->andWhere('deleted = 0')
            ->setParameter('iStatus', 1);

        // Add filters dynamically based on the parameters
        if (!empty($sName)) {
            $oQueryBuilder->andWhere('staff_name LIKE :sName')
                ->setParameter('sName', '%' . $sName . '%');
        }

        if (!empty($iUserType)) {
            $oQueryBuilder->andWhere('user_type = :iUserType')
                ->setParameter('iUserType', $iUserType);
        }

        if (!empty($sEmail)) {
            $oQueryBuilder->andWhere('email LIKE :sEmail')
                ->setParameter('sEmail', '%' . $sEmail . '%');
        }

        try {
            $oResult = $oQueryBuilder->executeQuery();
            return $oResult->fetchAllAssociative();
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }


    function login($sUserId, $sPassword)
    {
        $oConnection = new DBConnection();
        $oQueryBuilder = $oConnection->conn->createQueryBuilder();
        $sTableName = "app_user";

        $oQueryBuilder
            ->select("*")
            ->from($sTableName)
            ->where('status = :iStatus')
            ->andWhere("user_id = :sUserId")
            ->andWhere('deleted = 0')
            ->setParameter("sUserId", $sUserId)
            ->setParameter('iStatus', 1);

        try {
            $oResult = $oQueryBuilder->executeQuery();
            while ($oRow = $oResult->fetchAssociative()) {
                if (password_verify($sPassword, $oRow['password'])) {
                    return $oRow;
                }
            }
            return false;
        } catch (\Exception $e) {
            die("Error: " . $e->getMessage());
        }
    }
}
