/**
 * Created by sailwish001 on 2016/12/7.
 */
fis.set('project.files', ['admin/**']);
fis.set('project.ignore', ['*.cmd', 'fis-conf.js', 'source/source']);
fis.set('statics', '/source');
fis.set('pkg', '/admin');

fis.match('*', {
        deploy: fis.plugin('local-deliver', {
            to: 'D:/xampp/htdocs/sw-jiyin'
        })
    })
    .match('**/*', {
        release: '${statics}/$0'
    })
    .match('*.{html,js}', {
        useSameNameRequire: true
    })
    .match('*.scss', {
        parser: fis.plugin('node-sass'), //启用fis-parser-node-sass插件
        rExt: '.css'
    })
    .match('::packager', {
        spriter: fis.plugin('csssprites', {
            layout: 'matrix',
            margin: '15'
        })
    })
    .match('*.{html,css}', {
        // 给匹配到的文件分配属性 `useSprite`
        useSprite: true
    })
    .match('*.{js,css,less,png,jpg,gif}', {
        useHash: false
    })
    .match('/l10n/*.js', {
        useHash: false
    });


fis.media('prod')
    .match('*.js', {
        optimizer: fis.plugin('uglify-js')
    })
    .match('l10/*.js', {
        preprocessor: null,
        optimizer: null
    })
    .match('*.min.js', {
        preprocessor: null,
        optimizer: null
    })
    .match('*.css', {
        optimizer: fis.plugin('clean-css')
    })
    .match('*.min.css', {
        optimizer: null
    })
    .match('*.png', {
        optimizer: fis.plugin('png-compressor')
    });
    //所有页面中引用到的js资源
     //.match("*.js", {
     //packTo: "${pkg}/vendor.js"
     //})
     //所有页面中引用到的css资源
     //.match("*.css", {
     //packTo: "${pkg}/vendor.css"
 //});


