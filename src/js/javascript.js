/**
 * Imports
 */

import $ from 'jquery';
import 'bootstrap';
import Chart from 'chart.js';
const MarkdownIt = require('markdown-it');
const markdown = new MarkdownIt();

window.jQuery = $;
window.$ = $;
window.markdownParser = markdown;