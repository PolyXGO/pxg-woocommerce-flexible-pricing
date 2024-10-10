const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const glob = require('glob');
const fs = require('fs-extra');
const { execSync } = require('child_process');

// Plugin to delete the dist folder if it exists before processing
class CleanDistFolderPlugin {
    apply(compiler) {
        compiler.hooks.beforeRun.tapAsync('CleanDistFolderPlugin', (compilation, callback) => {
            const distPath = path.resolve(__dirname, 'dist');
            if (fs.existsSync(distPath)) {
                fs.removeSync(distPath);
                console.log('Removed existing dist folder');
            }
            callback();
        });
    }
}

// Plugin to minify CSS files
class MinifyCssPlugin {
    apply(compiler) {
        compiler.hooks.afterEmit.tap('MinifyCssPlugin', (compilation) => {
            // Find all CSS files in directories, excluding /css/lib
            const cssFiles = glob.sync('./assets/css/**/!(*.min).css', {
                ignore: ['./assets/css/lib/**/*']
            });

            cssFiles.forEach(file => {
                const relativePath = path.relative('./assets', file);
                const minifiedFile = path.join('dist/assets', relativePath.replace(/\.css$/, '.min.css'));
                const command = `npx cleancss -o ${minifiedFile} ${file}`;

                try {
                    execSync(command); // Minify the CSS file using clean-css-cli
                    console.log(`Successfully minified ${file} to ${minifiedFile}`);
                } catch (err) {
                    console.error(`Error minifying ${file}:`, err);
                }
            });
        });
    }
}

// Create an entry object that includes only the original JS files (excluding .min.js and not in /js/lib)
const jsEntry = glob.sync('./assets/js/**/!(*.min).js', {
    ignore: ['./assets/js/lib/**/*']
}).reduce((entries, file) => {
    const name = path.relative('./assets', file).replace(/\\/g, '/').replace(/\.js$/, '');
    entries[name] = `./${file}`; // Add entry
    return entries;
}, {});

module.exports = {
    entry: jsEntry, // Process the original JS files
    output: {
        filename: (pathData) => {
            const filePath = pathData.chunk.name.replace(/\\/g, '/');
            return `${filePath}.min.js`; // Save the file with the .min.js suffix
        },
        path: path.resolve(__dirname, 'dist/assets/'), // Output directory
    },
    resolve: {
        modules: [path.resolve(__dirname), 'node_modules'],
        extensions: ['.js'], // Process JS files
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                test: /\.js$/, // Minify JS files
                extractComments: false, // Do not extract comments into separate files. True if code comments are extracted into a separate file, this property works with the output comments attribute.
                terserOptions: {
                    compress: {
                        drop_console: true, // Remove console.log statements. True: remove console.log(). False: keep console.log().
                        dead_code: true, // Remove unused code
                        drop_debugger: true, // Remove debugger statements
                        conditionals: true, // Optimize conditional expressions
                        evaluate: true, // Evaluate constant expressions
                        loops: true, // Optimize loops
                        unused: false, // Do not remove unused variables/functions
                        toplevel: true, // Remove unused top-level variables/functions
                        hoist_funs: true, // Hoist function declarations
                        hoist_vars: true, // Hoist variable declarations
                        if_return: true, // Optimize if-s followed by return/continue
                        join_vars: true, // Join variable declarations
                        collapse_vars: true, // Collapse single-use variables
                    },
                    mangle: {
                        keep_classnames: true, // Keep class names
                        keep_fnames: true, // Keep function names
                    },
                    output: {
                        comments: /@license/i, // Only retain comments containing "@license" to comply with license requirements from other authors.
                        // comments: false, // false: remove all comments, true: keep all comments. False: removes comment code. True: keeps comment code.
                        beautify: false, // true: do not beautify the output code, false: beautify.
                    },
                },
            }),
        ],
    },
    module: {
        rules: [
            {
                test: /\.js$/, // Process JS files
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env'], // Use Babel preset-env
                    },
                },
            },
        ],
    },
    mode: 'production',
    plugins: [
        new CleanDistFolderPlugin(), // Plugin to delete the dist folder before building
        new MinifyCssPlugin(), // Plugin to minify CSS
        new CopyWebpackPlugin({
            patterns: [
                { from: path.resolve(__dirname, './assets/js/lib'), to: path.resolve(__dirname, 'dist/assets/js/lib'), noErrorOnMissing: true }, // Copy the js/lib folder
                { from: path.resolve(__dirname, './assets/css/lib'), to: path.resolve(__dirname, 'dist/assets/css/lib'), noErrorOnMissing: true  }, // Copy the css/lib folder
            ],
        }),
    ],
    devtool: false, // Do not generate source maps
};

// Function to minify CSS files separately if needed
const minifyCssFiles = () => {
    // Find all CSS files in directories, excluding /css/lib
    const cssFiles = glob.sync('./assets/css/**/!(*.min).css', {
        ignore: ['./assets/css/lib/**/*']
    });

    cssFiles.forEach(file => {
        const relativePath = path.relative('./assets', file);
        const minifiedFile = path.join('dist/assets', relativePath.replace(/\.css$/, '.min.css'));
        const command = `npx cleancss -o ${minifiedFile} ${file}`;

        try {
            execSync(command); // Minify the CSS file using clean-css-cli
            console.log(`Successfully minified ${file} to ${minifiedFile}`);
        } catch (err) {
            console.error(`Error minifying ${file}:`, err);
        }
    });
};

// Run the minify CSS function after Webpack completes if needed
minifyCssFiles();
