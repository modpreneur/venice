/**
 * Created by Jakub Fajkus on 05.02.16.
 */

import Gateway from 'trinity/Gateway';

var Slugify = {

    slugify(sourceField, outputField) {
        //call api
        Gateway.postJSON("/api/slugify", {string: sourceField.value}, function (response) {
            outputField.value = response;
        });
    }
};

export default Slugify;




