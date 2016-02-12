/**
 * Created by Jakub Fajkus on 10.12.15.
 */


import events from 'trinity/utils/closureEvents';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';
import Slugify from '../Libraries/Slugify';
import FormChanger from '../Libraries/FormChanger';
import Gateway from 'trinity/Gateway';
import BaseController from './BaseController';
import GridBuilder from '../Libraries/NecktieGridBuilder';

export default class ProductController extends BaseController {

    indexAction($scope) {
        $scope.productGrid = GridBuilder.build(q.id('product-grid'), this.request.query);
    }


    newAction($scope) {
        var select = q.id('entity_type_select');
        var oldType = select.options[select.selectedIndex].value;
        var newType;
        var controller = this;
        var scope = $scope;
        var formDiv = q.id("product_form");
        var formElementName = ":type_product";
        var oldFormName = formElementName.replace(":type", oldType);
        var url = "/admin/product/new/";

        FormChanger.refreshForm(formDiv, url + oldType, function () {
            scope.form = new VeniceForm(q('form[name="' + oldFormName + '"]'), VeniceForm.formType.NEW);
            controller.handleHandleGeneration(oldFormName + '_name', oldFormName + '_handle');

            var necktieIdField = q.id('standard_product_necktieId');

            if (necktieIdField) {
                events.listen(necktieIdField, 'change', function () {
                    Gateway.getJSON('/app_dev.php/api/product/' + this.value + '/necktie-exists', {}, function (response) {
                        if(response == true) {
                            scope.form.addError('standard_product_necktieId', "ID already exists", necktieIdField);
                            scope.form.lock();
                        }
                        else {
                        }
                    }, function(error){
                        console.log(error);
                    });
                });
            }
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

            FormChanger.refreshForm(formDiv, url + newType, function () {
                scope.form = new VeniceForm(q('form[name="' + newFormName + '"]'), VeniceForm.formType.NEW);

                controller.handleHandleGeneration(newFormName + '_name', newFormName + '_handle');

                var necktieIdField = q.id('standard_product_necktieId');

                if (necktieIdField) {
                    events.listen(necktieIdField, 'change', function () {
                        Gateway.getJSON('/app_dev.php/api/product/' + this.value + '/necktie-exists', {}, function (response) {
                            if(response == true) {
                                scope.form.addError('standard_product_necktieId', "ID already exists", necktieIdField);
                                scope.form.lock();
                            }
                            else {
                            }
                        }, function(error){
                            console.log(error);
                        });
                    });
                }
            });
        });
    }

    tabsAction($scope) {
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {
            let form = e.element.q('form');
            if (form) {
                $scope.veniceForms = $scope.veniceForms || {};
                var veniceForm = new VeniceForm(form);
                $scope.veniceForms[e.id] = new VeniceForm(form);
                $scope.veniceForm = veniceForm;
            }

            var necktieIdField = q.id('standard_product_necktieId');

            if (necktieIdField) {
                events.listen(necktieIdField, 'change', function () {
                    Gateway.getJSON('/app_dev.php/api/product/' + this.value + '/necktie-exists', {}, function (response) {
                        if(response == true) {
                            $scope.veniceForm.addError('standard_product_necktieId', "ID already exists", necktieIdField);
                            $scope.veniceForm.lock();
                        }
                        else {
                        }
                    }, function(error){
                        console.log(error);
                    });
                });
            }

            this.handleHandleGeneration('standard_product_name', 'standard_product_handle');
            this.handleHandleGeneration('free_product_name', 'free_product_handle');

        }, this);
    }

    /**
     * New standard product action
     * @param $scope
     */
    newContentProductAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="content_product_type_with_hidden_product"]'), VeniceForm.formType.NEW);
    }

    contentProductTabsAction($scope) {
        $scope.trinityTab = new TrinityTab();

        //On tabs load
        $scope.trinityTab.addListener('tab-load', function (e) {
            var form = e.element.q('form');
            if (form) {
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

        }, this);
    }
}