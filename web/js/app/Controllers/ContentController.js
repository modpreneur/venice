/**
 * Created by Jakub on 21.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/components/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import VeniceForm from '../Libraries/VeniceForm';
import Gateway from 'trinity/Gateway';
import Slugify from '../Libraries/Slugify';
import BaseController from './BaseController';
import FormChanger from '../Libraries/FormChanger';

export default class ContetntController extends BaseController {

    newAction($scope) {
        var formElementName = ":type_content";
        var select = q.id('entity_type_select');
        var oldType = select.options[select.selectedIndex].value;
        var newType;
        var controller = this;
        var scope = $scope;
        var formDiv = q.id("content_form");
        var oldFormName = formElementName.replace(":type", oldType);

        FormChanger.refreshForm(formDiv, "/admin/content/new/" + oldType, function () {
            scope.form = new VeniceForm(q('form[name="'+oldFormName+'"]'), VeniceForm.formType.NEW);

            controller.handleHandleGeneration(oldFormName + '_name', oldFormName + '_handle');
        });

        //save old value when user clicks the input
        events.listen(select, 'click', function (e) {
            oldType = select.options[select.selectedIndex].value;
        });
        //save old value when user uses keyboard
        events.listen(select, 'keydown', function (e) {
            oldType = select.options[select.selectedIndex].value;
        });
        //render new form after change
        events.listen(select, 'change', function (e) {
            newType = select.options[select.selectedIndex].value;
            var newFormName = formElementName.replace(":type", newType);

            FormChanger.refreshForm(formDiv, "/admin/content/new/" + newType, function () {
                scope.form = new VeniceForm(q('form[name="'+newFormName+'"]'), VeniceForm.formType.NEW);

                controller.handleHandleGeneration(newFormName + '_name', newFormName + '_handle');
            });
        });
    }

    tabsAction($scope) {
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function(e) {
            let form = e.element.q('form');
            if(form){
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

            //Edit tab
            if (e.id === 'tab2') {
                // Collection
                $scope.collection = _.map(qAll('[data-prototype]'), (node)=> {
                    return new Collection(node, {addFirst: false, label: true});
                });

                this.handleHandleGeneration();
            }

            //this.handleHandleGeneration('standard_product_name', 'standard_product_handle');
            //this.handleHandleGeneration('free_product_name', 'free_product_handle');

        }, this);
    }

    /**
     * New standard product action
     * @param $scope
     */
    newContentProductAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="content_product_type_with_hidden_content"]'), VeniceForm.formType.NEW);
    }

    contentProductTabsAction($scope) {
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {
            let form = e.element.q('form');
            if (form) {
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

        }, this);
    }

    handleHandleGeneration() {
        var titleField = q.id('group_content_name');
        var handleField = q.id('group_content_handle');

        if (titleField && handleField) {
            events.listen(titleField, 'input', function () {
                Slugify.slugify(titleField, handleField);
            });
        }
    }
}