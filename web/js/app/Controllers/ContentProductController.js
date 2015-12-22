/**
 * Created by Jakub Fajkus on 21.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityForm from 'trinity/TrinityForm';
import TrinityTab from 'trinity/TrinityTab';

export default class ContetntController extends Controller {

    /**
     *
     * @param $scope
     */
    newAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.form = new TrinityForm(q('form[name="contentproducttype"]'));
    }

    /**
     *
     * @param $scope
     */
    tabsAction($scope) {
        $scope.trinityTab = new TrinityTab();
    }
}
