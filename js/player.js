var Player = {
    
    validate: function(form) {
        
        var action = $(form).find("[name='action']").val();
        var $username = $(form).find("[name='username']");
        var $password = $(form).find("[name='password']");
        var $repeatPassword = $(form).find("[name='repeat_password']");
        
        var message = '';
        var $focusElement = false;
        if ($username.val().trim() == '') {
            message += ' Username is required! ';
            $focusElement = $username;
        } else if ($username.val().trim().length > 255) {
            message += ' Username can have max 255 characters! ';
            $focusElement = $username;
        }
        if ($password.val().trim() == '') {
            if (action != 'profile') {
                message += ' Password is required! ';
                if (!$focusElement) {
                    $focusElement = $password;
                }
            }
        } else {
            if ($repeatPassword.length > 0 && $password.val() != $repeatPassword.val()) {
                message += ' Passwords do not match! ';
                if (!$focusElement) {
                    $focusElement = $repeatPassword;
                }
            }
        }
        
        if (action == 'password_forgotten') {
            var $email = $(form).find("[name='email']");
            if ($email.val().trim() == '') {
                message += ' Email is required! ';
                if (!$focusElement) {
                    $focusElement = $email;
                }
            }
        }
        
        if (message) {
            //Util.showMessage(message);
            Util.showMessage(action, message);
            if ($focusElement) {
                $focusElement.focus();
            }
            return false;
        } else {
            return true;
        }
    },
    
    statusChangeCallback: function(response) {
        if (response.status === 'connected') {
            FB.api('/me', function(response) {
                console.log(response);
                $.ajax({
                    url: 'index.php?action=fb_login',
                    type: 'POST',
                    dataType: 'json',
                    data: response,
                    success: function(resp) {
                        if (resp) {
                            if (resp.session_id) {
                                document.cookie = 'PHPSESSID=' + resp.session_id;
                                location.reload();
                            } else if (resp.message) {
                                Util.showMessage(resp.message);
                            }
                        } else {
                            Util.showMessage('An error occurred while logging! Please try again.');
                        }
                    },
                    error: function() {
                        Util.showMessage('An error occurred while logging! Please try again.');
                    }
                });                
            });
        }
    },
    
    checkLoginState: function() {
        FB.getLoginStatus(function(response) {
            Player.statusChangeCallback(response);
        });
    },
    
    fbLogout: function(e, button) {
        e.preventDefault();
        FB.getLoginStatus(function(response) {
            if (response.status === 'connected') {
                FB.logout(function(response) {
                    if (response.status && response.status !== 'connected') {
                        $(button).closest('form').submit();
                    }
                });
            }
        });
    }
    
};