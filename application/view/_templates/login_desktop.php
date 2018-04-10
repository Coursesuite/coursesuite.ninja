	<div id="login-required" uk-modal <?php if ($this->Registration->show) {
		echo 'class="uk-modal-full uk-modal uk-open" style="display: block;"';
	} else {
		echo 'class="uk-modal-full"';
	} ?>>
	    <div class="uk-modal-dialog">
	        <button class="uk-modal-close-full uk-close-large" type="button" uk-close></button>
	        <div class="uk-grid-collapse uk-child-width-1-2@s uk-flex-middle" uk-grid>
	            <div class="cs-logon-background uk-visible@m uk-position-relative" uk-height-viewport>
    		        <img src="<?php echo $baseurl; ?>img/coursesuite-white.svg" class="uk-position-top-left uk-position-small">
	            </div>
	            <div class="uk-flex uk-flex-middle" uk-height-viewport>
		            <div class="uk-padding-large">
		                <h1>Register or Login here.</h1>
						<form method="post" class="uk-form-stacked">
							<input type="hidden" name="csrf_token" value="<?php echo $this->Registration->csrf_token; ?>">
							<div class="uk-margin">
								<label class="uk-form-label <?php echo $this->Registration->className; ?>" for="login-form-field"><?php echo $this->Registration->message; ?></label>
	<?php if ($this->Registration->sent) { ?>
						        <div class="uk-form-controls">
								    <div class="uk-inline">
								        <span class="uk-form-icon" uk-icon="icon: lock"></span>
							    	    <input name="onetimepassword" id="login-form-field" class="uk-input uk-width-large" type="password" placeholder="Your one-time password" required="required" value="" autocomplete="off">
								    </div>
								</div>
							</div>
							<div class="uk-margin">
								<div class="uk-form-controls">
									<input type="submit" class="uk-button uk-button-primary" value="Log in"> <a href="#" class="uk-button uk-button-link uk-modal-close uk-margin-left">Cancel</a>
								</div>
							</div>
	<?php } else { ?>
						        <div class="uk-form-controls">
								    <div class="uk-inline">
								        <span class="uk-form-icon" uk-icon="icon: mail"></span>
							    	    <input name="email" id="login-form-field" class="uk-input uk-width-large" type="email" placeholder="your.valid@email.address.com (required)" required="required" pattern="[a-z0-9!#$%&'*+/=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*" value="<?php echo $this->Registration->email; ?>" autocomplete="off">
								    </div>
								</div>
							</div>
							<div class="uk-margin">
								<div class="uk-form-controls">
									<input type="submit" class="uk-button uk-button-primary" value="Send password"> <a href="#" class="uk-button uk-button-link uk-modal-close uk-margin-left">Cancel</a>
								</div>
							</div>
	<?php } ?>
							<div class="uk-margin">
								<div class="uk-form-label"><a href="/content/terms">Terms &amp; Conditions</a></div>
							</div>
						</form>
		            </div>
		        </div>
	        </div>
	    </div>
	</div>