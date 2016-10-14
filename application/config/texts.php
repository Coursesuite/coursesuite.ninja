<?php

/**
 * Texts used in the application.
 * These texts are used via Text::get('FEEDBACK_USERNAME_ALREADY_TAKEN').
 * Could be extended to i18n etc.
 */
return array(
    "FEEDBACK_UNKNOWN_ERROR" => "Unknown error occurred!",
    "INCORRECT_USAGE" => "Incorrect usage",

    "FEEDBACK_DELETED" => "Your account has been deleted.",
    "FEEDBACK_ADMIN_USER_DELETED" => "The account " . (isset($id) ? $id : "") . " has been deleted",

    "FEEDBACK_CAPTCHA_WRONG" => "The entered captcha security characters were wrong.",

    "FEEDBACK_LOGIN_FAILED" => "Login failed.",
    "FEEDBACK_LOGIN_FAILED_3_TIMES" => "Login failed 3 or more times already. Please wait 30 seconds to try again.",

    "FEEDBACK_USER_EMAIL_ALREADY_TAKEN" => "Sorry, that email is already in use. Please choose another one.",
    "FEEDBACK_USER_DOES_NOT_EXIST" => "This user does not exist.",

    "FEEDBACK_USERNAME_OR_PASSWORD_WRONG" => "The username or password is incorrect. Please try again.",
    "FEEDBACK_USERNAME_FIELD_EMPTY" => "Username field was empty.",
    "FEEDBACK_USERNAME_OR_PASSWORD_FIELD_EMPTY" => "Username or password field was empty.",
    "FEEDBACK_USERNAME_EMAIL_FIELD_EMPTY" => "Username / email field was empty.",
    "FEEDBACK_USERNAME_SAME_AS_OLD_ONE" => "Sorry, that username is the same as your current one. Please choose another one.",
    "FEEDBACK_USERNAME_ALREADY_TAKEN" => "Sorry, that username is already taken. Please choose another one.",
    "FEEDBACK_USERNAME_CHANGE_SUCCESSFUL" => "Your username has been changed successfully.",
    "FEEDBACK_USERNAME_AND_PASSWORD_FIELD_EMPTY" => "Username and password fields were empty.",
    "FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN" => "Username does not fit the name pattern: only a-Z and numbers are allowed, 2 to 64 characters.",
    "FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG" => "Username cannot be shorter than 2 or longer than 64 characters.",

    "FEEDBACK_EMAIL_FIELD_EMPTY" => "Email field was empty.",
    "FEEDBACK_EMAIL_REPEAT_WRONG" => "Email and email repeat are not the same",
    "FEEDBACK_EMAIL_AND_PASSWORD_FIELDS_EMPTY" => "Email and password fields were empty.",
    "FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN" => "Sorry, your chosen email does not fit into the email naming pattern.",
    "FEEDBACK_EMAIL_SAME_AS_OLD_ONE" => "Sorry, that email address is the same as your current one. Please choose another one.",
    "FEEDBACK_EMAIL_CHANGE_SUCCESSFUL" => "Your email address has been changed successfully.",

    "FEEDBACK_NO_DESTROY" => "Sorry, you need type the words exactly as shown",

    "FEEDBACK_PASSWORD_WRONG_3_TIMES" => "You have typed in a wrong password 3 or more times already. Please wait 30 seconds to try again.",
    "FEEDBACK_PASSWORD_FIELD_EMPTY" => "Password field was empty.",
    "FEEDBACK_PASSWORD_REPEAT_WRONG" => "Password and password repeat are not the same.",
    "FEEDBACK_PASSWORD_TOO_SHORT" => "Password has a minimum length of 6 characters.",

    "FEEDBACK_VERIFICATION_MAIL_SENDING_FAILED" => "Sorry, we could not send you a verification email. Your account has NOT been created.",
    "FEEDBACK_VERIFICATION_MAIL_SENDING_ERROR" => "Verification email could not be sent due to: ",
    "FEEDBACK_VERIFICATION_MAIL_SENDING_SUCCESSFUL" => "A verification email has been sent successfully (Don't forget to check your spam folder).",

    "FEEDBACK_ACCOUNT_NOT_ACTIVATED_YET" => "Your account is not activated yet. Please click on the confirm link in the mail.",
    "FEEDBACK_ACCOUNT_SUSPENDED" => "Account Suspended for ",
    "FEEDBACK_ACCOUNT_SUSPENSION_DELETION_STATUS" => "This user's suspension / deletion / logon cap has been edited.",
    "FEEDBACK_ACCOUNT_CANT_DELETE_SUSPEND_OWN" => "You can not delete or suspend your own account.",
    "FEEDBACK_ACCOUNT_USER_SUCCESSFULLY_KICKED" => "The selected user has been successfully kicked out of the system (by resetting this user's session)",
    "FEEDBACK_ACCOUNT_SUCCESSFULLY_CREATED" => "Your account has been created successfully and we have sent you an email (Don't forget to check your spam folder). Please click the VERIFICATION LINK within that mail; Problems? <a href='mailto:info@coursesuite.com.au'>email us</a> \n <a href='" . Config::get('URL')."register/reVerify'>Resend verification email</a>",
    "FEEDBACK_ACCOUNT_CREATION_FAILED" => "Sorry, your registration failed. Please go back and try again.",
    "FEEDBACK_ACCOUNT_VERIFIFICATION_RESENT" => "A verification mail has been resent (check your inbox or spam folder). You need to click the VERIFICATION LINK within that mail.",
    "FEEDBACK_ACCOUNT_ACTIVATION_SUCCESSFUL" => "Activation was successful! You can now log in.",
    "FEEDBACK_ACCOUNT_ACTIVATION_FAILED" => "Sorry, no such id/verification code combination here! It might be possible that your mail provider (Yahoo? Hotmail?) automatically visits links in emails for anti-scam scanning, so this activation link might been clicked without your action. Please try to log in on the main page.",
    "FEEDBACK_ACCOUNT_USAGE_CAP" => "Sorry, this account has reached its usage quota and can no longer be used.",

    "FEEDBACK_AVATAR_UPLOAD_SUCCESSFUL" => "Avatar upload was successful.",
    "FEEDBACK_AVATAR_UPLOAD_WRONG_TYPE" => "Only JPEG and PNG files are supported.",
    "FEEDBACK_AVATAR_UPLOAD_TOO_SMALL" => "Avatar source file's width/height is too small. Needs to be 100x100 pixel minimum.",
    "FEEDBACK_AVATAR_UPLOAD_TOO_BIG" => "Avatar source file is too big. 5 Megabyte is the maximum.",
    "FEEDBACK_AVATAR_FOLDER_DOES_NOT_EXIST_OR_NOT_WRITABLE" => "Avatar folder does not exist or is not writable. Please change this via chmod 775 or 777.",
    "FEEDBACK_AVATAR_IMAGE_UPLOAD_FAILED" => "Something went wrong with the image upload.",
    "FEEDBACK_AVATAR_IMAGE_DELETE_SUCCESSFUL" => "You successfully deleted your avatar.",
    "FEEDBACK_AVATAR_IMAGE_DELETE_NO_FILE" => "You don't have a custom avatar.",
    "FEEDBACK_AVATAR_IMAGE_DELETE_FAILED" => "Something went wrong while deleting your avatar.",

    "FEEDBACK_PASSWORD_RESET_TOKEN_FAIL" => "Could not write token to database.",
    "FEEDBACK_PASSWORD_RESET_TOKEN_MISSING" => "No password reset token.",
    "FEEDBACK_PASSWORD_RESET_MAIL_SENDING_ERROR" => "Password reset mail could not be sent due to: ",
    "FEEDBACK_PASSWORD_RESET_MAIL_SENDING_SUCCESSFUL" => "A password reset mail has been sent successfully.",
    "FEEDBACK_PASSWORD_RESET_LINK_EXPIRED" => "Your reset link has expired. Please use the reset link within one hour.",
    "FEEDBACK_PASSWORD_RESET_COMBINATION_DOES_NOT_EXIST" => "Username/Verification code combination does not exist.",
    "FEEDBACK_PASSWORD_RESET_LINK_VALID" => "Password reset validation link is valid. Please change the password now.",
    "FEEDBACK_PASSWORD_CHANGE_SUCCESSFUL" => "Password successfully changed.",
    "FEEDBACK_PASSWORD_CHANGE_FAILED" => "Sorry, your password changing failed.",
    "FEEDBACK_PASSWORD_NEW_SAME_AS_CURRENT" => "New password is the same as the current password.",
    "FEEDBACK_PASSWORD_CURRENT_INCORRECT" => "Current password entered was incorrect.",

    "FEEDBACK_ACCOUNT_TYPE_CHANGE_SUCCESSFUL" => "Account type change successful",
    "FEEDBACK_ACCOUNT_TYPE_CHANGE_FAILED" => "Account type change failed",

    "FEEDBACK_NOTE_CREATION_FAILED" => "Note creation failed.",
    "FEEDBACK_NOTE_EDITING_FAILED" => "Note editing failed.",
    "FEEDBACK_NOTE_DELETION_FAILED" => "Note deletion failed.",

    "FEEDBACK_COOKIE_INVALID" => "Your remember-me-cookie is invalid.",
    "FEEDBACK_COOKIE_LOGIN_SUCCESSFUL" => "You were successfully logged in via the remember-me-cookie.",

    "EMAIL_COMMON_CONTENT_INTRO" => "Hi!\n\n",
    "EMAIL_COMMON_CONTENT_SIG" => "\n\nCheers,\nThe CourseSuite Team\ninfo@coursesuite.com.au\n",
    "EMAIL_PASSWORD_RESET_CONTENT" => "Please click on this link to reset your password: ",
    "EMAIL_VERIFICATION_CONTENT" => "Please click on this link to activate your account: ",
    "EMAIL_TRIAL_VERIFICATION_CONTENT" => "Thank you for registering for a trial account. Click the link to activate your 14 day trial: ",

    "TIER_MATRIX_HEADER" => "<h4>One subscription <i class='cs-loader cs-super'></i>.</h4>
							 <h3>Multiple products.</h3>",
    "TIER_MATRIX_CAVEATS" => "<div class='fine-print'>
								<p><i class='cs-loader cs-super'></i> This product is part of a paid subscription that offers multiple
								products (<a href='" . Config::get('URL') . "store/tiers/NinjaSuite'>details</a>).
								Subscriptions are re-billed at the opted frequency until cancelled.
								Discounts are available for longer subscriptions.
								</p>
								<div class='text-center'><img src='/img/fastspring.png'></div>
							</div>",

    "TIER_MATRIX_CAVEATS_OLD" => "<p>This product is part of a paid subscription that offers multiple
								products (<a href='" . Config::get('URL') . "store/tiers/NinjaSuite'>details</a>).</p>
								<p>Subscriptions are charged monthly until cancelled.</p>
								<div class='text-center'><img src='/img/fastspring.png'></div>
							",

    // google captcha response error codes
    "missing-input-secret" => "The secret parameter is missing.",
    "invalid-input-secret" => "The secret parameter is invalid or malformed.",
    "missing-input-response" => "Apparently you're a robot. All hail our new robot overlords / Did you forget to tick the box (please choose one)", // The response parameter is missing.",
    "invalid-input-response" => "The verification images you chose didn't match. Try again?",

    //Mailchimp feedback
    "FEEDBACK_MAILCHIMP_UPDATE_FAILED" => "Something went wrong while updating your settings.",
    "FEEDBACK_MAILCHIMP_UPDATE_SUCCESS" => "Successfully update newsletter settings",
    "FEEDBACK_MAILCHIMP_UNSUBSCRIBE_FAILED" => "Something went wrong, failed to unscubscribe from mailing list",
    "FEEDBACK_MAILCHIMP_UNSUBSCRIBE_SUCCESS" => "Successfully unsubscribed from mailing list",

    "EXCEEDED_MONTHLY_CAP" => "Monthly usage cap exceeded",
    "MISSING_PARAMETERS" => "Missing expected parameters",

    "FREE_TRIAL_EXPIRED" => "Your free trial has expired. To continue using the apps, please purchase a subscription.",

    "CONTACT_FORM_OK" => "Your message has been sent! Thanks for your interest.",
    "CONTACT_FORM_SPAM" => "I have always wanted to meet a robot!",
    "REGISTRATION_DOMAIN_BLACKLISTED" => "Sorry, this domain has been blacklisted. Please email info@coursesuite.com.au to resolve this.",

);
