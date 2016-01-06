/**
 * Created by Jakub Fajkus on 21.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import VeniceForm from '../Libraries/VeniceForm';
import TrinityTab from 'trinity/components/TrinityTab';

export default class ContetntController extends Controller {

    /**
     *
     * @param $scope
     */
    newAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.form = new VeniceForm(q('form[name="content_product"]'));
    }

    /**
     *
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
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
