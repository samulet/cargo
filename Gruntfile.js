'use strict';

module.exports = function (grunt) {

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            jsDir: ['public/js/'],
            cssDir: 'public/css/',
            compiledJs: '<%= jsDir %>compiled/cargo.js',
            compiledMinJs: '<%= jsDir %>compiled/cargo.min.js',
            devJsSources: [
                '<%= jsDir %>*.js',
                '<%= jsDir %>/pages/*.js',
                '<%= jsDir %>/partials/*.js'
            ],

            concat: {
                js: {
                    src: '<%= devJsSources %>',
                    dest: '<%= compiledJs %>'
                }
            },
            uglify: {
                js: {
                    files: {
                        '<%= compiledMinJs %>': '<%= devJsSources %>'
                    }
                }
            },
            jshint: {
                beforeconcat: {
                    options: {
                        globalstrict: true,
                        globals: {
                            angular: true,
                            localStorage: true,
                            document: true,
                            window: true,
                            navigator: true
                        }
                    },
                    src: '<%= devJsSources %>'
                },
                afterconcat: {
                    options: {
                        globalstrict: true,
                        globals: {
                            angular: true,
                            localStorage: true,
                            document: true,
                            window: true,
                            navigator: true
                        }
                    },
                    src: ['<%= compiledJs %>']
                }
            },
            watch: {
                js: {
                    files: ['<%= jsDir %>/**/*.js'],
                    tasks: 'concat'
                },
                css: {
                    files: ['<%= cssDir %>/additional_markup.css', '<%= cssDir %>/handheld.css', '<%= cssDir %>/theme.css'],
                    tasks: 'cssmin:minify'
                }
            },
            bower: {
                dev: {
                    dest: 'public/lib'
                }
            },
            clean: {
                dev: ['<%= compiledMinJs %>'],
                prod: ['<%= compiledJs %>']
            },
            cssmin: {
                minify: {
                    files: {
                        '<%= cssDir %>/cargo.min.css': ['<%= cssDir %>/additional_markup.css', '<%= cssDir %>/theme.css'],
                        '<%= cssDir %>/cargo.handheld.min.css': ['<%= cssDir %>/handheld.css']
                    }
                }
            },
            ngconstant: {
                options: {
                    space: '  '
                },
                dev: [
                    {
                        dest: '<%= jsDir %>/env_config.js',
                        wrap: '"use strict";\n\n <%= __ngModule %>',
                        name: 'env.config',
                        constants: {
                            REST_CONFIG: (function () {
                                var PROTOCOL = 'http';
                                var HOST = 'localhost';
                                var ZONE = '';
                                var HOST_CONTEXT = '';
                                var PORT = '8080';
                                return {
                                    PROTOCOL: PROTOCOL,
                                    HOST: HOST,
                                    HOST_CONTEXT: HOST_CONTEXT,
                                    PORT: PORT,
                                    DOMAIN: HOST + ZONE,
                                    BASE_URL: PROTOCOL + "://" + HOST + ZONE + ':' + PORT + HOST_CONTEXT
                                };
                            })()
                        }
                    }
                ],
                prod: [
                    {
                        dest: '<%= jsDir %>/env_config.js',
                        wrap: '"use strict";\n\n <%= __ngModule %>',
                        name: 'env.config',
                        constants: {
                            REST_CONFIG: (function () { //TODO fill up production settings
                                var PROTOCOL = '';
                                var HOST = '';
                                var ZONE = '';
                                var HOST_CONTEXT = '';
                                var PORT = '';
                                return {
                                    PROTOCOL: PROTOCOL,
                                    HOST: HOST,
                                    HOST_CONTEXT: HOST_CONTEXT,
                                    PORT: PORT,
                                    DOMAIN: HOST + ZONE,
                                    BASE_URL: PROTOCOL + "://" + HOST + ZONE + ':' + PORT + HOST_CONTEXT
                                };
                            })()
                        }
                    }
                ]
            }
        }
    );

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-bower');
    grunt.loadNpmTasks('grunt-ng-constant');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');

    grunt.registerTask('check_js', ['jshint:beforeconcat']);
    grunt.registerTask('dev', ['ngconstant:dev', 'concat:js', 'jshint:afterconcat', 'cssmin:minify']);
    grunt.registerTask('prod', ['ngconstant:prod', 'concat:js', 'jshint:afterconcat', 'uglify:js', 'cssmin:minify', 'clean:prod']);
}
;