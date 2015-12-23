/**
 * Created by Jakub on 21.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import TrinityForm from 'trinity/TrinityForm';

export default class ContetntController extends Controller {

    /**
     * Content and associations tabs action
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();
    }

    /**
     * Content tab action
     * @param $scope
     */
    tabAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();
    }

    /**
     * New blog article action
     * @param $scope
     */
    newPdfAction($scope) {
        $scope.form = new TrinityForm(q('form[name="pdfcontenttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newTextAction($scope) {
        $scope.form = new TrinityForm(q('form[name="textcontenttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newMp3Action($scope) {
        $scope.form = new TrinityForm(q('form[name="mp3contenttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newVideoAction($scope) {
        $scope.form = new TrinityForm(q('form[name="videocontenttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newIFrameAction($scope) {
        $scope.form = new TrinityForm(q('form[name="iframecontenttype"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newGroupAction($scope) {
        $scope.collection = _.map(qAll('[data-prototype]'), function (node) {
            return new Collection(node);
        });

        $scope.form = new TrinityForm(q('form[name="groupcontenttype"]'), TrinityForm.formType.NEW);
    }
}