const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    entry: {
        main: './src/js/index.js',  // Archivo principal de JS que importa el SCSS
        bootstrap: './src/js/bootstrap/bootstrap.js'  // Bootstrap separado
    },
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: '[name].min.js',  // Genera main.min.js y bootstrap.min.js
        clean: true,
    },
    devtool: 'source-map',  // Source maps habilitados para ambos entornos
    module: {
        rules: [
            {
                test: /\.scss$/,  // Procesa archivos SCSS
                use: [
                    MiniCssExtractPlugin.loader, // Extrae el CSS en un archivo separado
                    'css-loader',  // Convierte el CSS en módulos JavaScript
                    'sass-loader', // Compila SCSS a CSS
                ],
            },
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].min.css',  // Genera un archivo CSS por cada entrada
        }),
    ],
};
