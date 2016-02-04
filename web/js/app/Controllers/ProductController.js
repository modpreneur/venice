/**
 * Created by Jakub Fajkus on 10.12.15.
 */


import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';

export default class ProductController extends Controller {
    /**
     * New free product action
     * @param $scope
     */
    newFreeAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="free_product"]'), VeniceForm.formType.NEW);
    }

    /**
     * New standard product action
     * @param $scope
     */
    newStandardAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="standard_product"]'), VeniceForm.formType.NEW);
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
        $scope.trinityTab.addListener('tab-load', function(e) {
            let form = e.element.q('form');
            if(form){
                $scope.veniceForms = $scope.veniceForms || {};
                $scope.veniceForms[e.id] = new VeniceForm(form);
            }

        }, this);
    }

}