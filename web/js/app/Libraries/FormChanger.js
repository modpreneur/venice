/**
 * Created by Jakub on 09.02.16.
 */

import Gateway from 'trinity/Gateway';

// Used for switching similar forms and copying it's data from form to form
var FormChanger = {
    /**
     * Call the "url" to get a form which will be inserted into "parentElement".
     * After inserting the form the data from old form are copied to the new form.
     * The "afterFormChange" callback is called after the process.
     *
     * @param {Element}  parentElement    Element which will contain the form which will ge get from url
     * @param {string}   url              Url which will be used to get the form
     * @param {function} afterFormChange  Function which is called after the refreshing is done
     */
    refreshForm(parentElement, url, afterFormChange) {
        var formChanger = this;
        //call api and get form
        Gateway.get('/app_dev.php'+url, {}, function (response) {
            //save data from old form
            var oldHtml = parentElement.innerHTML;

            // If the element already contains a form
            // Change the form to the new form and copy the data
            if (oldHtml.includes("<form")) {
                console.log("form already there");
                var oldForm = parentElement.getElementsByTagName("form")[0];

                parentElement.innerHTML = response;
                var newForm = parentElement.getElementsByTagName("form")[0];

                //copy data to new form
                formChanger.copyFormData(oldForm, newForm);

            } else {
                parentElement.innerHTML = response;
            }

            if (typeof afterFormChange === 'function') {
                afterFormChange();
            }
        });
    },

    copyFormData(oldForm, newForm) {
        var oldInputs = this.getFormInputs(oldForm);
        var newInputs = this.getFormInputs(newForm);
        console.log("copy data");

        for (var newFormInput of newInputs) {
            console.log("first for");
            var newInputName = this.getShortInputName(newFormInput);

            for (var oldInput of oldInputs) {
                console.log("second frm");
                //if the input names are the same
                if (newInputName !== "[_token]" && newInputName === this.getShortInputName(oldInput)) {
                    console.log("names are the same"+newInputName);
                    if('checkbox' === newFormInput.getAttribute('type')) {
                        newFormInput.checked = oldInput.checked;
                    } else {
                        newFormInput.value = oldInput.value;
                    }
                } else {
                    console.log(newInputName +" !== "+ this.getShortInputName(oldInput));
                }
            }
        }
    },

    getShortInputName(formInput) {
        // e.g. free_product[name]
        var formInputFullName = formInput.getAttribute('name');

        var leftSquareBracketIndex = formInputFullName.indexOf('[');
        var rightSquareBracketIndex = formInputFullName.indexOf(']');

        // e.g name
        return formInputFullName.substr(leftSquareBracketIndex, rightSquareBracketIndex);
    },

    getFormInputs(form) {
        var inputs = Array.prototype.slice.call(form.getElementsByTagName("input"));
        var textAreas = Array.prototype.slice.call(form.getElementsByTagName("textarea"));
        var formName = form.getAttribute('name');
        var formTextAreas = [];
        var formInputs = [];

        inputs.forEach(function(input){
            //check if the input contains id of the form
            if(input.getAttribute("id") && -1 !== input.getAttribute("id").indexOf(formName)) {
                formInputs.push(input);
            }
        });

        textAreas.forEach(function(input){
            //check if the input contains id of the form
            if(input.getAttribute("id") && -1 !== input.getAttribute("id").indexOf(formName)) {
                formTextAreas.push(input);
            }
        });


        //convert node list of textareas and push it to the end of inputs
        return formInputs.concat(formTextAreas);
    }
};

export default FormChanger;




