<?php

require_once("business/accountService.php");
require_once("business/encryptionService.php");

// Check if an admin is logged in
$accountSvc      = new AccountService();
$account         = $accountSvc->getLoggedInUser(true);
$loggedInAsAdmin = ($account->getAdministrator() === "1" ? true : false);

if ($_POST) {

    // Send confirmation mail to administrator
    $encryptionSvc    = new EncryptionService();
    $confirmationCode = $encryptionSvc->encryptString(
        $account->getEmail(),
        $encryptionSvc::DELETE_MATCHING_STRING
    );
    
    // Generate the message
    $currentPath = $accountSvc->getCurrentPath();
    $link        = $currentPath . "deleteMatchingConfirmation.php?code" . $confirmationCode;
    
    $msg = "<p>Beste, <br><br>
            Klik op de onderstaande link om de matchings te verwijderen:<br>
            <a href=\"" . $link . "\">Verwijder de matchings</a><br><br>
            Met vriendelijke groeten, <br>
            VDAB</p>";
    
    // Send the mail
    $mailSvc = new MailService();
    $mailSvc->sendHtmlMail($mail, "Vinder | Verwijder matchings", $msg);
    
    include("presentation/deleteMatchingSuccess.php");
    
}

// Show the view
include("presentation/deleteMatching.php");