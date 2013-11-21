exports.config = {
    seleniumServerJar: './../../selenium/selenium-server-standalone-2.37.0.jar',
    seleniumPort: null,
    chromeDriver: './../../selenium/chromedriver',
    seleniumArgs: [],
    sauceUser: null,
    sauceKey: null,
    seleniumAddress: null,
    allScriptsTimeout: 11000,
    specs: ['./../pages/*Test.js'],

    capabilities: {
        'browserName': 'chrome'
    },

    baseUrl: 'http://cargo.dev:8000',
    rootElement: 'body',
    onPrepare: function () {
    },
    params: {
        login: {
            user: 'Jane',
            password: '1234'
        }
    },

    jasmineNodeOpts: {
        onComplete: null,
        isVerbose: false,
        showColors: true,
        includeStackTrace: true,
        defaultTimeoutInterval: 30000
    }
};
