var Common = function () {

    this.getButton = function (ptor, name) {
        return ptor.findElement(protractor.By.xpath('//button[contains(.,"' + name + '")]'));
    };

    this.getHeader = function (ptor, level, text) {
        return ptor.findElement(protractor.By.xpath('//h' + level + '[contains(.,"' + text + '")]'));
    };

    this.openHiddenBlock = function (ptor, addBtnName, formName) { //TODO fix replace with waitForVisible
        ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + addBtnName + '")]')).click();
    };

    this.closeHiddenBlock = function (ptor, cancelBtnName, formName) {
        ptor.findElement(protractor.By.xpath('//form[@name="' + formName + '"]//button[contains(.,"' + cancelBtnName + '")]')).click();
    };

};

module.exports = new Common();