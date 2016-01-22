/**
 * Created by Jakub Fajkus on 10.12.15.
 */


import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';

export default class ProductAccessController extends Controller {

    /**
     * New billing plan action
     * @param $scope
     */
    newAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="product_access"]'), VeniceForm.formType.NEW);
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
}