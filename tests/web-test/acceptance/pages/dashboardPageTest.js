describe('dashboardPageTest', function () {
    var common = require('./common.js');
    var okButtonName = 'Ok';

    var localStorage = {
        addAccount: function (val) {
            driver.executeScript("localStorage.setItem('accounts', '" + val + "')")
        },
        getAccount: function () {
            driver.executeScript("localStorage.getItem('accounts'")
        },
        removeAccount: function () {
            driver.executeScript("localStorage.removeItem('accounts'");
        }
    };

    var ptor = protractor.getInstance();
    var driver = ptor.driver;

    beforeEach(function () {
        ptor.get('/#!');
        common.cookies.addToken(ptor, '12345');
        localStorage.removeAccount();
    });

    it('checkAddAccountDialogWhenNoAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(common.getHeader(ptor, 4, 'Настройка аккаунта').isDisplayed()).toBeTruthy();
        expect(common.getHeader(ptor, 5, 'Пожалуйста, укажите название аккаунта - юридическое лицо, от имени которого совершаются действия').isDisplayed()).toBeTruthy();
        expect(ptor.findElement(protractor.By.input('account.name')).isDisplayed()).toBeTruthy();
    });

    it('checkNoAddAccountDialogWhenAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');
        localStorage.addAccount('{name: "someName"}');

        //Act & Verify
        expect(common.getHeader(ptor, 4, 'Настройка аккаунта').isDisplayed()).toBeFalsy();
        expect(common.getHeader(ptor, 5, 'Пожалуйста, укажите название аккаунта - юридическое лицо, от имени которого совершаются действия').isDisplayed()).toBeFalsy();
        expect(ptor.findElement(protractor.By.input('account.name')).isDisplayed()).toBeFalsy();
    });

    it('checkAddAccountWhenNoAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');
        var newAccountName = 'newAccountName';

        //Act & Verify
        ptor.findElement(protractor.By.input('account.name')).sendKeys(newAccountName);
        common.getButton(ptor, okButtonName).click();
        ptor.wait(function () {
            expect(localStorage.getAccount()).toBe('smt'); //TODO
            //TODO check DB
        }, 2000)
    });

});
