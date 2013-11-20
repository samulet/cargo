describe('user profile page', function () {
    var ptor = protractor.getInstance();

    beforeEach(function () {
        ptor.get('#!/user/profile');
        /*button = ptor.findElement(protractor.By.className('btn-say-hello'));
         button.click();*/
    });

    function getButton(name) {
        return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
    }

    function getHeader(level, text) {
        return ptor.findElement(protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
    }

    it('checkDefaultPageView', function () {
        var editBtn = getButton('Редактировать');
        expect(editBtn.getAttribute('disabled'));
        //getHeader(4, 'Личные данные').isDisplayed();
        //getHeader(4, 'Личные данные');
        getHeader(4, 'Паспортные sadsd данные')();
        getHeader(4, 'Прочие данные');
        getHeader(4, 'Телефоны');
        getHeader(4, 'Почтовые адреса');
        getHeader(4, 'Email');
        getHeader(4, 'Сайты');
        getHeader(4, 'Фотография профиля');
        getHeader(4, 'Цифровая подпись');
        getHeader(4, 'Аккаунты соцальных сетей');

    });

    it('checkEditMode', function () {
        var editBtn = getButton('Редактировать');
        editBtn.click();
        expect(editBtn.getAttribute('disabled'));
        getHeader(4, 'Личные данные');
        getHeader(4, 'Паспортные данные');
        getHeader(4, 'Прочие данные');
        getHeader(4, 'Телефоны');
        getHeader(4, 'Почтовые адреса');
        getHeader(4, 'Email');
        getHeader(4, 'Сайты');
        getHeader(4, 'Фотография профиля');
        getHeader(4, 'Цифровая подпись');
        getHeader(4, 'Аккаунты соцальных сетей');

    });

    /*it('checkPhoneBlockDefaultState', function() {
     message = ptor.findElement(protractor.By.name('phonesDataForm'));
     expect(message.getText()).toEqual('Hello!');
     });*/

    /*it('checkPhoneBlockVisibleSwitch', function() {
     var form = ptor.findElement(protractor.By.name('phonesDataForm'));
     form.findElement(protractor.By.name("showAddPhoneForm")).click();
     //expect(message.getText()).toEqual('Hello!');
     });*/
});