<?php

require_once("business/expertiseService.php");
require_once("business/accountService.php");

$accountSvc = new AccountService();

// Check if user is logged in
$account    = $accountSvc->getLoggedInUser();
$info       = $account->getInfo();

// Is the user logged in as an admin
$loggedInAsAdmin = ($account->getAdministrator() === "1" ? true : false);

// Get the ID from the logged in user
if ($loggedInAsAdmin) {
    if(isset($_GET["userId"])) {
        $id = $_GET["userId"];
        $account = $accountSvc->getById($id);
    }
    else {
        $id = $account->getId();
    }
}
// if showing another profile
elseif (isset($_GET["id"]) && $_GET["id"]!== NULL){
    $id = $_GET["id"];
    $account = $accountSvc -> getById($_GET["id"]);
}
else {
    $id = $account->getId();
}

// Get the necessary info to display the view
$expSrv      = new ExpertiseService();
$exps        = $expSrv->getExpertisesById($id);
$expExps     = $expSrv->getExpectedExpertisesById($id);
$extraExp    = $expSrv->getExtraExpertise($id);
$extraExpExp = $expSrv->getExtraExpectedExpertise($id);

include("presentation/accountShow.php");