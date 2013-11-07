'use strict';

module.exports = function (grunt) {

    grunt.initConfig({
            pkg: grunt.file.readJSON('package.json'),
            devJsDir: ['public/js/**/'],
            compiledJs: '<%= compiledDir %>/js/cargo.js',
            compiledMinJs: '<%= compiledDir %>/js/cargo.min.js',

            concat: {
                js: {
                    src: [
                        '<%= devJsDir %>*.js'
                    ],
                    dest: '<%= compiledJs %>'
                }
            },
            uglify: {
                js: {
                    files: {
                        '<%= compiledMinJs %>': ['<%= devJsDir %>*.js']
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
                stylus: {
                    files: ['<%= devDirStylus %>*.styl'],
                    tasks: 'stylus'
                },
                js: {
                    files: ['<%= devJsDir %>*.js', '<%= devJsDir %>pages/*.js'],
                    tasks: 'concat lint'
                }
            },
            /*bower: {
                dev: {
                    dest: 'dest/path'
                }
            }*/
        }
    );

    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');

    grunt.registerTask('default', ['concat:js', 'jshint', 'min:js', 'watch']);
}
;