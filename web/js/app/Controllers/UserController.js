/**
 * Created by Jakub Fajkus on 28.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/components/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import VeniceForm from '../Libraries/VeniceForm';

export default class ContetntController extends Controller {

    /**
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


    /**
     * New user
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new VeniceForm(q('form[name="user"]'), VeniceForm.formType.NEW);
    }

}