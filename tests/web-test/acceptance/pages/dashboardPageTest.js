describe('dashboardPageTest', function () {

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

    var Cookies = {
        addToken: function () {
            ptor.manage().addCookie('token', '12345');
        },
        removeToken: function () {
            ptor.manage().deleteCookie('token');
        }
    };

    function getHeader(level, text) {
        return ptor.findElement(protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
    }

    function getButton(name) {
        return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
    }

    var ptor = protractor.getInstance();
    var driver = ptor.driver;

    beforeEach(function () {
        ptor.get('/#!');
        Cookies.addToken();
        localStorage.removeAccount();
    });

    it('checkAddAccountDialogWhenNoAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(getHeader(4, 'Настройка аккаунта').isDisplayed()).toBeTruthy();
        expect(getHeader(5, 'Пожалуйста, укажите название аккаунта - юридическое лицо, от имени которого совершаются действия').isDisplayed()).toBeTruthy();
        expect(ptor.findElement(protractor.By.input('account.name')).isDisplayed()).toBeTruthy();
    });

    it('checkNoAddAccountDialogWhenAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');
        localStorage.addAccount('{name: "someName"}');

        //Act & Verify
        expect(getHeader(4, 'Настройка аккаунта').isDisplayed()).toBeFalsy();
        expect(getHeader(5, 'Пожалуйста, укажите название аккаунта - юридическое лицо, от имени которого совершаются действия').isDisplayed()).toBeFalsy();
        expect(ptor.findElement(protractor.By.input('account.name')).isDisplayed()).toBeFalsy();
    });

    it('checkAddAccountWhenNoAccountsStored', function () {
        //Setup
        ptor.get('#!/dashboard');
        var newAccountName = 'newAccountName';

        //Act & Verify
        ptor.findElement(protractor.By.input('account.name')).sendKeys(newAccountName);
        getButton(okButtonName).click();
        ptor.wait(function () {
            expect(localStorage.getAccount()).toBe('smt'); //TODO
            //TODO check DB
        }, 2000)
    });

});