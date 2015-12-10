/**
 * Created by fisa on 10/16/15.
 */

import routes from './routes.js';
import controllers from './controllers.js';
import AppBuilder from 'trinity/App.js';
import Mousetrap from 'mousetrap';

// Add shortcuts @TODO: move it to some global controller
Mousetrap.bind('ctrl+alt+n', function() {
    var newEntryButton = q('.new-entry');
    if(newEntryButton){
        window.location.assign(newEntryButton.getAttribute('href'));
    }
});

window.settings = {
    environment: 'prod',
    debug: false
};
window.App = new AppBuilder(routes, controllers, settings);
App.start();


// remove loading bar
var bars = qAll('.header-loader .bar');
if(bars.length > 0){
    _.map(bars, function(bar){
        bar.className += ' bar-end';
    });
    var timeoutID = null;
    timeoutID = setTimeout(function(){
        q('.header-loader').style.display = 'none';
        clearTimeout(timeoutID);
    }, 2000);
}
