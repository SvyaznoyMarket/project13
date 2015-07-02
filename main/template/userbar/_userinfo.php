<?php
/**
 * @var $page           \View\DefaultLayout
 */
?>

<li class="userbtn_i topbarfix_log topbarfix_log-unlogin" data-bind="visible: !name()">
    <a href="/login" class="topbarfix_log_lk bAuthLink"><span class="topbarfix_log_tx">Вход</span></a>
</li>

<li class="userbtn_i topbarfix_log topbarfix_log-login js-topbarfixLogin" data-bind="visible: name()" style="display: none">
    <a href="" class="topbarfix_log_lk" data-bind="attr: { href: link }, css: {'ep-member': isEnterprizeMember}">
        <!--ko text: firstName--><!--/ko--> <!--ko text: lastName--><!--/ko-->
    </a>

    <div class="userbar-dd userbar-dd--account">
    	<ul class="user-account">
    		<li class="user-account__i">
	    		<a href="" class="user-account__lk" data-bind="attr: { href: link }">
		    		<i class="user-account__icon i-header i-header--person" data-bind="css: {'i-header--person-ep': isEnterprizeMember}"></i><span class="user-account__text undrl">Личный кабинет</span>
	    		</a>
    		</li>
    		<li class="user-account__i"><a href="<?= $page->url('user.favorites') ?>" class="user-account__lk"><i class="user-account__icon i-header i-header--wishlist"></i><span class="user-account__text undrl">Избранное</span></a></li>
    		<li class="user-account__i"><a href="<?= $page->url('user.logout') ?>" class="user-account__lk"><i class="user-account__icon"></i><span class="user-account__text undrl">Выйти</span></a></li>
    	</ul>
    </div>

    <!-- Favourite widget -->
    <div id="favourite-userbar-popup-widget"></div>

</li>
