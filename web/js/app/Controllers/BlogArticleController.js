/**
 * Created by Jakub Fajkus on 10.12.15.
 */


import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityForm from 'trinity/TrinityForm';
import TrinityTab from 'trinity/TrinityTab';

export default class BlogArticleController extends Controller {

    /**
     * Tabs action
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();
    }

    /**
     * New blog article action
     * @param $scope
     */
    newAction($scope) {
        $scope.form = new TrinityForm(q('form[name="blogarticletype"]'), TrinityForm.formType.NEW);
    }
}