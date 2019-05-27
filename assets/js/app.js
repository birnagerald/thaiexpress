/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/jquery-ui.css');
require('../css/picto-foundry-food.css');
require('../css/style-portfolio.css');
require('../css/app.css');
require('../css/bootstrap.min.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

global.$ = global.jQuery = $;

require('bootstrap');