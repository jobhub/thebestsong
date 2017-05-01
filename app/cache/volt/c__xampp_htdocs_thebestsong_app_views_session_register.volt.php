
<?php echo $this->getContent(); ?>

<div class="page-header">
    <h2>Register for The Best Song</h2>
</div>

<?php echo $this->tag->form(array('session/register', 'id' => 'registerForm', 'class' => 'form-horizontal', 'onbeforesubmit' => 'return false')); ?>
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="first_name">Your First Name</label>
            <div class="controls">
                <?php echo $this->tag->textField(array('firstname', 'class' => 'input-xlarge')); ?>
                <p class="help-block">(required)</p>
                <div class="alert" id="name_alert">
                    <strong>Warning!</strong> Please enter your first name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="last_name">Your Last Name</label>
            <div class="controls">
                <?php echo $this->tag->textField(array('lastname', 'class' => 'input-xlarge')); ?>
                <p class="help-block">(required)</p>
                <div class="alert" id="name_alert">
                    <strong>Warning!</strong> Please enter your last name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">Email Address</label>
            <div class="controls">
                <?php echo $this->tag->textField(array('email', 'class' => 'input-xlarge')); ?>
                <p class="help-block">(required)</p>
                <div class="alert" id="email_alert">
                    <strong>Warning!</strong> Please enter your email
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="username">Username</label>
            <div class="controls">
                <?php echo $this->tag->textField(array('login', 'class' => 'input-xlarge')); ?>
                <p class="help-block">(required)</p>
                <div class="alert" id="username_alert">
                    <strong>Warning!</strong> Please enter your desired user name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="password">Password</label>
            <div class="controls">
                <?php echo $this->tag->passwordField(array('password', 'class' => 'input-xlarge')); ?>
                <p class="help-block">(minimum 8 characters)</p>
                <div class="alert" id="password_alert">
                    <strong>Warning!</strong> Please provide a valid password
                </div>
            </div>
        </div>
        <p>By signing up, you accept terms of use and privacy policy.</p>
        <div class="form-actions">
            <?php echo $this->tag->submitButton(array('Register', 'class' => 'btn btn-primary btn-large', 'onclick' => 'return SignUp.validate();')); ?>
        </div>
    </fieldset>
</form>
