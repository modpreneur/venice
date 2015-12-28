/**
 * Created by Jakub Fajkus on 28.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import TrinityForm from 'trinity/TrinityForm';

export default class ContetntController extends Controller {

    /**
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();
    }


    /**
     * New user
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new TrinityForm(q('form[name="usertype"]'), TrinityForm.formType.NEW);
    }

}