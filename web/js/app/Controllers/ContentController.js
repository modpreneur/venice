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
     * Content tab action
     * @param $scope
     */
    tabsAction($scope) {
        //Tell trinity there is tab to be loaded
        $scope.trinityTab = new TrinityTab();

        // Listen changes on the div with id "content-edit"
        $scope.trinityTab.listen('content-edit', function (e) {
            let $scope = this.getScope();

            // Collection
            $scope.collection = _.map(qAll('[data-prototype]'), function (node) {
                return new Collection(node, {addFirst:false, label:true});
            });
        }, false, this);

    }

    /**
     * New blog article action
     * @param $scope
     */
    newPdfAction($scope) {
        $scope.form = new TrinityForm(q('form[name="pdf_content"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newTextAction($scope) {
        $scope.form = new TrinityForm(q('form[name="text_content"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newMp3Action($scope) {
        $scope.form = new TrinityForm(q('form[name="mp3_content"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newVideoAction($scope) {
        $scope.form = new TrinityForm(q('form[name="video_content"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newIFrameAction($scope) {
        $scope.form = new TrinityForm(q('form[name="iframe_content"]'), TrinityForm.formType.NEW);
    }

    /**
     * New blog article action
     * @param $scope
     */
    newGroupAction($scope) {
        $scope.form = new TrinityForm(q('form[name="group_content"]'), TrinityForm.formType.NEW);
    }
}