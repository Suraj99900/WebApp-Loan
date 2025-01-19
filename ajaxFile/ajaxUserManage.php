<?php
header('Content-Type: application/json');

require_once "../classes/DB-Connection.php";
require_once "../classes/UserManage.php";
include_once "../classes/clientCode.php";
include_once "../classes/sessionManager.php";
include_once "../classes/class.Input.php";
include_once "../classes/function.php";

$sFlag = Input::request('sFlag');

if ($sFlag == 'addUser') {
    $sName = Input::request('username') ? Input::request('username') : '';
    $sEmail = Input::request('email') ? Input::request('email') : '';
    $sPhoneNumber = Input::request('phoneNumber') ? Input::request('phoneNumber') : '';
    $sPassword = Input::request('password') ? Input::request('password') : '';
    $iUserType = Input::request('userType') ? Input::request('userType') : '';
    $iSecretCode = Input::request('keyId') ? Input::request('keyId') : '';
    $bDirect = Input::request('isDirect') ? Input::request('isDirect') : false;

    // check client code
    if(!$bDirect){
        $oClient = new clientCode();
        $oClientResult = $oClient->getClientCodeByByCode($iSecretCode);
        if (!$oClientResult) {
            echo json_encode(array("error" => "Wrong Code", "status" => 500));
            return false;
        }
    }
    
    if ($sName == '' || $sEmail == '' || $sPassword == '' || $sPhoneNumber == '' ) {
        echo json_encode(array("error" => "Missing Parameters", "status" => 500));
        return false;
    }
    $userManage = new UserManage();
    $oResult = $userManage->addUser($sName, $sPassword, $iUserType, $sEmail, $sPhoneNumber);
    if (!$oResult) {
        echo json_encode(array("message" => "Error", "status" => 500));
    } else {
        echo json_encode(array("message" => "User created successfully", "status" => 200));
    }
}
if ($sFlag == 'updateUser') {
    $id = Input::request('userId');
    $name = Input::request('username') ? Input::request('username') : '';
    $sPhoneNumber = Input::request('phoneNumber') ? Input::request('phoneNumber'): '';
    $sEmail = Input::request('email') ? Input::request('email'): '';
    $sType = Input::request('userType') ? Input::request('userType') :'';
    $sPassword = Input::request('password') ? Input::request('password') :'';
    if ($id == '' || $sPhoneNumber == '' || $sType == '') {
        echo json_encode(array("error" => "Missing Parameters", "status" => 500));
        return false;
    }
    $userManage = new UserManage();
    $userManage->updateUser($id, $name, $sPhoneNumber, $sType,$sEmail,$sPassword);
    echo json_encode(array("message" => "User updated successfully", "status" => 200));
}
if ($sFlag == 'delete') {
    $id = Input::request('user_id');
    if ($id == '') {
        echo json_encode(array("error" => "Missing ID parameter", "status" => 500));
        return false;
    }
    $userManage = new UserManage();
    $userManage->deleteUser($id);
    echo json_encode(array("message" => "User deleted successfully", "status" => 200));
}
if ($sFlag == 'fetch') {
    $sName = Input::request('userName') ? Input::request('userName') :'';
    $iUserType = Input::request('userType') ? Input::request('userType') :'';
    $sEmail = Input::request('email') ? Input::request('email') :'';

    $userManage = new UserManage();
    $users = $userManage->fetchAll($sName,$iUserType,$sEmail);
    $response['status'] = 'success';
    $response['recordsTotal'] = count($users); // Total number of records
    $response['recordsFiltered'] = count($users); // Number of records after filtering (if applicable)
    $response['data'] = $users;
    echo json_encode($response);
}
if ($sFlag == 'fetchById') {
    $id = Input::request('id');
    if ($id == '') {
        echo json_encode(array("error" => "Missing ID parameter", "status" => 500));
        return false;
    }
    $userManage = new UserManage($id);
    $oResult = $userManage->fetchById($id);
    echo json_encode(array('data' => $oResult, "status" => 200));
}
if ($sFlag == "login") {
    $sUserId = Input::request('user_id') ? Input::request('user_id') : '';
    $password = Input::request('password') ? Input::request('password') : '';

    $userManage = new UserManage();
    $oResult = $userManage->login($sUserId, $password);
    if ($oResult != false) {
        echo json_encode(array($oResult, "status" => 200));
    } else {
        echo json_encode(array($oResult, "status" => 500, 'message' => "Wrong username and password."));
    }
}

if ($sFlag == 'userSymptoms') {
    $name = Input::request('name') ? Input::request('name') : '';
    $aNameData = fetchSymptoms($name);

    echo json_encode(array($aNameData, "status" => 200));
}
