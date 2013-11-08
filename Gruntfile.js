'use strict';

module.exports = function (grunt) {

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            jsDir: ['public/js/'],
            compiledJs: '<%= jsDir %>compiled/cargo.js',
            compiledMinJs: '<%= jsDir %>compiled/cargo.min.js',

            concat: {
                js: {
                    src: [
                        '<%= jsDir %>*.js'
                    ],
                    dest: '<%= compiledJs %>'
                }
            },
            uglify: {
                js: {
                    files: {
                        '<%= compiledMinJs %>': ['<%= jsDir %>*.js']
                    }
                }
            },
            jshint: {
                options: {
                    smarttabs: true
                },
                js: ['<%= compiledJs %>']
            },
            lint: {
                files: ['<%= compiledJs %>']
            },
            watch: {
                js: {
                    files: ['<%= jsDir %>/**/*.js'],
                    tasks: 'concat lint'
                }
            },
            bower: {
                dev: {
                    dest: 'public/lib'
                }
            },
            ngconstant: {
                options: {
                    space: '  '
                },
                development: [
                    {
                        dest: '<%= jsDir %>/env_config.js',
                        wrap: '"use strict";\n\n <%= __ngModule %>',
                        name: 'env.config',
                        constants: {
                            PROTOCOL: 'http',
                            HOST: 'localhost',
                            ZONE: '',
                            HOST_CONTEXT: '',
                            PORT: '8080',
                            REST_URL: 'http://localhost:8080'
                        }
                    }
                ],
                production: [
                    {
                        dest: '<%= jsDir %>/env_config.js',
                        wrap: '"use strict";\n\n <%= __ngModule %>',
                        name: 'env.config',
                        constants: {
                            PROTOCOL: '',
                            HOST: '',
                            ZONE: '',
                            HOST_CONTEXT: '',
                            PORT: '',
                            REST_URL: ''
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

    grunt.registerTask('default', ['ngconstant:development', 'concat:js', 'jshint', 'uglify:js', 'watch']);
    grunt.registerTask('production', ['ngconstant:production', 'concat:js', 'jshint', 'uglify:js', 'watch']);
}
;