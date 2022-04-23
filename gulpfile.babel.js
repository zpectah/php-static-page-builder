import { src, dest, series, parallel, watch } from 'gulp';
import del from 'del';
import webpack from 'webpack';
import webpack_stream from 'webpack-stream';
import gulpReplace from 'gulp-replace';
import gulpRename from 'gulp-rename';
import gulpJsonMinify from 'gulp-jsonminify';
import gulpIf from 'gulp-if';
import gulpCleanCss from 'gulp-clean-css';
import gulpCssImport from 'gulp-cssimport';
import gulpSourceMaps from 'gulp-sourcemaps';
import { VueLoaderPlugin } from 'vue-loader';

const sass = require('gulp-sass')(require('sass'));
const _path = require('path');

const ENV_DEV = 'development';
const ENV_TEST = 'test';
const ENV_PROD = 'production';
const PATH_SRC = './src/';
const PATH_DEV = './dev/';
const PATH_TEST = './test/';
const PATH_PROD = './prod/';

const getEnv = (env) => {
    let e = env;
    if (env === ENV_TEST) e = ENV_PROD;

    return e;
};

const source = {
    Public: [`./public/**/*`],
    Environment: './.envfile',
    Core: [
        `${PATH_SRC}**/*.php`,
        `${PATH_SRC}**/.htaccess`,
        `${PATH_SRC}**/*.xml`,
        `${PATH_SRC}**/*.txt`,
    ],
    Json: [`${PATH_SRC}**/*.json`, `!${PATH_SRC}**/scripts/**/*.json`],
    VueApp_index: `${PATH_SRC}scripts/index.js`,
    VueApp_watch: [
        `${PATH_SRC}scripts/**/*.js`,
        `${PATH_SRC}scripts/**/*.jsx`,
        `${PATH_SRC}scripts/**/*.vue`,
        `${PATH_SRC}scripts/**/*.css`,
    ],
    Styles: `${PATH_SRC}styles/index.scss`,
    Styles_watch: [
        `${PATH_SRC}styles/**/*.css`,
        `${PATH_SRC}styles/**/*.scss`
    ],
};

const taskDef = {
    Public: function (path, env, cb) {
        src(source.Public).pipe(dest(`${path}public/`));
        cb();
    },
    Clean: function (path, env, cb) {
        return del.sync(
            [`${path}**/*`, `!${path}logs/**`, `!${path}uploads/**`],
            cb(),
        );
    },
    Environment: function (path, env, cb) {
        const now = new Date();
        src(source.Environment)
            .pipe(gulpReplace('%%%%%ENV_ENV%%%%%', env))
            .pipe(gulpReplace('%%%%%ENV_TIMESTAMP%%%%%', now.getTime()))
            .pipe(gulpRename('env.php'))
            .pipe(dest(`${path}config/`));
        cb();
    },
    Core: function (path, env, cb) {
        src(source.Core).pipe(dest(path));
        cb();
    },
    Json: function (path, env, cb) {
        src(source.Json)
            .pipe(gulpIf(env !== ENV_DEV, gulpJsonMinify({})))
            .pipe(dest(path));
        cb();
    },
    VueApp: function (path, env, cb) {
        webpack_stream({
            mode: getEnv(env),
            optimization: {
                minimize: env !== ENV_DEV,
            },
            entry: {
                index: source.VueApp_index,
            },
            output: {
                path: '',
                filename: env === ENV_DEV ? '[name].bundle.js' : '[name].bundle.min.js',
            },
            resolve: {
                extensions: ['.js', '.jsx', '.vue'],
                alias: {
                    vue: "vue/dist/vue.esm-bundler.js"
                },
            },
            plugins: [
                new webpack.DefinePlugin({
                    __VUE_OPTIONS_API__: getEnv(env) === ENV_DEV,
                    __VUE_PROD_DEVTOOLS__: getEnv(env) === ENV_DEV,
                    'process.env': {
                        NODE_ENV: JSON.stringify(getEnv(env)),
                    }
                }),
                new VueLoaderPlugin(),
            ],
            module: {
                rules: [
                    {
                        test: /\.(js|jsx|ts|tsx)$/,
                        include: _path.resolve(__dirname, 'src'),
                        exclude: /node_modules/,
                        use: [
                            {
                                loader: 'babel-loader',
                                options: {
                                    presets: [
                                        '@babel/preset-env',
                                    ],
                                },
                            },
                        ],
                    },
                    {
                        test: /\.vue$/,
                        loader: "vue-loader",
                    },
                ],
            },
            performance: {
                hints: false,
            },
        }).pipe(dest(`${path}scripts/`));
        cb();
    },
    Styles: function (path, env, cb) {
        src(source.Styles)
            .pipe(gulpSourceMaps.init({}))
            .pipe(sass({}).on('error', sass.logError))
            .pipe(gulpCssImport({}))
            .pipe(
                gulpIf(env !== ENV_DEV, dest(`${path}styles/`)),
            )
            .pipe(
                gulpIf(
                    env !== ENV_DEV,
                    gulpCleanCss({
                        compatibility: 'ie9',
                    }),
                ),
            )
            .pipe(
                gulpIf(env !== ENV_DEV, gulpRename({
                    suffix: '.min',
                })),
            )
            .pipe(gulpIf(env !== ENV_DEV, gulpSourceMaps.write()))
            .pipe(dest(`${path}styles/`));
        cb();
    },
};

