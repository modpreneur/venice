/**
 * Created by Jakub on 11.02.16.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';
import Slugify from '../Libraries/Slugify';
import FormChanger from '../Libraries/FormChanger';
import Gateway from 'trinity/Gateway';

export default class BaseController extends Controller {

    //handleFormChanging($scope, containingDiv, url, formElementName) {
    //    console.log("changing in baseController");
    //    var select = q.id('entity_type_select');
    //    var oldType = select.options[select.selectedIndex].value;
    //    var newType;
    //    var controller = this;
    //    var scope = $scope;
    //    var formDiv = containingDiv;
    //    var oldFormName = formElementName.replace(":type", oldType);
    //
    //    FormChanger.refreshForm(formDiv, url + oldType, function () {
    //        scope.form = new VeniceForm(q('form[name="'+oldFormName+'"]'), VeniceForm.formType.NEW);
    //
    //        controller.handleHandleGeneration(oldFormName + '_name', oldFormName + '_handle');
    //    });
    //
    //    //save old value when user clicks the input
    //    events.listen(select, 'click', function (e) {
    //        oldType = select.options[select.selectedIndex].value;
    //    });
    //    //save old value when user uses keyboard
    //    events.listen(select, 'keydown', function (e) {
    //        oldType = select.options[select.selectedIndex].value;
    //    });
    //    //render new form after change
    //    events.listen(select, 'change', function (e) {
    //        newType = select.options[select.selectedIndex].value;
    //        var newFormName = formElementName.replace(":type", newType);
    //
    //        FormChanger.refreshForm(formDiv, url + newType, function () {
    //            scope.form = new VeniceForm(q('form[name="'+newFormName+'"]'), VeniceForm.formType.NEW);
    //
    //            controller.handleHandleGeneration(newFormName + '_name', newFormName + '_handle');
    //        });
    //    });
    //}

    handleHandleGeneration(inputId, outputId) {
        var titleField = q.id(inputId);
        var handleField = q.id(outputId);

        if (titleField && handleField) {
            events.listen(titleField, 'input', function () {
                Slugify.slugify(titleField, handleField);
            });
        }
    }
}