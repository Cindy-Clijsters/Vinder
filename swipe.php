<?php
require_once("business/accountService.php");
require_once("business/generalService.php");

// Check if an admin is logged in
$accountSvc      = new AccountService();
$loggedInAccount = $accountSvc->getLoggedInUser();

// Admins can't swipe
if ($loggedInAccount->getAdministrator() === "1") {
    
    $errorMsg                 = "Je bent momenteel ingelogd als een administrator.<br>Het is niet mogelijk om te swipen.";
    $amountMatchedCompanies   = $accountSvc->getAmountMatchedCompanies();
    $amountUnmatchedCompanies = $accountSvc->getAmountUnmatchedCompanies();
    
}

// Current date must be bigger then end of registration
if (!isset($errorMsg)) {
    
    $generalSvc = new GeneralService();
    $general    = $generalSvc->get();

    if ($general === null) {

        $warningMsg = "Het is momenteel niet mogelijk om te swipen.<br>Probeer later opnieuw.";

    } else {
        
        // Get the min. swipe date (end registry date + 1 day)
        $registerDate     = DateTime::createFromFormat("Y-m-d H:i:s", $general->getRegisterDate());
        $startSwipingDate = $registerDate->add(new DateInterval("P1D"));

        // Get the current date
        $currentDate = new DateTime();

        if ($currentDate < $startSwipingDate) {
            $warningMsg = "Het is momenteel niet mogelijk om te swipen.<br>Probeer na " . $startSwipingDate->format("d-m-Y") . " opnieuw.";
        }

    }
    
}

// Get the information for swiping
if (!isset($errorMsg) && !isset($warningMsg)) {
    
    $swipingInfo = $accountSvc->getCompleteSwipingInfo($loggedInAccount->getId()); 
    $currentPath = $accountSvc->getCurrentPath();
    $numOfCards  = count($swipingInfo);
    
} else {
    
    $numOfCards = 0;
    
}

// Show the view
include("presentation/swipe.php");