const Task = {
    Public: {
        dev: (cb) => taskDef.Public(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Public(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Public(PATH_PROD, ENV_PROD, cb),
    },
    Clean: {
        dev: (cb) => taskDef.Clean(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Clean(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Clean(PATH_PROD, ENV_PROD, cb),
    },
    Environment: {
        dev: (cb) => taskDef.Environment(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Environment(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Environment(PATH_PROD, ENV_PROD, cb),
    },
    Json: {
        dev: (cb) => taskDef.Json(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Json(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Json(PATH_PROD, ENV_PROD, cb),
    },
    Core: {
        dev: (cb) => taskDef.Core(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Core(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Core(PATH_PROD, ENV_PROD, cb),
    },
    VueApp: {
        dev: (cb) => taskDef.VueApp(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.VueApp(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.VueApp(PATH_PROD, ENV_PROD, cb),
    },
    Styles: {
        dev: (cb) => taskDef.Styles(PATH_DEV, ENV_DEV, cb),
        test: (cb) => taskDef.Styles(PATH_TEST, ENV_TEST, cb),
        prod: (cb) => taskDef.Styles(PATH_PROD, ENV_PROD, cb),
    },
};

export const dev_watch = (cb) => {
    watch(source.Public, {}, Task.Public.dev);
    watch([...source.Core, `${PATH_SRC}config/**/*.json`], {}, Task.Core.dev);
    watch(source.Json, {}, Task.Json.dev);
    watch(source.VueApp_watch, {}, Task.VueApp.dev);
    watch(source.Styles_watch, {}, Task.Styles.dev);
    cb();
};
export const dev = series(
    Task.Clean.dev,
    parallel(
        Task.Public.dev,
        Task.Json.dev,
        Task.Core.dev,
        Task.VueApp.dev,
        Task.Styles.dev,
    ),
    Task.Environment.dev,
);
export const test = series(
    Task.Clean.test,
    series(
        Task.Public.test,
        Task.Json.test,
        Task.Core.test,
        Task.VueApp.test,
        Task.Styles.test,
    ),
    Task.Environment.test,
);
export const prod = series(
    Task.Clean.prod,
    series(
        Task.Public.prod,
        Task.Json.prod,
        Task.Core.prod,
        Task.VueApp.prod,
        Task.Styles.prod,
    ),
    Task.Environment.prod,
);

export const start = series(dev, dev_watch);

export default dev;
