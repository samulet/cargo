/*
describe('userProfilePageTest', function () {
    */
/*var dbConnector = require('../utils/dbConnector.js');
     dbConnector.connectDb();
     var mongoose = dbConnector.mongoose();*//*


    const addBtnName = 'Добавить';
    const editBtnName = 'Редактировать';
    const cancelBtnName = 'Отменить';

    const personalDataBlock = {
        headerText: 'Личные данные',
        formName: 'personalDataForm',
        lastName: 'Фамилия',
        firstName: 'Имя',
        middleName: 'Отчество',
        uniqId: 'Уникальный номер',
        shortName: ' Краткое имя',
        docType: 'Вид документа'
    };

    const passportDataBlock = {
        headerText: 'Паспортные данные',
        formName: 'passportDataForm',
        series: 'Серия',
        number: 'Номер',
        issue: 'Дата выдачи',
        issuingAuthority: 'Орган выдавший',
        unitNumber: 'Номер подразделения',
        birthPlace: 'Место рождения',
        birthDate: 'Дата рождения',
        registrationType: 'Вид регистрации',
        registrationPlace: 'Место регистрации',
        registrationDate: 'Дата регистрации'
    };

    const miscDataBlock = {
        headerText: 'Прочие данные',
        formName: 'miscDataForm',
        snils: 'СНИЛС',
        inn: 'ИНН'
    };

    const photoDataBlock = {
        headerText: 'Фотография профиля',
        formName: 'photoDataForm'
    };

    const eSignatureDataBlock = {
        headerText: 'Цифровая подпись',
        formName: 'eSignatureDataForm',
        exist: 'Имеется цифровая подпись',
        validity: 'Срок действия подписи',
        qualification: 'Квалификация',
        powers: 'Полномочия'
    };

    const phonesBlock = {
        headerText: 'Телефоны',
        formName: 'phonesDataForm',
        phoneType: 'Вид телефона',
        countryCode: 'Код страны',
        cityCode: 'Код города',
        number: 'Номер телефона',
        additionalNumber: 'Добавочный'
    };

    const addressBlock = {
        formName: 'adressDataForm',
        headerText: 'Почтовые адреса',
        addressType: 'Вид адреса',
        addressCountry: 'Страна',
        addressIndex: 'Индекс',
        addressRegion: 'Регион',
        addressCity: 'Город',
        addressArea: 'Район',
        addressStreet: 'Улица',
        addressHouse: 'Дом',
        addressHousing: 'Корпус',
        addressApartment: 'Квартира'
    };

    const emailBlock = {
        formName: 'emailDataForm',
        headerText: 'Email',
        emailType: 'Вид Email',
        emailAddress: 'Email'
    };

    const sitesBlock = {
        formName: 'sitesDataForm',
        headerText: 'Сайты',
        sitesType: 'Тип сайта',
        sitesUrl: 'Адрес'
    };

    const socialDataBlock = {
        headerText: 'Аккаунты соцальных сетей'
    };

    function getButton(name) {
        return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
    }

    function getHeader(level, text) {
        return ptor.findElement(protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
    }

    function openHiddenBlock(formName) { //TODO fix replace with waitForVisible
        ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + addBtnName + '")]')).click();

    }

    function closeHiddenBlock(formName) {
        ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + cancelBtnName + '")]')).click();
    }

    function getFormLabelDisplay(formName, labelText) {
        return ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//label[contains(.,"' + labelText + '")]'));
    }

    function checkSitesBlockVisibleSwitch(isVisible) {
        expect(getFormLabelDisplay(sitesBlock.formName, sitesBlock.sitesType).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(sitesBlock.formName, sitesBlock.sitesUrl).isDisplayed()).toBe(isVisible);

    }

    function checkEmailBlockDisplay(isVisible) {
        expect(getFormLabelDisplay(emailBlock.formName, emailBlock.emailType).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(emailBlock.formName, emailBlock.emailAddress).isDisplayed()).toBe(isVisible);

    }

    function checkAddressBlockDisplay(isVisible) {
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressType).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressCountry).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressIndex).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressRegion).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressCity).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressArea).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressStreet).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressHouse).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressHousing).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(addressBlock.formName, addressBlock.addressApartment).isDisplayed()).toBe(isVisible);
    }

    function checkPhonesBlockDisplay(isVisible) {
        expect(getFormLabelDisplay(phonesBlock.formName, phonesBlock.phoneType).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(phonesBlock.formName, phonesBlock.countryCode).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(phonesBlock.formName, phonesBlock.cityCode).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(phonesBlock.formName, phonesBlock.number).isDisplayed()).toBe(isVisible);
        expect(getFormLabelDisplay(phonesBlock.formName, phonesBlock.additionalNumber).isDisplayed()).toBe(isVisible);

    }

    function checkHeadersDisplayed(isVisible) {
        expect(getHeader(4, personalDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, passportDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, miscDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, phonesBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, emailBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, sitesBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, addressBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, photoDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, eSignatureDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(getHeader(4, socialDataBlock.headerText).isDisplayed()).toBe(isVisible);
    }

    var ptor = protractor.getInstance();

    beforeEach(function () {
        ptor.get('#!/user/profile');
    });

    it('checkDefaultPageView', function () {
        //Setup

        //Act & Verify
        var editBtn = getButton(editBtnName);
        expect(editBtn.isDisplayed()).toBeTruthy();
        expect(editBtn.isEnabled()).toBeTruthy();

        expect(getHeader(3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
        checkHeadersDisplayed(false);
    });

    it('checkSwitchEditMode', function () {
        //Setup

        //Act & Verify
        var editBtn = getButton(editBtnName);
        editBtn.click();

        expect(getHeader(3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
        checkHeadersDisplayed(true);

        var cancelBtn = getButton(cancelBtnName);
        cancelBtn.click();
        ptor.wait(function () {

            expect(getHeader(3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
            checkHeadersDisplayed(false);
        }, 5000);
    });

    it('checkPhoneBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkPhonesBlockDisplay(false);
        getButton(editBtnName).click();
        setTimeout(function () {
            openHiddenBlock(phonesBlock.formName);
            expect(getHeader(4, phonesBlock.headerText).isDisplayed()).toBeTruthy();
            checkPhonesBlockDisplay(true);
            closeHiddenBlock(phonesBlock.formName);
            setTimeout(function () {
                checkPhonesBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkAddressBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkAddressBlockDisplay(false);
        getButton(editBtnName).click();
        ptor.wait(function () {
            openHiddenBlock(addressBlock.formName);
            expect(getHeader(4, addressBlock.headerText).isDisplayed()).toBeTruthy();
            checkAddressBlockDisplay(true);
            closeHiddenBlock(addressBlock.formName);
            ptor.wait(function () {
                checkAddressBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkEmailBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkEmailBlockDisplay(false);
        getButton(editBtnName).click();
        ptor.wait(function () {
            openHiddenBlock(emailBlock.formName);
            expect(getHeader(4, emailBlock.headerText).isDisplayed()).toBeTruthy();
            checkEmailBlockDisplay(true);
            closeHiddenBlock(emailBlock.formName);
            ptor.wait(function () {
                checkEmailBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkSitesBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkSitesBlockVisibleSwitch(false);
        getButton(editBtnName).click();
        ptor.wait(function () {
            openHiddenBlock(sitesBlock.formName);
            expect(getHeader(4, sitesBlock.headerText).isDisplayed()).toBeTruthy();
            checkSitesBlockVisibleSwitch(true);
            closeHiddenBlock(sitesBlock.formName);
            ptor.wait(function () {
                checkSitesBlockVisibleSwitch(false);
            }, 3000);
        }, 3000);
    });
});*/
