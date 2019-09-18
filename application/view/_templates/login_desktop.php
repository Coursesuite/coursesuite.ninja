	<div id="login-required" uk-modal <?php if ($this->Registration->show) {
		echo 'class="uk-modal-full uk-modal uk-open" style="display: block;"';
	} else {
		echo 'class="uk-modal-full"';
	} ?>>
	    <div class="uk-modal-dialog">
	        <button class="uk-modal-close-full uk-close-large cs-close" type="button" uk-close></button>
	        <div class="uk-grid-collapse uk-flex-middle" uk-grid>
	            <div class="cs-login-bg uk-visible@m uk-position-relative uk-width-1-3@s" uk-height-viewport>
    		        <img src="<?php echo $baseurl; ?>img/coursesuite-glyph-logo.svg" class="uk-position-center" width="200">
	            </div>
	            <div class="uk-flex uk-flex-middle uk-width-2-3@s" uk-height-viewport>
		            <div class="uk-padding-large">
				        <img src="<?php echo $baseurl; ?>img/coursesuite-black.svg" class="uk-hidden@m uk-margin-small">
<?php if (Config::get("FASTSPRING_CONTEXTUAL_STORE")) { ?>
		                <h1>Log in to access your account details.</h1>
		                <p>When you purchased an app we sent you some details - it might have been a Licence Key, an Order Number (e.g. COU123456-ABCD-1234), an API KEY (e.g. 84b906030d48527ce05c36820f94c9a3) or a password. Enter what you have in the box below and click Send Password and we'll give you a password. If you already have a password, use your email address and password to continue.</p>
<?php } else {  ?>
		                <h1>Register or Login here.</h1>
		                <?php echo $this->Registration->graph; ?>
		                <p>Register or Log below. If you don't yet have a password we'll send you one.</p>
<?php } ?>
						<form method="post" class="uk-form-stacked">
							<input type="hidden" name="csrf_token" value="<?php echo $this->Registration->csrf_token; ?>">
							<div class="uk-margin">
						        <div class="uk-form-controls">
								    <div class="uk-inline">
								        <span class="uk-form-icon" uk-icon="icon: mail"></span>
							    	    <input name="email" id="login-form-field-email" class="uk-input uk-width-large" type="text" placeholder="Licence Key, Order Number, Email or API KEY" required="required" old-pattern="[a-z0-9!#$%&'*+/=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*" value="<?php echo $this->Registration->email; ?>" autocomplete="off">
								    </div>
								</div>
							</div>
							<div class="uk-margin">
						        <div class="uk-form-controls">
								    <div class="uk-inline">
								        <span class="uk-form-icon" uk-icon="icon: lock"></span>
							    	    <input name="onetimepassword" id="login-form-field-password" class="uk-input uk-width-large" type="password" placeholder="Your password (leave blank if unknown)" value="" autocomplete="off">
								    </div>
								</div>
							</div>
							<div class="uk-margin">
								<label class="<?php echo $this->Registration->className; ?>" for="login-form-field"><?php echo $this->Registration->message; ?></label>
								<div class="uk-form-controls">
									<input type="submit" class="uk-button uk-button-primary" value="<?php echo ($this->Registration->sent) ? 'Log in':'Send password'; ?>" id="logon-form-submit"> <a href="#" class="uk-button uk-button-link uk-modal-close uk-margin-left">Cancel</a>
								</div>
							</div>
							<div class="uk-margin">
				            	<div class="uk-form-label">You'll remain logged on until you log off (uses a cookie).<div>
								<div class="uk-form-label"><a href="https://www.coursesuite.com/terms">Terms &amp; Conditions</a></div>
							</div>
						</form>
		            </div>
		        </div>
	        </div>
	    </div>
	</div>