/**
 * Created by fisa on 10/16/15.
 */
var path = require('path');
var fs = require('fs');

function loadDir(dirname) {
    //var fileNames = [];
    return fs.readdirSync(dirname).map(function(file) {
        // exclude non `.js` files
        if (path.extname(file).toLowerCase() !== '.js') return;

        //fileNames.push(path.basename(file, path.extname(file)));
        return {name: file.substring(0, file.indexOf('.js')), path: path.join(dirname, file)};
    });
}

var controllers = loadDir('./app/Controllers');

fs.open('./app/controllers.js','w+', function(err, fd){
    if(err){
        console.error('ERROR', err);
        return false;
    }
    //Controllers import
    var controllersImports = new Array(controllers.length);
    var controllersInArray = new Array(controllers.length);

    for(var index=0; index<controllers.length; index++){
        controllersImports[index] = ['import', controllers[index].name, 'from \"' + controllers[index].path + '\";\n'].join(' ');
        controllersInArray[index] = ['\"', controllers[index].name, '\":', controllers[index].name, ''].join('');
    }

    var buffer = controllersImports.join(''),
        arrayBuffer = 'var controllers = {' + controllersInArray.join(',') + '};',
        offset = 0;

    try {
        // write imports
        offset += fs.writeSync(fd, buffer, offset, buffer.length);
        //Create array
        offset += fs.writeSync(fd, arrayBuffer, offset, arrayBuffer.length);
    } catch(e){
        console.error('Write to file failed while writing imports, error:', e);
        return false;
    }

    //Export
    var exportLine = '\nexport default controllers;';
    try {
        offset += fs.writeSync(fd,exportLine,offset,exportLine.length);
    } catch(e){
        console.error('ERROR while writing to file - executing exportLine, error:', e);
        return false;
    }
    try {
        fs.closeSync(fd)
    } catch(e){
        console.error('ERROR while closing file')
    }
});



