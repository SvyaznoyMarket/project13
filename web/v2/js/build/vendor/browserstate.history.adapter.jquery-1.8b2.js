/**
 * History.js jQuery Adapter
 * @author Benjamin Arthur Lupton <contact@balupton.com>
 * @copyright 2010-2011 Benjamin Arthur Lupton <contact@balupton.com>
 * @license New BSD License <http://creativecommons.org/licenses/BSD/>
 */

!function(n,t){var i=n.History=n.History||{},r=n.jQuery;if("undefined"!=typeof i.Adapter)throw new Error("History.js Adapter has already been loaded...");i.Adapter={bind:function(n,t,i){r(n).bind(t,i)},trigger:function(n,t,i){r(n).trigger(t,i)},extractEventData:function(n,i,r){var e=i&&i.originalEvent&&i.originalEvent[n]||r&&r[n]||t;return e},onDomLoad:function(n){r(n)}},"undefined"!=typeof i.init&&i.init()}(window);