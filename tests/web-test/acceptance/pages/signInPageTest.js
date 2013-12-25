describe('userProfilePageTest', function () {
    var common = require('./common.js');
    var ptor = protractor.getInstance();

    beforeEach(function () {
        common.cookies.removeToken(ptor);
    });

    it('checkNoRedirectWhenTokenNotExistAndSignInPage', function () {
        //Setup
        ptor.get('#!/sign/in');

        //Act & Verify
        expect(common.getHeader(ptor, 1, 'Some inspiration wow-text.').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenExistAndSignInPage', function () {
        //Setup
        common.cookies.addToken(ptor, '12345');

        ptor.get('#!/sign/in');

        //Act & Verify
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"Some inspiration wow-text.")]'))).toBeFalsy();
        expect(common.getHeader(ptor, 3, 'dashboard success').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenExistAndDashboardPage', function () {
        //Setup
        common.cookies.addToken(ptor, '12345');
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"Some inspiration wow-text.")]'))).toBeFalsy();
        expect(common.getHeader(ptor, 3, 'dashboard success').isDisplayed()).toBeTruthy();
    });

    it('checkRedirectWhenTokenNotExistAndDashboardPage', function () {
        //Setup
        ptor.get('#!/dashboard');

        //Act & Verify
        expect(common.getHeader(ptor, 1, 'Some inspiration wow-text.').isDisplayed()).toBeTruthy();
        expect(ptor.isElementPresent(protractor.By.xpath('//h1[contains(.,"dashboard success")]'))).toBeFalsy();
    });
});
