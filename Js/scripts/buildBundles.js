/**
 * Created by fisa on 11/12/15.
 */
var fs = require('fs');
var path = require('path');
var exec = require('child_process').exec;

var bundlesPath = './bundles';
var trinityMasterHashPath = './jspm_packages/github/modpreneur/trinityJS@master/.jspm-hash';
var infoFile = './scripts/.bundle-info';

// Check if bundle exists
var bundleExists = fs.readdirSync(bundlesPath).filter(function(file){
    return file === 'trinity.bundle.js';
}).length === 1;

var actualHash = fs.readFileSync(trinityMasterHashPath).toString();
var infoHash ='';

try{
    infoHash = fs.readFileSync(infoFile).toString();
} catch(error){
    // File not exists
}
var isHashSame = infoHash.length > 0 && infoHash === actualHash;

if(!isHashSame || !bundleExists){

    // run build
    console.log('Starting to build..');
    exec('npm run build-trinity', function(error, stdout, stderr){
        if(error){
            console.error(error);
        }
        console.log('stdout: ' + stdout);
        if(stderr){
            console.log('stderr: ' + stderr);
        }
        // if all ok
        //write hash
        fs.writeFileSync(infoFile, actualHash);
        console.log('Successfully build');
    });
} else {
    console.log('All up to date!');
}
