/*
describe('userProfilePageTest', function () {

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

    var ptor = protractor.getInstance();

    beforeEach(function () {
        Cookies.removeToken();
    });

    it('checkNoRedirectWhenTokenNotExistAndSignInPage', function () {
        //Setup
        ptor.get('#!/sign/in');

        //Act & Verify
        expect(getHeader(1, 'Some inspiration wow-text.').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenExistAndSignInPage', function () {
        //Setup
        Cookies.addToken();

        ptor.get('#!/sign/in');

        //Act & Verify
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"Some inspiration wow-text.")]'))).toBeFalsy();
        expect(getHeader(3, 'dashboard success').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenExistAndDashboardPage', function () {
        //Setup
        Cookies.addToken();
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"Some inspiration wow-text.")]'))).toBeFalsy();
        expect(getHeader(3, 'dashboard success').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenNotExistAndDashboardPage', function () {
        //Setup
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(getHeader(1, 'Some inspiration wow-text.').isDisplayed()).toBeTruthy();
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"dashboard success")]'))).toBeFalsy();
    });
});*/
