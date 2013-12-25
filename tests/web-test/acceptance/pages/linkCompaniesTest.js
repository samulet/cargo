describe('linkCompaniesTest', function () {
    var common = require('./common.js');

    var ptor = protractor.getInstance();

    beforeEach(function () {
        common.cookies.addToken(ptor, '12345');
        //TODO add headers
        ptor.get('office#!/dashboard');
    });

    it('checkLinkCompaniesMenuItemDisplayed', function () {
        //Setup

        //Act & Verify

    });

    it('checkLinkCompaniesPopup', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultImportSelectValueWhenImportedCompaniesExist', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultImportSelectValueWhenImportedCompaniesNotExist', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultExistedSelectValueWhenExistedCompaniesExist', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultExistedSelectValueWhenExistedCompaniesNotExist', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultLinkedSelectValue', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultLinkedSelectValueWhenChooseCompanyWithLinks', function () {
        //Setup

        //Act & Verify

    });

    it('checkDefaultLinkedSelectValueWhenChooseCompanyWithNoLinks', function () {
        //Setup

        //Act & Verify

    });

    it('checkLinkCompanies', function () {
        //Setup

        //Act & Verify

    });

    it('checkCreateNewCompany', function () {
        //Setup

        //Act & Verify

    });

    it('checkUnlinkCompanies', function () {
        //Setup

        //Act & Verify

    });


});
