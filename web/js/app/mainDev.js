/**
 * Created by fisa on 10/16/15.
 */
import _ from 'lodash';
import externalHelpers from 'babel/external-helpers';
import 'trinity/devTools';
import routes from './routes.js';
import AppBuilder from 'trinity/App.js';
import Controller from 'trinity/Controller';
import Mousetrap from 'mousetrap';


// Add shortcuts @TODO: move it to some global controller
Mousetrap.bind('ctrl+alt+n', function() {
    var newEntryButton = q('.new-entry');
    if(newEntryButton){
        window.location.assign(newEntryButton.getAttribute('href'));
    }
});

window.settings = {
    environment: 'dev',
    debug: true
};
window.App = new AppBuilder(routes, null, settings);
App.devStart('app/Controllers', function successCall(isRoute){
    console.log('App Loaded!');
    if(!isRoute){
        console.log('INFO: This route doesn\'t have any controller!');
    }
    removeLoadingBar();
}, function errorCall(err){
    console.error(err);
    var bar = q('.header-loader .bar');
    if(bar){
        bar.style.backgroundColor = "#f00";
    }
});

function removeLoadingBar() {
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
}


