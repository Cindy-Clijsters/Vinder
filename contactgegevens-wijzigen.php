<?php
require_once("business/validationService.php");
require_once("business/accountService.php");

// Load the necessary services
$accountSvc    = new AccountService();
$validationSvc = new ValidationService();

// Check if the user is logged in
$loggedInAccount = $accountSvc->getLoggedInUser();
$menuItem        = "contactgegevens-wijzigen";

// Get the amount of matched and unmatched companies
$amountMatchedCompanies   = $accountSvc->getAmountMatchedCompanies();
$amountUnmatchedCompanies = $accountSvc->getAmountUnmatchedCompanies();

// Set the variables
$errors = [];
$msg    = '';

// Get the posted values
$name           = (filter_input(INPUT_POST, 'name') !== null ? filter_input(INPUT_POST, 'name') : $loggedInAccount->getName());
$contactPerson  = (filter_input(INPUT_POST, 'contactPerson') !== null ? filter_input(INPUT_POST, 'contactPerson') : $loggedInAccount->getContactPerson());
$email          = (filter_input(INPUT_POST, 'email') !== null ? filter_input(INPUT_POST, 'email') : $loggedInAccount->getEmail());


if ($_POST) {
    // Validate the fields
    $nameErrors = $validationSvc->checkRequiredAndMaxLength($name, 255);
    
    if ($nameErrors !== '') {
        $errors['name'] = $nameErrors;
    }
    
    $contactPersonErrors = $validationSvc->checkRequiredAndMaxLength($contactPerson, 255);

    if ($contactPersonErrors !== '') {
        $errors['contactPerson'] = $contactPersonErrors;
    }
    
    $emailErrors = $validationSvc->checkRequiredAndMaxLength($email, 255); 

    if ($emailErrors === '') {
        $emailErrors = $validationSvc->checkEmail($email);
    }
    
    if ($emailErrors == '') {
        $emailErrors = $validationSvc->checkUniqueAccountEmail($email, $loggedInAccount->getId());
    }
    
    if ($emailErrors !== '') {
        $errors['email'] = $emailErrors;
    }
    
    if (empty($errors)) {
        
        // Update the contactinfo
        $loggedInAccount->setName($name);
        $loggedInAccount->setContactPerson($contactPerson);
        $loggedInAccount->setEmail($email);
        
        $accountSvc->update($loggedInAccount);
        
        $msg = "Uw gegevens zijn met success aangepast.";
        
    }
}
// Show the view
include("presentation/contactgegevens-wijzigen.php");

