
{{ content() }}
<div class="login-or-signup">
    <div class="row">

        <div class="span6">
            <div class="page-header">
                <h2>Log In</h2>
            </div>
            {{ form('session/login', 'class': 'form-inline') }}
                <fieldset>
                    <div class="control-group">
                        <label class="control-label" for="login">Username/Email</label>
                        <div class="controls">
                            {{ text_field('login', 'size': "30", 'class': "input-xlarge") }}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="password">Password</label>
                        <div class="controls">
                            {{ password_field('password', 'size': "30", 'class': "input-xlarge") }}
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="remember_me">Remember me</label>
                            {{ check_field('rememberme', 'size': "3", 'class': "input-small") }}
                    </div>
                    <div class="form-actions">
                        <input type="button" id="login_btn" class="btn btn-primary btn-large" value="Login">
                    </div>
                </fieldset>
            </form>
        </div>

        <div class="span6">
            <div class="page-header">
                <h2>Don't have an account yet?</h2>
            </div>

            <p>Response:</p>
			<div id="response"></div>
            <div class="clearfix center">
                {{ link_to('session/register', 'Sign Up', 'class': 'btn btn-primary btn-large btn-success') }}
            </div>
            <div class="clearfix center">
                {{ link_to('session/logout', 'Sign out', 'class': 'btn btn-primary btn-large btn-success') }}
            </div>
        </div>

    </div>
</div>
