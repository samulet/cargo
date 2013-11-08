<?php
$I = new TestGuy($scenario);
$I->amOnPage('/');
$I->see('Zend Framework 2', 'span.zf-green');
