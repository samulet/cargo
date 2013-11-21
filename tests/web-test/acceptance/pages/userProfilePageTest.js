describe('userProfilePageTest', function () {
    /*var dbConnector = require('../utils/dbConnector.js');
     dbConnector.connectDb();
     var mongoose = dbConnector.mongoose();*/

    const editBtnName = 'Редактировать';
    const cancelBtnName = 'Отменить';
    const personalDataHeaderText = 'Личные данные';
    const passportDataHeaderText = 'Паспортные данные';
    const otherDataHeaderText = 'Прочие данные';
    const phonesDataHeaderText = 'Телефоны';
    const addressesDataHeaderText = 'Почтовые адреса';
    const emailsDataHeaderText = 'Email';
    const sitesDataHeaderText = 'Сайты';
    const photoDataHeaderText = 'Фотография профиля';
    const signDataHeaderText = 'Цифровая подпись';
    const socialDataHeaderText = 'Аккаунты соцальных сетей';

    function getButton(name) {
        return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
    }

    function getHeader(level, text) {
        return ptor.findElement(protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
    }

    var ptor = protractor.getInstance();

    beforeEach(function () {
        ptor.get('#!/user/profile');
    });


    it('checkDefaultPageView', function () {
        //Setup

        //Act & Verify
        var editBtn = getButton(editBtnName);
        expect(editBtn.isDisplayed()).toBe(true);
        expect(editBtn.isEnabled()).toBe(true);

        expect(getHeader(3, personalDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, personalDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, passportDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, otherDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, phonesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, addressesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, emailsDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, sitesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, photoDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, signDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, socialDataHeaderText).isDisplayed()).toBe(false);

    });

    it('checkSwitchEditMode', function () {
        //Setup

        //Act & Verify
        var editBtn = getButton(editBtnName);
        editBtn.click();

        expect(getHeader(3, personalDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, personalDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, passportDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, otherDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, phonesDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, addressesDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, emailsDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, sitesDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, photoDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, signDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, socialDataHeaderText).isDisplayed()).toBe(true);

        var cancelBtn = getButton(cancelBtnName);
        cancelBtn.click();

        expect(getHeader(3, personalDataHeaderText).isDisplayed()).toBe(true);
        expect(getHeader(4, personalDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, passportDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, otherDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, phonesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, addressesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, emailsDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, sitesDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, photoDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, signDataHeaderText).isDisplayed()).toBe(false);
        expect(getHeader(4, socialDataHeaderText).isDisplayed()).toBe(false);

    });

    it('checkPhoneBlockDefaultState', function () {
        //Setup

        //Act & Verify
        getButton(editBtnName).click();

    });

    /*it('checkPhoneBlockVisibleSwitch', function() {
     var form = ptor.findElement(protractor.By.name('phonesDataForm'));
     form.findElement(protractor.By.name("showAddPhoneForm")).click();
     //expect(message.getText()).toEqual('Hello!');
     });*/
});