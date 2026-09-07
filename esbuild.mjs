import * as esbuild from 'esbuild';
import { sassPlugin } from 'esbuild-sass-plugin';

/**
 * Note: Ensure you have 'sass' installed: npm install --save-dev sass
 */

// 1. Check if the --watch flag was passed in the command line
const isWatchMode = process.argv.includes('--watch');

// 2. Build configuration

// Shared loaders for both JS and CSS pipelines
const assetLoaders = {
    '.woff': 'file',
    '.woff2': 'file',
    '.eot': 'file',
    '.ttf': 'file',
    '.png': 'file',
    '.jpg': 'file',
    '.svg': 'file',
    '.gif' : 'file',
};

//Configuration for the JavaScript Build Task
const getJsConfig = () => ({
        entryPoints: ['resources/js/app.js', 'resources/js/sensors.js'],
        bundle: true,
        outdir: 'public/js',
        minify: true,
        sourcemap: true,
        entryNames: '[name]',
        loader: { ...assetLoaders, '.js': 'jsx' }
    });


// Configuration for the SASS Build Task
const getSassConfig = () => ({
        entryPoints: ['resources/sass/app.scss', 'resources/sass/rack.scss'],
        bundle: true,
        outdir: 'public/css',
        minify: true,
        sourcemap: true,
        entryNames: '[name]',
        plugins: [
            sassPlugin({
                // Silences warnings from dependencies (e.g., Bootstrap)
                quietDeps: true,
                // Silences specific deprecation types in your own code
                silenceDeprecations: [
                    'import',
                    'color-functions'
                ]
            })
        ],
        // Add loaders here as well so SASS @font-face rules can find them
         loader: assetLoaders
    });

async function runBuild() {
    try {
        if (isWatchMode) {
            console.log('👀 Watch mode enabled. Waiting for changes...');

            // We use separate contexts to prevent plugin interference 
            // between the SASS compiler and the JS transformation.
            const jsContext = await esbuild.context(getJsConfig());
            const sassContext = await esbuild.context(getSassConfig());

            await jsContext.watch();
            await sassContext.watch();

            console.log('✨ Watching for changes in resources/...');
        } else {
            console.log('🚀 Running one-time build...');
            // We use Promise.all to run both builds in parallel for even more speed
            await Promise.all([
                esbuild.build(getJsConfig()),
                esbuild.build(getSassConfig())
            ]);

            console.log('✅ Build completed successfully!');
        }
    } catch (error) {
        // Improved error logging for better debugging
        console.error('\n❌ Build failed!');
        console.error(error);
        process.exit(1);
    }
}

runBuild();
