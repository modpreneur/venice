/**
 * Created by Jakub on 21.12.15.
 */

import events from 'trinity/utils/closureEvents';
import Controller from 'trinity/Controller';
import TrinityTab from 'trinity/components/TrinityTab';
import Collection from 'trinity/Collection';
import _ from 'lodash';
import VeniceForm from '../Libraries/VeniceForm';

export default class ContetntController extends Controller {

    /**
     * Content tab action
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

            //Edit tab
            if(e.id === 'tab2'){
                // Collection
                $scope.collection = _.map(qAll('[data-prototype]'), (node)=>{
                    return new Collection(node, {addFirst:false, label:true});
                });
            }
        }, this);
    }

    /**
     * @param $scope
     */
    newPdfAction($scope) {
        $scope.form = new VeniceForm(q('form[name="pdf_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * @param $scope
     */
    newIframeAction($scope) {
        $scope.form = new VeniceForm(q('form[name="iframe_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * @param $scope
     */
    newMp3Action($scope) {
        $scope.form = new VeniceForm(q('form[name="mp3_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * @param $scope
     */
    newVideoAction($scope) {
        $scope.form = new VeniceForm(q('form[name="video_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * @param $scope
     */
    newHtmlAction($scope) {
        $scope.form = new VeniceForm(q('form[name="html_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * @param $scope
     */
    newGroupAction($scope) {
        $scope.form = new VeniceForm(q('form[name="group_content"]'), VeniceForm.formType.NEW);
    }

    /**
     * New standard product action
     * @param $scope
     */
    newContentProductAction($scope) {
        //Attach VeniceForm
        $scope.form = new VeniceForm(q('form[name="content_product_type_with_hidden_content"]'), VeniceForm.formType.NEW);
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