
/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */
window.$ = window.jQuery = require('jquery');
require('bootstrap');

// ----------------- filter table
import Mark from "mark.js";
let mark = new Mark("#filter-table");
$("#filter-input").on("keyup", function() {
    let value = $(this).val().toLowerCase();
    mark.unmark();
    mark.mark(value);

    $("#filter-table tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});
