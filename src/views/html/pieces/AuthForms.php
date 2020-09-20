<?php

namespace Plinct\Cms\Views\Html\Pieces;

class AuthForms
{
    public static function startApplication($email, $password) {
        return '<form id="startApplication-form" action="/admin/startApplication" method="post" class="form formPadrao" onsubmit="return checkStartApplicationForm(this);">
            <h3>Start application</h3>    
            <fieldset style="width: 100%;">
                <legend>Name</legend>
                <label>
                    <input name="userAdmin" type="text" value=""/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Email</legend>
                <label>
                    <input name="emailAdmin" type="text" value="'.$email.'"/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Password</legend>
                <label>
                    <input name="passwordAdmin" type="password" value="'.$password.'"/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Repeat password</legend>
                <label>
                    <input name="passwordRepeat" type="password"/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Database name</legend>
                <label>
                    <input name="dbName" type="text"/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Database user name</legend>
                <label>
                    <input name="dbUserName" type="text"/>
                </label>
            </fieldset>
            <fieldset style="width: 100%;">
                <legend>Database password</legend>
                <label>
                    <input name="dbPassword" type="password"/>
                </label>
            </fieldset>
            <input value="Enviar" type="submit">
        </form>';
    }
}
