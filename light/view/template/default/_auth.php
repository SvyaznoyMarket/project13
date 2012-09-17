<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="popupbox width694">
        <h2 class="pouptitle">Вход в Enter</h2>
        <div class="registerbox">


            <form id="reset-pwd-form" style="display: none;" action="/request-password" class="form" method="post">
                <div class="fl width327 mr20">

                    <div class="font16 pb20">Восстановление пароля:</div>

                    <div class="pb5">Введите e-mail или мобильный телефон, который использовали при регистрации, и мы пришлем вам пароль.</div>
                    <div class="error_list"></div>
                    <div class="pb5"><input name="login" type="text" class="text width315 mb10" value="" /></div>
                    <input type="submit" class="fr button whitebutton" value="Отправить запрос" />
                    <div class="clear pb10"></div>

                    Если вы вспомнили пароль, то вам надо лишь<br /><strong><a id="remember-pwd-trigger" href="javascript:void(0)" class="orange underline">войти в систему</a></strong>.

                </div>
            </form>
            <!--  <form id="reset-pwd-key-form" style="display: none;" action="/reset-password" class="form" method="post">
              <div class="fl width327 mr20">
                  <div class="font16 pb20">Восстановление пароля:</div>
                  <div class="pb5">Введите ключ, который был вам выслан по почте или смс.</div>
                  <div class="error_list"></div>
                  <div class="pb5"><input name="token" type="text" class="text width315 mb10" value="" /></div>
                  <input type="submit" class="fr button whitebutton" value="Отправить запрос" />
                  <div class="clear pb10"></div>
                  Если вы вспомнили пароль, то вам надо лишь<br /><strong><a id="remember-pwd-trigger2" href="javascript:void(0)" class="orange underline">войти в систему</a></strong>.
              </div>
            </form>-->

            <form id="login-form" action="/login" class="form" method="post">
                <input type="hidden" name="redirect_to" value="http://enter3.ent3.ru/catalog/appliances/" />
                <div class="fl width327 mr20">
                    <div class="font16 pb20">У меня есть логин и пароль</div>

                    <div class="pb5">E-mail или мобильный телефон:</div>
                    <div class="pb5">
                        <input type="text" name="signin[username]" class="text width315 mb10" id="signin_username" />      <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
                    </div>

                    <div class="pb5"><a id="forgot-pwd-trigger" href="/request-password" class="fr orange underline">Забыли пароль?</a>Пароль:</div>
                    <div class="pb5">
                        <input type="password" name="signin[password]" class="text width315 mb10" id="signin_password" />      <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
                    </div>

                    <input type="submit" class="fr button bigbutton" value="Войти" tabindex="4" />

                    <!--
                    <div class="ml20 pt10">
                      <label for="checkbox-8" class="prettyCheckbox checkbox list"><span class="holderWrap" style="width: 13px; height: 13px;"><span class="holder" style="width: 13px;"></span></span>Запомнить меня на этом компьютере</label>
                          </div>
                    -->

                </div>
            </form>
            <form id="register-form" action="/register" class="form" method="post">
                <input type="hidden" name="redirect_to" value="http://enter3.ent3.ru/catalog/appliances/" />
                <div class="fr width327 ml20">
                    <div class="font16 pb20">Я новый пользователь</div>
                    <div class="pb5">Как к вам обращаться?</div>
                    <div class="pb5">
                        <input tabindex="5" type="text" name="register[first_name]" class="text width315 mb10" id="register_first_name" />      <!--<input type="text" class="text width315 mb10" value="ivanov@domen.com" />-->
                    </div>
                    <div class="pb5">E-mail или мобильный телефон:</div>
                    <div class="pb5">
                        <input tabindex="6" type="text" name="register[username]" class="text width315 mb10" id="register_username" />      <!--<input type="password" class="text width315 mb10" value="Пароль" />-->
                    </div>
                    <input type="submit" class="fr button bigbutton" value="Регистрация" tabindex="10" />

                </div>
            </form>
            <div class="clear"></div>
        </div>
    </div>
    <div class="shareline">
        <div class="fl">Войти на сайт через:</div>

        <ul class="backetsharelist">
            <li>
                <a id="open_auth_vkontakte-link" class="open_auth-link vkontakte" href="/login/vkontakte" target="_blank">ВКонтакте</a>
            </li>
            <li>
                <a id="open_auth_facebook-link" class="open_auth-link facebook" href="/login/facebook" target="_blank">Facebook</a>
            </li>
            <li>
                <a id="open_auth_twitter-link" class="open_auth-link twitter" href="/login/twitter" target="_blank">Twitter</a>
            </li>
            <li>
                <a id="open_auth_odnoklassniki-link" class="open_auth-link odnoklassniki" href="/login/odnoklassniki" target="_blank">Odnoklassniki.ru</a>
            </li>
            <li>
                <a id="open_auth_mailru-link" class="open_auth-link mailru" href="/login/mailru" target="_blank">Mail.ru</a>
            </li>
        </ul>    <div class="clear"></div>
    </div>
</div>
