<?php
$I = new TestGuy($scenario);
$I->amOnPage('/user/register');
$I->see('Register', '.container h1');

$I->fillField('email', 'oleg@lobach.info');
$I->fillField('display_name', 'Oleg Lobach');
$I->fillField('password', '123456');
$I->fillField('passwordVerify', '123456');
$I->click('Register');

$I->see('Oleg Lobach', 'h1');