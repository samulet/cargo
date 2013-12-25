var common = function () {

    return {
        getButton: function (ptor, name) {
            return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
        },

        getHeader: function (ptor, level, text) {
            return ptor.findElement(ptor, protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
        },

        openHiddenBlock: function (ptor, formName, addBtnName) { //TODO fix replace with waitForVisible
            ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + addBtnName + '")]')).click();
        },

        closeHiddenBlock: function (ptor, formName, cancelBtnName) {
            ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + cancelBtnName + '")]')).click();
        },

        cookies: {
            addToken: function (ptor, token) {
                ptor.manage().addCookie('token', token);
            },
            removeToken: function (ptor) {
                ptor.manage().deleteCookie('token');
            }
        }
    }

};

module.exports = new common();