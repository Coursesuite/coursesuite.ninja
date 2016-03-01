<div class="container">
    <h1>
        Verification
    </h1>
    <?php $this->renderFeedbackMessages(); ?>
    <div class="login-page-box">
        <div class="table-wrapper">
            <div class="login-box">
                <form action="<?php echo Config::get('URL'); ?>login/login" method="get">
                    <input type="submit" class="login-submit-button" value="Log in"/>
                </form>
            </div>
        </div>
    </div>
</div>