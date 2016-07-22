<?php

/**
 * UserController
 * Controls everything that is user-related
 */
class UserController extends Controller
{
    /**
     * Construct this object by extending the basic Controller class.
     */
    public function __construct()
    {
        parent::__construct();

        // VERY IMPORTANT: All controllers/areas that should only be usable by logged-in users
        // need this line! Otherwise not-logged in users could do actions.
        Auth::checkAuthentication();
    }

    /**
     * Show user's PRIVATE profile
     */
    public function index() {

	    $model = array(
            'user_name' => Session::get('user_name'),
            'user_email' => Session::get('user_email'),
            'user_account_type' => Session::get('user_account_type'),
            'subscriptions' => SubscriptionModel::getAllSubscriptions(Session::get('user_id'), false, false, false, true),
            'baseurl' => Config::get('URL'),
            'products' => ProductModel::getAllSubscriptionProducts()
        );

        if (Config::get('USE_GRAVATAR')) {
	        $model["user_avatar"] = Session::get('user_gravatar_image_url');
	    } else {
		    $model["user_avatar"] = Session::get('user_avatar_file') . '?' . rand(1000,99999);
		    $model["edit_avatar"] = Config::get('URL') . 'user/editAvatar';
		}

        if (isset($model["subscriptions"]) && sizeof($model["subscriptions"]) > 0) {
            $fsprg = new FastSpring('coursesuite', Config::get('FASTSPRING_API_USER'), Config::get('FASTSPRING_API_PASSWORD'));
            try {
                $model["subscription_type"] = $fsprg->getSubscription($model['subscriptions'][0]->referenceId)->productName;
            } catch (Exception $e) {
                echo($e->getMessage());
            }
        }
        $this->View->renderHandlebars("user/myProfile", $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));

    }

    /**
     * Show edit-my-username page
     */
    public function editUsername()
    {
        $this->View->render('user/editUsername');
    }

    public function destroy()
    {
        $this->View->render('user/destroy');
    }

    public function destroy_action()
    {

        // check if csrf token is valid
        if (!Csrf::isTokenValid()) {
            LoginModel::logout();
            Redirect::home();
            exit();
        }

        if (Request::post("confirm_destroy") !== "delete me forever") {
	        Session::add('feedback_negative', Text::get('FEEDBACK_NO_DESTROY'));
	        Redirect::to("user/destroy");
	        exit;
        }

        LoginModel::logout();
        UserModel::destroyUserForever(Session::get('user_id'));
        Redirect::home();
        exit();

    }

    /**
     * Edit user name (perform the real action after form has been submitted)
     */
    public function editUsername_action()
    {
        // check if csrf token is valid
        if (!Csrf::isTokenValid()) {
            LoginModel::logout();
            Redirect::home();
            exit();
        }

        UserModel::editUserName(Request::post('user_name'));
        MailChimp::updateUserInfo(Session::get('user_email'), Request::post('user_name'), NULL, 'subscribed');
        Redirect::to('user/editUsername');
    }

    /**
     * Show edit-my-user-email page
     */
    public function editUserEmail()
    {
        $this->View->render('user/editUserEmail');
    }

    /**
     * Edit user email (perform the real action after form has been submitted)
     */
    // make this POST
    public function editUserEmail_action()
    {
        UserModel::editUserEmail(Request::post('user_email'));
        Redirect::to('user/editUserEmail');
    }

    /**
     * Edit avatar
     */
    public function editAvatar()
    {
        $this->View->render('user/editAvatar', array(
            'avatar_file_path' => AvatarModel::getPublicUserAvatarFilePathByUserId(Session::get('user_id'))
        ));
    }

    /**
     * Perform the upload of the avatar
     * POST-request
     */
    public function uploadAvatar_action()
    {
        AvatarModel::createAvatar();
        Redirect::to('user/editAvatar');
    }

    /**
     * Delete the current user's avatar
     */
    public function deleteAvatar_action()
    {
        AvatarModel::deleteAvatar(Session::get("user_id"));
        Redirect::to('user/editAvatar');
    }

    /**
     * Show the change-account-type page
     */
    public function changeUserRole()
    {
        $this->View->render('user/changeUserRole');
    }

    /**
     * Perform the account-type changing
     * POST-request
     */
    public function changeUserRole_action()
    {
        if (Request::post('user_account_upgrade')) {
            // "2" is quick & dirty account type 2, something like "premium user" maybe. you got the idea :)
            UserRoleModel::changeUserRole(2);
        }

        if (Request::post('user_account_downgrade')) {
            // "1" is quick & dirty account type 1, something like "basic user" maybe.
            UserRoleModel::changeUserRole(1);
        }

        Redirect::to('user/changeUserRole');
    }

    /**
     * Password Change Page
     */
    public function changePassword()
    {
        $this->View->render('user/changePassword');
    }

    /**
     * Password Change Action
     * Submit form, if retured positive redirect to index, otherwise show the changePassword page again
     */
    public function changePassword_action()
    {
        $result = PasswordResetModel::changePassword(
            Session::get('user_name'), Request::post('user_password_current'),
            Request::post('user_password_new'), Request::post('user_password_repeat')
        );

        if($result)
            Redirect::to('user/index');
        else
            Redirect::to('user/changePassword');
    }

    /**
     * Update newsletter subscription details page
     */
    public function changeNewsletterSubscription()
    {
        $model = array(
            'baseurl' => Config::get('URL'),
            'user_subbed' => MailChimp::isUserSubscribed(Session::get('user_email')),
            'list_interests' => MailChimp::getListInterests(),
            'user_interests' => MailChimp::getUserInterests(Session::get('user_email'))
        );
        $model['list_ids'] = array();
        foreach (MailChimp::getListInterests() as $toplist) {
            array_push($model['list_ids'], $toplist[1]);
        }
        $this->View->renderHandlebars('user/changeNewsletterSubscription', $model, "_templates", Config::get('FORCE_HANDLEBARS_COMPILATION'));
    }

    /**
     * Newsletter subscription settings change action
     *
     * Checks which interests catagories you have selected and updates you on the mailchimp mailinglist
     */
    public function changeNewsletterSubscription_action()
    {
        // Unsubscribe from newsletter
        if ($_POST['subscription'] == 'false'){
            if (MailChimp::unsubscribe(Session::get('user_email'))){
                Session::add('feedback_positive', Text::get('FEEDBACK_MAILCHIMP_UNSUBSCRIBED_SUCCESSFUL'));
                Redirect::to('user/index');
            }
            else{
                Session::add('feedback_negative', Text::get('FEEDBACK_MAILCHIMP_UNSUBSCRIBED_FAILED'));
                Redirect::to('user/changeNewsletterSubscription');
            }
        }
        // Subscribe / update interests
        elseif ($_POST['subscription'] == 'true'){
            // Check if user is subbed or not (only really needed for people who have never subbed)
            if (!MailChimp::isUserSubscribed(Session::get('user_email'))){
                MailChimp::subscribe(Session::get('user_email'), Session::get('user_name'));
            }

            $newInterests = array();
            // Remake users interests array based on selections
            for ($i = 0; $i < count(MailChimp::getListInterests()); $i++){
                if(strpos($_POST['interestCheck'.$i], 'false') !== false){
                    $key = str_replace('false', '', $_POST['interestCheck'.$i]);
                    $newInterests[$key] = false;
                }
                else{
                    $newInterests[$_POST['interestCheck'.$i]] = true;
                }
            }

            if (MailChimp::updateUserInfo(Session::get('user_email'), NULL, $newInterests)){
                Session::add('feedback_positive', Text::get('FEEDBACK_MAILCHIMP_UPDATE_SUCCESS')); //Not sure how to make this work
                Redirect::to('user/index');
            }
            else{
                Session::add('feedback_negative', Text::get('FEEDBACK_MAILCHIMP_UPDATE_FAILED')); //Not sure how to make this work
                Redirect::to('user/changeNewsletterSubscription');
            }

        }
    }

}
