/**
 * Imports
 */

// import $ from 'jquery';
// window.bootstrap = require('bootstrap/dist/js/bootstrap.bundle.js');
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;
import Chart from 'chart.js';
const MarkdownIt = require('markdown-it');
const markdown = new MarkdownIt();

// window.jQuery = $;
// window.$ = $;
window.markdownParser = markdown;

if (window.location.pathname === "/notify/new/react") {
  import("./notify/forms/Composer").then(foo => console.log(foo))
}
// import("module/foo").then(foo => console.log(foo.default))