<?php
declare(strict_types=1);
namespace Plinct\Cms\View\Authentication;

use Plinct\Cms\CmsFactory;

class AuthFragment
{
  /**
   * @param array|null $data
   */
  public function login(array $data = null): string
  {
    $message = $data['message'] ?? null;
		return ($message ? "<p class='aviso'>" . _($message) . "</p>" : null) . self::formLogin();
  }

  /**
   * @param $data
   * @return string
   */
  public function register($data = null): string
  {
    $message = null;
    $form = self::formRegister();
    if ($data) {
      $message = $data['message'];
      if ($message === "Duplicate entry for key 'email'" || (isset($data['data']['error']['code']) && $data['data']['error']['code'] === '23000')) {
        return "<p class='warning'>"._("This email already exists in our database!") . "</p>" . self::formLogin();
      }
    }
    $warning = $message ? "<p class='aviso'>"._($message)."</p>" : null;
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
        $returns[] = CmsFactory::response()->message()->warning($response['message']);
      } elseif ($response['status'] == 'success') {
        return ["<p class='warning'>{$response['data']['mail']['message']}</p>"];
      }
    }
    $returns[] = self::resetPasswordForm($email);
    return $returns;
  }

  /**
   * @param $params
   * @param null $data
   * @return ?string
   */
  public function changePassword($params, $data = null): ?string
  {
    if (isset($data['status'])) {
      if ($data['status'] == 'fail') {
        if ($data['message'] == "Token invalid or date expired") {
          return "<p class='warning'>"._($data['message']).". "._("Try again!")."</p>" . self::resetPasswordForm();
        }
        if ($data['message'] == "Password does not equal repeat") {
          return "<p class='warning'>"._($data['message']).". "._("Try again!")."</p>" . self::changePasswordForm($params);
        }
        if ($data['message'] == "Password must be at least 8 characters in length and must contain at least one number, one upper case letter, one lower case letter and one special character") {
          return "<p class='warning'>".($data['message']).". "._("Try again!")."</p>" . self::changePasswordForm($params);
        }
      }
      if ($data['status'] == "success") {
        return "<p class='warning'>"._("Your password has been changed successfully!")."</p>" . self::formLogin();
      }
    }
    return self::changePasswordForm($params);
  }

  /**
   * @param $params
   * @return ?string
   */
  private static function changePasswordForm($params): ?string
  {
    if (isset($params['selector']) && isset($params['validator'])) {
      return "<form action='/admin/auth/change_password' method='post' class='form formPadrao form-authentication'>
        <h3>" . _("Change your password") . "</h3>
        <input type='hidden' name='selector' value='{$params['selector']}'>
        <input type='hidden' name='validator' value='{$params['validator']}'>
        <fieldset style='width: 100%;'>
          <legend>" . _("Password") . "</legend>
          <input name='password' type='password' placeholder='" . _("Enter your new password") . "' required>
        </fieldset>
        <fieldset style='width: 100%;'>
          <legend>" . _("Repeat password") . "</legend>
          <input name='repeatPassword' type='password' placeholder='" . _("Repeat the new password") . "' required>
        </fieldset>
        <input name='submit' type='submit' value='" . _("Send") . "'>        
      </form>";
    }
    return null;
  }

  /**
   * @param string|null $email
   * @return string
   */
  private static function resetPasswordForm(string $email = null): string
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
        <p><a href="/admin/auth/resetPassword">'._("Forgot password?").'</a></p>
      </div>
    </form>';
  }
}
