<?php

declare(strict_types=1);

namespace Plinct\Cms\WebSite\Fragment\User;

class UserFragment
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
     * @return string
     */
    private static function formLogin(): string
    {
        return '<form action="/admin/login" method="post" class="form formPadrao">
            <h3>Entrar</h3>
            <fieldset style="width: 100%;">
                <legend>Email</legend>
                <input name="email" type="email">
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Senha</legend>
                <input name="password" type="password">
            </fieldset>
            <input name="submit" type="submit" value="Enviar">
            <p><a href="/admin/register">Fazer novo registro</a></p>
        </form>';
    }

    /**
     * @return string
     */
    private static function formRegister(): string
    {
        return '<form id="register-form" action="/admin/register" method="post" class="form formPadrao" onsubmit="return checkRegisterForm(this);">
            <h3>Novo registro de usuário</h3>
            <fieldset style="width: 100%;">
                <legend>Nome</legend>
                <input name="name" type="text">
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Email</legend>
                <input name="email" type="email">
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Senha</legend>
                <input name="password" type="password">
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Repita a senha</legend>
                <input name="passwordRepeat" type="password">
            </fieldset>
            <input name="submit" type="submit" value="Enviar">
        </form>';
    }
}
