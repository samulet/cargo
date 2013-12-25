describe('userProfilePageTest', function () {

    var common = require('./common.js');

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
        expect(common.getHeader(4, personalDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, passportDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, miscDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, phonesBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, emailBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, sitesBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, addressBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, photoDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, eSignatureDataBlock.headerText).isDisplayed()).toBe(isVisible);
        expect(common.getHeader(4, socialDataBlock.headerText).isDisplayed()).toBe(isVisible);
    }

    var ptor = protractor.getInstance();

    beforeEach(function () {
        ptor.get('#!/user/profile');
    });

    it('checkDefaultPageView', function () {
        //Setup

        //Act & Verify
        var editBtn = common.getButton(ptor, editBtnName);
        expect(editBtn.isDisplayed()).toBeTruthy();
        expect(editBtn.isEnabled()).toBeTruthy();

        expect(common.getHeader(3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
        checkHeadersDisplayed(false);
    });

    it('checkSwitchEditMode', function () {
        //Setup

        //Act & Verify
        var editBtn = common.getButton(ptor, editBtnName);
        editBtn.click();

        expect(common.getHeader(ptor, 3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
        checkHeadersDisplayed(true);

        var cancelBtn = common.getButton(ptor, cancelBtnName);
        cancelBtn.click();
        ptor.wait(function () {

            expect(common.getHeader(ptor, 3, personalDataBlock.headerText).isDisplayed()).toBeTruthy();
            checkHeadersDisplayed(false);
        }, 5000);
    });

    it('checkPhoneBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkPhonesBlockDisplay(false);
        common.getButton(ptor, editBtnName).click();
        setTimeout(function () {
            common.openHiddenBlock(ptor, phonesBlock.formName);
            expect(common.getHeader(ptor, 4, phonesBlock.headerText).isDisplayed()).toBeTruthy();
            checkPhonesBlockDisplay(true);
            common.closeHiddenBlock(ptor, phonesBlock.formName);
            setTimeout(function () {
                checkPhonesBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkAddressBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkAddressBlockDisplay(false);
        common.getButton(ptor, editBtnName).click();
        ptor.wait(function () {
            common.openHiddenBlock(addressBlock.formName);
            expect(common.getHeader(ptor, 4, addressBlock.headerText).isDisplayed()).toBeTruthy();
            checkAddressBlockDisplay(true);
            common.closeHiddenBlock(ptor, addressBlock.formName);
            ptor.wait(function () {
                checkAddressBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkEmailBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkEmailBlockDisplay(false);
        common.getButton(ptor, editBtnName).click();
        ptor.wait(function () {
            common.openHiddenBlock(emailBlock.formName);
            expect(common.getHeader(ptor, 4, emailBlock.headerText).isDisplayed()).toBeTruthy();
            checkEmailBlockDisplay(true);
            common.closeHiddenBlock(ptor, emailBlock.formName);
            ptor.wait(function () {
                checkEmailBlockDisplay(false);
            }, 3000);
        }, 3000);
    });

    it('checkSitesBlockVisibleSwitch', function () {
        //Setup

        //Act & Verify
        checkSitesBlockVisibleSwitch(false);
        common.getButton(ptor, editBtnName).click();
        ptor.wait(function () {
            common.openHiddenBlock(sitesBlock.formName);
            expect(common.getHeader(ptor, 4, sitesBlock.headerText).isDisplayed()).toBeTruthy();
            checkSitesBlockVisibleSwitch(true);
            common.closeHiddenBlock(ptor, sitesBlock.formName);
            ptor.wait(function () {
                checkSitesBlockVisibleSwitch(false);
            }, 3000);
        }, 3000);
    });
});
