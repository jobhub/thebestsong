
{{ content() }}

<div class="page-header">
    <h2>Register for The Best Song</h2>
</div>

{{ form('session/register', 'id': 'registerForm', 'class': 'form-horizontal', 'onbeforesubmit': 'return false') }}
    <fieldset>
        <div class="control-group">
            <label class="control-label" for="first_name">Your First Name</label>
            <div class="controls">
                {{ text_field('firstname', 'class': 'input-xlarge') }}
                <p class="help-block">(required)</p>
                <div class="alert" id="name_alert">
                    <strong>Warning!</strong> Please enter your first name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="last_name">Your Last Name</label>
            <div class="controls">
                {{ text_field('lastname', 'class': 'input-xlarge') }}
                <p class="help-block">(required)</p>
                <div class="alert" id="name_alert">
                    <strong>Warning!</strong> Please enter your last name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">Email Address</label>
            <div class="controls">
                {{ text_field('email', 'class': 'input-xlarge') }}
                <p class="help-block">(required)</p>
                <div class="alert" id="email_alert">
                    <strong>Warning!</strong> Please enter your email
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="username">Username</label>
            <div class="controls">
                {{ text_field('login', 'class': 'input-xlarge') }}
                <p class="help-block">(required)</p>
                <div class="alert" id="username_alert">
                    <strong>Warning!</strong> Please enter your desired user name
                </div>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="password">Password</label>
            <div class="controls">
                {{ password_field('password', 'class': 'input-xlarge') }}
                <p class="help-block">(minimum 8 characters)</p>
                <div class="alert" id="password_alert">
                    <strong>Warning!</strong> Please provide a valid password
                </div>
            </div>
        </div>
        <p>By signing up, you accept terms of use and privacy policy.</p>
        <div class="form-actions">
            {{ submit_button('Register', 'class': 'btn btn-primary btn-large', 'onclick': 'return SignUp.validate();') }}
        </div>
    </fieldset>
</form>
