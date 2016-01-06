/**
 * Created by fisa on 12/16/15.
 */

import {TrinityForm} from 'trinity/components';
import {messageService} from 'trinity/Services';

export default class VeniceForm extends TrinityForm {
    constructor(element, type, settings){
        super(element, type, settings);
        this.success(this.__onSuccess, this);
        this.error(this.__onError, this);
    }

    /**
     * Success Callback
     * @param response
     * @private
     */
    __onSuccess(response){
        messageService(response['message'],'success');
        console.log('Success', response);
    }

    /**
     * Error callback
     * @param error
     * @private
     */
    __onError(error){
        if(!error && TrinityForm.settings.debug)
        {
            console.log("Error undefined!", error)
            //return;
        }

        console.log('ERROR', error);
        let noErrors = true;
        // DB errors
        if(error.db){
            this.unlock();
        }

        // Global errors - CRFS token, etc.
        if(error['global'] && error['global'].length > 0){
            noErrors = false;
            __globalErrors(error['global']);
        }

        // Fields error
        if(error['fields'] && Object.keys(error['fields']).length > 0){
            noErrors = false;
            __fieldErrors.call(this, error['fields']);
        } else {
            this.unlock();
        }

        // Some error, but nothing related to Form
        if(noErrors && TrinityForm.settings.debug){
            messageService('DEBUG: Request failed but no FORM errors returned! check server response', 'warning');
        }
    }
}

/**
 * Export same types
 */
VeniceForm.formType = TrinityForm.formType;

/**
 * Handles global errors
 * @param errors
 * @private
 */
function __globalErrors(errors){
    let errLength = errors.length;
    for(let i=0; i< errLength; i++){
        //TODO: replace with message service
        messageService(errors[i], 'warning')
    }
}

/**
 * Handles Field Errors - adds them to form
 * @param fields
 * @private
 */
function __fieldErrors(fields){
    let keys = Object.keys(fields),
        keysLength = keys.length;

    for(let i=0; i<keysLength; i++){
        let k = keys[i];
        this.addError(k, fields[k], document.getElementById(k));
    }
}
