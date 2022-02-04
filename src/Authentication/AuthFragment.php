<?php

declare(strict_types=1);

namespace Plinct\Cms\Authentication;

use Plinct\Cms\WebSite\Fragment\Fragment;

class AuthFragment
{
    /**
     * @param array|null $authentication
     * @return string
     */
    public function login(array $authentication = null): string
    {
        $message = $authentication['message'] ?? null;
        return ($message ? "<p class='aviso'>" . _($message) . "</p>" : null) . self::formLogin();
    }

    /**
     * @param $authentication
     * @return string
     */
    public function register($authentication = null): string
    {
        $message = null;

        $form = self::formRegister();

        if ($authentication) {
            switch ($authentication) {
                case 'repeatPasswordNotWork':
                    $message = _("Registration not successful!") . "<br>" . _("Repeating the password doesn't work!");
                    break;
                case "emailExists":
                    $message = _("Registration not successful!") . "<br>" . _("This email already exists in our database!");
                    break;
                case "userAdded":
                    $message = _("Your registration was successful!") . "<br>" . _("Wait for confirmation from the administrator!");
                    $form = self::formLogin();
                    break;
                case "error":
                    $message = _("Registration not successful!") . "<br>" . _("Sorry! Something is wrong!");
                    break;
                default:
                    $message = _("Registration error!") . "<br>" . _($authentication);
            }
        }

        $warning = $message ? "<p class='aviso'>$message</p>" : null;

        return $warning . $form;
    }

    /**
     * @param array|null $response
     * @param string|null $email
     * @return array
     */
    public function resetPassword(array $response = null, string $email = null): array
    {
        $returns = null;

        if ($response) {
            if ($response['status'] == 'fail') {
                $returns[] = Fragment::noContent($response['message']);
            } elseif ($response['status'] == 'success') {
                return Fragment::noContent(sprintf(_("An email has been sent to %s"), $email));
            }
        }

        $returns[] = self::resetPasswordForm($email);

        return $returns;
    }

    /**
     * @param string|null $email
     * @return string
     */
    public static function resetPasswordForm(string $email = null): string
    {
        return "<form action='/admin/auth/resetPassword' class='form formPadrao form-authentication' method='post'>
            <h3>"._("Reset password")."</h3>
            <fieldset style='width: 100%;'>
                <legend>Email</legend>
                <input name='email' type='email' value='$email' placeholder='"._("Enter your email")."' required/>
            </fieldset>
            <input name='submit' type='submit' value='"._("Send")."'>
            <div class='form-authentication-actions'>
                <p><a href='/admin/auth/login'>"._("Sign in")."</a></p>
                <p><a href='/admin/auth/register'>"._("Sign on")."</a></p>
            </div>
            
        </form>";
    }

    /**
     * @return string
     */
    private static function formLogin(): string
    {
        return "<form action='/admin/login' method='post' class='form formPadrao form-authentication'>
            <h3>"._("Log in")."</h3>
            <fieldset style='width: 100%;'>
                <legend>Email</legend>
                <input name='email' type='email' required>
            </fieldset>
            <fieldset style='width: 100%;'>
                <legend>"._("Password")."</legend>
                <input name='password' type='password' required>
            </fieldset>
            <input name='submit' type='submit' value='"._("Send")."'>
            <div class='form-authentication-actions'>
                <p><a href='/admin/auth/register'>"._("Sign on")."</a></p>
                <p><a href='/admin/auth/resetPassword'>"._("Forgot password?")."</a></p>
            </div>
        </form>";
    }

    /**
     * @return string
     */
    private static function formRegister(): string
    {
        return '<form id="register-form" action="/admin/auth/register" method="post" class="form formPadrao form-authentication" onsubmit="return checkRegisterForm(this);">
            <h3>'._("Sign on").'</h3>
            <fieldset style="width: 100%;">
                <legend>'._("Name").'</legend>
                <input name="name" type="text" required>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Email</legend>
                <input name="email" type="email" required>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>'._("Password").'</legend>
                <input name="password" type="password" required>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>'._("Repeat password").'</legend>
                <input name="passwordRepeat" type="password" required>
            </fieldset>
            <input name="submit" type="submit" value="'._("Send").'">
            <div class="form-authentication-actions">
                <p><a href="/admin/auth/login">'._("Sign in").'</a></p>
                <!--<p><a href="/admin/auth/resetPassword">'._("Forgot password?").'</a></p>-->
            </div>
        </form>';
    }


}
